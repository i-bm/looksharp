<x-modal id="fun-fact-modal" title="Edit Fun Fact" size="medium">
    <form id="fun-fact-form" onsubmit="submitProfileForm(event, 'fun-fact-form', '{{ route('talent.profile.fun-fact.update') }}', 'fun-fact-modal')">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Fun Fact
            </label>
            <textarea name="fun_fact" id="fun-fact-text" rows="5" placeholder="What's something that most people don't know about you? Tell the world what makes you unique."
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical; font-family: inherit;">{{ $profile->fun_fact }}</textarea>
            <p style="margin-top: 5px; font-size: 12px; color: #666;">Max 1000 characters</p>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 25px;">
            <button type="button" onclick="closeModal('fun-fact-modal')" 
                style="padding: 10px 20px; border: 2px solid #ddd; border-radius: 4px; background: white; color: var(--text-color); cursor: pointer; font-size: 14px;">
                Cancel
            </button>
            <button type="submit" class="primary-btn1 btn-hover"
                style="padding: 10px 20px; border: none; cursor: pointer; font-size: 14px;">
                <i class="bi bi-check-lg"></i> Save Changes
            </button>
        </div>
    </form>
</x-modal>

