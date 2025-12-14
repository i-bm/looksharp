@extends('layouts.landing.main')

@php
    /** @var \App\Models\EmployerCompany $company */
@endphp

@section('content')
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-2">{{ $company->legal_name }}</h1>
            @if($company->trading_name)
            <p class="text-muted mb-4">Trading as {{ $company->trading_name }}</p>
            @endif

            <div class="card">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Industry</dt>
                        <dd class="col-sm-8">{{ $company->industry ?? '—' }}</dd>

                        <dt class="col-sm-4">Company size</dt>
                        <dd class="col-sm-8">{{ $company->company_size ?? '—' }}</dd>

                        <dt class="col-sm-4">Location</dt>
                        <dd class="col-sm-8">{{ trim(($company->city ?? '').' '.($company->country ?? '')) ?: '—' }}</dd>

                        <dt class="col-sm-4">Website</dt>
                        <dd class="col-sm-8">
                            @if($company->website)
                            <a href="{{ $company->website }}" target="_blank" rel="noopener">{{ $company->website }}</a>
                            @else
                            —
                            @endif
                        </dd>

                        <dt class="col-sm-4">LinkedIn</dt>
                        <dd class="col-sm-8">
                            @if($company->linkedin_url)
                            <a href="{{ $company->linkedin_url }}" target="_blank" rel="noopener">{{ $company->linkedin_url }}</a>
                            @else
                            —
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

