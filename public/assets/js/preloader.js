// Set page loading state immediately
document.documentElement.classList.add('page-loading');

// Wait for window load or timeout after 4 seconds
window.addEventListener('load', function() {
    removePreloader();
});

// Failsafe - remove preloader after 4 seconds no matter what
setTimeout(function() {
    removePreloader();
}, 3000);

// Add Livewire navigation event listeners
if (typeof window.Livewire !== 'undefined') {
    // Listen for Livewire page transition start
    document.addEventListener('livewire:navigating', function() {
        // Show preloader when navigation starts
        document.documentElement.classList.add('page-loading');
        const preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.classList.remove('hidden');
            preloader.classList.remove('opacity-0');
            preloader.classList.add('opacity-100');
        }
    });

    // Listen for Livewire page transition end
    document.addEventListener('livewire:navigated', function() {
        // Remove preloader when navigation completes
        removePreloader();
    });
}

// Function to remove preloader
function removePreloader() {
    // Remove the page loading class
    document.documentElement.classList.remove('page-loading');

    // Find the preloader
    var preloader = document.getElementById('preloader');

    if (preloader) {
        // First make it transparent by adding opacity-0 class and removing opacity-100
        preloader.classList.remove('opacity-100');
        preloader.classList.add('opacity-0');

        // Then after transition (500ms), remove it completely
        setTimeout(function() {
            preloader.classList.add('hidden');

            // Try to remove it from DOM entirely as a last resort
            if (preloader.parentNode) {
                preloader.parentNode.removeChild(preloader);
            }
        }, 500);
    }
}

// Make function globally accessible
window.removePreloader = removePreloader;
