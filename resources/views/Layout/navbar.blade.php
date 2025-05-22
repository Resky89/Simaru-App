<nav class="bg-[#213268] h-[72px] flex items-center justify-between px-6">
    <!-- Right Side -->
    <div class="flex-1"></div>
    <div class="flex items-center gap-2 md:gap-4">
        <!-- Notification -->
        <div class="relative hidden md:block">
            <div class="notification-dropdown cursor-pointer">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span id="notification-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                </div>

                <!-- Notification Dropdown -->
                <div id="notification-menu" class="hidden absolute right-0 mt-2 w-[350px] max-w-[95vw] bg-white rounded-lg shadow-xl z-50 max-h-[80vh] overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-[#232D42]">Notifikasi</h3>
                        <button id="refresh-notifications" class="text-[#213268] hover:text-[#182451]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>

                    <!-- Notification Tabs -->
                    <div class="flex border-b border-gray-200">
                        <button id="unread-tab" class="flex-1 py-2 text-sm font-medium text-center text-[#213268] border-b-2 border-[#213268]">
                            Belum Dibaca
                        </button>
                        <button id="read-tab" class="flex-1 py-2 text-sm font-medium text-center text-gray-500 hover:text-[#213268]">
                            Sudah Dibaca
                        </button>
                    </div>

                    <div id="notification-list" class="max-h-[60vh] overflow-y-auto overflow-x-hidden custom-scrollbar" style="scrollbar-width: thin; scrollbar-color: #213268 #f0f0f0;">
                    </div>
                </div>
            </div>
        </div>

        <!-- User Profile - Now clickable -->
        <a href="{{ route('profile') }}" class="flex items-center gap-2 md:gap-3 cursor-pointer hover:opacity-90 transition-opacity">
            <img src="https://ui-avatars.com/api/?name=Austin+Robertson" alt="User" class="w-8 h-8 md:w-10 md:h-10 rounded-full">
            <div class="text-white hidden md:block">
                <p class="text-sm font-medium">Austin Robertson</p>
                <p class="text-xs opacity-60">Super Admin</p>
            </div>
        </a>
    </div>
