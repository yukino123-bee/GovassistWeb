<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * @var list<string>
     */
    private array $optionalQuestions = [
        'Is the student an active member of any community youth organization?',
        'Is the student a dependent of a solo parent or a person with disability?',
        'Is the student willing to render community service hours if required?',
        'Is the student enrolled in a priority course or degree program?',
        'Is the student a first-time applicant for this specific assistance program?',
        'Is the patient currently admitted to a public hospital or certified healthcare facility?',
        "Is the patient's illness considered a severe or chronic condition requiring extensive treatment?",
        'Has the patient exhausted all available PhilHealth benefits for this admission?',
        'Does the patient require specialized medication not available in the local health center?',
        'Is the patient a senior citizen, a person with disability, or a minor?',
        'Is the patient scheduled for an urgent surgical procedure?',
        'Is the patient a member of an indigenous cultural community?',
        'Does the patient require continuous dialysis, chemotherapy, or physical therapy?',
        'Is the patient unable to work due to the current medical condition?',
        'Is the patient currently admitted to a hospital?',
        'Are you applying for dialysis treatment assistance?',
        'Are you applying for chemotherapy session assistance?',
        'Are you requesting for maintenance medicines?',
        'Are you a senior citizen or a Person with Disability (PWD)?',
        'Do you have an active PhilHealth membership?',
        'Is the requested assistance for a surgical operation?',
        'Is the assistance for dialysis treatment?',
        'Is the assistance for chemotherapy sessions?',
        'Is the assistance for maintenance medicines?',
        'Is the patient a senior citizen or a Person with Disability (PWD)?',
        'Does the patient have an active PhilHealth membership?',
        'Was the deceased a senior citizen or a person with disability?',
        'Is the cause of death related to an accident or a sudden tragic event?',
        'Was the deceased the primary breadwinner of the family?',
        'Is the applicant requesting assistance specifically for casket or interment services?',
        'Has the family complied with all local health regulations for the burial?',
        'Are you the person who directly paid for the funeral expenses?',
        'Have you secured a permit to transfer the cadaver (if applicable)?',
        'Are you requesting assistance for embalming or casket costs?',
        'Are you requesting assistance for cemetery lot or niche rental?',
        'Are you an active member of any local burial association or cooperative?',
        'Are you willing to sign a waiver of non-duplication of claims?',
        'Is the passenger traveling for an urgent medical referral or emergency?',
        'Is the passenger a stranded individual or victim of a calamity seeking to return home?',
        'Is the passenger traveling to seek employment opportunities outside the province?',
        'Is the passenger a senior citizen, a person with disability, or a pregnant woman?',
        'Is the passenger requesting assistance for a one-way trip only?',
        'Is the passenger traveling with essential medical equipment or a necessary escort?',
        'Are you stranded and need to return to your home province?',
        'Is your travel due to a medical emergency or hospital referral?',
        'Are you traveling to seek employment outside the municipality?',
        'Are you a victim of a recent calamity or disaster?',
        'Are you a rescued victim of human trafficking or abuse?',
        'Are you traveling alone?',
        'Is the job seeker a graduate of a vocational training or skills development program?',
        'Is the job seeker a displaced worker due to retrenchment or company closure?',
        'Is the job seeker applying for a livelihood starter kit or capital assistance?',
        'Is the job seeker a person with disability or a single parent seeking employment?',
        'Is the job seeker willing to undergo additional skills training if required?',
        'Is the job seeker applying for a position that matches their current qualifications?',
        'Is the job seeker an out-of-school youth seeking entry-level employment?',
        'Has the job seeker secured a social case study report from the MSWDO?',
        'Have you completed at least high school or Alternative Learning System (ALS)?',
        'Are you applying for a starter kit or capital assistance?',
        'Are you a solo parent seeking livelihood support?',
        'Are you a returning Overseas Filipino Worker (OFW)?',
        'Are you willing to attend a mandatory livelihood training seminar?',
        'Do you have a business plan or project proposal (for capital assistance)?',
        "Are you a member of a registered cooperative or workers' association?",
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('eligibility_questions')
            ->whereIn('question_text_en', $this->optionalQuestions)
            ->update(['is_required' => false]);

        DB::table('eligibility_questions')
            ->where('type', 'text')
            ->update(['is_required' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('eligibility_questions')
            ->whereIn('question_text_en', $this->optionalQuestions)
            ->update(['is_required' => true]);

        DB::table('eligibility_questions')
            ->where('type', 'text')
            ->update(['is_required' => true]);
    }
};
