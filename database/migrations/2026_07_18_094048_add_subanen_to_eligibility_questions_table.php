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
        Schema::table('eligibility_questions', function (Blueprint $table) {
            $table->text('question_text_sub')->nullable()->after('question_text_fil');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eligibility_questions', function (Blueprint $table) {
            $table->dropColumn('question_text_sub');
        });
    }
};
