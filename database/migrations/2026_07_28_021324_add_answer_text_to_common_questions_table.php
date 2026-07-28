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
        Schema::table('common_questions', function (Blueprint $table) {
            $table->text('answer_text')->nullable()->after('question_text');
        });

        DB::table('government_services')
            ->orderBy('id')
            ->each(function (object $service): void {
                $now = now();

                DB::table('common_questions')->insert([
                    [
                        'service_id' => $service->id,
                        'question_text' => "What is {$service->service_name}?",
                        'answer_text' => $service->description,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                    [
                        'service_id' => $service->id,
                        'question_text' => "Who is eligible for {$service->service_name}?",
                        'answer_text' => "Complete the eligibility assessment for {$service->service_name} to confirm whether you qualify.",
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                    [
                        'service_id' => $service->id,
                        'question_text' => "How do I apply for {$service->service_name}?",
                        'answer_text' => $service->procedure ?: 'Complete the eligibility assessment, upload every required document, and submit your application.',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                    [
                        'service_id' => $service->id,
                        'question_text' => "What documents are required for {$service->service_name}?",
                        'answer_text' => "Open the {$service->service_name} Requirements Checklist to view and upload every required document.",
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                    [
                        'service_id' => $service->id,
                        'question_text' => "How do I check my {$service->service_name} application status?",
                        'answer_text' => 'Open your Profile to view the application status and any facilitator remarks.',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('common_questions', function (Blueprint $table) {
            $table->dropColumn('answer_text');
        });
    }
};
