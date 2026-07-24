<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FacilitatorController;
use App\Http\Controllers\ResidentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    if (Auth::check() && Auth::user()->isFacilitator()) {
        return redirect()->route('facilitator.dashboard');
    }

    return redirect()->route('resident.home');
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

// Resident Public Routes
Route::prefix('resident')->middleware([EnsureEmailIsVerifiedIfLoggedIn::class])->group(function () {
    Route::get('/home', [ResidentController::class, 'home'])->name('resident.home');
    Route::get('/eligibility', [ResidentController::class, 'eligibility'])->name('resident.eligibility');
    Route::get('/inquiry', [ResidentController::class, 'inquiry'])->name('resident.inquiry');
    Route::post('/inquiry/manual', [ResidentController::class, 'submitManualInquiry'])->name('resident.inquiry.manual');

    // Inquiries Reply & Unsend (Moved for Guest Access)
    Route::get('/inquiry/{inquiry}/messages', [ResidentController::class, 'getMessages'])->name('resident.inquiry.messages');
    Route::post('/inquiry/{inquiry}/reply', [ResidentController::class, 'replyInquiry'])->name('resident.inquiry.reply');
    Route::delete('/inquiry/{inquiry}', [ResidentController::class, 'deleteInquiry'])->name('resident.inquiry.delete_inquiry');
    Route::delete('/inquiry/replies/{response}', [ResidentController::class, 'deleteReply'])->name('resident.inquiry.delete_reply');
});

