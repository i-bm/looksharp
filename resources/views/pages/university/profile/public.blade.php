@extends('layouts.landing.main')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-2">{{ $institution->name }}</h1>
            <div class="text-muted mb-4">
                @if($institution->type)
                <span>{{ ucfirst($institution->type->value) }}</span>
                @endif
                @if($institution->location)
                <span> • {{ $institution->location }}</span>
                @endif
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Website</div>
                            <div class="fw-semibold">
                                @if(!empty($institution->website))
                                <a href="{{ $institution->website }}" target="_blank" rel="noopener">{{ $institution->website }}</a>
                                @else
                                —
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Partner status</div>
                            <div class="fw-semibold">
                                {{ $institution->is_partner ? 'Partner' : 'Not verified as partner' }}
                                @if($institution->partnership_tier)
                                <span class="text-muted"> ({{ ucfirst($institution->partnership_tier->value) }})</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <hr>
                    <p class="mb-0 text-muted">University public profile is currently limited in MVP.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

