/**
 * Profile Modals JavaScript
 * Handles modal opening/closing, AJAX form submissions, and dynamic updates
 */

// Modal Management
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    const backdrop = document.getElementById(modalId + "-backdrop");

    if (modal && backdrop) {
        modal.style.display = "block";
        backdrop.style.display = "block";

        // Trigger animation
        setTimeout(() => {
            modal.classList.add("show");
            backdrop.classList.add("show");
        }, 10);

        // Prevent body scroll
        document.body.style.overflow = "hidden";
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const backdrop = document.getElementById(modalId + "-backdrop");

    if (modal && backdrop) {
        modal.classList.remove("show");
        backdrop.classList.remove("show");

        setTimeout(() => {
            modal.style.display = "none";
            backdrop.style.display = "none";
        }, 300);

        // Restore body scroll
        document.body.style.overflow = "";
    }
}

// Close modal on backdrop click
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("modal-backdrop")) {
        const modalId = e.target.id.replace("-backdrop", "");
        closeModal(modalId);
    }
});

// Close modal on Escape key
document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        const openModals = document.querySelectorAll(".profile-modal.show");
        openModals.forEach((modal) => {
            closeModal(modal.id);
        });
    }
});

// Generic form submission handler
function submitProfileForm(event, formId, url, modalId) {
    event.preventDefault();

    const form = document.getElementById(formId);
    if (!form) {
        showMessage("error", "Form not found. Please refresh the page.");
        return;
    }

    const formData = new FormData(form);

    // Convert FormData to a plain object for JSON
    const data = {};
    for (const [key, value] of formData.entries()) {
        // Skip _method and _token as we'll handle them separately
        if (key !== "_method" && key !== "_token") {
            // Handle array fields (e.g., career_interest_areas[])
            if (key.endsWith("[]")) {
                const arrayKey = key.slice(0, -2);
                if (!data[arrayKey]) {
                    data[arrayKey] = [];
                }
                data[arrayKey].push(value);
            } else {
                data[key] = value;
            }
        }
    }

    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    // Disable submit button
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

    // Add CSRF token
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    if (!csrfToken) {
        showMessage(
            "error",
            "Security token missing. Please refresh the page."
        );
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
        return;
    }

    fetch(url, {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify(data),
    })
        .then((response) => {
            // Check if response is JSON
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                return response.text().then((text) => {
                    throw new Error("Server returned non-JSON response");
                });
            }

            return response.json().then((data) => {
                return { status: response.status, data: data };
            });
        })
        .then(({ status, data }) => {
            // Success (200) or validation error (422) - both need handling
            if (status === 200 && data && data.success) {
                showMessage(
                    "success",
                    data.message || "Changes saved successfully!"
                );
                closeModal(modalId);
                // Reload page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                // Handle validation errors (422) or other errors
                let errorMessage =
                    data?.message ||
                    "Failed to save changes. Please try again.";

                // Check for validation errors (Laravel returns errors in this format)
                if (data?.errors) {
                    const firstError = Object.values(data.errors)[0];
                    if (Array.isArray(firstError)) {
                        errorMessage = firstError[0];
                    } else {
                        errorMessage = firstError;
                    }
                } else if (data?.message) {
                    errorMessage = data.message;
                }

                showMessage("error", errorMessage);
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch((error) => {
            showMessage("error", "An error occurred. Please try again.");
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
}

// Add Skill
function submitAddSkill(event) {
    event.preventDefault();

    const form = document.getElementById("add-skill-form");
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    fetch("/talent/profile/skill", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Skill added successfully!"
                );
                form.reset();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage("error", data.message || "Failed to add skill.");
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
}

// Delete Skill
function deleteSkill(id) {
    if (!confirm("Are you sure you want to remove this skill?")) {
        return;
    }

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    fetch(`/talent/profile/skill/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Skill removed successfully!"
                );
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage("error", data.message || "Failed to remove skill.");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
        });
}

// Add Language
function submitAddLanguage(event) {
    event.preventDefault();

    const form = document.getElementById("add-language-form");
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    fetch("/talent/profile/language", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Language added successfully!"
                );
                form.reset();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage("error", data.message || "Failed to add language.");
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
}

// Delete Language
function deleteLanguage(id) {
    if (!confirm("Are you sure you want to remove this language?")) {
        return;
    }

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    fetch(`/talent/profile/language/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Language removed successfully!"
                );
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to remove language."
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
        });
}

// Add Education
function submitAddEducation(event) {
    event.preventDefault();

    const form = document.getElementById("add-education-form");
    const formData = new FormData(form);

    // Map education form fields to expected format
    const data = {
        institution_id: formData.get("institution_id") || null,
        degree_type: formData.get("degree_type"),
        field_of_study: formData.get("field_of_study"),
        level: formData.get("level") || null,
        start_date_day: formData.get("education_start_date_day"),
        start_date_month: formData.get("education_start_date_month"),
        start_date_year: formData.get("education_start_date_year"),
        end_date_day: document.getElementById("education-is-current")?.checked
            ? null
            : formData.get("education_end_date_day") || null,
        end_date_month: document.getElementById("education-is-current")?.checked
            ? null
            : formData.get("education_end_date_month") || null,
        end_date_year: document.getElementById("education-is-current")?.checked
            ? null
            : formData.get("education_end_date_year") || null,
        is_current:
            document.getElementById("education-is-current")?.checked || false,
        is_primary:
            formData.get("is_primary") === "1" ||
            document.getElementById("education-is-primary")?.checked,
        gpa: formData.get("gpa") || null,
    };

    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    // Create new FormData with correct field names
    const newFormData = new FormData();
    Object.keys(data).forEach((key) => {
        if (data[key] !== null && data[key] !== undefined) {
            // Convert boolean values to "1" or "0" for FormData (Laravel boolean validation)
            const value =
                typeof data[key] === "boolean"
                    ? data[key]
                        ? "1"
                        : "0"
                    : data[key];
            newFormData.append(key, value);
        }
    });
    newFormData.append("_token", csrfToken);

    fetch("/talent/profile/education", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
        body: newFormData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Education added successfully!"
                );
                form.reset();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to add education."
                );
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
}

// Delete Education
function deleteEducation(id) {
    if (!confirm("Are you sure you want to remove this education record?")) {
        return;
    }

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    fetch(`/talent/profile/education/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Education removed successfully!"
                );
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to remove education."
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
        });
}

// Add Work History
function submitAddWorkHistory(event) {
    event.preventDefault();

    const form = document.getElementById("add-work-history-form");
    const formData = new FormData(form);

    const data = {
        company: formData.get("company"),
        position: formData.get("position"),
        description: formData.get("description") || null,
        location: formData.get("location") || null,
        start_date_day: formData.get("work_start_date_day"),
        start_date_month: formData.get("work_start_date_month"),
        start_date_year: formData.get("work_start_date_year"),
        end_date_day: document.getElementById("work-is-current")?.checked
            ? null
            : formData.get("work_end_date_day") || null,
        end_date_month: document.getElementById("work-is-current")?.checked
            ? null
            : formData.get("work_end_date_month") || null,
        end_date_year: document.getElementById("work-is-current")?.checked
            ? null
            : formData.get("work_end_date_year") || null,
        is_current:
            document.getElementById("work-is-current")?.checked || false,
    };

    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const newFormData = new FormData();
    Object.keys(data).forEach((key) => {
        if (data[key] !== null && data[key] !== undefined) {
            // Convert boolean values to "1" or "0" for FormData (Laravel boolean validation)
            const value =
                typeof data[key] === "boolean"
                    ? data[key]
                        ? "1"
                        : "0"
                    : data[key];
            newFormData.append(key, value);
        }
    });
    newFormData.append("_token", csrfToken);

    fetch("/talent/profile/work-history", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
        body: newFormData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Work history added successfully!"
                );
                form.reset();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to add work history."
                );
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
}

// Delete Work History
function deleteWorkHistory(id) {
    if (!confirm("Are you sure you want to remove this work history record?")) {
        return;
    }

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    fetch(`/talent/profile/work-history/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Work history removed successfully!"
                );
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to remove work history."
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
        });
}

