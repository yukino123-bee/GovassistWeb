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
        Schema::table('user_inquiries', function (Blueprint $table) {
            if (Schema::hasColumn('user_inquiries', 'is_bot')) {
                $table->dropColumn('is_bot');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_inquiries', function (Blueprint $table) {
            $table->boolean('is_bot')->default(false)->after('status');
        });
    }
};
