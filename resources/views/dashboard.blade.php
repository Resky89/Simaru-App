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
            <div class="card-body">
                <h2 class="card-title text-2xl text-[#232D42]">Asset By Status</h2>
                <div class="divider"></div>
                <!-- Add your pie chart here -->
                <div class="h-64">
                    <!-- Chart placeholder -->
                </div>
            </div>
        </div>

        <!-- Asset By Categories -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl text-[#232D42]">Asset By Categories</h2>
                <div class="divider"></div>
                <!-- Add your category charts here -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Category items -->
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
@endsection
