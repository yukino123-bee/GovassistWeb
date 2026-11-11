<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_inquiries', function (Blueprint $table) {
            // ID of the newest staff reply the resident has seen on the Inquiry page
            $table->unsignedBigInteger('resident_seen_reply_id')->nullable()->after('awaiting_reply_since');
        });
    }

    public function down(): void
    {
        Schema::table('user_inquiries', function (Blueprint $table) {
            $table->dropColumn('resident_seen_reply_id');
        });
    }
};
