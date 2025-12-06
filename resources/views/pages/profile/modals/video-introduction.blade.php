<x-modal id="video-introduction-modal" title="Add Video Introduction" size="medium">
    <form id="video-introduction-form" onsubmit="submitVideoIntroduction(event, 'video-introduction-form', '{{ route('talent.profile.video-introduction.update') }}', 'video-introduction-modal')">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--title-color);">
                Video URL (YouTube or Vimeo)
            </label>
            <input type="url" name="video_introduction" id="video-introduction-url" 
                placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/..."
                value="{{ $profile->video_introduction }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; font-family: inherit;">
            <p style="margin-top: 5px; font-size: 12px; color: #666;">
                Paste a YouTube or Vimeo link. Examples:<br>
                • https://www.youtube.com/watch?v=dQw4w9WgXcQ<br>
                • https://youtu.be/dQw4w9WgXcQ<br>
                • https://vimeo.com/123456789
            </p>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 25px;">
            <button type="button" onclick="closeModal('video-introduction-modal')" 
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