// Resident Protected Routes
Route::middleware(['auth', 'role:resident', 'verified'])->prefix('resident')->group(function () {
    // Eligibility Assessment
    Route::get('/eligibility/assess/{service}', [ResidentController::class, 'showAssessForm'])->name('resident.eligibility.assess');
    Route::post('/eligibility/assess/{service}', [ResidentController::class, 'processAssessForm'])->name('resident.eligibility.assess.submit');
    Route::get('/eligibility/result/{refNo}', [ResidentController::class, 'showAssessResult'])->name('resident.eligibility.result');
    Route::post('/eligibility/reassess/{service}', [ResidentController::class, 'requestReassessment'])->name('resident.eligibility.reassess');

    // Checklist & Document Upload
    Route::get('/eligibility/checklist/{service}', [ResidentController::class, 'checklist'])->name('resident.eligibility.checklist');
    Route::post('/eligibility/checklist/{service}/upload/{requirement}', [ResidentController::class, 'uploadDocument'])->name('resident.eligibility.upload');
    Route::post('/eligibility/checklist/{service}/type', [ResidentController::class, 'setApplicationType'])->name('resident.eligibility.set_type');
    Route::post('/eligibility/apply/{service}', [ResidentController::class, 'apply'])->name('resident.eligibility.apply');
    Route::post('/eligibility/checklist/{service}/edit', [ResidentController::class, 'unlockChecklist'])->name('resident.eligibility.checklist.edit');

    // Profile Settings
    Route::get('/profile', [ResidentController::class, 'profile'])->name('resident.profile');
    Route::get('/profile/edit', [ResidentController::class, 'editProfile'])->name('resident.profile.edit');
    Route::post('/profile/edit', [ResidentController::class, 'updateProfile'])->name('resident.profile.update');
    Route::post('/profile/avatar', [ResidentController::class, 'updateAvatar'])->name('resident.profile.avatar');

    // Legal Pages
    Route::get('/legal/terms', function () {
        return view('legal.terms');
    })->name('resident.legal.terms');
    Route::get('/legal/privacy', function () {
        return view('legal.privacy');
    })->name('resident.legal.privacy');
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
    Route::post('/services/translate', [FacilitatorController::class, 'translateService'])->name('facilitator.services.translate');
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

    // Users / Residents Registry CRUD
    Route::get('/users', [FacilitatorController::class, 'users'])->name('facilitator.users');
    Route::get('/users/create', [FacilitatorController::class, 'createUser'])->name('facilitator.users.create');
    Route::post('/users', [FacilitatorController::class, 'storeUser'])->name('facilitator.users.store');
    Route::get('/users/{user}', [FacilitatorController::class, 'showUser'])->name('facilitator.users.show');
    Route::get('/users/{user}/valid-id', [FacilitatorController::class, 'viewValidId'])->name('facilitator.users.valid_id');
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
    Route::get('/applications/{checklist}/download-all', [FacilitatorController::class, 'downloadAllApplicationFiles'])->name('facilitator.applications.download_all');
    Route::get('/applications/{checklist}/edit', [FacilitatorController::class, 'editApplication'])->name('facilitator.applications.edit');
    Route::put('/applications/{checklist}', [FacilitatorController::class, 'updateApplication'])->name('facilitator.applications.update');
    Route::delete('/applications/{checklist}', [FacilitatorController::class, 'destroyApplication'])->name('facilitator.applications.destroy');
    Route::post('/applications/{checklist}/status', [FacilitatorController::class, 'updateApplicationStatus'])->name('facilitator.applications.update_status');
    Route::get('/checklist-items/{item}/document', [FacilitatorController::class, 'viewDocument'])->name('facilitator.checklist_items.document');
    Route::post('/checklist-items/{item}/status', [FacilitatorController::class, 'updateItemStatus'])->name('facilitator.checklist_items.update_status');
    Route::post('/checklist-items/batch/{checklist}', [FacilitatorController::class, 'batchUpdateChecklistItems'])->name('facilitator.checklist_items.batch_update');
    Route::post('/checklist-items/{item}/auto-verify', [FacilitatorController::class, 'autoVerifyItem'])->name('facilitator.checklist_items.auto_verify');

    // Inquiries Management
    Route::get('/inquiries', [FacilitatorController::class, 'inquiries'])->name('facilitator.inquiries');
    Route::get('/inquiries/{inquiry}/messages', [FacilitatorController::class, 'getMessages'])->name('facilitator.inquiries.messages');
    Route::post('/inquiries/{inquiry}/reply', [FacilitatorController::class, 'replyInquiry'])->name('facilitator.inquiries.reply');
    Route::post('/inquiries/{inquiry}/status', [FacilitatorController::class, 'updateInquiryStatus'])->name('facilitator.inquiries.update_status');
    Route::delete('/inquiries/{inquiry}', [FacilitatorController::class, 'deleteInquiry'])->name('facilitator.inquiries.delete');

    // Document Templates Management
    Route::get('/templates', [FacilitatorController::class, 'templates'])->name('facilitator.templates');
    Route::post('/templates', [FacilitatorController::class, 'storeTemplate'])->name('facilitator.templates.store');
    Route::delete('/templates/{template}', [FacilitatorController::class, 'destroyTemplate'])->name('facilitator.templates.destroy');

    // Reports & Data Export Center
    Route::get('/reports', [FacilitatorController::class, 'reports'])->name('facilitator.reports');
    Route::get('/reports/export/applications', [FacilitatorController::class, 'exportApplications'])->name('facilitator.reports.export.applications');
    Route::get('/reports/export/residents', [FacilitatorController::class, 'exportResidents'])->name('facilitator.reports.export.residents');
    Route::get('/reports/export/assessments', [FacilitatorController::class, 'exportAssessments'])->name('facilitator.reports.export.assessments');
    Route::get('/reports/export/inquiries', [FacilitatorController::class, 'exportInquiries'])->name('facilitator.reports.export.inquiries');
    Route::get('/reports/export/all', [FacilitatorController::class, 'exportAllMasterReport'])->name('facilitator.reports.export.all');
});

Route::get('/run-migrations', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);

        return 'Migrations ran successfully: <br><pre>'.Artisan::output().'</pre>';
    } catch (Exception $e) {
        return 'Migration failed: '.$e->getMessage();
    }
});
