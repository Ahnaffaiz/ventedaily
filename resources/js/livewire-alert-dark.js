/**
 * LivewireAlert Theme Manager
 *
 * A simplified, direct approach to managing SweetAlert2 themes in Livewire.
 */

(function() {
    // The global theme state - we'll use this for consistent theme application
    let currentTheme = null;

    // Function to detect the current app theme
    function getCurrentTheme() {
        // For system preference
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

        // For app-specific theme indicators
        const htmlHasDarkClass = document.documentElement.classList.contains('dark');
        const bodyHasDarkClass = document.body.classList.contains('dark');
        const htmlDataTheme = document.documentElement.getAttribute('data-theme') === 'dark';
        const bodyDataTheme = document.body.getAttribute('data-theme') === 'dark';
        const localStorageDark = localStorage.getItem('theme') === 'dark';

        // Return 'dark' if any dark indicator is present, otherwise 'light'
        return (prefersDark || htmlHasDarkClass || bodyHasDarkClass || htmlDataTheme || bodyDataTheme || localStorageDark)
            ? 'dark'
            : 'light';
    }

    // Apply CSS styles directly to prevent any style conflicts
    function injectCSS() {
        // Remove any existing style tag
        const existingStyle = document.getElementById('swal-theme-styles');
        if (existingStyle) {
            existingStyle.remove();
        }

        // Create new style element
        const style = document.createElement('style');
        style.id = 'swal-theme-styles';

        // Define the CSS for both themes
        style.textContent = `
            /* Light theme - SweetAlert default, no additional styling needed */

            /* Dark theme styles */
            body.swal2-theme-dark .swal2-popup {
                background-color: #1e1e2d;
                color: #e1e1e1;
                border: 1px solid #3f3f5f;
            }

            body.swal2-theme-dark .swal2-title,
            body.swal2-theme-dark .swal2-html-container,
            body.swal2-theme-dark .swal2-confirm,
            body.swal2-theme-dark .swal2-cancel {
                color: #e1e1e1;
            }

            body.swal2-theme-dark .swal2-input,
            body.swal2-theme-dark .swal2-textarea,
            body.swal2-theme-dark .swal2-select {
                background-color: #2b2b40;
                color: #e1e1e1;
                border-color: #3f3f5f;
            }

            /* Add shadow for popups when no backdrop is present */
            body.swal2-theme-dark .swal2-container:not(.swal2-backdrop-show) .swal2-popup {
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
            }

            /* Make container transparent for regular alerts */
            body .swal2-container:not(.swal2-backdrop-show) {
                background-color: transparent !important;
            }

            /* Keep backdrop for question dialogs */
            body .swal2-container.swal2-backdrop-show {
                background-color: rgba(0, 0, 0, 0.4) !important;
            }
        `;

        // Add the style to the head
        document.head.appendChild(style);
    }

    // Apply the current theme to SweetAlert
    function applyTheme() {
        // Get current theme
        currentTheme = getCurrentTheme();

        // Apply theme to the body for consistent styling
        if (currentTheme === 'dark') {
            document.body.classList.add('swal2-theme-dark');
        } else {
            document.body.classList.remove('swal2-theme-dark');
        }
    }

    // Override SweetAlert2 initialization
    function patchSweetAlert() {
        if (!window.Swal || window.Swal._themePatched) {
            return;
        }

        // Mark as patched to prevent multiple patches
        window.Swal._themePatched = true;

        // Store original methods
        const originalFire = window.Swal.fire;

        // Override fire method
        window.Swal.fire = function() {
            // Get arguments
            const args = Array.from(arguments);
            let params = typeof args[0] === 'object' ? args[0] : {};

            // Set backdrop only for question dialogs by default
            if (!params.hasOwnProperty('backdrop')) {
                params.backdrop = false;
            }

            // Question dialogs should have backdrop
            if (params.icon === 'question' || params.type === 'question') {
                params.backdrop = true;
                params.customClass = params.customClass || {};
                params.customClass.container = 'swal2-backdrop-show ' + (params.customClass.container || '');
            }

            // Update first argument
            if (typeof args[0] === 'object') {
                args[0] = params;
            }

            // Apply theme before showing alert
            applyTheme();

            // Call original method
            return originalFire.apply(this, args);
        };

        // Also patch mixin
        if (window.Swal.mixin) {
            const originalMixin = window.Swal.mixin;

            window.Swal.mixin = function(params) {
                // Default backdrop false
                if (!params.hasOwnProperty('backdrop')) {
                    params.backdrop = false;
                }

                const instance = originalMixin.call(this, params);

                // Patch instance fire method
                if (instance.fire) {
                    const originalInstanceFire = instance.fire;

                    instance.fire = function() {
                        // Apply theme before showing alert
                        applyTheme();

                        // Handle question dialog backdrop
                        const args = Array.from(arguments);
                        let localParams = typeof args[0] === 'object' ? args[0] : {};

                        if ((localParams.icon === 'question' || localParams.type === 'question')
                            && !localParams.hasOwnProperty('backdrop')) {
                            localParams.backdrop = true;
                            localParams.customClass = localParams.customClass || {};
                            localParams.customClass.container = 'swal2-backdrop-show ' + (localParams.customClass.container || '');

                            if (typeof args[0] === 'object') {
                                args[0] = localParams;
                            }
                        }

                        return originalInstanceFire.apply(this, args);
                    };
                }

                return instance;
            };
        }
    }

    // Watch for theme changes
    function watchThemeChanges() {
        // Watch for system preference changes
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
                applyTheme();
            });
        }

        // Watch for app theme changes
        const observer = new MutationObserver(function() {
            const newTheme = getCurrentTheme();
            if (newTheme !== currentTheme) {
                applyTheme();
            }
        });

        // Observe html and body for class/attribute changes
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class', 'data-theme']
        });

        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['class', 'data-theme']
        });
    }

    // Livewire specific handling
    function setupLivewireHandlers() {
        // For regular Livewire
        if (window.Livewire) {
            window.Livewire.hook('message.processed', function() {
                patchSweetAlert();
                applyTheme();
            });
        }

        // For navigation
        document.addEventListener('livewire:navigated', function() {
            patchSweetAlert();
            applyTheme();
        });

        // For LivewireAlert events
        document.addEventListener('livewire-alert:show', function() {
            applyTheme();
        });

        document.addEventListener('livewire-alert:toast', function() {
            applyTheme();
        });
    }

    // Main initialization
    function init() {
        injectCSS();
        patchSweetAlert();
        applyTheme();
        watchThemeChanges();
        setupLivewireHandlers();
    }

    // Initialize when document is ready or immediately if already loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Make available globally so it can be called manually if needed
    window.refreshSweetAlertTheme = applyTheme;
})();
