@extends('layouts.dashboard.main')

@section('content')
<div class="profile-edit-container" style="max-width: 1000px; margin: 0 auto; padding: 20px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1
            style="font-family: var(--font-bricolageGrotesque); font-size: 28px; font-weight: 600; color: var(--title-color); margin: 0;">
            Edit Profile
        </h1>
        <a href="{{ route('talent.profile.show') }}"
            style="text-decoration: none; padding: 10px 20px; border: 2px solid #ddd; border-radius: 4px; color: var(--text-color);">
            <i class="bi bi-x-lg"></i> Cancel
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Profile Photo & Resume Section -->
    <div
        style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2
            style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
            Profile Photo & Documents
        </h2>

        <!-- Profile Photo -->
        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Profile Photo
            </label>
            <div style="display: flex; align-items: center; gap: 20px;">
                @if($profile->profile_photo)
                <img src="{{ asset('storage/'.$profile->profile_photo) }}" alt="Profile Photo"
                    style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #f0f0f0;">
                @else
                <div
                    style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #F53003 0%, #ff6b35 100%); display: flex; align-items: center; justify-content: center; font-size: 32px; color: white; font-weight: bold; border: 3px solid #f0f0f0;">
                    {{ strtoupper(substr($profile->first_name, 0, 1) . substr($profile->last_name, 0, 1)) }}
                </div>
                @endif
                <div>
                    <input type="file" name="photo" id="photo" accept="image/*" style="display: none;"
                        onchange="uploadPhoto(this)">
                    <button type="button" onclick="document.getElementById('photo').click()"
                        class="primary-btn1 btn-hover" style="padding: 8px 16px; font-size: 14px;">
                        {{ $profile->profile_photo ? 'Change Photo' : 'Upload Photo' }}
                    </button>
                    <p style="margin-top: 8px; font-size: 12px; color: #666;">Accepted: JPG, PNG (Max 2MB)</p>
                </div>
            </div>
        </div>

        <!-- Resume Upload -->
        <div>
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Resume
            </label>
            <div style="display: flex; align-items: center; gap: 20px;">
                @if($profile->resume_url)
                <div
                    style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #f0f0f0; border-radius: 4px;">
                    <i class="bi bi-file-earmark-pdf" style="font-size: 24px; color: #4CAF50;"></i>
                    <span style="color: #4CAF50; font-weight: 500;">Resume uploaded</span>
                </div>
                @else
                <div style="padding: 10px; background: #f0f0f0; border-radius: 4px; color: #999;">
                    No resume uploaded
                </div>
                @endif
                <div>
                    <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx" style="display: none;"
                        onchange="uploadResume(this)">
                    <button type="button" onclick="document.getElementById('resume').click()"
                        class="primary-btn1 btn-hover" style="padding: 8px 16px; font-size: 14px;">
                        {{ $profile->resume_url ? 'Change Resume' : 'Upload Resume' }}
                    </button>
                    <p style="margin-top: 8px; font-size: 12px; color: #666;">Accepted: PDF, DOC, DOCX (Max 5MB)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="{{ route('talent.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div
            style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <!-- Basic Information -->
            <h2
                style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                Basic Information
            </h2>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        First Name <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="first_name" value="{{ old('first_name', $profile->first_name) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Last Name <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="last_name" value="{{ old('last_name', $profile->last_name) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
            </div>

            <!-- Date of Birth -->
            @php
            $dobValues = getDateComponentValues('dob', $profile->date_of_birth);
            $currentYear = (int) date('Y');
            $dobYearOptions = getYearOptions($currentYear - 100, $currentYear - 13);
            @endphp
            @include('pages.profile.partials.date-selector', [
            'label' => 'Date of Birth',
            'required' => false,
            'prefix' => 'dob',
            'dayValue' => old('dob_day', $dobValues['day']),
            'monthValue' => old('dob_month', $dobValues['month']),
            'yearValue' => old('dob_year', $dobValues['year']),
            'yearOptions' => $dobYearOptions,
            'errorKey' => 'date_of_birth'
            ])

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Gender
                    </label>
                    <select name="gender"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $profile->gender) === 'male' ? 'selected' : '' }}>Male
                        </option>
                        <option value="female" {{ old('gender', $profile->gender) === 'female' ? 'selected' : ''
                            }}>Female</option>
                        <option value="other" {{ old('gender', $profile->gender) === 'other' ? 'selected' : '' }}>Other
                        </option>
                        <option value="prefer_not_to_say" {{ old('gender', $profile->gender) === 'prefer_not_to_say' ?
                            'selected' : '' }}>Prefer not to say</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Location
                    </label>
                    <input type="text" name="location" value="{{ old('location', $profile->location) }}"
                        placeholder="e.g., Accra, Ghana"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Bio
                </label>
                <textarea name="bio" rows="4" placeholder="Tell us about yourself..."
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;">{{ old('bio', $profile->bio) }}</textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Video Introduction URL (Optional)
                </label>
                <input type="url" name="video_introduction"
                    value="{{ old('video_introduction', $profile->video_introduction) }}"
                    placeholder="https://youtube.com/watch?v=... or https://vimeo.com/..."
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                <p style="margin-top: 5px; font-size: 12px; color: #666;">Add a link to your video introduction
                    (YouTube, Vimeo, etc.)</p>
            </div>
        </div>

        <!-- NSS Information -->
        <div
            style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h2
                style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                NSS Information
            </h2>

            <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        NSS Status
                    </label>
                    <input type="text" name="nss_status" value="{{ old('nss_status', $profile->nss_status) }}"
                        placeholder="e.g., Completed, In Progress, Not Started"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        NSS Posting Location
                    </label>
                    <input type="text" name="nss_posting_location"
                        value="{{ old('nss_posting_location', $profile->nss_posting_location) }}"
                        placeholder="e.g., Accra, Greater Accra Region"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        NSS Posting Number
                    </label>
                    <input type="text" name="nss_posting_number"
                        value="{{ old('nss_posting_number', $profile->nss_posting_number) }}"
                        placeholder="Your NSS posting number"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
            </div>
        </div>

        <!-- Additional Details Section -->
        <div
            style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h2
                style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                Additional Details
            </h2>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Fun Fact
                </label>
                <textarea name="fun_fact" rows="3" placeholder="What's something that most people don't know about you?"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;">{{ old('fun_fact', $profile->fun_fact) }}</textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Passion
                </label>
                <textarea name="passion" rows="3" placeholder="What motivates you? What drives you?"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;">{{ old('passion', $profile->passion) }}</textarea>
            </div>


            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Hobbies
                </label>
                <textarea name="hobbies" rows="3"
                    placeholder="What do you love to do outside of work? Cooking? Sports? Traveling?"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;">{{ old('hobbies', $profile->hobbies) }}</textarea>
            </div>
        </div>

        <!-- Portfolio & Social Links Section -->
        <div
            style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h2
                style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                Portfolio & Social Links
            </h2>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        GitHub URL
                    </label>
                    <input type="url" name="github_url" value="{{ old('github_url', $profile->github_url) }}"
                        placeholder="https://github.com/username"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Behance URL
                    </label>
                    <input type="url" name="behance_url" value="{{ old('behance_url', $profile->behance_url) }}"
                        placeholder="https://behance.net/username"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Portfolio URL
                </label>
                <input type="url" name="portfolio_url" value="{{ old('portfolio_url', $profile->portfolio_url) }}"
                    placeholder="https://yourportfolio.com"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        LinkedIn URL
                    </label>
                    <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $profile->linkedin_url) }}"
                        placeholder="https://linkedin.com/in/username"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Twitter/X URL
                    </label>
                    <input type="url" name="twitter_url" value="{{ old('twitter_url', $profile->twitter_url) }}"
                        placeholder="https://twitter.com/username"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
            </div>
        </div>

        <!-- Work Preferences Section -->
        <div
            style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h2
                style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                Work Preferences
            </h2>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Availability
                </label>
                <select name="availability"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    <option value="">Select Availability</option>
                    <option value="full_time" {{ old('availability', $profile->availability?->value) === 'full_time' ?
                        'selected' : '' }}>Full-time</option>
                    <option value="part_time" {{ old('availability', $profile->availability?->value) === 'part_time' ?
                        'selected' : '' }}>Part-time</option>
                    <option value="internship" {{ old('availability', $profile->availability?->value) === 'internship' ?
                        'selected' : '' }}>Internship</option>
                    <option value="contract" {{ old('availability', $profile->availability?->value) === 'contract' ?
                        'selected' : '' }}>Contract</option>
                    <option value="flexible" {{ old('availability', $profile->availability?->value) === 'flexible' ?
                        'selected' : '' }}>Flexible</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Availability Details
                </label>
                <textarea name="availability_details" rows="3"
                    placeholder="e.g., Available starting June 2025, Flexible hours preferred"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;">{{ old('availability_details', $profile->availability_details) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Preferred Location
                    </label>
                    <select name="preferred_location"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="">Select Location Preference</option>
                        <option value="remote" {{ old('preferred_location', $profile->preferred_location?->value) ===
                            'remote' ? 'selected' : '' }}>Remote</option>
                        <option value="hybrid" {{ old('preferred_location', $profile->preferred_location?->value) ===
                            'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        <option value="on_site" {{ old('preferred_location', $profile->preferred_location?->value) ===
                            'on_site' ? 'selected' : '' }}>On-site</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Salary Expectations (GHS)
                    </label>
                    <input type="number" name="salary_expectations"
                        value="{{ old('salary_expectations', $profile->salary_expectations) }}" placeholder="e.g., 3000"
                        step="0.01" min="0"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div style="display: flex; gap: 15px; margin-bottom: 30px;">
            <button type="submit" class="primary-btn1 btn-hover"
                style="padding: 12px 30px; border: none; cursor: pointer;">
                <i class="bi bi-check-lg"></i> Save Changes
            </button>
            <a href="{{ route('talent.profile.show') }}"
                style="text-decoration: none; padding: 12px 30px; border: 2px solid #ddd; border-radius: 4px; color: var(--text-color); display: inline-block;">
                Cancel
            </a>
        </div>
    </form>

    <!-- Education Section -->
    <div
        style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2
            style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
            Education
        </h2>

        <!-- Existing Education -->
        @if($profile->education->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px;">Your Education Records</h3>
            @foreach($profile->education as $edu)
            <div
                style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>{{ $edu->institution?->name ?? 'Institution not set' }}</strong><br>
                    <span>{{ ucfirst(str_replace('_', ' ', $edu->degree_type->value)) }} in {{ $edu->field_of_study
                        }}</span>
                    @if($edu->is_current)
                    <span
                        style="background: #4CAF50; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 10px;">Current</span>
                    @endif
                    @if($edu->is_primary)
                    <span
                        style="background: #2196F3; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 10px;">Primary</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('talent.profile.education.remove', ['id' => $edu->id]) }}"
                    style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to remove this education record?')"
                        style="background: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                        Remove
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Add Education Form -->
        <form method="POST" action="{{ route('talent.profile.education.add') }}">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px;">{{ $profile->education->count() > 0 ?
                'Add Another Education' : 'Add Education' }}</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Institution
                    </label>
                    <select name="institution_id"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="">Select Institution</option>
                        @foreach($institutions as $institution)
                        <option value="{{ $institution->id }}" {{ old('institution_id')==$institution->id ? 'selected' :
                            '' }}>
                            {{ $institution->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Degree Type <span style="color: #F53003;">*</span>
                    </label>
                    <select name="degree_type" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="">Select Degree Type</option>
                        <option value="certificate" {{ old('degree_type')=='certificate' ? 'selected' : '' }}>
                            Certificate</option>
                        <option value="diploma" {{ old('degree_type')=='diploma' ? 'selected' : '' }}>Diploma</option>
                        <option value="bachelors" {{ old('degree_type')=='bachelors' ? 'selected' : '' }}>Bachelor's
                        </option>
                        <option value="masters" {{ old('degree_type')=='masters' ? 'selected' : '' }}>Master's</option>
                        <option value="phd" {{ old('degree_type')=='phd' ? 'selected' : '' }}>PhD</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Field of Study <span style="color: #F53003;">*</span>
                </label>
                <input type="text" name="field_of_study" value="{{ old('field_of_study') }}" required
                    placeholder="e.g., Computer Science, Business Administration"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
            </div>

            @php
            $startDateValues = getDateComponentValues('start_date', old('start_date'));
            $endDateValues = getDateComponentValues('end_date', old('end_date'));
            $currentYear = (int) date('Y');
            $yearOptions = getYearOptions($currentYear - 50, $currentYear + 10);
            @endphp

            <div style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'Start Date',
                'required' => true,
                'prefix' => 'start_date',
                'dayValue' => old('start_date_day', $startDateValues['day']),
                'monthValue' => old('start_date_month', $startDateValues['month']),
                'yearValue' => old('start_date_year', $startDateValues['year']),
                'yearOptions' => $yearOptions,
                'errorKey' => 'start_date'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                <input type="hidden" name="is_current" value="0">
                <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                    <input type="checkbox" name="is_current" value="1" {{ old('is_current') ? 'checked' : '' }}
                        onchange="toggleEndDate(this)">
                    <span style="font-weight: 500;">Currently enrolled</span>
                </label>
            </div>

            <div id="endDateContainer" style="margin-bottom: 20px; {{ old('is_current') ? 'display: none;' : '' }}">
                @include('pages.profile.partials.date-selector', [
                'label' => 'End Date',
                'required' => false,
                'prefix' => 'end_date',
                'dayValue' => old('end_date_day', $endDateValues['day']),
                'monthValue' => old('end_date_month', $endDateValues['month']),
                'yearValue' => old('end_date_year', $endDateValues['year']),
                'yearOptions' => $yearOptions,
                'errorKey' => 'end_date'
                ])
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="is_primary" value="1" {{ old('is_primary') ? 'checked' : '' }}>
                        <span style="font-weight: 500;">Mark as primary education</span>
                    </label>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        GPA (Optional)
                    </label>
                    <input type="number" name="gpa" value="{{ old('gpa') }}" step="0.01" min="0" max="5"
                        placeholder="e.g., 3.5"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
            </div>

            <button type="submit" class="primary-btn1 btn-hover"
                style="padding: 10px 20px; border: none; cursor: pointer;">
                <i class="bi bi-plus-circle"></i> Add Education
            </button>
        </form>
    </div>

    <!-- Skills Section -->
    <div style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2
            style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
            Skills
        </h2>

        <!-- Existing Skills -->
        @if($profile->skills->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px;">Your Skills ({{
                $profile->skills->count() }})</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach($profile->skills as $skill)
                <div
                    style="background: #f9f9f9; padding: 10px 15px; border-radius: 20px; display: flex; align-items: center; gap: 10px;">
                    <span><strong>{{ $skill->skill_name }}</strong> - {{ ucfirst($skill->proficiency_level->value)
                        }}</span>
                    <form method="POST" action="{{ route('talent.profile.skill.remove', ['id' => $skill->id]) }}"
                        style="display: inline; margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Remove this skill?')"
                            style="background: #f44336; color: white; border: none; padding: 2px 8px; border-radius: 12px; cursor: pointer; font-size: 12px;">
                            ×
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Add Skill Form -->
        <form method="POST" action="{{ route('talent.profile.skill.add') }}">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px;">{{ $profile->skills->count() > 0 ? 'Add
                Another Skill' : 'Add Skills' }}</h3>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Skill Name <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="skill_name" value="{{ old('skill_name') }}" required
                        placeholder="e.g., JavaScript, Project Management, Communication"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Proficiency Level <span style="color: #F53003;">*</span>
                    </label>
                    <select name="proficiency_level" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="">Select Level</option>
                        <option value="beginner" {{ old('proficiency_level')=='beginner' ? 'selected' : '' }}>Beginner
                        </option>
                        <option value="intermediate" {{ old('proficiency_level')=='intermediate' ? 'selected' : '' }}>
                            Intermediate</option>
                        <option value="advanced" {{ old('proficiency_level')=='advanced' ? 'selected' : '' }}>Advanced
                        </option>
                        <option value="expert" {{ old('proficiency_level')=='expert' ? 'selected' : '' }}>Expert
                        </option>
                    </select>
                </div>
            </div>

            <button type="submit" class="primary-btn1 btn-hover"
                style="padding: 10px 20px; border: none; cursor: pointer;">
                <i class="bi bi-plus-circle"></i> Add Skill
            </button>
        </form>
    </div>

    <!-- Work History Section -->
    <div
        style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2
            style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
            Work History
        </h2>

        <!-- Existing Work History -->
        @if($profile->workHistory->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px;">Your Work History ({{
                $profile->workHistory->count() }})</h3>
            @foreach($profile->workHistory as $work)
            <div
                style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>{{ $work->position }}</strong> at <strong>{{ $work->company }}</strong><br>
                    <span style="color: #666; font-size: 14px;">
                        <i class="bi bi-calendar3"></i>
                        {{ $work->start_date?->format('M Y') }}
                        @if($work->is_current)
                        - Present
                        @elseif($work->end_date)
                        - {{ $work->end_date->format('M Y') }}
                        @endif
                    </span>
                    @if($work->location)
                    <br><span style="color: #666; font-size: 14px;"><i class="bi bi-geo-alt"></i> {{ $work->location
                        }}</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('talent.profile.work-history.remove', ['id' => $work->id]) }}"
                    style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to remove this work history record?')"
                        style="background: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                        Remove
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Add Work History Form -->
        <form method="POST" action="{{ route('talent.profile.work-history.add') }}">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px;">{{ $profile->workHistory->count() > 0 ?
                'Add Another Work Experience' : 'Add Work Experience' }}</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Company <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="company" value="{{ old('company') }}" required
                        placeholder="e.g., ABC Company"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Position/Title <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="position" value="{{ old('position') }}" required
                        placeholder="e.g., Barista, Babysitter, Intern"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Description
                </label>
                <textarea name="description" rows="3" value="{{ old('description') }}"
                    placeholder="Describe your responsibilities and achievements..."
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;">{{ old('description') }}</textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Location
                </label>
                <input type="text" name="location" value="{{ old('location') }}" placeholder="e.g., Accra, Ghana"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
            </div>

            @php
            $workStartDateValues = getDateComponentValues('work_start_date', old('work_start_date'));
            $workEndDateValues = getDateComponentValues('work_end_date', old('work_end_date'));
            $currentYear = (int) date('Y');
            $yearOptions = getYearOptions($currentYear - 50, $currentYear + 10);
            @endphp

            <div style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'Start Date',
                'required' => true,
                'prefix' => 'start_date',
                'dayValue' => old('start_date_day', $workStartDateValues['day']),
                'monthValue' => old('start_date_month', $workStartDateValues['month']),
                'yearValue' => old('start_date_year', $workStartDateValues['year']),
                'yearOptions' => $yearOptions,
                'errorKey' => 'start_date'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                <input type="hidden" name="is_current" value="0">
                <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                    <input type="checkbox" name="is_current" value="1" {{ old('is_current') ? 'checked' : '' }}
                        onchange="toggleWorkEndDate(this)">
                    <span style="font-weight: 500;">Currently working here</span>
                </label>
            </div>

            <div id="workEndDateContainer" style="margin-bottom: 20px; {{ old('is_current') ? 'display: none;' : '' }}">
                @include('pages.profile.partials.date-selector', [
                'label' => 'End Date',
                'required' => false,
                'prefix' => 'end_date',
                'dayValue' => old('end_date_day', $workEndDateValues['day']),
                'monthValue' => old('end_date_month', $workEndDateValues['month']),
                'yearValue' => old('end_date_year', $workEndDateValues['year']),
                'yearOptions' => $yearOptions,
                'errorKey' => 'end_date'
                ])
            </div>

            <button type="submit" class="primary-btn1 btn-hover"
                style="padding: 10px 20px; border: none; cursor: pointer;">
                <i class="bi bi-plus-circle"></i> Add Work Experience
            </button>
        </form>
    </div>

    <!-- Languages Section -->
    <div
        style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2
            style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
            Languages
        </h2>

        <!-- Existing Languages -->
        @if($profile->languages->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px;">Your Languages ({{
                $profile->languages->count() }})</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach($profile->languages as $language)
                <div
                    style="background: #f9f9f9; padding: 10px 15px; border-radius: 20px; display: flex; align-items: center; gap: 10px;">
                    <span><strong>{{ $language->language_name }}</strong> - {{
                        ucfirst($language->proficiency_level->value) }}</span>
                    <form method="POST" action="{{ route('talent.profile.language.remove', ['id' => $language->id]) }}"
                        style="display: inline; margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Remove this language?')"
                            style="background: #f44336; color: white; border: none; padding: 2px 8px; border-radius: 12px; cursor: pointer; font-size: 12px;">
                            ×
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Add Language Form -->
        <form method="POST" action="{{ route('talent.profile.language.add') }}">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px;">{{ $profile->languages->count() > 0 ?
                'Add Another Language' : 'Add Languages' }}</h3>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Language <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="language_name" value="{{ old('language_name') }}" required
                        placeholder="e.g., English, Twi, French"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Proficiency Level <span style="color: #F53003;">*</span>
                    </label>
                    <select name="proficiency_level" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="">Select Level</option>
                        <option value="beginner" {{ old('proficiency_level')=='beginner' ? 'selected' : '' }}>Beginner
                        </option>
                        <option value="intermediate" {{ old('proficiency_level')=='intermediate' ? 'selected' : '' }}>
                            Intermediate</option>
                        <option value="advanced" {{ old('proficiency_level')=='advanced' ? 'selected' : '' }}>Advanced
                        </option>
                        <option value="expert" {{ old('proficiency_level')=='expert' ? 'selected' : '' }}>Expert
                        </option>
                    </select>
                </div>
            </div>

            <button type="submit" class="primary-btn1 btn-hover"
                style="padding: 10px 20px; border: none; cursor: pointer;">
                <i class="bi bi-plus-circle"></i> Add Language
            </button>
        </form>
    </div>

    <!-- Certifications Section -->
    <div
        style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2
            style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
            Certifications
        </h2>

        <!-- Existing Certifications -->
        @if($profile->certifications->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px;">Your Certifications ({{
                $profile->certifications->count() }})</h3>
            @foreach($profile->certifications as $cert)
            <div
                style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>{{ $cert->name }}</strong><br>
                    <span style="color: #666; font-size: 14px;">Issued by {{ $cert->issuer }}</span><br>
                    <span style="color: #666; font-size: 14px;">
                        <i class="bi bi-calendar3"></i>
                        Obtained: {{ $cert->date_obtained->format('M Y') }}
                        @if($cert->expiration_date)
                        | Expires: {{ $cert->expiration_date->format('M Y') }}
                        @endif
                    </span>
                    @if($cert->credential_url)
                    <br><a href="{{ $cert->credential_url }}" target="_blank"
                        style="color: #2196F3; font-size: 14px;">View Credential <i
                            class="bi bi-box-arrow-up-right"></i></a>
                    @endif
                </div>
                <form method="POST" action="{{ route('talent.profile.certification.remove', ['id' => $cert->id]) }}"
                    style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to remove this certification?')"
                        style="background: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                        Remove
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Add Certification Form -->
        <form method="POST" action="{{ route('talent.profile.certification.add') }}">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px;">{{ $profile->certifications->count() > 0
                ? 'Add Another Certification' : 'Add Certification' }}</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Certification Name <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="e.g., Google Analytics Certified"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Issuer/Organization <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="issuer" value="{{ old('issuer') }}" required
                        placeholder="e.g., Google, Microsoft"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
            </div>

            @php
            $certObtainedDateValues = getDateComponentValues('cert_date_obtained', old('cert_date_obtained'));
            $certExpirationDateValues = getDateComponentValues('cert_expiration_date', old('cert_expiration_date'));
            $currentYear = (int) date('Y');
            $yearOptions = getYearOptions($currentYear - 50, $currentYear + 50);
            @endphp

            <div style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'Date Obtained',
                'required' => true,
                'prefix' => 'date_obtained',
                'dayValue' => old('date_obtained_day', $certObtainedDateValues['day']),
                'monthValue' => old('date_obtained_month', $certObtainedDateValues['month']),
                'yearValue' => old('date_obtained_year', $certObtainedDateValues['year']),
                'yearOptions' => $yearOptions,
                'errorKey' => 'date_obtained'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'Expiration Date (Optional)',
                'required' => false,
                'prefix' => 'expiration_date',
                'dayValue' => old('expiration_date_day', $certExpirationDateValues['day']),
                'monthValue' => old('expiration_date_month', $certExpirationDateValues['month']),
                'yearValue' => old('expiration_date_year', $certExpirationDateValues['year']),
                'yearOptions' => $yearOptions,
                'errorKey' => 'expiration_date'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Credential URL (Optional)
                </label>
                <input type="url" name="credential_url" value="{{ old('credential_url') }}"
                    placeholder="https://credly.com/badges/..."
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
            </div>

            <button type="submit" class="primary-btn1 btn-hover"
                style="padding: 10px 20px; border: none; cursor: pointer;">
                <i class="bi bi-plus-circle"></i> Add Certification
            </button>
        </form>
    </div>

    <!-- Volunteer Experiences Section -->
    <div
        style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2
            style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
            Volunteer Experiences
        </h2>

        <!-- Existing Volunteer Experiences -->
        @if($profile->volunteerExperiences->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px;">Your Volunteer Experiences ({{ $profile->volunteerExperiences->count() }})</h3>
            @foreach($profile->volunteerExperiences as $volunteer)
            <div
                style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>{{ $volunteer->organization }}</strong><br>
                    <span style="color: #666; font-size: 14px;">
                        <i class="bi bi-calendar3"></i>
                        {{ $volunteer->start_date?->format('M Y') }}
                        @if($volunteer->is_current)
                        - Present
                        @elseif($volunteer->end_date)
                        - {{ $volunteer->end_date->format('M Y') }}
                        @endif
                    </span>
                </div>
                <form method="POST" action="{{ route('talent.profile.volunteer-experience.remove', ['id' => $volunteer->id]) }}"
                    style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to remove this volunteer experience?')"
                        style="background: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                        Remove
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Add Volunteer Experience Form -->
        <form method="POST" action="{{ route('talent.profile.volunteer-experience.add') }}">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px;">{{ $profile->volunteerExperiences->count() > 0 ? 'Add Another Volunteer Experience' : 'Add Volunteer Experience' }}</h3>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Organization <span style="color: #F53003;">*</span>
                </label>
                <input type="text" name="organization" value="{{ old('organization') }}" required
                    placeholder="Organization Name (ex: Teach for America)"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
            </div>

            @php
            $volunteerStartDateValues = getDateComponentValues('volunteer_start_date', old('volunteer_start_date'));
            $volunteerEndDateValues = getDateComponentValues('volunteer_end_date', old('volunteer_end_date'));
            $currentYear = (int) date('Y');
            $yearOptions = getYearOptions($currentYear - 50, $currentYear + 10);
            @endphp

            <div style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'Start Date',
                'required' => true,
                'prefix' => 'start_date',
                'dayValue' => old('start_date_day', $volunteerStartDateValues['day']),
                'monthValue' => old('start_date_month', $volunteerStartDateValues['month']),
                'yearValue' => old('start_date_year', $volunteerStartDateValues['year']),
                'yearOptions' => $yearOptions,
                'errorKey' => 'start_date'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                <input type="hidden" name="is_current" value="0">
                <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                    <input type="checkbox" name="is_current" value="1" {{ old('is_current') ? 'checked' : '' }}
                        onchange="toggleVolunteerEndDate(this)">
                    <span style="font-weight: 500;">Currently volunteering here</span>
                </label>
            </div>

            <div id="volunteerEndDateContainer" style="margin-bottom: 20px; {{ old('is_current') ? 'display: none;' : '' }}">
                @include('pages.profile.partials.date-selector', [
                'label' => 'End Date',
                'required' => false,
                'prefix' => 'end_date',
                'dayValue' => old('end_date_day', $volunteerEndDateValues['day']),
                'monthValue' => old('end_date_month', $volunteerEndDateValues['month']),
                'yearValue' => old('end_date_year', $volunteerEndDateValues['year']),
                'yearOptions' => $yearOptions,
                'errorKey' => 'end_date'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Details
                </label>
                <textarea name="details" rows="3" value="{{ old('details') }}"
                    placeholder="Describe your volunteer work..."
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;">{{ old('details') }}</textarea>
            </div>

            <button type="submit" class="primary-btn1 btn-hover"
                style="padding: 10px 20px; border: none; cursor: pointer;">
                <i class="bi bi-plus-circle"></i> Add Volunteer Experience
            </button>
        </form>
    </div>

    <!-- Leadership Experiences Section -->
    <div
        style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2
            style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
            Leadership Experiences
        </h2>

        <!-- Existing Leadership Experiences -->
        @if($profile->leadershipExperiences->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px;">Your Leadership Experiences ({{ $profile->leadershipExperiences->count() }})</h3>
            @foreach($profile->leadershipExperiences as $leadership)
            <div
                style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>{{ $leadership->organization }}</strong>
                    @if($leadership->title)
                    <br><span style="color: #666; font-size: 14px;">{{ $leadership->title }}</span>
                    @endif
                    <br><span style="color: #666; font-size: 14px;">
                        <i class="bi bi-calendar3"></i>
                        {{ $leadership->start_date?->format('M Y') }}
                        @if($leadership->is_current)
                        - Present
                        @elseif($leadership->end_date)
                        - {{ $leadership->end_date->format('M Y') }}
                        @endif
                    </span>
                </div>
                <form method="POST" action="{{ route('talent.profile.leadership-experience.remove', ['id' => $leadership->id]) }}"
                    style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to remove this leadership experience?')"
                        style="background: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                        Remove
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Add Leadership Experience Form -->
        <form method="POST" action="{{ route('talent.profile.leadership-experience.add') }}">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px;">{{ $profile->leadershipExperiences->count() > 0 ? 'Add Another Leadership Experience' : 'Add Leadership Experience' }}</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Organization <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="organization" value="{{ old('organization') }}" required
                        placeholder="Organization Name"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Title
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        placeholder="Your leadership title"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
            </div>

            @php
            $leadershipStartDateValues = getDateComponentValues('leadership_start_date', old('leadership_start_date'));
            $leadershipEndDateValues = getDateComponentValues('leadership_end_date', old('leadership_end_date'));
            $currentYear = (int) date('Y');
            $yearOptions = getYearOptions($currentYear - 50, $currentYear + 10);
            @endphp

            <div style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'Start Date',
                'required' => true,
                'prefix' => 'start_date',
                'dayValue' => old('start_date_day', $leadershipStartDateValues['day']),
                'monthValue' => old('start_date_month', $leadershipStartDateValues['month']),
                'yearValue' => old('start_date_year', $leadershipStartDateValues['year']),
                'yearOptions' => $yearOptions,
                'errorKey' => 'start_date'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                <input type="hidden" name="is_current" value="0">
                <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                    <input type="checkbox" name="is_current" value="1" {{ old('is_current') ? 'checked' : '' }}
                        onchange="toggleLeadershipEndDate(this)">
                    <span style="font-weight: 500;">Currently in this role</span>
                </label>
            </div>

            <div id="leadershipEndDateContainer" style="margin-bottom: 20px; {{ old('is_current') ? 'display: none;' : '' }}">
                @include('pages.profile.partials.date-selector', [
                'label' => 'End Date',
                'required' => false,
                'prefix' => 'end_date',
                'dayValue' => old('end_date_day', $leadershipEndDateValues['day']),
                'monthValue' => old('end_date_month', $leadershipEndDateValues['month']),
                'yearValue' => old('end_date_year', $leadershipEndDateValues['year']),
                'yearOptions' => $yearOptions,
                'errorKey' => 'end_date'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Details
                </label>
                <textarea name="details" rows="3" value="{{ old('details') }}"
                    placeholder="Describe your leadership role and achievements..."
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;">{{ old('details') }}</textarea>
            </div>

            <button type="submit" class="primary-btn1 btn-hover"
                style="padding: 10px 20px; border: none; cursor: pointer;">
                <i class="bi bi-plus-circle"></i> Add Leadership Experience
            </button>
        </form>
    </div>

    <!-- Gigs / Freelance Section -->
    <div
        style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2
            style="font-size: 20px; font-weight: 600; color: var(--title-color); margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
            Gigs / Freelance
        </h2>

        <!-- Existing Gigs/Freelance -->
        @if($profile->gigsFreelance->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px;">Your Gigs/Freelance Work ({{ $profile->gigsFreelance->count() }})</h3>
            @foreach($profile->gigsFreelance as $gig)
            <div
                style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>{{ $gig->company }}</strong>
                    @if($gig->title)
                    <br><span style="color: #666; font-size: 14px;">{{ $gig->title }}</span>
                    @endif
                    <br><span style="color: #666; font-size: 14px;">
                        <i class="bi bi-calendar3"></i>
                        {{ $gig->start_date?->format('M Y') }}
                        @if($gig->is_current)
                        - Present
                        @elseif($gig->end_date)
                        - {{ $gig->end_date->format('M Y') }}
                        @endif
                    </span>
                </div>
                <form method="POST" action="{{ route('talent.profile.gigs-freelance.remove', ['id' => $gig->id]) }}"
                    style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to remove this gig/freelance work?')"
                        style="background: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                        Remove
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Add Gigs/Freelance Form -->
        <form method="POST" action="{{ route('talent.profile.gigs-freelance.add') }}">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px;">{{ $profile->gigsFreelance->count() > 0 ? 'Add Another Gig/Freelance Work' : 'Add Gigs/Freelance Work' }}</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Company <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="company" value="{{ old('company') }}" required
                        placeholder="Company Name (ex: Google)"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Title
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        placeholder="Your role/title"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>
            </div>

            @php
            $gigsStartDateValues = getDateComponentValues('gigs_start_date', old('gigs_start_date'));
            $gigsEndDateValues = getDateComponentValues('gigs_end_date', old('gigs_end_date'));
            $currentYear = (int) date('Y');
            $yearOptions = getYearOptions($currentYear - 50, $currentYear + 10);
            @endphp

            <div style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'Start Date',
                'required' => true,
                'prefix' => 'start_date',
                'dayValue' => old('start_date_day', $gigsStartDateValues['day']),
                'monthValue' => old('start_date_month', $gigsStartDateValues['month']),
                'yearValue' => old('start_date_year', $gigsStartDateValues['year']),
                'yearOptions' => $yearOptions,
                'errorKey' => 'start_date'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                <input type="hidden" name="is_current" value="0">
                <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                    <input type="checkbox" name="is_current" value="1" {{ old('is_current') ? 'checked' : '' }}
                        onchange="toggleGigsEndDate(this)">
                    <span style="font-weight: 500;">Currently working on this</span>
                </label>
            </div>

            <div id="gigsEndDateContainer" style="margin-bottom: 20px; {{ old('is_current') ? 'display: none;' : '' }}">
                @include('pages.profile.partials.date-selector', [
                'label' => 'End Date',
                'required' => false,
                'prefix' => 'end_date',
                'dayValue' => old('end_date_day', $gigsEndDateValues['day']),
                'monthValue' => old('end_date_month', $gigsEndDateValues['month']),
                'yearValue' => old('end_date_year', $gigsEndDateValues['year']),
                'yearOptions' => $yearOptions,
                'errorKey' => 'end_date'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                    Details
                </label>
                <textarea name="details" rows="3" value="{{ old('details') }}"
                    placeholder="Describe your gig/freelance work..."
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;">{{ old('details') }}</textarea>
            </div>

            <button type="submit" class="primary-btn1 btn-hover"
                style="padding: 10px 20px; border: none; cursor: pointer;">
                <i class="bi bi-plus-circle"></i> Add Gigs/Freelance Work
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function uploadPhoto(input) {
        if (input.files && input.files[0]) {
            const formData = new FormData();
            formData.append('photo', input.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("talent.profile.photo.upload") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        throw { data, status: response.status };
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    let errorMessage = data.error || 'Failed to upload photo';
                    if (data.errors && data.errors.photo) {
                        errorMessage = Array.isArray(data.errors.photo) 
                            ? data.errors.photo.join(', ') 
                            : data.errors.photo;
                    }
                    alert(errorMessage);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMessage = 'Failed to upload photo';
                if (error.data) {
                    if (error.data.errors && error.data.errors.photo) {
                        errorMessage = Array.isArray(error.data.errors.photo) 
                            ? error.data.errors.photo.join(', ') 
                            : error.data.errors.photo;
                    } else if (error.data.error) {
                        errorMessage = error.data.error;
                    }
                }
                alert(errorMessage);
            });
        }
    }

    function uploadResume(input) {
        if (input.files && input.files[0]) {
            const formData = new FormData();
            formData.append('resume', input.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("talent.profile.resume.upload") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Failed to upload resume');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to upload resume');
            });
        }
    }

    function toggleEndDate(checkbox) {
        const endDateContainer = document.getElementById('endDateContainer');
        if (checkbox.checked) {
            endDateContainer.style.display = 'none';
            // Clear end date fields
            const endDateInputs = endDateContainer.querySelectorAll('select');
            endDateInputs.forEach(input => input.value = '');
        } else {
            endDateContainer.style.display = 'block';
        }
    }

    function toggleWorkEndDate(checkbox) {
        const endDateContainer = document.getElementById('workEndDateContainer');
        if (checkbox.checked) {
            endDateContainer.style.display = 'none';
            // Clear end date fields
            const endDateInputs = endDateContainer.querySelectorAll('select');
            endDateInputs.forEach(input => input.value = '');
        } else {
            endDateContainer.style.display = 'block';
        }
    }

    function toggleVolunteerEndDate(checkbox) {
        const endDateContainer = document.getElementById('volunteerEndDateContainer');
        if (checkbox.checked) {
            endDateContainer.style.display = 'none';
            const endDateInputs = endDateContainer.querySelectorAll('select');
            endDateInputs.forEach(input => input.value = '');
        } else {
            endDateContainer.style.display = 'block';
        }
    }

    function toggleLeadershipEndDate(checkbox) {
        const endDateContainer = document.getElementById('leadershipEndDateContainer');
        if (checkbox.checked) {
            endDateContainer.style.display = 'none';
            const endDateInputs = endDateContainer.querySelectorAll('select');
            endDateInputs.forEach(input => input.value = '');
        } else {
            endDateContainer.style.display = 'block';
        }
    }

    function toggleGigsEndDate(checkbox) {
        const endDateContainer = document.getElementById('gigsEndDateContainer');
        if (checkbox.checked) {
            endDateContainer.style.display = 'none';
            const endDateInputs = endDateContainer.querySelectorAll('select');
            endDateInputs.forEach(input => input.value = '');
        } else {
            endDateContainer.style.display = 'block';
        }
    }
</script>
@endpush
@endsection
