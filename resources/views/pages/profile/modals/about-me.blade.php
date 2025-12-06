<x-modal id="about-me-modal" title="Edit About Me" size="medium">
    <form id="about-me-form" onsubmit="submitProfileForm(event, 'about-me-form', '{{ route('talent.profile.about-me.update') }}', 'about-me-modal')">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Headline
            </label>
            <input type="text" name="headline" id="about-me-headline" value="{{ $profile->headline }}" 
                placeholder="e.g., Software Engineer | Full Stack Developer"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; font-family: inherit;">
            <p style="margin-top: 5px; font-size: 12px; color: #666;">A brief professional headline that appears below your name</p>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Public URL
            </label>
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="color: #666; font-size: 14px;">{{ url('/') }}/profile/</span>
                <input type="text" name="public_url" id="about-me-public-url" value="{{ $profile->public_url }}" 
                    placeholder="your-unique-slug"
                    style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; font-family: inherit;">
            </div>
            <p style="margin-top: 5px; font-size: 12px; color: #666;">Create a unique URL for your public profile (letters, numbers, and hyphens only)</p>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Bio / About Me
            </label>
            <textarea name="bio" id="about-me-bio" rows="6" placeholder="Tell employers a little about yourself..."
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical; font-family: inherit;">{{ $profile->bio }}</textarea>
            <p style="margin-top: 5px; font-size: 12px; color: #666;">Max 1000 characters</p>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 25px;">
            <button type="button" onclick="closeModal('about-me-modal')" 
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

