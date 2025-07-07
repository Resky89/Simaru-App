<nav class="bg-[#213268] h-[72px] flex items-center justify-between px-6">
    <!-- Right Side -->
    <div class="flex-1"></div>
    <div class="flex items-center gap-2 md:gap-4">
        <!-- Notification -->
        <div class="relative">
            <div class="notification-dropdown cursor-pointer">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span id="notification-badge"
                        class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                </div>

                <!-- Notification Dropdown -->
                <div id="notification-menu"
                    class="hidden fixed left-1/2 transform -translate-x-1/2 md:left-auto md:transform-none md:right-6 top-[80px] w-[90%] max-w-[350px] bg-white rounded-lg shadow-xl z-50 max-h-[80vh] overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-[#232D42]">Notifikasi</h3>
                        <button id="refresh-notifications" class="text-[#213268] hover:text-[#182451]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>

                    <!-- Notification Tabs -->
                    <div class="flex border-b border-gray-200">
                        <button id="unread-tab"
                            class="flex-1 py-2 text-sm font-medium text-center text-[#213268] border-b-2 border-[#213268]">
                            Belum Dibaca
                        </button>
                        <button id="read-tab"
                            class="flex-1 py-2 text-sm font-medium text-center text-gray-500 hover:text-[#213268]">
                            Sudah Dibaca
                        </button>
                    </div>

                    <div id="notification-list" class="max-h-[60vh] overflow-y-auto overflow-x-hidden custom-scrollbar"
                        style="scrollbar-width: thin; scrollbar-color: #213268 #f0f0f0;">
                    </div>
                </div>
            </div>
        </div>

        <!-- User Profile - Now clickable -->
        <a href="{{ route('profile') }}"
            class="flex items-center gap-2 md:gap-3 cursor-pointer hover:opacity-90 transition-opacity">
            <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-white/20 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="text-white hidden md:block">
                @php
                    $profileData = session('profile_data', []);
                    $accessTokenPayload = session('access_token_payload', []);

                    // Use profile data if available, otherwise fallback to token payload
                    $employeeNumber = $profileData['employee_number'] ?? $accessTokenPayload['employee_number'] ?? 'N/A';

                    // Handle roles from profile data or fall back to token payload
                    if (isset($profileData['roles']) && is_array($profileData['roles']) && !empty($profileData['roles'])) {
                        $roleNames = array_map(function($role) {
                            return $role['role_name'] ?? '';
                        }, $profileData['roles']);
                        $roleText = implode(', ', array_filter($roleNames));
                    } else {
                    $roles = $accessTokenPayload['role_names'] ?? [];
                    $roleText = !empty($roles) ? (is_array($roles) ? implode(', ', $roles) : $roles) : 'No Role';
                    }
                @endphp
                <div class="flex items-center gap-1">
                    <p class="text-sm font-medium">{{ $employeeNumber }}</p>
                </div>
                <p class="text-xs opacity-60 mt-0.5">{{ $roleText }}</p>
            </div>
        </a>
    </div>
