<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitizenController;
use App\Http\Controllers\FacilitatorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    if (Auth::check() && Auth::user()->isFacilitator()) {
        return redirect()->route('facilitator.dashboard');
    }

    return redirect()->route('citizen.home');
});

// Authentication & Language Switcher
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->name('verification.notice');
    Route::post('/email/verify/process', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'verifyResend'])->middleware('throttle:6,1')->name('verification.send');
});

Route::post('/language/toggle', [AuthController::class, 'toggleLanguage'])->name('language.toggle');

use App\Http\Middleware\EnsureEmailIsVerifiedIfLoggedIn;
use Illuminate\Support\Facades\Artisan;

// Citizen Public Routes
Route::prefix('citizen')->middleware([EnsureEmailIsVerifiedIfLoggedIn::class])->group(function () {
    Route::get('/home', [CitizenController::class, 'home'])->name('citizen.home');
    Route::get('/eligibility', [CitizenController::class, 'eligibility'])->name('citizen.eligibility');
    Route::get('/inquiry', [CitizenController::class, 'inquiry'])->name('citizen.inquiry');
    Route::post('/inquiry/chat', [CitizenController::class, 'inquiryChat'])->name('citizen.inquiry.chat');
    Route::post('/inquiry/manual', [CitizenController::class, 'submitManualInquiry'])->name('citizen.inquiry.manual');
});

// Citizen Protected Routes
Route::middleware(['auth', 'role:citizen', 'verified'])->prefix('citizen')->group(function () {
    // Eligibility Assessment
    Route::get('/eligibility/assess/{service}', [CitizenController::class, 'showAssessForm'])->name('citizen.eligibility.assess');
    Route::post('/eligibility/assess/{service}', [CitizenController::class, 'processAssessForm'])->name('citizen.eligibility.assess.submit');
    Route::get('/eligibility/result/{refNo}', [CitizenController::class, 'showAssessResult'])->name('citizen.eligibility.result');
    Route::post('/eligibility/reassess/{service}', [CitizenController::class, 'requestReassessment'])->name('citizen.eligibility.reassess');

    // Checklist & Document Upload
    Route::get('/eligibility/checklist/{service}', [CitizenController::class, 'checklist'])->name('citizen.eligibility.checklist');
    Route::post('/eligibility/checklist/{service}/upload/{requirement}', [CitizenController::class, 'uploadDocument'])->name('citizen.eligibility.upload');
    Route::post('/eligibility/checklist/{service}/type', [CitizenController::class, 'setApplicationType'])->name('citizen.eligibility.set_type');
    Route::post('/eligibility/apply/{service}', [CitizenController::class, 'apply'])->name('citizen.eligibility.apply');

    // Profile Settings
    Route::get('/profile', [CitizenController::class, 'profile'])->name('citizen.profile');
    Route::get('/profile/edit', [CitizenController::class, 'editProfile'])->name('citizen.profile.edit');
    Route::post('/profile/edit', [CitizenController::class, 'updateProfile'])->name('citizen.profile.update');
    Route::post('/profile/avatar', [CitizenController::class, 'updateAvatar'])->name('citizen.profile.avatar');
});

