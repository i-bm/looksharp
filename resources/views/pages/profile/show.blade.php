@extends('layouts.dashboard.main')
@section('content')
<div class="profile-container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <!-- Profile Header -->
    <div class="profile-header"
        style="background: #fff; border-radius: 12px; padding: 40px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 30px; flex-wrap: wrap;">
            <!-- Profile Photo -->
            <div class="profile-photo-container" style="position: relative;">
                @if($profile->profile_photo)
                <img src="{{ asset('storage/'.$profile->profile_photo) }}" alt="{{ $profile->full_name }}"
                    style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #f0f0f0;">
                @else
                <div
                    style="width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, #F53003 0%, #ff6b35 100%); display: flex; align-items: center; justify-content: center; font-size: 48px; color: white; font-weight: bold; border: 4px solid #f0f0f0;">
                    {{ strtoupper(substr($profile->first_name, 0, 1) . substr($profile->last_name, 0, 1)) }}
                </div>
                @endif
            </div>

            <!-- Profile Info -->
            <div class="profile-info" style="flex: 1; min-width: 250px;">
                <h1
                    style="font-family: var(--font-bricolageGrotesque); font-size: 32px; font-weight: 600; color: var(--title-color); margin: 0 0 10px 0;">
                    {{ $profile->full_name }}
                </h1>

                @if($profile->location)
                <div
                    style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px; color: var(--text-color);">
                    <i class="bi bi-geo-alt-fill" style="color: #F53003;"></i>
                    <span>{{ $profile->location }}</span>
                </div>
                @endif

                @if($profile->bio)
                <p style="color: var(--text-color); line-height: 1.6; margin: 15px 0;">
                    {{ $profile->bio }}
                </p>
                @endif

                <!-- Action Buttons -->
                @if($isOwner)
                <div style="display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap;">
                    <a href="{{ route('talent.profile.edit') }}" class="primary-btn1 btn-hover"
                        style="text-decoration: none; padding: 10px 20px; display: inline-block;">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </a>
                    <a href="{{ route('talent.profile.public', $profile->id) }}" target="_blank"
                        style="text-decoration: none; padding: 10px 20px; display: inline-block; border: 2px solid #2196F3; color: #2196F3; border-radius: 4px; background: transparent;">
                        <i class="bi bi-box-arrow-up-right"></i> View Public Profile
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
        {{ session('error') }}
    </div>
    @endif

    <!-- Profile Content Grid -->
    <div
        style="display: grid; grid-template-columns: 1fr 350px; gap: 30px; @media (max-width: 968px) { grid-template-columns: 1fr; }">
        <!-- Main Content -->
        <div class="profile-main">
            <!-- Education Section -->
            @if($profile->education->count() > 0)
            <div class="profile-section"
                style="background: #fff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2
                    style="font-family: var(--font-bricolageGrotesque); font-size: 24px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-mortarboard" style="color: #F53003;"></i>
                    Education
                </h2>
                <div style="display: flex; flex-direction: column; gap: 25px;">
                    @foreach($profile->education as $education)
                    <div
                        style="padding-bottom: 25px; border-bottom: 1px solid #f0f0f0; @if($loop->last) border-bottom: none; @endif">
                        <div
                            style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 15px;">
                            <div style="flex: 1;">
                                <h3
                                    style="font-size: 18px; font-weight: 600; color: var(--title-color); margin: 0 0 5px 0;">
                                    {{ ucfirst(str_replace('_', ' ', $education->degree_type->value)) }}
                                </h3>
                                <p style="color: var(--text-color); margin: 5px 0; font-weight: 500;">
                                    {{ $education->field_of_study }}
                                </p>
                                @if($education->institution)
                                <p style="color: #666; margin: 5px 0;">
                                    {{ $education->institution->name }}
                                </p>
                                @endif
                                <div style="display: flex; gap: 15px; margin-top: 10px; flex-wrap: wrap;">
                                    <span style="color: #666; font-size: 14px;">
                                        <i class="bi bi-calendar3"></i>
                                        {{ $education->start_date?->format('M Y') }}
                                        @if($education->is_current)
                                        - Present
                                        @elseif($education->end_date)
                                        - {{ $education->end_date->format('M Y') }}
                                        @endif
                                    </span>
                                    @if($education->gpa)
                                    <span style="color: #666; font-size: 14px;">
                                        <i class="bi bi-star"></i>
                                        GPA: {{ number_format($education->gpa, 2) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @if($education->is_primary)
                            <span
                                style="background: #4CAF50; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                Primary
                            </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Skills Section -->
            @if($profile->skills->count() > 0)
            <div class="profile-section"
                style="background: #fff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2
                    style="font-family: var(--font-bricolageGrotesque); font-size: 24px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-tools" style="color: #F53003;"></i>
                    Skills
                </h2>
                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                    @foreach($profile->skills as $skill)
                    <div
                        style="background: #f8f9fa; padding: 10px 18px; border-radius: 20px; display: flex; align-items: center; gap: 8px;">
                        <span style="font-weight: 500; color: var(--title-color);">{{ $skill->skill_name }}</span>
                        <span
                            style="background: #e9ecef; padding: 2px 8px; border-radius: 10px; font-size: 12px; color: #666;">
                            {{ ucfirst($skill->proficiency_level->value) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Work History Section -->
            @if($profile->workHistory->count() > 0)
            <div class="profile-section"
                style="background: #fff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2
                    style="font-family: var(--font-bricolageGrotesque); font-size: 24px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-briefcase" style="color: #F53003;"></i>
                    Work History
                </h2>
                <div style="display: flex; flex-direction: column; gap: 25px;">
                    @foreach($profile->workHistory as $work)
                    <div
                        style="padding-bottom: 25px; border-bottom: 1px solid #f0f0f0; @if($loop->last) border-bottom: none; @endif">
                        <div style="flex: 1;">
                            <h3
                                style="font-size: 18px; font-weight: 600; color: var(--title-color); margin: 0 0 5px 0;">
                                {{ $work->position }}
                            </h3>
                            <p style="color: var(--text-color); margin: 5px 0; font-weight: 500;">
                                {{ $work->company }}
                            </p>
                            @if($work->location)
                            <p style="color: #666; margin: 5px 0;">
                                <i class="bi bi-geo-alt"></i> {{ $work->location }}
                            </p>
                            @endif
                            <div style="display: flex; gap: 15px; margin-top: 10px; flex-wrap: wrap;">
                                <span style="color: #666; font-size: 14px;">
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
                            <p style="color: var(--text-color); margin-top: 10px; line-height: 1.6;">
                                {{ $work->description }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Languages Section -->
            @if($profile->languages->count() > 0)
            <div class="profile-section"
                style="background: #fff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2
                    style="font-family: var(--font-bricolageGrotesque); font-size: 24px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-translate" style="color: #F53003;"></i>
                    Languages
                </h2>
                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                    @foreach($profile->languages as $language)
                    <div
                        style="background: #f8f9fa; padding: 10px 18px; border-radius: 20px; display: flex; align-items: center; gap: 8px;">
                        <span style="font-weight: 500; color: var(--title-color);">{{ $language->language_name }}</span>
                        <span
                            style="background: #e9ecef; padding: 2px 8px; border-radius: 10px; font-size: 12px; color: #666;">
                            {{ ucfirst($language->proficiency_level->value) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Certifications Section -->
            @if($profile->certifications->count() > 0)
            <div class="profile-section"
                style="background: #fff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2
                    style="font-family: var(--font-bricolageGrotesque); font-size: 24px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-award" style="color: #F53003;"></i>
                    Certifications
                </h2>
                <div style="display: flex; flex-direction: column; gap: 25px;">
                    @foreach($profile->certifications as $cert)
                    <div
                        style="padding-bottom: 25px; border-bottom: 1px solid #f0f0f0; @if($loop->last) border-bottom: none; @endif">
                        <div style="flex: 1;">
                            <h3
                                style="font-size: 18px; font-weight: 600; color: var(--title-color); margin: 0 0 5px 0;">
                                {{ $cert->name }}
                            </h3>
                            <p style="color: var(--text-color); margin: 5px 0; font-weight: 500;">
                                {{ $cert->issuer }}
                            </p>
                            <div style="display: flex; gap: 15px; margin-top: 10px; flex-wrap: wrap;">
                                <span style="color: #666; font-size: 14px;">
                                    <i class="bi bi-calendar3"></i>
                                    Obtained: {{ $cert->date_obtained->format('M Y') }}
                                </span>
                                @if($cert->expiration_date)
                                <span style="color: #666; font-size: 14px;">
                                    | Expires: {{ $cert->expiration_date->format('M Y') }}
                                </span>
                                @endif
                            </div>
                            @if($cert->credential_url)
                            <a href="{{ $cert->credential_url }}" target="_blank"
                                style="color: #2196F3; font-size: 14px; margin-top: 10px; display: inline-block;">
                                View Credential <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Additional Details Section -->
            @if($profile->fun_fact || $profile->passion || $profile->gigs_freelance || $profile->leadership ||
            $profile->volunteer || $profile->hobbies)
            <div class="profile-section"
                style="background: #fff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2
                    style="font-family: var(--font-bricolageGrotesque); font-size: 24px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-person-heart" style="color: #F53003;"></i>
                    Additional Details
                </h2>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    @if($profile->fun_fact)
                    <div>
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--title-color); margin: 0 0 8px 0;">Fun
                            Fact</h3>
                        <p style="color: var(--text-color); line-height: 1.6; margin: 0;">{{ $profile->fun_fact }}</p>
                    </div>
                    @endif

                    @if($profile->passion)
                    <div>
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--title-color); margin: 0 0 8px 0;">
                            Passion</h3>
                        <p style="color: var(--text-color); line-height: 1.6; margin: 0;">{{ $profile->passion }}</p>
                    </div>
                    @endif

                    @if($profile->gigs_freelance)
                    <div>
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--title-color); margin: 0 0 8px 0;">
                            Gigs / Freelance</h3>
                        <p style="color: var(--text-color); line-height: 1.6; margin: 0;">{{ $profile->gigs_freelance }}
                        </p>
                    </div>
                    @endif

                    @if($profile->leadership)
                    <div>
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--title-color); margin: 0 0 8px 0;">
                            Leadership</h3>
                        <p style="color: var(--text-color); line-height: 1.6; margin: 0;">{{ $profile->leadership }}</p>
                    </div>
                    @endif

                    @if($profile->volunteer)
                    <div>
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--title-color); margin: 0 0 8px 0;">
                            Volunteer</h3>
                        <p style="color: var(--text-color); line-height: 1.6; margin: 0;">{{ $profile->volunteer }}</p>
                    </div>
                    @endif

                    @if($profile->hobbies)
                    <div>
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--title-color); margin: 0 0 8px 0;">
                            Hobbies</h3>
                        <p style="color: var(--text-color); line-height: 1.6; margin: 0;">{{ $profile->hobbies }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Portfolio & Social Links Section -->
            @if($profile->github_url || $profile->behance_url || $profile->portfolio_url || $profile->linkedin_url ||
            $profile->twitter_url)
            <div class="profile-section"
                style="background: #fff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2
                    style="font-family: var(--font-bricolageGrotesque); font-size: 24px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-link-45deg" style="color: #F53003;"></i>
                    Portfolio & Social Links
                </h2>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @if($profile->github_url)
                    <a href="{{ $profile->github_url }}" target="_blank"
                        style="display: flex; align-items: center; gap: 10px; color: var(--text-color); text-decoration: none; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                        <i class="bi bi-github" style="font-size: 20px;"></i>
                        <span>GitHub</span>
                        <i class="bi bi-box-arrow-up-right" style="margin-left: auto;"></i>
                    </a>
                    @endif

                    @if($profile->behance_url)
                    <a href="{{ $profile->behance_url }}" target="_blank"
                        style="display: flex; align-items: center; gap: 10px; color: var(--text-color); text-decoration: none; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                        <i class="bi bi-palette" style="font-size: 20px;"></i>
                        <span>Behance</span>
                        <i class="bi bi-box-arrow-up-right" style="margin-left: auto;"></i>
                    </a>
                    @endif

                    @if($profile->portfolio_url)
                    <a href="{{ $profile->portfolio_url }}" target="_blank"
                        style="display: flex; align-items: center; gap: 10px; color: var(--text-color); text-decoration: none; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                        <i class="bi bi-briefcase" style="font-size: 20px;"></i>
                        <span>Portfolio</span>
                        <i class="bi bi-box-arrow-up-right" style="margin-left: auto;"></i>
                    </a>
                    @endif

                    @if($profile->linkedin_url)
                    <a href="{{ $profile->linkedin_url }}" target="_blank"
                        style="display: flex; align-items: center; gap: 10px; color: var(--text-color); text-decoration: none; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                        <i class="bi bi-linkedin" style="font-size: 20px; color: #0077b5;"></i>
                        <span>LinkedIn</span>
                        <i class="bi bi-box-arrow-up-right" style="margin-left: auto;"></i>
                    </a>
                    @endif

                    @if($profile->twitter_url)
                    <a href="{{ $profile->twitter_url }}" target="_blank"
                        style="display: flex; align-items: center; gap: 10px; color: var(--text-color); text-decoration: none; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                        <i class="bi bi-twitter" style="font-size: 20px; color: #1DA1F2;"></i>
                        <span>Twitter/X</span>
                        <i class="bi bi-box-arrow-up-right" style="margin-left: auto;"></i>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Work Preferences Section -->
            @if($profile->availability || $profile->availability_details || $profile->preferred_location ||
            $profile->salary_expectations)
            <div class="profile-section"
                style="background: #fff; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2
                    style="font-family: var(--font-bricolageGrotesque); font-size: 24px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-briefcase-fill" style="color: #F53003;"></i>
                    Work Preferences
                </h2>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    @if($profile->availability)
                    <div>
                        <span style="color: #666; font-size: 14px;">Availability</span>
                        <p style="margin: 5px 0 0 0; color: var(--title-color); font-weight: 500;">
                            {{ ucfirst(str_replace('_', ' ', $profile->availability->value)) }}
                        </p>
                    </div>
                    @endif

                    @if($profile->availability_details)
                    <div>
                        <span style="color: #666; font-size: 14px;">Availability Details</span>
                        <p style="margin: 5px 0 0 0; color: var(--text-color); line-height: 1.6;">
                            {{ $profile->availability_details }}
                        </p>
                    </div>
                    @endif

                    @if($profile->preferred_location)
                    <div>
                        <span style="color: #666; font-size: 14px;">Preferred Location</span>
                        <p style="margin: 5px 0 0 0; color: var(--title-color); font-weight: 500;">
                            {{ ucfirst(str_replace('_', ' ', $profile->preferred_location->value)) }}
                        </p>
                    </div>
                    @endif

                    @if($profile->salary_expectations)
                    <div>
                        <span style="color: #666; font-size: 14px;">Salary Expectations</span>
                        <p style="margin: 5px 0 0 0; color: var(--title-color); font-weight: 500;">
                            GHS {{ number_format($profile->salary_expectations, 2) }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="profile-sidebar">
            <!-- Profile Stats -->
            <div class="profile-card"
                style="background: #fff; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="font-size: 18px; font-weight: 600; color: var(--title-color); margin: 0 0 20px 0;">
                    Profile Completion
                </h3>
                <div
                    style="background: #f0f0f0; height: 12px; border-radius: 6px; overflow: hidden; margin-bottom: 10px;">
                    <div
                        style="background: #4CAF50; height: 100%; width: {{ $profile->profile_completeness_score ?? 0 }}%; transition: width 0.3s ease;">
                    </div>
                </div>
                <p style="text-align: center; font-size: 24px; font-weight: 600; color: var(--title-color); margin: 0;">
                    {{ $profile->profile_completeness_score ?? 0 }}%
                </p>
            </div>

            <!-- Additional Info -->
            <div class="profile-card"
                style="background: #fff; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="font-size: 18px; font-weight: 600; color: var(--title-color); margin: 0 0 20px 0;">
                    Additional Information
                </h3>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    @if($profile->date_of_birth)
                    <div>
                        <span style="color: #666; font-size: 14px;">Date of Birth</span>
                        <p style="margin: 5px 0 0 0; color: var(--title-color); font-weight: 500;">
                            {{ $profile->date_of_birth->format('F j, Y') }}
                        </p>
                    </div>
                    @endif

                    @if($profile->gender)
                    <div>
                        <span style="color: #666; font-size: 14px;">Gender</span>
                        <p style="margin: 5px 0 0 0; color: var(--title-color); font-weight: 500;">
                            {{ ucfirst($profile->gender) }}
                        </p>
                    </div>
                    @endif

                    @if($profile->nss_status)
                    <div>
                        <span style="color: #666; font-size: 14px;">NSS Status</span>
                        <p style="margin: 5px 0 0 0; color: var(--title-color); font-weight: 500;">
                            {{ $profile->nss_status }}
                        </p>
                        @if($profile->nss_posting_location)
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">
                            {{ $profile->nss_posting_location }}
                        </p>
                        @endif
                    </div>
                    @endif

                    @if($profile->verification_status)
                    <div>
                        <span style="color: #666; font-size: 14px;">Verification</span>
                        <p style="margin: 5px 0 0 0;">
                            <span
                                style="background: {{ $profile->verification_status === 'verified' ? '#4CAF50' : '#ff9800' }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                {{ ucfirst($profile->verification_status) }}
                            </span>
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
