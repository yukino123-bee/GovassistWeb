<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $sqlPath = base_path('database_export.sql');
        if (File::exists($sqlPath)) {
            Schema::disableForeignKeyConstraints();
            DB::unprepared(File::get($sqlPath));
            Schema::enableForeignKeyConstraints();
        }
    }
}