</nav>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notificationDropdown = document.querySelector('.notification-dropdown');
            const notificationMenu = document.getElementById('notification-menu');
            const notificationBadge = document.getElementById('notification-badge');
            const notificationList = document.getElementById('notification-list');
            const unreadTab = document.getElementById('unread-tab');
            const readTab = document.getElementById('read-tab');

            function positionDropdown() {
                if (!notificationMenu) return;

                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;

                const maxHeight = Math.min(viewportHeight * 0.8, viewportHeight - 100);
                notificationMenu.style.maxHeight = maxHeight + 'px';

                if (notificationList) {
                    const headerHeight = notificationMenu.querySelector('.border-b').offsetHeight || 0;
                    const tabsHeight = notificationMenu.querySelector('.flex.border-b').offsetHeight || 0;
                    const availableHeight = maxHeight - headerHeight - tabsHeight;
                    notificationList.style.maxHeight = `${availableHeight}px`;
                }
            }

            let notifications = {
                unread: [],
                read: []
            };
            let isDropdownOpen = false;
            let isNotificationsLoaded = false;
            let isLoading = false;
            let currentTab = 'unread';
            let page = {
                unread: 1,
                read: 1
            };
            let hasMorePages = {
                unread: true,
                read: true
            };
            let lastFetchTime = 0;
            let unreadCount = 0;

            function toggleDropdown() {
                if (isDropdownOpen) {
                    notificationMenu.classList.add('hidden');
                } else {
                    notificationMenu.classList.remove('hidden');
                    positionDropdown();

                    if (!isNotificationsLoaded && !isLoading) {
                        loadNotifications();
                    }
                }
                isDropdownOpen = !isDropdownOpen;
            }

            if (notificationDropdown) {
                notificationDropdown.addEventListener('click', function (e) {
                    if (e.target.closest('.mark-as-read')) {
                        return;
                    }
                    if (e.target.closest('#refresh-notifications')) {
                        e.stopPropagation();
                        page = {
                            unread: 1,
                            read: 1
                        };
                        isNotificationsLoaded = false;
                        loadNotifications();
                        return;
                    }
                    if (e.target.closest('#unread-tab') || e.target.closest('#read-tab')) {
                        e.stopPropagation();
                        return;
                    }
                    toggleDropdown();
                });
            }

            window.addEventListener('resize', function () {
                if (isDropdownOpen) {
                    positionDropdown();
                }
            });

            window.addEventListener('orientationchange', function () {
                if (isDropdownOpen) {
                    setTimeout(positionDropdown, 100);
                }
            });

            unreadTab.addEventListener('click', function () {
                if (currentTab !== 'unread') {
                    currentTab = 'unread';
                    updateTabUI();
                    page.unread = 1;
                    isNotificationsLoaded = false;
                    notifications.unread = [];
                    loadNotifications();
                }
            });

            readTab.addEventListener('click', function () {
                if (currentTab !== 'read') {
                    currentTab = 'read';
                    updateTabUI();
                    page.read = 1;
                    isNotificationsLoaded = false;
                    notifications.read = [];
                    loadNotifications();
                }
            });

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

            document.addEventListener('click', function (e) {
                if (isDropdownOpen && !notificationDropdown.contains(e.target)) {
                    notificationMenu.classList.add('hidden');
                    isDropdownOpen = false;
                }
            });

            function showLoading(isFirstLoad = true) {
                if (isFirstLoad) {
                    notificationList.innerHTML = '';
                } else {
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

            function loadNotifications(isLazyLoad = false) {
                if (isLoading) return;

                const currentPage = page[currentTab];
                showLoading(!isLazyLoad);

                fetch(`/notifications?limit=15&page=${currentPage}&is_read=${currentTab === 'read' ? 'true' : 'false'}`, {
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
                        lastFetchTime = Date.now();

                        if (data.success) {
                            const newNotifications = data.data;
                            const loadingMore = document.getElementById('loading-more');
                            if (loadingMore) {
                                loadingMore.remove();
                            }

                            notificationList.classList.add('overflow-y-auto', 'overflow-x-hidden');

                            if (data.pagination) {
                                hasMorePages[currentTab] = data.pagination.current_page < data.pagination.total_pages;
                                page[currentTab] = data.pagination.current_page + 1;
                            } else {
                                hasMorePages[currentTab] = false;
                            }

                            if (currentPage === 1 && !isLazyLoad && currentTab === 'unread') {
                                updateUnreadBadgeCount(data.pagination?.total_items || newNotifications.length);
                            }

                            if (isLazyLoad) {
                                if (currentTab === 'read') {
                                    notifications.read = [...notifications.read, ...newNotifications];
                                } else {
                                    notifications.unread = [...notifications.unread, ...newNotifications];
                                }
                            } else {
                                if (currentTab === 'read') {
                                    notifications.read = newNotifications;
                                } else {
                                    notifications.unread = newNotifications;
                                }
                            }

                            renderNotifications(newNotifications, !isLazyLoad);

                            isNotificationsLoaded = true;

                            if ((currentTab === 'unread' && notifications.unread.length === 0 && hasMorePages[currentTab]) ||
                                (currentTab === 'read' && notifications.read.length === 0 && hasMorePages[currentTab])) {
                                setTimeout(() => loadNotifications(isLazyLoad), 300);
                                return;
                            }

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

                            document.getElementById('retry-btn')?.addEventListener('click', function () {
                                page[currentTab] = 1;
                                loadNotifications();
                            });
                        }
                    });
            }

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

                    const now = new Date();
                    const diffMs = now - date;
                    const diffMins = Math.floor(diffMs / 60000);
                    const diffHours = Math.floor(diffMins / 60);
                    const diffDays = Math.floor(diffHours / 24);

                    const isNew = diffMins < 30;

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
                    notificationItem.className = `p-3 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200 break-words cursor-pointer
                        ${notification.is_read ? 'bg-gray-50' : isNew ? 'bg-[rgba(33,50,104,0.05)]' : 'bg-white'}`;
                    notificationItem.setAttribute('data-id', notification.id);

                    notificationItem.innerHTML = `
                        <div class="flex flex-col w-full">
                            <div class="notification-header flex items-start justify-between cursor-pointer group">
                            <div class="flex-1 min-w-0 pr-1">
                                    <h4 class="text-sm font-semibold ${notification.is_read ? 'text-gray-500' : 'text-[#232D42]'} mb-0.5 break-words">${notification.title}</h4>
                                        ${isNew && !notification.is_read ?
                            '<span class="text-xs bg-[#213268] text-white px-1.5 py-0.5 rounded-full inline-flex items-center justify-center min-w-[36px] mb-1">Baru</span>' :
                            ''}
                                    </div>
                                <div class="flex items-center gap-2 shrink-0 ml-1">
                                        <span class="text-xs text-gray-400 whitespace-nowrap" title="${formattedDate}">${timeAgo}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform duration-200 ${notification.is_read ? 'text-gray-400 rotate-180' : 'text-[#213268] group-hover:text-[#182451]'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="notification-detail ${notification.is_read ? '' : 'hidden'} mt-2">
                                <p class="text-xs text-gray-600 break-words">${notification.detail}</p>
                            </div>
                        </div>
                    `;

                    notificationItem.querySelector('.notification-header').addEventListener('click', function (e) {
                        e.stopPropagation();
                        const detailElement = notificationItem.querySelector('.notification-detail');
                        const chevronIcon = this.querySelector('svg');
                        const notificationId = notificationItem.getAttribute('data-id');

                        if (!notification.is_read) {
                            if (detailElement.classList.contains('hidden')) {
                                detailElement.classList.remove('hidden');
                                chevronIcon.classList.add('rotate-180');
                                chevronIcon.classList.remove('text-[#213268]', 'group-hover:text-[#182451]');
                                chevronIcon.classList.add('text-gray-400');
                                markAsRead(notificationId);
                            }
                        }
                    });

                    fragment.appendChild(notificationItem);
                });

                notificationList.appendChild(fragment);

                // Remove any existing end of notifications message
                const existingEndMessage = document.getElementById('end-of-notifications');
                if (existingEndMessage) existingEndMessage.remove();

                // Only show end message if we're on the last page
                if (!hasMorePages[currentTab]) {
                    const endMessage = document.createElement('div');
                    endMessage.id = 'end-of-notifications';
                    endMessage.className = 'p-3 text-center text-gray-500 border-t border-gray-100';
                    endMessage.innerHTML = `
                        <p class="text-xs">Tidak ada notifikasi lainnya</p>
                    `;
                    notificationList.appendChild(endMessage);
                } else {
                    // Add invisible scroll trigger for lazy loading
                    const scrollTrigger = document.createElement('div');
                    scrollTrigger.id = 'scroll-trigger';
                    scrollTrigger.className = 'h-5';
                    notificationList.appendChild(scrollTrigger);
                }
            }

            function markAsRead(notificationId) {
                const csrf_token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                if (!csrf_token) {
                    console.error('CSRF token not found');
                    return;
                }

                fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf_token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({})
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Failed with status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const notificationIndex = notifications.unread.findIndex(n => n.id == notificationId);

                            if (notificationIndex !== -1) {
                                const updatedNotification = {
                                    ...notifications.unread[notificationIndex],
                                    is_read: true,
                                    read_at: new Date().toISOString()
                                };

                                notifications.unread.splice(notificationIndex, 1);
                                notifications.read.unshift(updatedNotification);

                                const notificationElement = document.querySelector(`.p-3[data-id="${notificationId}"]`);
                                if (notificationElement) {
                                    notificationElement.classList.remove('bg-[rgba(33,50,104,0.05)]');
                                    notificationElement.classList.add('bg-gray-50');
                                    const titleElement = notificationElement.querySelector('h4');
                                    const chevronIcon = notificationElement.querySelector('.notification-header svg');
                                    if (titleElement) {
                                        titleElement.classList.remove('text-[#232D42]');
                                        titleElement.classList.add('text-gray-500');
                                    }
                                    if (chevronIcon) {
                                        chevronIcon.classList.remove('text-[#213268]', 'group-hover:text-[#182451]');
                                        chevronIcon.classList.add('text-gray-400', 'rotate-180');
                                    }
                                    const newBadge = notificationElement.querySelector('.bg-[#213268]');
                                    if (newBadge) {
                                        newBadge.remove();
                                    }
                                }

                                // Decrement the unread count
                                updateUnreadBadgeCount(unreadCount - 1);

                                // Check if all unread notifications have been read and we have more pages
                                if (notifications.unread.length === 0 && hasMorePages.unread && currentTab === 'unread') {
                                    setTimeout(() => loadNotifications(false), 300);
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error marking notification as read:', error);
                    });
            }

            function updateUnreadBadgeCount(count) {
                unreadCount = count > 0 ? count : 0;

                if (unreadCount > 0) {
                    notificationBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                    notificationBadge.classList.remove('hidden');

                    // Add bounce animation when count increases
                    notificationBadge.classList.add('animate-bounce');
                    setTimeout(() => {
                        notificationBadge.classList.remove('animate-bounce');
                    }, 1000);
                } else {
                    notificationBadge.classList.add('hidden');
                }
            }

            function checkForNewNotifications() {
                // Don't check if dropdown is open or if we checked recently
                if (isDropdownOpen || (Date.now() - lastFetchTime < 30000)) {
                    return;
                }

                fetch('/notifications?limit=1&is_read=false', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            lastFetchTime = Date.now();

                            if (data.pagination && data.pagination.total_items > unreadCount) {
                                updateUnreadBadgeCount(data.pagination.total_items);

                                // Visual indication of new notifications
                                const bell = notificationDropdown.querySelector('svg');
                                if (bell) {
                                    bell.classList.add('text-yellow-400', 'animate-pulse');
                                    setTimeout(() => {
                                        bell.classList.remove('text-yellow-400', 'animate-pulse');
                                    }, 3000);
                                }

                                // Reset notification lists to force reload when dropdown is opened
                                if (!isDropdownOpen) {
                                    isNotificationsLoaded = false;
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error checking for new notifications:', error);
                    });
            }

            // Initialize by loading unread count
            fetch('/notifications?limit=1&is_read=false', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.pagination) {
                        // Update the badge with the total number of unread notifications
                        updateUnreadBadgeCount(data.pagination.total_items || 0);
                        lastFetchTime = Date.now();
                    }
                })
                .catch(error => {
                    console.error('Error loading initial notification count:', error);
                });

            // Listen for scroll events to implement infinite scroll
            notificationList.addEventListener('scroll', function() {
                if (isLoading || !hasMorePages[currentTab]) return;

                const { scrollTop, scrollHeight, clientHeight } = notificationList;

                // Trigger loading when user scrolls near the bottom (20px from bottom)
                if (scrollHeight - scrollTop - clientHeight < 50) {
                    loadNotifications(true);
                }
            });

            // Check for new notifications periodically
            setInterval(checkForNewNotifications, 60000);
        });
    </script>
@endpush
