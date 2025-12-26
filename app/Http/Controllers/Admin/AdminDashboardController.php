<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Display admin dashboard overview.
     *
     * Note: Admin profile information is stored in the admin_profiles table
     * and can be accessed via $user->adminProfile relationship.
     * The User model has a adminProfile() relationship method.
     */
    public function index()
    {
        $user = auth()->user();

        Log::info('Admin dashboard accessed', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        try {
            $stats = $this->adminService->getDashboardStats();
            $title = 'Admin Dashboard';

            return view('pages.dashboard.admin.dashboard.index', compact('stats', 'title'));
        } catch (\Exception $e) {
            Log::error('Failed to load admin dashboard', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to load dashboard. Please try again.']);
        }
    }
}
