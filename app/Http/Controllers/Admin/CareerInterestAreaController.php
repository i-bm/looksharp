<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerInterestArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CareerInterestAreaController extends Controller
{
    /**
     * Display a listing of career interest areas.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        Log::info('Career interest areas management page accessed', [
            'user_id' => $user->id,
        ]);

        try {
            $query = CareerInterestArea::with('parent')->orderBy('order');

            // Filter by parent (categories vs subcategories)
            if ($request->has('type')) {
                if ($request->type === 'parents') {
                    $query->parents();
                } elseif ($request->type === 'children') {
                    $query->children();
                }
            }

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            // Filter by active status
            if ($request->has('is_active') && $request->is_active !== '') {
                $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
            }

            $careerInterestAreas = $query->paginate(20)->withQueryString();
            $parentCategories = CareerInterestArea::parents()->orderBy('order')->get();

            $title = 'Career Interest Areas';

            return view('admin.career-interest-areas.index', compact('careerInterestAreas', 'parentCategories', 'title'));
        } catch (\Exception $e) {
            Log::error('Failed to load career interest areas', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to load career interest areas. Please try again.']);
        }
    }

    /**
     * Show the form for creating a new career interest area.
     */
    public function create()
    {
        $user = auth()->user();

        Log::info('Career interest area create form accessed', [
            'user_id' => $user->id,
        ]);

        $parentCategories = CareerInterestArea::parents()->orderBy('order')->get();
        $title = 'Create Career Interest Area';

        return view('admin.career-interest-areas.create', compact('parentCategories', 'title'));
    }

    /**
     * Store a newly created career interest area.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        Log::info('Career interest area creation requested', [
            'user_id' => $user->id,
            'data_keys' => array_keys($request->all()),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:career_interest_areas,slug'],
            'parent_id' => ['nullable', 'exists:career_interest_areas,id'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            return DB::transaction(function () use ($validated, $user) {
                // Generate slug if not provided
                if (empty($validated['slug'])) {
                    $validated['slug'] = Str::slug($validated['name']);
                }

                $careerInterestArea = CareerInterestArea::create([
                    'name' => $validated['name'],
                    'slug' => $validated['slug'],
                    'parent_id' => $validated['parent_id'] ?? null,
                    'order' => $validated['order'] ?? 0,
                    'is_active' => $validated['is_active'] ?? true,
                ]);

                Log::info('Career interest area created successfully', [
                    'user_id' => $user->id,
                    'career_interest_area_id' => $careerInterestArea->id,
                    'name' => $careerInterestArea->name,
                ]);

                return redirect()->route('admin.career-interest-areas.index')
                    ->with('success', 'Career interest area created successfully.');
            });
        } catch (\Exception $e) {
            Log::error('Failed to create career interest area', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors(['error' => 'Failed to create career interest area. Please try again.']);
        }
    }

    /**
     * Display the specified career interest area.
     */
    public function show(string $id)
    {
        $user = auth()->user();

        Log::info('Career interest area details accessed', [
            'user_id' => $user->id,
            'career_interest_area_id' => $id,
        ]);

        try {
            $careerInterestArea = CareerInterestArea::with(['parent', 'children'])->findOrFail($id);
            $title = 'Career Interest Area Details';

            return view('admin.career-interest-areas.show', compact('careerInterestArea', 'title'));
        } catch (\Exception $e) {
            Log::error('Failed to load career interest area', [
                'user_id' => $user->id,
                'career_interest_area_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Career interest area not found.']);
        }
    }

    /**
     * Show the form for editing the specified career interest area.
     */
    public function edit(string $id)
    {
        $user = auth()->user();

        Log::info('Career interest area edit form accessed', [
            'user_id' => $user->id,
            'career_interest_area_id' => $id,
        ]);

        try {
            $careerInterestArea = CareerInterestArea::findOrFail($id);
            $parentCategories = CareerInterestArea::parents()->orderBy('order')->get();
            $title = 'Edit Career Interest Area';

            return view('admin.career-interest-areas.edit', compact('careerInterestArea', 'parentCategories', 'title'));
        } catch (\Exception $e) {
            Log::error('Failed to load career interest area for editing', [
                'user_id' => $user->id,
                'career_interest_area_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Career interest area not found.']);
        }
    }

    /**
     * Update the specified career interest area.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();

        Log::info('Career interest area update requested', [
            'user_id' => $user->id,
            'career_interest_area_id' => $id,
            'data_keys' => array_keys($request->all()),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:career_interest_areas,slug,'.$id],
            'parent_id' => ['nullable', 'exists:career_interest_areas,id', 'not_in:'.$id],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            return DB::transaction(function () use ($validated, $id, $user) {
                $careerInterestArea = CareerInterestArea::findOrFail($id);

                // Generate slug if not provided
                if (empty($validated['slug'])) {
                    $validated['slug'] = Str::slug($validated['name']);
                }

                $careerInterestArea->update([
                    'name' => $validated['name'],
                    'slug' => $validated['slug'],
                    'parent_id' => $validated['parent_id'] ?? $careerInterestArea->parent_id,
                    'order' => $validated['order'] ?? $careerInterestArea->order,
                    'is_active' => $validated['is_active'] ?? $careerInterestArea->is_active,
                ]);

                Log::info('Career interest area updated successfully', [
                    'user_id' => $user->id,
                    'career_interest_area_id' => $id,
                    'name' => $careerInterestArea->name,
                ]);

                return redirect()->route('admin.career-interest-areas.index')
                    ->with('success', 'Career interest area updated successfully.');
            });
        } catch (\Exception $e) {
            Log::error('Failed to update career interest area', [
                'user_id' => $user->id,
                'career_interest_area_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors(['error' => 'Failed to update career interest area. Please try again.']);
        }
    }

    /**
     * Remove the specified career interest area.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();

        Log::info('Career interest area deletion requested', [
            'user_id' => $user->id,
            'career_interest_area_id' => $id,
        ]);

        try {
            return DB::transaction(function () use ($id, $user) {
                $careerInterestArea = CareerInterestArea::findOrFail($id);

                // Check if it has children
                if ($careerInterestArea->children()->count() > 0) {
                    return back()->withErrors(['error' => 'Cannot delete category with subcategories. Please delete subcategories first.']);
                }

                $careerInterestArea->delete();

                Log::info('Career interest area deleted successfully', [
                    'user_id' => $user->id,
                    'career_interest_area_id' => $id,
                    'name' => $careerInterestArea->name,
                ]);

                return redirect()->route('admin.career-interest-areas.index')
                    ->with('success', 'Career interest area deleted successfully.');
            });
        } catch (\Exception $e) {
            Log::error('Failed to delete career interest area', [
                'user_id' => $user->id,
                'career_interest_area_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Failed to delete career interest area. Please try again.']);
        }
    }
}