// Add Certification
function submitAddCertification(event) {
    event.preventDefault();

    const form = document.getElementById("add-certification-form");
    const formData = new FormData(form);

    const data = {
        name: formData.get("name"),
        issuer: formData.get("issuer"),
        date_obtained_day: formData.get("cert_date_obtained_day"),
        date_obtained_month: formData.get("cert_date_obtained_month"),
        date_obtained_year: formData.get("cert_date_obtained_year"),
        expiration_date_day: formData.get("cert_expiration_date_day") || null,
        expiration_date_month:
            formData.get("cert_expiration_date_month") || null,
        expiration_date_year: formData.get("cert_expiration_date_year") || null,
        credential_url: formData.get("credential_url") || null,
    };

    // Only include expiration date if all fields are provided
    if (
        !data.expiration_date_day ||
        !data.expiration_date_month ||
        !data.expiration_date_year
    ) {
        data.expiration_date_day = null;
        data.expiration_date_month = null;
        data.expiration_date_year = null;
    }

    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const newFormData = new FormData();
    Object.keys(data).forEach((key) => {
        if (data[key] !== null && data[key] !== undefined) {
            // Convert boolean values to "1" or "0" for FormData (Laravel boolean validation)
            const value =
                typeof data[key] === "boolean"
                    ? data[key]
                        ? "1"
                        : "0"
                    : data[key];
            newFormData.append(key, value);
        }
    });
    newFormData.append("_token", csrfToken);

    fetch("/talent/profile/certification", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
        body: newFormData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Certification added successfully!"
                );
                form.reset();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to add certification."
                );
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
}

// Delete Certification
function deleteCertification(id) {
    if (!confirm("Are you sure you want to remove this certification?")) {
        return;
    }

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    fetch(`/talent/profile/certification/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Certification removed successfully!"
                );
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to remove certification."
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
        });
}

// Add Volunteer Experience
function submitAddVolunteerExperience(event) {
    event.preventDefault();

    const form = document.getElementById("add-volunteer-form");
    const formData = new FormData(form);

    const data = {
        organization: formData.get("organization"),
        start_date_day: formData.get("volunteer_start_date_day"),
        start_date_month: formData.get("volunteer_start_date_month"),
        start_date_year: formData.get("volunteer_start_date_year"),
        end_date_day: document.getElementById("volunteer-is-current")?.checked
            ? null
            : formData.get("volunteer_end_date_day") || null,
        end_date_month: document.getElementById("volunteer-is-current")?.checked
            ? null
            : formData.get("volunteer_end_date_month") || null,
        end_date_year: document.getElementById("volunteer-is-current")?.checked
            ? null
            : formData.get("volunteer_end_date_year") || null,
        is_current:
            document.getElementById("volunteer-is-current")?.checked || false,
        details: formData.get("details") || null,
    };

    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const newFormData = new FormData();
    Object.keys(data).forEach((key) => {
        if (data[key] !== null && data[key] !== undefined) {
            // Convert boolean values to "1" or "0" for FormData (Laravel boolean validation)
            const value =
                typeof data[key] === "boolean"
                    ? data[key]
                        ? "1"
                        : "0"
                    : data[key];
            newFormData.append(key, value);
        }
    });
    newFormData.append("_token", csrfToken);

    fetch("/talent/profile/volunteer-experience", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
        body: newFormData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Volunteer experience added successfully!"
                );
                form.reset();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to add volunteer experience."
                );
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
}

// Delete Volunteer Experience
function deleteVolunteerExperience(id) {
    if (
        !confirm("Are you sure you want to remove this volunteer experience?")
    ) {
        return;
    }

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    fetch(`/talent/profile/volunteer-experience/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Volunteer experience removed successfully!"
                );
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to remove volunteer experience."
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
        });
}

