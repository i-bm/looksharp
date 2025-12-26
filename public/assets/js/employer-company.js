/**
 * Employer Company Profile Page JavaScript
 * Handles collapsible sections, edit mode, and AJAX form submissions
 */

(function() {
    'use strict';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeSectionToggles();
        initializeEditButtons();
        initializeFormSubmissions();
    });

    /**
     * Initialize section toggle (collapse/expand) functionality
     */
    function initializeSectionToggles() {
        const toggleButtons = document.querySelectorAll('.btn-toggle-section');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const section = this.getAttribute('data-section');
                const card = document.querySelector(`.company-section-card[data-section="${section}"]`);
                
                if (card) {
                    card.classList.toggle('collapsed');
                    const icon = this.querySelector('i');
                    if (icon) {
                        if (card.classList.contains('collapsed')) {
                            icon.classList.remove('bi-chevron-down');
                            icon.classList.add('bi-chevron-right');
                        } else {
                            icon.classList.remove('bi-chevron-right');
                            icon.classList.add('bi-chevron-down');
                        }
                    }
                }
            });
        });
    }

    /**
     * Initialize edit button functionality
     */
    function initializeEditButtons() {
        const editButtons = document.querySelectorAll('.btn-edit-section');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const section = this.getAttribute('data-section');
                toggleEditMode(section, true);
            });
        });

        // Cancel edit buttons
        const cancelButtons = document.querySelectorAll('.btn-cancel-edit');
        cancelButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.company-section-edit-form');
                if (form) {
                    const section = form.getAttribute('data-section');
                    toggleEditMode(section, false);
                    resetFormErrors(form);
                }
            });
        });
    }

    /**
     * Toggle edit mode for a section
     */
    function toggleEditMode(section, showEdit) {
        const card = document.querySelector(`.company-section-card[data-section="${section}"]`);
        if (!card) return;

        const viewDiv = card.querySelector('.company-section-view');
        const editDiv = card.querySelector('.company-section-edit');
        const editButton = card.querySelector('.btn-edit-section');

        if (showEdit) {
            // Expand section if collapsed
            card.classList.remove('collapsed');
            const toggleButton = card.querySelector('.btn-toggle-section');
            if (toggleButton) {
                const icon = toggleButton.querySelector('i');
                if (icon) {
                    icon.classList.remove('bi-chevron-right');
                    icon.classList.add('bi-chevron-down');
                }
            }

            // Show edit form, hide view
            if (viewDiv) viewDiv.style.display = 'none';
            if (editDiv) editDiv.style.display = 'block';
            if (editButton) {
                editButton.innerHTML = '<i class="bi bi-x-circle"></i> Cancel';
                editButton.classList.remove('btn-outline-primary');
                editButton.classList.add('btn-outline-secondary');
            }

            // Reinitialize select-search components if they exist
            setTimeout(() => {
                if (typeof initializeDropdown === 'function') {
                    const selectContainers = editDiv.querySelectorAll('.select-search-container');
                    selectContainers.forEach(container => {
                        // Reinitialize if the function exists (from select-dropdown.js)
                        if (window.jQuery && typeof window.jQuery.fn.selectSearch === 'function') {
                            window.jQuery(container).selectSearch();
                        }
                    });
                }
            }, 100);
        } else {
            // Show view, hide edit form
            if (viewDiv) viewDiv.style.display = 'block';
            if (editDiv) editDiv.style.display = 'none';
            if (editButton) {
                editButton.innerHTML = '<i class="bi bi-pencil"></i> Edit';
                editButton.classList.remove('btn-outline-secondary');
                editButton.classList.add('btn-outline-primary');
            }
        }
    }

    /**
     * Initialize AJAX form submissions
     */
    function initializeFormSubmissions() {
        const forms = document.querySelectorAll('.company-section-edit-form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                submitSectionForm(this);
            });
        });
    }

    /**
     * Submit section form via AJAX
     */
    function submitSectionForm(form) {
        const section = form.getAttribute('data-section');
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton ? submitButton.innerHTML : 'Save Changes';

        // Disable submit button
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        }

        // Clear previous errors
        resetFormErrors(form);

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
            || form.querySelector('input[name="_token"]')?.value;

        if (!csrfToken) {
            showError('CSRF token not found. Please refresh the page.');
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
            return;
        }

        // Add CSRF token to form data if not already present
        if (!formData.has('_token')) {
            formData.append('_token', csrfToken);
        }

        // Get the form action URL
        const url = form.getAttribute('action');
        if (!url) {
            showError('Form action URL not found.');
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
            return;
        }

        // Validate file sizes before submission (for registration form)
        if (section === 'registration') {
            const ghanaCardInput = form.querySelector('input[name="ghana_card"]');
            const businessRegInput = form.querySelector('input[name="business_registration"]');
            const maxSize = 10 * 1024 * 1024; // 10MB

            if (ghanaCardInput && ghanaCardInput.files && ghanaCardInput.files[0]) {
                if (ghanaCardInput.files[0].size > maxSize) {
                    showError('Ghana Card file is too large. Maximum size is 10MB. Please select a smaller file.');
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                    }
                    return;
                }
            }

            if (businessRegInput && businessRegInput.files && businessRegInput.files[0]) {
                if (businessRegInput.files[0].size > maxSize) {
                    showError('Business Registration file is too large. Maximum size is 10MB. Please select a smaller file.');
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                    }
                    return;
                }
            }
        }

        // Submit via AJAX
        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            // Check if response is OK before parsing JSON
            if (!response.ok) {
                // Handle 413 specifically
                if (response.status === 413) {
                    return response.text().then(text => {
                        let errorMessage = 'File is too large. Maximum size is 10MB. Please try a smaller file.';
                        try {
                            const jsonData = JSON.parse(text);
                            if (jsonData.error) {
                                errorMessage = jsonData.error;
                            }
                        } catch (e) {
                            // If JSON parsing fails, use default message
                        }
                        throw { status: 413, message: errorMessage };
                    });
                }
                // Handle other HTTP errors
                return response.json().then(data => {
                    throw { status: response.status, data: data };
                }).catch(err => {
                    if (err.status) {
                        throw err;
                    }
                    throw { status: response.status, message: 'An error occurred while updating.' };
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showSuccess(data.message || 'Section updated successfully.');
                
                // Update company data from response if available
                if (data.company && window.companyData) {
                    if (data.company.industry !== undefined) window.companyData.industry = data.company.industry || '';
                    if (data.company.company_size !== undefined) window.companyData.company_size = data.company.company_size || '';
                    if (data.company.city !== undefined) window.companyData.city = data.company.city || '';
                    if (data.company.state_or_region !== undefined) window.companyData.state_or_region = data.company.state_or_region || '';
                    if (data.company.registration_number !== undefined) window.companyData.registration_number = data.company.registration_number || '';
                    if (data.company.primary_contact_name !== undefined) window.companyData.primary_contact_name = data.company.primary_contact_name || '';
                    if (data.company.primary_contact_phone !== undefined) window.companyData.primary_contact_phone = data.company.primary_contact_phone || '';
                }
                
                // Update submit button state after successful form submission
                setTimeout(() => {
                    updateSubmitButtonState();
                }, 100);
                
                // Reload page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                // Handle validation errors
                if (data.errors) {
                    displayFormErrors(form, data.errors);
                } else {
                    showError(data.error || data.message || 'An error occurred while updating.');
                }
                
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMessage = 'An error occurred while updating. Please try again.';
            
            if (error.status === 413) {
                errorMessage = error.message || 'File is too large. Maximum size is 10MB. Please try a smaller file.';
            } else if (error.data && error.data.error) {
                errorMessage = error.data.error;
            } else if (error.message) {
                errorMessage = error.message;
            }
            
            showError(errorMessage);
            
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    }

    /**
     * Display form validation errors
     */
    function displayFormErrors(form, errors) {
        Object.keys(errors).forEach(fieldName => {
            const input = form.querySelector(`[name="${fieldName}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.parentElement.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = Array.isArray(errors[fieldName]) 
                        ? errors[fieldName][0] 
                        : errors[fieldName];
                    feedback.style.display = 'block';
                }
            }
        });
    }

    /**
     * Reset form errors
     */
    function resetFormErrors(form) {
        const invalidInputs = form.querySelectorAll('.is-invalid');
        invalidInputs.forEach(input => {
            input.classList.remove('is-invalid');
        });
        
        const feedbacks = form.querySelectorAll('.invalid-feedback');
        feedbacks.forEach(feedback => {
            feedback.style.display = 'none';
            feedback.textContent = '';
        });
    }

    /**
     * Show success message
     * Uses global Toaster if available, falls back to old method for backward compatibility
     */
    function showSuccess(message) {
        if (window.Toaster && typeof window.Toaster.success === 'function') {
            window.Toaster.success(message);
        } else {
            // Fallback to old method
            let alertDiv = document.querySelector('.ajax-alert');
            if (!alertDiv) {
                alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show ajax-alert';
                alertDiv.setAttribute('role', 'alert');
                const container = document.querySelector('.dashboard-card');
                if (container) {
                    container.insertBefore(alertDiv, container.firstChild);
                }
            }
            alertDiv.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            alertDiv.classList.remove('alert-danger');
            alertDiv.classList.add('alert-success');
        }
    }

    /**
     * Show error message
     * Uses global Toaster if available, falls back to old method for backward compatibility
     */
    function showError(message) {
        if (window.Toaster && typeof window.Toaster.error === 'function') {
            window.Toaster.error(message);
        } else {
            // Fallback to old method
            let alertDiv = document.querySelector('.ajax-alert');
            if (!alertDiv) {
                alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show ajax-alert';
                alertDiv.setAttribute('role', 'alert');
                const container = document.querySelector('.dashboard-card');
                if (container) {
                    container.insertBefore(alertDiv, container.firstChild);
                }
            }
            alertDiv.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            alertDiv.classList.remove('alert-success');
            alertDiv.classList.add('alert-danger');
        }
    }

    /**
     * Initialize file upload handlers
     */
    function initializeFileUploads() {
        // Verification document uploads (skip if inside registration form)
        const verificationUploads = document.querySelectorAll('input[data-type="ghana_card"], input[data-type="business_registration"]');
        verificationUploads.forEach(input => {
            // Skip auto-upload if the input is inside a form with data-section="registration"
            const form = input.closest('form[data-section="registration"]');
            if (form) {
                // This input is part of the registration form, skip auto-upload
                return;
            }
            
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    uploadVerificationDocument(this, this.getAttribute('data-type'));
                }
            });
        });

        // Logo upload
        const logoUpload = document.getElementById('logo_upload');
        if (logoUpload) {
            // Skip auto-upload if the logo input is inside a form with data-section="branding"
            const form = logoUpload.closest('form[data-section="branding"]');
            if (!form) {
                // Only add auto-upload if not inside branding form (logo will upload with form submission)
                logoUpload.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        uploadLogo(this);
                    }
                });
            }
        }

        // Photo upload
        const photoUpload = document.getElementById('photo_upload');
        if (photoUpload) {
            // Skip auto-upload if the photo input is inside a form with data-section="branding"
            const form = photoUpload.closest('form[data-section="branding"]');
            if (!form) {
                // Only add auto-upload if not inside branding form (photos will upload with form submission)
                photoUpload.addEventListener('change', function() {
                    if (this.files && this.files.length > 0) {
                        Array.from(this.files).forEach(file => {
                            uploadPhoto(file);
                        });
                    }
                });
            }
        }

        // Video upload
        const videoUpload = document.getElementById('video_upload');
        if (videoUpload) {
            videoUpload.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    uploadVideo(this);
                }
            });
        }

        // Delete photo buttons
        document.querySelectorAll('.delete-photo-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const photoId = this.getAttribute('data-photo-id');
                if (confirm('Are you sure you want to delete this photo?')) {
                    deletePhoto(photoId);
                }
            });
        });

        // Delete testimonial buttons
        document.querySelectorAll('.delete-testimonial-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const testimonialId = this.getAttribute('data-testimonial-id');
                if (confirm('Are you sure you want to delete this testimonial?')) {
                    deleteTestimonial(testimonialId);
                }
            });
        });

        // Testimonial form submission
        const testimonialForm = document.getElementById('testimonial-form');
        if (testimonialForm) {
            testimonialForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitTestimonial(this);
            });
        }

    }

    /**
     * Upload verification document
     */
    function uploadVerificationDocument(input, type) {
        const file = input.files[0];
        if (!file) {
            showError('Please select a file to upload.');
            return;
        }

        // Check file size client-side (10MB = 10 * 1024 * 1024 bytes)
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            showError('File is too large. Maximum size is 10MB. Please select a smaller file.');
            input.value = ''; // Clear the input
            return;
        }

        const formData = new FormData();
        formData.append('document', file);
        formData.append('type', type);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        const progressContainer = input.closest('.verification-document-upload').querySelector('.upload-progress');
        const progressBar = progressContainer.querySelector('.progress-bar');
        
        progressContainer.style.display = 'block';
        progressBar.style.width = '0%';

        fetch(window.employerCompanyRoutes.verificationDocumentUpload, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token'),
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            // Check if response is OK before parsing JSON
            if (!response.ok) {
                // Handle 413 specifically
                if (response.status === 413) {
                    return response.text().then(text => {
                        let errorMessage = 'File is too large. Maximum size is 10MB. Please try a smaller file.';
                        try {
                            const jsonData = JSON.parse(text);
                            if (jsonData.error) {
                                errorMessage = jsonData.error;
                            }
                        } catch (e) {
                            // If JSON parsing fails, use default message
                        }
                        throw { status: 413, message: errorMessage };
                    });
                }
                // Handle other HTTP errors
                return response.json().then(data => {
                    throw { status: response.status, data: data };
                }).catch(err => {
                    if (err.status) {
                        throw err;
                    }
                    throw { status: response.status, message: 'An error occurred while uploading.' };
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showSuccess(data.message || 'Document uploaded successfully.');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showError(data.error || 'Failed to upload document.');
                progressContainer.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMessage = 'An error occurred while uploading. Please try again.';
            
            if (error.status === 413) {
                errorMessage = error.message || 'File is too large. Maximum size is 10MB. Please try a smaller file.';
            } else if (error.data && error.data.error) {
                errorMessage = error.data.error;
            } else if (error.message) {
                errorMessage = error.message;
            }
            
            showError(errorMessage);
            progressContainer.style.display = 'none';
        });
    }

    /**
     * Upload logo
     */
    function uploadLogo(input) {
        const formData = new FormData();
        formData.append('logo', input.files[0]);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        const progressContainer = input.closest('.logo-upload-area').querySelector('.upload-progress');
        const progressBar = progressContainer.querySelector('.progress-bar');
        
        progressContainer.style.display = 'block';
        progressBar.style.width = '0%';

        fetch(window.employerCompanyRoutes.logoUpload, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token'),
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message || 'Logo uploaded successfully.');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showError(data.error || 'Failed to upload logo.');
                progressContainer.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred while uploading. Please try again.');
            progressContainer.style.display = 'none';
        });
    }

    /**
     * Upload photo
     */
    function uploadPhoto(file) {
        const formData = new FormData();
        formData.append('photo', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        const progressContainer = document.querySelector('.photo-gallery .upload-progress');
        const progressBar = progressContainer.querySelector('.progress-bar');
        
        progressContainer.style.display = 'block';
        progressBar.style.width = '0%';

        fetch(window.employerCompanyRoutes.photoUpload, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token'),
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message || 'Photo uploaded successfully.');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showError(data.error || 'Failed to upload photo.');
                progressContainer.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred while uploading. Please try again.');
            progressContainer.style.display = 'none';
        });
    }

    /**
     * Upload video
     */
    function uploadVideo(input) {
        const formData = new FormData();
        formData.append('video', input.files[0]);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        const progressContainer = input.closest('.video-upload-area').querySelector('.upload-progress');
        const progressBar = progressContainer.querySelector('.progress-bar');
        
        progressContainer.style.display = 'block';
        progressBar.style.width = '0%';

        fetch(window.employerCompanyRoutes.videoUpload, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token'),
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message || 'Video uploaded successfully.');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showError(data.error || 'Failed to upload video.');
                progressContainer.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred while uploading. Please try again.');
            progressContainer.style.display = 'none';
        });
    }

    /**
     * Delete photo
     */
    function deletePhoto(photoId) {
        fetch(window.employerCompanyRoutes.photoDelete.replace(':photoId', photoId), {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message || 'Photo deleted successfully.');
                document.querySelector(`.photo-item[data-photo-id="${photoId}"]`)?.remove();
            } else {
                showError(data.error || 'Failed to delete photo.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred while deleting. Please try again.');
        });
    }

    /**
     * Submit testimonial
     */
    function submitTestimonial(form) {
        const formData = new FormData(form);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetch(window.employerCompanyRoutes.testimonialStore, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token'),
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message || 'Testimonial added successfully.');
                const modal = bootstrap.Modal.getInstance(document.getElementById('addTestimonialModal'));
                if (modal) modal.hide();
                form.reset();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showError(data.error || 'Failed to add testimonial.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred while adding testimonial. Please try again.');
        });
    }

    /**
     * Delete testimonial
     */
    function deleteTestimonial(testimonialId) {
        fetch(window.employerCompanyRoutes.testimonialDelete.replace(':testimonialId', testimonialId), {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message || 'Testimonial deleted successfully.');
                document.querySelector(`.testimonial-item[data-testimonial-id="${testimonialId}"]`)?.remove();
            } else {
                showError(data.error || 'Failed to delete testimonial.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred while deleting. Please try again.');
        });
    }

    /**
     * Check if all mandatory fields are filled
     */
    function checkMandatoryFields() {
        // Use company data from window object (set by PHP)
        const data = window.companyData || {};
        
        // Check all mandatory fields - they must have non-empty values
        return (data.industry && data.industry.trim() !== '') &&
               (data.company_size && data.company_size.trim() !== '') &&
               (data.city && data.city.trim() !== '') &&
               (data.state_or_region && data.state_or_region.trim() !== '') &&
               (data.registration_number && data.registration_number.trim() !== '') &&
               (data.primary_contact_name && data.primary_contact_name.trim() !== '') &&
               (data.primary_contact_phone && data.primary_contact_phone.trim() !== '');
    }

    /**
     * Update submit button state based on mandatory fields
     */
    function updateSubmitButtonState() {
        const submitButton = document.getElementById('submit-for-approval-btn');
        if (!submitButton) return;

        const allFieldsFilled = checkMandatoryFields();
        
        if (allFieldsFilled) {
            submitButton.disabled = false;
            submitButton.style.opacity = '1';
            submitButton.style.cursor = 'pointer';
        } else {
            submitButton.disabled = true;
            submitButton.style.opacity = '0.6';
            submitButton.style.cursor = 'not-allowed';
        }
    }

    // Initialize file uploads when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeFileUploads();
        
        // Check mandatory fields and update submit button state on page load
        updateSubmitButtonState();
    });

})();

