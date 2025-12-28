@extends('layouts.dashboard.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="mb-1">University Onboarding</h2>
                    <p class="text-muted mb-0">Complete your institution and career services admin details.</p>
                </div>
                <div class="text-end">
                    <div class="fw-semibold">Completion: {{ $wizardProgress['completeness_score'] ?? 0 }}%</div>
                    <div class="small text-muted">Step {{ $wizardProgress['current_step'] ?? 1 }} of 4</div>
                </div>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Fix the errors below and try again.</strong>
            </div>
            @endif

            <form method="POST" action="{{ route('university.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card mb-3">
                    <div class="card-header">
                        <strong>1) Choose your institution</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Select institution (recommended)</label>
                            <select name="institution_id" class="form-select">
                                <option value="">-- Select an institution --</option>
                                @foreach($institutions as $inst)
                                <option value="{{ $inst->id }}" {{ old('institution_id', $admin->institution_id) === $inst->id ? 'selected' : '' }}>
                                    {{ $inst->name }}
                                </option>
                                @endforeach
                            </select>
                            <div class="form-text">If you can’t find it, create a new one below.</div>
                            @error('institution_id')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Or create a new institution</label>
                            <input type="text" class="form-control" name="institution_name" value="{{ old('institution_name') }}" placeholder="e.g., University of Ghana">
                            @error('institution_name')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <strong>2) Institution details</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Institution type</label>
                                <select name="institution_type" class="form-select">
                                    <option value="">-- Select type --</option>
                                    @foreach($institutionTypes as $type)
                                    <option value="{{ $type->value }}" {{ old('institution_type', $institution?->type?->value) === $type->value ? 'selected' : '' }}>
                                        {{ ucfirst($type->value) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location (free text)</label>
                                <input type="text" class="form-control" name="institution_location" value="{{ old('institution_location', $institution?->location) }}" placeholder="e.g., Legon, Accra">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Website</label>
                                <input type="url" class="form-control" name="institution_website" value="{{ old('institution_website', $institution?->website) }}" placeholder="https://">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Student email domain</label>
                                <input type="text" class="form-control" name="student_email_domain" value="{{ old('student_email_domain', $institution?->student_email_domain) }}" placeholder="e.g., st.ug.edu.gh">
                                <div class="form-text">Used for student email verification.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="institution_city" value="{{ old('institution_city', $institution?->city) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State/Region</label>
                                <input type="text" class="form-control" name="institution_state" value="{{ old('institution_state', $institution?->state) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Country</label>
                                <input type="text" class="form-control" name="institution_country" value="{{ old('institution_country', $institution?->country ?? 'Ghana') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Institution email</label>
                                <input type="email" class="form-control" name="institution_email" value="{{ old('institution_email', $institution?->email) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Institution phone</label>
                                <input type="text" class="form-control" name="institution_phone" value="{{ old('institution_phone', $institution?->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Institution logo</label>
                                <input type="file" class="form-control" name="institution_logo" accept=".jpg,.jpeg,.png">
                                @error('institution_logo')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                @if($institution?->logo)
                                <div class="form-text">Current logo is set.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <strong>3) Career services admin details</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Your name</label>
                                <input type="text" class="form-control" name="admin_name" value="{{ old('admin_name', $admin->name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Your role/title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="admin_role" value="{{ old('admin_role', $admin->role) }}" placeholder="e.g., Career Services Officer">
                                @error('admin_role')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact email</label>
                                <input type="email" class="form-control" name="admin_email" value="{{ old('admin_email', $admin->email ?? Auth::user()->email) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact phone</label>
                                <input type="text" class="form-control" name="admin_phone" value="{{ old('admin_phone', $admin->phone ?? Auth::user()->phone_number) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <strong>4) Partnership tier</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Choose a tier (can be updated later)</label>
                            <select name="partnership_tier" class="form-select">
                                <option value="">-- Select tier --</option>
                                @foreach($partnershipTiers as $tier)
                                <option value="{{ $tier->value }}" {{ old('partnership_tier', $institution?->partnership_tier?->value) === $tier->value ? 'selected' : '' }}>
                                    {{ ucfirst($tier->value) }}
                                </option>
                                @endforeach
                            </select>
                            <div class="form-text">Partner features may require admin verification.</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save & Continue</button>
                    <a href="{{ route('university.profile.show') }}" class="btn btn-outline-secondary">Back to profile</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