</nav>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationDropdown = document.querySelector('.notification-dropdown');
        const notificationMenu = document.getElementById('notification-menu');
        const notificationBadge = document.getElementById('notification-badge');
        const notificationList = document.getElementById('notification-list');
        const unreadTab = document.getElementById('unread-tab');
        const readTab = document.getElementById('read-tab');

        let notifications = {
            unread: [],
            read: []
        };
        let isDropdownOpen = false;
        let isNotificationsLoaded = false;
        let isLoading = false;
        let currentTab = 'unread'; // Default tab
        let page = {
            unread: 1,
            read: 1
        };
        let hasMorePages = {
            unread: true,
            read: true
        };

        // Function to toggle dropdown
        function toggleDropdown() {
            if (isDropdownOpen) {
                notificationMenu.classList.add('hidden');
            } else {
                notificationMenu.classList.remove('hidden');

                // Only load notifications if they haven't been loaded yet
                if (!isNotificationsLoaded && !isLoading) {
                    loadNotifications();
                }
            }
            isDropdownOpen = !isDropdownOpen;
        }

        // Toggle dropdown on click
        if (notificationDropdown) {
            notificationDropdown.addEventListener('click', function(e) {
                // Don't close if clicking on a notification item that has actions
                if (e.target.closest('.mark-as-read')) {
                    return;
                }
                // Don't close if clicking on refresh button
                if (e.target.closest('#refresh-notifications')) {
                    e.stopPropagation();
                    // Reset page and reload notifications
                    page = {
                        unread: 1,
                        read: 1
                    };
                    isNotificationsLoaded = false;
                    loadNotifications();
                    return;
                }
                // Don't close if clicking on tabs
                if (e.target.closest('#unread-tab') || e.target.closest('#read-tab')) {
                    e.stopPropagation();
                    return;
                }
                toggleDropdown();
            });
        }

        // Tab switching
        unreadTab.addEventListener('click', function() {
            if (currentTab !== 'unread') {
                currentTab = 'unread';
                updateTabUI();
                renderNotifications(notifications.unread, true);

                // Load unread notifications if not loaded yet
                if (notifications.unread.length === 0 && !isLoading && hasMorePages.unread) {
                    loadNotifications();
                }
            }
        });

        readTab.addEventListener('click', function() {
            if (currentTab !== 'read') {
                currentTab = 'read';
                updateTabUI();
                renderNotifications(notifications.read, true);

                // Load read notifications if not loaded yet
                if (notifications.read.length === 0 && !isLoading && hasMorePages.read) {
                    loadNotifications();
                }
            }
        });

        // Update tab UI based on current selection
        function updateTabUI() {
            if (currentTab === 'unread') {
                unreadTab.classList.add('text-[#213268]', 'border-b-2', 'border-[#213268]');
                unreadTab.classList.remove('text-gray-500');
                readTab.classList.remove('text-[#213268]', 'border-b-2', 'border-[#213268]');
                readTab.classList.add('text-gray-500');
            } else {
                readTab.classList.add('text-[#213268]', 'border-b-2', 'border-[#213268]');
                readTab.classList.remove('text-gray-500');
                unreadTab.classList.remove('text-[#213268]', 'border-b-2', 'border-[#213268]');
                unreadTab.classList.add('text-gray-500');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (isDropdownOpen && !notificationDropdown.contains(e.target)) {
                notificationMenu.classList.add('hidden');
                isDropdownOpen = false;
            }
        });

        // Function to show loading state
        function showLoading(isFirstLoad = true) {
            if (isFirstLoad) {
                notificationList.innerHTML = '';
            } else {
                // Add loading indicator at the bottom for lazy loading
                const loadingMore = document.createElement('div');
                loadingMore.id = 'loading-more';
                loadingMore.className = 'p-3 text-center text-gray-500';
                loadingMore.innerHTML = `
                    <div class="animate-spin mx-auto h-5 w-5 border-3 border-t-transparent border-[#213268] rounded-full mb-2"></div>
                    <p class="text-sm">Memuat lebih banyak...</p>
                `;
                notificationList.appendChild(loadingMore);
            }
            isLoading = true;
        }

        // Function to load notifications
        function loadNotifications(isLazyLoad = false) {
            if (isLoading) return;

            // Set current page based on active tab
            const currentPage = page[currentTab];

            // Show loading state
            showLoading(!isLazyLoad);

            // Fetch all notifications (both read and unread)
            fetch(`/notifications?limit=15&page=${currentPage}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load notifications');
                }
                return response.json();
            })
            .then(data => {
                isLoading = false;

                if (data.success) {
                    // Get new notifications from response
                    const newNotifications = data.data;

                    // Remove loading indicator
                    const loadingMore = document.getElementById('loading-more');
                    if (loadingMore) {
                        loadingMore.remove();
                    }
                    
                    // Ensure notification list has overflow-y-auto but not overflow-x
                    notificationList.classList.add('overflow-y-auto', 'overflow-x-hidden');

                    // Handle pagination
                    if (data.pagination) {
                        hasMorePages[currentTab] = data.pagination.current_page < data.pagination.total_pages;
                        page[currentTab] = data.pagination.current_page + 1;
                    } else {
                        hasMorePages[currentTab] = false;
                    }

                    // Separate notifications into read and unread
                    const readNotifications = newNotifications.filter(n => n.is_read);
                    const unreadNotifications = newNotifications.filter(n => !n.is_read);

                    // Update notifications array based on current tab
                    if (isLazyLoad) {
                        if (currentTab === 'read') {
                            notifications.read = [...notifications.read, ...readNotifications];
                        } else {
                            notifications.unread = [...notifications.unread, ...unreadNotifications];
                        }
                    } else {
                        notifications.read = readNotifications;
                        notifications.unread = unreadNotifications;
                    }

                    // Render notifications for current tab
                    if (currentTab === 'read') {
                        renderNotifications(notifications.read, !isLazyLoad);
                    } else {
                        renderNotifications(notifications.unread, !isLazyLoad);
                    }
                    
                    isNotificationsLoaded = true;

                    // If no notifications for current tab
                    if ((currentTab === 'unread' && notifications.unread.length === 0) || 
                        (currentTab === 'read' && notifications.read.length === 0)) {
                        const emptyMessage = currentTab === 'unread' ?
                            'Tidak ada notifikasi baru' :
                            'Tidak ada notifikasi yang sudah dibaca';

                        notificationList.innerHTML = `
                            <div class="p-6 text-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <p>${emptyMessage}</p>
                            </div>
                        `;
                    }
                } else {
                    notificationList.innerHTML = `
                        <div class="p-6 text-center text-red-500">
                            <p>Gagal memuat notifikasi</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                isLoading = false;
                console.error('Error loading notifications:', error);

                if (!isLazyLoad) {
                    notificationList.innerHTML = `
                        <div class="p-6 text-center text-red-500">
                            <p>Terjadi kesalahan saat memuat notifikasi</p>
                            <button id="retry-btn" class="mt-2 px-3 py-1 bg-[#213268] text-white text-sm rounded-md hover:bg-[#182451]">Coba lagi</button>
                        </div>
                    `;

                    document.getElementById('retry-btn')?.addEventListener('click', function() {
                        page[currentTab] = 1;
                        loadNotifications();
                    });
                }
            });
        }

        // Function to render notifications
        function renderNotifications(notificationsToRender, clearFirst = false) {
            if (clearFirst) {
                notificationList.innerHTML = '';
            }

            if (notificationsToRender.length === 0 && clearFirst) {
                const emptyMessage = currentTab === 'unread' ?
                    'Tidak ada notifikasi baru' :
                    'Tidak ada notifikasi yang sudah dibaca';

                notificationList.innerHTML = `
                    <div class="p-6 text-center text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <p>${emptyMessage}</p>
                    </div>
                `;
                return;
            }

            // Create a document fragment to improve performance
            const fragment = document.createDocumentFragment();

            notificationsToRender.forEach(notification => {
                const date = new Date(notification.created_at);
                const formattedDate = date.toLocaleString('id-ID', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // Calculate how new the notification is
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMins / 60);
                const diffDays = Math.floor(diffHours / 24);

                // Add "new" indicator for notifications less than 30 minutes old
                const isNew = diffMins < 30;

                // Relative time display
                let timeAgo = '';
                if (diffMins < 1) {
                    timeAgo = 'Baru saja';
                } else if (diffMins < 60) {
                    timeAgo = `${diffMins} menit yang lalu`;
                } else if (diffHours < 24) {
                    timeAgo = `${diffHours} jam yang lalu`;
                } else if (diffDays < 7) {
                    timeAgo = `${diffDays} hari yang lalu`;
                } else {
                    timeAgo = formattedDate;
                }

                const notificationItem = document.createElement('div');
                notificationItem.className = `p-3 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200 break-words
                    ${notification.is_read ? 'bg-gray-50' : isNew ? 'bg-[rgba(33,50,104,0.05)]' : 'bg-white'}`;
                notificationItem.setAttribute('data-id', notification.id);

                notificationItem.innerHTML = `
                    <div class="flex justify-between items-start mb-2 gap-1">
                        <div class="flex-1 min-w-0 pr-1">
                            <h4 class="text-sm font-semibold ${notification.is_read ? 'text-gray-500' : 'text-[#232D42]'} mb-0.5">${notification.title}</h4>
                            <p class="text-xs text-gray-600 break-words">${notification.detail}</p>
                        </div>
                        <div class="flex flex-col items-end flex-shrink-0 ml-1">
                            <span class="text-xs text-gray-400 whitespace-nowrap" title="${formattedDate}">${timeAgo}</span>
                            ${isNew && !notification.is_read ? 
                                '<span class="text-xs bg-[#213268] text-white px-1.5 py-0.5 rounded-full inline-flex items-center justify-center min-w-[36px] mt-1">Baru</span>' : 
                                ''}
                        </div>
                    </div>
                    ${!notification.is_read ? `
                    <button class="mark-as-read text-xs text-[#213268] hover:text-[#182451] font-medium flex items-center" data-id="${notification.id}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Tandai sudah dibaca</span>
                    </button>
                    ` : ''}
                `;

                fragment.appendChild(notificationItem);
            });

            // Append the fragment to the notification list
            notificationList.appendChild(fragment);

            // Add lazy loading trigger if there are more pages
            if (hasMorePages[currentTab]) {
                addLazyLoadTrigger();
            }

            // Add event listeners to mark-as-read buttons
            document.querySelectorAll('.mark-as-read').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const notificationId = this.getAttribute('data-id');
                    markAsRead(notificationId);
                });
            });
        }

        // Add lazy load trigger at the bottom
        function addLazyLoadTrigger() {
            // Check if we already have a lazy load trigger
            if (document.getElementById('lazy-load-trigger')) {
                return;
            }

            // Create a trigger element
            const trigger = document.createElement('div');
            trigger.id = 'lazy-load-trigger';
            trigger.className = 'h-4'; // Small height to detect scroll
            notificationList.appendChild(trigger);
        }

        // Function to mark notification as read
        function markAsRead(notificationId) {
            // Get the CSRF token
            const csrf_token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!csrf_token) {
                console.error('CSRF token not found');
                alert('Tidak dapat menandai notifikasi sebagai dibaca: CSRF token tidak ditemukan');
                return;
            }

            // Find the button to update UI
            const button = document.querySelector(`.mark-as-read[data-id="${notificationId}"]`);
            if (button) {
                button.innerHTML = `<span class="flex items-center"><div class="animate-spin h-3 w-3 border border-t-transparent border-[#213268] rounded-full mr-1"></div> Memproses...</span>`;
                button.disabled = true;
            }

            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf_token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                // Adding empty body for POST request
                body: JSON.stringify({})
            })
            .then(response => {
                // Check for different HTTP status codes
                if (response.status === 401) {
                    throw new Error('Unauthorized: Please login again');
                } else if (response.status === 403) {
                    throw new Error('Forbidden: You do not have permission');
                } else if (response.status === 404) {
                    throw new Error(`Notification #${notificationId} not found`);
                } else if (!response.ok) {
                    throw new Error(`Failed with status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('Notification marked as read:', data);

                    // Find the notification in the unread array
                    const notificationIndex = notifications.unread.findIndex(n => n.id == notificationId);

                    if (notificationIndex !== -1) {
                        // Update to mark as read
                        const updatedNotification = {
                            ...notifications.unread[notificationIndex],
                            is_read: true,
                            read_at: new Date().toISOString()
                        };

                        // Remove from unread and add to read arrays
                        notifications.unread.splice(notificationIndex, 1);
                        notifications.read.unshift(updatedNotification);

                        // If in unread tab, remove notification from view with animation
                        if (currentTab === 'unread') {
                            const notificationElement = document.querySelector(`.p-3[data-id="${notificationId}"]`);
                            if (notificationElement) {
                                notificationElement.style.transition = 'opacity 0.3s, transform 0.3s';
                                notificationElement.style.opacity = '0';
                                notificationElement.style.transform = 'translateX(10px)';

                                setTimeout(() => {
                                    notificationElement.remove();

                                    // Show empty message if no more unread notifications
                                    if (notifications.unread.length === 0) {
                                        notificationList.innerHTML = `
                                            <div class="p-6 text-center text-gray-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                </svg>
                                                <p>Tidak ada notifikasi baru</p>
                                            </div>
                                        `;
                                    }
                                }, 300);
                            }
                        }
                    }

                    loadUnreadCount();
                } else {
                    // Handle API success: false case
                    console.error('API returned success: false', data);
                    throw new Error(data.errors || 'Failed to mark notification as read');
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);

                // Reset button state if it exists
                if (button) {
                    button.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Tandai sudah dibaca`;
                    button.disabled = false;
                }

                // Show error to user
                alert(`Gagal menandai notifikasi sebagai dibaca: ${error.message}`);
            });
        }

        // Function to load unread count
        function loadUnreadCount() {
            fetch('/notifications?limit=10', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load notifications');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Store previous count to check for changes
                    const prevCount = parseInt(notificationBadge.textContent) || 0;

                    // Filter unread notifications and count them
                    const unreadNotifications = data.data.filter(n => !n.is_read);
                    const count = unreadNotifications.length;

                    // Update badge
                    if (count > 0) {
                        notificationBadge.textContent = count > 99 ? '99+' : count;
                        notificationBadge.classList.remove('hidden');

                        // If count increased, animate the badge
                        if (count > prevCount) {
                            notificationBadge.classList.add('animate-bounce');
                            setTimeout(() => {
                                notificationBadge.classList.remove('animate-bounce');
                            }, 1000);
                        }
                    } else {
                        notificationBadge.classList.add('hidden');
                    }
                }
            })
            .catch(error => {
                console.error('Error loading notification count:', error);
            });
        }

        // Initial load of unread count
        loadUnreadCount();

        // Implement lazy loading via scroll event
        notificationList.addEventListener('scroll', function() {
            if (isLoading || !hasMorePages[currentTab]) return;

            const { scrollTop, scrollHeight, clientHeight } = notificationList;

            // If scrolled near bottom and not already loading
            if (scrollHeight - scrollTop - clientHeight < 50) {
                loadNotifications(true); // true means lazy loading
            }
        });

        // Refresh notifications count more frequently (every 30 seconds)
        setInterval(loadUnreadCount, 30000);

        // Refresh notifications data in the background (every minute)
        setInterval(() => {
            if (!isDropdownOpen) {
                // Silently update in the background if dropdown is closed
                fetch('/notifications?limit=5', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Filter for unread notifications
                        const newUnreadNotifications = data.data.filter(n => !n.is_read);
                        
                        // Check for new notifications
                        const oldNotifications = notifications.unread || [];

                        // Store IDs to compare
                        const oldIds = oldNotifications.map(n => n.id);
                        const hasNewNotifications = newUnreadNotifications.some(n => !oldIds.includes(n.id));

                        // If we have new notifications and dropdown is closed, indicate it
                        if (hasNewNotifications && !isDropdownOpen) {
                            // Update our local cache with new data
                            notifications.unread = newUnreadNotifications;

                            // Flash the notification bell
                            const bell = notificationDropdown.querySelector('svg');
                            if (bell) {
                                bell.classList.add('text-yellow-400', 'animate-pulse');

                                setTimeout(() => {
                                    bell.classList.remove('text-yellow-400', 'animate-pulse');
                                }, 3000);
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error silently updating notifications:', error);
                });
            }
        }, 60000);
    });
</script>
@endpush
