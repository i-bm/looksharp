<x-modal id="projects-modal" title="Edit Projects and Portfolio" size="large">
    <div>
        <!-- Existing Projects -->
        @if($profile->projects->count() > 0)
        <div class="mb-30">
            <h3 class="modal-section-title">Your Projects ({{ $profile->projects->count() }})</h3>
            @foreach($profile->projects as $project)
            <div class="modal-item">
                <div class="modal-item-content">
                    <strong>{{ $project->title }}</strong>
                    @if($project->project_type)
                    <br><span class="modal-item-meta">{{ $project->project_type }}</span>
                    @endif
                    @if($project->start_date)
                    <br><span class="modal-item-meta">
                        <i class="bi bi-calendar3"></i>
                        {{ $project->start_date->format('M Y') }}
                        @if($project->end_date)
                        - {{ $project->end_date->format('M Y') }}
                        @endif
                    </span>
                    @endif
                    @if($project->project_url)
                    <br><a href="{{ $project->project_url }}" target="_blank"
                        style="color: #2196F3; font-size: 14px; text-decoration: none;">
                        <i class="bi bi-box-arrow-up-right"></i> View Project
                    </a>
                    @endif
                    @if($project->description)
                    <br><span style="color: #666; font-size: 14px; margin-top: 4px; display: block;">{{ Str::limit($project->description, 100) }}</span>
                    @endif
                </div>
                <button type="button" onclick="deleteProject('{{ $project->id }}')" class="modal-btn-remove">
                    Remove
                </button>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Add Project Form -->
        <form id="add-project-form" onsubmit="submitAddProject(event)">
            @csrf
            <h3 class="modal-section-title">{{ $profile->projects->count() > 0 ? 'Add Another Project' : 'Add Project' }}</h3>

            <div class="modal-form-field">
                <label class="modal-label">
                    Project Title <span class="required-indicator">*</span>
                </label>
                <input type="text" name="title" id="project-title" required
                    placeholder="e.g., E-commerce Website, Mobile App"
                    class="modal-input">
            </div>

            <div class="modal-form-field">
                <label class="modal-label">
                    Project Type
                </label>
                <select name="project_type" id="project-type" class="modal-input">
                    <option value="">Select Type</option>
                    <option value="Web Application">Web Application</option>
                    <option value="Mobile App">Mobile App</option>
                    <option value="Desktop Application">Desktop Application</option>
                    <option value="Design Project">Design Project</option>
                    <option value="Research Project">Research Project</option>
                    <option value="Open Source">Open Source</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="modal-form-field">
                <label class="modal-label">
                    Description
                </label>
                <textarea name="description" id="project-description" rows="4"
                    placeholder="Describe your project, what you built, technologies used, and key achievements..."
                    class="modal-textarea"></textarea>
            </div>

            <div class="modal-form-field">
                <label class="modal-label">
                    Project URL (Optional)
                </label>
                <input type="url" name="project_url" id="project-url"
                    placeholder="https://example.com or https://github.com/username/project"
                    class="modal-input">
            </div>

            <div class="modal-form-field">
                <label class="modal-label">
                    Technologies Used (Optional)
                </label>
                <input type="text" name="technologies" id="project-technologies"
                    placeholder="e.g., React, Node.js, MongoDB (comma-separated)"
                    class="modal-input">
                <small style="color: #666; font-size: 12px; margin-top: 4px; display: block;">
                    Enter technologies separated by commas
                </small>
            </div>

            @php
            $currentYear = (int) date('Y');
            $yearOptions = getYearOptions($currentYear - 20, $currentYear + 5);
            @endphp

            <div style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'Start Date (Optional)',
                'required' => false,
                'prefix' => 'project_start_date',
                'dayValue' => '',
                'monthValue' => '',
                'yearValue' => '',
                'yearOptions' => $yearOptions,
                'errorKey' => 'start_date'
                ])
            </div>

            <div style="margin-bottom: 20px;">
                @include('pages.profile.partials.date-selector', [
                'label' => 'End Date (Optional)',
                'required' => false,
                'prefix' => 'project_end_date',
                'dayValue' => '',
                'monthValue' => '',
                'yearValue' => '',
                'yearOptions' => $yearOptions,
                'errorKey' => 'end_date'
                ])
            </div>

            <div class="modal-form-field">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_featured" id="project-is-featured" value="1">
                    <span class="checkbox-label-text">Mark as featured project</span>
                </label>
            </div>

            <div class="modal-form-field">
                <label class="modal-label">
                    Project Image (Optional)
                </label>
                <input type="file" name="image" id="project-image" accept="image/*"
                    class="modal-input">
                <small style="color: #666; font-size: 12px; margin-top: 4px; display: block;">
                    Upload a screenshot or image of your project (max 5MB)
                </small>
            </div>

            <div class="modal-form-actions">
                <button type="button" onclick="closeModal('projects-modal')" class="modal-btn-close">
                    Close
                </button>
                <button type="submit" class="primary-btn1 btn-hover" style="padding: 10px 20px; font-size: 14px;">
                    <i class="bi bi-plus-circle"></i> Add Project
                </button>
            </div>
        </form>
    </div>
</x-modal>

