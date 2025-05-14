<nav class="bg-[#213268] h-[72px] flex items-center justify-between px-6">
    <!-- Search Bar -->
    <div class="flex items-center ml-12 lg:ml-0">
        <div class="relative">
            <input type="text" placeholder="Search..."
                   class="w-[180px] md:w-[240px] h-[40px] pl-10 pr-4 rounded-lg bg-white/10 text-white placeholder-white/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </div>

    <!-- Right Side -->
    <div class="flex items-center gap-2 md:gap-4">
        <!-- Notification -->
        <div class="relative hidden md:block">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>


        <!-- User Profile - Now clickable -->
        <a href="{{ route('profile') }}" class="flex items-center gap-2 md:gap-3 cursor-pointer hover:opacity-90 transition-opacity">
            <img src="https://ui-avatars.com/api/?name=Austin+Robertson" alt="User" class="w-8 h-8 md:w-10 md:h-10 rounded-full">
            <div class="text-white hidden md:block">
                <p class="text-sm font-medium">Austin Robertson</p>
                <p class="text-xs opacity-60">Super Admin</p>
            </div>
        </a>
    </div>
</nav>