// Facilitator (Admin) Protected Routes
Route::middleware(['auth', 'role:facilitator'])->prefix('facilitator')->group(function () {
    Route::get('/dashboard', [FacilitatorController::class, 'dashboard'])->name('facilitator.dashboard');

    // Profile Settings
    Route::get('/profile', [FacilitatorController::class, 'editProfile'])->name('facilitator.profile.edit');
    Route::post('/profile', [FacilitatorController::class, 'updateProfile'])->name('facilitator.profile.update');

    // Services CRUD
    Route::get('/services', [FacilitatorController::class, 'services'])->name('facilitator.services');
    Route::get('/services/create', [FacilitatorController::class, 'createService'])->name('facilitator.services.create');
    Route::post('/services', [FacilitatorController::class, 'storeService'])->name('facilitator.services.store');
    Route::get('/services/{service}/edit', [FacilitatorController::class, 'editService'])->name('facilitator.services.edit');
    Route::put('/services/{service}', [FacilitatorController::class, 'updateService'])->name('facilitator.services.update');
    Route::delete('/services/{service}', [FacilitatorController::class, 'destroyService'])->name('facilitator.services.destroy');

    // Requirements CRUD
    Route::get('/requirements', [FacilitatorController::class, 'requirements'])->name('facilitator.requirements');
    Route::post('/requirements', [FacilitatorController::class, 'storeRequirement'])->name('facilitator.requirements.store');
    Route::delete('/requirements/{requirement}', [FacilitatorController::class, 'destroyRequirement'])->name('facilitator.requirements.destroy');

    // Eligibility criteria questions
    Route::get('/eligibility', [FacilitatorController::class, 'eligibility'])->name('facilitator.eligibility');
    Route::post('/eligibility', [FacilitatorController::class, 'storeQuestion'])->name('facilitator.eligibility.store');
    Route::get('/eligibility/{question}/edit', [FacilitatorController::class, 'editQuestion'])->name('facilitator.eligibility.edit');
    Route::put('/eligibility/{question}', [FacilitatorController::class, 'updateQuestion'])->name('facilitator.eligibility.update');
    Route::delete('/eligibility/{question}', [FacilitatorController::class, 'destroyQuestion'])->name('facilitator.eligibility.destroy');

    // Users / Citizens Registry CRUD
    Route::get('/users', [FacilitatorController::class, 'users'])->name('facilitator.users');
    Route::get('/users/create', [FacilitatorController::class, 'createUser'])->name('facilitator.users.create');
    Route::post('/users', [FacilitatorController::class, 'storeUser'])->name('facilitator.users.store');
    Route::get('/users/{user}', [FacilitatorController::class, 'showUser'])->name('facilitator.users.show');
    Route::get('/users/{user}/edit', [FacilitatorController::class, 'editUser'])->name('facilitator.users.edit');
    Route::put('/users/{user}', [FacilitatorController::class, 'updateUser'])->name('facilitator.users.update');
    Route::delete('/users/{user}', [FacilitatorController::class, 'destroyUser'])->name('facilitator.users.destroy');

    // Assessments history & CRUD
    Route::get('/assessments', [FacilitatorController::class, 'assessments'])->name('facilitator.assessments');
    Route::get('/assessments/{assessment}', [FacilitatorController::class, 'showAssessment'])->name('facilitator.assessments.show');
    Route::delete('/assessments/{assessment}', [FacilitatorController::class, 'destroyAssessment'])->name('facilitator.assessments.destroy');

    // Reassessments
    Route::get('/reassessments', [FacilitatorController::class, 'reassessments'])->name('facilitator.reassessments');
    Route::post('/reassessments/{reassessment}/status', [FacilitatorController::class, 'updateReassessmentStatus'])->name('facilitator.reassessments.update_status');

    // Checklists / Applications & Validation
    Route::get('/applications', [FacilitatorController::class, 'applications'])->name('facilitator.applications');
    Route::get('/applications/create', [FacilitatorController::class, 'createApplication'])->name('facilitator.applications.create');
    Route::post('/applications', [FacilitatorController::class, 'storeApplication'])->name('facilitator.applications.store');
    Route::get('/applications/{checklist}', [FacilitatorController::class, 'showApplication'])->name('facilitator.applications.show');
    Route::get('/applications/{checklist}/edit', [FacilitatorController::class, 'editApplication'])->name('facilitator.applications.edit');
    Route::put('/applications/{checklist}', [FacilitatorController::class, 'updateApplication'])->name('facilitator.applications.update');
    Route::delete('/applications/{checklist}', [FacilitatorController::class, 'destroyApplication'])->name('facilitator.applications.destroy');
    Route::post('/applications/{checklist}/status', [FacilitatorController::class, 'updateApplicationStatus'])->name('facilitator.applications.update_status');
    Route::post('/checklist-items/{item}/status', [FacilitatorController::class, 'updateItemStatus'])->name('facilitator.checklist_items.update_status');
    Route::post('/checklist-items/{item}/auto-verify', [FacilitatorController::class, 'autoVerifyItem'])->name('facilitator.checklist_items.auto_verify');

    // Inquiries Management
    Route::get('/inquiries', [FacilitatorController::class, 'inquiries'])->name('facilitator.inquiries');
    Route::post('/inquiries/{inquiry}/reply', [FacilitatorController::class, 'replyInquiry'])->name('facilitator.inquiries.reply');
    Route::post('/inquiries/{inquiry}/ai-draft', [FacilitatorController::class, 'generateAIDraft'])->name('facilitator.inquiries.ai_draft');
    Route::post('/inquiries/{inquiry}/status', [FacilitatorController::class, 'updateInquiryStatus'])->name('facilitator.inquiries.update_status');

    // Document Templates Management
    Route::get('/templates', [FacilitatorController::class, 'templates'])->name('facilitator.templates');
    Route::post('/templates', [FacilitatorController::class, 'storeTemplate'])->name('facilitator.templates.store');
    Route::delete('/templates/{template}', [FacilitatorController::class, 'destroyTemplate'])->name('facilitator.templates.destroy');
});

Route::get('/run-migrations', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);

        return 'Migrations ran successfully: <br><pre>'.Artisan::output().'</pre>';
    } catch (Exception $e) {
        return 'Migration failed: '.$e->getMessage();
    }
});
