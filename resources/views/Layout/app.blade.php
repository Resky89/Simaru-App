<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Asset Monitoring')</title>
    <link rel="icon" href="{{ api_url('images/logo.png') }}" type="image/png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Axios for AJAX requests -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        main {
            min-height: calc(100vh - 4rem);
            padding-top: 2rem;
            background-color: #f8fafc;
            max-width: 100%;
        }

        .page-enter-active,
        .page-leave-active {
            transition: opacity 0.5s ease;
        }

        .page-enter-from,
        .page-leave-to {
            opacity: 0;
        }

        body,
        p,
        span,
        div,
        button,
        input,
        select,
        textarea {
            font-size: max(16px, 0.75rem);
        }

        .content-section {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: 700;
        }

        /* Add responsive table support */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Make images responsive */
        img {
            max-width: 100%;
            height: auto;
        }
    </style>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body class="bg-[#ECECEC]">
    <!-- Welcome Message SweetAlert (Session Flash Data) -->
    @if(session('welcome_message'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Selamat Datang!',
                    text: "{{ session('employee_name') ? 'Selamat datang, ' . session('employee_name') : session('welcome_message') }}",
                    icon: 'success',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    background: '#ffffff',
                    iconColor: '#213268',
                    customClass: {
                        title: 'text-[#213268] font-bold',
                        popup: 'rounded-xl shadow-xl border border-gray-100'
                    }
                });
            });
        </script>
    @endif

    <div class="flex min-h-screen">
        <!-- Mobile Menu Button -->
        <button id="mobile-menu-button"
            class="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-lg bg-[#213268] text-white transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 menu-icon" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 close-icon hidden" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 z-30 hidden lg:hidden"></div>

        <!-- Sidebar -->
        <div id="sidebar"
            class="fixed top-0 left-0 h-full w-64 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            @include('Layout.sidebar')
        </div>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col lg:ml-64 overflow-hidden w-full">
            <!-- Navbar -->
            <div class="fixed top-0 right-0 left-0 lg:left-64 h-16 z-20">
                @include('Layout.navbar')
            </div>

            <!-- Content Area -->
            <main class="flex-1 p-4 sm:p-6 mt-16 overflow-y-auto overflow-x-hidden w-full max-w-full">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Include Reminders Popup -->
    @include('Layout.reminders-popup')

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- CSRF Token Auto-Refresh -->
    <script>
        // Configure axios with CSRF token
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        // Get the CSRF token from the meta tag
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }

        // Function to refresh the CSRF token
        const refreshCsrfToken = async () => {
            try {
                const response = await axios.post('/csrf-token-refresh', {}, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (response.data && response.data.token) {
                    // Update the CSRF token in the meta tag
                    const metaToken = document.querySelector('meta[name="csrf-token"]');
                    if (metaToken) {
                        metaToken.content = response.data.token;
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = response.data.token;
                    }

                    // Update any forms on the page
                    document.querySelectorAll('input[name="_token"]').forEach(input => {
                        input.value = response.data.token;
                    });

                    return response.data.token;
                }
            } catch (error) {
                console.error('Error refreshing CSRF token:', error);
            }
        };

        // Refresh token periodically (every 30 minutes)
        setInterval(refreshCsrfToken, 30 * 60 * 1000);

        // Refresh token when user becomes active after being idle
        let idleTime = 0;
        const idleInterval = setInterval(() => {
            idleTime++;
            // If user is idle for more than 25 minutes, refresh token when they return
            if (idleTime > 25) {
                refreshCsrfToken();
                idleTime = 0;
            }
        }, 60 * 1000); // Check every minute

        // Reset idle timer on user activity
        const resetIdleTime = () => {
            idleTime = 0;
        };

        // Track user activity
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, resetIdleTime, true);
        });

        // Handle 419 errors globally with axios
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response && error.response.status === 419) {
                    // Refresh token and retry the request
                    return refreshCsrfToken().then(token => {
                        // Clone the original request and set the new token
                        const config = error.config;
                        if (token) {
                            config.headers['X-CSRF-TOKEN'] = token;
                        }
                        // Retry the request
                        return axios(config);
                    });
                }
                return Promise.reject(error);
            }
        );
    </script>

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
            init: function () {
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

                this.state.initialized = true;
            },

            // Create the toggleSidebar function with proper binding
            createToggleSidebar: function () {
                return function () {
                    this.state.sidebarOpen = !this.state.sidebarOpen;

                    if (this.state.sidebarOpen) {
                        this.elements.sidebar.classList.remove('-translate-x-full');
                        this.elements.sidebarOverlay.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                        this.elements.mobileMenuButton.classList.add('hidden');
                    } else {
                        this.elements.sidebar.classList.add('-translate-x-full');
                        this.elements.sidebarOverlay.classList.add('hidden');
                        document.body.style.overflow = '';
                        this.elements.mobileMenuButton.classList.remove('hidden');
                    }
                }.bind(this);
            },

            // Initialize sidebar functionality
            initSidebar: function () {
                // Handle active menu state
                this.initActiveMenu();

                // Toggle sidebar when menu button is clicked
                this.elements.mobileMenuButton.addEventListener('click', this.toggleSidebar);

                // Close sidebar when overlay is clicked
                this.elements.sidebarOverlay.addEventListener('click', this.toggleSidebar);

                // Close sidebar when window is resized to desktop view
                window.addEventListener('resize', function () {
                    if (window.innerWidth >= 1024 && this.state.sidebarOpen) {
                        this.toggleSidebar();
                    }
                }.bind(this));
            },

            // Initialize navigation functionality
            initNavigation: function () {
                // Create a custom event for Alpine.js to set the activeMenu
                window.addEventListener('set-active-menu', function (e) {
                    const alpineComponent = document.querySelector('[x-data*="activeMenu"]')?.__x;
                    if (alpineComponent) {
                        alpineComponent.$data.activeMenu = e.detail.menu;
                    }
                });

                // AJAX Navigation System - Intercept sidebar link clicks
                document.querySelectorAll('#sidebar a[href]').forEach(link => {
                    link.addEventListener('click', function (e) {
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
                        window.history.pushState({ url }, '', url);

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

                                // Process scripts in new content
                                const scripts = doc.querySelectorAll('main script');
                                scripts.forEach(script => {
                                    const newScript = document.createElement('script');
                                    Array.from(script.attributes).forEach(attr => {
                                        newScript.setAttribute(attr.name, attr.value);
                                    });
                                    newScript.textContent = script.textContent;
                                    document.body.appendChild(newScript);
                                });

                                // Initialize any scripts needed for the new content
                                this.loadDynamicScripts();

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
                    }.bind(this)); // Bind this correctly to access elements
                });

                // Handle browser back/forward navigation
                window.addEventListener('popstate', function (e) {
                    if (e.state && e.state.url) {
                        this.loadContent(e.state.url, false);
                    } else {
                        window.location.reload();
                    }
                }.bind(this));
            },

            // Load content without pushing to history (used for back/forward navigation)
            loadContent: function (url, pushToHistory = true) {
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        this.elements.contentArea.innerHTML = doc.querySelector('main').innerHTML;

                        if (pushToHistory) {
                            window.history.pushState({ url }, '', url);
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

            // Load dynamic scripts properly
            loadDynamicScripts: function () {
                // Initialize modals
                if (typeof window.initModals === 'function') {
                    try { window.initModals(); } catch (e) { console.error('Error initializing modals:', e); }
                }

                // Initialize Alpine components
                if (typeof Alpine !== 'undefined' && Alpine.initTree) {
                    try { Alpine.initTree(document.body); } catch (e) { console.error('Error initializing Alpine components:', e); }
                }

                // Reinitialize modals on button click
                document.querySelectorAll('[data-modal-toggle], [data-toggle="modal"]').forEach(el => {
                    el.addEventListener('click', () => {
                        if (typeof window.initModals === 'function') {
                            setTimeout(() => window.initModals(), 50);
                        }
                    });
                });
            },

            // Initialize active menu state
            initActiveMenu: function () {
                const storedActiveMenu = localStorage.getItem('activeMenu');
                if (storedActiveMenu && storedActiveMenu !== 'null') {
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('set-active-menu', {
                            detail: { menu: storedActiveMenu }
                        }));
                    }, 100);
                }
            }
        };

        // Start initialization
        document.addEventListener('DOMContentLoaded', function () {
            AppManager.init();

            // Initialize Alpine components
            if (typeof window.initModals === 'function') {
                setTimeout(() => window.initModals(), 50);
            }
        });
    </script>
    @stack('scripts')

    <script>
        AOS.init();
    </script>

    <!-- Initialize App Manager -->
    <script>
        window.AppManager.init();
    </script>

    <!-- Include Global Helpers JavaScript -->
    <script src="{{ asset('js/helpers.js') }}"></script>

    <!-- Include Reminders Popup JavaScript -->
    <script src="{{ asset('js/reminders-popup.js') }}"></script>
</body>

</html>