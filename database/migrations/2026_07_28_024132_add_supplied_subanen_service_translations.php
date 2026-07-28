<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('government_services')
            ->whereIn('service_name', ['Educational Assistance', 'Educational Assistance Program'])
            ->orderBy('id')
            ->each(function (object $service): void {
                DB::table('service_translations')->updateOrInsert([
                    'service_id' => $service->id,
                    'language_code' => 'sub',
                ], [
                    'service_name' => 'Gabang ni programa ne ngaji',
                    'description' => 'Me phenun gobang rin sehutliha rn megiskiela,',
                    'procedure' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('service_translations')
            ->where('language_code', 'sub')
            ->where('service_name', 'Gabang ni programa ne ngaji')
            ->where('description', 'Me phenun gobang rin sehutliha rn megiskiela,')
            ->delete();
    }
};
