<x-modal id="skills-modal" title="Edit Skills" size="large">
    <div>
        <!-- Existing Skills -->
        @if($profile->skills->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px; color: var(--title-color);">Your Skills ({{ $profile->skills->count() }})</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach($profile->skills as $skill)
                <div
                    style="background: #f9f9f9; padding: 10px 15px; border-radius: 20px; display: flex; align-items: center; gap: 10px;">
                    <span><strong>{{ $skill->skill_name }}</strong> - {{ ucfirst($skill->proficiency_level->value) }}</span>
                    <button type="button" onclick="deleteSkill('{{ $skill->id }}')"
                        style="background: #f44336; color: white; border: none; padding: 2px 8px; border-radius: 12px; cursor: pointer; font-size: 12px; margin-left: 5px;">
                        ×
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Add Skill Form -->
        <form id="add-skill-form" onsubmit="submitAddSkill(event)">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px; color: var(--title-color);">{{ $profile->skills->count() > 0 ? 'Add Another Skill' : 'Add Skills' }}</h3>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Skill Name <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="skill_name" id="skill-name" required
                        placeholder="e.g., JavaScript, Project Management, Communication"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Proficiency Level <span style="color: #F53003;">*</span>
                    </label>
                    <select name="proficiency_level" id="proficiency-level" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="">Select Level</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                        <option value="expert">Expert</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 25px;">
                <button type="button" onclick="closeModal('skills-modal')" 
                    style="padding: 10px 20px; border: 2px solid #ddd; border-radius: 4px; background: white; color: var(--text-color); cursor: pointer; font-size: 14px;">
                    Close
                </button>
                <button type="submit" class="primary-btn1 btn-hover"
                    style="padding: 10px 20px; border: none; cursor: pointer; font-size: 14px;">
                    <i class="bi bi-plus-circle"></i> Add Skill
                </button>
            </div>
        </form>
    </div>
</x-modal>

