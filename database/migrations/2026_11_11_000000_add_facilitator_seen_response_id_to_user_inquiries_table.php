<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_inquiries', function (Blueprint $table) {
            // ID of the newest resident message staff have seen in the thread
            $table->unsignedBigInteger('facilitator_seen_response_id')->nullable()->after('resident_seen_reply_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_inquiries', function (Blueprint $table) {
            $table->dropColumn('facilitator_seen_response_id');
        });
    }
};
