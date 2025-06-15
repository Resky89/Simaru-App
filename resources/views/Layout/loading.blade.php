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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        hideLoadingScreen();

        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            const isNavLink = href &&
                             !href.startsWith('#') &&
                             !href.startsWith('javascript:') &&
                             !link.hasAttribute('download') &&
                             !link.getAttribute('target') &&
                             (href.startsWith('/') || href.includes(window.location.hostname));

            if (isNavLink && !e.ctrlKey && !e.metaKey) {
                showLoadingScreen();
            }
        });

        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM' && !e.target.hasAttribute('data-no-loading')) {
                showLoadingScreen();
            }
        });

        window.addEventListener('popstate', function() {
            showLoadingScreen();
        });

        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                hideLoadingScreen();
            }
        });
    });

    function showLoadingScreen() {
        const loadingScreen = document.getElementById('loading-screen');
        if (!loadingScreen) return;

        loadingScreen.classList.remove('hidden');
        setTimeout(function() {
            loadingScreen.classList.remove('opacity-0');
        }, 10);
    }

    function hideLoadingScreen() {
        const loadingScreen = document.getElementById('loading-screen');
        if (!loadingScreen) return;

        loadingScreen.classList.add('opacity-0');
        setTimeout(function() {
            loadingScreen.classList.add('hidden');
        }, 500);
    }

    window.addEventListener('load', function() {
        hideLoadingScreen();
    });

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

    @keyframes gentle-pulse {
        0% { transform: scale(1); opacity: 0.9; }
        50% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); opacity: 0.9; }
    }

    .animate-pulse {
        animation: gentle-pulse 2s infinite ease-in-out;
    }
    
    .animate-spin {
        filter: drop-shadow(0 2px 4px rgba(33, 50, 104, 0.2));
    }
</style>
