<?php

namespace App\Services;

use App\Enums\EmployerCompanyStatusEnum;
use App\Enums\EmployerCompanyVerificationStatusEnum;
use App\Models\EmployerCompany;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminService
{
    /**
     * Get dashboard statistics.
     *
     * @return array<string, mixed>
     */
    public function getDashboardStats(): array
    {
        Log::info('Fetching admin dashboard statistics');

        try {
            $employerCompanyCountsByStatus = EmployerCompany::query()
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $employerCompanyCountsByVerification = EmployerCompany::query()
                ->select('verification_status', DB::raw('count(*) as count'))
                ->groupBy('verification_status')
                ->pluck('count', 'verification_status')
                ->toArray();

            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'inactive_users' => User::where('is_active', false)->count(),
                'users_by_type' => User::select('user_type', DB::raw('count(*) as count'))
                    ->groupBy('user_type')
                    ->pluck('count', 'user_type')
                    ->toArray(),
                'recent_users' => User::with(['adminProfile', 'talentProfile'])
                    ->latest()
                    ->take(5)
                    ->get(),
                'users_created_today' => User::whereDate('created_at', today())->count(),
                'users_created_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'users_created_this_month' => User::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'employer_companies_by_status' => $employerCompanyCountsByStatus,
                'employer_companies_submitted' => (int) ($employerCompanyCountsByStatus[EmployerCompanyStatusEnum::SUBMITTED->value] ?? 0),
                'employer_companies_needs_changes' => (int) ($employerCompanyCountsByStatus[EmployerCompanyStatusEnum::NEEDS_CHANGES->value] ?? 0),
                'employer_companies_approved' => (int) ($employerCompanyCountsByStatus[EmployerCompanyStatusEnum::APPROVED->value] ?? 0),
                'employer_companies_verification_pending' => (int) ($employerCompanyCountsByVerification[EmployerCompanyVerificationStatusEnum::PENDING->value] ?? 0),
            ];

            Log::info('Admin dashboard statistics retrieved successfully', [
                'total_users' => $stats['total_users'],
                'active_users' => $stats['active_users'],
            ]);

            return $stats;
        } catch (\Exception $e) {
            Log::error('Failed to fetch admin dashboard statistics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Failed to retrieve dashboard statistics. Please try again.');
        }
    }

    /**
     * Get paginated and filtered user list.
     *
     * @param  array<string, mixed>  $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserList(array $filters = [])
    {
        Log::info('Fetching user list', [
            'filters' => $filters,
        ]);

        try {
            $query = User::with(['adminProfile', 'talentProfile']);

            // Filter by user type
            if (isset($filters['user_type']) && $filters['user_type']) {
                $query->where('user_type', $filters['user_type']);
            }

            // Filter by active status
            if (isset($filters['is_active']) && $filters['is_active'] !== '') {
                $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
            }

            // Search by email or name
            if (isset($filters['search']) && $filters['search']) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $filters['per_page'] ?? 15;

            $users = $query->paginate($perPage)->withQueryString();

            Log::info('User list retrieved successfully', [
                'total_users' => $users->total(),
                'current_page' => $users->currentPage(),
            ]);

            return $users;
        } catch (\Exception $e) {
            Log::error('Failed to fetch user list', [
                'filters' => $filters,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Failed to retrieve user list. Please try again.');
        }
    }

    /**
     * Activate a user account.
     *
     * @throws \Exception
     */
    public function activateUser(string $userId): User
    {
        Log::info('Activating user', [
            'user_id' => $userId,
        ]);

        try {
            return DB::transaction(function () use ($userId) {
                $user = User::findOrFail($userId);

                if ($user->is_active) {
                    Log::warning('User is already active', [
                        'user_id' => $userId,
                    ]);

                    return $user;
                }

                $user->update(['is_active' => true]);

                Log::info('User activated successfully', [
                    'user_id' => $userId,
                    'email' => $user->email,
                ]);

                return $user->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to activate user', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Failed to activate user. Please try again.');
        }
    }

    /**
     * Deactivate a user account.
     *
     * @throws \Exception
     */
    public function deactivateUser(string $userId): User
    {
        Log::info('Deactivating user', [
            'user_id' => $userId,
        ]);

        try {
            return DB::transaction(function () use ($userId) {
                $user = User::findOrFail($userId);

                // Prevent deactivating own account
                $currentUser = Auth::user();
                if ($currentUser && $user->id === $currentUser->id) {
                    throw new \Exception('You cannot deactivate your own account.');
                }

                if (! $user->is_active) {
                    Log::warning('User is already inactive', [
                        'user_id' => $userId,
                    ]);

                    return $user;
                }

                $user->update(['is_active' => false]);

                Log::info('User deactivated successfully', [
                    'user_id' => $userId,
                    'email' => $user->email,
                ]);

                return $user->fresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to deactivate user', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Failed to deactivate user. Please try again.');
        }
    }

    /**
     * Get analytics data.
     *
     * @return array<string, mixed>
     */
    public function getAnalyticsData(): array
    {
        Log::info('Fetching admin analytics data');

        try {
            $analytics = [
                'user_growth' => $this->getUserGrowthData(),
                'users_by_type' => $this->getUsersByTypeData(),
                'recent_activity' => $this->getRecentActivityData(),
            ];

            Log::info('Admin analytics data retrieved successfully');

            return $analytics;
        } catch (\Exception $e) {
            Log::error('Failed to fetch admin analytics data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Failed to retrieve analytics data. Please try again.');
        }
    }

    /**
     * Get user growth data for the last 12 months.
     *
     * @return array<string, mixed>
     */
    private function getUserGrowthData(): array
    {
        $months = [];
        $counts = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M Y');
            $counts[] = User::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return [
            'labels' => $months,
            'data' => $counts,
        ];
    }

    /**
     * Get users grouped by type.
     *
     * @return array<string, int>
     */
    private function getUsersByTypeData(): array
    {
        return User::select('user_type', DB::raw('count(*) as count'))
            ->groupBy('user_type')
            ->pluck('count', 'user_type')
            ->toArray();
    }

    /**
     * Get recent activity data.
     *
     * @return array<string, mixed>
     */
    private function getRecentActivityData(): array
    {
        return [
            'recent_users' => User::with(['adminProfile', 'talentProfile'])->latest()->take(10)->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->full_name,
                    'user_type' => $user->user_type,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray(),
        ];
    }
}
