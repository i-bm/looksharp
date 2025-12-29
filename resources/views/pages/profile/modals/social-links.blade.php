<x-modal id="social-links-modal" title="Edit Portfolio & Social Links" size="medium">
    <form id="social-links-form" onsubmit="submitProfileForm(event, 'social-links-form', '{{ route('talent.profile.social-links.update') }}', 'social-links-modal')">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                LinkedIn URL
            </label>
            <input type="url" name="linkedin_url" id="linkedin-url" value="{{ $profile->linkedin_url }}"
                placeholder="https://linkedin.com/in/username"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                GitHub URL
            </label>
            <input type="url" name="github_url" id="github-url" value="{{ $profile->github_url }}"
                placeholder="https://github.com/username"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
        </div>

        {{-- <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Behance URL
            </label>
            <input type="url" name="behance_url" id="behance-url" value="{{ $profile->behance_url }}"
                placeholder="https://behance.net/username"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
        </div> --}}

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Portfolio URL
            </label>
            <input type="url" name="portfolio_url" id="portfolio-url" value="{{ $profile->portfolio_url }}"
                placeholder="https://yourportfolio.com"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
        </div>



        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Twitter/X URL
            </label>
            <input type="url" name="twitter_url" id="twitter-url" value="{{ $profile->twitter_url }}"
                placeholder="https://x.com/username"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 25px;">
            <button type="button" onclick="closeModal('social-links-modal')"
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

