<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Services\GTECService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstitutionController extends Controller
{
    protected $gtecService;

    public function __construct(GTECService $gtecService)
    {
        $this->gtecService = $gtecService;
    }

    /**
     * Display a listing of institutions.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        Log::info('Institutions management page accessed', [
            'user_id' => $user->id,
            'filters' => $request->all(),
        ]);

        try {
            $query = Institution::query();

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('state', 'like', "%{$search}%");
                });
            }

            // Filter by active status
            if ($request->has('is_active') && $request->is_active !== '') {
                $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
            }

            // Filter by country
            if ($request->has('country') && $request->country) {
                $query->where('country', $request->country);
            }

            $sortBy = $request->input('sort_by', 'name');
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $institutions = $query->paginate(20)->withQueryString();

            $title = 'Institutions';

            return view('admin.institutions.index', compact('institutions', 'title'));
        } catch (\Exception $e) {
            Log::error('Failed to load institutions', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to load institutions. Please try again.']);
        }
    }

    /**
     * Show the form for creating a new institution.
     */
    public function create()
    {
        $user = auth()->user();

        Log::info('Institution create form accessed', [
            'user_id' => $user->id,
        ]);

        $title = 'Create Institution';

        return view('admin.institutions.create', compact('title'));
    }

    /**
     * Store a newly created institution.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        Log::info('Institution creation requested', [
            'user_id' => $user->id,
            'data_keys' => array_keys($request->all()),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'student_email_domain' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:500'],
            'logo' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            return DB::transaction(function () use ($validated, $user) {
                $institution = Institution::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'] ?? null,
                    'student_email_domain' => $validated['student_email_domain'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'state' => $validated['state'] ?? null,
                    'zip' => $validated['zip'] ?? null,
                    'country' => $validated['country'] ?? 'Ghana',
                    'website' => $validated['website'] ?? null,
                    'logo' => $validated['logo'] ?? null,
                    'is_active' => $validated['is_active'] ?? true,
                ]);

                Log::info('Institution created successfully', [
                    'user_id' => $user->id,
                    'institution_id' => $institution->id,
                    'name' => $institution->name,
                ]);

                return redirect()->route('admin.institutions.index')
                    ->with('success', 'Institution created successfully.');
            });
        } catch (\Exception $e) {
            Log::error('Failed to create institution', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors(['error' => 'Failed to create institution. Please try again.']);
        }
    }

    /**
     * Display the specified institution.
     */
    public function show(string $id)
    {
        $user = auth()->user();

        Log::info('Institution details accessed', [
            'user_id' => $user->id,
            'institution_id' => $id,
        ]);

        try {
            $institution = Institution::withCount(['users', 'education'])->findOrFail($id);
            $title = 'Institution Details';

            return view('admin.institutions.show', compact('institution', 'title'));
        } catch (\Exception $e) {
            Log::error('Failed to load institution', [
                'user_id' => $user->id,
                'institution_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Institution not found.']);
        }
    }

    /**
     * Show the form for editing the specified institution.
     */
    public function edit(string $id)
    {
        $user = auth()->user();

        Log::info('Institution edit form accessed', [
            'user_id' => $user->id,
            'institution_id' => $id,
        ]);

        try {
            $institution = Institution::findOrFail($id);
            $title = 'Edit Institution';

            return view('admin.institutions.edit', compact('institution', 'title'));
        } catch (\Exception $e) {
            Log::error('Failed to load institution for editing', [
                'user_id' => $user->id,
                'institution_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Institution not found.']);
        }
    }

    /**
     * Update the specified institution.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();

        Log::info('Institution update requested', [
            'user_id' => $user->id,
            'institution_id' => $id,
            'data_keys' => array_keys($request->all()),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'student_email_domain' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:500'],
            'logo' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            return DB::transaction(function () use ($validated, $id, $user) {
                $institution = Institution::findOrFail($id);

                $institution->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'] ?? null,
                    'student_email_domain' => $validated['student_email_domain'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'state' => $validated['state'] ?? null,
                    'zip' => $validated['zip'] ?? null,
                    'country' => $validated['country'] ?? $institution->country,
                    'website' => $validated['website'] ?? null,
                    'logo' => $validated['logo'] ?? null,
                    'is_active' => $validated['is_active'] ?? $institution->is_active,
                ]);

                Log::info('Institution updated successfully', [
                    'user_id' => $user->id,
                    'institution_id' => $id,
                    'name' => $institution->name,
                ]);

                return redirect()->route('admin.institutions.index')
                    ->with('success', 'Institution updated successfully.');
            });
        } catch (\Exception $e) {
            Log::error('Failed to update institution', [
                'user_id' => $user->id,
                'institution_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors(['error' => 'Failed to update institution. Please try again.']);
        }
    }

    /**
     * Remove the specified institution.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();

        Log::info('Institution deletion requested', [
            'user_id' => $user->id,
            'institution_id' => $id,
        ]);

        try {
            return DB::transaction(function () use ($id, $user) {
                $institution = Institution::findOrFail($id);

                // Check if institution has users or education records
                if ($institution->users()->count() > 0 || $institution->education()->count() > 0) {
                    return back()->withErrors(['error' => 'Cannot delete institution that has associated users or education records.']);
                }

                $institution->delete();

                Log::info('Institution deleted successfully', [
                    'user_id' => $user->id,
                    'institution_id' => $id,
                    'name' => $institution->name,
                ]);

                return redirect()->route('admin.institutions.index')
                    ->with('success', 'Institution deleted successfully.');
            });
        } catch (\Exception $e) {
            Log::error('Failed to delete institution', [
                'user_id' => $user->id,
                'institution_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Failed to delete institution. Please try again.']);
        }
    }

    /**
     * Sync institutions from GTEC API.
     */
    public function syncFromGTEC()
    {
        $user = auth()->user();

        Log::info('Institution GTEC sync requested', [
            'user_id' => $user->id,
        ]);

        try {
            $institutions = $this->gtecService->fetchAllInstitutions();

            if (empty($institutions)) {
                return back()->withErrors(['error' => 'No institutions found from GTEC API.']);
            }

            DB::transaction(function () use ($institutions, $user) {
                $created = 0;
                $updated = 0;

                foreach ($institutions as $institutionData) {
                    $mappedData = [
                        'name' => $institutionData['name'] ?? '',
                        'email' => $institutionData['email'] ?? null,
                        'city' => $institutionData['location'] ?? null,
                        'state' => $institutionData['region'] ?? null,
                        'country' => 'Ghana',
                        'is_active' => ($institutionData['accredStatus'] ?? '') === 'Active',
                    ];

                    $institution = Institution::updateOrCreate(
                        ['name' => $mappedData['name']],
                        $mappedData
                    );

                    if ($institution->wasRecentlyCreated) {
                        $created++;
                    } else {
                        $updated++;
                    }
                }

                Log::info('Institutions synced from GTEC successfully', [
                    'user_id' => $user->id,
                    'created' => $created,
                    'updated' => $updated,
                ]);
            });

            return back()->with('success', 'Institutions synced successfully from GTEC API.');
        } catch (\Exception $e) {
            Log::error('Failed to sync institutions from GTEC', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Failed to sync institutions. Please try again.']);
        }
    }
}
