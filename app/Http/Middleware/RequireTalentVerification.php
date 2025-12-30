<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequireTalentVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole('talent')) {
            Log::warning('RequireTalentVerification: User is not a talent', [
                'user_id' => $user?->id,
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'This feature is only available to talent.');
        }

        $profile = $user->talentProfile;

        if (! $profile) {
            Log::warning('RequireTalentVerification: Talent has no profile', [
                'user_id' => $user->id,
            ]);

            return redirect()->route('talent.profile.show')
                ->with('error', 'Please complete your profile first.');
        }

        if (! $profile->isVerified()) {
            Log::info('RequireTalentVerification: Talent not verified', [
                'user_id' => $user->id,
                'profile_id' => $profile->id,
                'verification_status' => $profile->verification_status,
            ]);

            $message = 'You must verify your profile to apply for jobs. Please submit your verification document for review.';

            if ($profile->verification_status === 'pending') {
                $message = 'Your verification is pending review. You will be able to apply for jobs once your verification is approved.';
            } elseif ($profile->verification_status === 'rejected') {
                $message = 'Your verification was rejected. Please submit a new verification document to apply for jobs.';
            }

            return redirect()->route('talent.profile.verification.show')
                ->with('error', $message);
        }

        return $next($request);
    }
}
