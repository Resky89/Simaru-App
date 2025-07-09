@extends('Layout.app')

@section('title', 'Rooms')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Room Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">Ruangan</h1>

                        <!-- Button Add Room -->
                        <div class="flex flex-wrap gap-3">
                            @if(hasPermission('room:import'))
                                <button id="importRoomBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-green-600 rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v8" />
                                    </svg>
                                    <span class="text-base">Impor Excel</span>
                                </button>
                            @endif
                            @if(hasPermission('room:create'))
                                <button id="addRoomBtn"
                                    class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                                    <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                        <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                    </svg>
                                    <span class="text-base">Tambah Ruangan</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput" placeholder="Cari ruangan atau gedung..."
                                class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                value="{{ request()->input('search', '') }}">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <select id="sortOrder"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" {{ request()->input('sort') == '' ? 'selected' : '' }}>Urutan Default
                                </option>
                                <option value="id_asc" {{ request()->input('sort') == 'id_asc' ? 'selected' : '' }}>Terlama
                                </option>
                                <option value="id_desc" {{ request()->input('sort') == 'id_desc' ? 'selected' : '' }}>Terbaru
                                </option>
                                <option value="name_asc" {{ request()->input('sort') == 'name_asc' ? 'selected' : '' }}>Nama
                                    (A-Z)</option>
                                <option value="name_desc" {{ request()->input('sort') == 'name_desc' ? 'selected' : '' }}>Nama
                                    (Z-A)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Room Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                        <div class="flex items-center space-x-1 cursor-pointer" id="sortByName">
                                            <span class="text-xs">Nama Ruangan</span>
                                            <span class="sort-icon">
                                                @php
                                                    $currentSort = request()->query('sort');
                                                    $sortIcon = 'none';

                                                    if ($currentSort === 'name_asc') {
                                                        $sortIcon = 'asc';
                                                    } elseif ($currentSort === 'name_desc') {
                                                        $sortIcon = 'desc';
                                                    }
                                                @endphp

                                                @if($sortIcon === 'asc')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                @elseif($sortIcon === 'desc')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Gedung</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                        <div class="flex items-center space-x-1 cursor-pointer" id="sortByFloor">
                                            <span class="text-xs">Lantai</span>
                                            <span class="sort-icon">
                                                @php
                                                    $floorSortIcon = 'none';

                                                    if ($currentSort === 'floor_asc') {
                                                        $floorSortIcon = 'asc';
                                                    } elseif ($currentSort === 'floor_desc') {
                                                        $floorSortIcon = 'desc';
                                                    }
                                                @endphp

                                                @if($floorSortIcon === 'asc')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                @elseif($floorSortIcon === 'desc')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Deskripsi</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rooms as $room)
                                    <tr>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $room['room_name'] ?? '-'}}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $room['building_name'] ?? '-' }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $room['floor_number'] ?? '-'}}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $room['description'] ?? '-' }}</td>
                                        <td class="p-3 border-t border-[#EEF1F4]">
                                            <div class="flex items-center space-x-2 justify-center">
                                                @if(hasPermission('room:edit'))
                                                    <button
                                                        class="edit-room-btn p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors"
                                                        data-id="{{ $room['room_id'] }}"
                                                        title="Edit Ruangan">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                @if(hasPermission('room:delete'))
                                                    <button
                                                        class="delete-room-btn p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors"
                                                        data-id="{{ $room['room_id'] }}"
                                                        title="Hapus Ruangan">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-3 text-center text-gray-500">Tidak ada ruangan yang ditemukan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination for Rooms -->
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ ($roomPagination['current_page'] ?? 1) <= 1 ? '#' : request()->fullUrlWithQuery(['room_page' => ($roomPagination['current_page'] ?? 1) - 1]) }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($roomPagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Sebelumnya
                            </a>
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $roomPagination['current_page'] ?? 1;
                                    $lastPage = $roomPagination['last_page'] ?? 1;
                                    $maxPagesShown = 5; // Show max 5 pages at once
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($lastPage, $startPage + $maxPagesShown - 1);

                                    if ($endPage - $startPage + 1 < $maxPagesShown) {
                                        $startPage = max(1, $endPage - $maxPagesShown + 1);
                                    }
                                @endphp

                                @if($startPage > 1)
                                    <a href="{{ request()->fullUrlWithQuery(['room_page' => 1]) }}"
                                        class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                        1
                                    </a>
                                    @if($startPage > 2)
                                        <span class="flex items-center justify-center">
                                            ...
                                        </span>
                                    @endif
                                @endif

                                @for ($i = $startPage; $i <= $endPage; $i++)
                                    <a href="{{ request()->fullUrlWithQuery(['room_page' => $i]) }}"
                                        class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                        {{ $i }}
                                    </a>
                                @endfor

                                @if($endPage < $lastPage)
                                    @if($endPage < $lastPage - 1)
                                        <span class="flex items-center justify-center">
                                            ...
                                        </span>
                                    @endif
                                    <a href="{{ request()->fullUrlWithQuery(['room_page' => $lastPage]) }}"
                                        class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                        {{ $lastPage }}
                                    </a>
                                @endif
                            </div>
                            <a href="{{ ($roomPagination['current_page'] ?? 1) >= ($roomPagination['last_page'] ?? 1) ? '#' : request()->fullUrlWithQuery(['room_page' => ($roomPagination['current_page'] ?? 1) + 1]) }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($roomPagination['current_page'] ?? 1) >= ($roomPagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}">
                                Selanjutnya
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">
                                @if(isset($roomPagination) && is_array($roomPagination))
                                    @php
                                        $currentPage = $roomPagination['current_page'] ?? 1;
                                        $perPage = $roomPagination['per_page'] ?? 10;
                                        $total = $roomPagination['total'] ?? count($rooms ?? []);
                                        $from = ($currentPage - 1) * $perPage + 1;
                                        $to = min($currentPage * $perPage, $total);
                                    @endphp
                                    Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} data
                                @else
                                    Menampilkan 1 sampai {{ count($rooms) }} dari {{ count($rooms) }} data
                                @endif
                            </span>
                            <select id="roomPerPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changeRoomPerPage(this.value)">
                                <option value="10" {{ isset($roomPagination['per_page']) && $roomPagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                                <option value="25" {{ isset($roomPagination['per_page']) && $roomPagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                                <option value="50" {{ isset($roomPagination['per_page']) && $roomPagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Room Modal -->
        @if(hasPermission('room:create'))
            <div id="addRoomModal" class="fixed inset-0 z-50 hidden">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                            id="roomModalContent">
                            <!-- Header -->
                            <div class="flex justify-between items-center p-6 pb-0">
                                <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">Tambah Ruangan</h2>
                                <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                    <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Form -->
                            <div class="p-6">
                                <form id="addRoomForm" action="{{ route('rooms.store') }}" method="POST" data-no-loading
                                    novalidate>
                                    @csrf
                                    <div class="space-y-4 max-w-[400px] mx-auto">
                                        <!-- Room Name Input -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Nama Ruangan <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="room_name" id="add_room_name"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Ketik nama ruangan" required>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nama Ruangan harus diisi
                                            </div>
                                        </div>

                                        <!-- Building Input -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Gedung <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="text" id="add_building_search"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                                    placeholder="Cari gedung" autocomplete="off" required>
                                                <input type="hidden" name="building_id" id="add_building_id">

                                                <!-- Building Dropdown -->
                                                <div id="add_building_dropdown"
                                                    class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-lg py-1 text-base overflow-auto focus:outline-none hidden">
                                                    <!-- Loading indicator -->
                                                    <div id="add_building_loading" class="flex justify-center py-2">
                                                        <div
                                                            class="animate-spin rounded-full h-5 w-5 border-b-2 border-[#213268]">
                                                        </div>
                                                        <span class="ml-2 text-gray-600">Memuat gedung...</span>
                                                    </div>
                                                    <ul id="add_building_list" class="max-h-56 overflow-y-auto"></ul>
                                                </div>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Gedung harus dipilih
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Floor Input -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Lantai <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="add_floor_number" name="floor_number"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Ketik lantai ruangan" required>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Lantai harus diisi</div>
                                        </div>

                                        <!-- Description Input -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">Deskripsi</label>
                                            <textarea name="description"
                                                class="w-full h-[100px] py-3 px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 resize-none"
                                                placeholder="Ketik deskripsi ruangan"></textarea>
                                        </div>

                                        <!-- Button Group -->
                                        <div class="pt-4">
                                            <button type="submit"
                                                class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                                Simpan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Edit Room Modal -->
        @if(hasPermission('room:edit'))
            <div id="editRoomModal" class="fixed inset-0 z-50 hidden">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                            id="editRoomModalContent">
                            <!-- Header -->
                            <div class="flex justify-between items-center p-6 pb-0">
                                <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">Edit Ruangan</h2>
                                <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                    <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Form -->
                            <div class="p-6 relative">
                                <div id="editRoomFormContent" class="relative">
                                    <form id="editRoomForm" action="" method="POST" data-no-loading novalidate>
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" id="editRoomId" name="room_id">
                                        <div class="space-y-4 max-w-[400px] mx-auto">
                                            <!-- Room Name Input -->
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666]">
                                                    Nama Ruangan <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" id="editRoomName" name="room_name"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                                    placeholder="Ketik nama ruangan" required>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Nama Ruangan harus
                                                    diisi</div>
                                            </div>

                                            <!-- Building Input -->
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666]">
                                                    Gedung <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <input type="text" id="edit_building_search"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                                        placeholder="Cari gedung" autocomplete="off" required>
                                                    <input type="hidden" name="building_id" id="editRoomBuilding">

                                                    <!-- Building Dropdown -->
                                                    <div id="edit_building_dropdown"
                                                        class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-lg py-1 text-base overflow-auto focus:outline-none hidden">
                                                        <!-- Loading indicator -->
                                                        <div id="edit_building_loading" class="flex justify-center py-2">
                                                            <div
                                                                class="animate-spin rounded-full h-5 w-5 border-b-2 border-[#213268]">
                                                            </div>
                                                            <span class="ml-2 text-gray-600">Memuat gedung...</span>
                                                        </div>
                                                        <ul id="edit_building_list" class="max-h-56 overflow-y-auto"></ul>
                                                    </div>
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Gedung harus
                                                        dipilih</div>
                                                </div>
                                            </div>

                                            <!-- Floor Input -->
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666]">
                                                    Lantai <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" id="editRoomFloor" name="floor_number"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                                    placeholder="Ketik lantai ruangan" required>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Lantai harus diisi
                                                </div>
                                            </div>

                                            <!-- Description Input -->
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666]">Deskripsi</label>
                                                <textarea id="editRoomDescription" name="description"
                                                    class="w-full h-[100px] py-3 px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 resize-none"
                                                    placeholder="Ketik deskripsi ruangan"></textarea>
                                            </div>

                                            <!-- Button Group -->
                                            <div class="pt-4">
                                                <button type="submit"
                                                    class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                                    Simpan
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Room Confirmation Modal -->
        @if(hasPermission('room:delete'))
            <div id="deleteRoomModal" class="fixed inset-0 z-50 hidden">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                            id="deleteRoomModalContent">
                            <!-- Header -->
                            <div class="flex justify-between items-center p-6 pb-0">
                                <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Hapus Ruangan</h2>
                                <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                    <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Content -->
                            <form id="deleteRoomForm" action="" method="POST" data-no-loading>
                                @csrf
                                @method('DELETE')
                                <input type="hidden" id="deleteRoomId" name="room_id">
                                <div class="p-6">
                                    <div class="space-y-6 max-w-[400px] mx-auto">
                                        <div class="flex flex-col items-center">
                                            <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-base text-gray-600 text-center">Apakah anda yakin ingin menghapus
                                                ruangan
                                                ini? Aksi ini tidak dapat dibatalkan.</p>
                                        </div>
                                        <div class="flex gap-3">
                                            <button type="button"
                                                class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <script>
            // Handle delete form submission with AJAX
            document.addEventListener('DOMContentLoaded', function () {
                const deleteRoomForm = document.getElementById('deleteRoomForm');
                if (deleteRoomForm) {
                    deleteRoomForm.addEventListener('submit', function (e) {
                        e.preventDefault();

                        const formData = new FormData(this);
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalBtnText = submitBtn.innerHTML;
                        const url = this.action;

                        // Show loading state
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `
                                <div class="flex items-center justify-center">
                                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                    <span>Memproses...</span>
                                </div>
                            `;

                        fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json().then(data => {
                                        data.status = response.status;
                                        return data;
                                    });
                                } else {
                                    throw new Error('Invalid response format');
                                }
                            })
                            .then(data => {
                                // Reset button state
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;

                                // Close the modal
                                const modal = document.getElementById('deleteRoomModal');
                                closeModal(modal, modal.querySelector('[id$="ModalContent"]'));

                                // Reload the page to show server-side notification
                                window.location.reload();
                            })
                            .catch(error => {
                                // Reset button state
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;

                                // Close the modal
                                const modal = document.getElementById('deleteRoomModal');
                                closeModal(modal, modal.querySelector('[id$="ModalContent"]'));

                                // Reload the page to show server-side error notification
                                window.location.reload();
                            });
                    });
                }
            });
        </script>

        <!-- Success and Error Notifications using JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Show session success message using the toast system
                @if(session('success'))
                    showToast('{{ session('success') }}', 'success');
                @endif

                // Show session error message using the toast system
                @if(session('error'))
                    showToast('{{ session('error') }}', 'error');
                @endif
                });

            // Function to show toast notifications
            window.showToast = function (message, type = 'info') {
                // Create the notification element
                const notification = document.createElement('div');
                notification.id = type + 'Notification' + Date.now(); // Unique ID to allow multiple notifications
                notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
                notification.role = 'alert';

                // Check if message contains HTML
                const hasHTML = /<[a-z][\s\S]*>/i.test(message);

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
                    title.textContent = 'Gagal!';
                    contentContainer.appendChild(title);

                    // Message container
                    const messageContainer = document.createElement('div');
                    messageContainer.className = 'error-message';

                    // Handle HTML content
                    if (hasHTML) {
                        messageContainer.innerHTML = message;
                    } else {
                        messageContainer.textContent = message;
                    }

                    contentContainer.appendChild(messageContainer);

                    // Close button
                    const closeBtn = document.createElement('span');
                    closeBtn.className = 'ml-4 cursor-pointer flex-shrink-0';
                    closeBtn.textContent = '×';
                    closeBtn.onclick = function () {
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
            }

            // Add slide-in animation and styling for error messages to CSS
            document.head.insertAdjacentHTML('beforeend', `
                    <style>
                        @keyframes slideInRight {
                            from { transform: translateX(100%); }
                            to { transform: translateX(0); }
                        }
                        .animate-slide-in-right {
                            animation: slideInRight 0.3s ease-out forwards;
                        }

                        /* Styling for error messages with HTML content */
                        .error-message ul {
                            margin-top: 0.5rem;
                            padding-left: 1.5rem;
                        }
                        .error-message ul li {
                            margin-bottom: 0.25rem;
                        }
                        .error-message ul li:last-child {
                            margin-bottom: 0;
                        }
                    </style>
                `);
        </script>

        <!-- Import Room Modal -->
        @if(hasPermission('room:import'))
            <div id="importRoomModal" class="fixed inset-0 z-50 hidden">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                            id="importRoomModalContent">
                            <!-- Header -->
                            <div class="flex justify-between items-center p-6 pb-0">
                                <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">IMPOR RUANGAN</h2>
                                <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                    <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Step 1: File Selection -->
                            <div id="import-room-step-1" class="block">
                                <div class="p-6">
                                    <div class="space-y-6">
                                        <!-- Import Instructions -->
                                        <div class="text-gray-600 text-sm bg-blue-50 p-4 rounded-lg">
                                            <p class="font-medium text-blue-600 mb-2">Petunjuk Impor:</p>
                                            <ul class="list-disc pl-5 space-y-1">
                                                <li>Gunakan format template Excel untuk mengimpor</li>
                                                <li>Kolom yang diperlukan: Nama Ruangan, Gedung, Lantai</li>
                                                <li>Maksimal 100 data per impor</li>
                                                <li>Format file yang didukung: .xlsx, .xls, .csv</li>
                                            </ul>
                                            <div class="mt-3 flex justify-end">
                                                <a href="{{ asset('docs/ImportRuanganTemplate.xlsx') }}" download
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-[#213268] rounded-md hover:bg-[#152451] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                        </path>
                                                    </svg>
                                                    Unduh Template
                                                </a>
                                            </div>
                                        </div>

                                        <!-- File Upload -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#213268]">File Excel</label>
                                            <div
                                                class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                                <!-- File preview -->
                                                <div id="room-excel-file-name" class="mt-2 mb-4 w-full hidden">
                                                    <div
                                                        class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                        <div class="flex items-center">
                                                            <svg class="w-6 h-6 text-green-600 mr-2"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            <span id="room-file-name-text"
                                                                class="text-sm text-gray-700 truncate"></span>
                                                            <button type="button" id="remove-room-excel"
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
                                                    <svg class="mx-auto h-12 w-12 text-[#213268]"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    <p class="mt-1 text-sm text-gray-600">Seret file Excel Anda atau <span
                                                            class="text-[#213268] font-semibold">telusuri file</span></p>
                                                    <p class="mt-1 text-xs text-gray-500">Format yang diterima: xlsx, xls, csv
                                                    </p>
                                                    <p class="mt-1 text-xs text-[#213268] font-medium">Klik di mana saja di area
                                                        ini untuk memilih file</p>
                                                </div>
                                                <input type="file" id="room_excel_file" name="excel_file"
                                                    accept=".xlsx,.xls,.csv"
                                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                            </div>
                                        </div>

                                        <!-- Error Message -->
                                        <div id="room-excel-error" class="hidden text-red-500 text-sm"></div>

                                        <!-- Loading Indicator -->
                                        <div id="room-excel-loading" class="hidden text-center py-2">
                                            <div
                                                class="inline-block w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin">
                                            </div>
                                            <p class="mt-2 text-sm text-gray-600">Memproses data Excel...</p>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="flex gap-3">
                                            <button type="button"
                                                class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                                Batal
                                            </button>
                                            <button type="button" id="room-preview-btn" disabled
                                                class="w-1/2 h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                                Pratinjau Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Import Progress -->
                            <div id="import-room-step-2" class="hidden">
                                <div class="p-6">
                                    <div class="space-y-6">
                                        <!-- Preview Header -->
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-semibold text-[#213268]">Pratinjau Data</h3>
                                            <span class="text-sm text-gray-500" id="room-preview-count">0 item ditemukan</span>
                                        </div>

                                        <!-- Preview Table -->
                                        <div class="overflow-x-auto max-h-[400px] border border-gray-200 rounded-lg">
                                            <table class="w-full">
                                                <thead class="sticky top-0 bg-[#213268] text-white">
                                                    <tr>
                                                        <th class="p-3 text-left text-xs font-semibold">No</th>
                                                        <th class="p-3 text-left text-xs font-semibold">Nama Ruangan</th>
                                                        <th class="p-3 text-left text-xs font-semibold">Gedung</th>
                                                        <th class="p-3 text-left text-xs font-semibold">Lantai</th>
                                                        <th class="p-3 text-left text-xs font-semibold">Deskripsi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="room-preview-table-body">
                                                    <!-- Preview data will be inserted here -->
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Warning/Error Messages -->
                                        <div id="room-preview-warnings"
                                            class="hidden text-yellow-600 text-sm bg-yellow-50 p-4 rounded-lg">
                                            <p class="font-medium mb-2">Peringatan:</p>
                                            <ul class="list-disc pl-5" id="room-warning-list">
                                                <!-- Warning messages will be inserted here -->
                                            </ul>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex gap-3">
                                            <button type="button" id="room-back-to-upload-btn"
                                                class="w-1/3 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                                Kembali
                                            </button>
                                            <form action="{{ route('rooms.import') }}" method="POST" id="room-import-form"
                                                class="w-2/3" data-no-loading enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="excel_data" id="room_excel_data">
                                                <button type="submit" id="room-import-btn"
                                                    class="w-full h-[45px] bg-green-600 text-white rounded-lg text-base hover:bg-green-700 transform active:scale-[0.98] transition-all duration-200">
                                                    Impor Data
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if(!hasPermission('room:create'))
                    const addButtons = document.querySelectorAll('#addRoomBtn');
                    addButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                    @if(!hasPermission('room:import'))
                        const importButtons = document.querySelectorAll('#importRoomBtn');
                        importButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                    @if(!hasPermission('room:edit'))
                        const editButtons = document.querySelectorAll('.edit-room-btn');
                        editButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                    @if(!hasPermission('room:delete'))
                        const deleteButtons = document.querySelectorAll('.delete-room-btn');
                        deleteButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                window.changeRoomPerPage = function (limit) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('room_limit', limit);

                    url.searchParams.set('room_page', 1);

                    window.location.href = url.toString();
                }

                const searchInput = document.getElementById('searchInput');
                const sortOrder = document.getElementById('sortOrder');

                function applyFilters() {
                    const searchValue = searchInput?.value.trim() || '';
                    const sortValue = sortOrder?.value || '';

                    const url = new URL(window.location.href);

                    ['search', 'sort', 'room_page'].forEach(param => {
                        url.searchParams.delete(param);
                    });

                    if (searchValue) url.searchParams.set('search', searchValue);
                    if (sortValue) url.searchParams.set('sort', sortValue);

                    url.searchParams.set('room_page', 1);

                    window.location.href = url.toString();
                }

                let searchTimeout;
                searchInput?.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(applyFilters, 500);
                });

                sortOrder?.addEventListener('change', applyFilters);

                const urlParams = new URLSearchParams(window.location.search);
                if (searchInput) searchInput.value = urlParams.get('search') || '';

                const addRoomModal = document.getElementById('addRoomModal');
                const editRoomModal = document.getElementById('editRoomModal');
                const deleteRoomModal = document.getElementById('deleteRoomModal');
                const importRoomModal = document.getElementById('importRoomModal');
                const closeButtons = document.querySelectorAll('.close-modal');

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

                async function loadBuildings(searchTerm, dropdownId, listId, loadingId) {
                    const buildingList = document.getElementById(listId);
                    const loadingIndicator = document.getElementById(loadingId);

                    if (!buildingList) return;

                    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                    buildingList.innerHTML = '';

                    try {
                        const response = await fetch(`{{ route('buildings') }}${searchTerm ? '?search=' + encodeURIComponent(searchTerm) : ''}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to fetch buildings');
                        }

                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Server returned non-JSON response');
                        }

                        const result = await response.json();
                        const buildings = result.data || [];

                        buildingList.innerHTML = '';

                        if (buildings.length === 0) {
                            const noResults = document.createElement('li');
                            noResults.className = 'px-4 py-2 text-gray-500 italic';
                            noResults.textContent = 'Tidak ada gedung ditemukan';
                            buildingList.appendChild(noResults);
                        } else {
                            buildings.forEach(item => {
                                const li = document.createElement('li');
                                li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                                const buildingName = item.building_name || 'Unknown';

                                li.textContent = buildingName;
                                li.setAttribute('data-id', item.building_id);
                                li.setAttribute('data-name', buildingName);

                                li.addEventListener('click', function () {
                                    const modalId = dropdownId.includes('add') ? 'add' : 'edit';
                                    const searchInput = document.getElementById(`${modalId}_building_search`);
                                    const hiddenInput = document.getElementById(modalId === 'add' ? 'add_building_id' : 'editRoomBuilding');
                                    const dropdown = document.getElementById(dropdownId);

                                    if (searchInput && hiddenInput) {
                                        searchInput.value = this.getAttribute('data-name');
                                        hiddenInput.value = this.getAttribute('data-id');

                                        searchInput.classList.remove('border-red-500');
                                        const errorElement = searchInput.closest('.space-y-2').querySelector('.error-message');
                                        if (errorElement) errorElement.classList.add('hidden');
                                    }

                                    if (dropdown) dropdown.classList.add('hidden');
                                });

                                buildingList.appendChild(li);
                            });
                        }
                    } catch (error) {
                        console.error('Error loading buildings:', error);
                        const errorItem = document.createElement('li');
                        errorItem.className = 'px-4 py-2 text-red-500';
                        errorItem.textContent = 'Error loading buildings: ' + error.message;
                        buildingList.innerHTML = '';
                        buildingList.appendChild(errorItem);
                    } finally {
                        if (loadingIndicator) loadingIndicator.classList.add('hidden');
                    }
                }

                function initBuildingDropdowns() {
                    const addBuildingSearch = document.getElementById('add_building_search');
                    const addBuildingDropdown = document.getElementById('add_building_dropdown');
                    const addBuildingList = document.getElementById('add_building_list');

                    const editBuildingSearch = document.getElementById('edit_building_search');
                    const editBuildingDropdown = document.getElementById('edit_building_dropdown');
                    const editBuildingList = document.getElementById('edit_building_list');

                    if (addBuildingSearch && addBuildingDropdown) {
                        addBuildingSearch.addEventListener('focus', function () {
                            addBuildingDropdown.classList.remove('hidden');
                            if (addBuildingList.children.length === 0) {
                                loadBuildings('', 'add_building_dropdown', 'add_building_list', 'add_building_loading');
                            }
                        });

                        let addBuildingTimeout;
                        addBuildingSearch.addEventListener('input', function () {
                            clearTimeout(addBuildingTimeout);
                            addBuildingTimeout = setTimeout(() => {
                                loadBuildings(this.value, 'add_building_dropdown', 'add_building_list', 'add_building_loading');
                            }, 300);
                        });
                    }

                    if (editBuildingSearch && editBuildingDropdown) {
                        editBuildingSearch.addEventListener('focus', function () {
                            editBuildingDropdown.classList.remove('hidden');
                            if (editBuildingList.children.length === 0) {
                                loadBuildings('', 'edit_building_dropdown', 'edit_building_list', 'edit_building_loading');
                            }
                        });

                        let editBuildingTimeout;
                        editBuildingSearch.addEventListener('input', function () {
                            clearTimeout(editBuildingTimeout);
                            editBuildingTimeout = setTimeout(() => {
                                loadBuildings(this.value, 'edit_building_dropdown', 'edit_building_list', 'edit_building_loading');
                            }, 300);
                        });
                    }

                    document.addEventListener('click', function (e) {
                        if (addBuildingSearch && addBuildingDropdown &&
                            !addBuildingSearch.contains(e.target) &&
                            !addBuildingDropdown.contains(e.target)) {
                            addBuildingDropdown.classList.add('hidden');
                        }

                        if (editBuildingSearch && editBuildingDropdown &&
                            !editBuildingSearch.contains(e.target) &&
                            !editBuildingDropdown.contains(e.target)) {
                            editBuildingDropdown.classList.add('hidden');
                        }
                    });
                }

                const addRoomBtn = document.getElementById('addRoomBtn');
                if (addRoomBtn) {
                    addRoomBtn.addEventListener('click', () => {
                        openModal(addRoomModal, addRoomModal.querySelector('[id$="ModalContent"]'));
                    });
                }

                document.querySelectorAll('.edit-room-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const roomId = button.getAttribute('data-id');
                        document.getElementById('editRoomForm').action = `{{ url('rooms/update') }}/${roomId}`;
                        document.getElementById('editRoomId').value = roomId;

                        document.getElementById('editRoomName').value = '';
                        document.getElementById('edit_building_search').value = '';
                        document.getElementById('editRoomBuilding').value = '';
                        document.getElementById('editRoomFloor').value = '';
                        document.getElementById('editRoomDescription').value = '';

                        const formContent = document.getElementById('editRoomFormContent');
                        if (formContent) {
                            formContent.classList.add('opacity-50');
                            const loader = document.createElement('div');
                            loader.id = 'editFormLoader';
                            loader.className = 'absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 z-10';
                            loader.innerHTML = `
                                        <div class="flex flex-col items-center">
                                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#213268] mb-4"></div>
                                            <p class="text-gray-600">Memuat data ruangan...</p>
                                        </div>
                                    `;
                            formContent.parentNode.appendChild(loader);
                        }

                        openModal(editRoomModal, editRoomModal.querySelector('[id$="ModalContent"]'));

                        const requestUrl = `{{ url('/rooms') }}/${roomId}`;
                        console.log('Fetching room data from:', requestUrl);

                        fetch(requestUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(response => {
                                console.log('Response status:', response.status);
                                if (!response.ok) {
                                    throw new Error(`Failed to fetch room data: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Room data received:', data);

                                const loader = document.getElementById('editFormLoader');
                                if (loader) loader.remove();

                                if (formContent) formContent.classList.remove('opacity-50');

                                if (!data.success) {
                                    throw new Error(data.message || 'Failed to fetch room data');
                                }

                                if (!data.data || typeof data.data !== 'object') {
                                    throw new Error('Invalid room data received from server');
                                }

                                const room = data.data;

                                try {
                                    if (!room.room_name) console.warn('Room name is missing in fetched data');
                                    if (!room.building_id) console.warn('Building ID is missing in fetched data');
                                    if (!room.floor_number) console.warn('Floor number is missing in fetched data');

                                    document.getElementById('editRoomName').value = room.room_name || '';
                                    document.getElementById('editRoomBuilding').value = room.building_id || '';
                                    document.getElementById('editRoomFloor').value = room.floor_number || '';
                                    document.getElementById('editRoomDescription').value = room.description || '';

                                    console.log('Form populated successfully with room data');

                                    fetch(`{{ route('buildings') }}`, {
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    })
                                        .then(response => {
                                            if (!response.ok) {
                                                throw new Error('Failed to fetch building data');
                                            }
                                            return response.json();
                                        })
                                        .then(buildingData => {
                                            if (!buildingData.success) {
                                                console.warn('Building data fetch was not successful:', buildingData.message || 'Unknown error');
                                                return;
                                            }

                                            if (buildingData.success && buildingData.data) {
                                                const building = buildingData.data.find(b => b.building_id == room.building_id);
                                                if (building) {
                                                    document.getElementById('edit_building_search').value = building.building_name || '';
                                                }
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error fetching building details:', error);
                                        });
                                } catch (error) {
                                    console.error('Error populating form with room data:', error);
                                    showToast('Warning: Some room data could not be loaded properly.', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching room:', error);
                                showToast('Error fetching room data: ' + error.message, 'error');

                                const loader = document.getElementById('editFormLoader');
                                if (loader) loader.remove();
                                if (formContent) formContent.classList.remove('opacity-50');

                                closeModal(editRoomModal, editRoomModal.querySelector('[id$="ModalContent"]'));
                            });
                    });
                });

                document.querySelectorAll('.delete-room-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const roomId = button.getAttribute('data-id');
                        document.getElementById('deleteRoomForm').action = `{{ url('rooms/delete') }}/${roomId}`;
                        document.getElementById('deleteRoomId').value = roomId;

                        openModal(deleteRoomModal, deleteRoomModal.querySelector('[id$="ModalContent"]'));
                    });
                });

                const importRoomBtn = document.getElementById('importRoomBtn');
                if (importRoomBtn) {
                    importRoomBtn.addEventListener('click', () => {
                        openModal(importRoomModal, importRoomModal.querySelector('[id$="ModalContent"]'));
                    });
                }

                function clearModalForms(modal) {
                    if (!modal) return;

                    const forms = modal.querySelectorAll('form');

                    forms.forEach(form => {
                        form.reset();

                        const inputs = form.querySelectorAll('input, select, textarea');
                        inputs.forEach(input => {
                            input.classList.remove('border-red-500');
                            const errorElement = input.closest('.space-y-2')?.querySelector('.error-message');
                            if (errorElement) errorElement.classList.add('hidden');
                        });
                    });

                    if (modal.id === 'importRoomModal') {
                        const fileInput = modal.querySelector('#room_excel_file');
                        if (fileInput) fileInput.value = '';

                        const fileNameContainer = modal.querySelector('#room-excel-file-name');
                        if (fileNameContainer) fileNameContainer.classList.add('hidden');

                        const previewBtn = modal.querySelector('#room-preview-btn');
                        if (previewBtn) previewBtn.disabled = true;

                        const errorDiv = modal.querySelector('#room-excel-error');
                        if (errorDiv) errorDiv.classList.add('hidden');

                        document.getElementById('import-room-step-1')?.classList.remove('hidden');
                        document.getElementById('import-room-step-2')?.classList.add('hidden');
                    }

                    if (modal.id === 'editRoomModal') {
                        const buildingSearchInput = document.getElementById('edit_building_search');
                        const buildingIdInput = document.getElementById('editRoomBuilding');

                        if (buildingSearchInput) buildingSearchInput.value = '';
                        if (buildingIdInput) buildingIdInput.value = '';

                        const dropdown = document.getElementById('edit_building_dropdown');
                        if (dropdown) dropdown.classList.add('hidden');
                    }

                    if (modal.id === 'addRoomModal') {
                        const buildingSearchInput = document.getElementById('add_building_search');
                        const buildingIdInput = document.getElementById('add_building_id');

                        if (buildingSearchInput) buildingSearchInput.value = '';
                        if (buildingIdInput) buildingIdInput.value = '';

                        const dropdown = document.getElementById('add_building_dropdown');
                        if (dropdown) dropdown.classList.add('hidden');
                    }
                }

                closeButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const modal = button.closest('[id$="Modal"]');
                        const content = modal.querySelector('[id$="ModalContent"]');
                        closeModal(modal, content);
                        clearModalForms(modal);
                    });
                });

                [addRoomModal, editRoomModal, deleteRoomModal, importRoomModal].forEach(modal => {
                    if (modal) {
                        modal.addEventListener('click', function (e) {
                            if (e.target === this.querySelector('.fixed.inset-0.z-50.overflow-y-auto') ||
                                e.target === this.querySelector('.fixed.inset-0.bg-black.bg-opacity-50')) {
                                const content = this.querySelector('[id$="ModalContent"]');
                                closeModal(this, content);
                                clearModalForms(this);
                            }
                        });
                    }
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        [addRoomModal, editRoomModal, deleteRoomModal, importRoomModal].forEach(modal => {
                            if (!modal.classList.contains('hidden')) {
                                const content = modal.querySelector('[id$="ModalContent"]');
                                closeModal(modal, content);
                                clearModalForms(modal);
                            }
                        });
                    }
                });

                initBuildingDropdowns();

                const addRoomForm = document.getElementById('addRoomForm');
                if (addRoomForm) {
                    addRoomForm.addEventListener('submit', function (event) {
                        event.preventDefault();

                        const roomNameInput = document.getElementById('add_room_name');
                        const buildingInput = document.getElementById('add_building_id');
                        const buildingSearchInput = document.getElementById('add_building_search');
                        const floorInput = document.getElementById('add_floor_number');

                        const isRoomNameValid = validateField(roomNameInput);
                        const isBuildingValid = validateBuildingField(buildingInput, buildingSearchInput);
                        const isFloorValid = validateField(floorInput);

                        if (!isRoomNameValid || !isBuildingValid || !isFloorValid) {
                            showToast('Silakan isi semua field yang diperlukan', 'error');
                            return false;
                        }

                        this.submit();
                    });
                }

                const editRoomForm = document.getElementById('editRoomForm');
                if (editRoomForm) {
                    editRoomForm.addEventListener('submit', function (event) {
                        event.preventDefault();

                        const roomNameInput = document.getElementById('editRoomName');
                        const buildingInput = document.getElementById('editRoomBuilding');
                        const buildingSearchInput = document.getElementById('edit_building_search');
                        const floorInput = document.getElementById('editRoomFloor');

                        const isRoomNameValid = validateField(roomNameInput);
                        const isBuildingValid = validateBuildingField(buildingInput, buildingSearchInput);
                        const isFloorValid = validateField(floorInput);

                        if (!isRoomNameValid || !isBuildingValid || !isFloorValid) {
                            showToast('Silakan isi semua field yang diperlukan', 'error');
                            return false;
                        }

                        this.submit();
                    });
                }

                function validateBuildingField(hiddenInput, searchInput) {
                    let errorElement = searchInput.closest('.space-y-2').querySelector('.error-message');

                    if (!hiddenInput.value) {
                        searchInput.classList.add('border-red-500');
                        if (errorElement) {
                            errorElement.textContent = 'Gedung harus dipilih';
                            errorElement.classList.remove('hidden');
                        }
                        return false;
                    } else {
                        searchInput.classList.remove('border-red-500');
                        if (errorElement) errorElement.classList.add('hidden');
                        return true;
                    }
                }

                function validateField(field) {
                    let errorElement = field.closest('.space-y-2').querySelector('.error-message');
                    let fieldName = field.getAttribute('placeholder').replace('Ketik ', '').replace('ruangan', '').trim();

                    if (field.tagName.toLowerCase() === 'select') {
                        if (!field.value) {
                            field.classList.add('border-red-500');
                            if (errorElement) {
                                errorElement.textContent = fieldName + ' harus dipilih';
                                errorElement.classList.remove('hidden');
                            }
                            return false;
                        } else {
                            field.classList.remove('border-red-500');
                            if (errorElement) errorElement.classList.add('hidden');
                            return true;
                        }
                    } else {
                        if (!field.value.trim()) {
                            field.classList.add('border-red-500');
                            if (errorElement) {
                                errorElement.textContent = fieldName + ' harus diisi';
                                errorElement.classList.remove('hidden');
                            }
                            return false;
                        } else {
                            field.classList.remove('border-red-500');
                            if (errorElement) errorElement.classList.add('hidden');
                            return true;
                        }
                    }
                }

                const addRoomFields = [
                    document.getElementById('add_room_name'),
                    document.getElementById('add_floor_number')
                ];

                addRoomFields.forEach(field => {
                    if (field) {
                        field.addEventListener('blur', function () {
                            validateField(this);
                        });

                        field.addEventListener('input', function () {
                            if (this.classList.contains('border-red-500')) {
                                validateField(this);
                            }
                        });
                    }
                });

                const addBuildingSearch = document.getElementById('add_building_search');
                const addBuildingId = document.getElementById('add_building_id');

                if (addBuildingSearch && addBuildingId) {
                    addBuildingSearch.addEventListener('blur', function () {
                        validateBuildingField(addBuildingId, this);
                    });
                }

                const editRoomFields = [
                    document.getElementById('editRoomName'),
                    document.getElementById('editRoomFloor')
                ];

                editRoomFields.forEach(field => {
                    if (field) {
                        field.addEventListener('blur', function () {
                            validateField(this);
                        });

                        field.addEventListener('input', function () {
                            if (this.classList.contains('border-red-500')) {
                                validateField(this);
                            }
                        });
                    }
                });

                const editBuildingSearch = document.getElementById('edit_building_search');
                const editBuildingId = document.getElementById('editRoomBuilding');

                if (editBuildingSearch && editBuildingId) {
                    editBuildingSearch.addEventListener('blur', function () {
                        validateBuildingField(editBuildingId, this);
                    });
                }

                let roomFormAjax = document.getElementById('addRoomForm');
                if (roomFormAjax) {
                    roomFormAjax.addEventListener('submit', function (e) {
                        e.preventDefault();

                        const roomNameInput = document.getElementById('add_room_name');
                        const buildingInput = document.getElementById('add_building_id');
                        const buildingSearchInput = document.getElementById('add_building_search');
                        const floorInput = document.getElementById('add_floor_number');

                        const isRoomNameValid = validateField(roomNameInput);
                        const isBuildingValid = validateBuildingField(buildingInput, buildingSearchInput);
                        const isFloorValid = validateField(floorInput);

                        if (!isRoomNameValid || !isBuildingValid || !isFloorValid) {
                            showToast('Silakan isi semua field yang diperlukan', 'error');
                            return false;
                        }

                        const formData = new FormData(this);
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalBtnText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `
                                    <div class="flex items-center justify-center">
                                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                        <span>Memproses...</span>
                                    </div>
                                `;

                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                            .then(response => {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json().then(data => {
                                        data.status = response.status;
                                        return data;
                                    });
                                } else {
                                    throw new Error('Invalid response format');
                                }
                            })
                            .then(data => {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;

                                if (data.success === true || (data.status >= 200 && data.status < 300)) {
                                    const modal = document.getElementById('addRoomModal');
                                    closeModal(modal, modal.querySelector('[id$="ModalContent"]'));

                                    window.location.reload();
                                } else {
                                    if (data.errors) {
                                        document.querySelectorAll('.error-message').forEach(el => {
                                            el.classList.add('hidden');
                                        });

                                        if (typeof data.errors === 'object') {
                                            Object.keys(data.errors).forEach(key => {
                                                let field;
                                                let errorElement;

                                                if (key === 'room_name') {
                                                    field = document.getElementById('add_room_name');
                                                } else if (key === 'building_id') {
                                                    field = document.getElementById('add_building_search');
                                                } else if (key === 'floor_number') {
                                                    field = document.getElementById('add_floor_number');
                                                }

                                                if (field) {
                                                    field.classList.add('border-red-500');
                                                    errorElement = field.closest('.space-y-2').querySelector('.error-message');

                                                    if (errorElement) {
                                                        const errorMsg = Array.isArray(data.errors[key]) ?
                                                            data.errors[key][0] : data.errors[key];

                                                        errorElement.textContent = errorMsg;
                                                        errorElement.classList.remove('hidden');
                                                    }
                                                }
                                            });
                                        }
                                    }
                                }
                            })
                            .catch(error => {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;

                                window.location.reload();
                            });
                    });
                }

                let editRoomFormAjax = document.getElementById('editRoomForm');
                if (editRoomFormAjax) {
                    editRoomFormAjax.addEventListener('submit', function (e) {
                        e.preventDefault();

                        const roomNameInput = document.getElementById('editRoomName');
                        const buildingInput = document.getElementById('editRoomBuilding');
                        const buildingSearchInput = document.getElementById('edit_building_search');
                        const floorInput = document.getElementById('editRoomFloor');

                        const isRoomNameValid = validateField(roomNameInput);
                        const isBuildingValid = validateBuildingField(buildingInput, buildingSearchInput);
                        const isFloorValid = validateField(floorInput);

                        if (!isRoomNameValid || !isBuildingValid || !isFloorValid) {
                            showToast('Silakan isi semua field yang diperlukan', 'error');
                            return false;
                        }

                        const formData = new FormData(this);
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalBtnText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `
                                    <div class="flex items-center justify-center">
                                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                        <span>Memproses...</span>
                                    </div>
                                `;

                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                            .then(response => {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json().then(data => {
                                        data.status = response.status;
                                        return data;
                                    });
                                } else {
                                    throw new Error('Invalid response format');
                                }
                            })
                            .then(data => {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;

                                if (data.success === true || (data.status >= 200 && data.status < 300)) {
                                    const modal = document.getElementById('editRoomModal');
                                    closeModal(modal, modal.querySelector('[id$="ModalContent"]'));

                                    window.location.reload();
                                } else {
                                    if (data.errors) {
                                        document.querySelectorAll('.error-message').forEach(el => {
                                            el.classList.add('hidden');
                                        });

                                        if (typeof data.errors === 'object') {
                                            Object.keys(data.errors).forEach(key => {
                                                let field;
                                                let errorElement;

                                                if (key === 'room_name') {
                                                    field = document.getElementById('editRoomName');
                                                } else if (key === 'building_id') {
                                                    field = document.getElementById('edit_building_search');
                                                } else if (key === 'floor_number') {
                                                    field = document.getElementById('editRoomFloor');
                                                }

                                                if (field) {
                                                    field.classList.add('border-red-500');
                                                    errorElement = field.closest('.space-y-2').querySelector('.error-message');

                                                    if (errorElement) {
                                                        const errorMsg = Array.isArray(data.errors[key]) ?
                                                            data.errors[key][0] : data.errors[key];

                                                        errorElement.textContent = errorMsg;
                                                        errorElement.classList.remove('hidden');
                                                    }
                                                }
                                            });
                                        }
                                    }
                                }
                            })
                            .catch(error => {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;

                                window.location.reload();
                            });
                    });
                }

                const roomImportForm = document.getElementById('room-import-form');
                if (roomImportForm) {
                    roomImportForm.addEventListener('submit', function (e) {
                        e.preventDefault();

                        const formData = new FormData(this);

                        const originalFileInput = document.getElementById('room_excel_file');
                        if (originalFileInput && originalFileInput.files.length > 0) {
                            formData.append('excel_file', originalFileInput.files[0]);
                        }

                        const importBtn = document.getElementById('room-import-btn');
                        const originalBtnText = importBtn.innerHTML;
                        importBtn.disabled = true;
                        importBtn.innerHTML = `
                                    <div class="flex items-center justify-center">
                                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                        <span>Memproses...</span>
                                    </div>
                                `;

                        fetch('{{ route('rooms.import') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                            .then(response => {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json().then(data => {
                                        data.status = response.status;
                                        return data;
                                    });
                                } else {
                                    throw new Error('Invalid response format');
                                }
                            })
                            .then(data => {
                                importBtn.disabled = false;
                                importBtn.innerHTML = originalBtnText;

                                if (data.success === true) {
                                    console.log('Import successful:', data);

                                    const modal = document.getElementById('importRoomModal');
                                    closeModal(modal, modal.querySelector('[id$="ModalContent"]'));

                                    showToast(data.message || 'Ruangan berhasil diimpor!', 'success');

                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                } else {
                                    console.error('Import error:', data);

                                    let errorMessage = data.message || 'Terjadi kesalahan selama pengimporan.';
                                    let errorDetails = [];

                                    if (data.data && data.data.errors) {
                                        console.log('Server returned detailed errors:', data.data.errors);

                                        if (Array.isArray(data.data.errors)) {
                                            data.data.errors.forEach(error => {
                                                if (typeof error === 'string') {
                                                    errorDetails.push(error);
                                                } else if (error.message) {
                                                    errorDetails.push(error.message);
                                                } else if (error.room_name && error.reason) {
                                                    errorDetails.push(`"${error.room_name}" - ${error.reason}`);
                                                } else if (error.row && error.reason) {
                                                    errorDetails.push(`Baris ${error.row}: ${error.reason}`);
                                                } else if (error.reason) {
                                                    errorDetails.push(error.reason);
                                                }
                                            });
                                        }
                                    }

                                    if (errorDetails.length > 0) {
                                        errorMessage = `${errorMessage}<ul class="mt-2 ml-4 list-disc">`;
                                        errorDetails.forEach(detail => {
                                            errorMessage += `<li>${detail}</li>`;
                                        });
                                        errorMessage += '</ul>';
                                    }

                                    showToast(errorMessage, 'error');
                                }
                            })
                            .catch(error => {
                                importBtn.disabled = false;
                                importBtn.innerHTML = originalBtnText;

                                console.error('Import fetch error:', error);

                                let errorMessage = 'Terjadi kesalahan yang tidak diketahui. Silakan coba lagi.';

                                if (error.response) {
                                    try {
                                        error.response.json().then(data => {
                                            if (data.message) {
                                                errorMessage = data.message;

                                                if (data.errors) {
                                                    errorMessage += '<ul class="mt-2 ml-4 list-disc">';
                                                    if (typeof data.errors === 'object') {
                                                        Object.values(data.errors).flat().forEach(err => {
                                                            errorMessage += `<li>${err}</li>`;
                                                        });
                                                    } else if (Array.isArray(data.errors)) {
                                                        data.errors.forEach(err => {
                                                            errorMessage += `<li>${err}</li>`;
                                                        });
                                                    }
                                                    errorMessage += '</ul>';
                                                }

                                                showToast(errorMessage, 'error');
                                            }
                                        }).catch(() => {
                                            showToast(`Error: ${error.response.statusText || errorMessage}`, 'error');
                                        });
                                    } catch (e) {
                                        showToast(errorMessage, 'error');
                                    }
                                } else {
                                    showToast(error.message || errorMessage, 'error');
                                }
                            });
                    });
                }

                const roomExcelFile = document.getElementById('room_excel_file');
                const roomFileNameContainer = document.getElementById('room-excel-file-name');
                const roomFileNameText = document.getElementById('room-file-name-text');
                const removeRoomExcel = document.getElementById('remove-room-excel');
                const roomPreviewBtn = document.getElementById('room-preview-btn');
                const roomExcelError = document.getElementById('room-excel-error');
                const roomExcelLoading = document.getElementById('room-excel-loading');

                if (roomExcelFile) {
                    roomExcelFile.addEventListener('change', function (e) {
                        if (roomExcelError) roomExcelError.classList.add('hidden');

                        if (this.files && this.files[0]) {
                            const file = this.files[0];
                            const fileExt = file.name.split('.').pop().toLowerCase();

                            if (!['xlsx', 'xls', 'csv'].includes(fileExt)) {
                                if (roomExcelError) {
                                    roomExcelError.textContent = 'Tipe file tidak valid. Silakan unggah file Excel (.xlsx, .xls) atau CSV.';
                                    roomExcelError.classList.remove('hidden');
                                }
                                this.value = '';
                                if (roomFileNameContainer) roomFileNameContainer.classList.add('hidden');
                                if (roomPreviewBtn) roomPreviewBtn.disabled = true;
                                return;
                            }

                            if (roomFileNameText) roomFileNameText.textContent = file.name;
                            if (roomFileNameContainer) roomFileNameContainer.classList.remove('hidden');
                            if (roomPreviewBtn) roomPreviewBtn.disabled = false;
                        } else {
                            if (roomFileNameContainer) roomFileNameContainer.classList.add('hidden');
                            if (roomPreviewBtn) roomPreviewBtn.disabled = true;
                        }
                    });
                }

                if (removeRoomExcel) {
                    removeRoomExcel.addEventListener('click', function () {
                        if (roomExcelFile) roomExcelFile.value = '';
                        if (roomFileNameContainer) roomFileNameContainer.classList.add('hidden');
                        if (roomPreviewBtn) roomPreviewBtn.disabled = true;
                        if (roomExcelError) roomExcelError.classList.add('hidden');
                    });
                }

                if (roomPreviewBtn) {
                    roomPreviewBtn.addEventListener('click', function () {
                        if (!roomExcelFile || !roomExcelFile.files || !roomExcelFile.files[0]) {
                            if (roomExcelError) {
                                roomExcelError.textContent = 'Silakan pilih file terlebih dahulu.';
                                roomExcelError.classList.remove('hidden');
                            }
                            return;
                        }

                        const file = roomExcelFile.files[0];

                        if (roomExcelLoading) roomExcelLoading.classList.remove('hidden');
                        if (roomExcelError) roomExcelError.classList.add('hidden');

                        const reader = new FileReader();

                        reader.onload = function (e) {
                            try {
                                const data = new Uint8Array(e.target.result);
                                const workbook = XLSX.read(data, { type: 'array' });

                                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];

                                const rows = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

                                if (rows.length < 2) {
                                    throw new Error('File tidak memiliki data atau header yang hilang.');
                                }

                                processRoomExcelData(rows);

                                if (roomExcelLoading) roomExcelLoading.classList.add('hidden');

                                document.getElementById('import-room-step-1').classList.add('hidden');
                                document.getElementById('import-room-step-2').classList.remove('hidden');
                            } catch (error) {
                                console.error('Excel parsing error:', error);
                                if (roomExcelLoading) roomExcelLoading.classList.add('hidden');
                                if (roomExcelError) {
                                    roomExcelError.textContent = 'Gagal memproses file: ' + error.message;
                                    roomExcelError.classList.remove('hidden');
                                }
                            }
                        };

                        reader.onerror = function () {
                            console.error('FileReader error:', reader.error);
                            if (roomExcelLoading) roomExcelLoading.classList.add('hidden');
                            if (roomExcelError) {
                                roomExcelError.textContent = 'Gagal membaca file. Silakan coba file lainnya.';
                                roomExcelError.classList.remove('hidden');
                            }
                        };

                        reader.readAsArrayBuffer(file);
                    });
                }

                const roomBackBtn = document.getElementById('room-back-to-upload-btn');
                if (roomBackBtn) {
                    roomBackBtn.addEventListener('click', function () {
                        document.getElementById('import-room-step-2').classList.add('hidden');
                        document.getElementById('import-room-step-1').classList.remove('hidden');
                    });
                }

                function processRoomExcelData(data) {
                    const headers = data[0];
                    const rows = data.slice(1).filter(row => row.length > 0 && row.some(cell => cell !== null && cell !== ''));
                    const headerMap = {};
                    headers.forEach((header, index) => {
                        if (header) {
                            const normalizedHeader = String(header).toLowerCase().trim()
                                .replace(/\s+/g, '_')
                                .replace(/[^a-z0-9_]/g, '');
                            headerMap[normalizedHeader] = index;
                        }
                    });

                    const previewData = [];
                    const warnings = [];

                    rows.forEach((row, rowIndex) => {
                        const item = {};

                        const getValue = (possibleNames) => {
                            for (const name of possibleNames) {
                                const normalizedName = name.toLowerCase().trim()
                                    .replace(/\s+/g, '_')
                                    .replace(/[^a-z0-9_]/g, '');

                                if (headerMap[normalizedName] !== undefined) {
                                    return row[headerMap[normalizedName]];
                                }
                            }
                            return null;
                        };

                        item.room_name = getValue(['room_name', 'name', 'nama_ruangan', 'nama ruangan', 'ruangan']);
                        item.building_name = getValue(['building_name', 'building', 'gedung', 'nama_gedung', 'nama gedung']);
                        item.floor_number = getValue(['floor_number', 'floor', 'lantai', 'nomor_lantai', 'nomor lantai']);
                        item.description = getValue(['description', 'desc', 'deskripsi', 'notes']);

                        if (!item.room_name) {
                            warnings.push(`Row ${rowIndex + 2}: Nama Ruangan tidak boleh kosong`);
                        }

                        if (!item.building_name) {
                            warnings.push(`Row ${rowIndex + 2}: Gedung tidak boleh kosong`);
                        }

                        if (!item.floor_number) {
                            warnings.push(`Row ${rowIndex + 2}: Lantai tidak boleh kosong`);
                        }

                        item._rowNum = rowIndex + 2;

                        previewData.push(item);
                    });

                    const roomNameMap = {};
                    previewData.forEach(item => {
                        if (item.room_name && item.building_name) {
                            const key = `${item.building_name}|${item.room_name.toLowerCase()}`;
                            if (!roomNameMap[key]) {
                                roomNameMap[key] = [];
                            }
                            roomNameMap[key].push(item._rowNum);
                        }
                    });

                    Object.entries(roomNameMap).forEach(([key, rows]) => {
                        if (rows.length > 1) {
                            const [buildingName, roomName] = key.split('|');
                            warnings.push(`Ruangan duplikat "${roomName}" untuk Gedung "${buildingName}" ditemukan di baris: ${rows.join(', ')}`);
                        }
                    });

                    document.getElementById('room_excel_data').value = JSON.stringify(previewData);

                    showRoomDataPreview(previewData, warnings);
                }

                function showRoomDataPreview(data, warnings) {
                    const previewTableBody = document.getElementById('room-preview-table-body');
                    const previewCount = document.getElementById('room-preview-count');
                    const warningsContainer = document.getElementById('room-preview-warnings');
                    const warningsList = document.getElementById('room-warning-list');

                    if (!previewTableBody || !previewCount) return;

                    previewTableBody.innerHTML = '';
                    if (warningsList) warningsList.innerHTML = '';
                    if (warningsContainer) warningsContainer.classList.add('hidden');

                    previewCount.textContent = `${data.length} item ditemukan`;

                    data.forEach((item, index) => {
                        const row = document.createElement('tr');
                        row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';

                        const indexCell = document.createElement('td');
                        indexCell.className = 'p-3 text-xs border-t border-[#EEF1F4]';
                        indexCell.textContent = index + 1;
                        row.appendChild(indexCell);

                        const fields = ['room_name', 'building_name', 'floor_number', 'description'];

                        fields.forEach(field => {
                            const cell = document.createElement('td');
                            cell.className = 'p-3 text-xs border-t border-[#EEF1F4]';
                            cell.textContent = item[field] || '-';
                            row.appendChild(cell);
                        });

                        previewTableBody.appendChild(row);
                    });

                    if (warnings && warnings.length > 0 && warningsList && warningsContainer) {
                        warnings.forEach(warning => {
                            const li = document.createElement('li');
                            li.textContent = warning;
                            warningsList.appendChild(li);
                        });
                        warningsContainer.classList.remove('hidden');

                        const importBtn = document.getElementById('room-import-btn');
                        const hasCriticalWarnings = warnings.some(warning =>
                            warning.includes('Nama Ruangan tidak boleh kosong') ||
                            warning.includes('Gedung tidak boleh kosong') ||
                            warning.includes('Lantai tidak boleh kosong')
                        );

                        if (importBtn && hasCriticalWarnings) {
                            importBtn.disabled = true;
                            importBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        } else if (importBtn) {
                            importBtn.disabled = false;
                            importBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    }
                }

                // Column header sorting
                const sortByNameHeader = document.getElementById('sortByName');
                if (sortByNameHeader) {
                    sortByNameHeader.addEventListener('click', function() {
                        const currentSort = '{{ request()->query("sort") }}';
                        let newSort;

                        if (currentSort === 'name_asc') {
                            newSort = 'name_desc';
                        } else {
                            newSort = 'name_asc';
                        }

                        const url = new URL(window.location.href);
                        url.searchParams.set('sort', newSort);
                        url.searchParams.set('room_page', 1);
                        window.location.href = url.toString();
                    });
                }

                const sortByFloorHeader = document.getElementById('sortByFloor');
                if (sortByFloorHeader) {
                    sortByFloorHeader.addEventListener('click', function() {
                        const currentSort = '{{ request()->query("sort") }}';
                        let newSort;

                        if (currentSort === 'floor_asc') {
                            newSort = 'floor_desc';
                        } else {
                            newSort = 'floor_asc';
                        }

                        const url = new URL(window.location.href);
                        url.searchParams.set('sort', newSort);
                        url.searchParams.set('room_page', 1);
                        window.location.href = url.toString();
                    });
                }
            });
        </script>
    @endpush

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
@endsection
