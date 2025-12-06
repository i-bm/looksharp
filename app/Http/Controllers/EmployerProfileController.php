<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployerProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('public');
        $this->middleware('role:'.UserRoleEnum::EMPLOYER->value)->except('public');
    }

    /**
     * Show the employer's company profile (private view).
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        // TODO: Implement when EmployerProfile or Company model exists
        // $company = $user->company;
        // if (!$company) {
        //     return redirect()->route('dashboard')
        //         ->with('error', 'Company profile not found. Please contact support.');
        // }

        return view('pages.employer.company.show', [
            'message' => 'Company profile feature coming soon.',
            // 'company' => $company,
        ]);
    }

    /**
     * Show the company profile edit page.
     */
    public function edit(): View|RedirectResponse
    {
        $user = Auth::user();

        // TODO: Implement when EmployerProfile or Company model exists

        return view('pages.employer.company.edit', [
            'message' => 'Company profile editing feature coming soon.',
        ]);
    }

    /**
     * Update company profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        // TODO: Implement when EmployerProfile or Company model exists

        return redirect()->route('employer.company.show')
            ->with('info', 'Company profile update feature coming soon.');
    }

    /**
     * Show public company profile (viewable by anyone).
     */
    public function public(string $id): View
    {
        // TODO: Implement when EmployerProfile or Company model exists
        // $company = Company::where('id', $id)->firstOrFail();

        return view('pages.employer.company.public', [
            'message' => 'Public company profile feature coming soon.',
            'id' => $id,
        ]);
    }
}

