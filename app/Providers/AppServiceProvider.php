<?php

namespace App\Providers;

use App\Models\ReassessmentRequest;
use App\Models\UserChecklist;
use App\Models\UserInquiry;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request): Limit {
            return Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip());
        });

        RateLimiter::for('registration', fn (Request $request): Limit => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('verification', fn (Request $request): Limit => Limit::perMinute(6)->by((string) ($request->user()?->getAuthIdentifier() ?? $request->ip())));
        RateLimiter::for('inquiries', fn (Request $request): Limit => Limit::perMinute(20)->by((string) ($request->user()?->getAuthIdentifier() ?? $request->ip())));

        view()->composer(['layouts.facilitator', 'facilitator.*'], function ($view) {
            // Fetch pending applications
            $pendingApps = UserChecklist::with(['user', 'service'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($app) {
                    return [
                        'type' => 'application',
                        'title' => 'New Application',
                        'message' => ($app->user?->name ?? 'Resident').' applied for '.($app->service?->name_en ?? 'Service'),
                        'time' => $app->created_at,
                        'link' => route('facilitator.applications.show', $app->id),
                    ];
                });

            // Fetch pending manual inquiries
            $pendingInquiries = UserInquiry::with(['user', 'service'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($inquiry) {
                    $name = $inquiry->user ? $inquiry->user->name : ($inquiry->guest_name ?? 'Guest');

                    return [
                        'type' => 'inquiry',
                        'title' => 'New Inquiry',
                        'message' => 'Inquiry from '.$name.' about '.($inquiry->service?->name_en ?? 'general'),
                        'time' => $inquiry->created_at,
                        'link' => route('facilitator.inquiries'),
                    ];
                });

            // Fetch pending reassessment requests
            $pendingReassessments = ReassessmentRequest::with(['user', 'service'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($reassess) {
                    return [
                        'type' => 'reassessment',
                        'title' => 'Reassessment Request',
                        'message' => ($reassess->user?->name ?? 'Resident').' requested reassessment for '.($reassess->service?->name_en ?? 'Service'),
                        'time' => $reassess->created_at,
                        'link' => route('facilitator.reassessments'),
                    ];
                });

            // Merge and sort
            $notifications = collect()
                ->merge($pendingApps)
                ->merge($pendingInquiries)
                ->merge($pendingReassessments)
                ->sortByDesc('time')
                ->take(10);

            $pendingAppsCount = UserChecklist::where('status', 'pending')->count();
            $pendingInquiriesCount = UserInquiry::where('status', 'pending')->count();
            $pendingReassessmentsCount = ReassessmentRequest::where('status', 'pending')->count();

            $view->with('adminNotifications', $notifications)
                ->with('pendingAppsCount', $pendingAppsCount)
                ->with('pendingInquiriesCount', $pendingInquiriesCount)
                ->with('pendingReassessmentsCount', $pendingReassessmentsCount);
        });
    }
}
