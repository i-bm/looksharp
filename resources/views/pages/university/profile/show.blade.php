@extends('layouts.dashboard.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="mb-1">{{ $institution->name ?? 'University Profile' }}</h2>
                    <p class="text-muted mb-0">Career services portal (MVP onboarding).</p>
                </div>
                <div class="text-end">
                    <div class="fw-semibold">Completion: {{ $wizardProgress['completeness_score'] ?? 0 }}%</div>
                    <a class="btn btn-primary btn-sm mt-2" href="{{ route('university.profile.edit') }}">Edit profile</a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <strong>Institution</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-muted small">Type</div>
                            <div class="fw-semibold">{{ $institution->type?->value ? ucfirst($institution->type->value) : '—' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Location</div>
                            <div class="fw-semibold">{{ $institution->location ?? $institution->city ?? '—' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Website</div>
                            <div class="fw-semibold">
                                @if(!empty($institution->website))
                                <a href="{{ $institution->website }}" target="_blank" rel="noopener">{{ $institution->website }}</a>
                                @else
                                —
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Partnership tier</div>
                            <div class="fw-semibold">{{ $institution->partnership_tier?->value ? ucfirst($institution->partnership_tier->value) : '—' }}</div>
                            @if($institution->partnership_tier && !$institution->is_partner)
                            <div class="small text-muted">Pending verification</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <strong>Career services admin</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-muted small">Name</div>
                            <div class="fw-semibold">{{ $admin->name ?? Auth::user()->full_name }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Role/Title</div>
                            <div class="fw-semibold">{{ $admin->role ?? '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Contact</div>
                            <div class="fw-semibold">
                                {{ $admin->email ?? Auth::user()->email }}
                                @if(!empty($admin->phone) || !empty(Auth::user()->phone_number))
                                <div class="small text-muted">{{ $admin->phone ?? Auth::user()->phone_number }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <strong>Next (UNI features)</strong>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Institution dashboard (UNI-01)</li>
                        <li>Bulk student upload via Excel/API (UNI-02)</li>
                        <li>Exclusive employer events/postings (UNI-03)</li>
                        <li>Branded career fairs (UNI-04)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

