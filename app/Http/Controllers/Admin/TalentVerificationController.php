<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TalentProfile;
use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;

class TalentVerificationController extends Controller
{
    public function __construct(private ProfileService $profileService) {}

    public function index(Request $request): View
    {
        $user = auth()->user();

        Log::info('Talent verifications admin index accessed', [
            'user_id' => $user?->id,
            'filters' => $request->all(),
        ]);

        $verificationStatus = $request->string('verification_status')->toString();
        $verificationType = $request->string('verification_type')->toString();
        $query = TalentProfile::query()->with(['user', 'verifier']);

        if ($verificationStatus !== '') {
            $query->where('verification_status', $verificationStatus);
        } else {
            $query->where('verification_status', 'pending');
        }

        if ($verificationType !== '') {
            $query->where('verification_type', $verificationType);
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('email', 'like', "%{$search}%");
                    });
            });
        }

        $profiles = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $title = 'Talent Verifications';

        return view('pages.dashboard.admin.talent-verifications.index', compact('profiles', 'title', 'verificationStatus', 'verificationType'));
    }

    public function show(string $id): View
    {
        $profile = TalentProfile::with(['user', 'verifier', 'education.institution'])->findOrFail($id);
        $title = 'Talent Verification Review';

        return view('pages.dashboard.admin.talent-verifications.show', compact('profile', 'title'));
    }

    public function verify(Request $request, string $id): RedirectResponse
    {
        $user = auth()->user();

        Log::info('Talent verification approval requested', [
            'user_id' => $user?->id,
            'profile_id' => $id,
        ]);

        $profile = TalentProfile::findOrFail($id);
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            $this->profileService->adminVerifyTalent(
                $user,
                $profile,
                $validated['notes'] ?? null
            );

            return redirect()->route('admin.talent-verifications.show', ['id' => $profile->id])
                ->with('success', 'Talent verified successfully.');
        } catch (\Exception $e) {
            Log::error('Talent verification approval failed', [
                'user_id' => $user?->id,
                'profile_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function reject(Request $request, string $id): RedirectResponse
    {
        $user = auth()->user();

        Log::info('Talent verification rejection requested', [
            'user_id' => $user?->id,
            'profile_id' => $id,
        ]);

        $profile = TalentProfile::findOrFail($id);
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:5000'],
        ]);

        try {
            $this->profileService->adminRejectVerification(
                $user,
                $profile,
                $validated['reason']
            );

            return redirect()->route('admin.talent-verifications.show', ['id' => $profile->id])
                ->with('success', 'Talent verification rejected.');
        } catch (\Exception $e) {
            Log::error('Talent verification rejection failed', [
                'user_id' => $user?->id,
                'profile_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function document(Request $request, string $id): BinaryFileResponse
    {
        $user = auth()->user();
        $documentType = $request->string('type', 'identity')->toString(); // 'student' or 'identity'

        Log::info('Talent verification document access requested', [
            'user_id' => $user?->id,
            'profile_id' => $id,
            'document_type' => $documentType,
        ]);

        $profile = TalentProfile::findOrFail($id);

        // Determine which document to serve based on type
        $documentPath = null;
        $documentLabel = '';

        if ($documentType === 'student') {
            $documentPath = $profile->student_verification_document_url;
            $documentLabel = 'Student ID';
        } else {
            $documentPath = $profile->verification_document_url;
            $documentLabel = $profile->verification_type === 'ghana_card' ? 'Ghana Card' : ($profile->verification_type === 'passport' ? 'Passport' : 'Identity Document');
        }

        if (! $documentPath) {
            Log::warning('Verification document URL not found in database', [
                'user_id' => $user?->id,
                'profile_id' => $id,
                'document_type' => $documentType,
                'verification_type' => $profile->verification_type,
            ]);
            abort(404, "{$documentLabel} document not found in database");
        }

        // Normalize path (remove leading slashes if present)
        $documentPath = ltrim($documentPath, '/');

        Log::info('Checking verification document file existence', [
            'user_id' => $user?->id,
            'profile_id' => $id,
            'document_path' => $documentPath,
            'document_type' => $documentType,
            'storage_disk' => 'private',
        ]);

        if (! Storage::disk('private')->exists($documentPath)) {
            Log::error('Verification document file not found on disk', [
                'user_id' => $user?->id,
                'profile_id' => $id,
                'document_path' => $documentPath,
                'document_type' => $documentType,
                'storage_root' => Storage::disk('private')->path(''),
            ]);
            abort(404, "{$documentLabel} document file not found on storage disk");
        }

        try {
            $filePath = Storage::disk('private')->path($documentPath);
            $mimeType = Storage::disk('private')->mimeType($documentPath) ?? 'application/octet-stream';

            Log::info('Serving verification document', [
                'user_id' => $user?->id,
                'profile_id' => $id,
                'file_path' => $filePath,
                'mime_type' => $mimeType,
                'document_type' => $documentType,
            ]);

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . strtolower(str_replace(' ', '-', $documentLabel)) . '-document"',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to serve verification document', [
                'user_id' => $user?->id,
                'profile_id' => $id,
                'document_path' => $documentPath,
                'document_type' => $documentType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, "Failed to serve {$documentLabel} document: " . $e->getMessage());
        }
    }
}
