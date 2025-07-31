document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const remindersButton = document.getElementById('reminders-button');
    const remindersBackdrop = document.getElementById('reminders-backdrop');
    const remindersPopup = document.getElementById('reminders-popup');
    const closeReminders = document.getElementById('close-reminders');
    const remindersBadge = document.getElementById('reminders-badge');
    const calibrationTab = document.getElementById('calibration-tab');
    const maintenanceTab = document.getElementById('maintenance-tab');
    const assetTypeFilter = document.getElementById('asset-type-filter');
    const refreshReminders = document.getElementById('refresh-reminders');
    const categoriesTabs = document.querySelectorAll('.category-tab');
    const remindersTableContainer = document.getElementById('reminders-table-container');
    const calibrationTable = document.getElementById('calibration-table');
    const maintenanceTable = document.getElementById('maintenance-table');
    const calibrationTableBody = document.getElementById('calibration-table-body');
    const maintenanceTableBody = document.getElementById('maintenance-table-body');
    const calibrationMobileView = document.getElementById('calibration-mobile-view');
    const maintenanceMobileView = document.getElementById('maintenance-mobile-view');
    const calibrationLoadMore = document.getElementById('calibration-load-more');
    const maintenanceLoadMore = document.getElementById('maintenance-load-more');
    const remindersLoading = document.getElementById('reminders-loading');
    const remindersEmpty = document.getElementById('reminders-empty');
    const remindersError = document.getElementById('reminders-error');
    const remindersRetry = document.getElementById('reminders-retry');
    const remindersContent = document.getElementById('reminders-content');
    const remindersCategoriesTabs = document.getElementById('reminders-categories-tabs');

    // State
    let currentTab = 'calibration'; // calibration or maintenance
    let currentCategory = 'today';  // today, one_to_fourteen_days, etc.
    let assetType = 'medical';      // medical, non_medical
    let isRemindersLoaded = false;
    let isLoading = false;
    let isModalOpen = false;
    let itemsPerPage = 20;          // Number of items to load at once
    let currentPage = {
        calibration: {},            // Object to store current page for each category
        maintenance: {}             // e.g. { today: 1, missed: 2 }
    };
    let hasMorePages = {
        calibration: {},            // Object to store if there are more pages for each category
        maintenance: {}             // e.g. { today: true, missed: false }
    };
    let remindersData = {
        calibration: null,
        maintenance: null
    };

    // Add animation timing variables
    const animationDuration = 300; // ms

    // Check if we're on a mobile device
    const isMobile = () => window.innerWidth < 640;

    // Interval translations
    /* const intervalTranslations = {
        'ONCE': 'Sekali',
        'DAILY': 'Harian',
        'WEEKLY': 'Mingguan',
        '2 WEEKS': '2 Minggu',
        'MONTHLY': 'Bulanan',
        '2 MONTHS': '2 Bulan',
        '3 MONTHS': '3 Bulan',
        '4 MONTHS': '4 Bulan',
        '6 MONTHS': '6 Bulan',
        'YEARLY': 'Tahunan'
    }; */

    // Helper function to translate intervals
    /* function translateInterval(interval) {
        return intervalTranslations[interval] || interval;
    } */

    // Format date to Indonesian format
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);

        // Get day name in Indonesian
        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const dayName = dayNames[date.getDay()];

        // Get day of month
        const day = date.getDate();

        // Get month name in Indonesian
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const month = monthNames[date.getMonth()];

        // Get year
        const year = date.getFullYear();

        return `${dayName}, ${day} ${month} ${year}`;
    }

    // Toggle reminders modal with smooth animations
    function toggleRemindersModal() {
        if (isModalOpen) {
            // Animate modal closing
            remindersPopup.classList.add('opacity-0', 'transform', 'translate-y-2');
            remindersBackdrop.classList.add('opacity-0');

            // After animation, hide completely
            setTimeout(() => {
                remindersPopup.classList.add('hidden');
                remindersBackdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');

                // Reset transform for next opening
                remindersPopup.classList.remove('opacity-0', 'transform', 'translate-y-2');
            }, animationDuration);
        } else {
            // Show elements first (but with opacity 0)
            remindersPopup.classList.remove('hidden');
            remindersBackdrop.classList.remove('hidden');

            // Force reflow to ensure transition works
            void remindersPopup.offsetWidth;

            // Start animation
            document.body.classList.add('overflow-hidden');

            if (!isRemindersLoaded && !isLoading) {
                loadRemindersData();
            } else {
                updateViewBasedOnDeviceSize();
            }

            // Scroll category tabs to show active tab
            setTimeout(scrollCategoryTabIntoView, 100);
        }
        isModalOpen = !isModalOpen;
    }

    // Function to scroll category tabs to ensure active tab is visible
    function scrollCategoryTabIntoView() {
        if (!remindersCategoriesTabs) return;

        const activeTab = remindersCategoriesTabs.querySelector('.category-tab.active');
        if (activeTab) {
            const containerWidth = remindersCategoriesTabs.offsetWidth;
            const tabLeft = activeTab.offsetLeft;
            const tabWidth = activeTab.offsetWidth;

            // Calculate ideal scroll position to center the tab
            const idealScrollLeft = tabLeft - (containerWidth / 2) + (tabWidth / 2);

            // Smooth scroll
            remindersCategoriesTabs.parentElement.scrollTo({
                left: Math.max(0, idealScrollLeft),
                behavior: 'smooth'
            });
        }
    }

    // Update tab UI with smooth transitions
    function updateTabUI() {
        // Update tab styling
        if (currentTab === 'calibration') {
            calibrationTab.classList.add('text-[#213268]', 'border-b-2', 'border-[#213268]');
            calibrationTab.classList.remove('text-gray-500');
            maintenanceTab.classList.remove('text-[#213268]', 'border-b-2', 'border-[#213268]');
            maintenanceTab.classList.add('text-gray-500');
        } else {
            maintenanceTab.classList.add('text-[#213268]', 'border-b-2', 'border-[#213268]');
            maintenanceTab.classList.remove('text-gray-500');
            calibrationTab.classList.remove('text-[#213268]', 'border-b-2', 'border-[#213268]');
            calibrationTab.classList.add('text-gray-500');
        }

        // Temporarily hide all views for transition effect
        calibrationTable.classList.add('hidden');
        maintenanceTable.classList.add('hidden');
        calibrationMobileView.classList.add('hidden');
        maintenanceMobileView.classList.add('hidden');

        // After brief delay, show appropriate view with fade-in effect
        setTimeout(() => {
            if (currentTab === 'calibration') {
                if (isMobile()) {
                    calibrationMobileView.classList.remove('hidden');
                    calibrationMobileView.classList.add('animate-fadeIn');
                } else {
                    calibrationTable.classList.remove('hidden');
                    calibrationTable.classList.add('animate-fadeIn');
                }
            } else {
                if (isMobile()) {
                    maintenanceMobileView.classList.remove('hidden');
                    maintenanceMobileView.classList.add('animate-fadeIn');
                } else {
                    maintenanceTable.classList.remove('hidden');
                    maintenanceTable.classList.add('animate-fadeIn');
                }
            }
        }, 50);
    }

    // Update category tab UI with smooth transitions
    function updateCategoryTabUI() {
        categoriesTabs.forEach(tab => {
            const category = tab.getAttribute('data-category');
            if (category === currentCategory) {
                tab.classList.add('active', 'text-[#213268]', 'border-b-2', 'border-[#213268]');
                tab.classList.remove('text-gray-500');
            } else {
                tab.classList.remove('active', 'text-[#213268]', 'border-b-2', 'border-[#213268]');
                tab.classList.add('text-gray-500');
            }
        });

        // Scroll to make active category visible
        scrollCategoryTabIntoView();
    }

    // Show loading state
    function showLoading() {
        remindersLoading.classList.remove('hidden');
        remindersLoading.classList.add('animate-fadeIn');
        calibrationTable.classList.add('hidden');
        maintenanceTable.classList.add('hidden');
        calibrationMobileView.classList.add('hidden');
        maintenanceMobileView.classList.add('hidden');
        remindersEmpty.classList.add('hidden');
        remindersError.classList.add('hidden');
        calibrationLoadMore.classList.add('hidden');
        maintenanceLoadMore.classList.add('hidden');
        isLoading = true;
    }

    // Hide loading state
    function hideLoading() {
        // Add fade-out animation
        remindersLoading.classList.add('opacity-0');
        setTimeout(() => {
            remindersLoading.classList.add('hidden');
            remindersLoading.classList.remove('opacity-0');
        }, 150);
        isLoading = false;
    }

    // Show error state
    function showError() {
        hideLoading();
        remindersError.classList.remove('hidden');
        remindersError.classList.add('animate-fadeIn');
        calibrationTable.classList.add('hidden');
        maintenanceTable.classList.add('hidden');
        calibrationMobileView.classList.add('hidden');
        maintenanceMobileView.classList.add('hidden');
        remindersEmpty.classList.add('hidden');
    }

    // Show empty state
    function showEmpty() {
        hideLoading();
        remindersEmpty.classList.remove('hidden');
        remindersEmpty.classList.add('animate-fadeIn');
        calibrationTable.classList.add('hidden');
        maintenanceTable.classList.add('hidden');
        calibrationMobileView.classList.add('hidden');
        maintenanceMobileView.classList.add('hidden');
        remindersError.classList.add('hidden');
    }

    // Show table based on current tab and device size
    function showTable() {
        hideLoading();
        remindersEmpty.classList.add('hidden');
        remindersError.classList.add('hidden');

        if (isMobile()) {
            // Show mobile views with animation
            if (currentTab === 'calibration') {
                calibrationMobileView.classList.remove('hidden');
                calibrationMobileView.classList.add('animate-fadeIn');
                maintenanceMobileView.classList.add('hidden');
                calibrationTable.classList.add('hidden');
                maintenanceTable.classList.add('hidden');

                // Show load more button if there are more pages
                if (hasMorePages.calibration[currentCategory]) {
                    calibrationLoadMore.classList.remove('hidden');
                    calibrationLoadMore.classList.add('animate-fadeIn');
                } else {
                    calibrationLoadMore.classList.add('hidden');
                }
            } else {
                maintenanceMobileView.classList.remove('hidden');
                maintenanceMobileView.classList.add('animate-fadeIn');
                calibrationMobileView.classList.add('hidden');
                calibrationTable.classList.add('hidden');
                maintenanceTable.classList.add('hidden');

                // Show load more button if there are more pages
                if (hasMorePages.maintenance[currentCategory]) {
                    maintenanceLoadMore.classList.remove('hidden');
                    maintenanceLoadMore.classList.add('animate-fadeIn');
                } else {
                    maintenanceLoadMore.classList.add('hidden');
                }
            }
        } else {
            // Show desktop tables with animation
            if (currentTab === 'calibration') {
                calibrationTable.classList.remove('hidden');
                calibrationTable.classList.add('animate-fadeIn');
                maintenanceTable.classList.add('hidden');
                calibrationMobileView.classList.add('hidden');
                maintenanceMobileView.classList.add('hidden');

                // Show load more button if there are more pages
                if (hasMorePages.calibration[currentCategory]) {
                    calibrationLoadMore.classList.remove('hidden');
                    calibrationLoadMore.classList.add('animate-fadeIn');
                } else {
                    calibrationLoadMore.classList.add('hidden');
                }
            } else {
                maintenanceTable.classList.remove('hidden');
                maintenanceTable.classList.add('animate-fadeIn');
                calibrationTable.classList.add('hidden');
                calibrationMobileView.classList.add('hidden');
                maintenanceMobileView.classList.add('hidden');

                // Show load more button if there are more pages
                if (hasMorePages.maintenance[currentCategory]) {
                    maintenanceLoadMore.classList.remove('hidden');
                    maintenanceLoadMore.classList.add('animate-fadeIn');
                } else {
                    maintenanceLoadMore.classList.add('hidden');
                }
            }
        }
    }

    // Load reminders data with improved error handling
    function loadRemindersData(loadMore = false) {
        if (isLoading) return;

        // If it's not a load more action, show loading indicator
        if (!loadMore) {
            showLoading();
        } else {
            // For load more, show inline loading indicator in the button
            const loadMoreBtn = currentTab === 'calibration'
                ? calibrationLoadMore.querySelector('button')
                : maintenanceLoadMore.querySelector('button');

            const originalText = loadMoreBtn.innerHTML;
            loadMoreBtn.disabled = true;
            loadMoreBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memuat...
            `;
        }

        const endpoint = currentTab === 'calibration'
            ? '/calibrations/task-reminders'
            : '/maintenance/task-reminders';

        // Initialize the page counter if not already set
        if (!currentPage[currentTab][currentCategory]) {
            currentPage[currentTab][currentCategory] = 1;
        }

        // If loading more, increment the page number
        if (loadMore) {
            currentPage[currentTab][currentCategory]++;
        }

        const page = currentPage[currentTab][currentCategory];

        const params = new URLSearchParams();
        params.append('asset_type', assetType);
        params.append('page', page);
        params.append('limit', itemsPerPage);
        params.append('category', currentCategory);

        // Add timeout for better error handling
        const timeoutPromise = new Promise((_, reject) => {
            setTimeout(() => reject(new Error('Request timed out')), 15000); // 15 seconds timeout
        });

        // Fetch data with timeout
        Promise.race([
            fetch(`${endpoint}?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }),
            timeoutPromise
        ])
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load reminders data');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.data) {
                // For the first load, store the complete data
                if (!loadMore) {
                    remindersData[currentTab] = data.data;
                }

                isRemindersLoaded = true;

                // Update counts for each category
                updateCategoryCounts(data.data);

                // Update pagination information
                if (data.pagination) {
                    hasMorePages[currentTab][currentCategory] = data.pagination.has_next;
                } else {
                    // If pagination info not provided, assume no more pages
                    hasMorePages[currentTab][currentCategory] = false;
                }

                // Render the current category data
                renderCategoryData(data.data[currentCategory], loadMore);
            } else {
                showError();
            }
        })
        .catch(error => {
            console.error('Error loading reminders:', error);
            showError();
        })
        .finally(() => {
            if (loadMore) {
                // Reset load more button
                const loadMoreBtn = currentTab === 'calibration'
                    ? calibrationLoadMore.querySelector('button')
                    : maintenanceLoadMore.querySelector('button');
                loadMoreBtn.disabled = false;
                loadMoreBtn.innerHTML = 'Muat Lebih Banyak';
            }
        });
    }

    // Update category counts with badge animations
    function updateCategoryCounts(data) {
        if (!data) return;

        for (const category in data) {
            const countElement = document.querySelector(`.${category}-count`);
            if (countElement && data[category] && data[category].total > 0) {
                // Animate count change if the value is different
                if (countElement.textContent !== data[category].total.toString()) {
                    countElement.classList.add('animate-pulse');
                    setTimeout(() => countElement.classList.remove('animate-pulse'), 1000);
                }
                countElement.textContent = data[category].total;
                countElement.classList.remove('hidden');
            } else if (countElement) {
                countElement.classList.add('hidden');
            }
        }
    }

    // Render category data with improved mobile cards
    function renderCategoryData(categoryData, loadMore = false) {
        if (!categoryData || !categoryData.items || categoryData.items.length === 0) {
            if (!loadMore) {
                showEmpty();
            }
            return;
        }

        const tableBody = currentTab === 'calibration'
            ? calibrationTableBody
            : maintenanceTableBody;

        const mobileView = currentTab === 'calibration'
            ? calibrationMobileView
            : maintenanceMobileView;

        // Clear existing table rows and mobile cards if not loading more
        if (!loadMore) {
            tableBody.innerHTML = '';
            mobileView.innerHTML = '';
        }

        // Add rows for each item with staggered animations
        categoryData.items.forEach((item, index) => {
            // Create row for desktop table view
            const row = document.createElement('tr');
            row.style.animationDelay = `${index * 50}ms`;
            row.className = 'hover:bg-gray-50 transition-colors';

            // Common columns for both calibration and maintenance
            let rowHTML = `
                <td class="px-4 py-3 text-sm font-medium text-gray-900">${item.asset_name}</td>
                <td class="px-4 py-3 text-sm text-gray-600">${item.asset_code}</td>
                <td class="px-4 py-3 text-sm text-gray-600">${item.brand || '-'}</td>
                <td class="px-4 py-3 text-sm text-gray-600">${item.location || '-'}</td>
                <td class="px-4 py-3 text-sm font-medium text-blue-700">${item.schedule}</td>
            `;

            // Add maintenance-specific columns if in maintenance tab
            if (currentTab === 'maintenance') {
                rowHTML += `
                    <td class="px-4 py-3 text-sm text-gray-600">${item.assigned_to || '-'}</td>
                `;
                /* rowHTML += `
                    <td class="px-4 py-3 text-sm text-gray-600">${translateInterval(item.interval) || '-'}</td>
                `; */
            }

            row.innerHTML = rowHTML;
            tableBody.appendChild(row);

            // Create card for mobile view - styled to match the screenshot with improved UI
            const card = document.createElement('div');
            card.className = 'reminder-card bg-white p-4';
            card.style.animationDelay = `${index * 50}ms`;

            // Get appropriate badge color based on schedule category
            const badgeColor = getBadgeColorByCategory(currentCategory);

            let cardContent = `
                <div class="flex items-start justify-between mb-3">
                    <div class="asset-name">${item.asset_name}</div>
                    <div class="schedule-date" style="background-color: ${badgeColor.bg}; color: ${badgeColor.text}">
                        ${item.schedule}
                    </div>
                </div>
                <div class="info-content">
                    <div class="info-row">
                        <div class="info-label">Kode Aset:</div>
                        <div class="info-value">${item.asset_code}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Merek:</div>
                        <div class="info-value">${item.brand || '-'}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Lokasi:</div>
                        <div class="info-value">${item.location || '-'}</div>
                    </div>
            `;

            // Add maintenance-specific info for mobile
            if (currentTab === 'maintenance') {
                cardContent += `
                    <div class="info-row">
                        <div class="info-label">Penanggung Jawab:</div>
                        <div class="info-value">${item.assigned_to || '-'}</div>
                    </div>
                `;
                /* cardContent += `
                    <div class="info-row">
                        <div class="info-label">Interval:</div>
                        <div class="info-value">${translateInterval(item.interval) || '-'}</div>
                    </div>
                `; */
            }

            cardContent += `</div>`;
            card.innerHTML = cardContent;
            mobileView.appendChild(card);
        });

        showTable();
    }

    // Helper function to get badge colors based on category
    function getBadgeColorByCategory(category) {
        switch(category) {
            case 'today':
                return { bg: '#fee2e2', text: '#b91c1c' }; // Red
            case 'one_to_fourteen_days':
                return { bg: '#ffedd5', text: '#c2410c' }; // Orange
            case 'fifteen_to_thirty_days':
                return { bg: '#fef9c3', text: '#854d0e' }; // Yellow
            case 'one_to_two_months':
                return { bg: '#dbeafe', text: '#1e40af' }; // Blue
            case 'two_to_three_months':
                return { bg: '#e0e7ff', text: '#3730a3' }; // Indigo
            case 'more_than_3_months':
                return { bg: '#f3e8ff', text: '#6b21a8' }; // Purple
            case 'missed':
                return { bg: '#f3f4f6', text: '#4b5563' }; // Gray
            default:
                return { bg: '#dbeafe', text: '#1e40af' }; // Default Blue
        }
    }

    // Update views based on device size
    function updateViewBasedOnDeviceSize() {
        // Add transition classes for smooth view changes
        calibrationTable.classList.add('transition-opacity');
        maintenanceTable.classList.add('transition-opacity');
        calibrationMobileView.classList.add('transition-opacity');
        maintenanceMobileView.classList.add('transition-opacity');

        if (isMobile()) {
            // We're on mobile
            calibrationTable.classList.add('hidden');
            maintenanceTable.classList.add('hidden');

            if (currentTab === 'calibration') {
                calibrationMobileView.classList.remove('hidden');
                maintenanceMobileView.classList.add('hidden');
            } else {
                maintenanceMobileView.classList.remove('hidden');
                calibrationMobileView.classList.add('hidden');
            }
        } else {
            // We're on desktop
            calibrationMobileView.classList.add('hidden');
            maintenanceMobileView.classList.add('hidden');

            if (currentTab === 'calibration') {
                calibrationTable.classList.remove('hidden');
                maintenanceTable.classList.add('hidden');
            } else {
                maintenanceTable.classList.remove('hidden');
                calibrationTable.classList.add('hidden');
            }
        }

        // Show load more button if appropriate
        if (currentTab === 'calibration' && hasMorePages.calibration[currentCategory]) {
            calibrationLoadMore.classList.remove('hidden');
        } else {
            calibrationLoadMore.classList.add('hidden');
        }

        if (currentTab === 'maintenance' && hasMorePages.maintenance[currentCategory]) {
            maintenanceLoadMore.classList.remove('hidden');
        } else {
            maintenanceLoadMore.classList.add('hidden');
        }
    }

    // Initialize reminders UI
    function initReminders() {
        // Add animation styles dynamically
        addAnimationStyles();

        // Setup event listeners for the reminders button
        if (remindersButton) {
            remindersButton.addEventListener('click', function(e) {
                toggleRemindersModal();
            });
        }

        // Close modal when clicking on close button only (removed backdrop click event)
        closeReminders.addEventListener('click', toggleRemindersModal);

        // Tab switching with better transitions
        calibrationTab.addEventListener('click', function() {
            if (currentTab !== 'calibration') {
                currentTab = 'calibration';
                updateTabUI();

                if (remindersData.calibration) {
                    // Use cached data if available
                    updateCategoryCounts(remindersData.calibration);
                    renderCategoryData(remindersData.calibration[currentCategory]);
                } else {
                    loadRemindersData();
                }
            }
        });

        maintenanceTab.addEventListener('click', function() {
            if (currentTab !== 'maintenance') {
                currentTab = 'maintenance';
                updateTabUI();

                if (remindersData.maintenance) {
                    // Use cached data if available
                    updateCategoryCounts(remindersData.maintenance);
                    renderCategoryData(remindersData.maintenance[currentCategory]);
                } else {
                    loadRemindersData();
                }
            }
        });

        // Category tab switching with smooth transitions
        categoriesTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                if (category !== currentCategory) {
                    currentCategory = category;
                    updateCategoryTabUI();

                    // Reset page counter for new category
                    currentPage[currentTab][currentCategory] = 1;

                    // Show a brief loading indicator for better UX
                    showLoading();

                    setTimeout(() => {
                        const currentData = remindersData[currentTab];
                        if (currentData && currentData[category]) {
                            renderCategoryData(currentData[category]);
                        } else {
                            loadRemindersData();
                        }
                    }, 300);
                }
            });
        });

        // Asset type filter change with improved UI
        assetTypeFilter.addEventListener('change', function() {
            const previousType = assetType;
            assetType = this.value;

            // Only reload if value actually changed
            if (previousType !== assetType) {
                isRemindersLoaded = false;
                remindersData.calibration = null;
                remindersData.maintenance = null;

                // Reset all pagination counters
                currentPage = { calibration: {}, maintenance: {} };
                hasMorePages = { calibration: {}, maintenance: {} };

                // Show the filter being applied with animation
                assetTypeFilter.classList.add('bg-blue-50');
                setTimeout(() => {
                    assetTypeFilter.classList.remove('bg-blue-50');
                    loadRemindersData();
                }, 300);
            }
        });

        // Refresh button with animation
        refreshReminders.addEventListener('click', function() {
            // Add rotation animation to refresh icon
            refreshReminders.classList.add('animate-spin');

            isRemindersLoaded = false;
            remindersData.calibration = null;
            remindersData.maintenance = null;

            // Reset all pagination counters
            currentPage = { calibration: {}, maintenance: {} };
            hasMorePages = { calibration: {}, maintenance: {} };

            // Remove animation after delay and reload data
            setTimeout(() => {
                refreshReminders.classList.remove('animate-spin');
                loadRemindersData();
            }, 600);
        });

        // Load more buttons with improved UI feedback
        calibrationLoadMore.querySelector('button').addEventListener('click', function() {
            loadRemindersData(true);
        });

        maintenanceLoadMore.querySelector('button').addEventListener('click', function() {
            loadRemindersData(true);
        });

        // Retry button with improved UI feedback
        remindersRetry.addEventListener('click', function() {
            const originalText = remindersRetry.textContent;
            remindersRetry.innerHTML = 'Mencoba ulang...';
            remindersRetry.disabled = true;

            setTimeout(() => {
                remindersRetry.innerHTML = originalText;
                remindersRetry.disabled = false;
                loadRemindersData();
            }, 300);
        });

        // Implement scroll-based lazy loading with threshold
        let scrollDebounceTimer;
        remindersContent.addEventListener('scroll', function() {
            if (isLoading) return;

            // Debounce scroll handling
            clearTimeout(scrollDebounceTimer);
            scrollDebounceTimer = setTimeout(() => {
                const scrollHeight = this.scrollHeight;
                const scrollTop = this.scrollTop;
                const clientHeight = this.clientHeight;

                // If scrolled to near bottom (within 200px) and there are more pages
                if (scrollTop + clientHeight >= scrollHeight - 200) {
                    if (currentTab === 'calibration' && hasMorePages.calibration[currentCategory]) {
                        loadRemindersData(true);
                    } else if (currentTab === 'maintenance' && hasMorePages.maintenance[currentCategory]) {
                        loadRemindersData(true);
                    }
                }
            }, 100);
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isModalOpen) {
                toggleRemindersModal();
            }
        });

        // Handle resize events for responsive layout
        let resizeDebounceTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeDebounceTimer);
            resizeDebounceTimer = setTimeout(() => {
                if (isModalOpen) {
                    updateViewBasedOnDeviceSize();
                }
            }, 250); // Debounce resize events
        });

        // Double click on header to scroll to top
        const modalHeader = document.querySelector('#reminders-popup > div:first-child');
        if (modalHeader) {
            modalHeader.addEventListener('dblclick', function(e) {
                // Don't trigger if clicking on controls
                if (e.target.closest('select') || e.target.closest('button')) {
                    return;
                }

                // Smooth scroll to top
                remindersContent.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    }

    // Add animation styles dynamically
    function addAnimationStyles() {
        // Create style element
        const styleEl = document.createElement('style');
        styleEl.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-fadeIn {
                animation: fadeIn 0.3s ease-out forwards;
            }

            .animate-pulse {
                animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }

            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }

            .animate-spin {
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            /* Fade transition for views */
            #calibration-table, #maintenance-table,
            #calibration-mobile-view, #maintenance-mobile-view {
                transition: opacity 0.2s ease-out;
            }

            /* Button transitions */
            button {
                transition: all 0.2s ease;
            }
        `;
        document.head.appendChild(styleEl);
    }

    // Initialize if DOM is ready
    if (remindersButton && remindersPopup) {
        initReminders();
    }
});
