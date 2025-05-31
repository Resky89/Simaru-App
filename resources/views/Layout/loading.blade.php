<!-- Full Screen Loading Animation -->
<div id="loading-screen" class="fixed inset-0 z-50 flex items-center justify-center bg-white hidden opacity-0">
    <div class="text-center p-8 rounded-xl">
        <div class="animate-pulse">
            <img src="{{ asset('images/Logo_RS_UMMI.png') }}" alt="RS UMMI Logo" class="mx-auto w-64 h-auto">
        </div>
        <div class="mt-6">
            <div class="animate-spin inline-block w-10 h-10 border-4 border-[#213268] border-t-transparent rounded-full shadow-md"></div>
        </div>
        <div class="mt-3 text-[#213268] font-semibold text-lg">Memuat...</div>
    </div>
</div>

<!-- JavaScript to control the loading screen -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide loading screen on initial page load
        hideLoadingScreen();

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

        // Add event listener for the pageshow event
        window.addEventListener('pageshow', function(event) {
            // Hide loading screen when the page is shown, including from bfcache
            if (event.persisted) {
                hideLoadingScreen();
            }
        });
    });

    // Function to show the loading screen
    function showLoadingScreen() {
        const loadingScreen = document.getElementById('loading-screen');
        if (!loadingScreen) return;

        loadingScreen.classList.remove('hidden');
        // Use a small timeout to allow the element to be in the DOM before adding opacity
        setTimeout(function() {
            loadingScreen.classList.remove('opacity-0');
        }, 10);
    }

    // Function to hide the loading screen
    function hideLoadingScreen() {
        const loadingScreen = document.getElementById('loading-screen');
        if (!loadingScreen) return;

        loadingScreen.classList.add('opacity-0');
        setTimeout(function() {
            loadingScreen.classList.add('hidden');
        }, 500); // Match the transition duration
    }

    // Hide loading when page has loaded (in case it was shown during navigation)
    window.addEventListener('load', function() {
        hideLoadingScreen();
    });

    // Additional safety measure: hide loading screen if the page is visible
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            hideLoadingScreen();
        }
    });
</script>

<style>
    #loading-screen {
        transition: opacity 0.5s ease-out;
        background-color: rgba(255, 255, 255, 0.97);
    }

    /* Animation for pulsing effect */
    @keyframes gentle-pulse {
        0% { transform: scale(1); opacity: 0.9; }
        50% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); opacity: 0.9; }
    }

    .animate-pulse {
        animation: gentle-pulse 2s infinite ease-in-out;
    }
    
    /* Enhanced spinner shadow */
    .animate-spin {
        filter: drop-shadow(0 2px 4px rgba(33, 50, 104, 0.2));
    }
</style>
