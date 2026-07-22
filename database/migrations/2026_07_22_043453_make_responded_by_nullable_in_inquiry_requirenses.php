<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Make responded_by nullable so guest citizens (unauthenticated users)
     * can submit inquiry messages without a user_id.
     */
    public function up(): void
    {
        Schema::table('inquiry_requirenses', function (Blueprint $table) {
            // Drop the existing non-nullable FK constraint first
            $table->dropForeign(['responded_by']);
            // Re-add as nullable FK
            $table->unsignedBigInteger('responded_by')->nullable()->change();
            $table->foreign('responded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiry_requirenses', function (Blueprint $table) {
            $table->dropForeign(['responded_by']);
            $table->unsignedBigInteger('responded_by')->nullable(false)->change();
            $table->foreign('responded_by')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
