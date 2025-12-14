<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Display analytics dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        Log::info('Analytics page accessed', [
            'user_id' => $user->id,
        ]);

        try {
            $analytics = $this->adminService->getAnalyticsData();
            $title = 'Analytics & Reports';

            return view('admin.analytics.index', compact('analytics', 'title'));
        } catch (\Exception $e) {
            Log::error('Failed to load analytics page', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to load analytics. Please try again.']);
        }
    }
}
