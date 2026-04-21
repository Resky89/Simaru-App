
/**
 * Reminders Popup Manager - Refactored Version
 * Handles calibration and maintenance reminders display
 */
class RemindersPopup {
    constructor() {
        this.initializeElements();
        this.initializeState();
        this.initializeStyles();
        this.bindEvents();
    }

    initializeElements() {
        this.elements = {
            // Modal controls
            button: document.getElementById('reminders-button'),
            backdrop: document.getElementById('reminders-backdrop'),
            popup: document.getElementById('reminders-popup'),
            close: document.getElementById('close-reminders'),

            // Tabs and navigation
            calibrationTab: document.getElementById('calibration-tab'),
            maintenanceTab: document.getElementById('maintenance-tab'),
            categoryTabs: document.querySelectorAll('.category-tab'),
            categoriesContainer: document.getElementById('reminders-categories-tabs'),

            // Controls
            assetTypeFilter: document.getElementById('asset-type-filter'),
            refreshButton: document.getElementById('refresh-reminders'),

            // Content areas
            content: document.getElementById('reminders-content'),
            calibrationTable: document.getElementById('calibration-table'),
            maintenanceTable: document.getElementById('maintenance-table'),
            calibrationTableBody: document.getElementById('calibration-table-body'),
            maintenanceTableBody: document.getElementById('maintenance-table-body'),
            calibrationMobileView: document.getElementById('calibration-mobile-view'),
            maintenanceMobileView: document.getElementById('maintenance-mobile-view'),

            // Load more buttons
            calibrationLoadMore: document.getElementById('calibration-load-more'),
            maintenanceLoadMore: document.getElementById('maintenance-load-more'),

            // State indicators
            loading: document.getElementById('reminders-loading'),
            empty: document.getElementById('reminders-empty'),
            error: document.getElementById('reminders-error'),
            retry: document.getElementById('reminders-retry')
        };
    }

    initializeState() {
        this.state = {
            currentTab: 'calibration',
            currentCategory: 'today',
            assetType: '', // Changed default to non_medical
            isLoaded: false,
            isLoading: false,
            isModalOpen: false,
            itemsPerPage: 20
        };

        this.pagination = {
            currentPage: { calibration: {}, maintenance: {} },
            hasMorePages: { calibration: {}, maintenance: {} }
        };

        this.cache = {
        calibration: null,
        maintenance: null
    };

        this.config = {
            animationDuration: 300,
            requestTimeout: 15000,
            scrollThreshold: 200
        };
    }

