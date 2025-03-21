@extends('Layout.app')

@section('title', 'Location')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Building Section -->
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
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buildings as $building)
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $building['building_id'] }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $building['building_name'] }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $building['address'] }}</td>
                                <td class="p-3 border-t border-[#EEF1F4]">
                                    <div class="flex justify-center gap-2">
                                        <button class="text-[#3D3D3D] hover:text-[#213268] edit-building-btn"
                                               data-id="{{ $building['building_id'] }}"
                                               data-name="{{ $building['building_name'] }}"
                                               data-address="{{ $building['address'] }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button class="text-[#3D3D3D] hover:text-red-500 delete-building-btn"
                                               data-id="{{ $building['building_id'] }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-3 text-center text-gray-500">No buildings found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex gap-2">
                        @if(isset($buildingPagination) && is_array($buildingPagination))
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($buildingPagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   onclick="changeBuildingPage({{ ($buildingPagination['current_page'] ?? 1) - 1 }})"
                                   {{ ($buildingPagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Prev
                            </button>

                            <div class="flex gap-1">
                                @php
                                    $currentPage = $buildingPagination['current_page'] ?? 1;
                                    $totalPages = $buildingPagination['total_pages'] ?? 1;
                                    $startPage = max(1, min($currentPage - 2, $totalPages - 4));
                                    $endPage = min($totalPages, max(5, $currentPage + 2));
                                @endphp

                                @for ($i = $startPage; $i <= $endPage; $i++)
                                <button class="w-8 h-8 {{ $i == $currentPage ? 'bg-[#213268] text-white' : 'border border-[#D8DAE5] text-[#213268]' }} rounded text-sm hover:bg-gray-50 {{ $i == $currentPage ? '' : 'hover:bg-gray-100' }}"
                                       onclick="changeBuildingPage({{ $i }})">
                                    {{ $i }}
                                </button>
                                @endfor
                            </div>
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($buildingPagination['current_page'] ?? 1) >= ($buildingPagination['total_pages'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   onclick="changeBuildingPage({{ ($buildingPagination['current_page'] ?? 1) + 1 }})"
                                   {{ ($buildingPagination['current_page'] ?? 1) >= ($buildingPagination['total_pages'] ?? 1) ? 'disabled' : '' }}>
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @else
                            <!-- Default pagination when no data -->
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 opacity-50 cursor-not-allowed" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Prev
                            </button>
                            <button class="w-8 h-8 bg-[#213268] text-white rounded text-sm">1</button>
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 opacity-50 cursor-not-allowed" disabled>
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">
                            @if(isset($buildingPagination) && is_array($buildingPagination))
                                @php
                                    $currentPage = $buildingPagination['current_page'] ?? 1;
                                    $perPage = $buildingPagination['limit'] ?? 10;
                                    $total = $buildingPagination['total_items'] ?? count($buildings);
                                    $from = ($currentPage - 1) * $perPage + 1;
                                    $to = min($currentPage * $perPage, $total);
                                @endphp
                                Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                            @else
                                Showing 1 to {{ count($buildings) }} of {{ count($buildings) }} entries
                            @endif
                        </span>
                        <select id="buildingPerPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changeBuildingPerPage(this.value)">
                            <option value="10" {{ isset($buildingPagination['limit']) && $buildingPagination['limit'] == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ isset($buildingPagination['limit']) && $buildingPagination['limit'] == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ isset($buildingPagination['limit']) && $buildingPagination['limit'] == 50 ? 'selected' : '' }}>50 per page</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Section -->
    <div class="card bg-base-100 shadow-xl">
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
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Building</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Floor</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Description</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $room)
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $room['room_id'] }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $room['room_name'] }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $room['building_name'] }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $room['floor_number'] }}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $room['description'] ?? '-' }}</td>
                                <td class="p-3 border-t border-[#EEF1F4]">
                                    <div class="flex justify-center gap-2">
                                        <button class="text-[#3D3D3D] hover:text-[#213268] edit-room-btn"
                                               data-id="{{ $room['room_id'] }}"
                                               data-name="{{ $room['room_name'] }}"
                                               data-building-id="{{ $room['building_id'] }}"
                                               data-floor-number="{{ $room['floor_number'] }}"
                                               data-description="{{ $room['description'] ?? '' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button class="text-[#3D3D3D] hover:text-red-500 delete-room-btn"
                                               data-id="{{ $room['room_id'] }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-3 text-center text-gray-500">No rooms found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Rooms -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex gap-2">
                        @if(isset($roomPagination) && is_array($roomPagination))
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($roomPagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   onclick="changeRoomPage({{ ($roomPagination['current_page'] ?? 1) - 1 }})"
                                   {{ ($roomPagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Prev
                            </button>
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $roomPagination['current_page'] ?? 1;
                                    $totalPages = $roomPagination['total_pages'] ?? 1;
                                    $startPage = max(1, min($currentPage - 2, $totalPages - 4));
                                    $endPage = min($totalPages, max(5, $currentPage + 2));
                                @endphp

                                @for ($i = $startPage; $i <= $endPage; $i++)
                                <button class="w-8 h-8 {{ $i == $currentPage ? 'bg-[#213268] text-white' : 'border border-[#D8DAE5] text-[#213268]' }} rounded text-sm hover:bg-gray-50 {{ $i == $currentPage ? '' : 'hover:bg-gray-100' }}"
                                       onclick="changeRoomPage({{ $i }})">
                                    {{ $i }}
                                </button>
                                @endfor
                            </div>
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($roomPagination['current_page'] ?? 1) >= ($roomPagination['total_pages'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   onclick="changeRoomPage({{ ($roomPagination['current_page'] ?? 1) + 1 }})"
                                   {{ ($roomPagination['current_page'] ?? 1) >= ($roomPagination['total_pages'] ?? 1) ? 'disabled' : '' }}>
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @else
                            <!-- Default pagination when no data -->
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 opacity-50 cursor-not-allowed" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Prev
                            </button>
                            <button class="w-8 h-8 bg-[#213268] text-white rounded text-sm">1</button>
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 opacity-50 cursor-not-allowed" disabled>
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">
                            @if(isset($roomPagination) && is_array($roomPagination))
                                @php
                                    $currentPage = $roomPagination['current_page'] ?? 1;
                                    $perPage = $roomPagination['limit'] ?? 10;
                                    $total = $roomPagination['total_items'] ?? count($rooms);
                                    $from = ($currentPage - 1) * $perPage + 1;
                                    $to = min($currentPage * $perPage, $total);
                                @endphp
                                Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                            @else
                                Showing 1 to {{ count($rooms) }} of {{ count($rooms) }} entries
                            @endif
                        </span>
                        <select id="roomPerPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changeRoomPerPage(this.value)">
                            <option value="10" {{ isset($roomPagination['limit']) && $roomPagination['limit'] == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ isset($roomPagination['limit']) && $roomPagination['limit'] == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ isset($roomPagination['limit']) && $roomPagination['limit'] == 50 ? 'selected' : '' }}>50 per page</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- All modals should be outside the main content section -->
<!-- Add Building Modal -->
<div id="addBuildingModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="buildingModalContent">
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
                    <form id="addBuildingForm" action="{{ route('buildings.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Building Name Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Building Name</label>
                                <input type="text" name="building_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here" required>
                            </div>

                            <!-- Address Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Address</label>
                                <input type="text" name="address"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here">
                            </div>

                            <!-- Button Group -->
                            <div class="pt-4">
                                <button type="submit" class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
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
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="editBuildingModalContent">
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
                    <form id="editBuildingForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editBuildingId" name="building_id">
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Building Name Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Building Name</label>
                                <input type="text" id="editBuildingName" name="building_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here" required>
                            </div>

                            <!-- Address Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Address</label>
                                <input type="text" id="editAddress" name="address"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here">
                            </div>

                            <!-- Button Group -->
                            <div class="pt-4">
                                <button type="submit" class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Building Confirmation Modal -->
<div id="deleteBuildingModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="deleteBuildingModalContent">
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
                    <p class="text-center text-gray-600 mb-6">Are you sure you want to delete this building? This action cannot be undone.</p>
                    <form id="deleteBuildingForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="deleteBuildingId" name="building_id">
                        <div>
                            <button type="submit" class="w-full h-[45px] bg-red-600 text-white rounded-lg text-base hover:bg-red-700 transform active:scale-[0.98] transition-all duration-200">
                                Delete
                            </button>
                        </div>
                    </form>
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
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="roomModalContent">
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
                    <form id="addRoomForm" action="{{ route('rooms.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Room Name Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Room Name</label>
                                <input type="text" name="room_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here" required>
                            </div>

                            <!-- Building Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Building</label>
                                <select name="building_id"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    required>
                                    <option value="" disabled selected>Select a building</option>
                                    @foreach($buildings as $building)
                                    <option value="{{ $building['building_id'] }}">{{ $building['building_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Floor Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Floor</label>
                                <input type="text" name="floor_number"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here" required>
                            </div>

                            <!-- Description Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Description</label>
                                <textarea name="description"
                                    class="w-full h-[100px] py-3 px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 resize-none"
                                    placeholder="Type here"></textarea>
                            </div>

                            <!-- Button Group -->
                            <div class="pt-4">
                                <button type="submit" class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
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
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="editRoomModalContent">
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
                    <form id="editRoomForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editRoomId" name="room_id">
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Room Name Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Room Name</label>
                                <input type="text" id="editRoomName" name="room_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here" required>
                            </div>

                            <!-- Building Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Building</label>
                                <select id="editRoomBuilding" name="building_id"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    required>
                                    <option value="" disabled>Select a building</option>
                                    @foreach($buildings as $building)
                                    <option value="{{ $building['building_id'] }}">{{ $building['building_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Floor Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Floor</label>
                                <input type="text" id="editRoomFloor" name="floor_number"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here" required>
                            </div>

                            <!-- Description Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Description</label>
                                <textarea id="editRoomDescription" name="description"
                                    class="w-full h-[100px] py-3 px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 resize-none"
                                    placeholder="Type here"></textarea>
                            </div>

                            <!-- Button Group -->
                            <div class="pt-4">
                                <button type="submit" class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Room Confirmation Modal -->
<div id="deleteRoomModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="deleteRoomModalContent">
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
                    <p class="text-center text-gray-600 mb-6">Are you sure you want to delete this room? This action cannot be undone.</p>
                    <form id="deleteRoomForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="deleteRoomId" name="room_id">
                        <div>
                            <button type="submit" class="w-full h-[45px] bg-red-600 text-white rounded-lg text-base hover:bg-red-700 transform active:scale-[0.98] transition-all duration-200">
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success and Error Notifications -->
@if(session('success'))
<div id="successNotification" class="fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50" role="alert">
    <div class="flex items-center">
        <div class="py-1">
            <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="font-bold">Success!</p>
            <p>{{ session('success') }}</p>
        </div>
        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
    </div>
</div>

<script>
    setTimeout(function() {
        const notification = document.getElementById('successNotification');
        if (notification) {
            notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(function() {
                notification.remove();
            }, 500);
        }
    }, 5000); // Hide after 5 seconds
</script>
@endif

@if(session('error'))
<div id="errorNotification" class="fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md z-50" role="alert">
    <div class="flex items-center">
        <div class="py-1">
            <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="font-bold">Error!</p>
            <p>{{ session('error') }}</p>
        </div>
        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
    </div>
</div>

<script>
    setTimeout(function() {
        const notification = document.getElementById('errorNotification');
        if (notification) {
            notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(function() {
                notification.remove();
            }, 500);
        }
    }, 5000); // Hide after 5 seconds
</script>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toast container
        const toastContainer = document.createElement('div');
        toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-4';
        document.body.appendChild(toastContainer);

        // Get all modal elements
        const addBuildingModal = document.getElementById('addBuildingModal');
        const editBuildingModal = document.getElementById('editBuildingModal');
        const deleteBuildingModal = document.getElementById('deleteBuildingModal');
        const addRoomModal = document.getElementById('addRoomModal');
        const editRoomModal = document.getElementById('editRoomModal');
        const deleteRoomModal = document.getElementById('deleteRoomModal');
        const closeButtons = document.querySelectorAll('.close-modal');

        // Function to open modal - updated to match Vendor.blade.php
        function openModal(modal, content) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
            }, 10);
        }

        // Function to close modal - updated to match Vendor.blade.php
        function closeModal(modal, content) {
            content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
            content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Add Building Modal
        document.getElementById('addBuildingBtn').addEventListener('click', () => {
            openModal(addBuildingModal, addBuildingModal.querySelector('[id$="ModalContent"]'));
        });

        // Add Room Modal
        document.getElementById('addRoomBtn').addEventListener('click', () => {
            openModal(addRoomModal, addRoomModal.querySelector('[id$="ModalContent"]'));
        });

        // Edit Building Modal
        document.querySelectorAll('.edit-building-btn').forEach(button => {
            button.addEventListener('click', () => {
                const buildingId = button.getAttribute('data-id');
                document.getElementById('editBuildingForm').action = `{{ url('location/buildings/update') }}/${buildingId}`;
                document.getElementById('editBuildingId').value = buildingId;
                document.getElementById('editBuildingName').value = button.getAttribute('data-name');
                document.getElementById('editAddress').value = button.getAttribute('data-address');

                openModal(editBuildingModal, editBuildingModal.querySelector('[id$="ModalContent"]'));
            });
        });

        // Edit Room Modal
        document.querySelectorAll('.edit-room-btn').forEach(button => {
            button.addEventListener('click', () => {
                const roomId = button.getAttribute('data-id');
                document.getElementById('editRoomForm').action = `{{ url('location/rooms/update') }}/${roomId}`;
                document.getElementById('editRoomId').value = roomId;
                document.getElementById('editRoomName').value = button.getAttribute('data-name');
                document.getElementById('editRoomBuilding').value = button.getAttribute('data-building-id');
                document.getElementById('editRoomFloor').value = button.getAttribute('data-floor-number');
                document.getElementById('editRoomDescription').value = button.getAttribute('data-description') || '';

                openModal(editRoomModal, editRoomModal.querySelector('[id$="ModalContent"]'));
            });
        });

        // Delete Building Modal
        document.querySelectorAll('.delete-building-btn').forEach(button => {
            button.addEventListener('click', () => {
                const buildingId = button.getAttribute('data-id');
                document.getElementById('deleteBuildingForm').action = `{{ url('location/buildings/delete') }}/${buildingId}`;
                document.getElementById('deleteBuildingId').value = buildingId;

                openModal(deleteBuildingModal, deleteBuildingModal.querySelector('[id$="ModalContent"]'));
            });
        });

        // Delete Room Modal
        document.querySelectorAll('.delete-room-btn').forEach(button => {
            button.addEventListener('click', () => {
                const roomId = button.getAttribute('data-id');
                document.getElementById('deleteRoomForm').action = `{{ url('location/rooms/delete') }}/${roomId}`;
                document.getElementById('deleteRoomId').value = roomId;

                openModal(deleteRoomModal, deleteRoomModal.querySelector('[id$="ModalContent"]'));
            });
        });

        // Close Modal Handlers - updated to match Vendor.blade.php
        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="ModalContent"]');
                closeModal(modal, content);
            });
        });

        // Close on outside click - updated to match Vendor.blade.php
        [addBuildingModal, editBuildingModal, deleteBuildingModal, addRoomModal, editRoomModal, deleteRoomModal].forEach(modal => {
            modal.addEventListener('click', function(e) {
                // Check if the click is directly on the modal's overlay area
                if (e.target === this.querySelector('.fixed.inset-0.z-50.overflow-y-auto') ||
                    e.target === this.querySelector('.fixed.inset-0.bg-black.bg-opacity-50')) {
                    const content = this.querySelector('[id$="ModalContent"]');
                    closeModal(this, content);
                }
            });
        });

        // Close on Escape key - added to match Vendor.blade.php
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

        // Show toast notification
        window.showToast = function(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            let bgColor, borderColor, textColor, icon;

            if (type === 'success') {
                bgColor = 'bg-green-100';
                borderColor = 'border-green-500';
                textColor = 'text-green-700';
                icon = `<svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            } else if (type === 'error') {
                bgColor = 'bg-red-100';
                borderColor = 'border-red-500';
                textColor = 'text-red-700';
                icon = `<svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            } else {
                bgColor = 'bg-blue-100';
                borderColor = 'border-blue-500';
                textColor = 'text-blue-700';
                icon = `<svg class="h-6 w-6 text-blue-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            }

            toast.className = `${bgColor} border-l-4 ${borderColor} ${textColor} p-4 rounded shadow-md z-50 opacity-0 transition-opacity duration-300`;
            toast.setAttribute('role', 'alert');

            // Create toast content
            toast.innerHTML = `
                <div class="flex items-center">
                    <div class="py-1">
                        ${icon}
                    </div>
                    <div>
                        <p class="font-bold">${type.charAt(0).toUpperCase() + type.slice(1)}!</p>
                        <p>${message}</p>
                    </div>
                    <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                </div>
            `;

            // Add to container
            toastContainer.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.remove('opacity-0');
                toast.classList.add('opacity-100');
            }, 10);

            // Remove after 5 seconds
            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
                setTimeout(() => {
                    if (toast.parentNode === toastContainer) {
                        toastContainer.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        }
    });
</script>
@endpush
@endsection
