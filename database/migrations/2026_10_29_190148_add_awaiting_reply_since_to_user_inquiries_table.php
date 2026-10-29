<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_inquiries', function (Blueprint $table) {
            $table->timestamp('awaiting_reply_since')->nullable()->index()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('user_inquiries', function (Blueprint $table) {
            $table->dropColumn('awaiting_reply_since');
        });
    }
};
