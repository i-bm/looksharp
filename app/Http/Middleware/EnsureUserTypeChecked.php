<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserTypeChecked
{
    /**
     * Handle an incoming request.
     * Redirects users who haven't completed user type selection to the selection page.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check authenticated users
        if (! Auth::check()) {
            return $next($request);
        }

        // Allow access to user type selection routes
        if ($request->routeIs('register.select-type') || $request->routeIs('register.select-type.store')) {
            return $next($request);
        }

        $user = Auth::user();

        // Check if user has completed type selection
        if (! $user->user_type_checked) {
            Log::info('User type not checked, redirecting to selection page', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->route('register.select-type')
                ->with('info', 'Please complete your registration by selecting your account type.');
        }

        return $next($request);
    }
}
