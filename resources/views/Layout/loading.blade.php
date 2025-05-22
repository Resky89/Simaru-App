<!-- Full Screen Loading Animation -->
<div id="loading-screen" class="fixed inset-0 z-50 flex items-center justify-center bg-white hidden opacity-0">
    <div class="text-center">
        <div class="animate-pulse">
            <img src="{{ asset('images/Logo_RS_UMMI.png') }}" alt="RS UMMI Logo" class="mx-auto w-64 h-auto">
        </div>
        <div class="mt-4">
            <div class="animate-spin inline-block w-8 h-8 border-4 border-blue-800 border-t-transparent rounded-full"></div>
        </div>
        <div class="mt-2 text-blue-800 font-semibold">Loading...</div>
    </div>
</div>

<!-- JavaScript to control the loading screen -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide loading screen on initial page load - it's already hidden by default now

        // Show loading screen when clicking on links that navigate to new pages
        document.addEventListener('click', function(e) {
            // Find if the click was on a link or inside a link
            const link = e.target.closest('a');
            if (!link) return; // Not a link, exit

            // Check if this is a navigation link (not a download, anchor, javascript, or external link)
            const href = link.getAttribute('href');
            const isNavLink = href &&
                             !href.startsWith('#') &&
                             !href.startsWith('javascript:') &&
                             !link.hasAttribute('download') &&
                             !link.getAttribute('target') &&
                             (href.startsWith('/') || href.includes(window.location.hostname));

            // If it's a navigation link, show the loading screen
            if (isNavLink && !e.ctrlKey && !e.metaKey) {
                showLoadingScreen();
            }
        });

        // Show loading screen when submitting forms
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM' && !e.target.hasAttribute('data-no-loading')) {
                showLoadingScreen();
            }
        });

        // Handle browser back/forward navigation
        window.addEventListener('popstate', function() {
            showLoadingScreen();
        });
    });

    // Function to show the loading screen
    function showLoadingScreen() {
        const loadingScreen = document.getElementById('loading-screen');
        loadingScreen.classList.remove('hidden');
        // Use a small timeout to allow the element to be in the DOM before adding opacity
        setTimeout(function() {
            loadingScreen.classList.remove('opacity-0');
        }, 10);
    }

    // Function to hide the loading screen
    function hideLoadingScreen() {
        const loadingScreen = document.getElementById('loading-screen');
        loadingScreen.classList.add('opacity-0');
        setTimeout(function() {
            loadingScreen.classList.add('hidden');
        }, 500); // Match the transition duration
    }

    // Hide loading when page has loaded (in case it was shown during navigation)
    window.addEventListener('load', function() {
        setTimeout(hideLoadingScreen, 300);
    });

    // For AJAX requests, you can manually control the loading screen:
    // Example: document.addEventListener('turbolinks:click', showLoadingScreen);
    // Example: document.addEventListener('turbolinks:load', hideLoadingScreen);

    // If using Laravel with Livewire, uncomment these lines:
    // document.addEventListener('livewire:load', function() {
    //     Livewire.hook('message.sent', () => showLoadingScreen());
    //     Livewire.hook('message.received', () => hideLoadingScreen());
    // });
</script>

<style>
    #loading-screen {
        transition: opacity 0.5s ease-out;
    }

    /* Animation for pulsing effect */
    @keyframes gentle-pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .animate-pulse {
        animation: gentle-pulse 2s infinite ease-in-out;
    }
</style>
