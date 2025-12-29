<x-modal id="welcome-modal" title="Welcome to Your Profile!" size="large">
    <div style="padding: 20px 0;">
        <!-- Welcome Message -->
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 48px; margin-bottom: 16px;">👋</div>
            <h2 style="font-size: 24px; font-weight: 600; color: var(--title-color); margin-bottom: 12px;">
                Welcome to Looksharp!
            </h2>
            <p style="font-size: 16px; color: var(--text-color); line-height: 1.6; max-width: 600px; margin: 0 auto;">
                We're excited to have you here! Let's help you build a standout profile that employers will love.
            </p>
        </div>

        <!-- Instructions Section -->
        <div style="background: #f9f9f9; border-radius: 8px; padding: 24px; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 600; color: var(--title-color); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined" style="font-size: 24px; color: var(--primary-color1);">lightbulb</span>
                Quick Start Guide
            </h3>
            <p style="font-size: 14px; color: var(--text-color); margin-bottom: 20px; line-height: 1.6;">
                Add as much detail as possible to your profile so employers can find and match with you. Here's what you should add:
            </p>
            
            <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                <div style="display: flex; align-items: start; gap: 12px; padding: 12px; background: white; border-radius: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--primary-color1); flex-shrink: 0; margin-top: 2px;">person</span>
                    <div>
                        <strong style="color: var(--title-color); font-size: 14px;">About Me</strong>
                        <p style="font-size: 13px; color: var(--text-color); margin: 4px 0 0 0;">Add your bio, location, and personal information</p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: start; gap: 12px; padding: 12px; background: white; border-radius: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--primary-color1); flex-shrink: 0; margin-top: 2px;">school</span>
                    <div>
                        <strong style="color: var(--title-color); font-size: 14px;">Education</strong>
                        <p style="font-size: 13px; color: var(--text-color); margin: 4px 0 0 0;">List your degrees, institutions, and academic achievements</p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: start; gap: 12px; padding: 12px; background: white; border-radius: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--primary-color1); flex-shrink: 0; margin-top: 2px;">work</span>
                    <div>
                        <strong style="color: var(--title-color); font-size: 14px;">Experience</strong>
                        <p style="font-size: 13px; color: var(--text-color); margin: 4px 0 0 0;">Add your work history, internships, and professional experience</p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: start; gap: 12px; padding: 12px; background: white; border-radius: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--primary-color1); flex-shrink: 0; margin-top: 2px;">psychology</span>
                    <div>
                        <strong style="color: var(--title-color); font-size: 14px;">Skills</strong>
                        <p style="font-size: 13px; color: var(--text-color); margin: 4px 0 0 0;">Showcase your technical and soft skills</p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: start; gap: 12px; padding: 12px; background: white; border-radius: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--primary-color1); flex-shrink: 0; margin-top: 2px;">rocket_launch</span>
                    <div>
                        <strong style="color: var(--title-color); font-size: 14px;">Projects & Portfolio</strong>
                        <p style="font-size: 13px; color: var(--text-color); margin: 4px 0 0 0;">Highlight your best work and projects</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Benefits Section -->
        <div style="background: linear-gradient(135deg, var(--primary-color2) 0%, var(--primary-color1) 100%); border-radius: 8px; padding: 20px; margin-bottom: 24px; color: white;">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined" style="font-size: 24px;">star</span>
                Why Complete Your Profile?
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px; line-height: 1.8;">
                <li style="margin-bottom: 8px; display: flex; align-items: start; gap: 8px;">
                    <span class="material-symbols-outlined" style="font-size: 18px; flex-shrink: 0; margin-top: 2px;">check_circle</span>
                    <span>Get matched with relevant employers based on your skills and preferences</span>
                </li>
                <li style="margin-bottom: 8px; display: flex; align-items: start; gap: 8px;">
                    <span class="material-symbols-outlined" style="font-size: 18px; flex-shrink: 0; margin-top: 2px;">check_circle</span>
                    <span>Increase your visibility to employers searching for talent</span>
                </li>
                <li style="display: flex; align-items: start; gap: 8px;">
                    <span class="material-symbols-outlined" style="font-size: 18px; flex-shrink: 0; margin-top: 2px;">check_circle</span>
                    <span>Build trust with employers through a complete and verified profile</span>
                </li>
            </ul>
        </div>

        <!-- Don't Show Again Checkbox -->
        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px;">
            <input type="checkbox" id="dont-show-welcome" style="width: 18px; height: 18px; cursor: pointer;">
            <label for="dont-show-welcome" style="font-size: 14px; color: var(--text-color); cursor: pointer;">
                Don't show this message again
            </label>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <button type="button" onclick="closeWelcomeModal()"
                style="padding: 12px 24px; border: 2px solid #ddd; border-radius: 6px; background: white; color: var(--text-color); cursor: pointer; font-size: 14px; font-weight: 500;">
                Maybe Later
            </button>
            <button type="button" onclick="startBuildingProfile()"
                class="primary-btn1 btn-hover"
                style="padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500;">
                <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle; margin-right: 4px;">edit</span>
                Start Building My Profile
            </button>
        </div>
    </div>
</x-modal>

<script>
function closeWelcomeModal() {
    const dontShow = document.getElementById('dont-show-welcome').checked;
    
    if (dontShow) {
        // Make AJAX call to dismiss modal
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch('{{ route("talent.profile.welcome-modal.dismiss") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('welcome-modal');
            } else {
                closeModal('welcome-modal');
            }
        })
        .catch(error => {
            console.error('Error dismissing welcome modal:', error);
            closeModal('welcome-modal');
        });
    } else {
        closeModal('welcome-modal');
    }
}

function startBuildingProfile() {
    // Scroll to the first editable section (About Me section)
    const aboutSection = document.querySelector('.profile-v2-section');
    if (aboutSection) {
        aboutSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    // Close the modal
    closeWelcomeModal();
    
    // Optionally open the about-me modal after a short delay
    setTimeout(() => {
        const editButton = document.querySelector('[onclick*="about-me-modal"]');
        if (editButton) {
            // Small delay to let scroll complete
            setTimeout(() => {
                openModal('about-me-modal');
            }, 500);
        }
    }, 300);
}
</script>

