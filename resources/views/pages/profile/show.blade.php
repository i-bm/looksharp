@extends('layouts.dashboard.main')
@section('content')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
    rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/css/profile-v2.css') }}">
@endpush

<div class="profile-v2-container {{ $isPublic ?? false ? 'profile-v2-public' : '' }}">
    <div class="profile-v2-wrapper">
        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="profile-v2-message profile-v2-message-success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="profile-v2-message profile-v2-message-error">
            {{ session('error') }}
        </div>
        @endif

        @php

            // dd($profile);

        @endphp
        <!-- Profile Header Card -->
        <div class="profile-v2-header-card">
            <!-- Cover Image -->
            {{-- <div class="profile-v2-cover" data-alt="Profile cover"></div> --}}

            <div class="profile-v2-header-content">
                <div class="profile-v2-header-inner">
                    <!-- Profile Photo -->
                    <div class="profile-v2-photo-wrapper">
                        @if($profile->profile_photo)
                        <div class="profile-v2-photo" style='background-image: url("{{ asset('storage/'.$profile->profile_photo) }}");'></div>
                        @else
                        <div class="profile-v2-photo">
                            {{ strtoupper(substr($profile->first_name ?? $profile->user->first_name ?? '', 0, 1) . substr($profile->last_name ?? $profile->user->last_name ?? '', 0, 1)) }}
                        </div>
                        @endif
                        @if($profile->verification_status === 'verified')
                        <div class="profile-v2-verified-badge" title="Verified">
                            <span class="material-symbols-outlined">check_circle</span>
                        </div>
                        @endif
                        @if($isOwner)
                        <button type="button" onclick="openPhotoUpload()" class="profile-v2-photo-upload-btn">
                            <span class="material-symbols-outlined">camera</span>
                        </button>
                        @endif
                    </div>

                    <!-- Profile Info -->
                    <div class="profile-v2-info">
                        <div class="profile-v2-info-header">
                            <div>
                                <div class="profile-v2-name-wrapper">
                                    <h1 class="profile-v2-name">
                                        {{ $profile->first_name && $profile->last_name ? $profile->full_name : $profile->user->first_name . ' ' . $profile->user->last_name }}
                                    </h1>
                                </div>

                                @php
                                $currentEducation = $profile->current_education;
                                $statusText = '';
                                if ($currentEducation && $currentEducation->institution) {
                                    $fieldOfStudy = ucfirst(str_replace('_', ' ', $currentEducation->field_of_study ?? ''));
                                    $statusText = $fieldOfStudy . ' at ' . $currentEducation->institution->name;
                                }
                                @endphp
                                @if($statusText)
                                <p class="profile-v2-headline">{{ $statusText }}</p>
                                @endif

                                <div class="profile-v2-meta">
                                    @if($profile->location)
                                    <span class="profile-v2-meta-item">
                                        <span class="material-symbols-outlined">location_on</span>
                                        {{ $profile->location }}
                                    </span>
                                    @endif
                                    @if($isOwner && $profile->public_url)
                                    <a class="text-primary"
                                        style="color: var(--primary-color1); text-decoration: none; font-weight: 500;"
                                        href="{{ route('talent.profile.public', ['slug' => $profile->public_url]) }}"
                                        target="_blank">View Public Profile</a>
                                    @endif
                                    @if($profile->bio)
                                    <span class="profile-v2-meta-item profile-v2-bio-text">
                                        @php
                                            $bioLength = strlen($profile->bio);
                                            $isLong = $bioLength > 150;
                                            $truncatedBio = $isLong ? substr($profile->bio, 0, 150) : $profile->bio;
                                        @endphp
                                        <span class="profile-v2-bio-content" data-full-bio="{{ json_encode($profile->bio) }}">
                                            {{ $truncatedBio }}
                                        </span>
                                        @if($isLong)
                                        <a href="#" class="profile-v2-bio-toggle" onclick="toggleBioText(event, this); return false;">Show more</a>
                                        @endif
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="profile-v2-actions">
                                @if(!$isOwner)
                                <button class="profile-v2-btn profile-v2-btn-primary">
                                    <span class="material-symbols-outlined">person_add</span>
                                    Connect
                                </button>
                                @endif

                                @if($isOwner)
                                    @if($profile->resume_url)
                                    <a href="{{ asset('storage/'.$profile->resume_url) }}" target="_blank"
                                        class="profile-v2-btn profile-v2-btn-secondary">
                                        <span class="material-symbols-outlined">download</span>
                                        Download Resume
                                    </a>
                                    @endif
                                    <button type="button" onclick="openResumeUpload()"
                                        class="profile-v2-btn profile-v2-btn-secondary">
                                        <span class="material-symbols-outlined">upload</span>
                                        {{ $profile->resume_url ? 'Update Resume' : 'Upload Resume' }}
                                    </button>
                                    <button type="button" onclick="openModal('about-me-modal')"
                                        class="profile-v2-btn profile-v2-btn-secondary profile-v2-btn-icon">
                                        <span class="material-symbols-outlined">edit</span>
                                    </button>
                                @else
                                    @if($profile->resume_url)
                                    <a href="{{ asset('storage/'.$profile->resume_url) }}" target="_blank"
                                        class="profile-v2-btn profile-v2-btn-secondary">
                                        <span class="material-symbols-outlined">download</span>
                                        Resume
                                    </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-v2-main-layout">
            <!-- Main Content -->
            <div class="profile-v2-main-content">
                <!-- About Section -->


                <!-- Projects and Portfolio Section -->
                <section class="profile-v2-section">
                    <div class="profile-v2-section-header">
                        <h3 class="profile-v2-section-title">
                            <span class="material-symbols-outlined">rocket_launch</span>
                            Projects and Portfolio
                        </h3>
                        @if($isOwner)
                        <div class="profile-v2-section-actions">
                            <button type="button" onclick="openModal('projects-modal')" class="profile-v2-edit-btn">
                                <span class="material-symbols-outlined">add</span>
                            </button>
                            <button type="button" onclick="openModal('projects-modal')" class="profile-v2-edit-btn">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                        </div>
                        @endif
                    </div>
                    @if($profile->projects->count() > 0)
                    <div class="profile-v2-projects-grid">
                        @foreach($profile->projects->take(3) as $project)
                        <div class="profile-v2-project-card">
                            <div class="profile-v2-project-image"
                                style='background-image: url("{{ $project->image_url ?: '
                                https://via.placeholder.com/400x200?text='.urlencode($project->title) }}");'>
                                <div class="profile-v2-project-overlay">
                                    @if($project->project_url)
                                    <a href="{{ $project->project_url }}" target="_blank"
                                        class="profile-v2-btn profile-v2-btn-secondary">
                                        <span class="material-symbols-outlined">visibility</span> View
                                    </a>
                                    @endif
                                </div>
                            </div>
                            <div class="profile-v2-project-body">
                                <div class="profile-v2-project-header">
                                    <h4 class="profile-v2-project-title">{{ $project->title }}</h4>
                                    @if($project->project_type)
                                    <span class="profile-v2-project-type">{{ $project->project_type }}</span>
                                    @endif
                                </div>
                                @if($project->description)
                                <p class="profile-v2-project-description">{{ $project->description }}</p>
                                @endif
                                @if($project->technologies && count($project->technologies) > 0)
                                <div class="profile-v2-project-tech">
                                    @foreach($project->technologies as $tech)
                                    <span class="profile-v2-tech-tag">{{ $tech }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($profile->projects->count() > 3)
                    <div
                        style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--borders-color); text-align: center;">
                        <button
                            style="color: var(--primary-color1); font-size: 14px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: none; border: none; cursor: pointer;">
                            View all projects <span class="material-symbols-outlined"
                                style="font-size: 16px;">arrow_forward</span>
                        </button>
                    </div>
                    @endif
                    @else
                    <div class="profile-v2-empty">
                        <span class="material-symbols-outlined">rocket_launch</span>
                        <p>No projects added yet. @if($isOwner)Click add to showcase your work!@endif</p>
                    </div>
                    @endif
                </section>

                <!-- Education Section -->
                <section class="profile-v2-section">
                    <div class="profile-v2-section-header">
                        <h3 class="profile-v2-section-title">Education</h3>
                        @if($isOwner)
                        <div class="profile-v2-section-actions">
                            <button type="button" onclick="openModal('education-modal')" class="profile-v2-edit-btn">
                                <span class="material-symbols-outlined">add</span>
                            </button>
                            <button type="button" onclick="openModal('education-modal')" class="profile-v2-edit-btn">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                        </div>
                        @endif
                    </div>
                    @if($profile->education->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        @foreach($profile->education as $education)
                        <div class="profile-v2-item">
                            <div class="profile-v2-item-icon">
                                <span class="material-symbols-outlined">school</span>
                            </div>
                            <div class="profile-v2-item-content">
                                <h4 class="profile-v2-item-title">
                                    {{ $education->institution ? $education->institution->name : 'Institution' }}
                                </h4>
                                <p class="profile-v2-item-subtitle">
                                    {{ ucfirst(str_replace('_', ' ', $education->degree_type->value ?? '')) }}
                                    @if($education->field_of_study)
                                    in {{ $education->field_of_study }}
                                    @endif
                                </p>
                                <p class="profile-v2-item-meta">
                                    {{ $education->start_date?->format('Y') }}
                                    @if($education->is_current)
                                    - Present
                                    @elseif($education->end_date)
                                    - {{ $education->end_date->format('Y') }}
                                    @endif
                                    @if($education->gpa)
                                    | GPA: {{ number_format($education->gpa, 2) }}/4.0
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="profile-v2-empty">
                        <span class="material-symbols-outlined">school</span>
                        <p>No education records yet. @if($isOwner)Click add to add your education!@endif</p>
                    </div>
                    @endif
                </section>

                <!-- Experience Section -->
                <section class="profile-v2-section">
                    <div class="profile-v2-section-header">
                        <h3 class="profile-v2-section-title">Experience</h3>
                        @if($isOwner)
                        <div class="profile-v2-section-actions">
                            <button type="button" onclick="openModal('work-history-modal')" class="profile-v2-edit-btn">
                                <span class="material-symbols-outlined">add</span>
                            </button>
                            <button type="button" onclick="openModal('work-history-modal')" class="profile-v2-edit-btn">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                        </div>
                        @endif
                    </div>
                    @if($profile->workHistory->count() > 0)
                    <div class="profile-v2-timeline">
                        @foreach($profile->workHistory as $index => $work)
                        <div class="profile-v2-timeline-icon">
                            <div
                                class="profile-v2-timeline-dot {{ $index === 0 ? 'profile-v2-timeline-dot-primary' : 'profile-v2-timeline-dot-secondary' }}">
                                <span class="material-symbols-outlined">work</span>
                            </div>
                            @if(!$loop->last)
                            <div class="profile-v2-timeline-line"></div>
                            @endif
                        </div>
                        <div class="profile-v2-timeline-content {{ !$loop->last ? '' : '' }}">
                            <h4 class="profile-v2-item-title">{{ $work->position }}</h4>
                            <p class="profile-v2-item-subtitle">{{ $work->company }}</p>
                            <p class="profile-v2-item-meta" style="font-size: 12px; margin-bottom: 8px;">
                                {{ $work->start_date?->format('M Y') }}
                                @if($work->is_current)
                                - Present
                                @elseif($work->end_date)
                                - {{ $work->end_date->format('M Y') }}
                                @endif
                                @if($work->location)
                                · {{ $work->location }}
                                @endif
                            </p>
                            @if($work->description)
                            <p class="profile-v2-item-description">{{ $work->description }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="profile-v2-empty">
                        <span class="material-symbols-outlined">work</span>
                        <p>No work experience added yet. @if($isOwner)Click add to add your experience!@endif</p>
                    </div>
                    @endif
                </section>

                <!-- Volunteer & Leadership Section -->
                <section class="profile-v2-section">
                    <div class="profile-v2-section-header">
                        <h3 class="profile-v2-section-title">Volunteer & Leadership</h3>
                        @if($isOwner)
                        <button type="button" onclick="openModal('volunteer-experience-modal')"
                            class="profile-v2-edit-btn">
                            <span class="material-symbols-outlined">add</span>
                        </button>
                        @endif
                    </div>
                    @php
                    $volunteerAndLeadership = collect();
                    foreach($profile->volunteerExperiences as $vol) {
                    $volunteerAndLeadership->push(['type' => 'volunteer', 'data' => $vol]);
                    }
                    foreach($profile->leadershipExperiences as $lead) {
                    $volunteerAndLeadership->push(['type' => 'leadership', 'data' => $lead]);
                    }
                    $volunteerAndLeadership = $volunteerAndLeadership->sortByDesc(function($item) {
                    return $item['data']->start_date;
                    });
                    @endphp
                    @if($volunteerAndLeadership->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($volunteerAndLeadership as $item)
                        <div class="profile-v2-item"
                            style="{{ !$loop->last ? 'border-bottom: 1px solid var(--borders-color); padding-bottom: 16px;' : '' }}">
                            <div class="profile-v2-item-icon" style="width: 48px; height: 48px;">
                                <span class="material-symbols-outlined">{{ $item['type'] === 'volunteer' ?
                                    'volunteer_activism' : 'groups' }}</span>
                            </div>
                            <div class="profile-v2-item-content">
                                <h4 class="profile-v2-item-title">
                                    {{ $item['type'] === 'leadership' && $item['data']->title ? $item['data']->title :
                                    ($item['type'] === 'volunteer' ? 'Volunteer' : 'Leader') }}
                                </h4>
                                <p class="profile-v2-item-subtitle">{{ $item['data']->organization }}</p>
                                <p class="profile-v2-item-meta" style="font-size: 12px; margin-top: 4px;">
                                    {{ $item['data']->start_date?->format('M Y') }}
                                    @if($item['data']->is_current)
                                    - Present
                                    @elseif($item['data']->end_date)
                                    - {{ $item['data']->end_date->format('M Y') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="profile-v2-empty">
                        <span class="material-symbols-outlined">volunteer_activism</span>
                        <p>No volunteer or leadership experience added yet. @if($isOwner)Click add to add your
                            experience!@endif</p>
                    </div>
                    @endif
                </section>

                <!-- Certifications Section -->
                <section class="profile-v2-section">
                    <div class="profile-v2-section-header">
                        <h3 class="profile-v2-section-title">Licenses & Certifications</h3>
                        @if($isOwner)
                        <button type="button" onclick="openModal('certifications-modal')" class="profile-v2-edit-btn">
                            <span class="material-symbols-outlined">add</span>
                        </button>
                        @endif
                    </div>
                    @if($profile->certifications->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($profile->certifications as $cert)
                        <div class="profile-v2-item">
                            <div class="profile-v2-item-icon" style="width: 48px; height: 48px;">
                                <span class="material-symbols-outlined">workspace_premium</span>
                            </div>
                            <div class="profile-v2-item-content">
                                <h4 class="profile-v2-item-title">{{ $cert->name }}</h4>
                                <p class="profile-v2-item-subtitle">{{ $cert->issuer }}</p>
                                <p class="profile-v2-item-meta" style="font-size: 12px; margin-top: 4px;">
                                    Issued {{ $cert->date_obtained?->format('M Y') }}
                                    @if($cert->credential_url)
                                    · <a href="{{ $cert->credential_url }}" target="_blank"
                                        style="color: var(--primary-color1); text-decoration: none;">Show credential</a>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="profile-v2-empty">
                        <span class="material-symbols-outlined">workspace_premium</span>
                        <p>No certifications added yet. @if($isOwner)Click add to add your certifications!@endif</p>
                    </div>
                    @endif
                </section>

                <!-- Gigs/Freelance Section -->
                <section class="profile-v2-section">
                    <div class="profile-v2-section-header">
                        <h3 class="profile-v2-section-title">Gigs / Freelance</h3>
                        @if($isOwner)
                        <button type="button" onclick="openModal('gigs-freelance-modal')" class="profile-v2-edit-btn">
                            <span class="material-symbols-outlined">add</span>
                        </button>
                        @endif
                    </div>
                    @if($profile->gigsFreelance->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($profile->gigsFreelance as $gig)
                        <div class="profile-v2-item">
                            <div
                                style="width: 64px; height: 64px; border-radius: 8px; background: #e5e7eb; background-size: cover; background-position: center; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                                <span class="material-symbols-outlined" style="color: #9ca3af;">work</span>
                            </div>
                            <div class="profile-v2-item-content">
                                <h4 class="profile-v2-item-title">{{ $gig->title ?: $gig->company }}</h4>
                                <p class="profile-v2-item-description" style="margin-top: 4px;">{{ $gig->details }}</p>
                                @if($gig->start_date)
                                <p class="profile-v2-item-meta" style="font-size: 12px; margin-top: 4px;">
                                    {{ $gig->start_date->format('M Y') }}
                                    @if($gig->is_current)
                                    - Present
                                    @elseif($gig->end_date)
                                    - {{ $gig->end_date->format('M Y') }}
                                    @endif
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="profile-v2-empty">
                        <span class="material-symbols-outlined">work</span>
                        <p>No gigs or freelance work added yet. @if($isOwner)Click add to add your work!@endif</p>
                    </div>
                    @endif
                </section>
            </div>

            <!-- Sidebar -->
            <div class="profile-v2-sidebar">
                @if(!($isPublic ?? false))
                <!-- Verification -->
                <section class="profile-v2-section">
                    <div class="profile-v2-section-header">
                        <h3 class="profile-v2-section-title profile-v2-section-title-small">
                            <span class="material-symbols-outlined">verified_user</span>
                            Verification
                        </h3>
                    </div>

                    @php
                        $isVerified = $profile->verification_status === 'verified';
                        $hasVerificationDocument = !empty($profile->verification_document_url);
                        $verificationStatus = $profile->verification_status ?? 'not_started';
                    @endphp

                    <div class="profile-v2-verification-body">
                        @if($isVerified)
                            <span class="profile-v2-pill profile-v2-pill-verified">
                                <span class="material-symbols-outlined">check_circle</span>
                                Verified
                            </span>
                            <p class="profile-v2-verification-help">Employers can see your verified badge on your profile.</p>
                            @if($profile->verification_verified_at)
                                <p class="profile-v2-verification-help" style="font-size: 12px; margin-top: 8px; color: var(--text-color);">
                                    Verified on {{ $profile->verification_verified_at->format('M d, Y') }}
                                </p>
                            @endif
                        @elseif($verificationStatus === 'rejected')
                            <span class="profile-v2-pill profile-v2-pill-rejected" style="background: #fee2e2; color: #991b1b;">
                                <span class="material-symbols-outlined">cancel</span>
                                Rejected
                            </span>
                            <p class="profile-v2-verification-help">Your verification was rejected. Please submit a new document.</p>
                            @if($isOwner)
                                <a href="{{ route('talent.profile.verification.show') }}" class="profile-v2-btn profile-v2-btn-primary profile-v2-verification-cta">
                                    <span class="material-symbols-outlined">upload</span>
                                    Resubmit verification
                                </a>
                            @endif
                        @elseif($hasVerificationDocument)
                            <span class="profile-v2-pill profile-v2-pill-pending">
                                <span class="material-symbols-outlined">hourglass_top</span>
                                Pending review
                            </span>
                            <p class="profile-v2-verification-help">We've received your document and we're reviewing it.</p>
                        @else
                            <span class="profile-v2-pill profile-v2-pill-neutral">
                                <span class="material-symbols-outlined">shield</span>
                                Not started
                            </span>
                            <p class="profile-v2-verification-help">Get verified to build trust and stand out to employers. Verification is required to apply for jobs.</p>
                        @endif

                        @if($isOwner && !$isVerified && $verificationStatus !== 'rejected')
                            <a href="{{ route('talent.profile.verification.show') }}" class="profile-v2-btn profile-v2-btn-primary profile-v2-verification-cta">
                                <span class="material-symbols-outlined">upload</span>
                                {{ $hasVerificationDocument ? 'Update verification' : 'Get verified' }}
                            </a>
                        @endif
                    </div>
                </section>
                @endif

                <!-- Work Preferences -->
                <section class="profile-v2-section">
                    <div class="profile-v2-section-header">
                        <h3 class="profile-v2-section-title profile-v2-section-title-small">Work Preference</h3>
                        @if($isOwner)
                        <button type="button" onclick="openModal('work-preferences-modal')" class="profile-v2-edit-btn">
                            <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                        </button>
                        @endif
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @if($profile->workModels && $profile->workModels->count() > 0)
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <span class="material-symbols-outlined"
                                style="color: var(--text-color); margin-top: 2px;">apartment</span>
                            <div>
                                <p style="font-size: 14px; font-weight: 700; color: var(--title-color); margin: 0;">
                                    Work Model</p>
                                <div class="profile-v2-tags" style="margin-top: 4px;">
                                    @foreach($profile->workModels as $workModel)
                                    <span class="profile-v2-tag">{{ $workModel->display_name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($profile->preferredCities && $profile->preferredCities->count() > 0)
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <span class="material-symbols-outlined"
                                style="color: var(--text-color); margin-top: 2px;">location_on</span>
                            <div>
                                <p style="font-size: 14px; font-weight: 700; color: var(--title-color); margin: 0;">
                                    Preferred Cities</p>
                                <div class="profile-v2-tags" style="margin-top: 4px;">
                                    @foreach($profile->preferredCities as $city)
                                    <span class="profile-v2-tag">{{ $city->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($profile->careerInterestAreas && $profile->careerInterestAreas->count() > 0)
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <span class="material-symbols-outlined"
                                style="color: var(--text-color); margin-top: 2px;">work_outline</span>
                            <div>
                                <p style="font-size: 14px; font-weight: 700; color: var(--title-color); margin: 0;">
                                    Career Interest Areas</p>
                                <div class="profile-v2-tags" style="margin-top: 4px;">
                                    @foreach($profile->careerInterestAreas as $area)
                                    <span class="profile-v2-tag">{{ $area->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($profile->job_categories && count($profile->job_categories) > 0)
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <span class="material-symbols-outlined"
                                style="color: var(--text-color); margin-top: 2px;">work_outline</span>
                            <div>
                                <p style="font-size: 14px; font-weight: 700; color: var(--title-color); margin: 0;">
                                    Roles</p>
                                <p style="font-size: 12px; color: var(--text-color); margin: 0;">{{ implode(', ',
                                    $profile->job_categories) }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </section>

                <!-- Skills Section -->
                <section class="profile-v2-section">
                    <div class="profile-v2-section-header">
                        <h3 class="profile-v2-section-title profile-v2-section-title-small">Skills</h3>
                        @if($isOwner)
                        <div class="profile-v2-section-actions">
                            <button type="button" onclick="openModal('skills-modal')" class="profile-v2-edit-btn">
                                <span class="material-symbols-outlined" style="font-size: 20px;">add</span>
                            </button>
                            <button type="button" onclick="openModal('skills-modal')" class="profile-v2-edit-btn">
                                <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                            </button>
                        </div>
                        @endif
                    </div>
                    @if($profile->skills->count() > 0)
                    <div class="profile-v2-tags">
                        @foreach($profile->skills as $skill)
                        <span class="profile-v2-tag">{{ $skill->skill_name }}</span>
                        @endforeach
                    </div>
                    @else
                    <p style="font-size: 14px; color: var(--text-color);">No skills added yet. @if($isOwner)Click add to
                        add your skills!@endif</p>
                    @endif
                </section>

                <!-- Languages Section -->
                <section class="profile-v2-section">
                    <div class="profile-v2-section-header">
                        <h3 class="profile-v2-section-title profile-v2-section-title-small">Languages</h3>
                        @if($isOwner)
                        <button type="button" onclick="openModal('languages-modal')" class="profile-v2-edit-btn">
                            <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                        </button>
                        @endif
                    </div>
                    @if($profile->languages->count() > 0)
                    <div class="profile-v2-languages">
                        @foreach($profile->languages as $language)
                        <div class="profile-v2-language-item">
                            <span class="profile-v2-language-name">{{ $language->language_name }}</span>
                            <span class="profile-v2-language-level">{{ ucfirst($language->proficiency_level->value ??
                                '') }}</span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p style="font-size: 14px; color: var(--text-color);">No languages added yet. @if($isOwner)Click
                        edit to add languages!@endif</p>
                    @endif
                </section>

                <!-- Hobbies Section -->
                <section class="profile-v2-section">
                    <div class="profile-v2-section-header">
                        <h3 class="profile-v2-section-title profile-v2-section-title-small">Hobbies</h3>
                        @if($isOwner)
                        <button type="button" onclick="openModal('hobbies-modal')" class="profile-v2-edit-btn">
                            <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                        </button>
                        @endif
                    </div>
                    @if($profile->hobbies)
                    @php
                    $hobbies = is_string($profile->hobbies) ? explode(',', $profile->hobbies) :
                    (is_array($profile->hobbies) ? $profile->hobbies : []);
                    @endphp
                    @if(count($hobbies) > 0)
                    <div class="profile-v2-tags">
                        @foreach($hobbies as $hobby)
                        <span class="profile-v2-tag profile-v2-tag-outline">{{ trim($hobby) }}</span>
                        @endforeach
                    </div>
                    @else
                    <p style="font-size: 14px; color: var(--text-color);">{{ $profile->hobbies }}</p>
                    @endif
                    @else
                    <p style="font-size: 14px; color: var(--text-color);">No hobbies added yet. @if($isOwner)Click edit
                        to add your hobbies!@endif</p>
                    @endif
                </section>

                <!-- Social Links Section -->
                <section class="profile-v2-section">
                    <div class="profile-v2-section-header">
                        <h3 class="profile-v2-section-title profile-v2-section-title-small">Social Links</h3>
                        @if($isOwner)
                        <button type="button" onclick="openModal('social-links-modal')" class="profile-v2-edit-btn">
                            <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                        </button>
                        @endif
                    </div>
                    <div class="profile-v2-social-links">
                        @if($profile->linkedin_url)
                        <a href="{{ $profile->linkedin_url }}" target="_blank" class="profile-v2-social-link">
                            <span class="profile-v2-social-link-icon" style="color: #0a66c2;">
                                <svg fill="currentColor" height="24" viewBox="0 0 24 24" width="24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z">
                                    </path>
                                </svg>
                            </span>
                            <span class="profile-v2-social-link-text">{{ str_replace(['https://', 'http://', 'www.',
                                'linkedin.com/in/'], '', $profile->linkedin_url) }}</span>
                            <span class="profile-v2-social-link-arrow material-symbols-outlined">open_in_new</span>
                        </a>
                        @endif
                        @if($profile->github_url)
                        <a href="{{ $profile->github_url }}" target="_blank" class="profile-v2-social-link">
                            <span class="profile-v2-social-link-icon" style="color: var(--title-color);">
                                <svg fill="currentColor" height="24" viewBox="0 0 24 24" width="24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z">
                                    </path>
                                </svg>
                            </span>
                            <span class="profile-v2-social-link-text">{{ str_replace(['https://', 'http://', 'www.',
                                'github.com/'], '', $profile->github_url) }}</span>
                            <span class="profile-v2-social-link-arrow material-symbols-outlined">open_in_new</span>
                        </a>
                        @endif
                        @if($profile->portfolio_url)
                        <a href="{{ $profile->portfolio_url }}" target="_blank" class="profile-v2-social-link">
                            <span class="profile-v2-social-link-icon material-symbols-outlined"
                                style="font-size: 24px; color: var(--primary-color2);">language</span>
                            <span class="profile-v2-social-link-text">{{ str_replace(['https://', 'http://', 'www.'],
                                '', $profile->portfolio_url) }}</span>
                            <span class="profile-v2-social-link-arrow material-symbols-outlined">open_in_new</span>
                        </a>
                        @endif
                        @if(!$profile->linkedin_url && !$profile->github_url && !$profile->portfolio_url)
                        <p style="font-size: 14px; color: var(--text-color);">No social links added yet.
                            @if($isOwner)Click edit to add your links!@endif</p>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

@if($isOwner)
<!-- Include Modals -->
@if($showWelcomeModal ?? false)
@include('pages.profile.modals.welcome')
@endif
@include('pages.profile.modals.about-me')
@include('pages.profile.modals.video-introduction')
@include('pages.profile.modals.projects')
@include('pages.profile.modals.education')
@include('pages.profile.modals.work-history')
@include('pages.profile.modals.skills')
@include('pages.profile.modals.languages')
@include('pages.profile.modals.certifications')
@include('pages.profile.modals.volunteer-experience')
@include('pages.profile.modals.leadership-experience')
@include('pages.profile.modals.gigs-freelance')
@include('pages.profile.modals.hobbies')
@include('pages.profile.modals.social-links')
@include('pages.profile.modals.work-preferences')
@endif

@push('scripts')
<script src="{{ asset('assets/js/profile-modals.js') }}"></script>
<script>
    function toggleBioText(event, linkElement) {
        event.preventDefault();
        const bioContainer = linkElement.closest('.profile-v2-bio-text');
        const bioContent = bioContainer.querySelector('.profile-v2-bio-content');
        const fullBioJson = bioContent.getAttribute('data-full-bio');

        // Parse JSON to get the original text (handles special characters properly)
        const fullBio = JSON.parse(fullBioJson);

        if (linkElement.textContent === 'Show more') {
            bioContent.textContent = fullBio;
            linkElement.textContent = 'Show less';
        } else {
            bioContent.textContent = fullBio.substring(0, 150);
            linkElement.textContent = 'Show more';
        }
    }

    // Auto-open welcome modal on page load if needed
    @if($isOwner && ($showWelcomeModal ?? false))
    document.addEventListener('DOMContentLoaded', function() {
        // Small delay to ensure page is fully loaded
        setTimeout(function() {
            openModal('welcome-modal');
        }, 500);
    });
    @endif
</script>
@endpush
@endsection
