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
        // 1. Service Categories
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Government Services
        Schema::create('government_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('service_categories')->onDelete('set null');
            $table->string('service_name');
            $table->text('description');
            $table->text('procedure')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        // 3. Service Translations
        Schema::create('service_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('government_services')->onDelete('cascade');
            $table->string('language_code', 5);
            $table->string('service_name');
            $table->text('description');
            $table->text('procedure')->nullable();
            $table->timestamps();
        });

        // 4. Service Requirements
        Schema::create('service_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('government_services')->onDelete('cascade');
            $table->json('requirement_text'); // cast as array/json
            $table->boolean('is_required')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        // 5. User Languages
        Schema::create('user_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('language_code', 5);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 6. User Inquiries
        Schema::create('user_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('government_services')->onDelete('set null');
            $table->text('inquiry_text');
            $table->string('status')->default('pending'); // pending, in_progress, resolved, closed
            $table->timestamps();
        });

        // 7. Inquiry Responses (inquiry_requirenses)
        Schema::create('inquiry_requirenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained('user_inquiries')->onDelete('cascade');
            $table->text('requireent_text'); // Response message
            $table->foreignId('responded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 8. Eligibility Questions (needed to run assessments dynamically)
        Schema::create('eligibility_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('government_services')->onDelete('cascade');
            $table->text('question_text_en');
            $table->text('question_text_ceb');
            $table->text('question_text_fil');
            $table->string('type')->default('boolean'); // boolean, number
            $table->string('expected_value');
            $table->string('operator')->default('=='); // ==, >, <, >=, <=
            $table->timestamps();
        });

        // 9. Eligibility Assessments
        Schema::create('eligibility_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('government_services')->onDelete('cascade');
            $table->string('status')->default('ineligible'); // eligible, ineligible
            $table->timestamps();
        });

        // 10. Assessment Answers
        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('eligibility_assessments')->onDelete('cascade');
            $table->text('question');
            $table->text('answer');
            $table->timestamps();
        });

        // 11. User Checklists (corresponds to applications)
        Schema::create('user_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('government_services')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 12. User Checklist Items (corresponds to uploaded docs)
        Schema::create('user_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_id')->constrained('user_checklists')->onDelete('cascade');
            $table->foreignId('requirement_id')->constrained('service_requirements')->onDelete('cascade');
            $table->boolean('is_submitted')->default(false);
            $table->string('file_path')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_checklist_items');
        Schema::dropIfExists('user_checklists');
        Schema::dropIfExists('assessment_answers');
        Schema::dropIfExists('eligibility_assessments');
        Schema::dropIfExists('eligibility_questions');
        Schema::dropIfExists('inquiry_requirenses');
        Schema::dropIfExists('user_inquiries');
        Schema::dropIfExists('user_languages');
        Schema::dropIfExists('service_requirements');
        Schema::dropIfExists('service_translations');
        Schema::dropIfExists('government_services');
        Schema::dropIfExists('service_categories');
    }
};
