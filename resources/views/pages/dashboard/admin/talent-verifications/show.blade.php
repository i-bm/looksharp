@extends('layouts.admin.main')

@php
    /** @var \App\Models\TalentProfile $profile */
@endphp

@section('content')
<div class="dashboard-container">
    <div class="dashboard-page-header">
        <div>
            <h1 class="dashboard-page-title">{{ $profile->full_name }}</h1>
            <p class="dashboard-page-subtitle">Verification Status: <strong>{{ ucfirst($profile->verification_status ?? 'pending') }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.talent-verifications.index') }}" class="btn btn-outline-secondary">Back</a>
            @if($profile->user)
            <a href="{{ route('talent.profile.public', ['slug' => $profile->public_url]) }}" class="btn btn-outline-primary" target="_blank" rel="noopener">View Profile</a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="dashboard-card">
        <h3 class="h5 mb-3">Talent Information</h3>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted small">Profile ID</div>
                <div class="fw-semibold">{{ $profile->id }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Email</div>
                <div class="fw-semibold">{{ $profile->user->email ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Full Name</div>
                <div class="fw-semibold">{{ $profile->full_name }}</div>
            </div>
            @if($profile->student_id)
            <div class="col-md-6">
                <div class="text-muted small">Student ID</div>
                <div class="fw-semibold d-flex align-items-center gap-2">
                    <span class="sensitive-data-masked" data-value="{{ $profile->student_id }}" data-type="student-id">
                        <span class="masked-text">Click to reveal</span>
                        <span class="revealed-text" style="display: none;">{{ $profile->student_id }}</span>
                    </span>
                    <button type="button" class="btn btn-sm btn-link p-0 sensitive-data-toggle" data-target="student-id" style="min-width: auto; text-decoration: none;" title="Toggle visibility">
                        <i class="bi bi-eye" data-icon="eye"></i>
                        <i class="bi bi-eye-slash" data-icon="eye-slash" style="display: none;"></i>
                    </button>
                </div>
            </div>
            @endif
            <div class="col-md-6">
                <div class="text-muted small">Phone Number</div>
                @if($profile->phone_number)
                <div class="fw-semibold d-flex align-items-center gap-2">
                    <span class="sensitive-data-masked" data-value="{{ $profile->phone_number }}" data-type="phone-number">
                        <span class="masked-text">Click to reveal</span>
                        <span class="revealed-text" style="display: none;">{{ $profile->phone_number }}</span>
                    </span>
                    <button type="button" class="btn btn-sm btn-link p-0 sensitive-data-toggle" data-target="phone-number" style="min-width: auto; text-decoration: none;" title="Toggle visibility">
                        <i class="bi bi-eye" data-icon="eye"></i>
                        <i class="bi bi-eye-slash" data-icon="eye-slash" style="display: none;"></i>
                    </button>
                </div>
                @else
                <div class="fw-semibold">—</div>
                @endif
            </div>
            @if($profile->student_email)
            <div class="col-md-6">
                <div class="text-muted small">Student Email</div>
                <div class="fw-semibold d-flex align-items-center gap-2">
                    <span class="sensitive-data-masked" data-value="{{ $profile->student_email }}" data-type="student-email-info">
                        <span class="masked-text">Click to reveal</span>
                        <span class="revealed-text" style="display: none;">{{ $profile->student_email }}</span>
                    </span>
                    <button type="button" class="btn btn-sm btn-link p-0 sensitive-data-toggle" data-target="student-email-info" style="min-width: auto; text-decoration: none;" title="Toggle visibility">
                        <i class="bi bi-eye" data-icon="eye"></i>
                        <i class="bi bi-eye-slash" data-icon="eye-slash" style="display: none;"></i>
                    </button>
                </div>
            </div>
            @endif
            @if($profile->location)
            <div class="col-md-6">
                <div class="text-muted small">Location</div>
                <div class="fw-semibold">{{ $profile->location }}</div>
            </div>
            @endif
            @if($profile->currentEducation && $profile->currentEducation->institution)
            <div class="col-md-6">
                <div class="text-muted small">Current Education</div>
                <div class="fw-semibold">{{ $profile->currentEducation->institution->name }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="dashboard-card mt-3">
        <h3 class="h5 mb-3">Verification Status</h3>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted small">Verification Status</div>
                <div class="fw-semibold">
                    @if($profile->verification_status === 'verified')
                        <span class="badge bg-success">Verified</span>
                    @elseif($profile->verification_status === 'rejected')
                        <span class="badge bg-danger">Rejected</span>
                    @else
                        <span class="badge bg-warning">Pending</span>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Document Types</div>
                <div class="fw-semibold">
                    @php
                        $hasStudentDoc = !empty($profile->student_verification_document_url);
                        $hasIdentityDoc = !empty($profile->verification_document_url);
                        $documents = [];
                        if ($hasStudentDoc) {
                            $documents[] = ['type' => 'student_id', 'label' => 'Student ID', 'badge' => 'bg-info'];
                        }
                        if ($hasIdentityDoc && $profile->verification_type) {
                            if ($profile->verification_type === 'ghana_card') {
                                $documents[] = ['type' => 'ghana_card', 'label' => 'Ghana Card', 'badge' => 'bg-primary'];
                            } elseif ($profile->verification_type === 'passport') {
                                $documents[] = ['type' => 'passport', 'label' => 'Passport', 'badge' => 'bg-secondary'];
                            } elseif ($profile->verification_type === 'student_id') {
                                $documents[] = ['type' => 'student_id', 'label' => 'Student ID', 'badge' => 'bg-info'];
                            } else {
                                $documents[] = ['type' => $profile->verification_type, 'label' => ucfirst(str_replace('_', ' ', $profile->verification_type)), 'badge' => 'bg-secondary'];
                            }
                        }
                    @endphp
                    @if(count($documents) > 0)
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($documents as $doc)
                                <span class="badge {{ $doc['badge'] }}">{{ $doc['label'] }}</span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>
            </div>
            @if($profile->verification_verified_at)
            <div class="col-md-6">
                <div class="text-muted small">Verified at</div>
                <div class="fw-semibold">{{ $profile->verification_verified_at->format('Y-m-d H:i') }}</div>
            </div>
            @endif
            @if($profile->verifier)
            <div class="col-md-6">
                <div class="text-muted small">Verified by</div>
                <div class="fw-semibold">{{ $profile->verifier->email }}</div>
            </div>
            @endif
        </div>

        <hr>
        <div>
            <h6 class="mb-3">Verification Documents</h6>

            <!-- Student ID Document -->
            <div class="mb-3">
                <div class="text-muted small mb-2">
                    <i class="bi bi-card-text"></i> Student ID Document
                </div>
                @if($profile->student_id)
                <div class="mb-2">
                    <div class="text-muted small">Student ID Number</div>
                    <div class="fw-semibold d-flex align-items-center gap-2">
                        <span class="sensitive-data-masked" data-value="{{ $profile->student_id }}" data-type="student-verification-id">
                            <span class="masked-text">Click to reveal</span>
                            <span class="revealed-text" style="display: none;">{{ $profile->student_id }}</span>
                        </span>
                        <button type="button" class="btn btn-sm btn-link p-0 sensitive-data-toggle" data-target="student-verification-id" style="min-width: auto; text-decoration: none;" title="Toggle visibility">
                            <i class="bi bi-eye" data-icon="eye"></i>
                            <i class="bi bi-eye-slash" data-icon="eye-slash" style="display: none;"></i>
                        </button>
                    </div>
                </div>
                @endif
                @if($profile->student_email)
                <div class="mb-2">
                    <div class="text-muted small">Student Email</div>
                    <div class="fw-semibold d-flex align-items-center gap-2">
                        <span class="sensitive-data-masked" data-value="{{ $profile->student_email }}" data-type="student-email">
                            <span class="masked-text">Click to reveal</span>
                            <span class="revealed-text" style="display: none;">{{ $profile->student_email }}</span>
                        </span>
                        <button type="button" class="btn btn-sm btn-link p-0 sensitive-data-toggle" data-target="student-email" style="min-width: auto; text-decoration: none;" title="Toggle visibility">
                            <i class="bi bi-eye" data-icon="eye"></i>
                            <i class="bi bi-eye-slash" data-icon="eye-slash" style="display: none;"></i>
                        </button>
                    </div>
                </div>
                @endif
                @if($profile->student_verification_document_url)
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#studentDocumentModal">
                        <i class="bi bi-eye"></i> View Document
                    </button>
                    <a href="{{ route('admin.talent-verifications.document', ['id' => $profile->id, 'type' => 'student']) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-box-arrow-up-right"></i> Open in New Tab
                    </a>
                    <a href="{{ route('admin.talent-verifications.document', ['id' => $profile->id, 'type' => 'student']) }}" download class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-download"></i> Download
                    </a>
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle"></i> Available
                    </span>
                </div>
                @else
                <div>
                    <span class="text-muted">No student ID document uploaded</span>
                    <span class="badge bg-warning ms-2">
                        <i class="bi bi-exclamation-triangle"></i> Missing
                    </span>
                </div>
                @endif
            </div>

            <!-- Identity Document (Ghana Card/Passport) -->
            @if($profile->verification_type)
            <div>
                <div class="text-muted small mb-2">
                    <i class="bi bi-person-badge"></i> Identity Document
                    @if($profile->verification_type === 'student_id')
                        (Student ID)
                    @elseif($profile->verification_type === 'ghana_card')
                        (Ghana Card)
                    @elseif($profile->verification_type === 'passport')
                        (Passport)
                    @endif
                </div>
                @if($profile->identity_document_number)
                <div class="mb-2">
                    <div class="text-muted small">
                        @if($profile->verification_type === 'student_id')
                            Student ID Number
                        @elseif($profile->verification_type === 'ghana_card')
                            Ghana Card Number
                        @elseif($profile->verification_type === 'passport')
                            Passport Number
                        @else
                            ID Number
                        @endif
                    </div>
                    <div class="fw-semibold d-flex align-items-center gap-2">
                        <span class="sensitive-data-masked" data-value="{{ $profile->identity_document_number }}" data-type="identity-verification-id">
                            <span class="masked-text">Click to reveal</span>
                            <span class="revealed-text" style="display: none;">{{ $profile->identity_document_number }}</span>
                        </span>
                        <button type="button" class="btn btn-sm btn-link p-0 sensitive-data-toggle" data-target="identity-verification-id" style="min-width: auto; text-decoration: none;" title="Toggle visibility">
                            <i class="bi bi-eye" data-icon="eye"></i>
                            <i class="bi bi-eye-slash" data-icon="eye-slash" style="display: none;"></i>
                        </button>
                    </div>
                </div>
                @endif
                @if($profile->verification_document_url)
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#identityDocumentModal">
                        <i class="bi bi-eye"></i> View Document
                    </button>
                    <a href="{{ route('admin.talent-verifications.document', ['id' => $profile->id, 'type' => 'identity']) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-box-arrow-up-right"></i> Open in New Tab
                    </a>
                    <a href="{{ route('admin.talent-verifications.document', ['id' => $profile->id, 'type' => 'identity']) }}" download class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-download"></i> Download
                    </a>
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle"></i> Available
                    </span>
                </div>
                @else
                <div>
                    <span class="text-muted">No identity document uploaded</span>
                    <span class="badge bg-warning ms-2">
                        <i class="bi bi-exclamation-triangle"></i> Missing
                    </span>
                </div>
                @endif
            </div>
            @endif
        </div>

        @if($profile->verification_status !== 'verified')
        <hr>
        <div>
            <h6 class="mb-2">Verification Actions</h6>
            <div class="row g-3">
                @if($profile->verification_status === 'pending')
                <div class="col-lg-6">
                    <form method="POST" action="{{ route('admin.talent-verifications.verify', ['id' => $profile->id]) }}">
                        @csrf
                        <label class="form-label">Verify Talent (optional notes)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional verification notes">{{ old('notes') }}</textarea>
                        <button type="submit" class="btn btn-success mt-2">Verify Talent</button>
                    </form>
                </div>
                @endif

                <div class="col-lg-6">
                    <form method="POST" action="{{ route('admin.talent-verifications.reject', ['id' => $profile->id]) }}">
                        @csrf
                        <label class="form-label">Reject Verification (reason required)</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="Reason for rejection" required>{{ old('reason') }}</textarea>
                        <button type="submit" class="btn btn-danger mt-2">Reject Verification</button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Student ID Document Modal -->
@if($profile->student_verification_document_url)
<div class="modal fade" id="studentDocumentModal" tabindex="-1" aria-labelledby="studentDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentDocumentModalLabel">
                    <i class="bi bi-card-text"></i> Student ID Document
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 400px; max-height: 80vh; overflow: auto;">
                <div id="studentDocumentViewerContainer" class="d-flex align-items-center justify-content-center" style="min-height: 400px;">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading document...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('admin.talent-verifications.document', ['id' => $profile->id, 'type' => 'student']) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="bi bi-box-arrow-up-right"></i> Open in New Tab
                </a>
                <a href="{{ route('admin.talent-verifications.document', ['id' => $profile->id, 'type' => 'student']) }}" download class="btn btn-outline-secondary">
                    <i class="bi bi-download"></i> Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Identity Document Modal -->
@if($profile->verification_document_url)
<div class="modal fade" id="identityDocumentModal" tabindex="-1" aria-labelledby="identityDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="identityDocumentModalLabel">
                    <i class="bi bi-person-badge"></i> Identity Document
                    @if($profile->verification_type === 'ghana_card')
                        - Ghana Card
                    @elseif($profile->verification_type === 'passport')
                        - Passport
                    @elseif($profile->verification_type === 'student_id')
                        - Student ID Card
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 400px; max-height: 80vh; overflow: auto;">
                <div id="identityDocumentViewerContainer" class="d-flex align-items-center justify-content-center" style="min-height: 400px;">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading document...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('admin.talent-verifications.document', ['id' => $profile->id, 'type' => 'identity']) }}" target="_blank" class="btn btn-outline-primary">
                    <i class="bi bi-box-arrow-up-right"></i> Open in New Tab
                </a>
                <a href="{{ route('admin.talent-verifications.document', ['id' => $profile->id, 'type' => 'identity']) }}" download class="btn btn-outline-secondary">
                    <i class="bi bi-download"></i> Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle sensitive data toggle functionality
    const toggleButtons = document.querySelectorAll('.sensitive-data-toggle');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const targetType = this.getAttribute('data-target');
            const container = this.closest('.fw-semibold').querySelector(`[data-type="${targetType}"]`);

            if (!container) return;

            const maskedText = container.querySelector('.masked-text');
            const revealedText = container.querySelector('.revealed-text');
            const eyeIcon = this.querySelector('[data-icon="eye"]');
            const eyeSlashIcon = this.querySelector('[data-icon="eye-slash"]');

            if (maskedText && revealedText) {
                const isCurrentlyMasked = maskedText.style.display !== 'none';

                if (isCurrentlyMasked) {
                    // Reveal
                    maskedText.style.display = 'none';
                    revealedText.style.display = 'inline';
                    if (eyeIcon) eyeIcon.style.display = 'none';
                    if (eyeSlashIcon) eyeSlashIcon.style.display = 'inline';
                } else {
                    // Mask
                    maskedText.style.display = 'inline';
                    revealedText.style.display = 'none';
                    if (eyeIcon) eyeIcon.style.display = 'inline';
                    if (eyeSlashIcon) eyeSlashIcon.style.display = 'none';
                }
            }
        });
    });

    // Helper function to load document in modal
    function setupDocumentModal(modalId, containerId, documentUrl) {
        const documentModal = document.getElementById(modalId);
        if (!documentModal) return;

        const container = document.getElementById(containerId);
        if (!container) return;

        documentModal.addEventListener('show.bs.modal', function() {
            // Reset container
            container.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Loading document...</p></div>';

            // Fetch document to determine type
            fetch(documentUrl)
                .then(response => {
                    if (!response.ok) throw new Error('Failed to load document');
                    return response.blob();
                })
                .then(blob => {
                    const fileType = blob.type;
                    const url = URL.createObjectURL(blob);

                    if (fileType.startsWith('image/')) {
                        // Display as image
                        container.innerHTML = `
                            <img src="${url}" alt="Verification Document" class="img-fluid" style="max-width: 100%; max-height: 75vh; object-fit: contain;" />
                        `;
                    } else if (fileType === 'application/pdf') {
                        // Display as PDF using iframe
                        container.innerHTML = `
                            <iframe src="${url}" style="width: 100%; height: 75vh; border: none;" type="application/pdf"></iframe>
                        `;
                    } else {
                        // Fallback: show download link
                        container.innerHTML = `
                            <div class="text-center p-4">
                                <i class="bi bi-file-earmark" style="font-size: 4rem; color: #6c757d;"></i>
                                <p class="mt-3">Document type: ${fileType}</p>
                                <a href="${documentUrl}" target="_blank" class="btn btn-primary mt-2">
                                    <i class="bi bi-box-arrow-up-right"></i> Open Document
                                </a>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading document:', error);
                    container.innerHTML = `
                        <div class="text-center p-4">
                            <i class="bi bi-exclamation-triangle" style="font-size: 4rem; color: #dc3545;"></i>
                            <p class="mt-3 text-danger">Failed to load document</p>
                            <p class="text-muted">${error.message}</p>
                            <a href="${documentUrl}" target="_blank" class="btn btn-outline-primary mt-2">
                                <i class="bi bi-box-arrow-up-right"></i> Try Opening in New Tab
                            </a>
                        </div>
                    `;
                });
        });

        // Clean up object URL when modal is closed
        documentModal.addEventListener('hidden.bs.modal', function() {
            const img = container.querySelector('img');
            const iframe = container.querySelector('iframe');
            if (img && img.src.startsWith('blob:')) {
                URL.revokeObjectURL(img.src);
            }
            if (iframe && iframe.src.startsWith('blob:')) {
                URL.revokeObjectURL(iframe.src);
            }
        });
    }

    // Setup Student ID Document Modal
    @if($profile->student_verification_document_url)
    setupDocumentModal(
        'studentDocumentModal',
        'studentDocumentViewerContainer',
        '{{ route('admin.talent-verifications.document', ['id' => $profile->id, 'type' => 'student']) }}'
    );
    @endif

    // Setup Identity Document Modal
    @if($profile->verification_document_url)
    setupDocumentModal(
        'identityDocumentModal',
        'identityDocumentViewerContainer',
        '{{ route('admin.talent-verifications.document', ['id' => $profile->id, 'type' => 'identity']) }}'
    );
    @endif
});
</script>
@endpush
@endsection

