<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';

    protected $description = 'Dump the database to a gzipped file and prune old local copies';

    public function handle(): int
    {
        $connection = config('database.connections.'.config('database.default'));
        $directory = storage_path('app/backups');
        File::ensureDirectoryExists($directory);

        $stamp = now()->format('Y-m-d_His');
        $name = basename((string) $connection['database']);

        if ($connection['driver'] === 'sqlite') {
            // VACUUM INTO writes a consistent copy even while the app is writing
            $raw = $directory.'/'.$name.'-'.$stamp.'.sqlite';
            DB::statement('VACUUM INTO ?', [$raw]);
            $contents = File::get($raw);
            File::delete($raw);
            $file = $directory.'/'.$name.'-'.$stamp.'.sqlite.gz';
        } else {
            $file = $directory.'/'.$name.'-'.$stamp.'.sql.gz';

            // Password goes through MYSQL_PWD so it never appears in the process list
            $dump = Process::timeout(900)
                ->env(['MYSQL_PWD' => (string) $connection['password']])
                ->run([
                    'mysqldump',
                    '--single-transaction',
                    '--no-tablespaces',
                    '-h', (string) $connection['host'],
                    '-P', (string) $connection['port'],
                    '-u', (string) $connection['username'],
                    $name,
                ]);

            if ($dump->failed()) {
                $this->error('Database dump failed: '.$dump->errorOutput());

                return self::FAILURE;
            }

            $contents = $dump->output();
        }

        File::put($file, gzencode($contents, 9));
        $this->info('Backup written to '.$file);

        $keepDays = (int) config('services.backup.keep_days');
        foreach (File::files($directory) as $old) {
            if ($old->getMTime() < now()->subDays($keepDays)->getTimestamp()) {
                File::delete($old->getPathname());
            }
        }

        return self::SUCCESS;
    }
}