// Add Leadership Experience
function submitAddLeadershipExperience(event) {
    event.preventDefault();

    const form = document.getElementById("add-leadership-form");
    const formData = new FormData(form);

    const data = {
        organization: formData.get("organization"),
        title: formData.get("title") || null,
        start_date_day: formData.get("leadership_start_date_day"),
        start_date_month: formData.get("leadership_start_date_month"),
        start_date_year: formData.get("leadership_start_date_year"),
        end_date_day: document.getElementById("leadership-is-current")?.checked
            ? null
            : formData.get("leadership_end_date_day") || null,
        end_date_month: document.getElementById("leadership-is-current")
            ?.checked
            ? null
            : formData.get("leadership_end_date_month") || null,
        end_date_year: document.getElementById("leadership-is-current")?.checked
            ? null
            : formData.get("leadership_end_date_year") || null,
        is_current:
            document.getElementById("leadership-is-current")?.checked || false,
        details: formData.get("details") || null,
    };

    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const newFormData = new FormData();
    Object.keys(data).forEach((key) => {
        if (data[key] !== null && data[key] !== undefined) {
            // Convert boolean values to "1" or "0" for FormData (Laravel boolean validation)
            const value =
                typeof data[key] === "boolean"
                    ? data[key]
                        ? "1"
                        : "0"
                    : data[key];
            newFormData.append(key, value);
        }
    });
    newFormData.append("_token", csrfToken);

    fetch("/talent/profile/leadership-experience", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
        body: newFormData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Leadership experience added successfully!"
                );
                form.reset();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to add leadership experience."
                );
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
}

// Delete Leadership Experience
function deleteLeadershipExperience(id) {
    if (
        !confirm("Are you sure you want to remove this leadership experience?")
    ) {
        return;
    }

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    fetch(`/talent/profile/leadership-experience/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message ||
                        "Leadership experience removed successfully!"
                );
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to remove leadership experience."
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
        });
}

// Add Gigs/Freelance
function submitAddGigsFreelance(event) {
    event.preventDefault();

    const form = document.getElementById("add-gigs-freelance-form");
    const formData = new FormData(form);

    const data = {
        company: formData.get("company"),
        title: formData.get("title") || null,
        start_date_day: formData.get("gigs_start_date_day"),
        start_date_month: formData.get("gigs_start_date_month"),
        start_date_year: formData.get("gigs_start_date_year"),
        end_date_day: document.getElementById("gigs-is-current")?.checked
            ? null
            : formData.get("gigs_end_date_day") || null,
        end_date_month: document.getElementById("gigs-is-current")?.checked
            ? null
            : formData.get("gigs_end_date_month") || null,
        end_date_year: document.getElementById("gigs-is-current")?.checked
            ? null
            : formData.get("gigs_end_date_year") || null,
        is_current:
            document.getElementById("gigs-is-current")?.checked || false,
        details: formData.get("details") || null,
    };

    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const newFormData = new FormData();
    Object.keys(data).forEach((key) => {
        if (data[key] !== null && data[key] !== undefined) {
            // Convert boolean values to "1" or "0" for FormData (Laravel boolean validation)
            const value =
                typeof data[key] === "boolean"
                    ? data[key]
                        ? "1"
                        : "0"
                    : data[key];
            newFormData.append(key, value);
        }
    });
    newFormData.append("_token", csrfToken);

    fetch("/talent/profile/gigs-freelance", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
        body: newFormData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Gigs/Freelance work added successfully!"
                );
                form.reset();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to add gigs/freelance work."
                );
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
}

// Delete Gigs/Freelance
function deleteGigsFreelance(id) {
    if (!confirm("Are you sure you want to remove this gigs/freelance work?")) {
        return;
    }

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    fetch(`/talent/profile/gigs-freelance/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Gigs/Freelance work removed successfully!"
                );
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to remove gigs/freelance work."
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
        });
}

// Add Project
function submitAddProject(event) {
    event.preventDefault();

    const form = document.getElementById("add-project-form");
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    fetch("/talent/profile/project", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Project added successfully!"
                );
                form.reset();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage("error", data.message || "Failed to add project.");
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
}

