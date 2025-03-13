@extends('Layout.app')

@section('title', 'Location')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Location Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">BUILDING</h1>

                    <!-- Button Add Building -->
                    <button id="addBuildingBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span class="text-base">Add Building</span>
                    </button>
                </div>

                <!-- Building Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[15%]">Building_Id</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Building Name</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Address</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">City</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[88px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">001</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">Building A</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">Jl. Example No. 123</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">Jakarta</td>
                                <td class="p-3 border-t border-[#EEF1F4]">
                                    <div class="flex justify-center gap-2">
                                        <button class="text-[#3D3D3D] hover:text-[#213268] edit-building-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button class="text-[#3D3D3D] hover:text-red-500 delete-building-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <button class="text-[#3D3D3D] hover:text-[#213268]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex gap-2">
                        <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Prev
                        </button>
                        <div class="flex gap-2">
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">1</button>
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">2</button>
                            <button class="w-8 h-8 flex items-center justify-center bg-[#213268] rounded text-white text-sm">3</button>
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">4</button>
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">5</button>
                        </div>
                        <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">
                        10 per page
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Section -->
    <div class="card bg-base-100 shadow-xl mt-6">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">ROOM</h1>

                    <!-- Button Add Room -->
                    <button id="addRoomBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span class="text-base">Add Room</span>
                    </button>
                </div>

                <!-- Room Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[15%]">Room_Id</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Room Name</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Building Name</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Floor</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[88px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">R001</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">NICU Room</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">Building A</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">1st Floor</td>
                                <td class="p-3 border-t border-[#EEF1F4]">
                                    <div class="flex justify-center gap-2"></div>
                                        <button class="text-[#3D3D3D] hover:text-[#213268] edit-room-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button class="text-[#3D3D3D] hover:text-red-500 delete-room-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <button class="text-[#3D3D3D] hover:text-[#213268]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex gap-2">
                        <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Prev
                        </button>
                        <div class="flex gap-2">
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">1</button>
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">2</button>
                            <button class="w-8 h-8 flex items-center justify-center bg-[#213268] rounded text-white text-sm">3</button>
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">4</button>
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">5</button>
                        </div>
                        <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">
                        10 per page
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Building Modal -->
<div id="addBuildingModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300" id="buildingModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">ADD BUILDING</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <div class="space-y-4 max-w-[400px] mx-auto">
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Building Name</label>
                            <input type="text" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200" placeholder="Type here">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Address</label>
                            <input type="text" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200" placeholder="Type here">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">City</label>
                            <input type="text" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200" placeholder="Type here">
                        </div>
                        <button class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Building Modal -->
<div id="editBuildingModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300" id="editBuildingModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">EDIT BUILDING</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <div class="space-y-4 max-w-[400px] mx-auto">
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Building Name</label>
                            <input type="text" id="editBuildingName" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200" placeholder="Type here">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Address</label>
                            <input type="text" id="editBuildingAddress" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200" placeholder="Type here">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">City</label>
                            <input type="text" id="editBuildingCity" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200" placeholder="Type here">
                        </div>
                        <button class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Building Modal -->
<div id="deleteBuildingModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300" id="deleteBuildingModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">DELETE BUILDING</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <div class="space-y-6 max-w-[400px] mx-auto">
                        <div class="flex flex-col items-center">
                            <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-base text-gray-600 text-center">Are you sure you want to delete this building? This action cannot be undone.</p>
                        </div>
                        <div class="flex gap-3">
                            <button class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                Cancel
                            </button>
                            <button class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Room Modal -->
<div id="addRoomModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300" id="roomModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">ADD ROOM</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <div class="space-y-4 max-w-[400px] mx-auto">
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Room Name</label>
                            <input type="text" id="addRoomName" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200" placeholder="Type here">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Building Name</label>
                            <div class="relative">
                                <select id="addRoomBuilding" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] appearance-none focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 bg-white cursor-pointer">
                                    <option value="">Select Building</option>
                                    <option value="building-a">Building A</option>
                                    <option value="building-b">Building B</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                    <svg class="w-4 h-4 text-[#203268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Floor</label>
                            <input type="text" id="addRoomFloor" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200" placeholder="Type here">
                        </div>
                        <button class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Room Modal -->
<div id="editRoomModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300" id="editRoomModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">EDIT ROOM</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <div class="space-y-4 max-w-[400px] mx-auto">
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Room Name</label>
                            <input type="text" id="editRoomName" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200" placeholder="Type here">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Building Name</label>
                            <div class="relative">
                                <select id="editRoomBuilding" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] appearance-none focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 bg-white cursor-pointer">
                                    <option value="">Select Building</option>
                                    <option value="building-a">Building A</option>
                                    <option value="building-b">Building B</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                    <svg class="w-4 h-4 text-[#203268]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Floor</label>
                            <input type="text" id="editRoomFloor" class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200" placeholder="Type here">
                        </div>
                        <button class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Room Modal -->
<div id="deleteRoomModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300" id="deleteRoomModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">DELETE ROOM</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <div class="space-y-6 max-w-[400px] mx-auto">
                        <div class="flex flex-col items-center">
                            <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-base text-gray-600 text-center">Are you sure you want to delete this room? This action cannot be undone.</p>
                        </div>
                        <div class="flex gap-3">
                            <button class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                Cancel
                            </button>
                            <button class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal elements
        const addBuildingBtn = document.getElementById('addBuildingBtn');
        const addRoomBtn = document.getElementById('addRoomBtn');
        const addBuildingModal = document.getElementById('addBuildingModal');
        const addRoomModal = document.getElementById('addRoomModal');
        const buildingModalContent = document.getElementById('buildingModalContent');
        const roomModalContent = document.getElementById('roomModalContent');
        const closeButtons = document.querySelectorAll('.close-modal');

        // Edit modals
        const editBuildingButtons = document.querySelectorAll('.edit-building-btn');
        const editRoomButtons = document.querySelectorAll('.edit-room-btn');
        const editBuildingModal = document.getElementById('editBuildingModal');
        const editRoomModal = document.getElementById('editRoomModal');
        const editBuildingModalContent = document.getElementById('editBuildingModalContent');
        const editRoomModalContent = document.getElementById('editRoomModalContent');

        // Delete modals
        const deleteBuildingButtons = document.querySelectorAll('.delete-building-btn');
        const deleteRoomButtons = document.querySelectorAll('.delete-room-btn');
        const deleteBuildingModal = document.getElementById('deleteBuildingModal');
        const deleteRoomModal = document.getElementById('deleteRoomModal');
        const deleteBuildingModalContent = document.getElementById('deleteBuildingModalContent');
        const deleteRoomModalContent = document.getElementById('deleteRoomModalContent');

        function openModal(modal, content) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
            }, 10);
        }

        function closeModal(modal, content) {
            content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
            content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Add event listeners
        addBuildingBtn.addEventListener('click', () => openModal(addBuildingModal, buildingModalContent));
        addRoomBtn.addEventListener('click', () => openModal(addRoomModal, roomModalContent));

        editBuildingButtons.forEach(button => {
            button.addEventListener('click', () => openModal(editBuildingModal, editBuildingModalContent));
        });

        editRoomButtons.forEach(button => {
            button.addEventListener('click', () => openModal(editRoomModal, editRoomModalContent));
        });

        deleteBuildingButtons.forEach(button => {
            button.addEventListener('click', () => openModal(deleteBuildingModal, deleteBuildingModalContent));
        });

        deleteRoomButtons.forEach(button => {
            button.addEventListener('click', () => openModal(deleteRoomModal, deleteRoomModalContent));
        });

        // Close modal handlers
        closeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const modal = button.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="ModalContent"]');
                closeModal(modal, content);
            });
        });

        // Close on outside click
        [addBuildingModal, editBuildingModal, deleteBuildingModal, addRoomModal, editRoomModal, deleteRoomModal].forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    const content = modal.querySelector('[id$="ModalContent"]');
                    closeModal(modal, content);
                }
            });
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                [addBuildingModal, editBuildingModal, deleteBuildingModal, addRoomModal, editRoomModal, deleteRoomModal].forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        const content = modal.querySelector('[id$="ModalContent"]');
                        closeModal(modal, content);
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection
