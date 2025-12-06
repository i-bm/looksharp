@extends('layouts.dashboard.main')
@section('content')
<div class="profile-container">
    <!-- Success/Error Messages -->
    <div id="profile-messages" class="profile-messages"></div>
    @if(session('success'))
    <div class="success-message">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="error-message">
        {{ session('error') }}
    </div>
    @endif

    <!-- Profile Content Grid -->
    <div class="row g-4">
        <!-- Left Column (Wider) -->
        <div class="col-lg-8">
            <!-- Profile Header Section -->
            <div class="card profile-main-card mb-4">
                <div class="card-body">
                    @if($isOwner)
                    <div class="profile-section-header-flex-lg mb-3">
                        <div></div>
                        <button type="button" onclick="openModal('about-me-modal')" class="edit-icon-btn-gray">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                    @endif

                    <div class="profile-header-section">
                        <!-- Profile Photo -->
                        <div class="profile-photo-large-container">
                            @if($profile->profile_photo)
                            <img src="{{ asset('storage/'.$profile->profile_photo) }}" alt="{{ $profile->full_name }}"
                                class="profile-photo-large">
                            @else
                            <div class="profile-photo-large-placeholder">
                                {{ strtoupper(substr($profile->first_name, 0, 1) . substr($profile->last_name, 0, 1)) }}
                            </div>
                            @endif
                            @if($isOwner)
                            <button type="button" onclick="openPhotoUpload()" class="photo-edit-overlay-btn">
                                <i class="bi bi-camera"></i>
                            </button>
                            @endif
                        </div>

                        <!-- Profile Info -->
                        <div class="profile-info-section">
                            <h1 class="profile-name-large">
                                {{ $profile->full_name }}
                            </h1>

                            @if($profile->headline)
                            <p class="profile-headline">
                                {{ $profile->headline }}
                            </p>
                            @endif

                            <!-- Status Line -->
                            @php
                            $statusParts = [];
                            $currentWork = $profile->workHistory->where('is_current', true)->first();
                            $currentEducation = $profile->education->where('is_current', true)->first();

                            if ($currentWork) {
                            $statusParts[] = $currentWork->position . ' at ' . $currentWork->company;
                            }

                            if ($currentEducation) {
                            $degreeType = ucfirst(str_replace('_', ' ', $currentEducation->degree_type->value));
                            $institutionName = $currentEducation->institution ? $currentEducation->institution->name :
                            'University';
                            $statusParts[] = $degreeType . ' Student at ' . $institutionName;
                            }

                            $statusText = !empty($statusParts) ? implode(' | ', $statusParts) : ($isPublic ? '' : 'Add
                            your current position or education');
                            @endphp
                            @if(!empty($statusText))
                            <p class="profile-status-line">
                                {{ $statusText }}
                            </p>
                            @endif

                            @if($profile->location)
                            <div class="profile-location-large">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>{{ $profile->location }}</span>
                            </div>
                            @endif

                            @if($profile->public_url)
                            <div class="profile-public-url-large mt-2">
                                <i class="bi bi-link-45deg"></i>
                                <a href="{{ route('talent.profile.public', ['slug' => $profile->public_url]) }}"
                                    target="_blank" class="text-decoration-none">
                                    {{ route('talent.profile.public', ['slug' => $profile->public_url]) }}
                                </a>
                                <button type="button"
                                    onclick="copyPublicUrl('{{ route('talent.profile.public', ['slug' => $profile->public_url]) }}')"
                                    class="btn btn-sm btn-link p-0 ms-2" title="Copy link">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Bio Section -->
                    <div class="profile-bio-section">
                        <p class="profile-bio-text">
                            {{ $profile->bio ?: 'Edit this section to tell employers a little about yourself!' }}
                        </p>
                    </div>

                    <!-- Video Introduction Section -->
                    @if($profile->video_introduction || $isOwner)
                    <div class="profile-video-section">
                        @if($profile->video_introduction)
                        @php
                        $embedUrl = getVideoEmbedUrl($profile->video_introduction);
                        @endphp
                        @if($embedUrl)
                        <div class="profile-video-wrapper">
                            <iframe src="{{ $embedUrl }}" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen class="profile-video-iframe">
                            </iframe>
                        </div>
                        @else
                        <div class="profile-video-placeholder">
                            <i class="bi bi-exclamation-triangle"></i>
                            <p>Invalid video URL. Please update with a valid YouTube or Vimeo link.</p>
                            @if($isOwner)
                            <button type="button" onclick="openModal('video-introduction-modal')"
                                class="btn btn-primary btn-sm">
                                Update Video
                            </button>
                            @endif
                        </div>
                        @endif
                        @else
                        <div class="profile-video-placeholder">
                            <i class="bi bi-camera-video"></i>
                            <p>Add a video introduction to showcase your personality! Paste a YouTube or Vimeo link.</p>
                            @if($isOwner)
                            <button type="button" onclick="openModal('video-introduction-modal')"
                                class="btn btn-primary btn-sm">
                                Add Video
                            </button>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Education Section -->
            <div class="card profile-section-card mb-4">
                <div class="card-body">
                    <div class="profile-section-header">
                        <h2 class="profile-section-title">
                            <i class="bi bi-mortarboard"></i>
                            EDUCATION
                        </h2>
                        @if($isOwner)
                        <div class="profile-section-actions">
                            <button type="button" onclick="openModal('education-modal')" class="add-btn">
                                <i class="bi bi-plus"></i>
                            </button>
                            <button type="button" onclick="openModal('education-modal')" class="edit-icon-btn-dark">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                    @if($profile->education->count() > 0)
                    <div class="profile-section-content">
                        @foreach($profile->education as $education)
                        <div class="profile-item @if($loop->last) profile-item-last @endif">
                            <div class="profile-item-header">
                                <div class="profile-item-main">
                                    @if($education->institution)
                                    <div class="flex items-center gap-10 mb-10">
                                        <div class="profile-item-icon">
                                            <i class="bi bi-mortarboard"></i>
                                        </div>
                                        <h3 class="profile-item-title">
                                            {{ $education->institution->name }}
                                        </h3>
                                    </div>
                                    @endif
                                    <div class="profile-item-meta">
                                        <p class="profile-item-text">
                                            <strong>DEGREE:</strong> {{ ucfirst(str_replace('_', ' ',
                                            $education->degree_type->value)) }}
                                        </p>
                                        @if($education->field_of_study)
                                        <p class="profile-item-text">
                                            <strong>MAJOR:</strong> {{ $education->field_of_study }}
                                        </p>
                                        @endif
                                        <div class="flex gap-15 mt-10 flex-wrap">
                                            <span class="text-muted text-sm flex items-center gap-8">
                                                <i class="bi bi-calendar3"></i>
                                                {{ $education->start_date?->format('F Y') }}
                                                @if($education->is_current)
                                                - Present
                                                @elseif($education->end_date)
                                                - {{ $education->end_date->format('F Y') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="profile-empty-state">
                        <p>No education records yet. Click the + button to add one.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Skills Section -->
            <div class="card profile-section-card mb-4">
                <div class="card-body">
                    <div class="profile-section-header-flex-lg">
                        <h2 class="profile-section-title">
                            <i class="bi bi-tools"></i>
                            SKILLS
                        </h2>
                        @if($isOwner)
                        <button type="button" onclick="openModal('skills-modal')" class="edit-icon-btn-gray">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @endif
                    </div>
                    @if($profile->skills->count() > 0)
                    <div class="profile-tags-container">
                        @foreach($profile->skills as $skill)
                        <div class="profile-tag profile-tag-pink">
                            <span class="profile-tag-name">{{ $skill->skill_name }}</span>
                            @if($skill->proficiency_level)
                            <span class="profile-tag-badge">
                                {{ ucfirst($skill->proficiency_level->value) }}
                            </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="profile-empty-text">No skills added yet. Click edit to add skills.</p>
                    @endif
                </div>
            </div>

            <!-- Work History Section -->
            <div class="card profile-section-card mb-4">
                <div class="card-body">
                    <div class="profile-section-header-flex-lg">
                        <h2 class="profile-section-title">
                            <i class="bi bi-briefcase"></i>
                            WORK HISTORY
                        </h2>
                        @if($isOwner)
                        <div class="d-flex gap-2 align-items-center">
                            <button type="button" onclick="openModal('work-history-modal')" class="add-btn">
                                <i class="bi bi-plus"></i>
                            </button>
                            <button type="button" onclick="openModal('work-history-modal')" class="edit-icon-btn-gray">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                    @if($profile->workHistory->count() > 0)
                    <div class="profile-experience-list">
                        @foreach($profile->workHistory as $work)
                        <div class="profile-experience-item">
                            <div class="profile-experience-content">
                                <h3 class="profile-experience-title">
                                    {{ $work->position }}
                                </h3>
                                <p class="profile-experience-subtitle">
                                    {{ $work->company }}
                                </p>
                                @if($work->location)
                                <p class="profile-experience-location">
                                    <i class="bi bi-geo-alt"></i> {{ $work->location }}
                                </p>
                                @endif
                                <div class="profile-experience-meta">
                                    <span class="profile-experience-date">
                                        <i class="bi bi-calendar3"></i>
                                        {{ $work->start_date?->format('M Y') }}
                                        @if($work->is_current)
                                        - Present
                                        @elseif($work->end_date)
                                        - {{ $work->end_date->format('M Y') }}
                                        @endif
                                    </span>
                                </div>
                                @if($work->description)
                                <p class="profile-experience-description">
                                    {{ $work->description }}
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="profile-empty-state-center">
                        <div class="profile-empty-icon">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <p class="profile-empty-state-text">No work history added yet. Add your past jobs and
                            internships to showcase your experience.</p>
                        @if($isOwner)
                        <button type="button" onclick="openModal('work-history-modal')"
                            class="btn btn-primary profile-empty-state-btn">
                            Add Work History
                        </button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column (Narrower) -->
        <div class="col-lg-4">
            <!-- Profile Strength Card -->
            @if(!$isPublic)
            <div class="card profile-section-card profile-strength-card mb-4">
                <div class="card-body">
                    <h3 class="profile-strength-title">Profile Strength:
                        @php
                        $completenessScore = $profile->profile_completeness_score ?? 0;
                        $strengthLevel = 'Beginner';
                        if ($completenessScore >= 80) {
                        $strengthLevel = 'Expert';
                        } elseif ($completenessScore >= 60) {
                        $strengthLevel = 'Advanced';
                        } elseif ($completenessScore >= 40) {
                        $strengthLevel = 'Intermediate';
                        }
                        @endphp
                        <span class="profile-strength-level">{{ $strengthLevel }}</span>
                    </h3>
                    <div class="profile-strength-progress">
                        <div class="profile-strength-progress-bar">
                            <div class="profile-strength-progress-fill" style="width: {{ $completenessScore }}%"></div>
                        </div>
                        <span class="profile-strength-percentage">{{ $completenessScore }}%</span>
                    </div>
                    <p class="profile-strength-tip">Complete next steps to become a top talent!</p>
                </div>
            </div>
            @endif

            <!-- Resume Section -->
            @if($profile->resume_url || $isOwner)
            <div class="card profile-section-card mb-4">
                <div class="card-body">
                    <div class="profile-section-header-flex-lg">
                        <h3 class="profile-section-title-small">RESUME</h3>
                        @if($isOwner)
                        <button type="button" onclick="openResumeUpload()" class="edit-icon-btn-gray">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @endif
                    </div>
                    @if($profile->resume_url)
                    <div class="profile-resume-item">
                        <div class="profile-resume-icon">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </div>
                        <div class="profile-resume-info">
                            <p class="profile-resume-filename">{{ basename($profile->resume_url) }}</p>
                            <p class="profile-resume-date">
                                Uploaded on {{ $profile->updated_at->format('d M Y') }}
                            </p>
                        </div>
                        <a href="{{ asset('storage/'.$profile->resume_url) }}" target="_blank"
                            class="profile-resume-download">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                    @else
                    <p class="profile-empty-text">No resume uploaded yet. Click edit to upload your resume.</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Verification Section -->
            <div class="card profile-section-card mb-4">
                <div class="card-body">
                    <h3 class="profile-section-title-small">VERIFICATION</h3>
                    <div class="profile-verification-list">
                        <div class="profile-verification-item">
                            <span class="profile-verification-label">Identity Verification</span>
                            @if($profile->verification_status === 'verified')
                            <span class="profile-verification-status verified">
                                <i class="bi bi-check-circle-fill"></i> Verified
                            </span>
                            @else
                            <span class="profile-verification-status pending">
                                <i class="bi bi-clock"></i> Pending
                            </span>
                            @endif
                        </div>
                        <div class="profile-verification-item">
                            <span class="profile-verification-label">NSS Info</span>
                            @if($profile->nss_status === 'completed' || $profile->nss_status === 'active')
                            <span class="profile-verification-status verified">
                                <i class="bi bi-check-circle-fill"></i> Verified
                            </span>
                            @else
                            <span class="profile-verification-status pending">
                                <i class="bi bi-clock"></i> Pending
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Links Section -->
            @if(($profile->github_url || $profile->behance_url || $profile->portfolio_url || $profile->linkedin_url ||
            $profile->twitter_url) || $isOwner)
            <div class="card profile-section-card mb-4">
                <div class="card-body">
                    <div class="profile-section-header-flex-lg">
                        <h3 class="profile-section-title-small">SOCIAL LINKS</h3>
                        @if($isOwner)
                        <button type="button" onclick="openModal('social-links-modal')" class="edit-icon-btn-gray">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @endif
                    </div>
                    @if($profile->github_url || $profile->behance_url || $profile->portfolio_url ||
                    $profile->linkedin_url || $profile->twitter_url)
                    <div class="profile-social-links-list">
                        @if($profile->linkedin_url)
                        <a href="{{ $profile->linkedin_url }}" target="_blank" class="profile-social-link-item">
                            <i class="bi bi-linkedin profile-social-link-icon-linkedin"></i>
                            <span>{{ str_replace(['https://', 'http://', 'www.', 'linkedin.com/in/'], '',
                                $profile->linkedin_url) }}</span>
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                        @endif

                        @if($profile->github_url)
                        <a href="{{ $profile->github_url }}" target="_blank" class="profile-social-link-item">
                            <i class="bi bi-github"></i>
                            <span>{{ str_replace(['https://', 'http://', 'www.', 'github.com/'], '',
                                $profile->github_url) }}</span>
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                        @endif

                        @if($profile->portfolio_url)
                        <a href="{{ $profile->portfolio_url }}" target="_blank" class="profile-social-link-item">
                            <i class="bi bi-briefcase"></i>
                            <span>Portfolio</span>
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                        @endif

                        @if($profile->behance_url)
                        <a href="{{ $profile->behance_url }}" target="_blank" class="profile-social-link-item">
                            <i class="bi bi-palette"></i>
                            <span>Behance</span>
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                        @endif

                        @if($profile->twitter_url)
                        <a href="{{ $profile->twitter_url }}" target="_blank" class="profile-social-link-item">
                            <i class="bi bi-twitter"></i>
                            <span>Twitter/X</span>
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                        @endif
                    </div>
                    @else
                    <p class="profile-empty-text">No social links added yet. Click edit to add your portfolio and social
                        media links.</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Additional Sections Below -->
    <div class="row g-4 mt-2">
        <!-- Languages Section -->
        @if($profile->languages->count() > 0 || $isOwner)
        <div class="col-lg-6">
            <div class="card profile-section-card mb-4">
                <div class="card-body">
                    <div class="profile-section-header-flex-lg">
                        <h2 class="profile-section-title">
                            <i class="bi bi-translate"></i>
                            LANGUAGES
                        </h2>
                        @if($isOwner)
                        <button type="button" onclick="openModal('languages-modal')" class="edit-icon-btn-gray">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @endif
                    </div>
                    @if($profile->languages->count() > 0)
                    <div class="profile-tags-container">
                        @foreach($profile->languages as $language)
                        <div class="profile-tag">
                            <span class="profile-tag-name">{{ $language->language_name }}</span>
                            <span class="profile-tag-badge">
                                {{ ucfirst($language->proficiency_level->value) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="profile-empty-text">No languages added yet. Click edit to add languages.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Certifications Section -->
        @if($profile->certifications->count() > 0 || $isOwner)
        <div class="col-lg-6">
            <div class="card profile-section-card mb-4">
                <div class="card-body">
                    <div class="profile-section-header-flex-lg">
                        <h2 class="profile-section-title">
                            <i class="bi bi-award"></i>
                            CERTIFICATIONS
                        </h2>
                        @if($isOwner)
                        <button type="button" onclick="openModal('certifications-modal')" class="edit-icon-btn-gray">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @endif
                    </div>
                    @if($profile->certifications->count() > 0)
                    <div class="profile-experience-list">
                        @foreach($profile->certifications as $cert)
                        <div class="profile-experience-item">
                            <div class="profile-experience-content">
                                <h3 class="profile-experience-title">
                                    {{ $cert->name }}
                                </h3>
                                <p class="profile-experience-subtitle">
                                    {{ $cert->issuer }}
                                </p>
                                <div class="profile-experience-meta">
                                    <span class="profile-experience-date">
                                        <i class="bi bi-calendar3"></i>
                                        Obtained: {{ $cert->date_obtained->format('M Y') }}
                                    </span>
                                    @if($cert->expiration_date)
                                    <span class="profile-experience-date">
                                        | Expires: {{ $cert->expiration_date->format('M Y') }}
                                    </span>
                                    @endif
                                </div>
                                @if($cert->credential_url)
                                <a href="{{ $cert->credential_url }}" target="_blank" class="profile-credential-link">
                                    View Credential <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="profile-empty-text">No certifications added yet. Click edit to add certifications.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Volunteer Experiences Section -->
        @if($profile->volunteerExperiences->count() > 0 || $isOwner)
        <div class="col-lg-6">
            <div class="card profile-section-card mb-4">
                <div class="card-body">
                    <div class="profile-section-header-flex-lg">
                        <h2 class="profile-section-title">
                            <i class="bi bi-heart"></i>
                            VOLUNTEER EXPERIENCES
                        </h2>
                        @if($isOwner)
                        <button type="button" onclick="openModal('volunteer-experience-modal')"
                            class="edit-icon-btn-gray">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @endif
                    </div>
                    @if($profile->volunteerExperiences->count() > 0)
                    <div class="profile-experience-list">
                        @foreach($profile->volunteerExperiences as $volunteer)
                        <div class="profile-experience-item">
                            <div class="profile-experience-content">
                                <h3 class="profile-experience-title">
                                    {{ $volunteer->organization }}
                                </h3>
                                <div class="profile-experience-meta">
                                    <span class="profile-experience-date">
                                        <i class="bi bi-calendar3"></i>
                                        {{ $volunteer->start_date?->format('M Y') }}
                                        @if($volunteer->is_current)
                                        - Present
                                        @elseif($volunteer->end_date)
                                        - {{ $volunteer->end_date->format('M Y') }}
                                        @endif
                                    </span>
                                </div>
                                @if($volunteer->details)
                                <p class="profile-experience-description">
                                    {{ $volunteer->details }}
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="profile-empty-text">No volunteer experiences added yet. Click edit to add one.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Leadership Experiences Section -->
        @if($profile->leadershipExperiences->count() > 0 || $isOwner)
        <div class="col-lg-6">
            <div class="card profile-section-card mb-4">
                <div class="card-body">
                    <div class="profile-section-header-flex-lg">
                        <h2 class="profile-section-title">
                            <i class="bi bi-people"></i>
                            LEADERSHIP EXPERIENCES
                        </h2>
                        @if($isOwner)
                        <button type="button" onclick="openModal('leadership-experience-modal')"
                            class="edit-icon-btn-gray">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @endif
                    </div>
                    @if($profile->leadershipExperiences->count() > 0)
                    <div class="profile-experience-list">
                        @foreach($profile->leadershipExperiences as $leadership)
                        <div class="profile-experience-item">
                            <div class="profile-experience-content">
                                <h3 class="profile-experience-title">
                                    {{ $leadership->organization }}
                                </h3>
                                @if($leadership->title)
                                <p class="profile-experience-subtitle">
                                    {{ $leadership->title }}
                                </p>
                                @endif
                                <div class="profile-experience-meta">
                                    <span class="profile-experience-date">
                                        <i class="bi bi-calendar3"></i>
                                        {{ $leadership->start_date?->format('M Y') }}
                                        @if($leadership->is_current)
                                        - Present
                                        @elseif($leadership->end_date)
                                        - {{ $leadership->end_date->format('M Y') }}
                                        @endif
                                    </span>
                                </div>
                                @if($leadership->details)
                                <p class="profile-experience-description">
                                    {{ $leadership->details }}
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="profile-empty-text">No leadership experiences added yet. Click edit to add one.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Gigs / Freelance Section -->
        @if($profile->gigsFreelance->count() > 0 || $isOwner)
        <div class="col-lg-6">
            <div class="card profile-section-card mb-4">
                <div class="card-body">
                    <div class="profile-section-header-flex-lg">
                        <h2 class="profile-section-title">
                            <i class="bi bi-briefcase"></i>
                            GIGS / FREELANCE
                        </h2>
                        @if($isOwner)
                        <div class="d-flex gap-2 align-items-center">
                            <button type="button" onclick="openModal('gigs-freelance-modal')" class="add-btn">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                    @if($profile->gigsFreelance->count() > 0)
                    <div class="profile-experience-list">
                        @foreach($profile->gigsFreelance as $gig)
                        <div class="profile-experience-item">
                            <div class="profile-experience-content">
                                <h3 class="profile-experience-title">
                                    {{ $gig->company }}
                                </h3>
                                @if($gig->title)
                                <p class="profile-experience-subtitle">
                                    {{ $gig->title }}
                                </p>
                                @endif
                                <div class="profile-experience-meta">
                                    <span class="profile-experience-date">
                                        <i class="bi bi-calendar3"></i>
                                        {{ $gig->start_date?->format('M Y') }}
                                        @if($gig->is_current)
                                        - Present
                                        @elseif($gig->end_date)
                                        - {{ $gig->end_date->format('M Y') }}
                                        @endif
                                    </span>
                                </div>
                                @if($gig->details)
                                <p class="profile-experience-description">
                                    {{ $gig->details }}
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="profile-empty-text">No gigs/freelance work
                        added yet. Click the + button to add one.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Hobbies Section -->
        @if($profile->hobbies || $isOwner)
        <div class="col-lg-6">
            <div class="card profile-section-card mb-4">
                <div class="card-body">
                    <div class="profile-section-header-flex-lg">
                        <h2 class="profile-section-title">
                            <i class="bi bi-heart"></i>
                            HOBBIES
                        </h2>
                        @if($isOwner)
                        <button type="button" onclick="openModal('hobbies-modal')" class="edit-icon-btn-gray">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @endif
                    </div>
                    <p class="text-text-color profile-hobbies-text">
                        {{ $profile->hobbies ?: 'Add your hobbies to show employers what you enjoy outside of work!' }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Work Preferences Section -->
        @if(($profile->availability || $profile->availability_details || $profile->preferred_location ||
        $profile->salary_expectations) || $isOwner)
        <div class="col-lg-6">
            <div class="card profile-section-card mb-4">
                <div class="card-body">
                    <div class="profile-section-header-flex-lg">
                        <h2 class="profile-section-title">
                            <i class="bi bi-briefcase-fill"></i>
                            WORK PREFERENCES
                        </h2>
                        @if($isOwner)
                        <button type="button" onclick="openModal('work-preferences-modal')" class="edit-icon-btn-gray">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @endif
                    </div>
                    @if($profile->availability || $profile->availability_details || $profile->preferred_location ||
                    $profile->salary_expectations)
                    <div class="profile-preferences-list">
                        @if($profile->availability)
                        <div class="profile-preference-item">
                            <span class="profile-preference-label">Availability</span>
                            <p class="profile-preference-value">
                                {{ ucfirst(str_replace('_', ' ', $profile->availability->value)) }}
                            </p>
                        </div>
                        @endif

                        @if($profile->availability_details)
                        <div class="profile-preference-item">
                            <span class="profile-preference-label">Availability Details</span>
                            <p class="profile-preference-value-text">
                                {{ $profile->availability_details }}
                            </p>
                        </div>
                        @endif

                        @if($profile->preferred_location)
                        <div class="profile-preference-item">
                            <span class="profile-preference-label">Preferred Location</span>
                            <p class="profile-preference-value">
                                {{ ucfirst(str_replace('_', ' ', $profile->preferred_location->value)) }}
                            </p>
                        </div>
                        @endif

                        @if($profile->salary_expectations)
                        <div class="profile-preference-item">
                            <span class="profile-preference-label">Salary Expectations</span>
                            <p class="profile-preference-value">
                                GHS {{ number_format($profile->salary_expectations, 2) }}
                            </p>
                        </div>
                        @endif
                    </div>
                    @else
                    <p class="profile-empty-text">No work preferences set yet. Click edit to add your availability and
                        preferences.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@if($isOwner)
<!-- Include Modals -->
@include('pages.profile.modals.about-me')
@include('pages.profile.modals.video-introduction')
@include('pages.profile.modals.education')
@include('pages.profile.modals.work-history')
@include('pages.profile.modals.skills')
@include('pages.profile.modals.languages')
@include('pages.profile.modals.certifications')
@include('pages.profile.modals.volunteer-experience')
@include('pages.profile.modals.leadership-experience')
@include('pages.profile.modals.gigs-freelance')
@include('pages.profile.modals.fun-fact')
@include('pages.profile.modals.passion')
@include('pages.profile.modals.hobbies')
@include('pages.profile.modals.social-links')
@include('pages.profile.modals.work-preferences')
@include('pages.profile.modals.demographics')
@endif

@push('scripts')
<script src="{{ asset('assets/js/profile-modals.js') }}"></script>
@endpush
@endsection