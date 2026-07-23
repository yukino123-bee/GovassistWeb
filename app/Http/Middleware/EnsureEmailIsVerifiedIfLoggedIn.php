<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedIfLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() &&
            $request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail()) {

            return $request->expectsJson()
                    ? abort(403, 'Your email address is not verified.')
                    : redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
