<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UniversityProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('public');
        $this->middleware('role:'.UserRoleEnum::UNIVERSITY->value)->except('public');
    }

    /**
     * Show the university's profile (private view).
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        // TODO: Implement when UniversityProfile model exists
        // $profile = $user->universityProfile;
        // if (!$profile) {
        //     return redirect()->route('dashboard')
        //         ->with('error', 'University profile not found. Please contact support.');
        // }

        return view('pages.university.profile.show', [
            'message' => 'University profile feature coming soon.',
            // 'profile' => $profile,
        ]);
    }

    /**
     * Show the university profile edit page.
     */
    public function edit(): View|RedirectResponse
    {
        $user = Auth::user();

        // TODO: Implement when UniversityProfile model exists

        return view('pages.university.profile.edit', [
            'message' => 'University profile editing feature coming soon.',
        ]);
    }

    /**
     * Update university profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        // TODO: Implement when UniversityProfile model exists

        return redirect()->route('university.profile.show')
            ->with('info', 'University profile update feature coming soon.');
    }

    /**
     * Show public university profile (viewable by anyone).
     */
    public function public(string $id): View
    {
        // TODO: Implement when UniversityProfile model exists
        // $profile = UniversityProfile::where('id', $id)->firstOrFail();

        return view('pages.university.profile.public', [
            'message' => 'Public university profile feature coming soon.',
            'id' => $id,
        ]);
    }
}

