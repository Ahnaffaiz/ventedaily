/**
 * SweetAlert2 Dark Mode Configuration
 * This file configures SweetAlert2 to automatically adapt to dark mode
 * based on system preferences or user settings.
 */

// Function to detect if dark mode is enabled
function isDarkMode() {
    // Check for system-level dark mode preference
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return true;
    }

    // Check for site-specific dark mode (if you have a class on the HTML/body element)
    if (document.documentElement.classList.contains('dark') ||
        document.body.classList.contains('dark') ||
        document.documentElement.getAttribute('data-theme') === 'dark' ||
        document.body.getAttribute('data-theme') === 'dark') {
        return true;
    }

    return false;
}

// Configure SweetAlert2 with dark mode support
document.addEventListener('DOMContentLoaded', function() {
    // Only proceed if Swal is defined
    if (typeof Swal === 'undefined') {
        console.warn('SweetAlert2 is not loaded yet');
        return;
    }

    // Set up a global SweetAlert2 configuration
    window.SweetAlertConfig = {
        // Default configuration that works well for both light and dark modes
        backdrop: `rgba(0,0,0,0.4)`,
        showCloseButton: true,

        // Function to apply proper theming based on current mode
        didOpen: (popup) => {
            // Apply dark mode if detected
            applyThemeToPopup(popup);
        },

        // Make sure all toasts and notifications also use dark mode
        didRender: (popup) => {
            // This catches toast notifications that might be created differently
            applyThemeToPopup(popup);
        }
    };

    // Function to apply theme to any SweetAlert popup
    function applyThemeToPopup(popup) {
        if (isDarkMode()) {
            Swal.getPopup().classList.add('swal2-dark');

            // Also add class to toast containers if they exist
            const allToasts = document.querySelectorAll('.swal2-container');
            allToasts.forEach(toast => {
                toast.classList.add('swal2-dark-container');
            });
        } else {
            Swal.getPopup().classList.remove('swal2-dark');

            // Remove class from toast containers
            const allToasts = document.querySelectorAll('.swal2-container');
            allToasts.forEach(toast => {
                toast.classList.remove('swal2-dark-container');
            });
        }
    }

    // Add comprehensive CSS for dark mode SweetAlert
    const style = document.createElement('style');
    style.textContent = `
        /* Dark mode for the popup container */
        .swal2-dark-container {
            background-color: rgba(0, 0, 0, 0.6) !important;
        }

        /* Dark mode for the popup itself */
        .swal2-dark {
            background-color: #1e1e2d !important;
            color: #e1e1e1 !important;
            border: 1px solid #3f3f5f !important;
        }

        /* Text elements */
        .swal2-dark .swal2-title,
        .swal2-dark .swal2-html-container,
        .swal2-dark .swal2-content {
            color: #e1e1e1 !important;
        }

        /* Form inputs */
        .swal2-dark .swal2-input,
        .swal2-dark .swal2-file,
        .swal2-dark .swal2-textarea,
        .swal2-dark .swal2-select,
        .swal2-dark .swal2-range {
            background-color: #2b2b40 !important;
            color: #e1e1e1 !important;
            border-color: #3f3f5f !important;
        }

        .swal2-dark .swal2-radio,
        .swal2-dark .swal2-checkbox {
            color: #e1e1e1 !important;
        }

        /* Validation message */
        .swal2-dark .swal2-validation-message {
            background-color: #2b2b40 !important;
            color: #f27474 !important;
        }

        /* Footer */
        .swal2-dark .swal2-footer {
            border-top-color: #3f3f5f !important;
            color: #e1e1e1 !important;
        }

        /* Progress bar */
        .swal2-dark .swal2-timer-progress-bar {
            background-color: rgba(255, 255, 255, 0.3) !important;
        }

        /* Icon colors */
        .swal2-dark .swal2-icon.swal2-question {
            border-color: #87adbd !important;
            color: #87adbd !important;
        }

        .swal2-dark .swal2-icon.swal2-warning {
            border-color: #f8bb86 !important;
            color: #f8bb86 !important;
        }

        .swal2-dark .swal2-icon.swal2-info {
            border-color: #3fc3ee !important;
            color: #3fc3ee !important;
        }

        .swal2-dark .swal2-icon.swal2-success {
            border-color: #a5dc86 !important;
            color: #a5dc86 !important;
        }

        .swal2-dark .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: #a5dc86 !important;
        }

        .swal2-dark .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(165, 220, 134, 0.3) !important;
        }

        .swal2-dark .swal2-icon.swal2-error {
            border-color: #f27474 !important;
            color: #f27474 !important;
        }

        .swal2-dark .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            background-color: #f27474 !important;
        }

        /* Button colors */
        .swal2-dark .swal2-confirm.swal2-styled,
        .swal2-dark .swal2-deny.swal2-styled,
        .swal2-dark .swal2-cancel.swal2-styled {
            background-color: #2b2b40 !important;
            color: #e1e1e1 !important;
            border: 1px solid #3f3f5f !important;
        }

        .swal2-dark .swal2-confirm.swal2-styled:focus,
        .swal2-dark .swal2-deny.swal2-styled:focus,
        .swal2-dark .swal2-cancel.swal2-styled:focus {
            box-shadow: 0 0 0 3px rgba(100, 150, 200, 0.4) !important;
        }

        /* Toast notifications */
        .swal2-dark.swal2-toast {
            background-color: #1e1e2d !important;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5) !important;
        }

        /* Question dialog for 'await' confirmations */
        .swal2-dark.swal2-modal[role="dialog"] {
            background-color: #1e1e2d !important;
            color: #e1e1e1 !important;
        }
    `;
    document.head.appendChild(style);

    // Set up a listener to watch for theme changes
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            // If there's an open SweetAlert dialog, update its theme
            if (Swal.isVisible()) {
                applyThemeToPopup();
            }
        });
    }

    // Handle toast notifications by overriding Swal.fire
    const originalSwalFire = Swal.fire;
    Swal.fire = function() {
        // Get the arguments provided to Swal.fire
        const args = Array.from(arguments);

        // If the first argument is an object (typical usage), merge with our config
        if (typeof args[0] === 'object') {
            args[0] = { ...window.SweetAlertConfig, ...args[0] };
        }

        // Call the original Swal.fire with our merged configuration
        return originalSwalFire.apply(this, args);
    };

    // Handle toast specific calls
    const originalSwalToast = Swal.mixin;
    Swal.mixin = function(params) {
        // For toast notifications, make sure we apply our dark theme
        if (params && params.toast) {
            params = { ...window.SweetAlertConfig, ...params };
        }
        return originalSwalToast.call(this, params);
    };

    // Handle Livewire Alerts specifically if it's a known pattern in your app
    document.addEventListener('livewire:load', function() {
        if (window.livewire) {
            // Listen for notification events that might be triggered by Livewire
            window.livewire.on('showAlert', (data) => {
                if (Swal.isVisible()) {
                    applyThemeToPopup();
                }
            });

            window.livewire.on('showNotification', (data) => {
                if (Swal.isVisible()) {
                    applyThemeToPopup();
                }
            });
        }
    });

    // Add class to any SweetAlert popups that might be rendered by direct DOM manipulation
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                for (let i = 0; i < mutation.addedNodes.length; i++) {
                    const node = mutation.addedNodes[i];
                    if (node.classList &&
                        (node.classList.contains('swal2-container') ||
                         node.classList.contains('swal2-popup'))) {
                        if (isDarkMode()) {
                            if (node.classList.contains('swal2-container')) {
                                node.classList.add('swal2-dark-container');
                            }
                            if (node.classList.contains('swal2-popup')) {
                                node.classList.add('swal2-dark');
                            }
                            // Also check for child popups
                            const popups = node.querySelectorAll('.swal2-popup');
                            popups.forEach(popup => {
                                popup.classList.add('swal2-dark');
                            });
                        }
                    }
                }
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Export the isDarkMode function for use elsewhere if needed
export { isDarkMode };
