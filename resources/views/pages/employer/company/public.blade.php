@extends('layouts.landing.main')

@php
    /** @var \App\Models\EmployerCompany $company */
    $company->load(['photos', 'testimonials']);
@endphp

@section('content')
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Company Header -->
            <div class="row mb-4">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    @if($company->logo_url)
                    <img src="{{ asset('storage/'.$company->logo_url) }}" alt="{{ $company->legal_name }}" class="img-fluid" style="max-width: 200px; border-radius: 8px;">
                    @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; border-radius: 8px; margin: 0 auto;">
                        <i class="bi bi-building" style="font-size: 4rem; color: #ccc;"></i>
                    </div>
                    @endif
                </div>
                <div class="col-md-9">
                    <h1 class="mb-2">{{ $company->legal_name }}</h1>
                    @if($company->trading_name)
                    <p class="text-muted mb-2">Trading as {{ $company->trading_name }}</p>
                    @endif
                    @if($company->year_established)
                    <p class="text-muted small mb-2">Established {{ $company->year_established }}</p>
                    @endif
                    @if($company->company_description)
                    <p class="mt-3">{{ $company->company_description }}</p>
                    @endif
                </div>
            </div>

            <!-- Company Video -->
            @if($company->video_url)
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">About Us</h5>
                    <video controls class="w-100" style="border-radius: 8px;" src="{{ asset('storage/'.$company->video_url) }}"></video>
                </div>
            </div>
            @endif

            <!-- Company Photos -->
            @if($company->photos->count() > 0)
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Company Photos</h5>
                    <div class="row g-3">
                        @foreach($company->photos as $photo)
                        <div class="col-md-3">
                            <img src="{{ asset('storage/'.$photo->photo_url) }}" class="img-fluid rounded" alt="Company Photo" style="height: 200px; width: 100%; object-fit: cover;">
                            @if($photo->caption)
                            <p class="small text-muted mt-2 mb-0">{{ $photo->caption }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Employee Testimonials -->
            @if($company->testimonials->count() > 0)
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">What Our Team Says</h5>
                    <div class="row g-3">
                        @foreach($company->testimonials as $testimonial)
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        @if($testimonial->photo_url)
                                        <img src="{{ asset('storage/'.$testimonial->photo_url) }}" alt="{{ $testimonial->employee_name }}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <i class="bi bi-person" style="font-size: 1.5rem; color: #ccc;"></i>
                                        </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $testimonial->employee_name }}</h6>
                                            @if($testimonial->employee_title)
                                            <p class="text-muted small mb-0">{{ $testimonial->employee_title }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="mb-0">"{{ $testimonial->testimonial }}"</p>
                                    @if($testimonial->is_featured)
                                    <span class="badge bg-primary mt-2">Featured</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Company Details -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Company Details</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Industry</dt>
                        <dd class="col-sm-8">{{ $company->industry ?? '—' }}</dd>

                        <dt class="col-sm-4">Company size</dt>
                        <dd class="col-sm-8">{{ $company->company_size ?? '—' }}</dd>

                        <dt class="col-sm-4">Location</dt>
                        <dd class="col-sm-8">{{ trim(($company->city ?? '').' '.($company->country ?? '')) ?: '—' }}</dd>

                        @if($company->address)
                        <dt class="col-sm-4">Address</dt>
                        <dd class="col-sm-8">{{ $company->address }}</dd>
                        @endif

                        <dt class="col-sm-4">Website</dt>
                        <dd class="col-sm-8">
                            @if($company->website)
                            <a href="{{ $company->website }}" target="_blank" rel="noopener">{{ $company->website }}</a>
                            @else
                            —
                            @endif
                        </dd>

                        @if($company->linkedin_url)
                        <dt class="col-sm-4">LinkedIn</dt>
                        <dd class="col-sm-8">
                            <a href="{{ $company->linkedin_url }}" target="_blank" rel="noopener">{{ $company->linkedin_url }}</a>
                        </dd>
                        @endif

                        @if($company->facebook_url)
                        <dt class="col-sm-4">Facebook</dt>
                        <dd class="col-sm-8">
                            <a href="{{ $company->facebook_url }}" target="_blank" rel="noopener">{{ $company->facebook_url }}</a>
                        </dd>
                        @endif

                        @if($company->twitter_url)
                        <dt class="col-sm-4">Twitter</dt>
                        <dd class="col-sm-8">
                            <a href="{{ $company->twitter_url }}" target="_blank" rel="noopener">{{ $company->twitter_url }}</a>
                        </dd>
                        @endif

                        @if($company->instagram_url)
                        <dt class="col-sm-4">Instagram</dt>
                        <dd class="col-sm-8">
                            <a href="{{ $company->instagram_url }}" target="_blank" rel="noopener">{{ $company->instagram_url }}</a>
                        </dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

