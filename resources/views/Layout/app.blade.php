<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Asset Monitoring')</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js - Only load it ONCE here, with defer -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Font - Using Poppins as requested -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        main {
            min-height: calc(100vh - 4rem);
            padding-top: 2rem;
            background-color: #f8fafc;
        }
        .page-enter-active,
        .page-leave-active {
            transition: opacity 0.5s ease;
        }

        .page-enter-from,
        .page-leave-to {
            opacity: 0;
        }

        /* Ensure minimum font size */
        body, p, span, div, button, input, select, textarea {
            font-size: max(16px, 0.75rem);
        }

        /* Standard padding for content sections */
        .content-section {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        /* Bold headings for better highlighting */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
        }
    </style>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-[#ECECEC]">
    <div class="flex min-h-screen">
        <!-- Mobile Menu Button -->
        <button id="mobile-menu-button" class="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-lg bg-[#213268] text-white transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 close-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 z-30 hidden lg:hidden"></div>

        <!-- Sidebar -->
        <div id="sidebar" class="fixed top-0 left-0 h-full w-64 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            @include('Layout.sidebar')
        </div>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Navbar -->
            <div class="fixed top-0 right-0 left-0 lg:left-64 h-16 z-20">
                @include('Layout.navbar')
            </div>

            <!-- Content Area -->
            <main class="flex-1 p-6 mt-16 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // GLOBAL APP MANAGER - Handles sidebar, navigation, modals and auth
        window.AppManager = {
            // State variables
            state: {
                initialized: false,
                sidebarOpen: false,
                authChecked: false
            },

            // DOM elements (will be populated on init)
            elements: {},

            // Initialize the app
            init: function() {
                if (this.state.initialized) return;

                // Cache DOM elements
                this.elements = {
                    mobileMenuButton: document.getElementById('mobile-menu-button'),
                    sidebar: document.getElementById('sidebar'),
                    sidebarOverlay: document.getElementById('sidebar-overlay'),
                    contentArea: document.querySelector('main')
                };

                // Make toggleSidebar available globally in the AppManager
                this.toggleSidebar = this.createToggleSidebar();

                // Initialize components
                this.initSidebar();
                this.initNavigation();
                this.initAuth();

                // Set up token refresh
                this.setupTokenRefresh();

                this.state.initialized = true;
            },

            // Create the toggleSidebar function with proper binding
            createToggleSidebar: function() {
                return function() {
                    console.log('Toggle sidebar called, current state:', this.state.sidebarOpen);

                    this.state.sidebarOpen = !this.state.sidebarOpen;

                    if (this.state.sidebarOpen) {
                        console.log('Opening sidebar');
                        this.elements.sidebar.classList.remove('-translate-x-full');
                        this.elements.sidebarOverlay.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';

                        // Hide button completely
                        this.elements.mobileMenuButton.classList.add('hidden');
                    } else {
                        console.log('Closing sidebar');
                        this.elements.sidebar.classList.add('-translate-x-full');
                        this.elements.sidebarOverlay.classList.add('hidden');
                        document.body.style.overflow = '';

                        // Show button
                        this.elements.mobileMenuButton.classList.remove('hidden');
                    }
                }.bind(this);
            },

            // Initialize sidebar functionality
            initSidebar: function() {
                // Handle active menu state
                this.initActiveMenu();

                // Store active menu state
                let activeMenu = localStorage.getItem('activeMenu') || null;
                let activeSubMenu = localStorage.getItem('activeSubMenu') || null;

                console.log('Initializing sidebar, adding event listeners');

                // Toggle sidebar when menu button is clicked
                this.elements.mobileMenuButton.addEventListener('click', this.toggleSidebar);

                // Close sidebar when overlay is clicked
                this.elements.sidebarOverlay.addEventListener('click', this.toggleSidebar);

                // Close sidebar when window is resized to desktop view
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 1024 && this.state.sidebarOpen) {
                        this.toggleSidebar();
                    }
                }.bind(this));
            },

            // Initialize navigation functionality
            initNavigation: function() {
                // Set initial active states based on current URL
                this.initializeActiveMenus();

                // Create a custom event for Alpine.js to set the activeMenu
                window.addEventListener('set-active-menu', function(e) {
                    // Find Alpine.js component instance
                    const alpineComponent = document.querySelector('[x-data*="activeMenu"]').__x;
                    if (alpineComponent) {
                        // Set the activeMenu variable in Alpine.js
                        alpineComponent.$data.activeMenu = e.detail.menu;
                    }
                });

                // AJAX Navigation System - Intercept sidebar link clicks
                document.querySelectorAll('#sidebar a[href]').forEach(link => {
                    link.addEventListener('click', function(e) {
                        // Skip for links that should perform full page loads
                        if (this.getAttribute('data-full-load') === 'true') {
                            return true;
                        }

                        e.preventDefault();
                        const url = this.getAttribute('href');

                        // Store this as active submenu
                        localStorage.setItem('activeSubMenu', url);

                        // Check if this is a submenu item
                        const parentSubmenu = this.closest('[x-show]');
                        if (parentSubmenu) {
                            // Find the parent menu id
                            const parentMenuId = parentSubmenu.getAttribute('x-show').match(/activeMenu === ['"]([^'"]+)['"]/);
                            if (parentMenuId && parentMenuId[1]) {
                                localStorage.setItem('activeMenu', parentMenuId[1]);
                            }
                        } else {
                            // This is a main menu item, clear active menu state
                            localStorage.removeItem('activeMenu');
                        }

                        // Update browser URL without full page reload
                        window.history.pushState({url}, '', url);

                        // Show loading indicator in content area
                        this.elements.contentArea.innerHTML = '<div class="flex items-center justify-center h-full w-full"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#213268]"></div></div>';

                        // Highlight active menu item
                        document.querySelectorAll('#sidebar a div').forEach(div => {
                            div.classList.remove('bg-[#56C5F1]/20');
                        });

                        const activeDiv = this.querySelector('div');
                        if (activeDiv) {
                            activeDiv.classList.add('bg-[#56C5F1]/20');
                        }

                        // Close mobile sidebar if open
                        if (this.state.sidebarOpen) {
                            this.toggleSidebar();
                        }

                        // Fetch content via AJAX
                        fetch(url)
                            .then(response => response.text())
                            .then(html => {
                                // Extract only the content part from the response
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newContent = doc.querySelector('main').innerHTML;

                                // Update content area
                                this.elements.contentArea.innerHTML = newContent;

                                // PENTING: Evaluasi script-script yang ada di konten baru
                                const scripts = doc.querySelectorAll('main script');
                                scripts.forEach(script => {
                                    // Buat elemen script baru
                                    const newScript = document.createElement('script');

                                    // Salin semua atribut
                                    Array.from(script.attributes).forEach(attr => {
                                        newScript.setAttribute(attr.name, attr.value);
                                    });

                                    // Salin isi script
                                    newScript.textContent = script.textContent;

                                    // Sisipkan script baru ke dalam dokumen
                                    document.body.appendChild(newScript);
                                });

                                // Initialize any scripts needed for the new content
                                this.loadDynamicScripts();

                                // Buat listener untuk mendeteksi interaksi pengguna dengan modal
                                document.body.addEventListener('click', function modalClickHandler(e) {
                                    if (e.target.closest('[data-modal-toggle]') || e.target.closest('[data-toggle="modal"]')) {
                                        this.loadDynamicScripts(); // Reinisialisasi modal saat tombol modal diklik
                                    }
                                }.bind(this));

                                // Update page title if available
                                const newTitle = doc.querySelector('title')?.innerText;
                                if (newTitle) {
                                    document.title = newTitle;
                                }
                            })
                            .catch(error => {
                                console.error('Error loading page:', error);
                                this.elements.contentArea.innerHTML = '<div class="p-6 text-center"><h2 class="text-xl text-red-600">Error loading content</h2><p class="mt-2">Please try again or refresh the page.</p></div>';
                            });
                    });
                });

                // Handle browser back/forward navigation
                window.addEventListener('popstate', function(e) {
                    if (e.state && e.state.url) {
                        this.loadContent(e.state.url, false);
                    } else {
                        window.location.reload();
                    }
                }.bind(this));
            },

            // Load content without pushing to history (used for back/forward navigation)
            loadContent: function(url, pushToHistory = true) {
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        this.elements.contentArea.innerHTML = doc.querySelector('main').innerHTML;

                        if (pushToHistory) {
                            window.history.pushState({url}, '', url);
                        }

                        this.loadDynamicScripts();

                        // Update active menu item
                        document.querySelectorAll('#sidebar a').forEach(link => {
                            const div = link.querySelector('div');
                            if (div) {
                                if (link.getAttribute('href') === url) {
                                    div.classList.add('bg-[#56C5F1]/20');
                                } else {
                                    div.classList.remove('bg-[#56C5F1]/20');
                                }
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error loading page:', error);
                        this.elements.contentArea.innerHTML = '<div class="p-6 text-center"><h2 class="text-xl text-red-600">Error loading content</h2><p class="mt-2">Please try again or refresh the page.</p></div>';
                    });
            },

            // Modify the loadDynamicScripts function to properly reinitialize components
            loadDynamicScripts: function() {
                // Buat fungsi untuk menginisialisasi modal dengan lebih agresif
                function forceInitModals() {
                    // Reset flags untuk memaksa semua komponen dimuat ulang
                    if (window.AppState) {
                        window.AppState.modalsInitialized = false;
                        window.AppState.initialized = false;
                    }

                    // Inisialisasi semua modal yang mungkin ada
                    if (typeof window.initModals === 'function') {
                        window.initModals();
                    }

                    if (typeof window.initBrandModals === 'function') {
                        window.initBrandModals();
                    }

                    // Panggil fungsi inisialisasi global
                    if (typeof window.initializeAlpineComponents === 'function') {
                        window.initializeAlpineComponents();
                    }

                    // Inisialisasi Alpine.js jika ada
                    if (typeof Alpine !== 'undefined' && Alpine.initTree) {
                        Alpine.initTree(document.body);
                    }

                    // Tandai sebagai terinisialisasi
                    if (window.AppState) {
                        window.AppState.modalsInitialized = true;
                    }
                }
            },

            // Initialize active menu state
            initActiveMenu: function() {
                const storedActiveMenu = localStorage.getItem('activeMenu');
                if (storedActiveMenu && storedActiveMenu !== 'null') {
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('set-active-menu', {
                            detail: { menu: storedActiveMenu }
                        }));
                    }, 100);
                }
            },

            initializeComponents: function() {
                // Initialize modals and other components
                if (typeof window.reinitializeModals === 'function') {
                    window.reinitializeModals();
                }

                if (typeof window.initBrandModals === 'function') {
                    window.initBrandModals();
                }
            },
        };

        // Start initialization
        AppManager.init();
    </script>
    <script>
        // Global initialization function for all Alpine.js components and modals
        function initializeAlpineComponents() {
            // PENTING: Selalu inisialisasi modal setelah navigasi halaman
            if (typeof window.initModals === 'function') {
                try {
                    window.initModals();
                } catch (e) {
                    console.error('Error initializing modals:', e);
                }
            }

            // Inisialisasi Alpine.js komponen lainnya
            if (window.Alpine) {
                try {
                    window.Alpine.initTree(document.body);
                } catch (e) {
                    console.error('Error initializing Alpine components:', e);
                }
            }
        }

        // Run on initial page load
        document.addEventListener('DOMContentLoaded', function() {
            // Delay slightly to ensure DOM is fully ready
            setTimeout(initializeAlpineComponents, 50);

            // Check for errors in AJAX responses
            $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
                if (jqXHR.status === 401) {
                    console.log('Received 401 response, attempting to refresh token');

                    // Try to refresh the token
                    $.ajax({
                        url: '/auth/refresh-token',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                console.log('Token refreshed successfully, retrying original request');
                                // Retry the original request
                                $.ajax(ajaxSettings);
                            } else {
                                console.log('Token refresh failed, redirecting to login');
                                window.location.href = '/login';
                            }
                        },
                        error: function() {
                            console.log('Token refresh failed with error, redirecting to login');
                            window.location.href = '/login';
                        }
                    });
                }
            })
    </script>
    <!-- Stack for additional scripts -->
    @stack('scripts')
</body>
</html>
