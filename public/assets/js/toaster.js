/**
 * Global Toaster Notification System
 * Uses Bootstrap Toast API for consistent notifications across the application
 */

(function() {
    'use strict';

    // Default configuration
    const DEFAULT_DURATION = 5000; // 5 seconds
    const TOAST_CONTAINER_ID = 'global-toast-container';
    const TOAST_POSITION = 'top-end'; // top-start, top-center, top-end, bottom-start, bottom-center, bottom-end

    /**
     * Create toast container if it doesn't exist
     */
    function ensureToastContainer() {
        let container = document.getElementById(TOAST_CONTAINER_ID);
        if (!container) {
            container = document.createElement('div');
            container.id = TOAST_CONTAINER_ID;
            container.className = 'toast-container position-fixed p-3';
            container.setAttribute('style', 'z-index: 9999; top: 0; right: 0;');
            document.body.appendChild(container);
        }
        return container;
    }

    /**
     * Get Bootstrap icon class for toast type
     */
    function getIconClass(type) {
        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };
        return icons[type] || icons.info;
    }

    /**
     * Get Bootstrap background color class for toast type
     */
    function getBgClass(type) {
        const bgClasses = {
            success: 'bg-success',
            error: 'bg-danger',
            warning: 'bg-warning',
            info: 'bg-info'
        };
        return bgClasses[type] || bgClasses.info;
    }

    /**
     * Get text color class for toast type
     */
    function getTextClass(type) {
        const textClasses = {
            success: 'text-white',
            error: 'text-white',
            warning: 'text-dark',
            info: 'text-white'
        };
        return textClasses[type] || textClasses.info;
    }

    /**
     * Main method to show a toast notification
     * @param {string} type - Type of toast: 'success', 'error', 'warning', 'info'
     * @param {string} message - Message to display
     * @param {number} duration - Duration in milliseconds (default: 5000)
     */
    function show(type, message, duration = DEFAULT_DURATION) {
        if (!message) {
            console.warn('Toaster: No message provided');
            return;
        }

        const container = ensureToastContainer();
        const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        
        // Create toast element
        const toastEl = document.createElement('div');
        toastEl.id = toastId;
        toastEl.className = 'toast align-items-center ' + getBgClass(type) + ' ' + getTextClass(type) + ' border-0';
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        toastEl.setAttribute('data-bs-autohide', 'true');
        toastEl.setAttribute('data-bs-delay', duration);

        // Create toast content
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center">
                    <i class="bi ${getIconClass(type)} me-2"></i>
                    <span>${escapeHtml(message)}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        container.appendChild(toastEl);

        // Initialize Bootstrap Toast
        const toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: duration
        });

        // Remove element from DOM after it's hidden
        toastEl.addEventListener('hidden.bs.toast', function() {
            toastEl.remove();
        });

        // Show the toast
        toast.show();
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Show success toast
     */
    function success(message, duration) {
        show('success', message, duration);
    }

    /**
     * Show error toast
     */
    function error(message, duration) {
        show('error', message, duration);
    }

    /**
     * Show warning toast
     */
    function warning(message, duration) {
        show('warning', message, duration);
    }

    /**
     * Show info toast
     */
    function info(message, duration) {
        show('info', message, duration);
    }

    // Expose Toaster globally
    window.Toaster = {
        show: show,
        success: success,
        error: error,
        warning: warning,
        info: info
    };

    // Initialize container when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensureToastContainer);
    } else {
        ensureToastContainer();
    }
})();

