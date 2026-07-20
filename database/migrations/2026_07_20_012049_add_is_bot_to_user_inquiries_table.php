<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('user_inquiries', 'is_bot')) {
            Schema::table('user_inquiries', function (Blueprint $table) {
                $table->boolean('is_bot')->default(false)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_inquiries', function (Blueprint $table) {
            $table->dropColumn('is_bot');
        });
    }
};
