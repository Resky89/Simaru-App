@extends('Layout.app')

@section('title', 'Rooms')

@section('content')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Room Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">Ruangan</h1>

                        <!-- Button Add Room -->
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
                    </div>

                    <!-- Room Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[15%]">ID Ruangan
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Ruangan</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Gedung</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Lantai</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Deskripsi</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Aksi
                                    </th>
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
                                                    data-id="{{ $room['room_id'] }}" data-name="{{ $room['room_name'] }}"
                                                    data-building-id="{{ $room['building_id'] }}"
                                                    data-floor-number="{{ $room['floor_number'] }}"
                                                    data-description="{{ $room['description'] ?? '' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button class="text-[#3D3D3D] hover:text-red-500 delete-room-btn"
                                                    data-id="{{ $room['room_id'] }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
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
                            <a href="{{ $roomPagination['prev_page_url'] ?? '#' }}"
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
                            <a href="{{ $roomPagination['next_page_url'] ?? '#' }}"
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
                            <form id="addRoomForm" action="{{ route('rooms.store') }}" method="POST">
                                @csrf
                                <div class="space-y-4 max-w-[400px] mx-auto">
                                    <!-- Room Name Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Nama Ruangan</label>
                                        <input type="text" name="room_name"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik nama ruangan" required>
                                    </div>

                                    <!-- Building Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Gedung</label>
                                        <select name="building_id"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            required>
                                            <option value="" disabled selected>Pilih gedung</option>
                                            @foreach($buildings as $building)
                                                <option value="{{ $building['building_id'] }}">{{ $building['building_name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Floor Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Lantai</label>
                                        <input type="text" name="floor_number"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik lantai ruangan" required>
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

        <!-- Edit Room Modal -->
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
                        <div class="p-6">
                            <form id="editRoomForm" action="" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="editRoomId" name="room_id">
                                <div class="space-y-4 max-w-[400px] mx-auto">
                                    <!-- Room Name Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Nama Ruangan</label>
                                        <input type="text" id="editRoomName" name="room_name"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik nama ruangan" required>
                                    </div>

                                    <!-- Building Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Gedung</label>
                                        <select id="editRoomBuilding" name="building_id"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            required>
                                            <option value="" disabled>Pilih gedung</option>
                                            @foreach($buildings as $building)
                                                <option value="{{ $building['building_id'] }}">{{ $building['building_name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Floor Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Lantai</label>
                                        <input type="text" id="editRoomFloor" name="floor_number"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik lantai ruangan" required>
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

        <!-- Delete Room Confirmation Modal -->
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
                        <form id="deleteRoomForm" action="" method="POST">
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

        <!-- Success and Error Notifications -->
        @if(session('success'))
            <div id="successNotification"
                class="fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50"
                role="alert">
                <div class="flex items-center">
                    <div class="py-1">
                        <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Berhasil!</p>
                        <p>{{ session('success') }}</p>
                    </div>
                    <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                </div>
            </div>

            <script>
                setTimeout(function () {
                    const notification = document.getElementById('successNotification');
                    if (notification) {
                        notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                        setTimeout(function () {
                            notification.remove();
                        }, 500);
                    }
                }, 5000); // Hide after 5 seconds
            </script>
        @endif

        @if(session('error'))
            <div id="errorNotification"
                class="fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md z-50"
                role="alert">
                <div class="flex items-center">
                    <div class="py-1">
                        <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Gagal!</p>
                        <p>{{ session('error') }}</p>
                    </div>
                    <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                </div>
            </div>

            <script>
                setTimeout(function () {
                    const notification = document.getElementById('errorNotification');
                    if (notification) {
                        notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                        setTimeout(function () {
                            notification.remove();
                        }, 500);
                    }
                }, 5000); // Hide after 5 seconds
            </script>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Function to change items per page for rooms
                window.changeRoomPerPage = function (limit) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('room_limit', limit);
                    window.location.href = url.toString();
                }

                // Toast container
                const toastContainer = document.createElement('div');
                toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-4';
                document.body.appendChild(toastContainer);

                // Get all modal elements
                const addRoomModal = document.getElementById('addRoomModal');
                const editRoomModal = document.getElementById('editRoomModal');
                const deleteRoomModal = document.getElementById('deleteRoomModal');
                const closeButtons = document.querySelectorAll('.close-modal');

                // Function to open modal
                function openModal(modal, content) {
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                        content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                    }, 10);
                }

                // Function to close modal
                function closeModal(modal, content) {
                    content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                    content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                    }, 300);
                }

                // Add Room Modal
                document.getElementById('addRoomBtn').addEventListener('click', () => {
                    openModal(addRoomModal, addRoomModal.querySelector('[id$="ModalContent"]'));
                });

                // Edit Room Modal
                document.querySelectorAll('.edit-room-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const roomId = button.getAttribute('data-id');
                        document.getElementById('editRoomForm').action = `{{ url('rooms/update') }}/${roomId}`;
                        document.getElementById('editRoomId').value = roomId;
                        document.getElementById('editRoomName').value = button.getAttribute('data-name');
                        document.getElementById('editRoomBuilding').value = button.getAttribute('data-building-id');
                        document.getElementById('editRoomFloor').value = button.getAttribute('data-floor-number');
                        document.getElementById('editRoomDescription').value = button.getAttribute('data-description') || '';

                        openModal(editRoomModal, editRoomModal.querySelector('[id$="ModalContent"]'));
                    });
                });

                // Delete Room Modal
                document.querySelectorAll('.delete-room-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const roomId = button.getAttribute('data-id');
                        document.getElementById('deleteRoomForm').action = `{{ url('rooms/delete') }}/${roomId}`;
                        document.getElementById('deleteRoomId').value = roomId;

                        openModal(deleteRoomModal, deleteRoomModal.querySelector('[id$="ModalContent"]'));
                    });
                });

                // Modal close handlers
                closeButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const modal = button.closest('[id$="Modal"]');
                        const content = modal.querySelector('[id$="ModalContent"]');
                        closeModal(modal, content);
                    });
                });

                // Close on outside click
                [addRoomModal, editRoomModal, deleteRoomModal].forEach(modal => {
                    modal.addEventListener('click', function (e) {
                        if (e.target === this.querySelector('.fixed.inset-0.z-50.overflow-y-auto') ||
                            e.target === this.querySelector('.fixed.inset-0.bg-black.bg-opacity-50')) {
                            const content = this.querySelector('[id$="ModalContent"]');
                            closeModal(this, content);
                        }
                    });
                });

                // Close on Escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        [addRoomModal, editRoomModal, deleteRoomModal].forEach(modal => {
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
