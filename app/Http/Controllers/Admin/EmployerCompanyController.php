<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\EmployerCompanyStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\EmployerCompany;
use App\Services\EmployerCompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployerCompanyController extends Controller
{
    public function __construct(private EmployerCompanyService $employerCompanyService) {}

    public function index(Request $request): View
    {
        $user = auth()->user();

        Log::info('Employer companies admin index accessed', [
            'user_id' => $user?->id,
            'filters' => $request->all(),
        ]);

        $status = $request->string('status')->toString();
        $query = EmployerCompany::query()->with(['creator', 'reviewer']);

        if ($status !== '') {
            $query->where('status', $status);
        } else {
            $query->where('status', EmployerCompanyStatusEnum::SUBMITTED->value);
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('legal_name', 'like', "%{$search}%")
                    ->orWhere('official_email', 'like', "%{$search}%");
            });
        }

        $companies = $query->orderByDesc('submitted_at')->orderByDesc('created_at')->paginate(20)->withQueryString();

        $title = 'Employer Companies';

        return view('pages.dashboard.admin.employer-companies.index', compact('companies', 'title', 'status'));
    }

    public function create(): View
    {
        $title = 'Provision Employer Company';

        return view('pages.dashboard.admin.employer-companies.create', compact('title'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        Log::info('Employer company provision requested', [
            'user_id' => $user?->id,
            'data_keys' => array_keys($request->all()),
        ]);

        $validated = $request->validate([
            'invite_email' => ['required', 'email', 'max:255'],
            'legal_name' => ['required', 'string', 'max:255'],
            'trading_name' => ['nullable', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'company_size' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'official_email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'primary_contact_name' => ['nullable', 'string', 'max:255'],
            'primary_contact_title' => ['nullable', 'string', 'max:255'],
            'primary_contact_email' => ['nullable', 'email', 'max:255'],
            'primary_contact_phone' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            $companyData = $validated;
            unset($companyData['invite_email']);

            $company = $this->employerCompanyService->adminProvisionCompanyAndInvite(
                $user,
                $companyData,
                $validated['invite_email']
            );

            return redirect()->route('admin.employer-companies.show', ['id' => $company->id])
                ->with('success', 'Company provisioned and invite sent.');
        } catch (\Exception $e) {
            Log::error('Employer company provision failed', [
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(string $id): View
    {
        $company = EmployerCompany::with(['creator', 'reviewer', 'verifier', 'members'])->findOrFail($id);
        $title = 'Company Review';

        return view('pages.dashboard.admin.employer-companies.show', compact('company', 'title'));
    }

    public function approve(Request $request, string $id): RedirectResponse
    {
        $company = EmployerCompany::findOrFail($id);
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $this->employerCompanyService->adminApprove(auth()->user(), $company, $validated['notes'] ?? null);

        return redirect()->route('admin.employer-companies.show', ['id' => $company->id])
            ->with('success', 'Company approved.');
    }

    public function needsChanges(Request $request, string $id): RedirectResponse
    {
        $company = EmployerCompany::findOrFail($id);
        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:5000'],
        ]);

        $this->employerCompanyService->adminNeedsChanges(auth()->user(), $company, $validated['notes']);

        return redirect()->route('admin.employer-companies.show', ['id' => $company->id])
            ->with('success', 'Company marked as needs changes.');
    }

    public function reject(Request $request, string $id): RedirectResponse
    {
        $company = EmployerCompany::findOrFail($id);
        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:5000'],
        ]);

        $this->employerCompanyService->adminReject(auth()->user(), $company, $validated['notes']);

        return redirect()->route('admin.employer-companies.show', ['id' => $company->id])
            ->with('success', 'Company rejected.');
    }

    public function suspend(Request $request, string $id): RedirectResponse
    {
        $company = EmployerCompany::findOrFail($id);
        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:5000'],
        ]);

        $this->employerCompanyService->adminSuspend(auth()->user(), $company, $validated['notes']);

        return redirect()->route('admin.employer-companies.show', ['id' => $company->id])
            ->with('success', 'Company suspended.');
    }

    public function verify(Request $request, string $id): RedirectResponse
    {
        $company = EmployerCompany::findOrFail($id);
        $validated = $request->validate([
            'verified' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $admin = auth()->user();

        Log::info('Employer company verification action requested', [
            'admin_user_id' => $admin?->id,
            'company_id' => $company->id,
            'verified' => (bool) $validated['verified'],
            'has_notes' => isset($validated['notes']) && trim((string) $validated['notes']) !== '',
        ]);

        $this->employerCompanyService->adminVerifyCompany(
            admin: $admin,
            company: $company,
            verified: (bool) $validated['verified'],
            notes: $validated['notes'] ?? null
        );

        return redirect()->route('admin.employer-companies.show', ['id' => $company->id])
            ->with('success', (bool) $validated['verified'] ? 'Company verification marked as verified.' : 'Company verification rejected.');
    }

    public function downloadDocument(string $id, string $type): StreamedResponse
    {
        $company = EmployerCompany::findOrFail($id);

        $path = match ($type) {
            'ghana_card' => $company->ghana_card_document_url,
            'business_registration' => $company->business_registration_document_url,
            default => null,
        };

        if ($path === null || trim((string) $path) === '') {
            abort(404);
        }

        if (! Storage::disk('private')->exists($path)) {
            abort(404);
        }

        $filenamePrefix = $type === 'ghana_card' ? 'ghana-card' : 'business-registration';
        $filename = "{$filenamePrefix}-{$company->id}";

        Log::info('Employer company verification document download', [
            'admin_user_id' => auth()->id(),
            'company_id' => $company->id,
            'type' => $type,
            'path' => $path,
        ]);

        return Storage::disk('private')->download($path, $filename);
    }
}
