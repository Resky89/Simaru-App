@extends('Layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="h-full">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <!-- Total Asset Card -->
        <div class="stats bg-gradient-to-r from-[rgba(80,130,7,0.1)] to-[rgba(80,130,7,0.2)] rounded-lg border-none">
            <div class="stat flex flex-row items-center gap-3 p-4">
                <div class="stat-figure text-[#659B09] opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <path d="M3 9h18" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <div class="stat-value text-[28px] font-medium text-[#232D42]">134.4k</div>
                    <div class="stat-title text-[14px] text-[#659B09] m-0 opacity-80">Asset</div>
                </div>
            </div>
        </div>

        <!-- Under Repair Card -->
        <div class="stats bg-gradient-to-r from-[rgba(218,174,15,0.1)] to-[rgba(218,174,15,0.2)] rounded-lg border-none">
            <div class="stat flex flex-row items-center gap-3 p-4">
                <div class="stat-figure text-[#DAAE0F] opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <div class="stat-value text-[28px] font-medium text-[#232D42]">50</div>
                    <div class="stat-title text-[14px] text-[#DAAE0F] m-0 opacity-80">Under Repair</div>
                </div>
            </div>
        </div>

        <!-- Net Asset Value Card -->
        <div class="stats bg-gradient-to-r from-[rgba(255,74,43,0.1)] to-[rgba(255,74,43,0.2)] rounded-lg border-none">
            <div class="stat flex flex-row items-center gap-3 p-4">
                <div class="stat-figure text-[#F16A1B] opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                        <path d="M12 6v2m0 8v2"/>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <div class="stat-value text-[28px] font-medium text-[#232D42]">500</div>
                    <div class="stat-title text-[14px] text-[#F16A1B] m-0 opacity-80">Net Asset Value</div>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="stats bg-gradient-to-r from-[rgba(37,177,255,0.1)] to-[rgba(37,177,255,0.2)] rounded-lg border-none">
            <div class="stat flex flex-row items-center gap-3 p-4">
                <div class="stat-figure text-[#1B8ADB] opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <div class="stat-value text-[28px] font-medium text-[#232D42]">500</div>
                    <div class="stat-title text-[14px] text-[#1B8ADB] m-0 opacity-80">Users</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mt-4 md:mt-6">
        <!-- Asset By Status -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4">
                <!-- Title -->
                <h2 class="text-2xl font-medium text-[#232D42] font-['Inter'] mb-3">Asset By Status</h2>

                <!-- Divider -->
                <div class="w-full border-t-2 border-[#ECECEC] mb-4"></div>

                <!-- Chart Container -->
                <div class="flex flex-col md:flex-row">
                    <!-- Pie Chart -->
                    <div class="w-full md:w-2/3">
                        <canvas id="assetStatusChart" class="max-h-[250px]"></canvas>
                    </div>

                    <!-- Legend -->
                    <div class="w-full md:w-1/3 pt-2 md:pt-6 space-y-2">
                        <!-- Available -->
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 bg-[#7CB60C]"></div>
                            <span class="text-sm text-[#4F4F4F]">Available</span>
                        </div>

                        <!-- Maintenance -->
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 bg-[#ACC3EF]"></div>
                            <span class="text-sm text-[#4F4F4F]">Maintenance</span>
                        </div>

                        <!-- Check Out -->
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 bg-[#FFD016]"></div>
                            <span class="text-sm text-[#4F4F4F]">Check Out</span>
                        </div>

                        <!-- Dispose -->
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 bg-[#25B1FF]"></div>
                            <span class="text-sm text-[#4F4F4F]">Dispose</span>
                        </div>

                        <!-- Lost -->
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 bg-[#FF4A2B]"></div>
                            <span class="text-sm text-[#4F4F4F]">Lost</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asset By Categories -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4">
                <!-- Title and Toggle -->
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-2xl font-medium text-[#232D42] font-['Inter']">Asset By Categories</h2>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="displayToggle" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#213268]"></div>
                    </label>
                </div>

                <!-- Divider -->
                <div class="w-full border-t-2 border-[#ECECEC] mb-4"></div>

                <!-- Categories Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Category Item 1 -->
                    <div class="flex items-center gap-4">
                        <div class="relative w-16 h-16">
                            <div class="w-full h-full rounded-full border-[6px] border-[rgba(117,117,117,0.31)]">
                                <div class="absolute inset-0 rounded-full border-[6px] border-[#213268] border-l-transparent border-t-transparent"></div>
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="number-value font-['Inter'] font-medium text-xl text-[#232D42]">400</span>
                                <span class="percent-value hidden font-['Inter'] font-medium text-xl text-[#232D42]">78%</span>
                            </div>
                        </div>
                        <div class="font-['Inter'] font-medium text-lg text-[#232D42]">Diagnostic Equipment</div>
                    </div>

                    <!-- Category Item 2 -->
                    <div class="flex items-center gap-4">
                        <div class="relative w-16 h-16">
                            <div class="w-full h-full rounded-full border-[6px] border-[rgba(117,117,117,0.31)]">
                                <div class="absolute inset-0 rounded-full border-[6px] border-[#213268] border-l-transparent border-t-transparent"></div>
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="number-value font-['Inter'] font-medium text-xl text-[#232D42]">50</span>
                                <span class="percent-value hidden font-['Inter'] font-medium text-xl text-[#232D42]">10%</span>
                            </div>
                        </div>
                        <div class="font-['Inter'] font-medium text-lg text-[#232D42]">Diagnostic Equipment</div>
                    </div>

                    <!-- Category Item 3 -->
                    <div class="flex items-center gap-4">
                        <div class="relative w-16 h-16">
                            <div class="w-full h-full rounded-full border-[6px] border-[rgba(117,117,117,0.31)]">
                                <div class="absolute inset-0 rounded-full border-[6px] border-[#213268] border-l-transparent border-t-transparent"></div>
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="number-value font-['Inter'] font-medium text-xl text-[#232D42]">50</span>
                                <span class="percent-value hidden font-['Inter'] font-medium text-xl text-[#232D42]">10%</span>
                            </div>
                        </div>
                        <div class="font-['Inter'] font-medium text-lg text-[#232D42]">Diagnostic Equipment</div>
                    </div>

                    <!-- Category Item 4 -->
                    <div class="flex items-center gap-4">
                        <div class="relative w-16 h-16">
                            <div class="w-full h-full rounded-full border-[6px] border-[rgba(117,117,117,0.31)]">
                                <div class="absolute inset-0 rounded-full border-[6px] border-[#213268] border-l-transparent border-t-transparent"></div>
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="number-value font-['Inter'] font-medium text-xl text-[#232D42]">10</span>
                                <span class="percent-value hidden font-['Inter'] font-medium text-xl text-[#232D42]">2%</span>
                            </div>
                        </div>
                        <div class="font-['Inter'] font-medium text-lg text-[#232D42]">Diagnostic Equipment</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mt-4 md:mt-6">
        <!-- Upcoming Assets Calibration -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl text-[#232D42]">Upcoming Assets Calibration</h2>
                <div class="divider"></div>
                <div class="space-y-4">
                    <!-- Calibration items -->
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-medium text-lg">Infusion Pump XYZ</h3>
                            <p class="text-gray-500">ICU Room 1</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[#213268]">19 March</p>
                            <p class="text-gray-500">Wed</p>
                        </div>
                        <button class="btn btn-outline btn-sm">View</button>
                    </div>
                    <!-- Add more items as needed -->
                </div>
            </div>
        </div>

        <!-- Asset By Location -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl text-[#232D42]">Asset By Location</h2>
                <div class="divider"></div>
                <div class="space-y-4">
                    <!-- Location progress bars -->
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>ER Room</span>
                            <span>85%</span>
                        </div>
                        <progress class="progress progress-primary w-full" value="85" max="100"></progress>
                    </div>
                    <!-- Add more locations as needed -->
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card bg-base-100 shadow-xl mt-4 md:mt-6">
        <div class="card-body">
            <h2 class="card-title text-2xl text-[#213268]">Recent Activity</h2>
            <div class="divider"></div>
            <!-- Activity items -->
            <div class="space-y-4">
                <div class="flex flex-col md:flex-row gap-2 md:gap-4 items-start border-l-4 border-[#25B1FF] pl-4">
                    <div class="min-w-[120px]">
                        <p class="font-medium text-lg">04 Mar 25</p>
                        <p class="text-gray-500">16:00</p>
                    </div>
                    <div class="hidden md:block divider divider-horizontal"></div>
                    <div>
                        <p class="text-gray-500">Asset ID: 123456</p>
                        <p>You change status from Checked Out to Available</p>
                    </div>
                </div>
                <!-- Add more activity items as needed -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('assetStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Available', 'Maintenance', 'Check Out', 'Dispose', 'Lost'],
            datasets: [{
                data: [45, 20, 15, 12, 8], // Sesuaikan dengan data Anda
                backgroundColor: [
                    '#7CB60C',  // Available - Green
                    '#ACC3EF',  // Maintenance - Grey
                    '#DAAE0F',  // Check Out - yellow
                    '#25B1FF',  // Dispose - Blue
                    '#FF4A2B'   // Lost - Red
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            layout: {
                padding: {
                    left: 20,
                    right: 20
                }
            }
        }
    });

    const displayToggle = document.getElementById('displayToggle');
    const numberValues = document.querySelectorAll('.number-value');
    const percentValues = document.querySelectorAll('.percent-value');

    displayToggle.addEventListener('change', function() {
        if (this.checked) {
            // Show percentage values
            numberValues.forEach(el => el.classList.add('hidden'));
            percentValues.forEach(el => el.classList.remove('hidden'));
        } else {
            // Show number values
            numberValues.forEach(el => el.classList.remove('hidden'));
            percentValues.forEach(el => el.classList.add('hidden'));
        }
    });
});
</script>
@endsection
