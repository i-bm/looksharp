<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        Log::info('User management page accessed', [
            'user_id' => $user->id,
            'filters' => $request->all(),
        ]);

        try {
            $filters = [
                'user_type' => $request->input('user_type'),
                'is_active' => $request->input('is_active'),
                'search' => $request->input('search'),
                'sort_by' => $request->input('sort_by', 'created_at'),
                'sort_order' => $request->input('sort_order', 'desc'),
                'per_page' => $request->input('per_page', 15),
            ];

            $users = $this->adminService->getUserList($filters);
            $title = 'User Management';

            return view('admin.users.index', compact('users', 'filters', 'title'));
        } catch (\Exception $e) {
            Log::error('Failed to load user management page', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to load users. Please try again.']);
        }
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $user = auth()->user();

        Log::info('User details accessed', [
            'admin_user_id' => $user->id,
            'viewed_user_id' => $id,
        ]);

        try {
            $targetUser = User::with(['adminProfile', 'talentProfile'])->findOrFail($id);
            $title = 'User Details';

            return view('admin.users.show', compact('targetUser', 'title'));
        } catch (\Exception $e) {
            Log::error('Failed to load user details', [
                'admin_user_id' => $user->id,
                'viewed_user_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'User not found.']);
        }
    }

    /**
     * Activate a user account.
     */
    public function activate(string $id)
    {
        $adminUser = auth()->user();

        Log::info('User activation requested', [
            'admin_user_id' => $adminUser->id,
            'target_user_id' => $id,
        ]);

        try {
            $user = $this->adminService->activateUser($id);

            Log::info('User activated successfully', [
                'admin_user_id' => $adminUser->id,
                'target_user_id' => $id,
            ]);

            return back()->with('success', 'User activated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to activate user', [
                'admin_user_id' => $adminUser->id,
                'target_user_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Deactivate a user account.
     */
    public function deactivate(string $id)
    {
        $adminUser = auth()->user();

        Log::info('User deactivation requested', [
            'admin_user_id' => $adminUser->id,
            'target_user_id' => $id,
        ]);

        try {
            $user = $this->adminService->deactivateUser($id);

            Log::info('User deactivated successfully', [
                'admin_user_id' => $adminUser->id,
                'target_user_id' => $id,
            ]);

            return back()->with('success', 'User deactivated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to deactivate user', [
                'admin_user_id' => $adminUser->id,
                'target_user_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
