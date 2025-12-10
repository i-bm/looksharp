<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        $user = Auth::user();
        $talentProfile = $user->talentProfile;

        // Profile completion data
        $profileCompletion = $talentProfile?->profile_completeness_score ?? 0;
        $profileCompletionChange = 0; // Placeholder - can be calculated from audit logs in the future

        // Application data (placeholder until Application model exists)
        $activeApplications = 0;
        $newApplications = 0;
        $recentApplications = [];

        // Opportunity data (placeholder until Opportunity model exists)
        $savedOpportunities = 0;
        $recommendedOpportunities = [];

        // Application chart data (placeholder - empty data for now)
        $applicationChartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        ];

        return view('pages.dashboard.index', compact(
            'title',
            'profileCompletion',
            'profileCompletionChange',
            'activeApplications',
            'newApplications',
            'savedOpportunities',
            'recentApplications',
            'recommendedOpportunities',
            'applicationChartData'
        ));
    }
}
