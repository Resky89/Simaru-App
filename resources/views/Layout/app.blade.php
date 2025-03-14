<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Asset Monitoring')</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Font (Optional - Gunakan font yang modern) -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
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
    </style>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-[#ECECEC]">
    <div class="flex min-h-screen">
        <!-- Mobile Menu Button -->
        <button id="mobile-menu-button" class="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-lg bg-[#213268] text-white transition-opacity duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
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
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const contentArea = document.querySelector('main');
            let isSidebarOpen = false;

            // Store active menu state
            let activeMenu = localStorage.getItem('activeMenu') || null;
            let activeSubMenu = localStorage.getItem('activeSubMenu') || null;

            function toggleSidebar() {
                isSidebarOpen = !isSidebarOpen;

                if (isSidebarOpen) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebarOverlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    mobileMenuButton.classList.add('opacity-0'); // Sembunyikan tombol
                    mobileMenuButton.style.pointerEvents = 'none'; // Nonaktifkan interaksi
                } else {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                    document.body.style.overflow = '';
                    mobileMenuButton.classList.remove('opacity-0'); // Tampilkan tombol
                    mobileMenuButton.style.pointerEvents = 'auto'; // Aktifkan interaksi
                }
            }

            // Toggle sidebar when menu button is clicked
            mobileMenuButton.addEventListener('click', toggleSidebar);

            // Close sidebar when overlay is clicked
            sidebarOverlay.addEventListener('click', toggleSidebar);

            // Close sidebar when window is resized to desktop view
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024 && isSidebarOpen) {
                    toggleSidebar();
                }
            });

            // Set initial active states based on current URL
            function initializeActiveMenus() {
                const currentPath = window.location.pathname;

                // Check all links and mark the matching one as active
                document.querySelectorAll('#sidebar a[href]').forEach(link => {
                    const linkPath = new URL(link.href, window.location.origin).pathname;

                    if (linkPath === currentPath) {
                        // Mark this link as active
                        const menuDiv = link.querySelector('div');
                        if (menuDiv) {
                            menuDiv.classList.add('bg-[#56C5F1]/20');
                        }

                        // If this is a submenu item, expand its parent menu
                        const parentSubmenu = link.closest('[x-show]');
                        if (parentSubmenu) {
                            // Get the parent menu button
                            const parentButton = parentSubmenu.previousElementSibling;
                            if (parentButton && parentButton.tagName === 'BUTTON') {
                                // Find the parent menu id from the x-show attribute
                                const parentMenuId = parentSubmenu.getAttribute('x-show').match(/activeMenu === ['"]([^'"]+)['"]/);
                                if (parentMenuId && parentMenuId[1]) {
                                    // Set the active menu in Alpine.js
                                    window.dispatchEvent(new CustomEvent('set-active-menu', {
                                        detail: { menu: parentMenuId[1] }
                                    }));

                                    // Store in localStorage
                                    localStorage.setItem('activeMenu', parentMenuId[1]);
                                    localStorage.setItem('activeSubMenu', currentPath);
                                }
                            }
                        } else {
                            // This is a main menu item, clear submenu state
                            localStorage.removeItem('activeMenu');
                            localStorage.setItem('activeSubMenu', currentPath);
                        }
                    }
                });
            }

            // Initialize active menus on page load
            initializeActiveMenus();

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
                    contentArea.innerHTML = '<div class="flex items-center justify-center h-full w-full"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#213268]"></div></div>';

                    // Highlight active menu item
                    document.querySelectorAll('#sidebar a div').forEach(div => {
                        div.classList.remove('bg-[#56C5F1]/20');
                    });

                    const activeDiv = this.querySelector('div');
                    if (activeDiv) {
                        activeDiv.classList.add('bg-[#56C5F1]/20');
                    }

                    // Close mobile sidebar if open
                    if (isSidebarOpen) {
                        toggleSidebar();
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
                            contentArea.innerHTML = newContent;

                            // Initialize any scripts needed for the new content
                            loadDynamicScripts();

                            // Update page title if available
                            const newTitle = doc.querySelector('title')?.innerText;
                            if (newTitle) {
                                document.title = newTitle;
                            }
                        })
                        .catch(error => {
                            console.error('Error loading page:', error);
                            contentArea.innerHTML = '<div class="p-6 text-center"><h2 class="text-xl text-red-600">Error loading content</h2><p class="mt-2">Please try again or refresh the page.</p></div>';
                        });
                });
            });

            // Handle browser back/forward navigation
            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.url) {
                    loadContent(e.state.url, false);
                } else {
                    window.location.reload();
                }
            });

            // Load content without pushing to history (used for back/forward navigation)
            function loadContent(url, pushToHistory = true) {
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        contentArea.innerHTML = doc.querySelector('main').innerHTML;

                        if (pushToHistory) {
                            window.history.pushState({url}, '', url);
                        }

                        loadDynamicScripts();

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
                        contentArea.innerHTML = '<div class="p-6 text-center"><h2 class="text-xl text-red-600">Error loading content</h2><p class="mt-2">Please try again or refresh the page.</p></div>';
                    });
            }

            // Function to initialize scripts for dynamically loaded content
            function loadDynamicScripts() {
                // Re-initialize any libraries or components
                if (typeof AOS !== 'undefined') {
                    AOS.refresh();
                }

                // If you're using Alpine.js, make sure to reinitialize it for dynamic content
                if (typeof Alpine !== 'undefined') {
                    // For Alpine.js v3+
                    if (Alpine.initTree) {
                        Alpine.initTree(document.body);
                    }
                    // For older Alpine versions
                    else if (Alpine.initializeComponent) {
                        document.querySelectorAll('[x-data]').forEach(el => {
                            Alpine.initializeComponent(el);
                        });
                    }

                    // Set active menu from localStorage after Alpine initializes
                    const storedActiveMenu = localStorage.getItem('activeMenu');
                    if (storedActiveMenu) {
                        window.dispatchEvent(new CustomEvent('set-active-menu', {
                            detail: { menu: storedActiveMenu }
                        }));
                    }
                }
            }

            // Token refresh logic
            setInterval(function() {
                fetch('/auth/refresh-token', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Token refresh failed');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        window.location.href = '/login';
                    }
                })
                .catch(error => {
                    console.error('Error refreshing token:', error);
                    window.location.href = '/login';
                });
            }, 50 * 60 * 1000); // 50 minutes
        });
    </script>
    @stack('scripts')
</body>
</html>
