<div id="sidebar-container" class="w-[250px] h-screen bg-white rounded-r-[20px] flex flex-col relative overflow-hidden">
    <!-- Header with Logo -->
    <div class="h-[72px] relative">
        <div class="w-full h-[72px] bg-white shadow-[0_4px_8.1px_2px_#56C5F1] rounded-tr-[20px]">
            <img src="{{ asset('images/Logo_RS_UMMI.png') }}" alt="Logo RS UMMI"
                class="w-[180px] h-[47px] absolute left-[13px] top-[12px]">
        </div>
    </div>

    <!-- Menu Items Container with Scroll -->
    <div class="flex-1 overflow-y-auto overflow-x-hidden">
        <!-- Menu Items -->
        <div id="menu-container" class="flex flex-col gap-[5px] mt-5 transition-all duration-500 ease-in-out px-[13px]">
            @php
                // Define consistent classes
                $menuItemClass = "w-full bg-white transform transition-all duration-300 ease-in-out group hover:translate-x-2 menu-item";
                $menuLinkClass = "h-[41px] rounded-[8px] flex items-center transition-all duration-300 ease-in-out hover:bg-[#56C5F1]/20";
                $iconWrapperClass = "w-6 h-6 ml-5";
                $menuTextClass = "ml-[20px] font-['Poppins'] text-[16px] text-[#757575] font-medium";
                $submenuTextClass = "font-['Poppins'] text-[14px] text-[#757575] font-medium";
                $submenuLinkClass = "h-[41px] flex items-center hover:bg-[#56C5F1]/20 rounded-[8px] px-4";
            @endphp

            <!-- Dashboard -->
            <div class="{{ $menuItemClass }}">
                <a href="{{ route('dashboard') }}" class="block dashboard-link" data-menu="dashboard">
                    <div class="{{ $menuLinkClass }} {{ Request::routeIs('dashboard') ? 'bg-[#56C5F1]/20' : '' }}">
                        <div class="{{ $iconWrapperClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="#757575">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </svg>
                        </div>
                        <span class="{{ $menuTextClass }}">Dashboard</span>
                    </div>
                </a>
            </div>

            <!-- Master Data -->
            <div class="{{ $menuItemClass }}">
                <button class="w-full focus:outline-none toggle-menu" data-menu="masterdata">
                    <div class="{{ $menuLinkClass }} menu-header">
                        <div class="{{ $iconWrapperClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="#757575">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <span class="{{ $menuTextClass }}">Master Data</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-6 h-6 ml-auto mr-4 transform transition-transform duration-200 menu-arrow"
                            fill="none" viewBox="0 0 24 24" stroke="#757575">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>
                <!-- Sub Menu -->
                <div class="ml-[41px] mt-1 overflow-hidden submenu" data-parent="masterdata"
                    style="max-height: 0; opacity: 0; transition: all 0.3s ease-out;">
                    <a href="{{ route('asset-categories') }}" class="block">
                        <div
                            class="{{ $submenuLinkClass }} {{ Request::routeIs('asset-categories') ? 'bg-[#56C5F1]/20' : '' }}">
                            <span class="{{ $submenuTextClass }}">Sub Categories</span>
                        </div>
                    </a>
                    <a href="{{ route('brands') }}" class="block">
                        <div class="{{ $submenuLinkClass }} {{ Request::routeIs('brands') ? 'bg-[#56C5F1]/20' : '' }}">
                            <span class="{{ $submenuTextClass }}">Brands</span>
                        </div>
                    </a>
                    <a href="{{ route('buildings') }}" class="block">
                        <div class="{{ $submenuLinkClass }} {{ Request::routeIs('buildings') ? 'bg-[#56C5F1]/20' : '' }}">
                            <span class="{{ $submenuTextClass }}">Buildings</span>
                        </div>
                    </a>
                    <a href="{{ route('rooms') }}" class="block">
                        <div class="{{ $submenuLinkClass }} {{ Request::routeIs('rooms') ? 'bg-[#56C5F1]/20' : '' }}">
                            <span class="{{ $submenuTextClass }}">Rooms</span>
                        </div>
                    </a>
                    <a href="{{ route('vendor') }}" class="block">
                        <div class="{{ $submenuLinkClass }} {{ Request::routeIs('vendor') ? 'bg-[#56C5F1]/20' : '' }}">
                            <span class="{{ $submenuTextClass }}">Vendor</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Asset (New Menu) -->
            <div class="{{ $menuItemClass }}">
                <button class="w-full focus:outline-none toggle-menu" data-menu="asset">
                    <div class="{{ $menuLinkClass }} menu-header">
                        <div class="{{ $iconWrapperClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="#757575">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <span class="{{ $menuTextClass }}">Asset</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-6 h-6 ml-auto mr-4 transform transition-transform duration-200 menu-arrow"
                            fill="none" viewBox="0 0 24 24" stroke="#757575">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>
                <!-- Sub Menu -->
                <div class="ml-[41px] mt-1 overflow-hidden submenu" data-parent="asset"
                    style="max-height: 0; opacity: 0; transition: all 0.3s ease-out;">
                    <a href="{{ route('asset-master') }}" class="block">
                        <div
                            class="{{ $submenuLinkClass }} {{ Request::routeIs('asset-master') ? 'bg-[#56C5F1]/20' : '' }}">
                            <span class="{{ $submenuTextClass }}">Master Asset</span>
                        </div>
                    </a>
                    <a href="{{ route('asset-unit') }}" class="block">
                        <div
                            class="{{ $submenuLinkClass }} {{ Request::routeIs('asset-unit') ? 'bg-[#56C5F1]/20' : '' }}">
                            <span class="{{ $submenuTextClass }}">Unit Asset</span>
                        </div>
                    </a>
                    <a href="{{ route('asset-documents') }}" class="block">
                        <div
                            class="{{ $submenuLinkClass }} {{ Request::routeIs('asset-documents') ? 'bg-[#56C5F1]/20' : '' }}">
                            <span class="{{ $submenuTextClass }}">Asset Documents</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Calibration -->
            <div class="{{ $menuItemClass }}">
                <a href="{{ route('calibration') }}" class="block calibration-link" data-menu="calibration">
                    <div class="{{ $menuLinkClass }} {{ Request::routeIs('calibration') ? 'bg-[#56C5F1]/20' : '' }}">
                        <div class="{{ $iconWrapperClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="#757575">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="{{ $menuTextClass }}">Calibration</span>
                    </div>
                </a>
            </div>

            <!-- Maintenance -->
            <div class="{{ $menuItemClass }}">
                <a href="{{ route('maintenance') }}" class="block maintenance-link" data-menu="maintenance">
                    <div class="{{ $menuLinkClass }} {{ Request::routeIs('maintenance') ? 'bg-[#56C5F1]/20' : '' }}">
                        <div class="{{ $iconWrapperClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="#757575">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="{{ $menuTextClass }}">Maintenance</span>
                    </div>
                </a>
            </div>

            <!-- Complaint & Repair -->
            <div class="{{ $menuItemClass }}">
                <a href="{{ route('complaint.index') }}" class="block complaint-link" data-menu="complaint">
                    <div class="{{ $menuLinkClass }} {{ Request::routeIs('complaint.*') ? 'bg-[#56C5F1]/20' : '' }}">
                        <div class="{{ $iconWrapperClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="#757575">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <span class="{{ $menuTextClass }}">Complaint & Repair</span>
                    </div>
                </a>
            </div>

            <!-- Procurement -->
            <div class="{{ $menuItemClass }}">
                <button class="w-full focus:outline-none toggle-menu" data-menu="procurement">
                    <div class="{{ $menuLinkClass }} menu-header">
                        <div class="{{ $iconWrapperClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="#757575">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 16l-4-4m0 0l4-4m-4 4h10M7 16v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <span class="{{ $menuTextClass }}">Procurement</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-6 h-6 ml-auto mr-4 transform transition-transform duration-200 menu-arrow"
                            fill="none" viewBox="0 0 24 24" stroke="#757575">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>
                <!-- Sub Menu -->
                <div class="ml-[41px] mt-1 overflow-hidden submenu" data-parent="procurement"
                    style="max-height: 0; opacity: 0; transition: all 0.3s ease-out;">
                    @php
                        $procurementSubmenuItems = [
                            ['route' => 'procurement.request', 'name' => 'Request'],
                            ['route' => 'procurement.price-comparison', 'name' => 'Price Comparison'],
                            ['route' => 'procurement.purchase-order', 'name' => 'Purchase Order'],
                            ['route' => 'procurement.receipt', 'name' => 'Receipt'],
                        ];
                    @endphp

                    @foreach($procurementSubmenuItems as $item)
                        <a href="{{ route($item['route']) }}" class="block">
                            <div
                                class="{{ $submenuLinkClass }} {{ Request::routeIs($item['route']) ? 'bg-[#56C5F1]/20' : '' }}">
                                <span class="{{ $submenuTextClass }}">{{ $item['name'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Report -->
            <div class="{{ $menuItemClass }}">
                <button class="w-full focus:outline-none toggle-menu" data-menu="report">
                    <div class="{{ $menuLinkClass }} menu-header">
                        <div class="{{ $iconWrapperClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="#757575">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="{{ $menuTextClass }}">Report</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-6 h-6 ml-auto mr-4 transform transition-transform duration-200 menu-arrow"
                            fill="none" viewBox="0 0 24 24" stroke="#757575">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>
                <!-- Sub Menu -->
                <div class="ml-[41px] mt-1 overflow-hidden submenu" data-parent="report"
                    style="max-height: 0; opacity: 0; transition: all 0.3s ease-out;">
                    @php
                        $reportSubmenuItems = [
                            ['route' => 'report.opname', 'name' => 'Opname Report'],
                            ['route' => 'report.finance', 'name' => 'Finance Report'],
                            ['route' => 'report.depreciation', 'name' => 'Depreciation Report'],
                        ];
                    @endphp

                    @foreach($reportSubmenuItems as $item)
                        <a href="{{ route($item['route']) }}" class="block">
                            <div
                                class="{{ $submenuLinkClass }} {{ Request::routeIs($item['route']) ? 'bg-[#56C5F1]/20' : '' }}">
                                <span class="{{ $submenuTextClass }}">{{ $item['name'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Account -->
            <div class="{{ $menuItemClass }}">
                <button class="w-full focus:outline-none toggle-menu" data-menu="account">
                    <div class="{{ $menuLinkClass }} menu-header">
                        <div class="{{ $iconWrapperClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="#757575">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="{{ $menuTextClass }}">Account</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-6 h-6 ml-auto mr-4 transform transition-transform duration-200 menu-arrow"
                            fill="none" viewBox="0 0 24 24" stroke="#757575">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>
                <!-- Sub Menu -->
                <div class="ml-[41px] mt-1 overflow-hidden submenu" data-parent="account"
                    style="max-height: 0; opacity: 0; transition: all 0.3s ease-out;">
                    <a href="{{ route('user') }}" class="block">
                        <div class="{{ $submenuLinkClass }} {{ Request::routeIs('user') ? 'bg-[#56C5F1]/20' : '' }}">
                            <span class="{{ $submenuTextClass }}">User</span>
                        </div>
                    </a>
                    <a href="{{ route('roles') }}" class="block">
                        <div class="{{ $submenuLinkClass }} {{ Request::routeIs('roles') ? 'bg-[#56C5F1]/20' : '' }}">
                            <span class="{{ $submenuTextClass }}">Role</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout at bottom -->
    <div class="w-full px-[13px] py-5 bg-white">
        <div class="{{ $menuItemClass }}">
            <form action="{{ route('logout') }}" method="POST" class="block">
                @csrf
                <button type="submit" class="w-full text-left">
                    <div class="{{ $menuLinkClass }}">
                        <div class="{{ $iconWrapperClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="#757575">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <span class="{{ $menuTextClass }}">Log Out</span>
                    </div>
                </button>
            </form>
        </div>
    </div>

    <!-- JavaScript for sidebar functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = {
                // Store DOM references
                elements: {
                    toggleButtons: document.querySelectorAll('.toggle-menu'),
                    menuItems: document.querySelectorAll('.menu-item'),
                    submenus: document.querySelectorAll('.submenu'),
                    arrows: document.querySelectorAll('.menu-arrow'),
                    menuHeaders: document.querySelectorAll('.menu-header'),
                    directLinks: document.querySelectorAll('#menu-container > .menu-item > a:not(.toggle-menu)'),
                    allLinks: document.querySelectorAll('#sidebar-container a')
                },

                // Helper methods
                updateElementStyle: function (element, isActive, selector, activeClass, inactiveClass) {
                    if (!element) return;
                    const target = selector ? element.querySelector(selector) : element;
                    if (!target) return;

                    if (isActive) {
                        inactiveClass && target.classList.remove(inactiveClass);
                        activeClass && target.classList.add(activeClass);
                    } else {
                        activeClass && target.classList.remove(activeClass);
                        inactiveClass && target.classList.add(inactiveClass);
                    }
                },

                // Central method to close all submenus
                closeAllSubmenus: function () {
                    this.elements.submenus.forEach(submenu => {
                        submenu.style.maxHeight = '0';
                        submenu.style.opacity = '0';
                    });

                    this.elements.arrows.forEach(arrow => {
                        arrow.classList.remove('rotate-90');
                        if (arrow.querySelector('path')) {
                            arrow.querySelector('path').setAttribute('stroke', '#757575');
                        }
                    });

                    this.elements.menuHeaders.forEach(header => {
                        header.classList.remove('bg-[#56C5F1]/20');
                        this.updateElementStyle(header, false, 'span', 'text-[#213268]', 'text-[#757575]');

                        const svg = header.querySelector('.w-6.h-6:not(.menu-arrow)');
                        if (svg && svg.querySelector('path')) {
                            svg.querySelector('path').setAttribute('stroke', '#757575');
                        }
                    });

                    this.elements.menuItems.forEach(item => {
                        item.classList.remove('opacity-75', 'scale-[0.98]');
                    });

                    localStorage.removeItem('activeMenu');
                },

                // Method to open a specific submenu
                openSubmenu: function (menuName) {
                    const targetSubmenu = document.querySelector(`.submenu[data-parent="${menuName}"]`);
                    const targetArrow = document.querySelector(`.toggle-menu[data-menu="${menuName}"] .menu-arrow`);
                    const targetHeader = document.querySelector(`.toggle-menu[data-menu="${menuName}"] .menu-header`);

                    this.closeAllSubmenus();

                    this.elements.menuItems.forEach(item => {
                        item.classList.add('opacity-75', 'scale-[0.98]');
                    });

                    setTimeout(() => {
                        if (targetSubmenu) {
                            targetSubmenu.style.maxHeight = targetSubmenu.scrollHeight + 'px';
                            targetSubmenu.style.opacity = '1';
                        }

                        if (targetArrow) {
                            targetArrow.classList.add('rotate-90');
                            if (targetArrow.querySelector('path')) {
                                targetArrow.querySelector('path').setAttribute('stroke', '#213268');
                            }
                        }

                        if (targetHeader) {
                            targetHeader.classList.add('bg-[#56C5F1]/20');
                            this.updateElementStyle(targetHeader, true, 'span', 'text-[#213268]', 'text-[#757575]');

                            const svg = targetHeader.querySelector('.w-6.h-6:not(.menu-arrow)');
                            if (svg && svg.querySelector('path')) {
                                svg.querySelector('path').setAttribute('stroke', '#213268');
                            }
                        }
                    }, 10);

                    if (targetSubmenu) {
                        const menuItem = targetSubmenu.closest('.menu-item');
                        if (menuItem) {
                            menuItem.classList.remove('opacity-75', 'scale-[0.98]');
                        }
                    }

                    localStorage.setItem('activeMenu', menuName);
                },

                // Toggle a menu (open if closed, close if open)
                toggleMenu: function (menuName) {
                    const targetSubmenu = document.querySelector(`.submenu[data-parent="${menuName}"]`);
                    const isOpen = (targetSubmenu && targetSubmenu.style.maxHeight !== '0px' && targetSubmenu.style.maxHeight !== '');
                    isOpen ? this.closeAllSubmenus() : this.openSubmenu(menuName);
                },

                // Initialize sidebar functionality
                init: function () {
                    const self = this;

                    // Toggle button click events
                    this.elements.toggleButtons.forEach(button => {
                        button.addEventListener('click', function () {
                            self.toggleMenu(this.getAttribute('data-menu'));
                        });
                    });

                    // Direct links click handling
                    this.elements.directLinks.forEach(link => {
                        link.addEventListener('click', function () {
                            self.closeAllSubmenus();
                            const linkDiv = this.querySelector('div');
                            if (linkDiv) {
                                linkDiv.classList.add('bg-[#56C5F1]/20');
                                self.updateElementStyle(linkDiv, true, 'span', 'text-[#213268]', 'text-[#757575]');

                                const svg = linkDiv.querySelector('.w-6.h-6');
                                if (svg && svg.querySelector('path')) {
                                    svg.querySelector('path').setAttribute('stroke', '#213268');
                                }
                            }
                        });
                    });

                    // Update active direct links from routing
                    this.elements.directLinks.forEach(link => {
                        const linkDiv = link.querySelector('div');
                        if (linkDiv && linkDiv.classList.contains('bg-[#56C5F1]/20')) {
                            self.updateElementStyle(linkDiv, true, 'span', 'text-[#213268]', 'text-[#757575]');

                            const svg = linkDiv.querySelector('.w-6.h-6');
                            if (svg && svg.querySelector('path')) {
                                svg.querySelector('path').setAttribute('stroke', '#213268');
                            }
                        }
                    });

                    // Submenu links click handling
                    document.querySelectorAll('.submenu a').forEach(link => {
                        link.addEventListener('click', function () {
                            self.closeAllSubmenus();

                            // Highlight parent menu
                            const submenu = this.closest('.submenu');
                            if (submenu) {
                                const menuName = submenu.getAttribute('data-parent');
                                const menuHeader = document.querySelector(`.toggle-menu[data-menu="${menuName}"] .menu-header`);

                                if (menuHeader) {
                                    menuHeader.classList.add('bg-[#56C5F1]/20');
                                    self.updateElementStyle(menuHeader, true, 'span', 'text-[#213268]', 'text-[#757575]');

                                    const svg = menuHeader.querySelector('.w-6.h-6:not(.menu-arrow)');
                                    if (svg && svg.querySelector('path')) {
                                        svg.querySelector('path').setAttribute('stroke', '#213268');
                                    }
                                }
                            }

                            // Highlight clicked submenu link
                            const linkDiv = this.querySelector('div');
                            if (linkDiv) {
                                linkDiv.classList.add('bg-[#56C5F1]/20');
                                self.updateElementStyle(linkDiv, true, 'span', 'text-[#213268]', 'text-[#757575]');
                            }
                        });
                    });

                    // Update active submenu links from routing
                    document.querySelectorAll('.submenu a div').forEach(div => {
                        if (div.classList.contains('bg-[#56C5F1]/20')) {
                            self.updateElementStyle(div, true, 'span', 'text-[#213268]', 'text-[#757575]');

                            const submenu = div.closest('.submenu');
                            if (submenu) {
                                const menuName = submenu.getAttribute('data-parent');
                                localStorage.setItem('activeMenu', menuName);
                            }
                        }
                    });

                    // Navigation tracking
                    this.elements.allLinks.forEach(link => {
                        link.addEventListener('click', function (e) {
                            const href = this.getAttribute('href');
                            if (!href || href.startsWith('#') || href.includes('login')) return;
                            sessionStorage.setItem('sidebarNavigation', 'true');
                        });
                    });

                    // Restore active menu from localStorage
                    const storedActiveMenu = localStorage.getItem('activeMenu');
                    if (storedActiveMenu) {
                        this.openSubmenu(storedActiveMenu);
                    }

                    // Make methods available globally
                    window.toggleSidebarMenu = (menuName) => self.toggleMenu(menuName);
                    window.closeAllSidebarMenus = () => self.closeAllSubmenus();
                }
            };

            // Initialize the sidebar
            sidebar.init();

            // === MODAL INITIALIZATION ===
            function reinitializeModals() {
                console.log('Reinitializing modals after sidebar navigation');

                // DOM elements
                const modals = document.querySelectorAll('[id$="Modal"]');
                const closeButtons = document.querySelectorAll('.close-modal');

                // Helper functions for modal operations
                if (typeof window.openModal !== 'function') {
                    window.openModal = function (modal, content) {
                        modal.classList.remove('hidden');
                        setTimeout(() => {
                            content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                            content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                        }, 10);
                    };
                }

                if (typeof window.closeModal !== 'function') {
                    window.closeModal = function (modal, content) {
                        content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                        content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                        setTimeout(() => {
                            modal.classList.add('hidden');
                        }, 300);
                    };
                }

                // Initialize modal triggers
                function initModalTrigger(selector, modalIdFunc) {
                    document.querySelectorAll(selector).forEach(button => {
                        if (!button.hasAttribute('data-modal-initialized')) {
                            button.addEventListener('click', function (e) {
                                e.preventDefault();
                                const modalId = modalIdFunc(this);
                                const modal = document.getElementById(modalId);
                                const content = modal?.querySelector(`#${modalId}Content`);
                                if (modal && content) openModal(modal, content);
                            });
                            button.setAttribute('data-modal-initialized', 'true');
                        }
                    });
                }

                // Initialize standard modal buttons
                initModalTrigger('[id$="Btn"]', btn => btn.id.replace('Btn', 'Modal'));

                // Initialize specialized buttons
                initModalTrigger('.edit-brand-btn', () => 'editBrandModal');
                initModalTrigger('.delete-brand-btn', () => 'deleteBrandModal');

                // Initialize close buttons
                closeButtons.forEach(button => {
                    if (!button.hasAttribute('data-modal-initialized')) {
                        button.addEventListener('click', function (e) {
                            e.preventDefault();
                            const modal = button.closest('[id$="Modal"]');
                            const content = modal.querySelector('[id$="ModalContent"]');
                            closeModal(modal, content);
                        });
                        button.setAttribute('data-modal-initialized', 'true');
                    }
                });

                // Initialize background click to close
                modals.forEach(modal => {
                    if (!modal.hasAttribute('data-modal-initialized')) {
                        modal.addEventListener('click', function (e) {
                            if (e.target === modal) {
                                const content = modal.querySelector('[id$="ModalContent"]');
                                closeModal(modal, content);
                            }
                        });
                        modal.setAttribute('data-modal-initialized', 'true');
                    }
                });
            }

            // Check if page loaded after sidebar navigation
            window.addEventListener('DOMContentLoaded', function () {
                if (sessionStorage.getItem('sidebarNavigation') === 'true') {
                    sessionStorage.removeItem('sidebarNavigation');
                    reinitializeModals();
                }
            });

            // Watch for content changes
            const contentArea = document.querySelector('main');
            if (contentArea) {
                const observer = new MutationObserver(reinitializeModals);
                observer.observe(contentArea, {
                    childList: true,
                    subtree: true
                });
            }

            // Make function available globally
            window.reinitializeModalsAfterNavigation = reinitializeModals;
        });
    </script>
</div>
