<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-[#213268]">Depreciation</h2>
        <button class="flex items-center justify-center gap-2 px-4 py-2 bg-[#213268] rounded-lg text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-sm">Manage</span>
        </button>
    </div>

    <!-- Loading indicator -->
    <div id="loadingIndicator" class="hidden">
        <div class="flex justify-center items-center py-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#213268]"></div>
        </div>
    </div>

    <!-- Error message container -->
    <div id="errorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
    </div>

    <!-- Content sections -->
    <div id="contentSections" class="hidden">
    <!-- Top Asset Depreciation Table -->
    <div class="overflow-x-auto mb-8">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Date Acquired</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Total Cost</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Salvage Value</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Life (Months)</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Depreciation Method</th>
                </tr>
            </thead>
                <tbody id="depreciationSummary">
                <tr>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Depreciation Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
        <h3 class="text-center text-lg font-semibold text-[#213268] mb-6">Depreciation Monthly Status</h3>
        <div class="h-64 w-full">
            <canvas id="depreciationChart"></canvas>
        </div>
    </div>

    <!-- Bottom Depreciation Details Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">#</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Month</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Depreciation Expense</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Accumulated Depreciation at Month-end</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Book Value at Month-end</th>
                </tr>
            </thead>
                <tbody id="monthlyDepreciationData">
                <tr>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const DepreciationSystem = {
        initialized: false,
        assetId: {{ $asset['asset_id'] ?? 'null' }},
        apiBaseUrl: "{{ config('app.api_url', '') }}",
        chart: null,

        init() {
            if (this.initialized) return;
            console.log('Initializing Depreciation System with Asset ID:', this.assetId);

            if (!this.assetId) {
                console.error('Asset ID is not available');
                this.showError('Asset ID tidak tersedia');
                return;
            }

            this.loadDepreciationData();
            this.initialized = true;
        },

        showLoading() {
            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.getElementById('contentSections').classList.add('hidden');
            document.getElementById('errorMessage').classList.add('hidden');
        },

        hideLoading() {
            document.getElementById('loadingIndicator').classList.add('hidden');
            document.getElementById('contentSections').classList.remove('hidden');
        },

        showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            document.getElementById('contentSections').classList.add('hidden');
            document.getElementById('loadingIndicator').classList.add('hidden');
        },

        formatCurrency(value) {
            if (value === null || value === undefined) return '-';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        },

        loadDepreciationData() {
            if (!this.assetId) {
                this.showError('Asset ID tidak tersedia');
                return;
            }

            this.showLoading();
            console.log('Fetching depreciation data for asset ID:', this.assetId);

            fetch(`/asset-depreciation/${this.assetId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received depreciation data:', data);

                if (!data.status || !data.data || !data.data.depreciation) {
                    throw new Error(data.message || 'Invalid data structure received');
                }

                const depreciation = data.data.depreciation;
                this.updateDepreciationData(depreciation);
                this.hideLoading();
            })
            .catch(error => {
                console.error('Error loading depreciation data:', error);
                this.showError('Gagal memuat data depresiasi: ' + error.message);
            });
        },

        updateDepreciationData(depreciation) {
            // Update summary table
            document.getElementById('depreciationSummary').innerHTML = `
                <tr>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${depreciation.date_acquired || '-'}</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.formatCurrency(depreciation.total_cost)}</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.formatCurrency(depreciation.salvage_value)}</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${depreciation.asset_life_months || '-'}</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">${depreciation.depreciation_method || '-'}</td>
                </tr>
            `;

            // Update monthly data table
            if (Array.isArray(depreciation.monthly_data)) {
                const monthlyRows = depreciation.monthly_data.map(month => `
                    <tr>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${month.month_number}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${month.month_name}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.formatCurrency(month.expense)}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.formatCurrency(month.accumulated_depreciation)}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${this.formatCurrency(month.book_value)}</td>
                    </tr>
                `).join('');
                document.getElementById('monthlyDepreciationData').innerHTML = monthlyRows;
            }

            // Update chart
            if (depreciation.chart_data) {
                this.updateChart(depreciation.chart_data);
            }
        },

        updateChart(chartData) {
        const ctx = document.getElementById('depreciationChart').getContext('2d');

            // Destroy existing chart if it exists
            if (this.chart) {
                this.chart.destroy();
            }

            if (!chartData || !chartData.years || !chartData.values) {
                console.error('Invalid chart data:', chartData);
                return;
            }

            this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                    labels: chartData.years,
                datasets: [{
                        data: chartData.values,
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    pointBackgroundColor: '#36A2EB',
                    pointRadius: 4,
                    borderWidth: 2,
                    tension: 0.1,
                        fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                                label: (context) => {
                                    return this.formatCurrency(context.parsed.y);
                                }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value === 0) return '0';
                                return (value / 1000000).toFixed(1) + 'M';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
        }
    };

    // Initialize the Depreciation System
    DepreciationSystem.init();
    });
</script>
@endpush