// Delete Project
function deleteProject(id) {
    if (!confirm("Are you sure you want to remove this project?")) {
        return;
    }

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    fetch(`/talent/profile/project/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Project removed successfully!"
                );
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showMessage("error", data.message || "Failed to remove project.");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage("error", "An error occurred. Please try again.");
        });
}

// Institution Autocomplete for Education Modal
let institutionAutocompleteTimeout = null;
let selectedInstitutionIndex = -1;

function initInstitutionAutocomplete() {
    const input = document.getElementById("education-institution-input");
    const hiddenInput = document.getElementById("education-institution-id");
    const suggestions = document.getElementById("education-institution-suggestions");

    if (!input || !hiddenInput || !suggestions) return;

    // Clear previous selection when modal opens
    input.value = "";
    hiddenInput.value = "";

    // Input event handler with debounce
    input.addEventListener("input", function (e) {
        const query = e.target.value.trim();
        clearTimeout(institutionAutocompleteTimeout);
        selectedInstitutionIndex = -1;

        if (query.length >= 2) {
            institutionAutocompleteTimeout = setTimeout(() => {
                fetchInstitutions(query);
            }, 300);
        } else {
            hideInstitutionSuggestions();
            hiddenInput.value = "";
        }
    });

    // Keydown handler for navigation
    input.addEventListener("keydown", function (e) {
        if (e.key === "ArrowDown") {
            e.preventDefault();
            selectedInstitutionIndex = Math.min(
                selectedInstitutionIndex + 1,
                suggestions.querySelectorAll(".institution-autocomplete-item").length - 1
            );
            updateSelectedInstitution();
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            selectedInstitutionIndex = Math.max(selectedInstitutionIndex - 1, -1);
            updateSelectedInstitution();
        } else if (e.key === "Enter") {
            e.preventDefault();
            if (selectedInstitutionIndex >= 0) {
                const items = suggestions.querySelectorAll(".institution-autocomplete-item");
                if (items[selectedInstitutionIndex]) {
                    items[selectedInstitutionIndex].click();
                }
            }
        } else if (e.key === "Escape") {
            hideInstitutionSuggestions();
        }
    });

    // Click outside to close suggestions
    document.addEventListener("click", function (e) {
        const container = document.querySelector(".institution-autocomplete-container");
        if (container && !container.contains(e.target)) {
            hideInstitutionSuggestions();
        }
    });
}

async function fetchInstitutions(query) {
    try {
        const response = await fetch(
            `/api/autocomplete/institutions?q=${encodeURIComponent(query)}`
        );
        const institutions = await response.json();
        showInstitutionSuggestions(institutions);
    } catch (error) {
        console.error("Error fetching institutions:", error);
        hideInstitutionSuggestions();
    }
}

function showInstitutionSuggestions(institutions) {
    const suggestions = document.getElementById("education-institution-suggestions");
    if (!suggestions) return;

    suggestions.innerHTML = "";

    if (institutions.length === 0) {
        const emptyItem = document.createElement("div");
        emptyItem.className = "institution-autocomplete-item empty";
        emptyItem.textContent = "No institutions found";
        suggestions.appendChild(emptyItem);
        suggestions.classList.add("show");
        return;
    }

    institutions.forEach((institution, index) => {
        const item = document.createElement("div");
        item.className = "institution-autocomplete-item";
        item.textContent = institution.name;
        item.dataset.id = institution.id;
        item.dataset.name = institution.name;
        item.addEventListener("click", function () {
            selectInstitution(institution.id, institution.name);
        });
        suggestions.appendChild(item);
    });

    suggestions.classList.add("show");
    selectedInstitutionIndex = -1;
}

function hideInstitutionSuggestions() {
    const suggestions = document.getElementById("education-institution-suggestions");
    if (suggestions) {
        suggestions.classList.remove("show");
        selectedInstitutionIndex = -1;
    }
}

function updateSelectedInstitution() {
    const suggestions = document.getElementById("education-institution-suggestions");
    if (!suggestions) return;

    const items = suggestions.querySelectorAll(".institution-autocomplete-item");
    items.forEach((item, index) => {
        if (index === selectedInstitutionIndex) {
            item.classList.add("selected");
            item.scrollIntoView({ block: "nearest", behavior: "smooth" });
        } else {
            item.classList.remove("selected");
        }
    });
}

function selectInstitution(id, name) {
    const input = document.getElementById("education-institution-input");
    const hiddenInput = document.getElementById("education-institution-id");

    if (input && hiddenInput) {
        input.value = name;
        hiddenInput.value = id;
        hideInstitutionSuggestions();
    }
}

// Initialize autocomplete when education modal opens
document.addEventListener("DOMContentLoaded", function () {
    // Watch for education modal opening
    const educationModal = document.getElementById("education-modal");
    if (educationModal) {
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (
                    mutation.type === "attributes" &&
                    mutation.attributeName === "style"
                ) {
                    if (
                        educationModal.style.display === "block" &&
                        !educationModal.classList.contains("autocomplete-initialized")
                    ) {
                        // Small delay to ensure modal is fully rendered
                        setTimeout(() => {
                            initInstitutionAutocomplete();
                            educationModal.classList.add("autocomplete-initialized");
                        }, 100);
                    } else if (educationModal.style.display === "none") {
                        // Reset when modal closes
                        educationModal.classList.remove("autocomplete-initialized");
                        const input = document.getElementById("education-institution-input");
                        const hiddenInput = document.getElementById("education-institution-id");
                        if (input) input.value = "";
                        if (hiddenInput) hiddenInput.value = "";
                        hideInstitutionSuggestions();
                    }
                }
            });
        });

        observer.observe(educationModal, {
            attributes: true,
            attributeFilter: ["style"],
        });
    }
});

// Toggle end date containers
function toggleEducationEndDate(checkbox) {
    const container = document.getElementById("education-end-date-container");
    if (checkbox.checked) {
        container.style.display = "none";
        // Clear end date fields
        container
            .querySelectorAll("select")
            .forEach((select) => (select.value = ""));
    } else {
        container.style.display = "block";
    }
}

function toggleWorkEndDate(checkbox) {
    const container = document.getElementById("work-end-date-container");
    if (checkbox.checked) {
        container.style.display = "none";
        container
            .querySelectorAll("select")
            .forEach((select) => (select.value = ""));
    } else {
        container.style.display = "block";
    }
}

function toggleVolunteerEndDate(checkbox) {
    const container = document.getElementById("volunteer-end-date-container");
    if (checkbox.checked) {
        container.style.display = "none";
        container
            .querySelectorAll("select")
            .forEach((select) => (select.value = ""));
    } else {
        container.style.display = "block";
    }
}

function toggleLeadershipEndDate(checkbox) {
    const container = document.getElementById("leadership-end-date-container");
    if (checkbox.checked) {
        container.style.display = "none";
        container
            .querySelectorAll("select")
            .forEach((select) => (select.value = ""));
    } else {
        container.style.display = "block";
    }
}

function toggleGigsEndDate(checkbox) {
    const container = document.getElementById("gigs-end-date-container");
    if (checkbox.checked) {
        container.style.display = "none";
        container
            .querySelectorAll("select")
            .forEach((select) => (select.value = ""));
    } else {
        container.style.display = "block";
    }
}

// Show message
// Uses global Toaster if available, falls back to old method for backward compatibility
function showMessage(type, message) {
    // Use global Toaster if available
    if (window.Toaster && typeof window.Toaster.show === 'function') {
        // Map types to Toaster methods
        switch (type) {
            case "success":
                window.Toaster.success(message);
                break;
            case "error":
                window.Toaster.error(message);
                break;
            case "info":
                window.Toaster.info(message);
                break;
            case "warning":
                window.Toaster.warning(message);
                break;
            default:
                window.Toaster.info(message);
        }
        return;
    }

    // Fallback to old method
    const messagesContainer = document.getElementById("profile-messages");
    if (!messagesContainer) return;

    const messageDiv = document.createElement("div");
    let bgColor, textColor;
    switch (type) {
        case "success":
            bgColor = "#d4edda";
            textColor = "#155724";
            break;
        case "error":
            bgColor = "#f8d7da";
            textColor = "#721c24";
            break;
        case "info":
            bgColor = "#d1ecf1";
            textColor = "#0c5460";
            break;
        default:
            bgColor = "#d1ecf1";
            textColor = "#0c5460";
    }
    messageDiv.style.cssText = `background: ${bgColor}; color: ${textColor}; padding: 12px; border-radius: 4px; margin-bottom: 20px;`;
    messageDiv.textContent = message;

    messagesContainer.innerHTML = "";
    messagesContainer.appendChild(messageDiv);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

// Photo upload function
function openPhotoUpload() {
    const input = document.createElement("input");
    input.type = "file";
    input.accept = "image/*";
    input.onchange = function (e) {
        const file = e.target.files[0];
        if (file) {
            uploadPhoto(file);
        }
    };
    input.click();
}

function uploadPhoto(file) {
    const formData = new FormData();
    formData.append("photo", file);
    formData.append(
        "_token",
        document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content")
    );

    fetch("/talent/profile/photo", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: formData,
    })
        .then((response) => {
            return response.json().then((data) => {
                if (!response.ok) {
                    throw { data, status: response.status };
                }
                return data;
            });
        })
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Photo uploaded successfully!"
                );
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                let errorMessage = data.error || "Failed to upload photo.";
                if (data.errors && data.errors.photo) {
                    errorMessage = Array.isArray(data.errors.photo)
                        ? data.errors.photo.join(", ")
                        : data.errors.photo;
                }
                showMessage("error", errorMessage);
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            let errorMessage = "An error occurred while uploading photo.";
            if (error.data) {
                if (error.data.errors && error.data.errors.photo) {
                    errorMessage = Array.isArray(error.data.errors.photo)
                        ? error.data.errors.photo.join(", ")
                        : error.data.errors.photo;
                } else if (error.data.error) {
                    errorMessage = error.data.error;
                }
            }
            showMessage("error", errorMessage);
        });
}

// Banner upload function (placeholder - requires banner_image field in database)
function openBannerUpload() {
    const input = document.createElement("input");
    input.type = "file";
    input.accept = "image/*";
    input.onchange = function (e) {
        const file = e.target.files[0];
        if (file) {
            // Note: This requires a banner_image field in the talent_profiles table
            // For now, show a message that this feature is coming soon
            showMessage(
                "info",
                "Banner upload feature will be available soon. A database migration is required."
            );
        }
    };
    input.click();
}

function openPrivacySettings() {
    // TODO: Implement privacy settings modal
    showMessage("info", "Privacy settings coming soon!");
}

// Job Categories Checkbox Management
function toggleCategoryGroup(category, checked) {
    const children = document.querySelectorAll(
        `.category-child[data-parent="${category}"]`
    );
    children.forEach((child) => {
        child.checked = checked;
    });
}

function updateParentCheckbox(category) {
    const children = document.querySelectorAll(
        `.category-child[data-parent="${category}"]`
    );
    const parent = document.querySelector(
        `.category-parent[data-category="${category}"]`
    );

    if (!parent) return;

    const checkedChildren = Array.from(children).filter(
        (child) => child.checked
    );

    // Update parent checkbox state
    if (checkedChildren.length === 0) {
        parent.checked = false;
        parent.indeterminate = false;
    } else if (checkedChildren.length === children.length) {
        parent.checked = true;
        parent.indeterminate = false;
    } else {
        parent.checked = false;
        parent.indeterminate = true;
    }
}

// Initialize parent checkbox states on page load
document.addEventListener("DOMContentLoaded", function () {
    // Get all parent checkboxes dynamically
    const parentCheckboxes = document.querySelectorAll(".category-parent");
    parentCheckboxes.forEach((parent) => {
        const categoryId = parent.getAttribute("data-category");
        if (categoryId) {
            updateParentCheckbox(categoryId);
        }
    });
});

// Video introduction form submission
function submitVideoIntroduction(event, formId, url, modalId) {
    event.preventDefault();

    const form = document.getElementById(formId);
    const formData = new FormData(form);
    const videoUrl = formData.get("video_introduction");

    // Validate URL if provided
    if (videoUrl && videoUrl.trim() !== "") {
        // Check if it's a YouTube or Vimeo URL
        const youtubePattern =
            /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
        const vimeoPattern =
            /(?:vimeo\.com\/|player\.vimeo\.com\/video\/)(\d+)/;
        const embedPattern = /(youtube\.com\/embed|player\.vimeo\.com\/video)/;

        if (
            !youtubePattern.test(videoUrl) &&
            !vimeoPattern.test(videoUrl) &&
            !embedPattern.test(videoUrl)
        ) {
            showMessage("error", "Please enter a valid YouTube or Vimeo URL.");
            return;
        }
    }

    fetch(url, {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify({
            video_introduction: videoUrl ? videoUrl.trim() : null,
        }),
    })
        .then((response) => {
            return response.json().then((data) => {
                if (!response.ok) {
                    throw { data, status: response.status };
                }
                return data;
            });
        })
        .then((data) => {
            if (data.success) {
                showMessage(
                    "success",
                    data.message || "Video introduction updated successfully!"
                );
                closeModal(modalId);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to update video introduction."
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage(
                "error",
                error.data?.message ||
                    "An error occurred while updating the video introduction."
            );
        });
}

// Resume upload function
function openResumeUpload() {
    const input = document.createElement("input");
    input.type = "file";
    input.accept = ".pdf,.doc,.docx";
    input.onchange = function (e) {
        const file = e.target.files[0];
        if (file) {
            uploadResume(file);
        }
    };
    input.click();
}

function uploadResume(file) {
    // Check file size (max 10MB)
    if (file.size > 10 * 1024 * 1024) {
        showMessage("error", "Resume file is too large. Maximum size is 10MB.");
        return;
    }

    const formData = new FormData();
    formData.append("resume", file);
    formData.append(
        "_token",
        document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content")
    );

    showMessage("info", "Uploading resume... Please wait.");

    fetch("/talent/profile/resume", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: formData,
    })
        .then((response) => {
            return response.json().then((data) => {
                if (!response.ok) {
                    throw { data, status: response.status };
                }
                return data;
            });
        })
        .then((data) => {
            if (data.success) {
                showMessage("success", "Resume uploaded successfully!");
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showMessage(
                    "error",
                    data.message || "Failed to upload resume."
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showMessage(
                "error",
                error.data?.message ||
                    "An error occurred while uploading the resume."
            );
        });
}

// Copy public URL to clipboard
function copyPublicUrl(url) {
    navigator.clipboard.writeText(url).then(
        function () {
            showMessage("success", "Public URL copied to clipboard!");
        },
        function () {
            // Fallback for older browsers
            const textArea = document.createElement("textarea");
            textArea.value = url;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand("copy");
                showMessage("success", "Public URL copied to clipboard!");
            } catch (err) {
                showMessage(
                    "error",
                    "Failed to copy URL. Please copy manually."
                );
            }
            document.body.removeChild(textArea);
        }
    );
}

// Tag-based Input Components
class CareerInterestTags {
    constructor(containerId, hiddenInputId, availableOptions = []) {
        this.container = document.getElementById(containerId);
        this.hiddenInput = document.getElementById(hiddenInputId);
        this.availableOptions = availableOptions; // Array of {id, name}
        this.selectedTags = [];
        this.init();
    }

    init() {
        if (!this.container) return;

        const wrapper = this.container.querySelector('.career-interest-tags-wrapper');
        const input = this.container.querySelector('.career-interest-input');
        const suggestions = this.container.querySelector('.career-interest-suggestions');

        if (!wrapper || !input || !suggestions) return;

        // Load existing tags from hidden input
        if (this.hiddenInput && this.hiddenInput.value) {
            try {
                const ids = JSON.parse(this.hiddenInput.value);
                ids.forEach(id => {
                    const option = this.availableOptions.find(opt => opt.id === id);
                    if (option) {
                        this.addTag(option.id, option.name);
                    }
                });
            } catch (e) {
                console.error('Error parsing career interest areas:', e);
            }
        }

        // Input event handler
        input.addEventListener('input', (e) => {
            const query = e.target.value.trim().toLowerCase();
            if (query.length > 0) {
                this.showSuggestions(query);
            } else {
                this.hideSuggestions();
            }
        });

        // Keydown handler
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = e.target.value.trim();
                if (query.length > 0) {
                    const match = this.findMatch(query);
                    if (match) {
                        this.addTag(match.id, match.name);
                        input.value = '';
                        this.hideSuggestions();
                    }
                }
            } else if (e.key === 'Backspace' && input.value === '') {
                // Remove last tag if input is empty
                if (this.selectedTags.length > 0) {
                    this.removeTag(this.selectedTags[this.selectedTags.length - 1].id);
                }
            }
        });

        // Click outside to close suggestions
        document.addEventListener('click', (e) => {
            if (!this.container.contains(e.target)) {
                this.hideSuggestions();
            }
        });
    }

    findMatch(query) {
        const lowerQuery = query.toLowerCase();
        // Find exact match first
        let match = this.availableOptions.find(opt => 
            opt.name.toLowerCase() === lowerQuery && 
            !this.selectedTags.find(tag => tag.id === opt.id)
        );
        if (match) return match;

        // Find partial match
        match = this.availableOptions.find(opt => 
            opt.name.toLowerCase().includes(lowerQuery) && 
            !this.selectedTags.find(tag => tag.id === opt.id)
        );
        return match;
    }

    showSuggestions(query) {
        const suggestions = this.container.querySelector('.career-interest-suggestions');
        const lowerQuery = query.toLowerCase();
        
        // Filter available options
        const filtered = this.availableOptions.filter(opt => 
            opt.name.toLowerCase().includes(lowerQuery) &&
            !this.selectedTags.find(tag => tag.id === opt.id)
        );

        // Clear suggestions
        suggestions.innerHTML = '';

        if (filtered.length === 0) {
            suggestions.classList.remove('show');
            return;
        }

        // Add label
        const label = document.createElement('div');
        label.className = 'suggestions-label';
        label.textContent = 'Suggestions:';
        suggestions.appendChild(label);

        // Add suggestion tags
        const tagsContainer = document.createElement('div');
        tagsContainer.className = 'suggestions-tags';
        
        filtered.slice(0, 10).forEach(option => {
            const tag = document.createElement('span');
            tag.className = 'suggestion-tag';
            tag.textContent = option.name;
            tag.addEventListener('click', () => {
                this.addTag(option.id, option.name);
                this.container.querySelector('.career-interest-input').value = '';
                this.hideSuggestions();
            });
            tagsContainer.appendChild(tag);
        });

        suggestions.appendChild(tagsContainer);
        suggestions.classList.add('show');
    }

    hideSuggestions() {
        const suggestions = this.container.querySelector('.career-interest-suggestions');
        if (suggestions) {
            suggestions.classList.remove('show');
        }
    }

    addTag(id, name) {
        if (this.selectedTags.find(tag => tag.id === id)) {
            return; // Already added
        }

        const wrapper = this.container.querySelector('.career-interest-tags-wrapper');
        const input = this.container.querySelector('.career-interest-input');

        const tag = document.createElement('span');
        tag.className = 'career-interest-tag';
        tag.dataset.id = id;
        tag.innerHTML = `
            ${name}
            <span class="career-interest-tag-remove" onclick="careerInterestTags.removeTag('${id}')">×</span>
        `;

        // Insert before input
        wrapper.insertBefore(tag, input);

        this.selectedTags.push({ id, name });
        this.updateHiddenInput();
    }

    removeTag(id) {
        const tag = this.container.querySelector(`.career-interest-tag[data-id="${id}"]`);
        if (tag) {
            tag.remove();
            this.selectedTags = this.selectedTags.filter(tag => tag.id !== id);
            this.updateHiddenInput();
        }
    }

    updateHiddenInput() {
        if (this.hiddenInput) {
            const ids = this.selectedTags.map(tag => tag.id);
            this.hiddenInput.value = JSON.stringify(ids);
        }
    }
}

class PreferredCityTags {
    constructor(containerId, hiddenInputId) {
        this.container = document.getElementById(containerId);
        this.hiddenInput = document.getElementById(hiddenInputId);
        this.selectedTags = [];
        this.autocompleteTimeout = null;
        this.init();
    }

    init() {
        if (!this.container) {
            console.error('PreferredCityTags: Container not found');
            return;
        }

        const wrapper = this.container.querySelector('.preferred-city-tags-wrapper');
        const input = this.container.querySelector('.preferred-city-input');
        const suggestions = this.container.querySelector('.preferred-city-suggestions');

        if (!wrapper) {
            console.error('PreferredCityTags: Wrapper not found');
            return;
        }
        if (!input) {
            console.error('PreferredCityTags: Input not found');
            return;
        }
        if (!suggestions) {
            console.error('PreferredCityTags: Suggestions container not found');
            return;
        }

        // Load existing tags from hidden input
        if (this.hiddenInput && this.hiddenInput.value) {
            try {
                const data = JSON.parse(this.hiddenInput.value);
                if (Array.isArray(data)) {
                    data.forEach(item => {
                        if (item.id && item.name) {
                            this.addTag(item.id, item.name);
                        }
                    });
                }
            } catch (e) {
                console.error('Error parsing preferred cities:', e);
            }
        }

        // Input event handler with debounce for autocomplete
        input.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            clearTimeout(this.autocompleteTimeout);
            
            if (query.length >= 2) {
                this.autocompleteTimeout = setTimeout(() => {
                    this.fetchCities(query);
                }, 300);
            } else {
                this.hideSuggestions();
            }
        });

        // Focus event to show suggestions if there's text
        input.addEventListener('focus', (e) => {
            const query = e.target.value.trim();
            if (query.length >= 2) {
                this.fetchCities(query);
            }
        });

        // Keydown handler
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = e.target.value.trim();
                if (query.length > 0) {
                    // Try to add first suggestion if available
                    const suggestionsEl = this.container.querySelector('.preferred-city-suggestions');
                    const firstSuggestion = suggestionsEl ? suggestionsEl.querySelector('.suggestion-tag') : null;
                    if (firstSuggestion) {
                        firstSuggestion.click();
                    } else {
                        // If no suggestions, add as-is (will be looked up by backend)
                        const tempId = 'temp_' + Date.now();
                        this.addTag(tempId, query);
                        input.value = '';
                        this.hideSuggestions();
                    }
                }
            } else if (e.key === 'Backspace' && input.value === '') {
                // Remove last tag if input is empty
                if (this.selectedTags.length > 0) {
                    this.removeTag(this.selectedTags[this.selectedTags.length - 1].id);
                }
            }
        });

        // Click outside to close suggestions
        document.addEventListener('click', (e) => {
            if (!this.container.contains(e.target)) {
                this.hideSuggestions();
            }
        });
    }

    async fetchCities(query) {
        try {
            const response = await fetch(`/api/autocomplete/cities?q=${encodeURIComponent(query)}`);
            if (!response.ok) {
                console.error('Autocomplete request failed:', response.status);
                this.hideSuggestions();
                return;
            }
            const cities = await response.json();
            if (Array.isArray(cities)) {
                this.showSuggestions(cities);
            } else {
                console.error('Invalid response format from autocomplete:', cities);
                this.hideSuggestions();
            }
        } catch (error) {
            console.error('Error fetching cities:', error);
            this.hideSuggestions();
        }
    }

    showSuggestions(cities) {
        const suggestions = this.container.querySelector('.preferred-city-suggestions');
        
        // Filter out already selected cities
        const filtered = cities.filter(city => 
            !this.selectedTags.find(tag => tag.id === city.id || tag.name.toLowerCase() === city.name.toLowerCase())
        );

        // Clear suggestions
        suggestions.innerHTML = '';

        if (filtered.length === 0) {
            suggestions.classList.remove('show');
            return;
        }

        // Add label
        const label = document.createElement('div');
        label.className = 'suggestions-label';
        label.textContent = 'Suggestions:';
        suggestions.appendChild(label);

        // Add suggestion tags
        const tagsContainer = document.createElement('div');
        tagsContainer.className = 'suggestions-tags';
        
        filtered.slice(0, 10).forEach(city => {
            const tag = document.createElement('span');
            tag.className = 'suggestion-tag';
            tag.textContent = city.name;
            tag.addEventListener('click', () => {
                this.addTag(city.id, city.name);
                this.container.querySelector('.preferred-city-input').value = '';
                this.hideSuggestions();
            });
            tagsContainer.appendChild(tag);
        });

        suggestions.appendChild(tagsContainer);
        suggestions.classList.add('show');
    }

    hideSuggestions() {
        const suggestions = this.container.querySelector('.preferred-city-suggestions');
        if (suggestions) {
            suggestions.classList.remove('show');
        }
    }

    addTag(id, name) {
        // Check if city with same name already exists
        if (this.selectedTags.find(tag => tag.name.toLowerCase() === name.toLowerCase())) {
            return; // Already added
        }

        const wrapper = this.container.querySelector('.preferred-city-tags-wrapper');
        const input = this.container.querySelector('.preferred-city-input');

        const tag = document.createElement('span');
        tag.className = 'preferred-city-tag';
        tag.dataset.id = id;
        tag.innerHTML = `
            ${name}
            <span class="preferred-city-tag-remove" onclick="preferredCityTags.removeTag('${id}')">×</span>
        `;

        // Insert before input
        wrapper.insertBefore(tag, input);

        this.selectedTags.push({ id, name });
        this.updateHiddenInput();
    }

    removeTag(id) {
        const tag = this.container.querySelector(`.preferred-city-tag[data-id="${id}"]`);
        if (tag) {
            tag.remove();
            this.selectedTags = this.selectedTags.filter(tag => tag.id !== id);
            this.updateHiddenInput();
        }
    }

    updateHiddenInput() {
        if (this.hiddenInput) {
            this.hiddenInput.value = JSON.stringify(this.selectedTags);
        }
    }
}

// Global instances (will be initialized when modal opens)
let careerInterestTags = null;
let preferredCityTags = null;

// Custom form submission for work preferences
function submitWorkPreferencesForm(event, formId, url, modalId) {
    event.preventDefault();

    const form = document.getElementById(formId);
    if (!form) {
        showMessage("error", "Form not found. Please refresh the page.");
        return;
    }

    // Get career interest areas from hidden input
    const careerInterestAreasInput = document.getElementById('career-interest-areas-hidden');
    let careerInterestAreas = [];
    if (careerInterestAreasInput && careerInterestAreasInput.value) {
        try {
            careerInterestAreas = JSON.parse(careerInterestAreasInput.value);
        } catch (e) {
            console.error('Error parsing career interest areas:', e);
        }
    }

    // Get preferred cities from hidden input
    const preferredCitiesInput = document.getElementById('preferred-cities-hidden');
    let preferredCities = [];
    if (preferredCitiesInput && preferredCitiesInput.value) {
        try {
            const citiesData = JSON.parse(preferredCitiesInput.value);
            // Send city IDs if available, otherwise send names
            preferredCities = citiesData.map(city => {
                if (typeof city === 'string') {
                    return city;
                }
                // If city has a real ID (not temp), send the ID
                if (city.id && !city.id.toString().startsWith('temp_')) {
                    return city.id;
                }
                // Otherwise send the name (backend will look it up)
                return city.name || city;
            });
        } catch (e) {
            console.error('Error parsing preferred cities:', e);
        }
    }

    // Get work models - get all checked checkboxes
    const workModelCheckboxes = form.querySelectorAll('input[name="work_models[]"]:checked');
    const workModels = Array.from(workModelCheckboxes).map(cb => cb.value);

    const data = {
        career_interest_areas: careerInterestAreas,
        preferred_cities: preferredCities,
        work_models: workModels,
    };

    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    // Disable submit button
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

    // Add CSRF token
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    if (!csrfToken) {
        showMessage(
            "error",
            "Security token missing. Please refresh the page."
        );
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
        return;
    }

    fetch(url, {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify(data),
    })
        .then((response) => {
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                return response.text().then((text) => {
                    throw new Error("Server returned non-JSON response");
                });
            }

            return response.json().then((data) => {
                return { status: response.status, data: data };
            });
        })
        .then(({ status, data }) => {
            if (status === 200 && data && data.success) {
                showMessage(
                    "success",
                    data.message || "Changes saved successfully!"
                );
                closeModal(modalId);
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                let errorMessage =
                    data?.message ||
                    "Failed to save changes. Please try again.";

                if (data?.errors) {
                    const firstError = Object.values(data.errors)[0];
                    if (Array.isArray(firstError)) {
                        errorMessage = firstError[0];
                    } else {
                        errorMessage = firstError;
                    }
                } else if (data?.message) {
                    errorMessage = data.message;
                }

                showMessage("error", errorMessage);
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch((error) => {
            showMessage("error", "An error occurred. Please try again.");
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
}

// Initialize tag components when work preferences modal opens
document.addEventListener('DOMContentLoaded', function() {
    // Watch for modal opening - check both style.display and class changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            const modal = document.getElementById('work-preferences-modal');
            if (!modal) return;

            // Check if modal is visible (either by style.display or show class)
            const isVisible = modal.style.display === 'block' || 
                            modal.classList.contains('show') ||
                            window.getComputedStyle(modal).display !== 'none';

            if (isVisible && !careerInterestTags) {
                // Get available career interest areas from the page
                const availableOptions = [];
                document.querySelectorAll('.category-child').forEach(checkbox => {
                    if (checkbox.value) {
                        const name = checkbox.getAttribute('data-name') || 
                                    (checkbox.nextElementSibling ? checkbox.nextElementSibling.textContent.trim() : '');
                        if (name) {
                            availableOptions.push({
                                id: checkbox.value,
                                name: name
                            });
                        }
                    }
                });

                // Initialize components
                careerInterestTags = new CareerInterestTags(
                    'career-interest-tags-container',
                    'career-interest-areas-hidden',
                    availableOptions
                );

                preferredCityTags = new PreferredCityTags(
                    'preferred-city-tags-container',
                    'preferred-cities-hidden'
                );
            } else if (!isVisible && careerInterestTags) {
                // Reset components when modal closes
                careerInterestTags = null;
                preferredCityTags = null;
            }
        });
    });

    const modal = document.getElementById('work-preferences-modal');
    if (modal) {
        // Observe both style and class changes
        observer.observe(modal, { 
            attributes: true, 
            attributeFilter: ['style', 'class'],
            subtree: false
        });
    }

    // Also check on modal open function call
    const originalOpenModal = window.openModal;
    if (typeof originalOpenModal === 'function') {
        window.openModal = function(modalId) {
            originalOpenModal(modalId);
            // Small delay to ensure DOM is updated
            setTimeout(function() {
                if (modalId === 'work-preferences-modal' && !careerInterestTags) {
                    const availableOptions = [];
                    document.querySelectorAll('.category-child').forEach(checkbox => {
                        if (checkbox.value) {
                            const name = checkbox.getAttribute('data-name') || 
                                        (checkbox.nextElementSibling ? checkbox.nextElementSibling.textContent.trim() : '');
                            if (name) {
                                availableOptions.push({
                                    id: checkbox.value,
                                    name: name
                                });
                            }
                        }
                    });

                    careerInterestTags = new CareerInterestTags(
                        'career-interest-tags-container',
                        'career-interest-areas-hidden',
                        availableOptions
                    );

                    preferredCityTags = new PreferredCityTags(
                        'preferred-city-tags-container',
                        'preferred-cities-hidden'
                    );
                }
            }, 100);
        };
    }
});
