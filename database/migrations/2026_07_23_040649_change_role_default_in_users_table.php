<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('resident')->change();
        });

        // Update existing users
        DB::table('users')->where('role', 'citizen')->update(['role' => 'resident']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('citizen')->change();
        });

        // Revert existing users
        DB::table('users')->where('role', 'resident')->update(['role' => 'citizen']);
    }
};
