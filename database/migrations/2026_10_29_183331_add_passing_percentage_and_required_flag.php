<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('government_services', function (Blueprint $table) {
            $table->unsignedTinyInteger('passing_percentage')->default(100);
        });

        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->boolean('is_required')->default(true)->after('is_eligible');
        });
    }

    public function down(): void
    {
        Schema::table('government_services', function (Blueprint $table) {
            $table->dropColumn('passing_percentage');
        });

        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->dropColumn('is_required');
        });
    }
};
