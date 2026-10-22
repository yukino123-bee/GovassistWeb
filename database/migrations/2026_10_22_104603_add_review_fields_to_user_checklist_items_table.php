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
        Schema::table('user_checklist_items', function (Blueprint $table) {
            $table->string('review_result')->nullable();
            $table->float('review_score')->nullable();
            $table->text('review_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_checklist_items', function (Blueprint $table) {
            $table->dropColumn(['review_result', 'review_score', 'review_note']);
        });
    }
};
