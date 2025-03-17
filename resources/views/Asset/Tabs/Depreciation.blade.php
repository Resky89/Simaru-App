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
            <tbody>
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
            <tbody>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the chart canvas
        const ctx = document.getElementById('depreciationChart').getContext('2d');

        // Chart data
        const years = ['COST', '2025', '2026', '2027', '2028', '2029', '2030'];
        const values = [4500000, 4200000, 3800000, 3600000, 3200000, 2700000, 2000000];

        // Create the chart
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: years,
                datasets: [{
                    data: values,
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    pointBackgroundColor: '#36A2EB',
                    pointRadius: 4,
                    borderWidth: 2,
                    tension: 0.1,
                    fill: false
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
                            label: function(context) {
                                return context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5000000,
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
    });
</script>
@endpush
