<form class="company-section-edit-form branding-section" data-section="branding"
    action="{{ route('employer.company.branding.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Logo Upload -->
    <div class="mb-4">
        <label class="form-label">Company Logo</label>
        <div class="logo-upload-area">
            @if($company->logo_url)
            <div class="current-logo mb-3">
                <img src="{{ asset('storage/'.$company->logo_url) }}" alt="Company Logo"
                    style="max-width: 200px; max-height: 200px; border-radius: 8px;">
            </div>
            @endif
            <input type="file" id="logo_upload" name="logo" class="form-control" accept=".jpg,.jpeg,.png">
            <small class="form-text text-muted">Accepted formats: JPG, PNG (Max: 5MB). Recommended size:
                400x400px</small>
            <div class="upload-progress mt-2" style="display: none;">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Description -->
    <div class="mb-4">
        <label class="form-label">Company Description</label>
        <textarea name="company_description" class="form-control" rows="5"
            placeholder="Tell talent about your company culture, values, and what makes you unique...">{{ old('company_description', $company->company_description) }}</textarea>
        <small class="form-text text-muted">This will be displayed on your public company page</small>
    </div>

    <!-- Year Established -->
    <div class="mb-4">
        <label class="form-label">Year Established</label>
        <input type="number" name="year_established" class="form-control"
            value="{{ old('year_established', $company->year_established) }}" placeholder="e.g. 2010" min="1900"
            max="{{ date('Y') }}">
    </div>

    <!-- Video Upload -->
    <div class="mb-4">
        <label class="form-label">Company Video (Max 90 seconds)</label>
        <div class="video-upload-area">
            @if($company->video_url)
            <div class="current-video mb-3">
                <video controls style="max-width: 100%; border-radius: 8px;"
                    src="{{ asset('storage/'.$company->video_url) }}"></video>
            </div>
            @endif
            <input type="file" id="video_upload" class="form-control" accept=".mp4,.mov,.avi" data-type="video">
            <small class="form-text text-muted">Accepted formats: MP4, MOV, AVI (Max: 50MB, Max duration: 90
                seconds)</small>
            <div class="upload-progress mt-2" style="display: none;">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Gallery -->
    <div class="mb-4">
        <label class="form-label">Company Photos</label>
        <div class="photo-gallery">
            <div class="row g-3 mb-3" id="photo-gallery-grid">
                @foreach($company->photos as $photo)
                <div class="col-md-3 photo-item" data-photo-id="{{ $photo->id }}">
                    <div class="card">
                        <img src="{{ asset('storage/'.$photo->photo_url) }}" class="card-img-top" alt="Company Photo"
                            style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2">
                            @if($photo->caption)
                            <p class="card-text small mb-1">{{ Str::limit($photo->caption, 30) }}</p>
                            @endif
                            <button type="button" class="btn btn-sm btn-danger delete-photo-btn"
                                data-photo-id="{{ $photo->id }}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <input type="file" id="photo_upload" name="photos[]" class="form-control" accept=".jpg,.jpeg,.png"
                data-type="photo" multiple>
            <small class="form-text text-muted">Accepted formats: JPG, PNG (Max: 10MB per photo). You can upload
                multiple photos.</small>
            <div class="upload-progress mt-2" style="display: none;">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials -->
    <div class="mb-4">
        <label class="form-label">Employee Testimonials</label>
        <div class="testimonials-section">
            @foreach($company->testimonials as $testimonial)
            <div class="card mb-3 testimonial-item" data-testimonial-id="{{ $testimonial->id }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1">{{ $testimonial->employee_name }}</h6>
                            @if($testimonial->employee_title)
                            <p class="text-muted small mb-0">{{ $testimonial->employee_title }}</p>
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-danger delete-testimonial-btn"
                            data-testimonial-id="{{ $testimonial->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <p class="mb-0">{{ $testimonial->testimonial }}</p>
                    @if($testimonial->is_featured)
                    <span class="badge bg-primary mt-2">Featured</span>
                    @endif
                </div>
            </div>
            @endforeach

            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                data-bs-target="#addTestimonialModal">
                <i class="bi bi-plus-circle"></i> Add Testimonial
            </button>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2 justify-content-end">
        <button type="button" class="btn btn-outline-secondary btn-cancel-edit">Cancel</button>
        <button type="submit" class="primary-btn1">
            Save Changes
            <span></span>
        </button>
    </div>
</form>

<!-- Add Testimonial Modal -->
<div class="modal fade" id="addTestimonialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Employee Testimonial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="testimonial-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Employee Name <span class="text-danger">*</span></label>
                        <input type="text" name="employee_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Employee Title</label>
                        <input type="text" name="employee_title" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Testimonial <span class="text-danger">*</span></label>
                        <textarea name="testimonial" class="form-control" rows="4" required maxlength="2000"></textarea>
                        <small class="form-text text-muted">Maximum 2000 characters</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Employee Photo (Optional)</label>
                        <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png">
                        <small class="form-text text-muted">Accepted formats: JPG, PNG (Max: 5MB)</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured">
                            <label class="form-check-label" for="is_featured">Feature this testimonial</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="primary-btn1">Add Testimonial</button>
                </div>
            </form>
        </div>
    </div>
</div>