    initializeStyles() {
        const styleId = 'reminders-popup-styles';
        if (document.getElementById(styleId)) return;

        const styles = document.createElement('style');
        styles.id = styleId;
        styles.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.7; }
            }

            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            .animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
            .animate-pulse { animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
            .animate-spin { animation: spin 1s linear infinite; }

            .reminder-card {
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                margin-bottom: 12px;
                transition: all 0.2s ease;
            }

            .reminder-card:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                border-color: #d1d5db;
            }

            .asset-name {
                font-weight: 600;
                color: #111827;
                font-size: 14px;
                line-height: 1.4;
            }

            .schedule-date {
                font-size: 12px;
                font-weight: 500;
                padding: 4px 8px;
                border-radius: 6px;
                white-space: nowrap;
            }

            .info-content { margin-top: 12px; }

            .info-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 6px;
                font-size: 13px;
            }

            .info-label {
                color: #6b7280;
                font-weight: 500;
            }

            .info-value {
                color: #374151;
                font-weight: 400;
                text-align: right;
                max-width: 60%;
                word-break: break-word;
            }

            .loading-spinner {
                display: inline-block;
                width: 16px;
                height: 16px;
                border: 2px solid #f3f4f6;
                border-radius: 50%;
                border-top-color: #3b82f6;
                animation: spin 1s ease-in-out infinite;
            }
        `;
        document.head.appendChild(styles);
    }

    bindEvents() {
        // Modal controls
        this.elements.button?.addEventListener('click', () => this.toggleModal());
        this.elements.close?.addEventListener('click', () => this.toggleModal());

        // Tab switching
        this.elements.calibrationTab?.addEventListener('click', () => this.switchTab('calibration'));
        this.elements.maintenanceTab?.addEventListener('click', () => this.switchTab('maintenance'));

        // Category tabs
        this.elements.categoryTabs?.forEach(tab => {
            tab.addEventListener('click', () => {
                const category = tab.getAttribute('data-category');
                this.switchCategory(category);
            });
        });

        // Controls
        this.elements.assetTypeFilter?.addEventListener('change', (e) => this.changeAssetType(e.target.value));
        this.elements.refreshButton?.addEventListener('click', () => this.refreshData());
        this.elements.retry?.addEventListener('click', () => this.retryLoad());

        // Load more buttons
        this.elements.calibrationLoadMore?.querySelector('button')?.addEventListener('click', () => this.loadMoreData());
        this.elements.maintenanceLoadMore?.querySelector('button')?.addEventListener('click', () => this.loadMoreData());

        // Keyboard and scroll events
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.state.isModalOpen) this.toggleModal();
        });

        this.elements.content?.addEventListener('scroll', this.debounce(() => this.handleScroll(), 100));
        window.addEventListener('resize', this.debounce(() => this.updateViewBasedOnDevice(), 250));
    }

    toggleModal() {
        if (this.state.isModalOpen) {
            this.closeModal();
        } else {
            this.openModal();
        }
    }

    openModal() {
        this.elements.backdrop?.classList.remove('hidden');
        this.elements.popup?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        void this.elements.popup?.offsetWidth; // Force reflow

        this.elements.popup?.classList.remove('opacity-0', 'transform', 'translate-y-2');
        this.elements.backdrop?.classList.remove('opacity-0');

        this.state.isModalOpen = true;

        if (!this.state.isLoaded && !this.state.isLoading) {
            this.loadData();
        } else {
            this.updateViewBasedOnDevice();
        }

        setTimeout(() => this.scrollCategoryTabIntoView(), 100);
    }

    closeModal() {
        this.elements.popup?.classList.add('opacity-0', 'transform', 'translate-y-2');
        this.elements.backdrop?.classList.add('opacity-0');

        setTimeout(() => {
            this.elements.popup?.classList.add('hidden');
            this.elements.backdrop?.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            this.elements.popup?.classList.remove('opacity-0', 'transform', 'translate-y-2');
        }, this.config.animationDuration);

        this.state.isModalOpen = false;
    }

    switchTab(tab) {
        if (this.state.currentTab === tab) return;

        this.state.currentTab = tab;
        this.updateTabUI();

        if (this.cache[tab]) {
            this.updateCategoryCounts(this.cache[tab]);
            this.renderCategoryData(this.cache[tab][this.state.currentCategory]);
            } else {
            this.loadData();
        }
    }

    switchCategory(category) {
        if (this.state.currentCategory === category) return;

        this.state.currentCategory = category;
        this.updateCategoryTabUI();
        this.pagination.currentPage[this.state.currentTab][category] = 1;

        this.showLoading();

        setTimeout(() => {
            const currentData = this.cache[this.state.currentTab];
            if (currentData && currentData[category]) {
                this.renderCategoryData(currentData[category]);
            } else {
                this.loadData();
            }
        }, 300);
    }

    changeAssetType(newType) {
        if (this.state.assetType === newType) return;

        this.state.assetType = newType;
        this.resetDataAndPagination();

        this.elements.assetTypeFilter?.classList.add('bg-blue-50');
        setTimeout(() => {
            this.elements.assetTypeFilter?.classList.remove('bg-blue-50');
            this.loadData();
        }, 300);
    }

    refreshData() {
        this.elements.refreshButton?.classList.add('animate-spin');
        this.resetDataAndPagination();

        setTimeout(() => {
            this.elements.refreshButton?.classList.remove('animate-spin');
            this.loadData();
        }, 600);
    }

    retryLoad() {
        const originalText = this.elements.retry?.textContent;

        if (this.elements.retry) {
            this.elements.retry.innerHTML = 'Mencoba ulang...';
            this.elements.retry.disabled = true;
        }

        setTimeout(() => {
            if (this.elements.retry) {
                this.elements.retry.innerHTML = originalText;
                this.elements.retry.disabled = false;
            }
            this.loadData();
        }, 300);
    }

    loadMoreData() {
        if (this.state.isLoading) return;
        this.loadData(true);
    }

    async loadData(loadMore = false) {
        if (this.state.isLoading) return;

        if (!loadMore) {
            this.showLoading();
        } else {
            this.showLoadMoreLoading();
        }

        try {
            const endpoint = this.state.currentTab === 'calibration'
            ? '/calibrations/task-reminders'
            : '/maintenance/task-reminders';

            if (!this.pagination.currentPage[this.state.currentTab][this.state.currentCategory]) {
                this.pagination.currentPage[this.state.currentTab][this.state.currentCategory] = 1;
        }

        if (loadMore) {
                this.pagination.currentPage[this.state.currentTab][this.state.currentCategory]++;
            }

            const page = this.pagination.currentPage[this.state.currentTab][this.state.currentCategory];

            const params = new URLSearchParams({
                asset_type: this.state.assetType,
                page: page.toString(),
                limit: this.state.itemsPerPage.toString(),
                category: this.state.currentCategory
            });

            const response = await this.fetchWithTimeout(`${endpoint}?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: Failed to load reminders`);
            }

            const data = await response.json();

            if (data.success && data.data) {
                this.handleDataSuccess(data, loadMore);
            } else {
                throw new Error('Invalid response format');
            }

        } catch (error) {
            console.error('Error loading reminders:', error);
            this.showError();
        } finally {
            if (loadMore) {
                this.hideLoadMoreLoading();
            }
        }
    }

    handleDataSuccess(data, loadMore) {
        if (!loadMore) {
            this.cache[this.state.currentTab] = data.data;
        }

        this.state.isLoaded = true;
        this.updateCategoryCounts(data.data);

        if (data.pagination) {
            this.pagination.hasMorePages[this.state.currentTab][this.state.currentCategory] = data.pagination.has_next;
        } else {
            this.pagination.hasMorePages[this.state.currentTab][this.state.currentCategory] = false;
        }

        this.renderCategoryData(data.data[this.state.currentCategory], loadMore);
    }

    updateTabUI() {
        const activeClasses = ['text-[#213268]', 'border-b-2', 'border-[#213268]'];
        const inactiveClasses = ['text-gray-500'];

        [this.elements.calibrationTab, this.elements.maintenanceTab].forEach(tab => {
            tab?.classList.remove(...activeClasses, ...inactiveClasses);
        });

        if (this.state.currentTab === 'calibration') {
            this.elements.calibrationTab?.classList.add(...activeClasses);
            this.elements.maintenanceTab?.classList.add(...inactiveClasses);
        } else {
            this.elements.maintenanceTab?.classList.add(...activeClasses);
            this.elements.calibrationTab?.classList.add(...inactiveClasses);
        }

        this.hideAllViews();
        setTimeout(() => this.showCurrentView(), 50);
    }

    updateCategoryTabUI() {
        this.elements.categoryTabs?.forEach(tab => {
            const category = tab.getAttribute('data-category');
            const activeClasses = ['active', 'text-[#213268]', 'border-b-2', 'border-[#213268]'];
            const inactiveClasses = ['text-gray-500'];

            tab.classList.remove(...activeClasses, ...inactiveClasses);

            if (category === this.state.currentCategory) {
                tab.classList.add(...activeClasses);
            } else {
                tab.classList.add(...inactiveClasses);
            }
        });

        this.scrollCategoryTabIntoView();
    }

    scrollCategoryTabIntoView() {
        if (!this.elements.categoriesContainer) return;

        const activeTab = this.elements.categoriesContainer.querySelector('.category-tab.active');
        if (!activeTab) return;

        const containerWidth = this.elements.categoriesContainer.offsetWidth;
        const tabLeft = activeTab.offsetLeft;
        const tabWidth = activeTab.offsetWidth;

        const idealScrollLeft = tabLeft - (containerWidth / 2) + (tabWidth / 2);

        this.elements.categoriesContainer.parentElement?.scrollTo({
            left: Math.max(0, idealScrollLeft),
            behavior: 'smooth'
        });
    }

    updateCategoryCounts(data) {
        if (!data) return;

        Object.keys(data).forEach(category => {
            const countElement = document.querySelector(`.${category}-count`);
            if (!countElement) return;

            const count = data[category]?.total || 0;

            if (count > 0) {
                if (countElement.textContent !== count.toString()) {
                    countElement.classList.add('animate-pulse');
                    setTimeout(() => countElement.classList.remove('animate-pulse'), 1000);
                }
                countElement.textContent = count.toString();
                countElement.classList.remove('hidden');
            } else {
                countElement.classList.add('hidden');
            }
        });
    }

    renderCategoryData(categoryData, loadMore = false) {
        if (!categoryData || !categoryData.items || categoryData.items.length === 0) {
            if (!loadMore) this.showEmpty();
            return;
        }

        const tableBody = this.state.currentTab === 'calibration'
            ? this.elements.calibrationTableBody
            : this.elements.maintenanceTableBody;

        const mobileView = this.state.currentTab === 'calibration'
            ? this.elements.calibrationMobileView
            : this.elements.maintenanceMobileView;

        if (!loadMore) {
            if (tableBody) tableBody.innerHTML = '';
            if (mobileView) mobileView.innerHTML = '';
        }

        categoryData.items.forEach((item, index) => {
            this.renderTableRow(item, index, tableBody);
            this.renderMobileCard(item, index, mobileView);
        });

        this.showTable();
    }

    renderTableRow(item, index, tableBody) {
        if (!tableBody) return;

            const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 transition-colors animate-fadeIn';
            row.style.animationDelay = `${index * 50}ms`;

            let rowHTML = `
            <td class="px-4 py-3 text-sm font-medium text-gray-900">${this.escapeHtml(item.asset_name || '-')}</td>
            <td class="px-4 py-3 text-sm text-gray-600">${this.escapeHtml(item.asset_code || '-')}</td>
            <td class="px-4 py-3 text-sm text-gray-600">${this.escapeHtml(item.brand || '-')}</td>
            <td class="px-4 py-3 text-sm text-gray-600">${this.escapeHtml(item.location || '-')}</td>
            <td class="px-4 py-3 text-sm font-medium text-blue-700">${this.escapeHtml(item.schedule || '-')}</td>
        `;

        if (this.state.currentTab === 'maintenance') {
            rowHTML += `<td class="px-4 py-3 text-sm text-gray-600">${this.escapeHtml(item.assigned_to || '-')}</td>`;
            }

            row.innerHTML = rowHTML;
            tableBody.appendChild(row);
    }

    renderMobileCard(item, index, mobileView) {
        if (!mobileView) return;

            const card = document.createElement('div');
        card.className = 'reminder-card bg-white p-4 animate-fadeIn';
            card.style.animationDelay = `${index * 50}ms`;

        const badgeColor = this.getBadgeColorByCategory(this.state.currentCategory);

            let cardContent = `
                <div class="flex items-start justify-between mb-3">
                <div class="asset-name">${this.escapeHtml(item.asset_name || '-')}</div>
                    <div class="schedule-date" style="background-color: ${badgeColor.bg}; color: ${badgeColor.text}">
                    ${this.escapeHtml(item.schedule || '-')}
                    </div>
                </div>
                <div class="info-content">
                    <div class="info-row">
                        <div class="info-label">Kode Aset:</div>
                    <div class="info-value">${this.escapeHtml(item.asset_code || '-')}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Merek:</div>
                    <div class="info-value">${this.escapeHtml(item.brand || '-')}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Lokasi:</div>
                    <div class="info-value">${this.escapeHtml(item.location || '-')}</div>
                    </div>
            `;

        if (this.state.currentTab === 'maintenance') {
                cardContent += `
                    <div class="info-row">
                        <div class="info-label">Penanggung Jawab:</div>
                    <div class="info-value">${this.escapeHtml(item.assigned_to || '-')}</div>
                    </div>
                `;
        }

        cardContent += '</div>';
            card.innerHTML = cardContent;
            mobileView.appendChild(card);
    }

    getBadgeColorByCategory(category) {
        const colors = {
            today: { bg: '#fee2e2', text: '#b91c1c' },
            one_to_fourteen_days: { bg: '#ffedd5', text: '#c2410c' },
            fifteen_to_thirty_days: { bg: '#fef9c3', text: '#854d0e' },
            one_to_two_months: { bg: '#dbeafe', text: '#1e40af' },
            two_to_three_months: { bg: '#e0e7ff', text: '#3730a3' },
            more_than_3_months: { bg: '#f3e8ff', text: '#6b21a8' },
            missed: { bg: '#f3f4f6', text: '#4b5563' }
        };
        return colors[category] || colors.today;
    }

    showLoading() {
        this.state.isLoading = true;
        this.elements.loading?.classList.remove('hidden');
        this.elements.loading?.classList.add('animate-fadeIn');
        this.hideAllViews();
        this.elements.empty?.classList.add('hidden');
        this.elements.error?.classList.add('hidden');
        this.hideLoadMoreButtons();
    }

    hideLoading() {
        this.state.isLoading = false;
        this.elements.loading?.classList.add('opacity-0');
        setTimeout(() => {
            this.elements.loading?.classList.add('hidden');
            this.elements.loading?.classList.remove('opacity-0');
        }, 150);
    }

    showLoadMoreLoading() {
        const loadMoreBtn = this.state.currentTab === 'calibration'
            ? this.elements.calibrationLoadMore?.querySelector('button')
            : this.elements.maintenanceLoadMore?.querySelector('button');

        if (loadMoreBtn) {
            loadMoreBtn.disabled = true;
            loadMoreBtn.innerHTML = '<div class="loading-spinner"></div><span class="ml-2">Memuat...</span>';
        }
    }

    hideLoadMoreLoading() {
        const loadMoreBtn = this.state.currentTab === 'calibration'
            ? this.elements.calibrationLoadMore?.querySelector('button')
            : this.elements.maintenanceLoadMore?.querySelector('button');

        if (loadMoreBtn) {
            loadMoreBtn.disabled = false;
            loadMoreBtn.innerHTML = 'Muat Lebih Banyak';
        }
    }

    showError() {
        this.hideLoading();
        this.elements.error?.classList.remove('hidden');
        this.elements.error?.classList.add('animate-fadeIn');
        this.hideAllViews();
        this.elements.empty?.classList.add('hidden');
    }

    showEmpty() {
        this.hideLoading();
        this.elements.empty?.classList.remove('hidden');
        this.elements.empty?.classList.add('animate-fadeIn');
        this.hideAllViews();
        this.elements.error?.classList.add('hidden');
    }

    showTable() {
        this.hideLoading();
        this.elements.empty?.classList.add('hidden');
        this.elements.error?.classList.add('hidden');
        this.updateViewBasedOnDevice();
        this.updateLoadMoreButtons();
    }

    hideAllViews() {
        [
            this.elements.calibrationTable,
            this.elements.maintenanceTable,
            this.elements.calibrationMobileView,
            this.elements.maintenanceMobileView
        ].forEach(view => view?.classList.add('hidden'));
    }

    showCurrentView() {
        const isMobile = window.innerWidth < 640;

        if (this.state.currentTab === 'calibration') {
            const element = isMobile ? this.elements.calibrationMobileView : this.elements.calibrationTable;
            element?.classList.remove('hidden');
            element?.classList.add('animate-fadeIn');
        } else {
            const element = isMobile ? this.elements.maintenanceMobileView : this.elements.maintenanceTable;
            element?.classList.remove('hidden');
            element?.classList.add('animate-fadeIn');
        }
    }

    updateViewBasedOnDevice() {
        this.hideAllViews();
        setTimeout(() => this.showCurrentView(), 50);
    }

    updateLoadMoreButtons() {
        const hasMore = this.pagination.hasMorePages[this.state.currentTab]?.[this.state.currentCategory];

        if (this.state.currentTab === 'calibration') {
            if (hasMore) {
                this.elements.calibrationLoadMore?.classList.remove('hidden');
                this.elements.calibrationLoadMore?.classList.add('animate-fadeIn');
                } else {
                this.elements.calibrationLoadMore?.classList.add('hidden');
            }
                } else {
            if (hasMore) {
                this.elements.maintenanceLoadMore?.classList.remove('hidden');
                this.elements.maintenanceLoadMore?.classList.add('animate-fadeIn');
                        } else {
                this.elements.maintenanceLoadMore?.classList.add('hidden');
            }
        }
    }

    hideLoadMoreButtons() {
        this.elements.calibrationLoadMore?.classList.add('hidden');
        this.elements.maintenanceLoadMore?.classList.add('hidden');
    }

    handleScroll() {
        if (this.state.isLoading || !this.elements.content) return;

        const { scrollHeight, scrollTop, clientHeight } = this.elements.content;

        if (scrollTop + clientHeight >= scrollHeight - this.config.scrollThreshold) {
            const hasMore = this.pagination.hasMorePages[this.state.currentTab]?.[this.state.currentCategory];
            if (hasMore) this.loadData(true);
        }
    }

    resetDataAndPagination() {
        this.state.isLoaded = false;
        this.cache.calibration = null;
        this.cache.maintenance = null;
        this.pagination.currentPage = { calibration: {}, maintenance: {} };
        this.pagination.hasMorePages = { calibration: {}, maintenance: {} };
    }

    async fetchWithTimeout(url, options = {}) {
        const timeoutPromise = new Promise((_, reject) => {
            setTimeout(() => reject(new Error('Request timeout')), this.config.requestTimeout);
        });

        return Promise.race([fetch(url, options), timeoutPromise]);
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, (m) => map[m]);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.remindersPopup = new RemindersPopup();
});
