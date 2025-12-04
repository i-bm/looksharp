<h2 style="font-family: var(--font-bricolageGrotesque); font-weight: 600; font-size: 24px; margin-bottom: 30px;">
    Skills
</h2>

<!-- Existing Skills -->
@if($skills->count() > 0)
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 18px; margin-bottom: 15px;">Your Skills ({{ $skills->count() }})</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
            @foreach($skills as $skill)
                <div style="background: #f9f9f9; padding: 10px 15px; border-radius: 20px; display: flex; align-items: center; gap: 10px;">
                    <span><strong>{{ $skill->skill_name }}</strong> - {{ ucfirst($skill->proficiency_level->value) }}</span>
                    <form method="POST" action="{{ route('profile.skill.remove', ['id' => $skill->id]) }}" style="display: inline; margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Remove this skill?')" 
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
<form method="POST" action="{{ route('profile.build.save', ['step' => 3]) }}">
    @csrf

    <h3 style="font-size: 18px; margin-bottom: 20px;">{{ $skills->count() > 0 ? 'Add Another Skill' : 'Add Skills' }}</h3>
    <p style="color: #666; margin-bottom: 20px;">Add at least 3 skills to improve your profile completeness.</p>

    <!-- Skill Name -->
    <div style="margin-bottom: 20px;">
        <label for="skill_name" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Skill Name <span style="color: red;">*</span>
        </label>
        <input type="text" 
               name="skill_name" 
               id="skill_name" 
               value="{{ old('skill_name') }}" 
               required
               placeholder="e.g., JavaScript, Project Management, Communication"
               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        @error('skill_name')
            <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Proficiency Level -->
    <div style="margin-bottom: 30px;">
        <label for="proficiency_level" style="display: block; margin-bottom: 8px; font-weight: 500;">
            Proficiency Level <span style="color: red;">*</span>
        </label>
        <select name="proficiency_level" 
                id="proficiency_level" 
                required
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            <option value="">Select Proficiency Level</option>
            <option value="beginner" {{ old('proficiency_level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
            <option value="intermediate" {{ old('proficiency_level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
            <option value="advanced" {{ old('proficiency_level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
            <option value="expert" {{ old('proficiency_level') == 'expert' ? 'selected' : '' }}>Expert</option>
        </select>
        @error('proficiency_level')
            <span style="color: red; font-size: 14px;">{{ $message }}</span>
        @enderror
    </div>

    <!-- Navigation Buttons -->
    <div style="display: flex; justify-content: space-between; gap: 20px;">
        <a href="{{ route('profile.build.step', ['step' => 2]) }}" 
           class="primary-btn1 btn-hover" 
           style="text-decoration: none; padding: 12px 24px; display: inline-block;">
            Previous
        </a>
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="primary-btn1 btn-hover" style="padding: 12px 24px;">
                Add Skill
            </button>
            <a href="{{ route('profile.build.step', ['step' => 4]) }}" 
               class="primary-btn1 btn-hover" 
               style="text-decoration: none; padding: 12px 24px; display: inline-block; {{ $skills->count() < 3 ? 'opacity: 0.7;' : '' }}">
                Continue to Verification
            </a>
        </div>
    </div>
</form>

