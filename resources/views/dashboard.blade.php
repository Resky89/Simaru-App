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
            <div class="card-body p-4">
                <!-- Title -->
                <h2 class="text-2xl font-medium text-[#232D42] font-['Inter'] mb-3">Upcoming Assets Calibration</h2>

                <!-- Divider -->
                <div class="w-full border-t-2 border-[#ECECEC] mb-4"></div>

                <!-- Calibration Items Container -->
                <div class="space-y-4">
                    <!-- Item 1 -->
                    <div class="flex justify-between items-center">
                        <div class="flex gap-4">
                            <div class="flex flex-col w-[173px]">
                                <h3 class="text-lg font-['Inter'] font-medium text-[#232D42]">Infusion Pump XYZ</h3>
                                <p class="text-[14px] font-['Inter'] text-[#8A92A6]">ICU Room 1</p>
                            </div>
                            <div class="flex flex-col w-[70px]">
                                <p class="text-[14px] font-['Inter'] text-[#213268]">19 March</p>
                                <p class="text-[14px] font-['Inter'] text-[#8A92A6]">Wed</p>
                            </div>
                        </div>
                        <button class="flex items-center px-3 py-1.5 border border-[#213268] rounded">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#213268" stroke-width="1.5">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Item 2 -->
                    <div class="flex justify-between items-center">
                        <div class="flex gap-4">
                            <div class="flex flex-col w-[173px]">
                                <h3 class="text-lg font-['Inter'] font-medium text-[#232D42]">Ventilator ABC</h3>
                                <p class="text-[14px] font-['Inter'] text-[#8A92A6]">NICU Room 1</p>
                            </div>
                            <div class="flex flex-col w-[70px]">
                                <p class="text-[14px] font-['Inter'] text-[#213268]">19 March</p>
                                <p class="text-[14px] font-['Inter'] text-[#8A92A6]">Wed</p>
                            </div>
                        </div>
                        <button class="flex items-center px-3 py-1.5 border border-[#213268] rounded">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#213268" stroke-width="1.5">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Item 3 -->
                    <div class="flex justify-between items-center">
                        <div class="flex gap-4">
                            <div class="flex flex-col w-[173px]">
                                <h3 class="text-lg font-['Inter'] font-medium text-[#232D42]">MRI Scanner</h3>
                                <p class="text-[14px] font-['Inter'] text-[#8A92A6]">Radiology</p>
                            </div>
                            <div class="flex flex-col w-[82px]">
                                <p class="text-[14px] font-['Inter'] text-[#213268]">23 March</p>
                                <p class="text-[14px] font-['Inter'] text-[#8A92A6]">Next Week</p>
                            </div>
                        </div>
                        <button class="flex items-center px-3 py-1.5 bg-[#213268] rounded">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Item 4 -->
                    <div class="flex justify-between items-center">
                        <div class="flex gap-4">
                            <div class="flex flex-col w-[173px]">
                                <h3 class="text-lg font-['Inter'] font-medium text-[#232D42]">Anesthesia Machine</h3>
                                <p class="text-[14px] font-['Inter'] text-[#8A92A6]">Operating Room</p>
                            </div>
                            <div class="flex flex-col w-[82px]">
                                <p class="text-[14px] font-['Inter'] text-[#213268]">22 March</p>
                                <p class="text-[14px] font-['Inter'] text-[#8A92A6]">Next Week</p>
                            </div>
                        </div>
                        <button class="flex items-center px-3 py-1.5 border border-[#213268] rounded">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#213268" stroke-width="1.5">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asset By Location -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4">
                <!-- Title -->
                <h2 class="text-2xl font-medium text-[#232D42] font-['Inter'] mb-3">Asset By Location</h2>

                <!-- Divider -->
                <div class="w-full border-t-2 border-[#ECECEC] mb-4"></div>

                <!-- Location List -->
                <div class="space-y-[18px]">
                    <!-- ER Room -->
                    <div class="w-full">
                        <div class="flex justify-between items-center mb-2">
                            <div class="text-lg font-['Inter'] font-medium text-[#232D42]">ER Room</div>
                            <div class="text-lg font-['Inter'] font-medium text-[#232D42]">100%</div>
                        </div>
                        <div class="relative" x-data="{ showTooltip: false }">
                            <div class="w-full h-2 bg-[rgba(117,117,117,0.31)] rounded-[4px] cursor-pointer"
                                 @click="showTooltip = !showTooltip"
                                 @mouseenter="showTooltip = true"
                                 @mouseleave="showTooltip = false">
                                <div class="absolute h-2 left-0 w-[45%] bg-[#213268] rounded-[4px]"></div>
                            </div>
                            <!-- Tooltip -->
                            <div x-show="showTooltip"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                                 class="absolute -top-8 left-[calc(45%-20px)]">
                                <div class="bg-white shadow-lg rounded-lg px-3 py-2 text-center min-w-[40px]">
                                    <span class="text-[14px] font-['Inter'] font-medium text-[#344054]">45</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ICU Room -->
                    <div class="w-full">
                        <div class="flex justify-between items-center mb-2">
                            <div class="text-lg font-['Inter'] font-medium text-[#232D42]">ICU Room</div>
                            <div class="text-lg font-['Inter'] font-medium text-[#232D42]">100%</div>
                        </div>
                        <div class="relative" x-data="{ showTooltip: false }">
                            <div class="w-full h-2 bg-[rgba(117,117,117,0.31)] rounded-[4px] cursor-pointer"
                                 @click="showTooltip = !showTooltip"
                                 @mouseenter="showTooltip = true"
                                 @mouseleave="showTooltip = false">
                                <div class="absolute h-2 left-0 w-[45%] bg-[#213268] rounded-[4px]"></div>
                            </div>
                            <!-- Tooltip -->
                            <div x-show="showTooltip"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                                 class="absolute -top-8 left-[calc(45%-20px)]">
                                <div class="bg-white shadow-lg rounded-lg px-3 py-2 text-center min-w-[40px]">
                                    <span class="text-[14px] font-['Inter'] font-medium text-[#344054]">45</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Laboratory -->
                    <div class="w-full">
                        <div class="flex justify-between items-center mb-2">
                            <div class="text-lg font-['Inter'] font-medium text-[#232D42]">Laboratory</div>
                            <div class="text-lg font-['Inter'] font-medium text-[#232D42]">100%</div>
                        </div>
                        <div class="relative" x-data="{ showTooltip: false }">
                            <div class="w-full h-2 bg-[rgba(117,117,117,0.31)] rounded-[4px] cursor-pointer"
                                 @click="showTooltip = !showTooltip"
                                 @mouseenter="showTooltip = true"
                                 @mouseleave="showTooltip = false">
                                <div class="absolute h-2 left-0 w-[45%] bg-[#213268] rounded-[4px]"></div>
                            </div>
                            <!-- Tooltip -->
                            <div x-show="showTooltip"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                                 class="absolute -top-8 left-[calc(45%-20px)]">
                                <div class="bg-white shadow-lg rounded-lg px-3 py-2 text-center min-w-[40px]">
                                    <span class="text-[14px] font-['Inter'] font-medium text-[#344054]">45</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Radiology -->
                    <div class="w-full">
                        <div class="flex justify-between items-center mb-2">
                            <div class="text-lg font-['Inter'] font-medium text-[#232D42]">Radiology</div>
                            <div class="text-lg font-['Inter'] font-medium text-[#232D42]">100%</div>
                        </div>
                        <div class="relative" x-data="{ showTooltip: false }">
                            <div class="w-full h-2 bg-[rgba(117,117,117,0.31)] rounded-[4px] cursor-pointer"
                                 @click="showTooltip = !showTooltip"
                                 @mouseenter="showTooltip = true"
                                 @mouseleave="showTooltip = false">
                                <div class="absolute h-2 left-0 w-[45%] bg-[#213268] rounded-[4px]"></div>
                            </div>
                            <!-- Tooltip -->
                            <div x-show="showTooltip"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                                 class="absolute -top-8 left-[calc(45%-20px)]">
                                <div class="bg-white shadow-lg rounded-lg px-3 py-2 text-center min-w-[40px]">
                                    <span class="text-[14px] font-['Inter'] font-medium text-[#344054]">45</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="flex flex-col items-start p-6 gap-8 w-full bg-white rounded-lg shadow-lg mb-6">
        <!-- Header -->
        <div class="flex justify-between items-center w-full">
            <!-- Month and Year -->
            <div class="flex items-center p-3 gap-3 bg-white">
                <h2 id="currentMonth" class="text-2xl font-[Lato] font-black text-[#252525]"></h2>
                <span id="currentYear" class="text-2xl font-[Lato] font-light text-[#252525]"></span>
            </div>

            <!-- Navigation -->
            <div class="flex gap-2">
                <button onclick="changeMonth(-1)" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                </button>
                <button onclick="changeMonth(1)" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="flex flex-col gap-2 w-full">
            <!-- Days Header -->
            <div class="flex justify-between items-center pb-1 bg-white">
                <div class="flex-1 text-center text-xs font-[Lato] text-[#252525] opacity-50">Sun</div>
                <div class="flex-1 text-center text-xs font-[Lato] text-[#252525] opacity-50">Mon</div>
                <div class="flex-1 text-center text-xs font-[Lato] text-[#252525] opacity-50">Tue</div>
                <div class="flex-1 text-center text-xs font-[Lato] text-[#252525] opacity-50">Wed</div>
                <div class="flex-1 text-center text-xs font-[Lato] text-[#252525] opacity-50">Thu</div>
                <div class="flex-1 text-center text-xs font-[Lato] text-[#252525] opacity-50">Fri</div>
                <div class="flex-1 text-center text-xs font-[Lato] text-[#252525] opacity-50">Sat</div>
            </div>

            <!-- Calendar Days -->
            <div id="calendarDays" class="grid grid-cols-7 gap-0.5">
                <!-- Days will be inserted here by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card bg-white shadow-xl p-7 md:p-14 space-y-6">
        <!-- Title -->
        <h2 class="text-[32px] font-medium font-['Inter'] text-[#213268]">Recent Activity</h2>

        <!-- Activity List -->
        <div class="space-y-3.5">
            <!-- Activity Item 1 -->
            <div class="relative w-full h-[115px] bg-white shadow-md">
                <!-- Blue Line -->
                <div class="absolute left-0 top-0 w-2 h-full bg-[#25B1FF] rounded-r-[10px]"></div>

                <!-- Date & Time -->
                <div class="absolute left-[22px] top-[27px] space-y-3">
                    <p class="text-2xl font-medium font-['Inter'] text-black">04 Mar 25</p>
                    <p class="text-xl font-medium font-['Inter'] text-[#757575]">16:00</p>
                </div>

                <!-- Vertical Divider -->
                <div class="absolute left-[166px] top-0 h-full flex items-center">
                    <div class="w-px h-[83px] bg-[#CAC4D0]"></div>
                </div>

                <!-- Content -->
                <div class="absolute left-[197px] top-1/2 -translate-y-1/2 space-y-4">
                    <p class="text-lg font-normal font-['Inter'] text-[#757575]">Asset ID: 123456</p>
                    <p class="text-2xl font-normal font-['Inter'] text-black">You change status from <span class="text-[#DAAE0F]">Checked Out</span> to <span class="text-[#7CB60C]">Available</span></p>
                </div>
            </div>

            <!-- Activity Item 2 -->
            <div class="relative w-full h-[115px] bg-white shadow-md">
                <!-- Blue Line -->
                <div class="absolute left-0 top-0 w-2 h-full bg-[#25B1FF] rounded-r-[10px]"></div>

                <!-- Date & Time -->
                <div class="absolute left-[22px] top-[27px] space-y-3">
                    <p class="text-2xl font-medium font-['Inter'] text-black">03 Mar 25</p>
                    <p class="text-xl font-medium font-['Inter'] text-[#757575]">15:00</p>
                </div>

                <!-- Vertical Divider -->
                <div class="absolute left-[166px] top-0 h-full flex items-center">
                    <div class="w-px h-[83px] bg-[#CAC4D0]"></div>
                </div>

                <!-- Content -->
                <div class="absolute left-[197px] top-1/2 -translate-y-1/2 space-y-4">
                    <p class="text-lg font-normal font-['Inter'] text-[#757575]">Asset ID: 125384</p>
                    <p class="text-2xl font-normal font-['Inter'] text-black">Jhon Doe change status from <span class="text-[#DAAE0F]">Checked Out</span> to <span class="text-[#7CB60C]">Available</span></p>
                </div>
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

