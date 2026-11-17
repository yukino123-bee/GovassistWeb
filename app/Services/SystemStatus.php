<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RuntimeException;
use Throwable;

class SystemStatus
{
    /**
     * Check every technology the app depends on, for the facilitator status view.
     *
     * @return array<int, array{name: string, ok: bool, detail: string}>
     */
    public function checks(): array
    {
        return [
            $this->probe('Database', fn () => DB::select('select 1'), 'Connected ('.config('database.default').')'),
            $this->probe('Cache', fn () => Cache::store()->get('system-status-probe'), 'Driver: '.config('cache.default')),
            $this->probe('File storage', fn () => is_writable(storage_path('app')) ?: throw new RuntimeException('not writable'), 'storage/app is writable'),
            $this->probe('Website build (Vite)', fn () => is_file(public_path('build/manifest.json')) ?: throw new RuntimeException('missing'), 'Build files found'),
            $this->probe('Document reader (Python)', fn () => $this->tool(['python3', '-c', 'import rapidfuzz']), 'Python with rapidfuzz is ready'),
            $this->probe('OCR engine (Tesseract)', fn () => $this->tool(['tesseract', '--version']), 'Text recognition engine found'),
            $this->probe('PDF pages (pdftoppm)', fn () => $this->tool(['pdftoppm', '-v']), 'PDF page converter found'),
            $this->probe('SMS', function () {
                if (! config('services.sms.enabled', true)) {
                    throw new RuntimeException('turned off');
                }
                if (config('services.sms.driver', 'log') === 'log') {
                    throw new RuntimeException('log only');
                }
            }, 'Sending through '.config('services.sms.driver').' provider'),
        ];
    }

    /**
     * Run one check. The probe throws when the technology is down; details stay in the log.
     *
     * @return array{name: string, ok: bool, detail: string}
     */
    private function probe(string $name, Closure $probe, string $okDetail): array
    {
        try {
            $probe();

            return ['name' => $name, 'ok' => true, 'detail' => $okDetail];
        } catch (Throwable $exception) {
            Log::warning("System status check failed: {$name}", ['error' => $exception->getMessage()]);

            return ['name' => $name, 'ok' => false, 'detail' => 'Not available right now. Check the log for details.'];
        }
    }

    private function tool(array $command): bool
    {
        if (Process::timeout(15)->run($command)->failed()) {
            throw new RuntimeException('command failed: '.$command[0]);
        }

        return true;
    }
}
