<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('government_services', function (Blueprint $table) {
            $table->unsignedTinyInteger('minimum_percentage')->default(60)->after('passing_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('government_services', function (Blueprint $table) {
            $table->dropColumn('minimum_percentage');
        });
    }
};
