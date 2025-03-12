<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div x-data="{ activeMenu: null }" class="w-[250px] h-screen bg-white rounded-r-[20px] flex flex-col relative overflow-y-auto">
    <!-- Header with Logo -->
    <div class="h-[72px] relative">
        <div class="w-full h-[72px] bg-white shadow-[0_4px_8.1px_2px_#56C5F1] rounded-tr-[20px]">
            <img src="{{ asset('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI" class="w-[180px] h-[47px] absolute left-[13px] top-[12px]">
        </div>
    </div>

    <!-- Menu Items -->
    <div class="flex flex-col gap-[5px] mt-5">
        <!-- Dashboard -->
        <div class="h-[52px] w-full bg-white">
            <a href="{{ route('dashboard') }}" class="block">
                <div class="mx-[13px] h-[41px] mt-[5px] rounded-[8px] flex items-center hover:bg-[#56C5F1]/20 {{ Request::routeIs('dashboard') ? 'bg-[#56C5F1]/20' : '' }}">
                    <div class="w-6 h-6 ml-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="#757575">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                    </div>
                    <span class="ml-[20px] font-['Public_Sans'] text-[16px] text-[#757575] font-medium">Dashboard</span>
                </div>
            </a>
        </div>

        <!-- Procurement -->
        <div class="w-full bg-white">
            <button @click="activeMenu = (activeMenu === 'procurement') ? null : 'procurement'" class="w-full">
                <div class="mx-[13px] h-[41px] mt-[5px] rounded-[8px] flex items-center hover:bg-[#56C5F1]/20">
                    <div class="w-6 h-6 ml-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="#757575">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 16l-4-4m0 0l4-4m-4 4h10M7 16v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <span class="ml-[20px] font-['Public_Sans'] text-[16px] text-[#757575] font-medium">Procurement</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 ml-auto mr-4 transform transition-transform duration-200"
                         :class="{ 'rotate-90': activeMenu === 'procurement' }"
                         fill="none" viewBox="0 0 24 24" stroke="#757575">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>
            <!-- Sub Menu -->
            <div x-show="activeMenu === 'procurement'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-y-0 -translate-y-2 origin-top"
                 x-transition:enter-end="opacity-100 transform scale-y-100 translate-y-0 origin-top"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-y-100 translate-y-0 origin-top"
                 x-transition:leave-end="opacity-0 transform scale-y-0 -translate-y-2 origin-top"
                 class="ml-[54px] mt-1 overflow-hidden">
                <!-- Sub menu items here -->
            </div>
        </div>

        <!-- Management Asset -->
        <div class="w-full bg-white">
            <button @click="activeMenu = (activeMenu === 'asset') ? null : 'asset'" class="w-full">
                <div class="mx-[13px] h-[41px] mt-[5px] rounded-[8px] flex items-center hover:bg-[#56C5F1]/20">
                    <div class="w-6 h-6 ml-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="#757575">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <span class="ml-[20px] font-['Public_Sans'] text-[16px] text-[#757575] font-medium">Asset</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 ml-auto mr-4 transform transition-transform duration-200"
                         :class="{ 'rotate-90': activeMenu === 'asset' }"
                         fill="none" viewBox="0 0 24 24" stroke="#757575">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>
            <!-- Sub Menu -->
            <div x-show="activeMenu === 'asset'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-y-0 -translate-y-2 origin-top"
                 x-transition:enter-end="opacity-100 transform scale-y-100 translate-y-0 origin-top"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-y-100 translate-y-0 origin-top"
                 x-transition:leave-end="opacity-0 transform scale-y-0 -translate-y-2 origin-top"
                 class="ml-[54px] mt-1 overflow-hidden">
                <!-- Asset Categories -->
                <a href="#" class="block">
                    <div class="h-[41px] flex items-center hover:bg-[#56C5F1]/20 rounded-[8px] px-4">
                        <span class="font-['Public_Sans'] text-[14px] text-[#757575] font-medium">Asset Categories</span>
                    </div>
                </a>
                <!-- View Asset -->
                <a href="#" class="block">
                    <div class="h-[41px] flex items-center hover:bg-[#56C5F1]/20 rounded-[8px] px-4">
                        <span class="font-['Public_Sans'] text-[14px] text-[#757575] font-medium">View Asset</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Location -->
        <div class="h-[52px] w-full bg-white">
            <div class="mx-[13px] h-[41px] mt-[5px] rounded-[8px] flex items-center hover:bg-[#56C5F1]/20">
                <div class="w-6 h-6 ml-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="#757575">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <span class="ml-[20px] font-['Public_Sans'] text-[16px] text-[#757575] font-medium">Location</span>
            </div>
        </div>

        <!-- Vendor -->
        <div class="h-[52px] w-full bg-white">
            <div class="mx-[13px] h-[41px] mt-[5px] rounded-[8px] flex items-center hover:bg-[#56C5F1]/20">
                <div class="w-6 h-6 ml-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="#757575">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span class="ml-[20px] font-['Public_Sans'] text-[16px] text-[#757575] font-medium">Vendor</span>
            </div>
        </div>

        <!-- Department -->
        <div class="h-[52px] w-full bg-white">
            <div class="mx-[13px] h-[41px] mt-[5px] rounded-[8px] flex items-center hover:bg-[#56C5F1]/20">
                <div class="w-6 h-6 ml-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="#757575">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span class="ml-[20px] font-['Public_Sans'] text-[16px] text-[#757575] font-medium">Department</span>
            </div>
        </div>

        <!-- Report -->
        <div class="h-[52px] w-full bg-white">
            <div class="mx-[13px] h-[41px] mt-[5px] rounded-[8px] flex items-center hover:bg-[#56C5F1]/20">
                <div class="w-6 h-6 ml-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="#757575">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <span class="ml-[20px] font-['Public_Sans'] text-[16px] text-[#757575] font-medium">Report</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 ml-auto mr-4" fill="none" viewBox="0 0 24 24" stroke="#757575">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </div>

        <!-- Account -->
        <div class="w-full bg-white">
            <button @click="activeMenu = (activeMenu === 'account') ? null : 'account'" class="w-full">
                <div class="mx-[13px] h-[41px] mt-[5px] rounded-[8px] flex items-center hover:bg-[#56C5F1]/20">
                    <div class="w-6 h-6 ml-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="#757575">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="ml-[20px] font-['Public_Sans'] text-[16px] text-[#757575] font-medium">Account</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 ml-auto mr-4 transform transition-transform duration-200"
                         :class="{ 'rotate-90': activeMenu === 'account' }"
                         fill="none" viewBox="0 0 24 24" stroke="#757575">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>
            <!-- Sub Menu -->
            <div x-show="activeMenu === 'account'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-y-0 -translate-y-2 origin-top"
                 x-transition:enter-end="opacity-100 transform scale-y-100 translate-y-0 origin-top"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-y-100 translate-y-0 origin-top"
                 x-transition:leave-end="opacity-0 transform scale-y-0 -translate-y-2 origin-top"
                 class="ml-[54px] mt-1 overflow-hidden">
                <!-- Employee -->
                <a href="#" class="block">
                    <div class="h-[41px] flex items-center hover:bg-[#56C5F1]/20 rounded-[8px] px-4">
                        <span class="font-['Public_Sans'] text-[14px] text-[#757575] font-medium">Employee</span>
                    </div>
                </a>
                <!-- User -->
                <a href="#" class="block">
                    <div class="h-[41px] flex items-center hover:bg-[#56C5F1]/20 rounded-[8px] px-4">
                        <span class="font-['Public_Sans'] text-[14px] text-[#757575] font-medium">User</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Logout at bottom -->
    <div class="absolute bottom-16 w-full">
        <div class="h-[52px] w-full bg-white">
            <div class="mx-[13px] h-[41px] mt-[5px] rounded-[8px] flex items-center hover:bg-[#56C5F1]/20">
                <div class="w-6 h-6 ml-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="#757575">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </div>
                <span class="ml-[20px] font-['Public_Sans'] text-[16px] text-[#757575] font-medium">Log Out</span>
            </div>
        </div>
    </div>
</div>
