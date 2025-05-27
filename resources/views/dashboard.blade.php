@extends('Layout.app')

@section('title', 'Dashboard')

@section('content')
    @include('Layout.loading')
    <div class="h-full">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 md:gap-6">
            <!-- Total Asset Card -->
            <div class="stats bg-gradient-to-r from-[rgba(80,130,7,0.1)] to-[rgba(80,130,7,0.2)] rounded-lg border-none">
                <div class="stat flex flex-row items-center gap-3 p-4">
                    <div class="stat-figure text-[#659B09] opacity-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <path d="M3 9h18" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="stat-value text-[28px] font-medium text-[#213268]">
                            {{ formatCompactNumber($dashboardData['total_assets'] ?? 0) }}
                        </div>
                        <div class="stat-title text-[14px] text-[#659B09] m-0 opacity-80">Aset</div>
                    </div>
                </div>
            </div>

            <!-- Under Repair Card -->
            <div
                class="stats bg-gradient-to-r from-[rgba(218,174,15,0.1)] to-[rgba(218,174,15,0.2)] rounded-lg border-none">
                <div class="stat flex flex-row items-center gap-3 p-4">
                    <div class="stat-figure text-[#DAAE0F] opacity-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path
                                d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="stat-value text-[28px] font-medium text-[#213268]">
                            {{ formatCompactNumber($dashboardData['assets_by_status']['under repair'] ?? 0) }}
                        </div>
                        <div class="stat-title text-[14px] text-[#DAAE0F] m-0 opacity-80">Dalam Perbaikan </div>
                    </div>
                </div>
            </div>

            <!-- Net Asset Value Card -->
            <div class="stats bg-gradient-to-r from-[rgba(255,74,43,0.1)] to-[rgba(255,74,43,0.2)] rounded-lg border-none">
                <div class="stat flex flex-row items-center gap-3 p-4">
                    <div class="stat-figure text-[#F16A1B] opacity-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" />
                            <path d="M12 6v2m0 8v2" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="stat-value text-[28px] font-medium text-[#213268]">
                            {{ formatCompactCurrency($dashboardData['total_book_value'] ?? 0) }}
                        </div>
                        <div class="stat-title text-[14px] text-[#F16A1B] m-0 opacity-80">Nilai Buku</div>
                    </div>
                </div>
            </div>

            <!-- Acquisition Cost Card -->
            <div
                class="stats bg-gradient-to-r from-[rgba(111,67,205,0.1)] to-[rgba(111,67,205,0.2)] rounded-lg border-none">
                <div class="stat flex flex-row items-center gap-3 p-4">
                    <div class="stat-figure text-[#6F43CD] opacity-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="stat-value text-[28px] font-medium text-[#213268]">
                            {{ formatCompactCurrency($dashboardData['total_acquisition_cost'] ?? 0) }}
                        </div>
                        <div class="stat-title text-[14px] text-[#6F43CD] m-0 opacity-80">Biaya Pengadaan</div>
                    </div>
                </div>
            </div>

            <!-- Users Card -->
            <div
                class="stats bg-gradient-to-r from-[rgba(37,177,255,0.1)] to-[rgba(37,177,255,0.2)] rounded-lg border-none">
                <div class="stat flex flex-row items-center gap-3 p-4">
                    <div class="stat-figure text-[#1B8ADB] opacity-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="stat-value text-[28px] font-medium text-[#213268]">
                            {{ formatCompactNumber($dashboardData['total_users'] ?? 0) }}
                        </div>
                        <div class="stat-title text-[14px] text-[#1B8ADB] m-0 opacity-80">Pengguna</div>
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
                    <h2 class="text-2xl font-medium text-[#213268] font-['Poppins'] mb-3">Aset Berdasarkan Status</h2>

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
                                <span class="text-sm text-[#4F4F4F]">Tersedia</span>
                            </div>

                            <!-- Maintenance -->
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 bg-[#25B1FF]"></div>
                                <span class="text-sm text-[#4F4F4F]">Perawatan</span>
                            </div>

                            <!-- Check Out -->
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 bg-[#FFD016]"></div>
                                <span class="text-sm text-[#4F4F4F]">Pinjam</span>
                            </div>

                            <!-- Dispose -->
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 bg-[#ACC3EF]"></div>
                                <span class="text-sm text-[#4F4F4F]">Dihapuskan</span>
                            </div>

                            <!-- Lost -->
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 bg-[#FF4A2B]"></div>
                                <span class="text-sm text-[#4F4F4F]">Hilang</span>
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
                        <h2 class="text-2xl font-medium text-[#213268] font-['Poppins']">Aset Berdasarkan Kategori</h2>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="displayToggle" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#213268]">
                            </div>
                        </label>
                    </div>

                    <!-- Divider -->
                    <div class="w-full border-t-2 border-[#ECECEC] mb-4"></div>

                    <!-- Categories Grid with scroll -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[240px] overflow-y-auto pr-2 custom-scrollbar"
                        style="scrollbar-width: thin; scrollbar-color: #213268 #f0f0f0;">
                        @forelse($dashboardData['assets_by_subcategory'] as $category)
                            @php
                                $totalBySubcategory = array_sum(array_column($dashboardData['assets_by_subcategory'], 'count'));
                                $percentage = $totalBySubcategory ? round(($category['count'] / $totalBySubcategory) * 100) : 0;
                                $radius = 15.9;
                                $circumference = 2 * 3.14159 * $radius;
                            @endphp
                            <div class="flex items-center gap-4 animate-fade-in">
                                <div class="relative min-w-[64px] w-16 h-16 flex-shrink-0">
                                    <!-- Progress Circle -->
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                        <!-- Background Circle -->
                                        <circle cx="18" cy="18" r="{{ $radius }}" fill="none" stroke="rgba(117,117,117,0.31)"
                                            stroke-width="2.5" />

                                        <!-- Progress Circle -->
                                        @if($percentage > 0)
                                            <circle cx="18" cy="18" r="{{ $radius }}" fill="none" stroke="#213268"
                                                stroke-width="2.5" stroke-dasharray="{{ $circumference }}"
                                                stroke-dashoffset="{{ $circumference - ($percentage / 100 * $circumference) }}"
                                                class="progress-circle" data-percentage="{{ $percentage }}" />
                                        @endif
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span
                                            class="number-value font-['Poppins'] font-medium text-xl text-[#213268] animate-count-up"
                                            data-target="{{ $category['count'] }}">0</span>
                                        <span
                                            class="percent-value hidden font-['Poppins'] font-medium text-xl text-[#213268] animate-count-up"
                                            data-target="{{ $percentage }}">0%</span>
                                    </div>
                                </div>
                                <div class="font-['Poppins'] font-medium text-lg text-[#213268] truncate">
                                    {{ $category['subcategory_name'] }}
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-4 text-gray-500">
                                Tidak ada data kategori tersedia
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mt-4 md:mt-6">
            <!-- Upcoming Assets Calibration -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body p-4">
                    <!-- Title -->
                    <h2 class="text-2xl font-medium text-[#213268] font-['Poppins'] mb-3">Aset Yang Akan Dikalibrasi</h2>

                    <!-- Divider -->
                    <div class="w-full border-t-2 border-[#ECECEC] mb-4"></div>

                    <!-- Calibration Items Container -->
                    <div class="space-y-4 max-h-[336px] overflow-y-auto pr-2 custom-scrollbar"
                        style="scrollbar-width: thin; scrollbar-color: #213268 #f0f0f0;">
                        @forelse($dashboardData['upcoming_calibrations']['all'] as $calibration)
                            <div class="flex justify-between items-center">
                                <div class="flex gap-4">
                                    <div class="flex flex-col w-[173px]">
                                        <h3 class="text-lg font-['Poppins'] font-medium text-[#213268]">
                                            {{ $calibration['asset_name'] ?? 'Aset Tidak Diketahui' }}
                                        </h3>
                                        <p class="text-[14px] font-['Poppins'] text-[#8A92A6]">
                                            {{ $calibration['room_name'] ?? 'Lokasi Tidak Diketahui' }}
                                        </p>
                                    </div>
                                    <div class="flex flex-col w-[82px]">
                                        @php
                                            $date = $calibration['planning_calibration_date'] ?? now()->format('Y-m-d');
                                            $dateObj = \Carbon\Carbon::parse($date);

                                            // Indonesian month names (abbreviated)
                                            $indonesianMonths = [
                                                'Jan',
                                                'Feb',
                                                'Mar',
                                                'Apr',
                                                'Mei',
                                                'Jun',
                                                'Jul',
                                                'Agt',
                                                'Sep',
                                                'Okt',
                                                'Nov',
                                                'Des'
                                            ];

                                            // Format the date with Indonesian month
                                            $formattedDate = $dateObj->format('d') . ' ' . $indonesianMonths[$dateObj->month - 1];

                                            $dayLabel = $dateObj->isToday() ? 'Hari Ini' : ($dateObj->isTomorrow() ? 'Besok' : ($dateObj->isCurrentWeek() ? $dateObj->format('D') : 'Minggu Depan'));
                                            // Determine if urgent based on date (within next 3 days)
                                            $isUrgent = $dateObj->diffInDays(now()) <= 3;
                                        @endphp
                                        <p class="text-[14px] font-['Poppins'] text-[#213268]">{{ $formattedDate }}</p>
                                        <p class="text-[14px] font-['Poppins'] text-[#8A92A6]">{{ $dayLabel }}</p>
                                    </div>
                                </div>
                                @if(hasPermission('calibration:report'))
                                    <button data-id="{{ $calibration['task_code'] }}"
                                        class="edit-calibration-btn flex items-center px-3 py-1.5 {{ $isUrgent ? 'bg-[#213268]' : 'border border-[#213268]' }} rounded">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="{{ $isUrgent ? 'white' : '#213268' }}" stroke-width="1.5">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-gray-300" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="mt-2 text-gray-500">Tidak ada data kalibrasi yang akan datang</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Asset By Location -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body p-4">
                    <!-- Title -->
                    <h2 class="text-2xl font-medium text-[#213268] font-['Poppins'] mb-3">Aset Berdasarkan Lokasi</h2>

                    <!-- Divider -->
                    <div class="w-full border-t-2 border-[#ECECEC] mb-4"></div>

                    <!-- Location List -->
                    <div class="space-y-[18px] max-h-[336px] overflow-y-auto pr-2 custom-scrollbar" id="location-container"
                        style="scrollbar-width: thin; scrollbar-color: #213268 #f0f0f0;">
                        @forelse($dashboardData['assets_by_location'] as $location)
                            @php
                                $totalByLocation = array_sum(array_column($dashboardData['assets_by_location'], 'count'));
                                $percentage = $totalByLocation ? round(($location['count'] / $totalByLocation) * 100) : 0;

                                // Handle 100% case correctly
                                $barWidth = $percentage;
                                // Ensure the tooltip appears at the right edge when percentage is 100
                                $tooltipPosition = $percentage == 100 ? "right-0" : "left-[calc({$percentage}%-20px)]";
                            @endphp
                            <div class="w-full animate-fade-in" style="animation-delay: {{ $loop->index * 150 }}ms">
                                <div class="flex justify-between items-center mb-2">
                                    <div class="text-lg font-['Poppins'] font-medium text-[#213268]">
                                        {{ $location['room_name'] }}
                                    </div>
                                    <div class="text-lg font-['Poppins'] font-medium text-[#213268] animate-count-up"
                                        data-target="{{ $percentage }}">0%</div>
                                </div>
                                <div class="relative" x-data="{ showTooltip: false }">
                                    <div class="w-full h-2 bg-[rgba(117,117,117,0.31)] rounded-[4px] cursor-pointer"
                                        @click="showTooltip = !showTooltip" @mouseenter="showTooltip = true"
                                        @mouseleave="showTooltip = false">
                                        <div class="absolute h-2 left-0 w-0 bg-[#213268] rounded-[4px] animate-loading-bar"
                                            data-width="{{ $barWidth }}"></div>
                                    </div>
                                    <!-- Tooltip -->
                                    <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                                        x-transition:enter-end="opacity-100 transform translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 transform translate-y-0"
                                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                                        class="absolute -top-8 {{ $tooltipPosition }}">
                                        <div class="bg-white shadow-lg rounded-lg px-3 py-2 text-center min-w-[40px]">
                                            <span
                                                class="text-[14px] font-['Poppins'] font-medium text-[#344054]">{{ $location['count'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-500">
                                Tidak ada data lokasi tersedia
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar -->
        <div class="flex flex-col items-start p-6 gap-8 w-full bg-white rounded-lg shadow-lg mb-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center w-full gap-3">
                <div class="flex flex-col md:flex-row w-full justify-between items-center gap-4">
                    <!-- Title and Navigation -->
                    <div class="flex items-center gap-3">
                        <button onclick="changeMonth(-1)"
                            class="w-10 h-10 flex items-center justify-center bg-white border border-[#213268]/20 rounded-lg hover:bg-[#213268]/5 text-[#213268] transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 18l-6-6 6-6" />
                            </svg>
                        </button>

                        <h2 id="currentMonth"
                            class="text-2xl font-['Poppins'] font-black text-[#213268] min-w-[120px] text-center"></h2>
                        <span id="currentYear" class="text-2xl font-['Poppins'] font-medium text-[#213268]/70"></span>

                        <button onclick="changeMonth(1)"
                            class="w-10 h-10 flex items-center justify-center bg-white border border-[#213268]/20 rounded-lg hover:bg-[#213268]/5 text-[#213268] transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 18l6-6-6-6" />
                            </svg>
                        </button>
                    </div>

                    <!-- Date Selector -->
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <select id="monthSelector" onchange="goToSelectedDate()"
                                class="appearance-none select pl-4 pr-10 py-2 w-full min-w-[150px] bg-white text-[#213268] border border-[#213268]/20 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#213268]/40">
                                <option value="0">Januari</option>
                                <option value="1">Februari</option>
                                <option value="2">Maret</option>
                                <option value="3">April</option>
                                <option value="4">Mei</option>
                                <option value="5">Juni</option>
                                <option value="6">Juli</option>
                                <option value="7">Agustus</option>
                                <option value="8">September</option>
                                <option value="9">Oktober</option>
                                <option value="10">November</option>
                                <option value="11">Desember</option>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-[#213268]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </div>
                        </div>

                        <div class="relative">
                            <select id="yearSelector" onchange="goToSelectedDate()"
                                class="appearance-none select pl-4 pr-10 py-2 w-full min-w-[100px] bg-white text-[#213268] border border-[#213268]/20 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#213268]/40">
                                <!-- Years will be added via JavaScript -->
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-[#213268]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="flex flex-col gap-2 w-full">
                <!-- Days Header -->
                <div class="flex justify-between items-center py-2 bg-white border-b border-[#213268]/10">
                    <div class="flex-1 text-center text-xs font-['Poppins'] font-medium text-[#213268]">Min</div>
                    <div class="flex-1 text-center text-xs font-['Poppins'] font-medium text-[#213268]">Sen</div>
                    <div class="flex-1 text-center text-xs font-['Poppins'] font-medium text-[#213268]">Sel</div>
                    <div class="flex-1 text-center text-xs font-['Poppins'] font-medium text-[#213268]">Rab</div>
                    <div class="flex-1 text-center text-xs font-['Poppins'] font-medium text-[#213268]">Kam</div>
                    <div class="flex-1 text-center text-xs font-['Poppins'] font-medium text-[#213268]">Jum</div>
                    <div class="flex-1 text-center text-xs font-['Poppins'] font-medium text-[#213268]">Sab</div>
                </div>

                <!-- Calendar Days -->
                <div id="calendarDays" class="grid grid-cols-7 gap-0.5">
                    <!-- Days will be inserted here by JavaScript -->
                    <div class="col-span-7 text-center py-8">
                        <span class="loading loading-spinner loading-md text-[#213268]"></span>
                        <p class="text-gray-500 mt-2">Memuat data kalender...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card bg-white shadow-xl p-4 md:p-7 lg:p-14 space-y-6">
            <!-- Title -->
            <h2 class="text-2xl font-medium font-['Poppins'] text-[#213268]">Aktivitas Terakhir</h2>

            <!-- Activity List -->
            <div class="space-y-3.5" id="asset-activities">
                <div class="text-center py-8">
                    <span class="loading loading-spinner loading-md text-[#213268]"></span>
                    <p class="text-gray-500 mt-2">Memuat aktivitas terakhir...</p>
                </div>
            </div>

            <!-- Load More Button -->
            <div class="flex justify-center mt-8" id="load-more-container" style="display: none;">
                <button id="load-more-activities"
                    class="relative flex items-center justify-center gap-2 px-6 py-3 bg-white border border-[#ECECEC] rounded-xl shadow-md hover:shadow-lg transition-all duration-300 group">
                    <div class="flex flex-col items-center justify-center">
                        <span class="font-medium text-[#213268]">Muat Lebih Banyak</span>
                        <span class="text-xs text-gray-500">Menampilkan aktivitas sebelumnya</span>
                    </div>
                    <div
                        class="absolute right-4 w-8 h-8 flex items-center justify-center rounded-full bg-[#213268] text-white transform group-hover:translate-y-1 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Perform Calibration Modal -->
    @if(hasPermission('calibration:report'))
        <div id="viewCalibrationModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[800px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="viewCalibrationModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">LAKUKAN KALIBRASI</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="viewCalibrationModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <form id="performCalibrationForm" class="space-y-6" data-no-loading enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="calibration_id" name="calibration_id">

                                <!-- Required fields note -->
                                <div class="text-sm text-gray-600 mb-4">
                                    Bidang dengan tanda <span class="text-red-500">*</span> wajib diisi
                                </div>

                                <!-- ASSET INFORMATION SECTION -->
                                <div class="bg-blue-100 rounded-lg p-4 mb-6">
                                    <h3 class="text-[#213268] font-semibold text-lg mb-4">Informasi Aset</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Left Column -->
                                        <div class="space-y-4">
                                            <!-- Asset Code -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">KODE ASET</label>
                                                <input type="text" id="asset_code_display"
                                                    class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                    readonly>
                                            </div>

                                            <!-- Asset Name -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">NAMA ASET</label>
                                                <input type="text" id="asset_name_display"
                                                    class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                    readonly>
                                            </div>

                                            <!-- Serial Number -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">NOMOR SERI</label>
                                                <input type="text" id="serial_number_display"
                                                    class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                    readonly>
                                            </div>
                                        </div>

                                        <!-- Right Column -->
                                        <div class="space-y-4">
                                            <!-- Brand (Merk) -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">MERK</label>
                                                <input type="text" id="brand_name_display"
                                                    class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                    readonly>
                                            </div>

                                            <!-- Type -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">TIPE</label>
                                                <input type="text" id="model_number_display"
                                                    class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                    readonly>
                                            </div>

                                            <!-- Location -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">LOKASI</label>
                                                <input type="text" id="location_display"
                                                    class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CALIBRATION SCHEDULE SECTION -->
                                <div class="bg-yellow-100 rounded-lg p-4 mb-6">
                                    <h3 class="text-[#213268] font-semibold text-lg mb-4">Jadwal Kalibrasi</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <!-- Planning Date -->
                                        <div>
                                            <label for="planning_calibration_date"
                                                class="block text-sm font-medium text-gray-700">
                                                TANGGAL RENCANA<span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" id="planning_date_display" name="planning_calibration_date"
                                                class="mt-1 block w-full py-2 px-3 bg-gray-100 border border-gray-300 rounded-md text-gray-600 focus:outline-none"
                                                readonly>
                                        </div>

                                        <!-- Work Date (Actual Calibration Date) -->
                                        <div>
                                            <label for="actual_calibration_date"
                                                class="block text-sm font-medium text-gray-700">
                                                TANGGAL KERJA<span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" id="actual_calibration_date" name="actual_calibration_date"
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"
                                                required>
                                        </div>

                                        <!-- Next Calibration Date -->
                                        <div>
                                            <label for="next_calibration_date" class="block text-sm font-medium text-gray-700">
                                                KALIBRASI BERIKUTNYA<span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" id="next_calibration_date" name="next_calibration_date"
                                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <!-- CALIBRATION DETAILS SECTION -->
                                <div class="bg-green-100 rounded-lg p-4 mb-6">
                                    <h3 class="text-[#213268] font-semibold text-lg mb-4">Detail Kalibrasi</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Left Column -->
                                        <div class="space-y-4">
                                            <!-- Certificate Number -->
                                            <div>
                                                <label for="certificate_number" class="block text-sm font-medium text-gray-700">
                                                    NOMOR SERTIFIKAT<span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" id="certificate_number" name="certificate_number"
                                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"
                                                    required>
                                            </div>

                                            <!-- Vendor -->
                                            <div>
                                                <label for="vendor_id" class="block text-sm font-medium text-gray-700">
                                                    VENDOR
                                                </label>
                                                <div class="relative">
                                                    <input type="text" id="vendor_search" placeholder="Cari vendor..."
                                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                                                    <input type="hidden" id="vendor_id" name="vendor_id">
                                                    <div id="vendor_results"
                                                        class="absolute z-10 w-full bg-white mt-1 rounded-md shadow-lg max-h-60 overflow-y-auto border border-gray-300">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right Column -->
                                        <div class="space-y-4">
                                            <!-- Service Price -->
                                            <div>
                                                <label for="calibration_price" class="block text-sm font-medium text-gray-700">
                                                    BIAYA LAYANAN
                                                </label>
                                                <div class="relative mt-1">
                                                    <div
                                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                                    </div>
                                                    <input type="number" id="calibration_price" name="calibration_price"
                                                        step="0.01"
                                                        class="block w-full pl-10 py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                                                </div>
                                            </div>

                                            <!-- Result -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">
                                                    HASIL<span class="text-red-500">*</span>
                                                </label>
                                                <div class="mt-2 flex flex-wrap gap-6">
                                                    <div class="flex items-center">
                                                        <input type="radio" id="result_pass" name="calibration_result"
                                                            value="pass" class="h-4 w-4 text-[#213268] focus:ring-[#213268]"
                                                            required>
                                                        <label for="result_pass"
                                                            class="ml-2 text-sm text-gray-700">Lulus</label>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <input type="radio" id="result_fail" name="calibration_result"
                                                            value="fail" class="h-4 w-4 text-[#213268] focus:ring-[#213268]">
                                                        <label for="result_fail"
                                                            class="ml-2 text-sm text-gray-700">Gagal</label>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <input type="radio" id="result_unknown" name="calibration_result"
                                                            value="unknown" class="h-4 w-4 text-[#213268] focus:ring-[#213268]">
                                                        <label for="result_unknown" class="ml-2 text-sm text-gray-700">Tidak
                                                            Ditemukan</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- DOCUMENTATION SECTION -->
                                <div class="bg-blue-100 rounded-lg p-4 mb-6">
                                    <h3 class="text-[#213268] font-semibold text-lg mb-4">Dokumentasi</h3>

                                    <!-- Document File -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">BERKAS TERUNGGAH</label>
                                        <div
                                            class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                            <!-- File preview container -->
                                            <div id="file-preview" class="mt-2 mb-4 w-full hidden">
                                                <div
                                                    class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                    <!-- Image preview -->
                                                    <img id="image-preview"
                                                        class="w-full h-auto max-h-64 object-contain mx-auto rounded hidden"
                                                        alt="Pratinjau file">

                                                    <!-- PDF/File preview -->
                                                    <div id="file-info" class="flex items-center">
                                                        <svg class="w-6 h-6 text-red-600 mr-2"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <span id="file-name-text" class="text-sm text-gray-700 truncate"></span>
                                                        <button type="button" id="remove-file"
                                                            class="ml-auto text-red-500 hover:text-red-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <svg class="mx-auto h-12 w-12 text-[#213268]" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                <p class="mt-1 text-sm text-gray-600">Seret berkas Anda atau <span
                                                        class="text-[#213268] font-semibold">jelajahi berkas</span></p>
                                                <p class="mt-1 text-xs text-gray-500">Format yang diterima: PDF, JPG, JPEG, PNG
                                                    (Maks: 10MB)</p>
                                                <p class="mt-1 text-xs text-[#213268] font-medium">Klik di mana saja di area ini
                                                    untuk memilih berkas</p>
                                            </div>
                                            <input type="file" id="document_file" name="file" accept=".pdf,.jpg,.jpeg,.png"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        </div>
                                    </div>

                                    <!-- Calibration Notes - Full Width -->
                                    <div class="mt-4">
                                        <label for="notes" class="block text-sm font-medium text-gray-700">CATATAN
                                            KALIBRASI</label>
                                        <textarea id="notes" name="notes" rows="3"
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#213268] focus:border-[#213268]"
                                            placeholder="Tambahkan catatan atau keterangan tambahan tentang kalibrasi ini..."></textarea>
                                    </div>
                                </div>

                                <!-- Status -->
                                <input type="hidden" id="status_calibration" name="status_calibration" value="completed">

                                <div class="pt-4">
                                    <button type="submit"
                                        class="w-full py-3 bg-[#213268] text-white rounded-lg hover:bg-[#152349] transition-colors duration-200 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Simpan Kalibrasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Add helper functions for the view -->
    @php
        function formatCompactNumber($number)
        {
            if ($number >= 1000000000) {
                return number_format($number / 1000000000, 1) . 'M';
            }
            if ($number >= 1000000) {
                return number_format($number / 1000000, 1) . 'Jt';
            }
            if ($number >= 1000) {
                return number_format($number / 1000, 1) . 'rb';
            }
            return $number;
        }

        function formatCompactCurrency($number)
        {
            if ($number >= 1000000000) {
                return 'Rp ' . number_format($number / 1000000000, 1) . 'M';
            }
            if ($number >= 1000000) {
                return 'Rp ' . number_format($number / 1000000, 1) . 'jt';
            }
            if ($number >= 1000) {
                return 'Rp ' . number_format($number / 1000, 1) . 'rb';
            }
            return 'Rp ' . $number;
        }

        // Helper function to convert degrees to radians if not already defined
        if (!function_exists('deg2rad')) {
            function deg2rad($degrees)
            {
                return $degrees * M_PI / 180;
            }
        }
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes rotateCircle {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(var(--rotation-angle, 180deg));
            }
        }

        @keyframes growWidth {
            0% {
                width: 0;
            }
        }

        @keyframes pulse {
            0% {
                opacity: 0.5;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.5;
            }
        }

        .animate-fade-in {
            opacity: 0;
            animation: fadeIn 0.5s ease-out forwards;
        }

        .animate-loading-circle {
            animation: rotateCircle 1s ease-out forwards;
        }

        .animate-loading-bar {
            animation: growWidth 1s ease-out forwards;
        }

        .animate-pulse {
            animation: pulse 1.5s ease-in-out infinite;
        }

        /* Custom scrollbar styling */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #213268;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #152349;
        }

        /* Vendor dropdown styling */
        #vendor_results {
            display: none;
            z-index: 999;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #vendor_results .vendor-item {
            transition: background-color 0.2s;
            cursor: pointer;
        }

        #vendor_results .vendor-item:hover {
            background-color: rgba(33, 50, 104, 0.1);
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        /* SVG Circle Progress Animation */
        @keyframes fillProgress {
            0% {
                stroke-dashoffset: 100;
            }
        }

        .progress-circle {
            animation: fillProgress 1.5s ease-out forwards;
        }
    </style>

    <script>
        // Initial calendar setup
        let currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();

        const months = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];

        // Store events in global scope
        let calendarEvents = {};

        // Asset Activities Pagination
        let currentActivitiesPage = 1;
        let activitiesPerPage = 10;
        let hasMoreActivities = false;
        let isLoadingActivities = false;

        // Helper function to prevent multiple submissions
        function preventMultipleSubmits(form, buttonSelector) {
            if (!form) return;

            form.addEventListener('submit', function (e) {
                // Only proceed if validation passes
                if (this.checkValidity()) {
                    // Find the submit button
                    const submitBtn = this.querySelector(buttonSelector);
                    if (submitBtn && !submitBtn.disabled) {
                        // Save original button text
                        const originalText = submitBtn.innerHTML;

                        // Disable the button and show loading state
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `
                            <div class="flex items-center justify-center">
                                <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                <span>Memproses...</span>
                            </div>
                        `;

                        // Re-enable button after 10 seconds as a failsafe
                        setTimeout(() => {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            }
                        }, 10000);
                    }
                }
            });
        }

        // Toast notification function
        window.showToast = function (message, type = 'success') {
            // Create the notification element
            const notification = document.createElement('div');
            notification.id = type + 'Notification' + Date.now(); // Unique ID to allow multiple notifications
            notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
            notification.setAttribute('role', 'alert');

            // Helper function to process error object to HTML
            function processErrorObject(errorObj) {
                // Special format seen in screenshot: {success: false, errors: [{path, message}]}
                if (errorObj.success === false && Array.isArray(errorObj.errors) && errorObj.errors.length > 0) {
                    // Check if it's the path/message format
                    const firstError = errorObj.errors[0];
                    if (typeof firstError === 'object' && firstError !== null &&
                        firstError.path && firstError.message) {

                        console.log('✓ Found specific error format with path/message properties');
                        let errorList = '<ul>';
                        errorObj.errors.forEach(err => {
                            errorList += `<li><strong>${err.path}</strong>: ${err.message}</li>`;
                        });
                        errorList += '</ul>';
                        return errorList;
                    }

                    // Regular array errors
                    console.log('Found error array format:', errorObj.errors);
                    return processErrorArray(errorObj.errors);
                }

                // Laravel validation errors format
                if (errorObj.errors && typeof errorObj.errors === 'object') {
                    console.log('Found Laravel validation errors format');
                    let errorList = '<ul>';
                    Object.entries(errorObj.errors).forEach(([field, errors]) => {
                        if (Array.isArray(errors)) {
                            errors.forEach(error => {
                                errorList += `<li><strong>${field}</strong>: ${error}</li>`;
                            });
                        } else if (typeof errors === 'string') {
                            errorList += `<li><strong>${field}</strong>: ${errors}</li>`;
                        }
                    });
                    errorList += '</ul>';
                    return errorList;
                }

                // Single error message
                if (errorObj.message) {
                    return errorObj.message;
                }

                if (errorObj.error) {
                    return errorObj.error;
                }

                // Fallback: stringify the object
                try {
                    return JSON.stringify(errorObj);
                } catch (e) {
                    return "Error tidak dapat ditampilkan";
                }
            }

            // Helper function to process error arrays
            function processErrorArray(errArray) {
                // If it's array of objects with path/message
                if (errArray.length > 0 && typeof errArray[0] === 'object' &&
                    errArray[0] !== null && errArray[0].path && errArray[0].message) {

                    let errorList = '<ul>';
                    errArray.forEach(err => {
                        errorList += `<li><strong>${err.path}</strong>: ${err.message}</li>`;
                    });
                    errorList += '</ul>';
                    return errorList;
                }

                // If it's just an array of strings
                if (typeof errArray[0] === 'string') {
                    let errorList = '<ul>';
                    errArray.forEach(err => {
                        errorList += `<li>${err}</li>`;
                    });
                    errorList += '</ul>';
                    return errorList;
                }

                // Fallback: join as comma-separated list
                return errArray.join(', ');
            }

            if (type === 'success') {
                notification.classList.add('bg-green-100', 'border-l-4', 'border-green-500', 'text-green-700');
                notification.innerHTML = `
                    <div class="flex items-start">
                        <div class="py-1">
                            <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">Berhasil!</p>
                            <div>${message}</div>
                        </div>
                        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                    </div>
                `;
            } else {
                notification.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700', 'overflow-auto');

                // Process error message for proper display
                let processedMessage = '';

                // Handle different error message formats
                if (typeof message === 'string') {
                    processedMessage = message;
                } else if (Array.isArray(message)) {
                    processedMessage = processErrorArray(message);
                } else if (typeof message === 'object' && message !== null) {
                    processedMessage = processErrorObject(message);
                } else {
                    processedMessage = "Terjadi kesalahan";
                }

                // Structure for the notification
                const wrapper = document.createElement('div');
                wrapper.className = 'flex items-start';

                // Icon container
                const iconContainer = document.createElement('div');
                iconContainer.className = 'py-1 flex-shrink-0';
                iconContainer.innerHTML = `
                    <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                `;

                // Content container
                const contentContainer = document.createElement('div');
                contentContainer.className = 'flex-grow max-w-xs sm:max-w-sm md:max-w-md';

                // Title
                const title = document.createElement('p');
                title.className = 'font-bold';
                title.textContent = 'Error!';
                contentContainer.appendChild(title);

                // Message container
                const messageContainer = document.createElement('div');
                messageContainer.className = 'error-message';

                // Always use innerHTML for processedMessage as it may contain HTML
                messageContainer.innerHTML = processedMessage;

                contentContainer.appendChild(messageContainer);

                // Close button
                const closeBtn = document.createElement('span');
                closeBtn.className = 'ml-4 cursor-pointer flex-shrink-0';
                closeBtn.textContent = '×';
                closeBtn.onclick = function() {
                    notification.remove();
                };

                // Assemble the notification
                wrapper.appendChild(iconContainer);
                wrapper.appendChild(contentContainer);
                wrapper.appendChild(closeBtn);
                notification.appendChild(wrapper);
            }

            // Add to document
            document.body.appendChild(notification);

            // Auto-remove notification after 5 seconds
            setTimeout(() => {
                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => notification.remove(), 500);
            }, 5000);

            return notification;
        };

        // Add slide-in animation for error messages
        document.head.insertAdjacentHTML('beforeend', `
            <style>
                @keyframes slideInRight {
                    from { transform: translateX(100%); }
                    to { transform: translateX(0); }
                }
                .animate-slide-in-right {
                    animation: slideInRight 0.3s ease-out forwards;
                }
                .error-message ul {
                    margin-top: 0.5rem;
                    padding-left: 1.5rem;
                    list-style-type: disc;
                }
                .error-message li {
                    margin-bottom: 0.25rem;
                }
                .error-message strong {
                    color: #b91c1c;
                    font-weight: 600;
                }
            </style>
        `);

        // Move the function declarations to the global scope
        function showAllEvents(dateStr) {
            // Format the date for display
            const displayDate = new Date(dateStr);
            const formattedDate = `${displayDate.getDate()} ${months[displayDate.getMonth()]} ${displayDate.getFullYear()}`;

            // Create event list HTML
            let eventListHTML = '';

            // Check if we have events for this date
            if (!calendarEvents[dateStr] || calendarEvents[dateStr].length === 0) {
                eventListHTML = `
                        <div class="text-center py-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="mt-2 text-gray-500">Tidak ada jadwal untuk tanggal ini</p>
                        </div>
                    `;
            } else {
                // Format each event
                calendarEvents[dateStr].forEach(event => {
                    const bgColor = event.type === 'info' ? 'bg-[#E5F6FF]' :
                        event.type === 'urgent' ? 'bg-[#FFE5E5]' :
                            'bg-[#FFF8E5]';

                    const typeLabel = event.type === 'info' ? 'Kalibrasi' :
                        event.type === 'urgent' ? 'Perawatan' :
                            'Garansi';

                    eventListHTML += `
                            <div class="mb-3 p-4 ${bgColor} rounded-lg shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="font-medium text-lg mb-2">${event.title}</div>
                                    <span class="text-xs font-medium px-2 py-1 rounded-full ${event.type === 'info' ? 'bg-blue-100 text-blue-800' :
                            event.type === 'urgent' ? 'bg-red-100 text-red-800' :
                                'bg-yellow-100 text-yellow-800'
                        }">${typeLabel}</span>
                                </div>
                                ${event.assetName ? `<div class="text-sm mb-1"><span class="font-medium">Asset:</span> ${event.assetName}</div>` : ''}
                                ${event.location ? `<div class="text-sm mb-1"><span class="font-medium">Lokasi:</span> ${event.location}</div>` : ''}
                                ${event.url ? `
                                    <div class="mt-3 pt-2 border-t border-gray-200">
                                        <a href="${event.url}" class="inline-flex items-center text-[#213268] hover:text-[#152349] text-sm">
                                            <span>Lihat detail</span>
                                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                        </a>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                });
            }

            // Create modal HTML with improved styling
            const modalHTML = `
                    <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" onclick="document.getElementById('eventModal').remove()">
                        <div class="bg-white rounded-lg shadow-xl p-0 max-w-md w-full max-h-[80vh] overflow-hidden" onclick="event.stopPropagation()">
                            <div class="flex justify-between items-center p-4 bg-[#213268] text-white">
                                <h3 class="text-xl font-bold">Jadwal: ${formattedDate}</h3>
                                <button onclick="document.getElementById('eventModal').remove()" class="text-white hover:text-white/80 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="p-6 overflow-y-auto max-h-[60vh]">
                                ${eventListHTML}
                            </div>
                        </div>
                    </div>
                `;

            // Append modal to body
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        function changeMonth(delta) {
            currentMonth += delta;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            } else if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }

            // Fetch new data for the selected month
            fetchCalendarData(currentYear, currentMonth);
        }

        function goToSelectedDate() {
            const monthSelector = document.getElementById('monthSelector');
            const yearSelector = document.getElementById('yearSelector');

            currentMonth = parseInt(monthSelector.value);
            currentYear = parseInt(yearSelector.value);

            // Fetch data for selected month/year
            fetchCalendarData(currentYear, currentMonth);
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize chart with data from PHP
            const ctx = document.getElementById('assetStatusChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Tersedia', 'Perawatan', 'Pinjam', 'Dihapuskan', 'Hilang'],
                    datasets: [{
                        data: [
                                {{ $dashboardData['assets_by_status']['available'] ?? 0 }},
                                {{ $dashboardData['assets_by_status']['under repair'] ?? 0 }},
                                {{ $dashboardData['assets_by_status']['checked out'] ?? 0 }},
                                {{ $dashboardData['assets_by_status']['dispose'] ?? 0 }},
                            {{ $dashboardData['assets_by_status']['lost'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#7CB60C',  // Available - Green
                            '#25B1FF',  // Maintenance - Grey
                            '#DAAE0F',  // Check Out - yellow
                            '#ACC3EF',  // Dispose - Blue
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

            // Toggle display between number and percentage
            const displayToggle = document.getElementById('displayToggle');
            const numberValues = document.querySelectorAll('.number-value');
            const percentValues = document.querySelectorAll('.percent-value');

            displayToggle.addEventListener('change', function () {
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

            // Fill year selector with current year and ±5 years
            populateYearSelector();

            // Set initial selection in dropdowns
            document.getElementById('monthSelector').value = currentMonth;
            document.getElementById('yearSelector').value = currentYear;

            // Make calendar functions accessible globally
            window.showAllEvents = showAllEvents;
            window.changeMonth = changeMonth;
            window.goToSelectedDate = goToSelectedDate;
            window.fetchCalendarData = fetchCalendarData;
            window.generateCalendar = generateCalendar;

            // Initialize calendar and fetch initial data here, within DOMContentLoaded
            generateCalendar(currentMonth, currentYear);
            fetchCalendarData(currentYear, currentMonth);
            loadAssetActivities(); // Load initial asset activities

            // Apply form submission protection
            const performCalibrationForm = document.getElementById('performCalibrationForm');
            if (performCalibrationForm) {
                preventMultipleSubmits(performCalibrationForm, 'button[type="submit"]');
            }

            // Function to populate year selector with options
            function populateYearSelector() {
                const yearSelector = document.getElementById('yearSelector');
                const currentYear = new Date().getFullYear();

                // Clear existing options
                yearSelector.innerHTML = '';

                // Add options for 5 years before and after current year
                for (let year = currentYear - 5; year <= currentYear + 5; year++) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    yearSelector.appendChild(option);
                }
            }

            // Move fetchCalendarData to global scope AND make it globally accessible
            async function fetchCalendarData(year, month) {
                try {
                    // Update the selectors to match the current year and month
                    document.getElementById('monthSelector').value = month;
                    document.getElementById('yearSelector').value = year;

                    // Show a loading state for calendar
                    document.getElementById('calendarDays').innerHTML = `
                        <div class="col-span-7 text-center py-8">
                            <span class="loading loading-spinner loading-md text-[#213268]"></span>
                            <p class="text-gray-500 mt-2">Memuat data kalender...</p>
                        </div>
                    `;



                    const response = await fetch(`/dashboard/calendar?year=${year}&month=${month + 1}`);
                    const data = await response.json();



                    if (data.success) {
                        // Update UI with fetched data
                        const eventsData = data.data?.events || [];
                        processCalendarEvents(eventsData);
                    } else {
                        // Handle API error
                        document.getElementById('calendarDays').innerHTML = `
                            <div class="col-span-7 text-center py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-gray-500 mt-2">Gagal memuat data kalender</p>
                                <p class="text-xs text-gray-500 mt-1">${JSON.stringify(data.errors)}</p>
                            </div>
                        `;
                    }
                } catch (error) {
                    // Handle network error
                    document.getElementById('calendarDays').innerHTML = `
                        <div class="col-span-7 text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            <p class="mt-2 text-gray-500">Kesalahan jaringan, coba lagi nanti</p>
                        </div>
                    `;
                }
            }

            // Move processCalendarEvents to global scope
            function processCalendarEvents(apiEvents) {
                // Clear previous events
                calendarEvents = {};

                if (!apiEvents || !apiEvents.length) {
                    // Generate calendar with no events
                    generateCalendar(currentMonth, currentYear);
                    return;
                }



                // Default date will be middle of current month
                const defaultDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-15`;

                apiEvents.forEach(event => {


                    // Handle missing date property
                    let dateStr = event.date;
                    if (!dateStr) {


                        // Try to extract date from other properties if available
                        if (event.scheduled_date) {
                            dateStr = event.scheduled_date;
                        } else if (event.start_date) {
                            dateStr = event.start_date;
                        } else if (event.created_at) {
                            // Extract just the date part if it's a datetime
                            const createdDate = new Date(event.created_at);
                            dateStr = createdDate.toISOString().split('T')[0];
                        } else {
                            // Generate a random date within this month for demo purposes
                            const day = Math.floor(Math.random() * 28) + 1; // Random day between 1-28
                            dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        }


                    }

                    // Ensure date format is consistent (YYYY-MM-DD)
                    if (dateStr && !dateStr.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        try {
                            // Try to parse and format the date if it's in a different format
                            const parsedDate = new Date(dateStr);
                            if (!isNaN(parsedDate.getTime())) {
                                dateStr = parsedDate.toISOString().split('T')[0];
                            } else {
                                dateStr = defaultDate;
                            }
                        } catch (e) {

                            dateStr = defaultDate;
                        }
                    }

                    if (!calendarEvents[dateStr]) {
                        calendarEvents[dateStr] = [];
                    }

                    // Map event types to UI styles
                    let type = 'info';
                    if (event.type === 'calibration') type = 'info';
                    if (event.type === 'maintenance') type = 'urgent';
                    if (event.type === 'warranty') type = 'warning';

                    // Extract title from event data
                    const title = event.title || event.description || event.asset_master_name || 'Event';

                    calendarEvents[dateStr].push({
                        title: title,
                        type: type,
                        assetId: event.asset_id,
                        assetCode: event.asset_code,
                        assetName: event.asset_name || event.asset_master_name,
                        location: event.location,
                        url: event.url || null // Link to detail view if available
                    });
                });



                // Sort events by priority (urgent first, then warning, then info)
                for (const date in calendarEvents) {
                    calendarEvents[date].sort((a, b) => {
                        const priority = { 'urgent': 1, 'warning': 2, 'info': 3 };
                        return priority[a.type] - priority[b.type];
                    });
                }

                // Regenerate calendar to show new events
                generateCalendar(currentMonth, currentYear);
            }

            // Move generateCalendar to global scope
            function generateCalendar(month, year) {
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const startingDay = firstDay.getDay();
                const totalDays = lastDay.getDate();

                // Update header and selectors
                document.getElementById('currentMonth').textContent = months[month];
                document.getElementById('currentYear').textContent = year;
                document.getElementById('monthSelector').value = month;
                document.getElementById('yearSelector').value = year;

                const calendarDays = document.getElementById('calendarDays');
                calendarDays.innerHTML = '';

                // Previous month's days
                const prevMonthLastDay = new Date(year, month, 0).getDate();
                for (let i = startingDay - 1; i >= 0; i--) {
                    const day = prevMonthLastDay - i;
                    calendarDays.innerHTML += `
                        <div class="p-2 min-h-[104px] bg-white border border-[#213268]/5">
                            <div class="text-xs font-['Poppins'] text-center text-[#213268] opacity-30">${day}</div>
                        </div>
                    `;
                }

                // Current month's days
                for (let day = 1; day <= totalDays; day++) {
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const isToday = day === new Date().getDate() &&
                        month === new Date().getMonth() &&
                        year === new Date().getFullYear();

                    const hasEvents = calendarEvents[dateStr] && calendarEvents[dateStr].length > 0;

                    let dayEvents = '';
                    if (hasEvents) {


                        // Show max 3 events in the calendar cell
                        const visibleEvents = calendarEvents[dateStr].slice(0, 3);
                        dayEvents = visibleEvents.map(event => {
                            const bgColor = event.type === 'info' ? 'bg-[#E5F6FF]' :
                                event.type === 'urgent' ? 'bg-[#FFE5E5]' :
                                    'bg-[#FFF8E5]'; // For warning type

                            const borderColor = event.type === 'info' ? 'border-l-4 border-l-[#25B1FF]' :
                                event.type === 'urgent' ? 'border-l-4 border-l-[#FF4A2B]' :
                                    'border-l-4 border-l-[#DAAE0F]'; // For warning type

                            if (event.url) {
                                return `<a href="${event.url}" class="px-2 py-1.5 ${bgColor} ${borderColor} text-xs font-['Poppins'] mb-1 rounded shadow-sm truncate block hover:bg-opacity-80">${event.title}</a>`;
                            } else {
                                return `<div class="px-2 py-1.5 ${bgColor} ${borderColor} text-xs font-['Poppins'] mb-1 rounded shadow-sm truncate">${event.title}</div>`;
                            }
                        }).join('');

                        // Add "view more" link if there are more than 3 events
                        if (calendarEvents[dateStr].length > 3) {
                            const moreCount = calendarEvents[dateStr].length - 3;
                            dayEvents += `
                                    <div class="text-right">
                                        <span class="text-[#213268] text-xs cursor-pointer font-medium hover:underline" onclick="showAllEvents('${dateStr}')">+${moreCount} lainnya</span>
                                    </div>
                                `;
                        }
                    }

                    // Add classes for hover effect and cursor pointer if the day has events or is in current month
                    const dayClasses = [
                        'p-2',
                        'min-h-[104px]',
                        isToday ? 'bg-[#213268]/10' : 'bg-white',
                        'border',
                        'border-[#213268]/10',
                        'transition-all',
                        'duration-200',
                        'hover:shadow-md',
                        'hover:border-[#213268]/30',
                        'cursor-pointer'
                    ].join(' ');

                    calendarDays.innerHTML += `
                            <div class="${dayClasses}" onclick="showAllEvents('${dateStr}')">
                                <div class="flex justify-between items-center mb-2">
                                    <div class="text-sm font-['Poppins'] ${isToday ? 'font-bold' : ''}">${day}</div>
                                    ${hasEvents ? `<div class="w-2.5 h-2.5 rounded-full bg-[#213268] animate-pulse"></div>` : ''}
                                </div>
                                <div class="flex flex-col gap-1">
                                    ${dayEvents}
                                </div>
                            </div>
                        `;
                }

                // Next month's days
                const remainingDays = 42 - (startingDay + totalDays);
                for (let day = 1; day <= remainingDays; day++) {
                    calendarDays.innerHTML += `
                        <div class="p-2 min-h-[104px] bg-white border border-[#213268]/5">
                            <div class="text-xs font-['Poppins'] text-center text-[#213268] opacity-30">${day}</div>
                        </div>
                    `;
                }
            }

            // Function to format date from ISO string to readable format
            function formatDate(dateString) {
                const date = new Date(dateString);
                const day = String(date.getDate()).padStart(2, '0');

                // Indonesian month names (abbreviated)
                const indonesianMonths = [
                    'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                    'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
                ];
                const month = indonesianMonths[date.getMonth()];
                const year = date.getFullYear().toString().slice(-2);

                return `${day} ${month} ${year}`;
            }

            // Function to format time from ISO string to readable format
            function formatTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleString('id-ID', { hour: '2-digit', minute: '2-digit' });
            }

            // Function to create HTML for a single activity item
            function createActivityItemHTML(activity) {
                // Map activity status to badge classes
                const getStatusBadgeClass = (statusType) => {
                    const statusClasses = {
                        'Available': 'bg-[#659B09]',
                        'Checked out': 'bg-[#F59E0B]',
                        'Under Repair': 'bg-[#25B1FF]',
                        'Dispose': 'bg-[#ACC3EF]',
                        'Lost': 'bg-[#FF4A2B]'
                    };
                    return statusClasses[statusType] || 'bg-gray-500';
                };

                // Map status to Indonesian translation
                const getStatusLabel = (status) => {
                    const statusLabels = {
                        'Available': 'TERSEDIA',
                        'Checked out': 'DIPINJAM',
                        'Under Repair': 'PERBAIKAN',
                        'Dispose': 'DIHAPUSKAN',
                        'Lost': 'HILANG'
                    };
                    return statusLabels[status] || status.toUpperCase();
                };

                // Create status badge HTML
                const createStatusBadge = (status) => {
                    return `<span class="py-1 px-3 rounded-md text-xs text-white ${getStatusBadgeClass(status)}">${getStatusLabel(status)}</span>`;
                };

                // Format the date and time from action_date
                const dateFormatted = activity.action_date ? formatDate(activity.action_date) : 'N/A';
                const timeFormatted = activity.action_date ? formatTime(activity.action_date) : 'N/A';

                // Get user info
                const userName = activity.user ? activity.user.employee_number || 'User' : 'User';

                // Get asset info
                const assetCode = activity.asset_info ? activity.asset_info.asset_code : 'N/A';
                const assetName = activity.asset_info ? activity.asset_info.asset_name : '';

                // Create content based on activity type
                let content = '';
                if (activity.action_type === 'ASSET_STATUS_CHANGE') {
                    content = `
                        <p class="text-lg font-normal font-['Poppins'] text-black">
                            ${userName} mengubah status dari
                            ${createStatusBadge(activity.old_status)} ke
                            ${createStatusBadge(activity.new_status)}
                        </p>
                    `;
                } else if (activity.action_type === 'ASSET_LOCATION_CHANGE') {
                    content = `
                        <p class="text-lg font-normal font-['Poppins'] text-black">
                            ${userName} memindahkan dari
                            <span class="font-medium text-[#213268]">${activity.old_location || 'Unknown'}</span> ke
                            <span class="font-medium text-[#213268]">${activity.new_location || 'Unknown'}</span>
                        </p>
                    `;
                } else {
                    content = `
                        <p class="text-lg font-normal font-['Poppins'] text-black">
                            ${userName} ${activity.message || 'melakukan update'}
                        </p>
                    `;
                }

                return `
                    <div class="relative w-full bg-white shadow-sm border border-[#ECECEC] rounded-lg p-4 mb-3 hover:shadow-md transition-shadow duration-200">
                        <!-- Blue Line -->
                        <div class="absolute left-0 top-0 w-1.5 h-full bg-[#25B1FF] rounded-l-lg"></div>

                        <div class="flex flex-col md:flex-row pl-4">
                            <!-- Date & Time -->
                            <div class="flex items-center md:flex-col md:items-start gap-2 md:gap-1 mb-3 md:mb-0 md:min-w-[120px] md:mr-6">
                                <p class="text-lg font-medium font-['Poppins'] text-[#213268]">${dateFormatted}</p>
                                <p class="text-base font-medium font-['Poppins'] text-[#757575]">${timeFormatted}</p>
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <!-- Asset Information -->
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <div class="inline-flex items-center px-3 py-1 bg-[#F3F6FF] rounded-md">
                                        <span class="text-sm font-medium text-[#213268]">Kode Aset: ${assetCode}</span>
                                    </div>
                                    ${assetName ? `
                                    <div class="inline-flex items-center px-3 py-1 bg-[#F3F6FF] rounded-md">
                                        <span class="text-sm font-medium text-[#213268]">Aset: ${assetName}</span>
                                    </div>
                                    ` : ''}
                                </div>

                                <!-- Status Change -->
                                <div class="text-base font-['Poppins'] text-[#213268]">
                                    ${userName} mengubah status dari ${createStatusBadge(activity.old_status)} ke ${createStatusBadge(activity.new_status)}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Function to load asset activities
            async function loadAssetActivities(page = 1, append = false) {
                if (isLoadingActivities) return;
                isLoadingActivities = true;

                const activitiesContainer = document.getElementById('asset-activities');
                const loadMoreContainer = document.getElementById('load-more-container');

                // Show loading state if not appending
                if (!append) {
                    activitiesContainer.innerHTML = `
                        <div class="text-center py-12">
                            <div class="inline-block p-4 bg-[#213268]/5 rounded-full">
                                <span class="loading loading-spinner loading-md text-[#213268]"></span>
                            </div>
                            <p class="mt-3 text-gray-600 font-medium">Memuat aktivitas terakhir...</p>
                        </div>
                    `;
                } else {
                    // Add a loading indicator at the bottom when loading more
                    activitiesContainer.insertAdjacentHTML('beforeend', `
                        <div id="activities-loading-more" class="text-center py-6 animate-pulse">
                            <div class="inline-block p-2 bg-[#213268]/5 rounded-full">
                                <span class="loading loading-spinner loading-sm text-[#213268]"></span>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">Memuat lebih banyak aktivitas...</p>
                        </div>
                    `);
                }

                try {
                    const response = await fetch(`/dashboard/activities?page=${page}&limit=${activitiesPerPage}`);
                    const data = await response.json();



                    // Remove loading state
                    if (append) {
                        const loadingMore = document.getElementById('activities-loading-more');
                        if (loadingMore) loadingMore.remove();
                    } else {
                        activitiesContainer.innerHTML = '';
                    }

                    if (data.success) {
                        const activities = data.data?.histories || [];
                        const pagination = data.data?.pagination || {};

                        // Update pagination state
                        currentActivitiesPage = pagination.current_page || page;
                        hasMoreActivities = pagination.has_next || false;

                        // Display activities
                        if (activities.length === 0 && !append) {
                            activitiesContainer.innerHTML = `
                                <div class="text-center py-12 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="inline-block p-3 bg-gray-100 rounded-full mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-700 mb-1">Tidak Ada Aktivitas</h3>
                                    <p class="text-gray-500">Belum ada aktivitas aset yang tercatat</p>
                                </div>
                            `;
                        } else {
                            const activitiesHTML = activities.map(activity => createActivityItemHTML(activity)).join('');

                            if (append) {
                                activitiesContainer.insertAdjacentHTML('beforeend', activitiesHTML);
                            } else {
                                activitiesContainer.innerHTML = activitiesHTML;
                            }
                        }

                        // Show/hide load more button
                        loadMoreContainer.style.display = hasMoreActivities ? 'block' : 'none';
                    } else {
                        // Show error message
                        if (!append) {
                            activitiesContainer.innerHTML = `
                                <div class="text-center py-12 bg-red-50 rounded-lg border border-red-100">
                                    <div class="inline-block p-3 bg-red-100 rounded-full mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-red-700 mb-1">Gagal Memuat Data</h3>
                                    <p class="text-red-500">${data.errors?.general || 'Terjadi kesalahan saat memuat aktivitas'}</p>
                                    <button onclick="loadAssetActivities()" class="mt-4 px-4 py-2 bg-[#213268] text-white text-sm rounded-md hover:bg-[#152349] transition-colors duration-200">
                                        Coba Lagi
                                    </button>
                                </div>
                            `;
                        }
                    }
                } catch (error) {


                    // Remove loading indicator if appending
                    if (append) {
                        const loadingMore = document.getElementById('activities-loading-more');
                        if (loadingMore) loadingMore.remove();
                    } else {
                        // Show error message
                        activitiesContainer.innerHTML = `
                            <div class="text-center py-12 bg-red-50 rounded-lg border border-red-100">
                                <div class="inline-block p-3 bg-red-100 rounded-full mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-red-700 mb-1">Kesalahan Jaringan</h3>
                                <p class="text-red-500">Gagal terhubung ke server, mohon periksa koneksi Anda</p>
                                <p class="text-xs text-red-400 mt-1">${error.message}</p>
                                <button onclick="loadAssetActivities()" class="mt-4 px-4 py-2 bg-[#213268] text-white text-sm rounded-md hover:bg-[#152349] transition-colors duration-200">
                                    Coba Lagi
                                </button>
                            </div>
                        `;
                    }
                } finally {
                    isLoadingActivities = false;
                }
            }

            // Set up load more button event listener
            document.getElementById('load-more-activities').addEventListener('click', () => {
                if (!isLoadingActivities && hasMoreActivities) {
                    loadAssetActivities(currentActivitiesPage + 1, true);
                }
            });

            // Calendar is now initialized inside the DOMContentLoaded event handler

            // Animations for asset category circles
            const animateCircles = () => {
                document.querySelectorAll('.progress-circle').forEach(circle => {
                    const percentage = parseInt(circle.getAttribute('data-percentage') || '0');

                    // Don't animate if the percentage is 0
                    if (percentage === 0) {
                        circle.style.display = 'none'; // Hide the circle
                    } else {
                        // Make sure the circle is visible and has the correct offset
                        circle.style.display = 'block';
                        // The stroke-dashoffset value is set by PHP in the template
                    }
                });
            };

            // Animations for asset location bars
            const animateBars = () => {
                document.querySelectorAll('.animate-loading-bar').forEach(bar => {
                    const targetWidth = bar.getAttribute('data-width');
                    // Don't animate if width is 0
                    if (parseInt(targetWidth) === 0) {
                        bar.style.width = '0%';
                        bar.classList.remove('animate-loading-bar'); // Remove animation class
                    } else {
                        bar.style.width = `${targetWidth}%`;
                    }
                });
            };

            // Count-up animation for numbers
            const animateCounters = () => {
                document.querySelectorAll('.animate-count-up').forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target'), 10);
                    const isPercentage = counter.textContent.includes('%');
                    const duration = 1000; // 1 second
                    let startTime = null;

                    function updateCounter(timestamp) {
                        if (!startTime) startTime = timestamp;
                        const progress = Math.min((timestamp - startTime) / duration, 1);
                        const value = Math.floor(progress * target);
                        counter.textContent = isPercentage ? `${value}%` : value;

                        if (progress < 1) {
                            window.requestAnimationFrame(updateCounter);
                        } else {
                            counter.textContent = isPercentage ? `${target}%` : target;
                        }
                    }

                    window.requestAnimationFrame(updateCounter);
                });
            };

            // Apply staggered fade-in animations
            const applyFadeInStagger = () => {
                document.querySelectorAll('.animate-fade-in').forEach((element, index) => {
                    element.style.animationDelay = `${index * 100}ms`;
                    element.style.opacity = 1;
                });
            };

            // Initialize all animations
            setTimeout(() => {
                applyFadeInStagger();
                animateCircles();
                animateBars();
                animateCounters();
            }, 300);

            // Calibration Modal Functions


            // Define the modal elements - get them directly
            const modals = {
                view: document.getElementById('viewCalibrationModal'),
                delete: document.getElementById('deleteCalibrationModal')
            };

            const modalContents = {
                view: document.getElementById('viewCalibrationModalContent'),
                delete: document.getElementById('deleteCalibrationModalContent')
            };



            // Function to open modal
            function openModal(modal, content) {

                if (!modal || !content) {

                    return;
                }
                modal.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                    content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                }, 10);
            }

            // Function to close modal
            function closeModal(modal, content) {
                if (!modal || !content) return;
                content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            // Close modal buttons
            document.querySelectorAll('.close-modal').forEach(button => {

                button.addEventListener('click', () => {

                    const modalId = button.getAttribute('data-modal');


                    // Use direct references for more reliability
                    if (modalId === 'viewCalibrationModal') {
                        closeModal(modals.view, modalContents.view);
                    } else {
                        // Fallback to the dynamic approach
                        const modal = document.getElementById(modalId);
                        if (!modal) {

                            return;
                        }
                        const content = modal.querySelector('[id$="ModalContent"]');
                        if (!content) {

                            return;
                        }
                        closeModal(modal, content);
                    }
                });
            });

            // Open calibration modal when clicking on edit button in upcoming calibrations
            const calibrationButtons = document.querySelectorAll('.edit-calibration-btn');


            calibrationButtons.forEach(button => {


                button.addEventListener('click', function (e) {
                    // Make sure we're using the correct modal
                    const modal = document.getElementById('viewCalibrationModal');
                    const modalContent = document.getElementById('viewCalibrationModalContent');

                    e.preventDefault();
                    const calibrationId = this.getAttribute('data-id');

                    // Set the calibration ID in the hidden field
                    document.getElementById('calibration_id').value = calibrationId;



                    // Fetch calibration details using search parameter
                    fetch(`/calibrations?search=${encodeURIComponent(calibrationId)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw data;
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Extract calibration data from the response
                                let calibration;

                                // Check all possible data structures
                                if (data.calibrations && Array.isArray(data.calibrations) && data.calibrations.length > 0) {
                                    // If data is in data.calibrations array
                                    calibration = data.calibrations[0];
                                } else if (data.data && Array.isArray(data.data) && data.data.length > 0) {
                                    // If data is in data.data array
                                    calibration = data.data[0];
                                } else if (typeof data.data === 'object' && data.data !== null) {
                                    // If data is directly in data.data object
                                    calibration = data.data;
                                }

                                if (!calibration) {
                                    showToast('Tidak dapat menemukan data kalibrasi', 'error');
                                    return;
                                }

                                // Store the actual calibration ID for submission later
                                document.getElementById('calibration_id').value = calibration.id || calibrationId;

                                // Set current date as work date by default when modal opens
                                const today = new Date().toISOString().split('T')[0];
                                document.getElementById('actual_calibration_date').value = today;

                                // Set min date for next_calibration_date to today
                                const nextCalibrationDateInput = document.getElementById('next_calibration_date');
                                if (nextCalibrationDateInput) {
                                    nextCalibrationDateInput.setAttribute('min', today);
                                }

                                // Set read-only display fields
                                document.getElementById('planning_date_display').value = calibration.planning_calibration_date || '';
                                document.getElementById('asset_code_display').value = calibration.asset_code || '-';
                                document.getElementById('asset_name_display').value = calibration.asset_name || '-';
                                document.getElementById('brand_name_display').value = calibration.brand_name || '-';
                                document.getElementById('model_number_display').value = calibration.model_number || '-';
                                document.getElementById('serial_number_display').value = calibration.serial_number || '-';

                                // Set location display
                                let locationText = '-';
                                if (calibration.location) {
                                    const locationParts = [];
                                    if (calibration.location.room_name) locationParts.push(calibration.location.room_name);
                                    if (calibration.location.floor_number) locationParts.push(calibration.location.floor_number);
                                    if (calibration.location.building_name) locationParts.push(calibration.location.building_name);
                                    if (locationParts.length > 0) {
                                        locationText = locationParts.join(' | ');
                                    }
                                }
                                document.getElementById('location_display').value = locationText;

                                // Clear form fields that need to be filled by user
                                document.getElementById('certificate_number').value = '';
                                document.getElementById('vendor_search').value = '';
                                document.getElementById('vendor_id').value = '';
                                document.getElementById('calibration_price').value = '';
                                document.getElementById('notes').value = '';

                                // Clear radio buttons
                                document.getElementById('result_pass').checked = false;
                                document.getElementById('result_fail').checked = false;
                                document.getElementById('result_unknown').checked = false;

                                // Reset file input
                                const fileInput = document.getElementById('document_file');
                                if (fileInput) fileInput.value = '';

                                // Hide file preview
                                const filePreview = document.getElementById('file-preview');
                                if (filePreview) filePreview.classList.add('hidden');

                                openModal(modals.view, modalContents.view);
                            } else {
                                // Show error toast (implement toast function if not already available)
                                showToast('Failed to load calibration details: ' + (data.message || 'Unknown error'), 'error');
                            }
                        })
                        .catch(error => {
                            showToast('Error loading calibration details: ' + (error.message || 'Unknown error'), 'error');
                        });
                });
            });

            // Add file upload functionality
            document.getElementById('document_file')?.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    // Show preview container
                    const filePreview = document.getElementById('file-preview');
                    const imagePreview = document.getElementById('image-preview');
                    const fileInfo = document.getElementById('file-info');
                    const fileNameText = document.getElementById('file-name-text');

                    if (filePreview) filePreview.classList.remove('hidden');

                    // Set file name
                    if (fileNameText) fileNameText.textContent = file.name;

                    // Check if file is an image
                    if (file.type.startsWith('image/')) {
                        // Show image preview, hide file info
                        if (imagePreview) {
                            const objectUrl = URL.createObjectURL(file);
                            imagePreview.src = objectUrl;
                            imagePreview.classList.remove('hidden');
                        }
                        if (fileInfo) fileInfo.classList.add('hidden');
                    } else {
                        // Show file info, hide image preview
                        if (fileInfo) fileInfo.classList.remove('hidden');
                        if (imagePreview) imagePreview.classList.add('hidden');
                    }
                }
            });

            // Remove selected file
            document.getElementById('remove-file')?.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                // Reset file input
                const fileInput = document.getElementById('document_file');
                if (fileInput) fileInput.value = '';

                // Hide preview
                const filePreview = document.getElementById('file-preview');
                if (filePreview) filePreview.classList.add('hidden');

                // Clean up image preview URL if it exists
                const imagePreview = document.getElementById('image-preview');
                if (imagePreview && imagePreview.src) {
                    URL.revokeObjectURL(imagePreview.src);
                    imagePreview.src = '';
                }
            });

            // Form submission for calibration
            document.getElementById('performCalibrationForm')?.addEventListener('submit', function (e) {
                e.preventDefault();

                const calibrationId = document.getElementById('calibration_id').value;

                // Create a new FormData object instead of using the form directly
                const formData = new FormData();

                // Get all form fields
                const formElements = this.elements;

                // Only add non-empty fields to the FormData
                for (let i = 0; i < formElements.length; i++) {
                    const element = formElements[i];

                    // Skip buttons, fieldsets, and hidden calibration_id field
                    if (element.type === 'button' || element.type === 'submit' ||
                        element.tagName === 'FIELDSET' || element.name === 'calibration_id') {
                        continue;
                    }

                    // Handle different input types
                    if (element.type === 'radio' || element.type === 'checkbox') {
                        // Only include checked radio/checkbox values
                        if (element.checked) {
                            formData.append(element.name, element.value);
                        }
                    }
                    // Handle file inputs
                    else if (element.type === 'file') {
                        // Only include files if they exist
                        if (element.files && element.files.length > 0) {
                            formData.append(element.name, element.files[0]);
                        }
                    }
                    // Handle all other input types
                    else if (element.value.trim() !== '') {
                        formData.append(element.name, element.value.trim());

                        // Log for debugging
                        console.log(`Adding field: ${element.name} = ${element.value.trim()}`);
                    } else {
                        console.log(`Skipping empty field: ${element.name}`);
                    }
                }

                // Add _method field for PUT request
                formData.append('_method', 'PUT');

                // Log all form data that will be sent
                console.log('Form data to be sent:');
                for (const pair of formData.entries()) {
                    console.log(`${pair[0]}: ${pair[1]}`);
                }

                // Clear any existing field error highlights
                this.querySelectorAll('.border-red-500').forEach(el => {
                    el.classList.remove('border-red-500');
                });

                // Show loading state on button
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <div class="flex items-center justify-center">
                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                        <span>Memproses...</span>
                    </div>
                `;

                fetch(`/calibrations/report/${calibrationId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    // Log the raw response for debugging
                    console.log('Response status:', response.status);
                    return response.json().then(data => {
                        // Always capture the data, don't throw it yet
                        if (!response.ok) {
                            console.log('Error response data:', data);
                            return Promise.reject(data);
                        }
                        return data;
                    });
                })
                .then(data => {
                    console.log('Success response:', data);
                    if (data.success) {
                        // Close the modal
                        closeModal(modals.view, modalContents.view);

                        // Show success message
                        showToast(data.message || 'Kalibrasi berhasil disimpan', 'success');

                        // Reload the page after a slight delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        // Handle unexpected "success: false" in a successful response
                        console.warn('Received success:false in a 2xx response', data);
                        showToast(data, 'error');

                        // Re-enable the submit button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                })
                .catch(error => {
                    console.error('Form submission error:', error);

                    // Pass the complete error object to showToast for smart formatting
                    showToast(error, 'error');

                    // Highlight fields with errors if we can identify them
                    if (error.errors) {
                        // Handle array of error objects with path/message format
                        if (Array.isArray(error.errors)) {
                            error.errors.forEach(err => {
                                if (err.path) {
                                    const field = document.getElementById(err.path) ||
                                                document.querySelector(`[name="${err.path}"]`);
                                    if (field) {
                                        field.classList.add('border-red-500');
                                    }
                                }
                            });
                        }
                        // Handle object with field names as keys
                        else if (typeof error.errors === 'object') {
                            Object.keys(error.errors).forEach(fieldName => {
                                const field = document.getElementById(fieldName) ||
                                            document.querySelector(`[name="${fieldName}"]`);
                                if (field) {
                                    field.classList.add('border-red-500');
                                }
                            });
                        }
                    }

                    // Re-enable the submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
            });
        });


        // Animations for asset category circles
        const animateCircles = () => {
            document.querySelectorAll('.animate-loading-circle').forEach(circle => {
                const targetRotation = circle.getAttribute('data-rotation');
                const percentage = parseInt(circle.getAttribute('data-percentage') || '0');

                // Don't animate if the percentage is 0
                if (percentage === 0) {
                    circle.style.display = 'none'; // Completely hide the circle
                } else {
                    // For transform-based animations (0-50%)
                    circle.style.transform = `rotate(${percentage * 1.8}deg)`;
                }
            });
        };

        // Animations for asset location bars
        const animateBars = () => {
            document.querySelectorAll('.animate-loading-bar').forEach(bar => {
                const targetWidth = bar.getAttribute('data-width');
                // Don't animate if width is 0
                if (parseInt(targetWidth) === 0) {
                    bar.style.width = '0%';
                    bar.classList.remove('animate-loading-bar'); // Remove animation class
                } else {
                    bar.style.width = `${targetWidth}%`;
                }
            });
        };

        // Count-up animation for numbers
        const animateCounters = () => {
            document.querySelectorAll('.animate-count-up').forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'), 10);
                const isPercentage = counter.textContent.includes('%');
                const duration = 1000; // 1 second
                let startTime = null;

                function updateCounter(timestamp) {
                    if (!startTime) startTime = timestamp;
                    const progress = Math.min((timestamp - startTime) / duration, 1);
                    const value = Math.floor(progress * target);
                    counter.textContent = isPercentage ? `${value}%` : value;

                    if (progress < 1) {
                        window.requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = isPercentage ? `${target}%` : target;
                    }
                }

                window.requestAnimationFrame(updateCounter);
            });
        };

        // Apply staggered fade-in animations
        const applyFadeInStagger = () => {
            document.querySelectorAll('.animate-fade-in').forEach((element, index) => {
                element.style.animationDelay = `${index * 100}ms`;
                element.style.opacity = 1;
            });
        };

        // Initialize all animations
        setTimeout(() => {
            applyFadeInStagger();
            animateCircles();
            animateBars();
            animateCounters();
        }, 300);

        // Asset Activities Pagination

        // Close the DOMContentLoaded event listener

        // Vendor search functionality with debounce
        let allVendors = []; // Store all vendors for client-side filtering
        const vendorSearchInput = document.getElementById('vendor_search');
        const vendorIdInput = document.getElementById('vendor_id');
        const vendorResults = document.getElementById('vendor_results');

        // Initial load of vendors
        loadAllVendors();

        // Show/hide vendor results
        vendorSearchInput?.addEventListener('focus', function () {
            filterAndDisplayVendors(this.value.trim());
            vendorResults.style.display = 'block';
        });

        // Hide vendor results when clicking outside
        document.addEventListener('click', function (e) {
            if (e.target !== vendorSearchInput && !vendorResults.contains(e.target)) {
                vendorResults.style.display = 'none';
            }
        });

        // Search vendors with debounce
        vendorSearchInput?.addEventListener('input', debounce(function () {
            const searchTerm = this.value.trim();
            filterAndDisplayVendors(searchTerm);
        }, 300));

        // Load all vendors
        function loadAllVendors() {
            vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor...</div>';
            vendorResults.style.display = 'block';

            // First try to get from localStorage to avoid delay
            const cachedVendors = localStorage.getItem('allVendors');
            if (cachedVendors) {
                try {
                    allVendors = JSON.parse(cachedVendors);

                    // Show the dropdown with cached data
                    filterAndDisplayVendors(vendorSearchInput?.value.trim() || '');

                    // Still load fresh data in the background
                    fetchAllVendors();

                    return; // Exit early with cached data
                } catch (e) {
                    console.error('Error parsing cached vendors:', e);
                }
            }

            // If no cache, fetch from API
            fetchAllVendors();
        }

        // Fetch all vendors with pagination
        function fetchAllVendors() {
            let page = 1;
            allVendors = []; // Reset array

            function fetchPage(page) {
                if (page === 1) {
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor...</div>';
                } else {
                    // Update loading message for subsequent pages
                    vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor (halaman ' + page + ')...</div>';
                }

                fetch(`/vendor?json=true&page=${page}&limit=100`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Server merespon dengan status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        let vendors = [];
                        let pagination = null;

                        // Handle different response formats
                        if (Array.isArray(data)) {
                            vendors = data;
                        } else if (data.vendors && Array.isArray(data.vendors)) {
                            vendors = data.vendors;
                            pagination = data.pagination;
                        } else if (data.data && Array.isArray(data.data)) {
                            vendors = data.data;
                            pagination = data.pagination;
                        }

                        // Add to our collection
                        allVendors = [...allVendors, ...vendors];

                        // Check if there are more pages
                        const hasNextPage = pagination && pagination.has_next;

                        if (hasNextPage) {
                            // Fetch next page
                            fetchPage(page + 1);
                        } else {
                            // Cache for future use
                            try {
                                localStorage.setItem('allVendors', JSON.stringify(allVendors));
                            } catch (e) {
                                console.error('Error caching vendors:', e);
                            }

                            // If the input has a value, filter and display
                            if (vendorSearchInput && vendorSearchInput.value.trim()) {
                                filterAndDisplayVendors(vendorSearchInput.value.trim());
                            } else {
                                vendorResults.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => {
                        console.error(`Error fetching vendors page ${page}:`, error);
                        vendorResults.innerHTML = '<div class="p-2 text-sm text-red-500">Gagal memuat vendor</div>';

                        // If we got some vendors, still show them
                        if (allVendors.length > 0) {
                            filterAndDisplayVendors(vendorSearchInput?.value.trim() || '');
                        }
                    });
            }

            // Start fetching from page 1
            fetchPage(page);
        }

        // Filter and display vendors based on search term
        function filterAndDisplayVendors(searchTerm) {
            // Make sure dropdown is visible
            vendorResults.style.display = 'block';

            // Show loading message during search
            if (searchTerm && searchTerm.length > 0) {
                vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Mencari vendor...</div>';
            }

            // If we have no vendors yet
            if (allVendors.length === 0) {
                vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat vendor...</div>';
                return;
            }

            // Filter vendors
            let filteredVendors = allVendors;
            if (searchTerm) {
                const term = searchTerm.toLowerCase();
                filteredVendors = allVendors.filter(vendor => {
                    if (!vendor.vendor_name) return false;
                    return vendor.vendor_name.toLowerCase().includes(term);
                });
            }

            // Sort by relevance if we have a search term
            if (searchTerm) {
                filteredVendors.sort((a, b) => {
                    if (!a.vendor_name || !b.vendor_name) return 0;

                    // Exact matches first
                    if (a.vendor_name.toLowerCase() === searchTerm.toLowerCase()) return -1;
                    if (b.vendor_name.toLowerCase() === searchTerm.toLowerCase()) return 1;

                    // Then starts-with matches
                    const aStarts = a.vendor_name.toLowerCase().startsWith(searchTerm.toLowerCase());
                    const bStarts = b.vendor_name.toLowerCase().startsWith(searchTerm.toLowerCase());
                    if (aStarts && !bStarts) return -1;
                    if (bStarts && !aStarts) return 1;

                    // Then alphabetical
                    return a.vendor_name.localeCompare(b.vendor_name);
                });
            }

            // Limit to first 20 for performance
            const displayVendors = filteredVendors.slice(0, 20);

            // Update DOM with animation delay
            vendorResults.innerHTML = '';

            if (displayVendors.length === 0) {
                vendorResults.innerHTML = '<div class="p-2 text-sm text-gray-500">Tidak ada vendor ditemukan</div>';
                return;
            }

            // Add vendor items with staggered animation
            displayVendors.forEach((vendor, index) => {
                if (!vendor.vendor_name || !vendor.vendor_id) {
                    console.error('Vendor missing name or ID:', vendor);
                    return;
                }

                const div = document.createElement('div');
                div.className = 'p-2 text-sm hover:bg-gray-100 cursor-pointer vendor-item';
                div.textContent = vendor.vendor_name;
                div.setAttribute('data-id', vendor.vendor_id);
                div.style.animationDelay = `${index * 30}ms`; // Staggered animation
                div.classList.add('fade-in');

                div.addEventListener('click', function () {
                    vendorIdInput.value = this.getAttribute('data-id');
                    vendorSearchInput.value = this.textContent;
                    vendorResults.style.display = 'none';
                });

                vendorResults.appendChild(div);
            });

            // Show count if limited
            if (filteredVendors.length > 20) {
                const countDiv = document.createElement('div');
                countDiv.className = 'p-2 text-xs text-gray-500 text-center border-t fade-in';
                countDiv.textContent = `Menampilkan 20 dari ${filteredVendors.length} vendor`;
                vendorResults.appendChild(countDiv);
            }
        }

        // Debounce function to limit API calls
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func.apply(this, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Form submission for calibration
    </script>
@endsection
