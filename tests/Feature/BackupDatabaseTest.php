<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

test('sqlite backup writes a gzipped copy that holds the data', function () {
    $source = storage_path('framework/testing/backup-source.sqlite');
    File::ensureDirectoryExists(dirname($source));
    File::put($source, '');

    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => $source]);
    DB::purge('sqlite');
    DB::connection('sqlite')->statement('create table probe (id integer)');
    DB::connection('sqlite')->insert('insert into probe (id) values (42)');

    $this->artisan('backup:database')->assertSuccessful();

    $backup = collect(File::files(storage_path('app/backups')))
        ->filter(fn ($file) => str_starts_with($file->getFilename(), 'backup-source.sqlite-') && str_ends_with($file->getFilename(), '.sqlite.gz'))
        ->sortByDesc(fn ($file) => $file->getMTime())
        ->first();

    expect($backup)->not->toBeNull();

    $copy = storage_path('framework/testing/backup-restored.sqlite');
    File::put($copy, gzdecode(File::get($backup->getPathname())));
    $restored = new PDO('sqlite:'.$copy);

    expect((int) $restored->query('select id from probe')->fetchColumn())->toBe(42);

    File::delete([$source, $copy, $backup->getPathname()]);
});
