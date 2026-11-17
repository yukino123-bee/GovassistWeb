<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
            $this->probe('OCR service', fn () => Http::timeout(15)->get(config('services.ocr.url').'/health')->json('status') === 'ok' ?: throw new RuntimeException('not ok'), 'Document reading service is online'),
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
}
