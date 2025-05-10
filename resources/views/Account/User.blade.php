@extends('Layout.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- User Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PENGGUNA</h1>

                        <!-- Button Add User -->
                        <button id="addUserBtn"
                            class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                                <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                            </svg>
                            <span class="text-base">Tambah Pengguna</span>
                        </button>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput" placeholder="Cari berdasarkan nomor pegawai..."
                                class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <select id="statusFilter"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" selected>Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                            </select>

                            <select id="sortOrder"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" selected>Urutan Default</option>
                                <option value="id_asc">Terlama</option>
                                <option value="id_desc">Terbaru</option>
                            </select>
                        </div>
                    </div>

                    <!-- User Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nomor Pegawai</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Peran</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Status</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users ?? [] as $user)
                                    <tr>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $user['employee_number'] ?? '-' }}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            @if(isset($user['roles']) && is_array($user['roles']))
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($user['roles'] as $role)
                                                        <span class="px-2 py-1 bg-gray-100 rounded-full text-xs">
                                                            {{ $role['role_name'] ?? '-' }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            <span
                                                class="px-2 py-1 rounded text-xs {{ ($user['is_active'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ($user['is_active'] ?? false) ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            <div class="flex items-center space-x-2 justify-center">
                                                <button class="edit-user-btn p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors"
                                                    data-user-id="{{ $user['user_id'] }}"
                                                    data-employee-number="{{ $user['employee_number'] }}"
                                                    data-role-ids="{{ isset($user['roles']) ? json_encode(array_column($user['roles'], 'role_id')) : '[]' }}"
                                                    data-is-active="{{ $user['is_active'] ? 'true' : 'false' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button class="delete-user-btn p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors"
                                                    data-user-id="{{ $user['user_id'] }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada pengguna ditemukan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination for USERS section -->
                    <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ $user_pagination['prev_page_url'] ?? '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($user_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Sebelumnya
                            </a>
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $user_pagination['current_page'] ?? 1;
                                    $lastPage = $user_pagination['last_page'] ?? 1;
                                    $maxPagesShown = 5; // Show max 5 pages at once
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($lastPage, $startPage + $maxPagesShown - 1);

                                    if ($endPage - $startPage + 1 < $maxPagesShown) {
                                        $startPage = max(1, $endPage - $maxPagesShown + 1);
                                    }
                                @endphp

                                @if($startPage > 1)
                                    <a href="{{ request()->fullUrlWithQuery(['user_page' => 1]) }}"
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
                                    <a href="{{ request()->fullUrlWithQuery(['user_page' => $i]) }}"
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
                                    <a href="{{ request()->fullUrlWithQuery(['user_page' => $lastPage]) }}"
                                        class="h-8 w-8 flex items-center justify-center border border-[#D8DAE5] text-[#213268] rounded">
                                        {{ $lastPage }}
                                    </a>
                                @endif
                            </div>
                            <a href="{{ $user_pagination['next_page_url'] ?? '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($user_pagination['current_page'] ?? 1) >= ($user_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}">
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
                                @if(isset($user_pagination) && is_array($user_pagination))
                                    @php
                                        $currentPage = $user_pagination['current_page'] ?? 1;
                                        $perPage = $user_pagination['per_page'] ?? 10;
                                        $total = $user_pagination['total'] ?? count($users ?? []);
                                        $from = ($currentPage - 1) * $perPage + 1;
                                        $to = min($currentPage * $perPage, $total);
                                    @endphp
                                    Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} entri
                                @else
                                    Menampilkan 1 sampai {{ count($users ?? []) }} dari {{ count($users ?? []) }} entri
                                @endif
                            </span>
                            <select id="userPerPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changeUserPerPage(this.value)">
                                <option value="10" {{ isset($user_pagination['per_page']) && $user_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                                <option value="25" {{ isset($user_pagination['per_page']) && $user_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                                <option value="50" {{ isset($user_pagination['per_page']) && $user_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- All modals should be outside the main content section -->
        <!-- Add User Modal -->
        <div id="addUserModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="addUserModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">TAMBAH PENGGUNA</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="addUserModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="addUserForm" action="{{ route('users.store') }}" method="POST">
                                @csrf
                                <div class="space-y-4 max-w-[400px] mx-auto">
                                    <!-- Employee Number Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Nomor Pegawai</label>
                                        <input type="text" name="employee_number"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Masukkan nomor pegawai" required>
                                    </div>

                                    <!-- Password Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Kata Sandi</label>
                                        <input type="password" name="password"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Masukkan kata sandi" required autocomplete="new-password">
                                    </div>

                                    <!-- Roles Selection for Add User Modal -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Peran</label>
                                        <div class="relative">
                                            <input type="text" id="add-roles-input" placeholder="Cari peran..."
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                autocomplete="off">
                                            <div id="add-roles-dropdown" class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                <!-- Loading indicator and role options will be added dynamically -->
                                            </div>
                                        </div>
                                        <div id="add-role-hidden-inputs"></div>
                                        <div id="add-selected-roles-display" class="flex flex-wrap gap-2 mt-2"></div>
                                    </div>

                                    <!-- Active Status -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Status</label>
                                        <select name="is_active"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                            <option value="1" selected>Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </div>

                                    <!-- Button Group -->
                                    <div class="pt-4">
                                        <button type="submit"
                                            class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
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

        <!-- Edit User Modal -->
        <div id="editUserModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="editUserModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT PENGGUNA</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="editUserModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="editUserForm" action="" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="space-y-4 max-w-[400px] mx-auto">
                                    <!-- Employee Number Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Nomor Pegawai</label>
                                        <input type="text" id="edit_employee_number" name="employee_number"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Masukkan nomor pegawai" required>
                                    </div>

                                    <!-- Roles Selection for Edit User Modal -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Peran</label>
                                        <div class="relative">
                                            <input type="text" id="edit-roles-input" placeholder="Cari peran..."
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                autocomplete="off">
                                            <div id="edit-roles-dropdown" class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                <!-- Loading indicator and role options will be added dynamically -->
                                            </div>
                                        </div>
                                        <div id="edit-role-hidden-inputs"></div>
                                        <div id="edit-selected-roles-display" class="flex flex-wrap gap-2 mt-2"></div>
                                    </div>

                                    <!-- Active Status -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Status</label>
                                        <select id="edit_is_active" name="is_active"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </div>

                                    <!-- Button Group -->
                                    <div class="pt-4">
                                        <button type="submit"
                                            class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                            Perbarui
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete User Modal -->
        <div id="deleteUserModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="deleteUserModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">HAPUS PENGGUNA</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="deleteUserModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <form id="deleteUserForm" action="" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="p-6">
                                <div class="space-y-6 max-w-[400px] mx-auto">
                                    <div class="flex flex-col items-center">
                                        <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus
                                            pengguna ini? Tindakan ini tidak dapat dibatalkan.</p>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="button"
                                            class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200"
                                            data-modal="deleteUserModal">
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to change items per page for users
            window.changeUserPerPage = function (limit) {
                const url = new URL(window.location.href);
                url.searchParams.set('user_limit', limit);
                window.location.href = url.toString();
            }

            // Set up role search and selection for both modals
            setupRoleSearch('add-roles-input', 'add-roles-dropdown', 'add-selected-roles-display', 'add-role-hidden-inputs');
            setupRoleSearch('edit-roles-input', 'edit-roles-dropdown', 'edit-selected-roles-display', 'edit-role-hidden-inputs');

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                // Set initial value from URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                searchInput.value = urlParams.get('search') || '';

                // Add debounce for search
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(applyFilters, 500);
                });
            }

            // Function to apply all filters and sorting
            function applyFilters() {
                const searchTerm = document.getElementById('searchInput').value;
                const statusFilter = document.getElementById('statusFilter').value;
                const sortOrder = document.getElementById('sortOrder').value;

                // Construct URL with filters
                const url = new URL(window.location.href);

                // Set search parameter
                if (searchTerm) url.searchParams.set('search', searchTerm);
                else url.searchParams.delete('search');

                // Set status parameter
                if (statusFilter) url.searchParams.set('status', statusFilter);
                else url.searchParams.delete('status');

                // Set sort parameter
                if (sortOrder) url.searchParams.set('sort', sortOrder);
                else url.searchParams.delete('sort');

                // Reset to page 1 when filters change
                url.searchParams.set('user_page', 1);

                // Navigate to the new URL
                window.location.href = url.toString();
            }

            // Add event listeners for dropdown filters
            const statusFilterSelect = document.getElementById('statusFilter');
            if (statusFilterSelect) {
                // Set initial value from URL
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('status')) {
                    statusFilterSelect.value = urlParams.get('status');
                }

                statusFilterSelect.addEventListener('change', applyFilters);
            }

            // Add event listener for sort order
            const sortOrderSelect = document.getElementById('sortOrder');
            if (sortOrderSelect) {
                // Set initial value from URL
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('sort')) {
                    sortOrderSelect.value = urlParams.get('sort');
                }

                sortOrderSelect.addEventListener('change', applyFilters);
            }

            // Function to set up role search
            function setupRoleSearch(inputId, dropdownId, displayContainerId, hiddenInputsId) {
                const input = document.getElementById(inputId);
                const dropdown = document.getElementById(dropdownId);
                const displayContainer = document.getElementById(displayContainerId);
                const hiddenInputsContainer = document.getElementById(hiddenInputsId);

                if (!input || !dropdown || !displayContainer || !hiddenInputsContainer) return;

                const inputContainer = input.closest('.relative');

                // Store selected roles
                const selectedRoles = new Map();

                // Create loading indicator
                const loadingIndicator = document.createElement('div');
                loadingIndicator.className = 'flex justify-center py-2';
                loadingIndicator.innerHTML = `
                    <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                `;
                loadingIndicator.id = `${inputId}-loading`;
                loadingIndicator.style.display = 'none';
                dropdown.appendChild(loadingIndicator);

                // Create roles list container
                const rolesListContainer = document.createElement('div');
                rolesListContainer.id = `${inputId}-list`;
                rolesListContainer.className = 'max-h-56 overflow-y-auto';
                dropdown.appendChild(rolesListContainer);

                // Ensure dropdown has proper z-index and positioning
                dropdown.style.zIndex = '50';

                // Create the custom input container that will hold the selected roles and the actual input
                const customInputContainer = document.createElement('div');
                customInputContainer.className = 'flex flex-wrap items-start gap-1 w-full h-full p-2 overflow-auto';

                // Move the input into the new container
                const parent = input.parentNode;
                input.classList.add('flex-grow');
                input.classList.add('min-w-[80px]');
                input.classList.add('outline-none');
                input.classList.add('bg-transparent');
                input.style.boxShadow = 'none';
                input.style.border = 'none';
                input.style.padding = '0';
                input.style.margin = '0';
                input.style.height = 'auto';

                // Create a wrapper that will replace the input
                const wrapper = document.createElement('div');
                wrapper.className = 'w-full min-h-[45px] h-auto max-h-[200px] overflow-y-auto px-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus-within:outline-none focus-within:border-[#213268] focus-within:ring-2 focus-within:ring-[#213268] focus-within:ring-opacity-20 transition-all duration-200';

                // Replace input with wrapper and move input inside the custom container
                parent.replaceChild(wrapper, input);
                wrapper.appendChild(customInputContainer);
                customInputContainer.appendChild(input);

                // Debounce function for search
                function debounce(func, wait) {
                    let timeout;
                    return function() {
                        const context = this, args = arguments;
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(context, args), wait);
                    };
                }

                // Show dropdown when input is focused
                input.addEventListener('focus', function() {
                    dropdown.classList.remove('hidden');
                    filterAndDisplayRoles('');
                    wrapper.classList.add('border-[#213268]', 'ring-2', 'ring-[#213268]', 'ring-opacity-20');
                });

                // Also handle click on input to show dropdown (helps with mobile)
                input.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent event from bubbling to wrapper
                    dropdown.classList.remove('hidden');
                    filterAndDisplayRoles('');
                });

                // Remove focus styles when clicking outside
                document.addEventListener('click', function(e) {
                    if (!wrapper.contains(e.target) && !dropdown.contains(e.target)) {
                        wrapper.classList.remove('border-[#213268]', 'ring-2', 'ring-[#213268]', 'ring-opacity-20');
                    }
                });

                // Hide dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!wrapper.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });

                // Filter options while typing with debounce
                input.addEventListener('input', debounce(function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    filterAndDisplayRoles(searchTerm);
                }, 300));

                // Add backspace functionality to remove the last selected role
                input.addEventListener('keydown', function(e) {
                    // If backspace key is pressed and input is empty
                    if (e.key === 'Backspace' && this.value === '') {
                        // Get the last role ID from the selected roles
                        if (selectedRoles.size > 0) {
                            // Convert Map to array and get the last key
                            const roleIds = Array.from(selectedRoles.keys());
                            const lastRoleId = roleIds[roleIds.length - 1];

                            // Delete the last role
                            selectedRoles.delete(lastRoleId);

                            // Update the UI
                            renderSelectedRoles();

                            // Show dropdown with updated options
                            dropdown.classList.remove('hidden');
                            filterAndDisplayRoles('');

                            // Focus on input
                            input.focus();

                            // Prevent default backspace behavior
                            e.preventDefault();
                        }
                    }
                });

                // Function to filter and display roles
                function filterAndDisplayRoles(searchTerm) {
                    // Show loading indicator
                    loadingIndicator.style.display = 'flex';
                    rolesListContainer.innerHTML = '';

                    // Get all available roles
                    const roles = @json($roles['data'] ?? []);

                    // Filter roles based on search term and exclude already selected roles
                    let filteredRoles = roles.filter(role => {
                        // Skip already selected roles
                        if (selectedRoles.has(role.role_id.toString())) {
                            return false;
                        }

                        // If there's a search term, match against it
                        if (searchTerm) {
                            return role.role_name.toLowerCase().includes(searchTerm);
                        }

                        // If no search term, include all non-selected roles
                        return true;
                    });

                    // Hide loading indicator
                    loadingIndicator.style.display = 'none';

                    // If no roles match search
                    if (filteredRoles.length === 0) {
                        const noResults = document.createElement('div');
                        noResults.className = 'p-2 text-center text-gray-500 italic';
                        noResults.textContent = 'Tidak ada peran ditemukan';
                        rolesListContainer.appendChild(noResults);
                        return;
                    }

                    // Display filtered roles
                    filteredRoles.forEach(role => {
                        const option = document.createElement('div');
                        option.className = 'p-2 hover:bg-gray-50 role-option cursor-pointer';
                        option.setAttribute('data-role-id', role.role_id);
                        option.setAttribute('data-role-name', role.role_name);
                        option.setAttribute('data-target', inputId.split('-')[0]);

                        option.innerHTML = `<span class="text-sm text-gray-700">${role.role_name}</span>`;

                        option.addEventListener('click', function() {
                            const roleId = this.getAttribute('data-role-id');
                            const roleName = this.getAttribute('data-role-name');

                            // Add role if not already selected
                            if (!selectedRoles.has(roleId)) {
                                selectedRoles.set(roleId, roleName);
                                renderSelectedRoles();
                            }

                            // Clear input and hide dropdown
                            input.value = '';

                            // Focus back on input for more selections
                            input.focus();

                            // Make sure dropdown remains visible for additional selections
                            dropdown.classList.remove('hidden');

                            // Refilter to show remaining options
                            filterAndDisplayRoles('');
                        });

                        rolesListContainer.appendChild(option);
                    });
                }

                // Render selected roles
                function renderSelectedRoles() {
                    // Clear container except for the input
                    Array.from(customInputContainer.children).forEach(child => {
                        if (child !== input) {
                            customInputContainer.removeChild(child);
                        }
                    });

                    // Clear hidden inputs
                    hiddenInputsContainer.innerHTML = '';

                    // Group badges into rows to maximize space
                    const badgesContainer = document.createElement('div');
                    badgesContainer.className = 'flex flex-wrap gap-1 w-full';
                    customInputContainer.insertBefore(badgesContainer, input);

                    // Add badges and hidden inputs for each selected role
                    selectedRoles.forEach((roleName, roleId) => {
                        // Create badge
                        const badge = document.createElement('div');
                        badge.className = 'inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded-md text-xs my-1';
                        badge.innerHTML = `
                            <span>${roleName}</span>
                            <span class="cursor-pointer hover:text-red-500 font-medium" data-role-id="${roleId}">×</span>
                        `;

                        // Remove badge when clicking the x
                        badge.querySelector('span:last-child').addEventListener('click', function(e) {
                            e.stopPropagation(); // Prevent bubbling up to the container click handler
                            const roleId = this.getAttribute('data-role-id');
                            selectedRoles.delete(roleId);
                            renderSelectedRoles();

                            // Focus back on input
                            input.focus();

                            // Refilter to show this option again
                            filterAndDisplayRoles(input.value.toLowerCase().trim());
                        });

                        // Add badge to the container
                        badgesContainer.appendChild(badge);

                        // Create hidden input for form submission
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'role_ids[]';
                        hiddenInput.value = roleId;
                        hiddenInputsContainer.appendChild(hiddenInput);
                    });

                    // Add a flex-break after badges for cleaner layout
                    if (selectedRoles.size > 0) {
                        const breakDiv = document.createElement('div');
                        breakDiv.className = 'w-full flex-basis-100';
                        customInputContainer.insertBefore(breakDiv, input);
                    }

                    // Dynamically adjust height based on content
                    adjustWrapperHeight();
                }

                // Function to adjust wrapper height based on content
                function adjustWrapperHeight() {
                    // Reset to default height first
                    wrapper.style.height = '';

                    // Get the content height
                    const contentHeight = customInputContainer.scrollHeight;

                    // Set new height, respecting min and max heights
                    if (contentHeight < 45) {
                        wrapper.style.height = '45px';
                    } else if (contentHeight > 200) {
                        wrapper.style.height = '200px';
                        wrapper.style.overflowY = 'auto';
                    } else {
                        wrapper.style.height = contentHeight + 'px';
                        wrapper.style.overflowY = 'visible';
                    }

                    // Ensure the input is visible (scroll to it if needed)
                    if (selectedRoles.size > 0) {
                        input.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }

                // Listen for window resize to adjust height
                window.addEventListener('resize', debounce(adjustWrapperHeight, 100));

                // Allow container click to focus the input
                wrapper.addEventListener('click', function(e) {
                    input.focus();
                });

                // Public method to set selected roles (for edit form)
                window[`set${inputId.split('-')[0].charAt(0).toUpperCase() + inputId.split('-')[0].slice(1)}SelectedRoles`] = function(roleIds, roleNames) {
                    // Clear existing selections
                    selectedRoles.clear();

                    // Add new selections
                    if (Array.isArray(roleIds) && Array.isArray(roleNames) && roleIds.length === roleNames.length) {
                        roleIds.forEach((id, index) => {
                            selectedRoles.set(id.toString(), roleNames[index]);
                        });
                    }

                    renderSelectedRoles();
                };
            }

            // Modified setupEditUserForm function to handle role names
            window.setupEditUserForm = function (userId, employeeNumber, roleIds, isActive) {
                const form = document.getElementById('editUserForm');
                if (!form) return;

                form.action = `{{ url('user/update') }}/${userId}`;

                // Set employee number
                const employeeInput = document.getElementById('edit_employee_number');
                if (employeeInput) employeeInput.value = employeeNumber;

                // Set is_active value
                const activeSelect = document.getElementById('edit_is_active');
                if (activeSelect) activeSelect.value = (isActive === 'true') ? '1' : '0';

                // Get role names for the selected role IDs
                const roles = @json($roles['data'] ?? []);
                const roleNames = [];

                if (Array.isArray(roleIds) && roleIds.length > 0 && Array.isArray(roles)) {
                    roleIds.forEach(roleId => {
                        const role = roles.find(r => r.role_id.toString() === roleId.toString());
                        if (role) {
                            roleNames.push(role.role_name);
                        }
                    });
                }

                // Set selected roles - this calls the function created in setupRoleSearch
                if (typeof setEditSelectedRoles === 'function') {
                    setEditSelectedRoles(roleIds, roleNames);
                }

                // Open the modal
                const modal = document.getElementById('editUserModal');
                const content = document.getElementById('editUserModalContent');
                if (modal && content) {
                    openModal(modal, content);
                }
            };

            // Reset role selection when opening Add User modal
            document.getElementById('addUserBtn').addEventListener('click', () => {
                window.setAddSelectedRoles([], []);
            });

            // Toast container
            const toastContainer = document.createElement('div');
            toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-4';
            document.body.appendChild(toastContainer);

            // Get all modal elements
            const addUserModal = document.getElementById('addUserModal');
            const editUserModal = document.getElementById('editUserModal');
            const deleteUserModal = document.getElementById('deleteUserModal');
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

                // Hide any open dropdowns
                document.getElementById('add-roles-dropdown').classList.add('hidden');
                document.getElementById('edit-roles-dropdown').classList.add('hidden');
            }

            // Edit User Modal
            document.querySelectorAll('.edit-user-btn').forEach(button => {
                button.addEventListener('click', function () {
                    let userId = '';
                    let employeeNumber = '';
                    let roleIds = [];
                    let isActive = 'false';

                    try {
                        userId = this.getAttribute('data-user-id') || '';
                        employeeNumber = this.getAttribute('data-employee-number') || '';

                        // Safely parse the JSON data
                        const roleIdsStr = this.getAttribute('data-role-ids') || '[]';
                        roleIds = JSON.parse(roleIdsStr);

                        isActive = this.getAttribute('data-is-active') || 'false';
                    } catch (error) {
                        console.error('Error processing button data:', error);
                    }

                    // Use the global function to set up the form
                    window.setupEditUserForm(userId, employeeNumber, roleIds, isActive);
                });
            });

            // Delete User Modal
            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', function() {
                    try {
                        const userId = this.getAttribute('data-user-id') || '';
                        const deleteForm = document.getElementById('deleteUserForm');
                        if (deleteForm) {
                            deleteForm.action = `{{ url('user/delete') }}/${userId}`;
                        }

                        const modal = document.getElementById('deleteUserModal');
                        const content = modal?.querySelector('[id$="ModalContent"]');
                        if (modal && content) {
                            openModal(modal, content);
                        }
                    } catch (error) {
                        console.error('Error in delete user button:', error);
                    }
                });
            });

            // Close Modal Handlers
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    try {
                        const modalId = button.getAttribute('data-modal');
                        if (!modalId) return;

                        const modal = document.getElementById(modalId);
                        if (!modal) return;

                        const content = modal.querySelector('[id$="ModalContent"]');
                        if (!content) return;

                        closeModal(modal, content);
                    } catch (error) {
                        console.error('Error closing modal:', error);
                    }
                });
            });

            // Close on outside click
            [addUserModal, editUserModal, deleteUserModal].forEach(modal => {
                if (!modal) return;

                modal.addEventListener('click', function (e) {
                    try {
                        // Check if the click is directly on the modal's overlay area
                        const overlayArea = this.querySelector('.fixed.inset-0.z-50.overflow-y-auto');
                        const bgOverlay = this.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');

                        if (e.target === overlayArea || e.target === bgOverlay) {
                            const content = this.querySelector('[id$="ModalContent"]');
                            if (content) {
                                closeModal(this, content);
                            }
                        }
                    } catch (error) {
                        console.error('Error in modal outside click handler:', error);
                    }
                });
            });

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    try {
                        [addUserModal, editUserModal, deleteUserModal].forEach(modal => {
                            if (modal && !modal.classList.contains('hidden')) {
                                const content = modal.querySelector('[id$="ModalContent"]');
                                if (content) {
                                    closeModal(modal, content);
                                }
                            }
                        });
                    } catch (error) {
                        console.error('Error in Escape key handler:', error);
                    }
                }
            });

            // Show toast notification
            window.showToast = function (message, type = 'info') {
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
                    titleP.textContent = 'Berhasil';
                } else if (type === 'error') {
                    bgColor = 'bg-red-100';
                    borderColor = 'border-red-500';
                    textColor = 'text-red-700';
                    icon = `<svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>`;
                    titleP.textContent = 'Kesalahan';
                } else {
                    bgColor = 'bg-blue-100';
                    borderColor = 'border-blue-500';
                    textColor = 'text-blue-700';
                    icon = `<svg class="h-6 w-6 text-blue-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>`;
                    titleP.textContent = type === 'info' ? 'Informasi' : type.charAt(0).toUpperCase() + type.slice(1);
                }

                // Simple HTML detection
                const hasHTML = typeof message === 'string' && message.indexOf('<') !== -1 && message.indexOf('>') !== -1;

                toast.className = `${bgColor} border-l-4 ${borderColor} ${textColor} p-4 rounded shadow-md z-50 opacity-0 transition-opacity duration-300`;
                toast.setAttribute('role', 'alert');

                // Create simple content structure
                const content = document.createElement('div');
                content.className = 'flex items-start';

                // Add icon
                const iconDiv = document.createElement('div');
                iconDiv.className = 'py-1 flex-shrink-0';
                iconDiv.innerHTML = icon;
                content.appendChild(iconDiv);

                // Add message content
                const messageDiv = document.createElement('div');
                messageDiv.className = 'flex-grow';

                const titleP = document.createElement('p');
                titleP.className = 'font-bold';
                titleP.textContent = type.charAt(0).toUpperCase() + type.slice(1);
                messageDiv.appendChild(titleP);

                const messageP = document.createElement('div');
                messageP.className = 'error-message';
                if (hasHTML && typeof message === 'string') {
                    messageP.innerHTML = message;
                } else {
                    messageP.textContent = message;
                }
                messageDiv.appendChild(messageP);
                content.appendChild(messageDiv);

                // Add close button
                const closeBtn = document.createElement('span');
                closeBtn.className = 'ml-4 cursor-pointer flex-shrink-0';
                closeBtn.textContent = '×';
                closeBtn.onclick = function() {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                };
                content.appendChild(closeBtn);

                // Add content to toast
                toast.appendChild(content);

                // Add to container
                toastContainer.appendChild(toast);

                // Animate in
                setTimeout(() => {
                    toast.classList.remove('opacity-0');
                    toast.classList.add('opacity-100');
                }, 10);

                // Remove after 5 seconds
                setTimeout(() => {
                    if (toast && toast.parentNode) {
                        toast.classList.remove('opacity-100');
                        toast.classList.add('opacity-0');
                        setTimeout(() => {
                            if (toast && toast.parentNode === toastContainer) {
                                toastContainer.removeChild(toast);
                            }
                        }, 300);
                    }
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

            // Show toast notifications for session messages on page load
            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif
        });
    </script>
@endsection