let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

const months = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

// Sample events data with different types
const events = {
    "2025-03-13": [
        { title: "Is this when the Superbowl is?", type: "info" },
        { title: "Run to the store to get some nachos", type: "info" },
        { title: "Deadline: Submit application!", type: "urgent" }
    ],
    "2025-03-15": [
        { title: "Team Meeting", type: "info" },
        { title: "Project Review", type: "urgent" }
    ],
    "2025-03-20": [
        { title: "Monthly Report Due", type: "urgent" },
        { title: "Lunch with Team", type: "info" }
    ]
};

function generateCalendar(month, year) {
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startingDay = firstDay.getDay();
    const totalDays = lastDay.getDate();

    // Update header
    document.getElementById('currentMonth').textContent = months[month];
    document.getElementById('currentYear').textContent = year;

    const calendarDays = document.getElementById('calendarDays');
    calendarDays.innerHTML = '';

    // Previous month's days
    const prevMonthLastDay = new Date(year, month, 0).getDate();
    for (let i = startingDay - 1; i >= 0; i--) {
        const day = prevMonthLastDay - i;
        calendarDays.innerHTML += `
            <div class="p-2 min-h-[104px] bg-white border border-gray-100">
                <div class="text-xs font-[Lato] text-center text-[#252525] opacity-50">${day}</div>
            </div>
        `;
    }

    // Current month's days
    for (let day = 1; day <= totalDays; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const isToday = day === currentDate.getDate() &&
                       month === currentDate.getMonth() &&
                       year === currentDate.getFullYear();

        let dayEvents = '';
        if (events[dateStr]) {
            dayEvents = events[dateStr].map(event => {
                const bgColor = event.type === 'info' ? 'bg-[#E5F6FF]' :
                              event.type === 'urgent' ? 'bg-[#FFE5E5]' :
                              'bg-[#D2F0FF]';
                return `<div class="px-2 py-1 ${bgColor} text-xs font-[Lato] mb-1 rounded">${event.title}</div>`;
            }).join('');

            // Add "view more" link if there are more than 3 events
            if (events[dateStr].length > 3) {
                dayEvents += `
                    <div class="text-right">
                        <a href="#" class="text-[#015DE7] text-xs">view more</a>
                    </div>
                `;
            }
        }

        calendarDays.innerHTML += `
            <div class="p-2 min-h-[104px] ${isToday ? 'bg-gray-100' : 'bg-white'} border border-gray-100">
                <div class="text-sm font-[Lato] ${isToday ? 'font-bold' : ''} mb-2">${day}</div>
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
            <div class="p-2 min-h-[104px] bg-white border border-gray-100">
                <div class="text-xs font-[Lato] text-center text-[#252525] opacity-50">${day}</div>
            </div>
        `;
    }
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
    generateCalendar(currentMonth, currentYear);
}

// Initialize calendar
generateCalendar(currentMonth, currentYear);
</script>
@endsection
