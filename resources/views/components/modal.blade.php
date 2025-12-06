@props(['id', 'title', 'size' => 'medium'])

@php
$sizeClasses = [
'small' => 'max-w-md',
'medium' => 'max-w-2xl',
'large' => 'max-w-4xl',
];
$modalSize = $sizeClasses[$size] ?? $sizeClasses['medium'];
@endphp

<!-- Modal Backdrop -->
<div id="{{ $id }}-backdrop" class="modal-backdrop"></div>

<!-- Modal -->
<div id="{{ $id }}" class="profile-modal">
    <div class="modal-dialog" style="max-width: 800px;">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h3 class="modal-title">
                    {{ $title }}
                </h3>
                <button type="button" class="modal-close" onclick="closeModal('{{ $id }}')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>