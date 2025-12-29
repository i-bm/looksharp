<x-modal id="languages-modal" title="Edit Languages" size="large">
    <div>
        <!-- Existing Languages -->
        @if($profile->languages->count() > 0)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 15px; color: var(--title-color);">Your Languages ({{ $profile->languages->count() }})</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach($profile->languages as $language)
                <div
                    style="background: #f9f9f9; padding: 10px 15px; border-radius: 20px; display: flex; align-items: center; gap: 10px;">
                    <span><strong>{{ $language->language_name }}</strong> - {{ ucfirst($language->proficiency_level->value) }}</span>
                    <button type="button" onclick="deleteLanguage('{{ $language->id }}')"
                        style="background: #f44336; color: white; border: none; padding: 2px 8px; border-radius: 12px; cursor: pointer; font-size: 12px; margin-left: 5px;">
                        ×
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Add Language Form -->
        <form id="add-language-form" onsubmit="submitAddLanguage(event)">
            @csrf
            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 20px; color: var(--title-color);">{{ $profile->languages->count() > 0 ? 'Add Another Language' : 'Add Languages' }}</h3>

            <div class="row g-4">
                <div class="col-md-6 ">
                    <label class="" style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Language <span style="color: #F53003;">*</span>
                    </label>
                    <input type="text" name="language_name" id="language-name" required
                        placeholder="e.g., English, Twi, French"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <div class="col-md-6">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                        Proficiency Level <span style="color: #F53003;">*</span>
                    </label>
                    <select name="proficiency_level" id="language-proficiency-level" required
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
                <button type="button" onclick="closeModal('languages-modal')"
                    style="padding: 10px 20px; border: 2px solid #ddd; border-radius: 4px; background: white; color: var(--text-color); cursor: pointer; font-size: 14px;">
                    Close
                </button>
                <button type="submit" class="primary-btn1 btn-hover"
                    style="padding: 10px 20px; border: none; cursor: pointer; font-size: 14px;">
                    <i class="bi bi-plus-circle"></i> Add Language
                </button>
            </div>
        </form>
    </div>
</x-modal>

