@extends('Layout.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- User Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PENGGUNA</h1>

                        <!-- Button Add User -->
                        @if(hasPermission('user:create'))
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
                        @endif
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput" placeholder="Cari berdasarkan nomor pegawai..."
                                value="{{ $search ?? '' }}"
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
                                <option value="" {{ !isset($filters['status']) || $filters['status'] === '' ? 'selected' : '' }}>Semua Status</option>
                                <option value="active" {{ isset($filters['status']) && $filters['status'] === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ isset($filters['status']) && $filters['status'] === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>

                            <select id="sortOrder"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" {{ ($sort ?? '') == '' ? 'selected' : '' }}>Urutan Default</option>
                                <option value="id_asc" {{ ($sort ?? '') == 'id_asc' ? 'selected' : '' }}>Terlama</option>
                                <option value="id_desc" {{ ($sort ?? '') == 'id_desc' ? 'selected' : '' }}>Terbaru</option>
                            </select>
                        </div>
                    </div>

                    <!-- User Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nomor Pegawai</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Pegawai</th>
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
                                            {{ $user['employee_name'] ?? '-' }}
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
                                                @if(hasPermission('user:edit'))
                                                    <button
                                                        class="edit-user-btn p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors"
                                                        data-user-id="{{ $user['user_id'] }}"
                                                        data-employee-number="{{ $user['employee_number'] }}"
                                                        data-employee-name="{{ $user['employee_name'] }}"
                                                        data-role-ids="{{ isset($user['roles']) ? json_encode(array_column($user['roles'], 'role_id')) : '[]' }}"
                                                        data-role-names="{{ isset($user['roles']) ? json_encode(array_column($user['roles'], 'role_name')) : '[]' }}"
                                                        data-is-active="{{ $user['is_active'] ? 'true' : 'false' }}"
                                                        title="Edit Pengguna">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                @if(hasPermission('user:delete'))
                                                    <button
                                                        class="delete-user-btn p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors"
                                                        data-user-id="{{ $user['user_id'] }}"
                                                        title="Hapus Pengguna">
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
                                        <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada
                                            pengguna ditemukan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination for USERS section -->
                    <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ $users_pagination['prev_page_url'] ?? '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($users_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Sebelumnya
                            </a>
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $users_pagination['current_page'] ?? 1;
                                    $lastPage = $users_pagination['last_page'] ?? 1;
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
                            <a href="{{ $users_pagination['next_page_url'] ?? '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($users_pagination['current_page'] ?? 1) >= ($users_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}">
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
                                @if(isset($users_pagination) && is_array($users_pagination))
                                    @php
                                        $currentPage = $users_pagination['current_page'] ?? 1;
                                        $perPage = $users_pagination['per_page'] ?? 10;
                                        $total = $users_pagination['total'] ?? count($users ?? []);
                                        $from = ($currentPage - 1) * $perPage + 1;
                                        $to = min($currentPage * $perPage, $total);
                                    @endphp
                                    Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} data
                                @else
                                    Menampilkan 1 sampai {{ count($users ?? []) }} dari {{ count($users ?? []) }} entri
                                @endif
                            </span>
                            <select id="userPerPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changeUserPerPage(this.value)">
                                <option value="10" {{ isset($users_pagination['per_page']) && $users_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per halaman</option>
                                <option value="25" {{ isset($users_pagination['per_page']) && $users_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per halaman</option>
                                <option value="50" {{ isset($users_pagination['per_page']) && $users_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per halaman</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add User Modal -->
        @if(hasPermission('user:create'))
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
                                <form id="addUserForm" action="{{ route('users.store') }}" method="POST" novalidate
                                    data-no-loading>
                                    @csrf
                                    <div class="space-y-4 max-w-[400px] mx-auto">
                                        <!-- Employee Number Input -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Nomor Pegawai <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="employee_number"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Masukkan nomor pegawai">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nomor pegawai harus
                                                diisi</div>
                                        </div>

                                        <!-- Employee Name Input -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Nama Pegawai <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="employee_name"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Masukkan nama pegawai">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nama pegawai harus
                                                diisi</div>
                                        </div>

                                        <!-- Password Input -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Kata Sandi <span class="text-red-500">*</span>
                                            </label>
                                            <input type="password" name="password"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Masukkan kata sandi" autocomplete="new-password">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Kata sandi harus diisi
                                            </div>
                                        </div>

                                        <!-- Roles Selection for Add User Modal -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Peran <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="text" id="add-roles-input" placeholder="Cari peran..."
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    autocomplete="off">
                                                <div id="add-roles-dropdown"
                                                    class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                    <!-- Loading indicator and role options will be added dynamically -->
                                                </div>
                                            </div>
                                            <div id="add-role-hidden-inputs"></div>
                                            <div id="add-selected-roles-display" class="flex flex-wrap gap-2 mt-2"></div>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Setidaknya satu peran
                                                harus dipilih</div>
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
        @endif

        <!-- Edit User Modal -->
        @if(hasPermission('user:edit'))
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
                                <form id="editUserForm" action="" method="POST" novalidate data-no-loading>
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-4 max-w-[400px] mx-auto">
                                        <!-- Employee Number Input -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Nomor Pegawai <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="edit_employee_number" name="employee_number"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Masukkan nomor pegawai">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nomor pegawai harus
                                                diisi</div>
                                        </div>

                                        <!-- Employee Name Input -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Nama Pegawai <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="edit_employee_name" name="employee_name"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Masukkan nama pegawai">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nama pegawai harus
                                                diisi</div>
                                        </div>

                                        <!-- Roles Selection for Edit User Modal -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">
                                                Peran <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="text" id="edit-roles-input" placeholder="Cari peran..."
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    autocomplete="off">
                                                <div id="edit-roles-dropdown"
                                                    class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                    <!-- Loading indicator and role options will be added dynamically -->
                                                </div>
                                            </div>
                                            <div id="edit-role-hidden-inputs"></div>
                                            <div id="edit-selected-roles-display" class="flex flex-wrap gap-2 mt-2"></div>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Setidaknya satu peran
                                                harus dipilih</div>
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
        @endif

        <!-- Delete User Modal -->
        @if(hasPermission('user:delete'))
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
                            <form id="deleteUserForm" action="" method="POST" data-no-loading>
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
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(!hasPermission('user:create'))
                const addButtons = document.querySelectorAll('#addUserBtn');
                addButtons.forEach(btn => {
                    if (btn) {
                        btn.style.display = 'none';
                    }
                });
            @endif

                @if(!hasPermission('user:edit'))
                    const editButtons = document.querySelectorAll('.edit-user-btn');
                    editButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                @if(!hasPermission('user:delete'))
                    const deleteButtons = document.querySelectorAll('.delete-user-btn');
                    deleteButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const sortOrder = document.getElementById('sortOrder');

            function applyFilters() {
                const searchValue = searchInput?.value.trim() || '';
                const statusValue = statusFilter?.value || '';
                const sortValue = sortOrder?.value || '';

                const url = new URL(window.location.href);

                ['search', 'status', 'sort', 'page'].forEach(param => {
                    url.searchParams.delete(param);
                });

                if (searchValue) url.searchParams.set('search', searchValue);
                if (statusValue) url.searchParams.set('status', statusValue);
                if (sortValue) url.searchParams.set('sort', sortValue);

                url.searchParams.set('page', 1);

                window.location.href = url.toString();
            }

            let searchTimeout;
            searchInput?.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 500);
            });

            statusFilter?.addEventListener('change', applyFilters);

            sortOrder?.addEventListener('change', applyFilters);

            const urlParams = new URLSearchParams(window.location.search);
            if (searchInput) searchInput.value = urlParams.get('search') || '';
            if (statusFilter) {
                const statusValue = urlParams.get('status');
                if (statusValue) {
                    statusFilter.value = statusValue;
                }
            }
            if (sortOrder) {
                const sortValue = urlParams.get('sort');
                if (sortValue) {
                    sortOrder.value = sortValue;
                }
            }

            window.changeUserPerPage = function (limit) {
                const url = new URL(window.location.href);
                url.searchParams.set('limit', limit);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            }

            setupRoleSearch('add-roles-input', 'add-roles-dropdown', 'add-selected-roles-display', 'add-role-hidden-inputs');
            setupRoleSearch('edit-roles-input', 'edit-roles-dropdown', 'edit-selected-roles-display', 'edit-role-hidden-inputs');

            let cachedRoles = new Map();

            function fetchRoles(searchTerm = '', roleIds = [], callback) {
                let queryParams = new URLSearchParams();
                queryParams.append('json', 'true');
                queryParams.append('limit', '100');

                if (searchTerm) {
                    queryParams.append('search', searchTerm);
                }

                if (Array.isArray(roleIds) && roleIds.length > 0) {
                    roleIds.forEach(id => queryParams.append('ids[]', id));
                }

                const url = `/roles?${queryParams.toString()}`;

                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Server responded with status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        let roles = [];

                        if (Array.isArray(data)) {
                            roles = data;
                        } else if (data.roles && Array.isArray(data.roles)) {
                            roles = data.roles;
                        } else if (data.data && Array.isArray(data.data)) {
                            roles = data.data;
                        }

                        roles.forEach(role => {
                            cachedRoles.set(role.role_id.toString(), role);
                        });

                        callback(roles);
                    })
                    .catch(error => {
                        console.error('Error fetching roles:', error);
                        callback([]);
                    });
            }

            function setupRoleSearch(inputId, dropdownId, displayContainerId, hiddenInputsId) {
                const input = document.getElementById(inputId);
                const dropdown = document.getElementById(dropdownId);
                const displayContainer = document.getElementById(displayContainerId);
                const hiddenInputsContainer = document.getElementById(hiddenInputsId);

                if (!input || !dropdown || !displayContainer || !hiddenInputsContainer) return;

                const inputContainer = input.closest('.relative');

                const selectedRoles = new Map();

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

                const rolesListContainer = document.createElement('div');
                rolesListContainer.id = `${inputId}-list`;
                rolesListContainer.className = 'max-h-56 overflow-y-auto';
                dropdown.appendChild(rolesListContainer);

                dropdown.style.zIndex = '50';

                const customInputContainer = document.createElement('div');
                customInputContainer.className = 'flex flex-wrap items-start gap-1 w-full h-full p-2 overflow-auto';

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

                const wrapper = document.createElement('div');
                wrapper.className = 'w-full min-h-[45px] h-auto max-h-[200px] overflow-y-auto px-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus-within:outline-none focus-within:border-[#213268] focus-within:ring-2 focus-within:ring-[#213268] focus-within:ring-opacity-20 transition-all duration-200';

                parent.replaceChild(wrapper, input);
                wrapper.appendChild(customInputContainer);
                customInputContainer.appendChild(input);

                function debounce(func, wait) {
                    let timeout;
                    return function () {
                        const context = this, args = arguments;
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(context, args), wait);
                    };
                }

                input.addEventListener('focus', function () {
                    dropdown.classList.remove('hidden');
                    filterAndDisplayRoles('');
                    wrapper.classList.add('border-[#213268]', 'ring-2', 'ring-[#213268]', 'ring-opacity-20');
                });

                input.addEventListener('click', function (e) {
                    e.stopPropagation();
                    dropdown.classList.remove('hidden');
                    filterAndDisplayRoles('');
                });

                document.addEventListener('click', function (e) {
                    if (!wrapper.contains(e.target) && !dropdown.contains(e.target)) {
                        wrapper.classList.remove('border-[#213268]', 'ring-2', 'ring-[#213268]', 'ring-opacity-20');
                    }
                });

                document.addEventListener('click', function (e) {
                    if (!wrapper.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });

                input.addEventListener('input', debounce(function () {
                    const searchTerm = this.value.toLowerCase().trim();
                    filterAndDisplayRoles(searchTerm);
                }, 300));

                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace' && this.value === '') {
                        if (selectedRoles.size > 0) {
                            const roleIds = Array.from(selectedRoles.keys());
                            const lastRoleId = roleIds[roleIds.length - 1];

                            selectedRoles.delete(lastRoleId);

                            renderSelectedRoles();

                            dropdown.classList.remove('hidden');
                            filterAndDisplayRoles('');

                            input.focus();

                            e.preventDefault();
                        }
                    }
                });

                function filterAndDisplayRoles(searchTerm) {
                    loadingIndicator.style.display = 'flex';
                    rolesListContainer.innerHTML = '';

                    const selectedRoleIds = Array.from(selectedRoles.keys());

                    let cachedResults = [];

                    if (!searchTerm) {
                        cachedResults = Array.from(cachedRoles.values())
                            .filter(role => !selectedRoleIds.includes(role.role_id.toString()));
                    } else {
                        cachedResults = Array.from(cachedRoles.values())
                            .filter(role =>
                                !selectedRoleIds.includes(role.role_id.toString()) &&
                                role.role_name.toLowerCase().includes(searchTerm.toLowerCase())
                            );
                    }

                    if (cachedResults.length >= 5 && !searchTerm) {
                        renderRoleDropdown(cachedResults);
                        return;
                    }

                    fetchRoles(searchTerm, [], function (roles) {
                        const filteredRoles = roles.filter(role =>
                            !selectedRoleIds.includes(role.role_id.toString())
                        );

                        renderRoleDropdown(filteredRoles);
                    });
                }

                function renderRoleDropdown(filteredRoles) {
                    rolesListContainer.innerHTML = '';

                    loadingIndicator.style.display = 'none';

                    if (filteredRoles.length === 0) {
                        const noResults = document.createElement('div');
                        noResults.className = 'p-2 text-center text-gray-500 italic';
                        noResults.textContent = 'Tidak ada peran ditemukan';
                        rolesListContainer.appendChild(noResults);
                        return;
                    }

                    filteredRoles.forEach(role => {
                        const option = document.createElement('div');
                        option.className = 'p-2 hover:bg-gray-50 role-option cursor-pointer';
                        option.setAttribute('data-role-id', role.role_id);
                        option.setAttribute('data-role-name', role.role_name);
                        option.setAttribute('data-target', inputId.split('-')[0]);

                        option.innerHTML = `<span class="text-sm text-gray-700">${role.role_name}</span>`;

                        option.addEventListener('click', function () {
                            const roleId = this.getAttribute('data-role-id');
                            const roleName = this.getAttribute('data-role-name');

                            if (!selectedRoles.has(roleId)) {
                                selectedRoles.set(roleId, roleName);
                                renderSelectedRoles();
                            }

                            input.value = '';

                            input.focus();

                            dropdown.classList.remove('hidden');

                            filterAndDisplayRoles('');
                        });

                        rolesListContainer.appendChild(option);
                    });

                    if (filteredRoles.length > 20) {
                        const countMsg = document.createElement('div');
                        countMsg.className = 'p-2 text-center text-gray-500 text-xs';
                        countMsg.textContent = `Menampilkan ${filteredRoles.length} peran yang cocok`;
                        rolesListContainer.appendChild(countMsg);
                    }
                }

                function renderSelectedRoles() {
                    Array.from(customInputContainer.children).forEach(child => {
                        if (child !== input) {
                            customInputContainer.removeChild(child);
                        }
                    });

                    hiddenInputsContainer.innerHTML = '';

                    const badgesContainer = document.createElement('div');
                    badgesContainer.className = 'flex flex-wrap gap-1 w-full';
                    customInputContainer.insertBefore(badgesContainer, input);

                    selectedRoles.forEach((roleName, roleId) => {
                        const badge = document.createElement('div');
                        badge.className = 'inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded-md text-xs my-1';
                        badge.innerHTML = `
                                <span>${roleName}</span>
                                <span class="cursor-pointer hover:text-red-500 font-medium" data-role-id="${roleId}">×</span>
                            `;

                        badge.querySelector('span:last-child').addEventListener('click', function (e) {
                            e.stopPropagation();
                            const roleId = this.getAttribute('data-role-id');
                            selectedRoles.delete(roleId);
                            renderSelectedRoles();

                            input.focus();

                            filterAndDisplayRoles(input.value.toLowerCase().trim());

                            checkRolesValidation();
                        });

                        badgesContainer.appendChild(badge);

                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'role_ids[]';
                        hiddenInput.value = roleId;
                        hiddenInputsContainer.appendChild(hiddenInput);
                    });

                    if (selectedRoles.size > 0) {
                        const breakDiv = document.createElement('div');
                        breakDiv.className = 'w-full flex-basis-100';
                        customInputContainer.insertBefore(breakDiv, input);
                    }

                    checkRolesValidation();

                    adjustWrapperHeight();
                }

                function checkRolesValidation() {
                    const errorElement = displayContainer.closest('.space-y-2').querySelector('.error-message');
                    const hasRoles = hiddenInputsContainer.querySelectorAll('input[name="role_ids[]"]').length > 0;

                    const formSubmitted = inputContainer.closest('form').classList.contains('was-validated');

                    if (!hasRoles && formSubmitted) {
                        wrapper.classList.add('border-red-500');
                        if (errorElement) errorElement.classList.remove('hidden');
                    } else {
                        wrapper.classList.remove('border-red-500');
                        if (errorElement) errorElement.classList.add('hidden');
                    }
                }

                function adjustWrapperHeight() {
                    wrapper.style.height = '';

                    const contentHeight = customInputContainer.scrollHeight;

                    if (contentHeight < 45) {
                        wrapper.style.height = '45px';
                    } else if (contentHeight > 200) {
                        wrapper.style.height = '200px';
                        wrapper.style.overflowY = 'auto';
                    } else {
                        wrapper.style.height = contentHeight + 'px';
                        wrapper.style.overflowY = 'visible';
                    }

                    if (selectedRoles.size > 0) {
                        input.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }

                window.addEventListener('resize', debounce(adjustWrapperHeight, 100));

                wrapper.addEventListener('click', function (e) {
                    input.focus();
                });

                window[`set${inputId.split('-')[0].charAt(0).toUpperCase() + inputId.split('-')[0].slice(1)}SelectedRoles`] = function (roleIds, roleNames) {
                    selectedRoles.clear();

                    if (Array.isArray(roleIds) && Array.isArray(roleNames) && roleIds.length === roleNames.length) {
                        roleIds.forEach((id, index) => {
                            selectedRoles.set(id.toString(), roleNames[index]);

                            if (!cachedRoles.has(id.toString())) {
                                cachedRoles.set(id.toString(), {
                                    role_id: id,
                                    role_name: roleNames[index]
                                });
                            }
                        });
                    }

                    renderSelectedRoles();

                    checkRolesValidation();
                };
            }

            window.setupEditUserForm = function (userId, employeeNumber, employeeName, roleIds, isActive) {
                const form = document.getElementById('editUserForm');
                if (!form) return;

                form.action = `{{ url('user/update') }}/${userId}`;

                const employeeInput = document.getElementById('edit_employee_number');
                if (employeeInput) employeeInput.value = employeeNumber;

                const nameInput = document.getElementById('edit_employee_name');
                if (nameInput) nameInput.value = employeeName;

                const activeSelect = document.getElementById('edit_is_active');
                if (activeSelect) activeSelect.value = (isActive === 'true') ? '1' : '0';

                const modal = document.getElementById('editUserModal');
                const content = document.getElementById('editUserModalContent');
                if (modal && content) {
                    openModal(modal, content);
                }

                let roleNames = [];
                try {
                    const editButtons = document.querySelectorAll(`.edit-user-btn[data-user-id="${userId}"]`);
                    if (editButtons.length > 0) {
                        const roleNamesAttr = editButtons[0].getAttribute('data-role-names');
                        if (roleNamesAttr) {
                            roleNames = JSON.parse(roleNamesAttr);
                        }
                    }
                } catch (error) {
                    console.error("Error parsing role names:", error);
                }

                if (Array.isArray(roleNames) && roleNames.length === roleIds.length) {
                    if (typeof setEditSelectedRoles === 'function') {
                        setEditSelectedRoles(roleIds, roleNames);
                    }
                } else {
                    fetchRoles('', roleIds, function (roles) {
                        const roleMap = new Map();
                        roles.forEach(role => {
                            roleMap.set(role.role_id.toString(), role.role_name);
                        });

                        const fetchedRoleNames = roleIds.map(id =>
                            roleMap.get(id.toString()) || `Role ID: ${id}`
                        );

                        if (typeof setEditSelectedRoles === 'function') {
                            setEditSelectedRoles(roleIds, fetchedRoleNames);
                        }
                    });
                }
            };

            const addUserBtn = document.getElementById('addUserBtn');
            if (addUserBtn) {
                addUserBtn.addEventListener('click', () => {
                    window.setAddSelectedRoles([], []);

                    const addUserForm = document.getElementById('addUserForm');
                    if (addUserForm) {
                        addUserForm.classList.remove('was-validated');
                    }

                    document.querySelectorAll('#addUserModal .error-message').forEach(el => {
                        el.classList.add('hidden');
                    });

                    document.querySelectorAll('#addUserModal input, #addUserModal select').forEach(el => {
                        el.classList.remove('border-red-500');
                    });
                });
            }

            const toastContainer = document.createElement('div');
            toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-4';
            document.body.appendChild(toastContainer);

            const addUserModal = document.getElementById('addUserModal');
            const editUserModal = document.getElementById('editUserModal');
            const deleteUserModal = document.getElementById('deleteUserModal');
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

                    if (modal.id === 'addUserModal') {
                        resetForm('addUserForm');
                    } else if (modal.id === 'editUserModal') {
                        resetForm('editUserForm');
                    }
                }, 300);
            }

            function resetForm(formId) {
                const form = document.getElementById(formId);
                if (!form) return;

                form.reset();

                form.classList.remove('was-validated');

                form.querySelectorAll('input, select, textarea').forEach(field => {
                    field.classList.remove('border-red-500');
                    const errorElement = field.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });

                if (formId === 'addUserForm') {
                    setAddSelectedRoles([], []);
                    const roleWrapper = document.querySelector('#add-roles-input').closest('.relative');
                    roleWrapper.classList.remove('border-red-500');
                    const roleError = document.querySelector('#add-selected-roles-display').closest('.space-y-2').querySelector('.error-message');
                    if (roleError) roleError.classList.add('hidden');
                } else if (formId === 'editUserForm') {
                    setEditSelectedRoles([], []);
                    const roleWrapper = document.querySelector('#edit-roles-input').closest('.relative');
                    roleWrapper.classList.remove('border-red-500');
                    const roleError = document.querySelector('#edit-selected-roles-display').closest('.space-y-2').querySelector('.error-message');
                    if (roleError) roleError.classList.add('hidden');
                }
            }

            document.querySelectorAll('.edit-user-btn').forEach(button => {
                button.addEventListener('click', function () {
                    let userId = '';
                    let employeeNumber = '';
                    let employeeName = '';
                    let roleIds = [];
                    let isActive = 'false';

                    try {
                        userId = this.getAttribute('data-user-id') || '';
                        employeeNumber = this.getAttribute('data-employee-number') || '';
                        employeeName = this.getAttribute('data-employee-name') || '';

                        const roleIdsStr = this.getAttribute('data-role-ids') || '[]';
                        roleIds = JSON.parse(roleIdsStr);

                        isActive = this.getAttribute('data-is-active') || 'false';
                    } catch (error) {
                        console.error('Error processing button data:', error);
                    }

                    window.setupEditUserForm(userId, employeeNumber, employeeName, roleIds, isActive);
                });
            });

            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', function () {
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

            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const modalId = button.getAttribute('data-modal');
                    if (!modalId) return;

                    const modal = document.getElementById(modalId);
                    if (!modal) return;

                    const content = modal.querySelector('[id$="ModalContent"]');
                    if (!content) return;

                    closeModal(modal, content);
                });
            });

            [addUserModal, editUserModal, deleteUserModal].forEach(modal => {
                if (!modal) return;

                modal.addEventListener('click', function (e) {
                    try {
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

            window.showToast = function (message, type = 'info') {
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

                let hasHTML = false;
                let isArray = Array.isArray(message);
                let isObject = typeof message === 'object' && message !== null && !isArray;

                if (typeof message === 'string') {
                    hasHTML = message.indexOf('<') !== -1 && message.indexOf('>') !== -1;
                }

                toast.className = `${bgColor} border-l-4 ${borderColor} ${textColor} p-4 rounded shadow-md z-50 opacity-0 transition-opacity duration-300 max-w-md overflow-y-auto max-h-[80vh]`;
                toast.setAttribute('role', 'alert');

                const content = document.createElement('div');
                content.className = 'flex items-start';

                const iconDiv = document.createElement('div');
                iconDiv.className = 'py-1 flex-shrink-0';
                iconDiv.innerHTML = icon;
                content.appendChild(iconDiv);

                const messageDiv = document.createElement('div');
                messageDiv.className = 'flex-grow';

                const titleP = document.createElement('p');
                titleP.className = 'font-bold';
                titleP.textContent = type === 'success' ? 'Berhasil' :
                    type === 'error' ? 'Gagal' :
                        type.charAt(0).toUpperCase() + type.slice(1);
                messageDiv.appendChild(titleP);

                const messageP = document.createElement('div');
                messageP.className = 'error-message';

                if (isArray) {
                    let htmlContent = '<ul class="mt-2 ml-4 list-disc">';
                    message.forEach(item => {
                        htmlContent += `<li>${item}</li>`;
                    });
                    htmlContent += '</ul>';
                    messageP.innerHTML = htmlContent;
                    hasHTML = true;
                } else if (isObject) {
                    let htmlContent = '<ul class="mt-2 ml-4 list-disc">';
                    Object.entries(message).forEach(([key, value]) => {
                        if (Array.isArray(value)) {
                            value.forEach(item => {
                                htmlContent += `<li>${item}</li>`;
                            });
                        } else {
                            htmlContent += `<li>${key}: ${value}</li>`;
                        }
                    });
                    htmlContent += '</ul>';
                    messageP.innerHTML = htmlContent;
                    hasHTML = true;
                } else if (hasHTML && typeof message === 'string') {
                    messageP.innerHTML = message;
                } else {
                    messageP.textContent = message;
                }

                messageDiv.appendChild(messageP);
                content.appendChild(messageDiv);

                const closeBtn = document.createElement('span');
                closeBtn.className = 'ml-4 cursor-pointer flex-shrink-0';
                closeBtn.textContent = '×';
                closeBtn.onclick = function () {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                };
                content.appendChild(closeBtn);

                toast.appendChild(content);

                let toastContainer = document.querySelector('.toast-container');
                if (!toastContainer) {
                    toastContainer = document.createElement('div');
                    toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-4 toast-container';
                    document.body.appendChild(toastContainer);
                }

                toastContainer.appendChild(toast);

                setTimeout(() => {
                    toast.classList.remove('opacity-0');
                    toast.classList.add('opacity-100');
                }, 10);

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
                        }
                        .error-message ul li {
                            margin-bottom: 0.25rem;
                        }
                        .error-message ul li:last-child {
                            margin-bottom: 0;
                        }
                    </style>
                `);

            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif

                @if(session('error'))
                    showToast("{{ session('error') }}", 'error');
                @endif

            function preventMultipleSubmits(form, buttonSelector) {
                if (!form) return;

                form.addEventListener('submit', function (e) {
                    this.classList.add('was-validated');

                    let isValid = true;

                    if (form.id === 'addUserForm') {
                        const employeeNumberInput = this.querySelector('[name="employee_number"]');
                        const employeeNameInput = this.querySelector('[name="employee_name"]');
                        const passwordInput = this.querySelector('[name="password"]');
                        const roleInputsContainer = document.getElementById('add-role-hidden-inputs');

                        const isEmployeeNumberValid = validateField(employeeNumberInput);
                        const isEmployeeNameValid = validateField(employeeNameInput);
                        const isPasswordValid = validateField(passwordInput);

                        const hasRoles = roleInputsContainer.querySelectorAll('input[name="role_ids[]"]').length > 0;
                        const roleSelector = document.getElementById('add-selected-roles-display');
                        const roleErrorElement = roleSelector.closest('.space-y-2').querySelector('.error-message');

                        if (!hasRoles) {
                            roleSelector.closest('.relative').classList.add('border-red-500');
                            if (roleErrorElement) roleErrorElement.classList.remove('hidden');
                            isValid = false;
                        } else {
                            roleSelector.closest('.relative').classList.remove('border-red-500');
                            if (roleErrorElement) roleErrorElement.classList.add('hidden');
                        }

                        isValid = isEmployeeNumberValid && isEmployeeNameValid && isPasswordValid && hasRoles;
                    } else if (form.id === 'editUserForm') {
                        const employeeNumberInput = this.querySelector('[name="employee_number"]');
                        const employeeNameInput = this.querySelector('[name="employee_name"]');
                        const roleInputsContainer = document.getElementById('edit-role-hidden-inputs');

                        const isEmployeeNumberValid = validateField(employeeNumberInput);
                        const isEmployeeNameValid = validateField(employeeNameInput);

                        const hasRoles = roleInputsContainer.querySelectorAll('input[name="role_ids[]"]').length > 0;
                        const roleSelector = document.getElementById('edit-selected-roles-display');
                        const roleErrorElement = roleSelector.closest('.space-y-2').querySelector('.error-message');

                        if (!hasRoles) {
                            roleSelector.closest('.relative').classList.add('border-red-500');
                            if (roleErrorElement) roleErrorElement.classList.remove('hidden');
                            isValid = false;
                        } else {
                            roleSelector.closest('.relative').classList.remove('border-red-500');
                            if (roleErrorElement) roleErrorElement.classList.add('hidden');
                        }

                        isValid = isEmployeeNumberValid && isEmployeeNameValid && hasRoles;
                    }

                    if (!isValid) {
                        e.preventDefault();
                        showToast('Silakan isi semua field yang diperlukan', 'error');
                        return false;
                    }

                    const submitBtn = this.querySelector(buttonSelector);
                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `
                                <div class="flex items-center justify-center">
                                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                    <span>Memproses...</span>
                                </div>
                            `;

                        setTimeout(() => {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            }
                        }, 10000);
                    }
                });
            }

            // Only use preventMultipleSubmits for the delete form
            // preventMultipleSubmits(document.getElementById('addUserForm'), 'button[type="submit"]');
            // preventMultipleSubmits(document.getElementById('editUserForm'), 'button[type="submit"]');
            preventMultipleSubmits(document.getElementById('deleteUserForm'), 'button[type="submit"]');

            // Add form submission handlers with fetch API
            const addUserFormElement = document.getElementById('addUserForm');
            if (addUserFormElement) {
                addUserFormElement.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Client-side validation
                    const form = this;
                    form.classList.add('was-validated');

                    // Validate required fields
                    const employeeNumberInput = form.querySelector('[name="employee_number"]');
                    const employeeNameInput = form.querySelector('[name="employee_name"]');
                    const passwordInput = form.querySelector('[name="password"]');
                    const roleInputsContainer = document.getElementById('add-role-hidden-inputs');

                    const isEmployeeNumberValid = validateField(employeeNumberInput);
                    const isEmployeeNameValid = validateField(employeeNameInput);
                    const isPasswordValid = validateField(passwordInput);

                    const hasRoles = roleInputsContainer.querySelectorAll('input[name="role_ids[]"]').length > 0;
                    const roleSelector = document.getElementById('add-selected-roles-display');
                    const roleErrorElement = roleSelector.closest('.space-y-2').querySelector('.error-message');

                    if (!hasRoles) {
                        roleSelector.closest('.relative').classList.add('border-red-500');
                        if (roleErrorElement) roleErrorElement.classList.remove('hidden');
                    } else {
                        roleSelector.closest('.relative').classList.remove('border-red-500');
                        if (roleErrorElement) roleErrorElement.classList.add('hidden');
                    }

                    const isValid = isEmployeeNumberValid && isEmployeeNameValid && isPasswordValid && hasRoles;

                    if (!isValid) {
                        showToast('Silakan isi semua field yang diperlukan', 'error');
                        return false;
                    }

                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `
                            <div class="flex items-center justify-center">
                                <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                <span>Memproses...</span>
                            </div>
                        `;
                    }

                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                            showToast(data.message || 'User berhasil dibuat', 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw data.errors || 'Gagal membuat user';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (error.errors) {
                            showToast(error.errors, 'error');

                            // Highlight fields with errors
                            Object.keys(error.errors).forEach(field => {
                                const input = form.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('border-red-500');
                                    const errorElement = input.closest('.space-y-2')?.querySelector('.error-message');
                                    if (errorElement) {
                                        errorElement.textContent = Array.isArray(error.errors[field])
                                            ? error.errors[field][0]
                                            : error.errors[field];
                                        errorElement.classList.remove('hidden');
                                    }
                                }
                            });
                        } else {
                            showToast(error.message || 'Terjadi kesalahan saat membuat user', 'error');
                        }
                    })
                    .finally(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    });
                });

                addUserFormElement.querySelectorAll('input').forEach(input => {
                    input.addEventListener('input', function () {
                        this.classList.remove('border-red-500');
                        const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    });
                });
            }

            const editUserFormElement = document.getElementById('editUserForm');
            if (editUserFormElement) {
                editUserFormElement.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Client-side validation
                    const form = this;
                    form.classList.add('was-validated');

                    // Validate required fields
                    const employeeNumberInput = form.querySelector('[name="employee_number"]');
                    const employeeNameInput = form.querySelector('[name="employee_name"]');
                    const roleInputsContainer = document.getElementById('edit-role-hidden-inputs');

                    const isEmployeeNumberValid = validateField(employeeNumberInput);
                    const isEmployeeNameValid = validateField(employeeNameInput);

                    const hasRoles = roleInputsContainer.querySelectorAll('input[name="role_ids[]"]').length > 0;
                    const roleSelector = document.getElementById('edit-selected-roles-display');
                    const roleErrorElement = roleSelector.closest('.space-y-2').querySelector('.error-message');

                    if (!hasRoles) {
                        roleSelector.closest('.relative').classList.add('border-red-500');
                        if (roleErrorElement) roleErrorElement.classList.remove('hidden');
                    } else {
                        roleSelector.closest('.relative').classList.remove('border-red-500');
                        if (roleErrorElement) roleErrorElement.classList.add('hidden');
                    }

                    const isValid = isEmployeeNumberValid && isEmployeeNameValid && hasRoles;

                    if (!isValid) {
                        showToast('Silakan isi semua field yang diperlukan', 'error');
                        return false;
                    }

                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `
                            <div class="flex items-center justify-center">
                                <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                <span>Memproses...</span>
                            </div>
                        `;
                    }

                    const formData = new FormData(form);
                    formData.append('_method', 'PUT');

                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                            showToast(data.message || 'User berhasil diperbarui', 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw data.errors || 'Gagal memperbarui user';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (error.errors) {
                            showToast(error.errors, 'error');

                            // Highlight fields with errors
                            Object.keys(error.errors).forEach(field => {
                                const input = form.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('border-red-500');
                                    const errorElement = input.closest('.space-y-2')?.querySelector('.error-message');
                                    if (errorElement) {
                                        errorElement.textContent = Array.isArray(error.errors[field])
                                            ? error.errors[field][0]
                                            : error.errors[field];
                                        errorElement.classList.remove('hidden');
                                    }
                                }
                            });
                        } else {
                            showToast(error.message || 'Terjadi kesalahan saat memperbarui user', 'error');
                        }
                    })
                    .finally(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    });
                });

                editUserFormElement.querySelectorAll('input').forEach(input => {
                    input.addEventListener('input', function () {
                        this.classList.remove('border-red-500');
                        const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    });
                });
            }

            function validateField(field) {
                let errorElement = field.closest('.space-y-2').querySelector('.error-message');

                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    if (errorElement) errorElement.classList.remove('hidden');
                    return false;
                } else {
                    field.classList.remove('border-red-500');
                    if (errorElement) errorElement.classList.add('hidden');
                    return true;
                }
            }
        });
    </script>
@endsection