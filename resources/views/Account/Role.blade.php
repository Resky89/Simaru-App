@extends('Layout.app')

@section('title', 'Role Management')

@section('content')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Role Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">ROLE</h1>

                        <!-- Button Add Role -->
                        @if(hasPermission('role:create') || hasPermission('role:assign_permissions'))
                        <button id="addRoleBtn"
                            class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                                <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                            </svg>
                            <span class="text-base">Tambah Role</span>
                        </button>
                        @endif
                    </div>

                    <!-- Search and Sort -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" id="searchInput" placeholder="Cari berdasarkan nama role..."
                                value="{{ $search ?? '' }}"
                                class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <select id="sortOrder"
                                class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <option value="" {{ ($sort ?? '') == '' ? 'selected' : '' }}>Default Order</option>
                                <option value="id_asc" {{ ($sort ?? '') == 'id_asc' ? 'selected' : '' }}>Terbaru</option>
                                <option value="id_desc" {{ ($sort ?? '') == 'id_desc' ? 'selected' : '' }}>Terlama</option>
                                <option value="name_asc" {{ ($sort ?? '') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                                <option value="name_desc" {{ ($sort ?? '') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Role Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Role</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Deskripsi</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles['data'] ?? [] as $role)
                                    <tr>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $role['role_name'] ?? '-' }}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $role['description'] ?? '-' }}</td>
                                        <td class="p-3 border-t border-[#EEF1F4]">
                                            <div class="flex items-center space-x-2 justify-center">
                                                @if(hasPermission('role:edit') || hasPermission('role:assign_permissions'))
                                                <button class="edit-role-btn p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors"
                                                    data-role-id="{{ $role['role_id'] }}"
                                                    data-role-name="{{ $role['role_name'] }}"
                                                    data-description="{{ $role['description'] ?? '' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                @endif
                                                @if(hasPermission('role:delete'))
                                                <button class="delete-role-btn p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors"
                                                    data-role-id="{{ $role['role_id'] }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-3 text-xs border-t border-[#EEF1F4] text-center">Tidak ada role yang ditemukan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination for ROLE section -->
                    <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ $roles['pagination']['prev_page_url'] ?? '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($roles['pagination']['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Sebelumnya
                            </a>
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $roles['pagination']['current_page'] ?? 1;
                                    $lastPage = $roles['pagination']['last_page'] ?? 1;
                                @endphp

                                @for ($i = 1; $i <= $lastPage; $i++)
                                    <a href="{{ request()->fullUrlWithQuery(['role_page' => $i]) }}"
                                        class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                        {{ $i }}
                                    </a>
                                @endfor
                            </div>
                            <a href="{{ $roles['pagination']['next_page_url'] ?? '#' }}"
                                class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($roles['pagination']['current_page'] ?? 1) >= ($roles['pagination']['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}">
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
                                @if(isset($roles['pagination']) && is_array($roles['pagination']))
                                                            @php
                                                                $currentPage = $roles['pagination']['current_page'] ?? 1;
                                                                $perPage = $roles['pagination']['per_page'] ?? 10;
                                                                $total = $roles['pagination']['total'] ?? count($roles['data'] ?? []);
                                                                $from = ($currentPage - 1) * $perPage + 1;
                                                                $to = min($currentPage * $perPage, $total);
                                                            @endphp
                                                            Menampilkan {{ $from }} sampai {{ $to }} dari {{ $total }} data
                                @else
                                    Menampilkan 1 sampai {{ count($roles['data'] ?? []) }} dari {{ count($roles['data'] ?? []) }} entri
                                @endif
                            </span>
                            <select id="rolePerPageSelect"
                                class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm"
                                onchange="changeRolePerPage(this.value)">
                                <option value="10" {{ isset($roles['pagination']['per_page']) && $roles['pagination']['per_page'] == 10 ? 'selected' : '' }}>10 data per halaman</option>
                                <option value="25" {{ isset($roles['pagination']['per_page']) && $roles['pagination']['per_page'] == 25 ? 'selected' : '' }}>25 data per halaman</option>
                                <option value="50" {{ isset($roles['pagination']['per_page']) && $roles['pagination']['per_page'] == 50 ? 'selected' : '' }}>50 data per halaman</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Role Modal -->
        @if(hasPermission('role:create') || hasPermission('role:assign_permissions'))
        <div id="addRoleModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[900px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="addRoleModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">TAMBAH ROLE</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="addRoleModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="addRoleForm" action="{{ route('roles.store') }}" method="POST">
                                @csrf
                                <div class="space-y-5 mx-auto">
                                    <!-- Role Name Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">
                                            Nama Role <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="role_name"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Masukkan nama role">
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Nama Role wajib diisi</div>
                                    </div>

                                    <!-- Description Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Deskripsi</label>
                                        <textarea name="description"
                                            class="w-full h-[100px] py-3 px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 resize-none"
                                            placeholder="Masukkan deskripsi role"></textarea>
                                    </div>

                                    <!-- Permissions Header -->
                                    @if(hasPermission('role:assign_permissions'))
                                    <div class="pt-2">
                                        <div class="pb-4 border-b border-gray-200">
                                            <h3 class="text-lg font-bold text-[#213268] mb-2">IZIN</h3>
                                            <p class="text-sm text-gray-600 mb-4">Tentukan hak akses setiap pengguna dan apa yang dapat dan tidak dapat mereka lakukan dalam akun Anda.</p>

                                            <div class="flex flex-wrap gap-6 mt-3">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" id="add-all-permission"
                                                        class="checkbox checkbox-primary" data-target="all">
                                                    <label for="add-all-permission"
                                                        class="font-semibold cursor-pointer select-none">
                                                        Semua Izin</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Permission Groups Container -->
                                        <div id="add-permissions-container" class="space-y-6 pt-3">
                                            <p class="text-center text-gray-500 py-4">Memuat data izin...</p>
                                        </div>
                                    @else
                                    <div class="pt-2">
                                        <div class="pb-4 border-b border-gray-200">
                                            <div class="bg-blue-50 text-blue-700 p-4 rounded-md">
                                                <div class="flex">
                                                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <div>
                                                        <p class="font-medium">Izin tidak tersedia</p>
                                                        <p class="text-sm mt-1">Anda tidak memiliki akses untuk menetapkan izin ke peran ini.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Button Group -->
                                    <div class="pt-6">
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

        <!-- Edit Role Modal -->
        @if(hasPermission('role:edit') || hasPermission('role:assign_permissions'))
        <div id="editRoleModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[900px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="editRoleModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT ROLE</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="editRoleModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="editRoleForm" action="" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="space-y-5 mx-auto">
                                    <!-- Role Name Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">
                                            Nama Role <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="edit_role_name" name="role_name"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Masukkan nama role">
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Nama Role wajib diisi</div>
                                    </div>

                                    <!-- Description Input -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Deskripsi</label>
                                        <textarea id="edit_description" name="description"
                                            class="w-full h-[100px] py-3 px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 resize-none"
                                            placeholder="Masukkan deskripsi role"></textarea>
                                    </div>

                                    <!-- Permissions Header -->
                                    @if(hasPermission('role:assign_permissions'))
                                    <div class="pt-2">
                                        <div class="pb-4 border-b border-gray-200">
                                            <h3 class="text-lg font-bold text-[#213268] mb-2">IZIN</h3>
                                            <p class="text-sm text-gray-600 mb-4">Tentukan hak akses setiap pengguna dan apa yang dapat dan tidak dapat mereka lakukan dalam akun Anda.</p>

                                            <div class="flex flex-wrap gap-6 mt-3">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" id="edit-all-permission"
                                                        class="checkbox checkbox-primary" data-target="all">
                                                    <label for="edit-all-permission"
                                                        class="font-semibold cursor-pointer select-none">
                                                        Semua Izin</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Permission Groups Container -->
                                    <div id="edit-permissions-container" class="space-y-6 pt-3">
                                        <p class="text-center text-gray-500 py-4">Memuat izin...</p>
                                    </div>
                                    @else
                                    <div class="pt-2">
                                        <div class="pb-4 border-b border-gray-200">
                                            <div class="bg-blue-50 text-blue-700 p-4 rounded-md">
                                                <div class="flex">
                                                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <div>
                                                        <p class="font-medium">Izin tidak tersedia</p>
                                                        <p class="text-sm mt-1">Anda tidak memiliki akses untuk menetapkan izin ke peran ini.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Button Group -->
                                    <div class="pt-6">
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

        <!-- Delete Role Modal -->
        @if(hasPermission('role:delete'))
        <div id="deleteRoleModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="deleteRoleModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">HAPUS ROLE</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                data-modal="deleteRoleModal">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <form id="deleteRoleForm" action="" method="POST">
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
                                        <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus role ini? Aksi ini tidak dapat dibatalkan.</p>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="button"
                                            class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200"
                                            data-modal="deleteRoleModal">
                                            Batal
                                        </button>
                                        <button type="submit" id="delete-role-btn"
                                            class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <script>
                                // Prevent multiple submit for delete form
                                document.getElementById('deleteRoleForm').addEventListener('submit', function(e) {
                                    const submitBtn = document.getElementById('delete-role-btn');

                                    if (submitBtn && !submitBtn.disabled) {
                                        // Save original text
                                        const originalText = submitBtn.innerHTML;

                                        // Disable button and show loading state
                                        submitBtn.disabled = true;
                                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                                        submitBtn.innerHTML = `
                                            <div class="flex items-center justify-center">
                                                <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                                <span>Memproses...</span>
                                            </div>
                                        `;

                                        // Safety timeout to re-enable after 10 seconds
                                        setTimeout(() => {
                                            if (submitBtn) {
                                                submitBtn.disabled = false;
                                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                                submitBtn.innerHTML = originalText;
                                            }
                                        }, 10000);
                                    }
                                });
                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script>
        // Function to show toast notifications
        window.showToast = function(message, type = 'success') {
            // Create the notification element
            const notification = document.createElement('div');
            notification.id = type + 'Notification' + Date.now(); // Unique ID to allow multiple notifications
            notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
            notification.role = 'alert';

            // Check if message contains HTML or is an array
            const hasHTML = typeof message === 'string' && /<[a-z][\s\S]*>/i.test(message);
            const isArray = Array.isArray(message);

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
                title.textContent = 'Error!';
                contentContainer.appendChild(title);

                // Message container
                const messageContainer = document.createElement('div');
                messageContainer.className = 'error-message';

                // Handle different message formats
                if (isArray) {
                    // Format array as HTML list
                    let htmlContent = '<ul class="mt-2 ml-4 list-disc">';
                    message.forEach(item => {
                        htmlContent += `<li>${item}</li>`;
                    });
                    htmlContent += '</ul>';
                    messageContainer.innerHTML = htmlContent;
                } else if (hasHTML) {
                    messageContainer.innerHTML = message;
                } else {
                    messageContainer.textContent = message;
                }

                contentContainer.appendChild(messageContainer);

                // Close button
                const closeBtn = document.createElement('span');
                closeBtn.className = 'ml-4 cursor-pointer flex-shrink-0';
                closeBtn.textContent = '×';
                closeBtn.onclick = function() {
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
        document.addEventListener('DOMContentLoaded', function() {
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
                        list-style-type: disc;
                    }
                    .error-message ul li {
                        margin-bottom: 0.25rem;
                    }
                    .error-message ul li:last-child {
                        margin-bottom: 0;
                    }
                </style>
            `);
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Permission-aware initialization
            @if(!hasPermission('role:create') && !hasPermission('role:assign_permissions'))
            // Disable related elements if user doesn't have permission
            const addButtons = document.querySelectorAll('#addRoleBtn');
            addButtons.forEach(btn => {
                if (btn) {
                    btn.style.display = 'none';
                }
            });
            @endif

            @if(!hasPermission('role:edit') && !hasPermission('role:assign_permissions'))
            // Disable edit functionality if user doesn't have permission
            const editButtons = document.querySelectorAll('.edit-role-btn');
            editButtons.forEach(btn => {
                if (btn) {
                    btn.style.display = 'none';
                }
            });
            @endif

            @if(!hasPermission('role:delete'))
            // Disable delete functionality if user doesn't have permission
            const deleteButtons = document.querySelectorAll('.delete-role-btn');
            deleteButtons.forEach(btn => {
                if (btn) {
                    btn.style.display = 'none';
                }
            });
            @endif

            // Show toast notifications for session messages on page load
            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif

            // Function to change items per page for roles
            window.changeRolePerPage = function (limit) {
                const url = new URL(window.location.href);
                url.searchParams.set('role_limit', limit);
                url.searchParams.set('role_page', 1); // Reset to first page when changing limit
                window.location.href = url.toString();
            }

            // Get all modal elements
            const addRoleModal = document.getElementById('addRoleModal');
            const editRoleModal = document.getElementById('editRoleModal');
            const deleteRoleModal = document.getElementById('deleteRoleModal');
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

            // Function to fetch permissions
            async function fetchPermissions() {
                try {
                    const response = await fetch('{{ route("roles.permissions") }}');
                    if (!response.ok) {
                        throw new Error('Failed to fetch permissions');
                    }
                    const data = await response.json();
                    return data.data || [];
                } catch (error) {
                    console.error('Error fetching permissions:', error);
                    return [];
                }
            }

            // Function to render permission checkboxes - restructured version
            function renderPermissionCheckboxes(permissions, selectedIds = [], containerId = 'add-permissions-container') {
                const container = document.getElementById(containerId);
                if (!container) return;

                if (!permissions || permissions.length === 0) {
                    container.innerHTML = '<p class="text-center text-gray-500 py-4">Tidak ada izin yang tersedia</p>';
                    return;
                }

                // Clear the container
                container.innerHTML = '';

                // Group permissions by their feature/menu (first part before colon)
                const groupedPermissions = {};
                permissions.forEach(permission => {
                    if (permission.permission_name === '*') {
                        // Skip the all permissions one as we handle it separately
                        return;
                    }

                    let group = 'Lainnya';
                    let action = '';
                    let originalGroup = '';

                    if (permission.permission_name.includes(':')) {
                        const parts = permission.permission_name.split(':');
                        originalGroup = parts[0];
                        action = parts[1];

                        // Combine maintenance report permissions with maintenance
                        if (originalGroup === 'maintenance-report') {
                            group = 'Perawatan';
                        }
                        // Combine repair permissions with complaint
                        else if (originalGroup === 'repair') {
                            group = 'Keluhan dan Perbaikan';
                        }
                        // For other groups, translate and format them
                        else {
                            // Map group names to Indonesian without hyphens
                            switch (originalGroup) {
                                case 'asset-master': group = 'Master Aset'; break;
                                case 'asset-subcategory': group = 'Kategori'; break;
                                case 'asset': group = 'Aset'; break;
                                case 'brand': group = 'Merk'; break;
                                case 'building': group = 'Gedung'; break;
                                case 'calibration': group = 'Kalibrasi'; break;
                                case 'complaint': group = 'Keluhan dan Perbaikan'; break;
                                case 'dashboard': group = 'Dashboard'; break;
                                case 'document': group = 'Dokumen'; break;
                                case 'maintenance': group = 'Perawatan'; break;
                                case 'mobile': group = 'Mobile'; break;
                                case 'price-comparison': group = 'Perbandingan Harga'; break;
                                case 'procurement': group = 'Pengadaan'; break;
                                case 'purchase-order': group = 'Pemesanan'; break;
                                case 'receipt': group = 'Penerimaan'; break;
                                case 'report': group = 'Laporan'; break;
                                case 'role': group = 'Peran'; break;
                                case 'room': group = 'Ruangan'; break;
                                case 'user': group = 'Pengguna'; break;
                                case 'vendor': group = 'Vendor'; break;
                                default:
                                    // Default formatting for groups not explicitly mapped
                                    group = originalGroup.charAt(0).toUpperCase() + originalGroup.slice(1).toLowerCase();
                                    group = group.replace(/-/g, ' '); // Remove hyphens
                                    break;
                            }
                        }
                    }

                    if (!groupedPermissions[group]) {
                        groupedPermissions[group] = {
                            viewPermission: null,
                            otherPermissions: []
                        };
                    }

                    // Separate view permissions from other permissions
                    if (action === 'view') {
                        groupedPermissions[group].viewPermission = permission;
                    } else {
                        groupedPermissions[group].otherPermissions.push(permission);
                    }
                });

                // Generate HTML for each group
                let html = '';
                for (const [group, permGroup] of Object.entries(groupedPermissions)) {
                    const { viewPermission, otherPermissions } = permGroup;

                    // Generate unique IDs for this group
                    const groupId = group.toLowerCase().replace(/[^a-z0-9]/g, '_');
                    const groupContainerId = `${containerId}-${groupId}-container`;

                    html += `
                        <div class="permission-group bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <h4 class="text-[#213268] text-lg font-semibold">${group}</h4>`;

                    // Add View Only checkbox next to the group name if it exists
                    if (viewPermission) {
                        const viewPermId = `${containerId}-perm-${viewPermission.permission_id}`;
                        const isViewChecked = selectedIds.includes(viewPermission.permission_id);

                        html += `
                                    <input type="checkbox"
                                        id="${viewPermId}"
                                        name="permission_ids[]"
                                        value="${parseInt(viewPermission.permission_id)}"
                                        class="checkbox checkbox-primary view-permission-checkbox"
                                        data-group="${groupId}"
                                        ${isViewChecked ? 'checked' : ''}>
                                `;
                    }

                    html += `
                                </div>
                            </div>

                            <p class="text-sm text-gray-500 mb-3">${viewPermission ? viewPermission.description : 'Manage permissions for this feature'}</p>

                            <!-- Other permissions container -->
                            <div id="${groupContainerId}" class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3" ${viewPermission && !selectedIds.includes(viewPermission.permission_id) ? 'style="display:none;"' : ''}>`;

                    // Add other permissions
                    otherPermissions.forEach(permission => {
                        const isChecked = selectedIds.includes(permission.permission_id);
                        const permId = `${containerId}-perm-${permission.permission_id}`;

                        // Map common actions to more user-friendly names in Indonesian
                        let displayName = permission.permission_name;
                        if (permission.permission_name.includes(':')) {
                            const action = permission.permission_name.split(':')[1];

                            switch (action) {
                                case 'create': displayName = 'Tambah'; break;
                                case 'edit': displayName = 'Ubah'; break;
                                case 'delete': displayName = 'Hapus'; break;
                                case 'export': displayName = 'Ekspor'; break;
                                case 'import': displayName = 'Impor'; break;
                                case 'approve': displayName = 'Setujui'; break;
                                case 'reject': displayName = 'Tolak'; break;
                                case 'medical': displayName = 'Medis'; break;
                                case 'non-medical': displayName = 'Non Medis'; break;
                                case 'assign': displayName = 'Hubungkan'; break;
                                case 'assign_permissions': displayName = 'Tetapkan Izin'; break;
                                case 'checkout': displayName = 'Checkout'; break;
                                case 'return': displayName = 'Pengembalian'; break;
                                case 'dispose': displayName = 'Penghapusan'; break;
                                case 'report-loss': displayName = 'Lapor Kehilangan'; break;
                                case 'report-found': displayName = 'Lapor Ditemukan'; break;
                                case 'opname': displayName = 'Stock Opname'; break;
                                case 'complete': displayName = 'Selesaikan'; break;
                                case 'depreciation': displayName = 'Depresiasi'; break;
                                case 'finance': displayName = 'Keuangan'; break;
                                default: displayName = action.charAt(0).toUpperCase() + action.slice(1).replace(/-/g, ' ');
                            }
                        }

                        html += `
                                <div class="flex items-start gap-3 hover:bg-gray-50 p-2 rounded">
                                    <input type="checkbox"
                                        id="${permId}"
                                        name="permission_ids[]"
                                        value="${parseInt(permission.permission_id)}"
                                        class="checkbox checkbox-primary mt-1 permission-checkbox"
                                        data-group="${groupId}"
                                        ${isChecked ? 'checked' : ''}>
                                    <label for="${permId}" class="cursor-pointer select-none">
                                        <div class="font-medium">${displayName}</div>
                                        <div class="text-xs text-gray-500">${permission.description}</div>
                                    </label>
                                </div>`;
                    });

                    html += `
                            </div>
                        </div>`;
                }

                // Add the HTML to the container
                container.innerHTML = html;

                // Add event listeners for view checkboxes to show/hide other permissions
                container.querySelectorAll('.view-permission-checkbox').forEach(checkbox => {
                    const groupId = checkbox.getAttribute('data-group');
                    const permissionsContainer = document.getElementById(`${containerId}-${groupId}-container`);

                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            // Show other permissions when view is checked
                            permissionsContainer.style.display = 'grid';
                        } else {
                            // Hide other permissions when view is unchecked
                            permissionsContainer.style.display = 'none';

                            // Uncheck all other permissions in this group
                            permissionsContainer.querySelectorAll('.permission-checkbox').forEach(cb => {
                                cb.checked = false;
                            });
                        }
                    });
                });

                // Find the "All" permission and set up the all permission checkbox
                const allPermission = permissions.find(p => p.permission_name === '*');
                if (allPermission) {
                    // Create a hidden input for the all permission
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'checkbox';
                    hiddenInput.name = 'permission_ids[]';
                    hiddenInput.value = allPermission.permission_id;
                    hiddenInput.id = `${containerId}-hidden-all-permission`;
                    hiddenInput.className = 'hidden';
                    hiddenInput.checked = selectedIds.includes(allPermission.permission_id);
                    container.appendChild(hiddenInput);

                    // Set the all permission checkbox state
                    const allCheckbox = document.querySelector(`#${containerId.replace('-permissions-container', '')}-all-permission`);
                    if (allCheckbox) {
                        allCheckbox.checked = selectedIds.includes(allPermission.permission_id);

                        // Toggle all checkboxes when the all permission is toggled
                        allCheckbox.addEventListener('change', function () {
                            const allCheckboxes = container.querySelectorAll('.permission-checkbox, .view-permission-checkbox');
                            const hiddenAllInput = document.getElementById(`${containerId}-hidden-all-permission`);

                            // Set checked state for all checkboxes
                            allCheckboxes.forEach(cb => {
                                cb.checked = this.checked;
                                cb.disabled = this.checked;

                                // If this is a view checkbox, trigger its change event to show/hide other permissions
                                if (cb.classList.contains('view-permission-checkbox')) {
                                    const event = new Event('change');
                                    cb.dispatchEvent(event);
                                }
                            });

                            // Update the hidden input for All permission
                            hiddenAllInput.checked = this.checked;

                            // Handle hidden inputs for form submission
                            if (this.checked) {
                                // Add hidden inputs for all permissions when "All Permission" is checked
                                permissions.forEach(permission => {
                                    if (permission.permission_name !== '*') {
                                        // Check if the hidden input already exists
                                        const existingInput = container.querySelector(`input[type="hidden"][name="permission_ids[]"][value="${permission.permission_id}"]`);
                                        if (!existingInput) {
                                            const hiddenInput = document.createElement('input');
                                            hiddenInput.type = 'hidden';
                                            hiddenInput.name = 'permission_ids[]';
                                            hiddenInput.value = permission.permission_id;
                                            container.appendChild(hiddenInput);
                                        }
                                    }
                                });
                            } else {
                                // Remove hidden inputs when "All Permission" is unchecked
                                const hiddenInputs = container.querySelectorAll('input[type="hidden"][name="permission_ids[]"]:not([id])');
                                hiddenInputs.forEach(input => {
                                    input.remove();
                                });
                            }
                        });

                        // If "All Permission" is already checked on load, handle initial state
                        if (allCheckbox.checked) {
                            const checkboxes = container.querySelectorAll('.permission-checkbox, .view-permission-checkbox');
                            checkboxes.forEach(cb => {
                                cb.disabled = true;
                                cb.checked = true;
                            });

                            // Make sure all permission containers are visible
                            container.querySelectorAll('[id$="-container"]').forEach(container => {
                                container.style.display = 'grid';
                            });
                        }
                    }
                }
            }

            // Add event listener for add role button to open the modal
            document.getElementById('addRoleBtn').addEventListener('click', async () => {
                @if(hasPermission('role:assign_permissions'))
                const permissions = await fetchPermissions();
                renderPermissionCheckboxes(permissions, [], 'add-permissions-container');
                @endif

                // Reset the form
                document.getElementById('addRoleForm').reset();

                // Open the add modal
                openModal(addRoleModal, document.getElementById('addRoleModalContent'));
            });

            // Update the edit role button event listener in Role.blade.php
            document.querySelectorAll('.edit-role-btn').forEach(button => {
                button.addEventListener('click', async () => {
                    const roleId = button.getAttribute('data-role-id');

                    try {
                        // Show loading state
                        document.getElementById('edit_role_name').value = "Memuat...";
                        document.getElementById('edit_description').value = "Memuat...";
                        @if(hasPermission('role:assign_permissions'))
                        document.getElementById('edit-permissions-container').innerHTML = '<p class="text-center text-gray-500 py-4">Memuat data izin...</p>';
                        @endif

                        // Open the modal while loading
                        openModal(editRoleModal, document.getElementById('editRoleModalContent'));

                        // Fetch role data from the API - use the named route
                        const response = await fetch(`{{ url('roles') }}/${roleId}`);

                        if (!response.ok) {
                            throw new Error('Gagal mengambil detail role');
                        }

                        // Parse the response
                        const roleData = await response.json();

                        if (!roleData.success) {
                            throw new Error(roleData.message || 'Gagal mengambil detail role');
                        }

                        // Set form action
                        document.getElementById('editRoleForm').action = `{{ url('roles') }}/${roleId}`;

                        // Populate form fields with data from API
                        const role = roleData.data;
                        document.getElementById('edit_role_name').value = role.role_name;
                        document.getElementById('edit_description').value = role.description || '';

                        @if(hasPermission('role:assign_permissions'))
                        // Load all permissions then select the ones from our role
                        const permissions = await fetchPermissions();
                        const selectedPermissionIds = role.permissions?.map(p => p.permission_id) || [];

                        // Render the permissions with the correct ones selected
                        renderPermissionCheckboxes(permissions, selectedPermissionIds, 'edit-permissions-container');
                        @endif

                    } catch (error) {
                        console.error('Error loading role:', error);
                        showToast(`Error loading role: ${error.message}`, 'error');

                        @if(hasPermission('role:assign_permissions'))
                        // Load permissions with empty selection in case of error
                        const permissions = await fetchPermissions();
                        renderPermissionCheckboxes(permissions, [], 'edit-permissions-container');
                        @endif
                    }
                });
            });

            // Close Modal Handlers
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const modalId = button.getAttribute('data-modal');
                    const modal = document.getElementById(modalId);
                    const content = modal.querySelector('[id$="ModalContent"]');
                    closeModal(modal, content);
                });
            });

            // Close on outside click
            [addRoleModal, editRoleModal, deleteRoleModal].forEach(modal => {
                modal.addEventListener('click', function (e) {
                    // Check if the click is directly on the modal's overlay area
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
                    [addRoleModal, editRoleModal, deleteRoleModal].forEach(modal => {
                        if (!modal.classList.contains('hidden')) {
                            const content = modal.querySelector('[id$="ModalContent"]');
                            closeModal(modal, content);
                        }
                    });
                }
            });

            // Also need to update the permissions header sections in both modals to remove "Set as Admin"
            document.addEventListener('DOMContentLoaded', function () {
                // Find and modify the permission header sections
                const addPermissionHeader = document.querySelector('#addRoleModal .pb-4.border-b.border-gray-200 .flex.flex-wrap.gap-6.mt-3');
                const editPermissionHeader = document.querySelector('#editRoleModal .pb-4.border-b.border-gray-200 .flex.flex-wrap.gap-6.mt-3');

                if (addPermissionHeader) {
                    // Remove the "Set as Admin" checkbox
                    const setAsAdminDiv = addPermissionHeader.querySelector('div:nth-child(2)');
                    if (setAsAdminDiv) {
                        setAsAdminDiv.remove();
                    }
                }

                if (editPermissionHeader) {
                    // Remove the "Set as Admin" checkbox
                    const setAsAdminDiv = editPermissionHeader.querySelector('div:nth-child(2)');
                    if (setAsAdminDiv) {
                        setAsAdminDiv.remove();
                    }
                }
            });

            // Add Role Form Submit Handler
            document.getElementById('addRoleForm').addEventListener('submit', function (e) {
                e.preventDefault();

                // First validate form
                const roleNameInput = this.querySelector('[name="role_name"]');
                if (!roleNameInput.value.trim()) {
                    const errorElement = roleNameInput.closest('.space-y-2').querySelector('.error-message');
                    if (errorElement) errorElement.classList.remove('hidden');
                    roleNameInput.classList.add('border-red-500');
                    showToast('Silakan isi semua field yang diperlukan', 'error');
                    return false;
                }

                // Prevent multiple submissions
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    // Save original button text
                    const originalText = submitBtn.innerHTML;

                    // Disable the button and show loading state
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                    submitBtn.innerHTML = `
                        <div class="flex items-center justify-center">
                            <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                            <span>Memproses...</span>
                        </div>
                    `;

                    // Re-enable button after 10 seconds as a failsafe
                    setTimeout(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalText;
                        }
                    }, 10000);
                }

                try {
                    // Create a new form with the same action and method
                    const form = document.createElement('form');
                    form.action = this.action;
                    form.method = this.method;

                    // Copy the CSRF token
                    const csrfToken = this.querySelector('input[name="_token"]');
                    if (csrfToken) {
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = csrfToken.value;
                        form.appendChild(tokenInput);
                    }

                    // Add role name
                    const roleName = this.querySelector('[name="role_name"]');
                    const roleNameInput = document.createElement('input');
                    roleNameInput.type = 'hidden';
                    roleNameInput.name = 'role_name';
                    roleNameInput.value = roleName.value.trim();
                    form.appendChild(roleNameInput);

                    // Add description ONLY if it's not empty
                    const description = this.querySelector('[name="description"]');
                    if (description && description.value && description.value.trim() !== '') {
                        const descInput = document.createElement('input');
                        descInput.type = 'hidden';
                        descInput.name = 'description';
                        descInput.value = description.value.trim();
                        form.appendChild(descInput);
                    }

                    @if(hasPermission('role:assign_permissions'))
                    // Get all checked checkboxes and hidden inputs with permission IDs
                    const permissionInputs = this.querySelectorAll('input[name="permission_ids[]"]:checked, input[name="permission_ids[]"][type="hidden"]');

                    // Add numeric permission IDs, avoiding duplicates
                    const uniqueIds = new Set();
                    permissionInputs.forEach(input => {
                        uniqueIds.add(parseInt(input.value));
                    });

                    uniqueIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'permission_ids[]';
                        input.value = id; // This is now a number
                        form.appendChild(input);
                    });
                    @endif

                    // Append form to body, submit it, then remove it
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                } catch (error) {
                    console.error('Error submitting form:', error);
                    showToast('Terjadi kesalahan saat mengirim form', 'error');

                    // Re-enable button if error
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = originalText || 'Simpan';
                    }
                }
            });

            // Edit Role Form Submit Handler (similar logic)
            document.getElementById('editRoleForm').addEventListener('submit', function (e) {
                e.preventDefault();

                // First validate form
                const roleNameInput = this.querySelector('[name="role_name"]');
                if (!roleNameInput.value.trim()) {
                    const errorElement = roleNameInput.closest('.space-y-2').querySelector('.error-message');
                    if (errorElement) errorElement.classList.remove('hidden');
                    roleNameInput.classList.add('border-red-500');
                    showToast('Silakan isi semua field yang diperlukan', 'error');
                    return false;
                }

                // Prevent multiple submissions
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    // Save original button text
                    const originalText = submitBtn.innerHTML;

                    // Disable the button and show loading state
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                    submitBtn.innerHTML = `
                        <div class="flex items-center justify-center">
                            <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                            <span>Memproses...</span>
                        </div>
                    `;

                    // Re-enable button after 10 seconds as a failsafe
                    setTimeout(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalText;
                        }
                    }, 10000);
                }

                try {
                    // Create a new form with the same action and method
                    const form = document.createElement('form');
                    form.action = this.action;
                    form.method = this.method;

                    // Copy the CSRF token and method field
                    const csrfToken = this.querySelector('input[name="_token"]');
                    if (csrfToken) {
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = csrfToken.value;
                        form.appendChild(tokenInput);
                    }

                    const methodField = this.querySelector('input[name="_method"]');
                    if (methodField) {
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = methodField.value;
                        form.appendChild(methodInput);
                    }

                    // Add role name
                    const roleName = this.querySelector('[name="role_name"]');
                    const roleNameInput = document.createElement('input');
                    roleNameInput.type = 'hidden';
                    roleNameInput.name = 'role_name';
                    roleNameInput.value = roleName.value.trim();
                    form.appendChild(roleNameInput);

                    // Add description ONLY if it's not empty
                    const description = this.querySelector('[name="description"]');
                    if (description && description.value && description.value.trim() !== '') {
                        const descInput = document.createElement('input');
                        descInput.type = 'hidden';
                        descInput.name = 'description';
                        descInput.value = description.value.trim();
                        form.appendChild(descInput);
                    }

                    @if(hasPermission('role:assign_permissions'))
                    // Get all checked checkboxes and hidden inputs with permission IDs
                    const permissionInputs = this.querySelectorAll('input[name="permission_ids[]"]:checked, input[name="permission_ids[]"][type="hidden"]');

                    // Add numeric permission IDs, avoiding duplicates
                    const uniqueIds = new Set();
                    permissionInputs.forEach(input => {
                        uniqueIds.add(parseInt(input.value));
                    });

                    uniqueIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'permission_ids[]';
                        input.value = id; // This is now a number
                        form.appendChild(input);
                    });
                    @endif

                    // Append form to body, submit it, then remove it
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                } catch (error) {
                    console.error('Error submitting form:', error);
                    showToast('Terjadi kesalahan saat mengirim form', 'error');

                    // Re-enable button if error
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = originalText || 'Perbarui';
                    }
                }
            });

            // Search and sorting functionality
            const searchInput = document.getElementById('searchInput');
            const sortOrder = document.getElementById('sortOrder');

            // Function to handle search and sorting
            function applyFilters() {
                const searchValue = searchInput?.value.trim() || '';
                const sortValue = sortOrder?.value || '';

                // Create URL with filter parameters
                const url = new URL(window.location.href);

                // Clear existing parameters we're going to set
                ['search', 'sort', 'role_page'].forEach(param => {
                    url.searchParams.delete(param);
                });

                // Add new parameters if they have values
                if (searchValue) url.searchParams.set('search', searchValue);
                if (sortValue) url.searchParams.set('sort', sortValue);

                // Reset to page 1 when filters change
                url.searchParams.set('role_page', 1);

                // Navigate to the new URL
                window.location.href = url.toString();
            }

            // Add event listeners with debounce for search
            let searchTimeout;
            searchInput?.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 500);
            });

            // Add event listener for sort order
            sortOrder?.addEventListener('change', applyFilters);

            // Set initial values from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (searchInput) searchInput.value = urlParams.get('search') || '';
            if (sortOrder) {
                const sortValue = urlParams.get('sort');
                if (sortValue) {
                    sortOrder.value = sortValue;
                }
            }

            // Function to change page while preserving search and sort parameters
            window.changePage = function(page) {
                const url = new URL(window.location.href);
                url.searchParams.set('role_page', page);
                window.location.href = url.toString();
            };

            // Form validation for Add Role
            document.getElementById('addRoleForm').addEventListener('submit', function(event) {
                const roleNameInput = this.querySelector('[name="role_name"]');

                const isRoleNameValid = validateField(roleNameInput);

                if (!isRoleNameValid) {
                    event.preventDefault();
                    showToast('Silakan isi semua field yang diperlukan', 'error');
                }
            });

            // Form validation for Edit Role
            document.getElementById('editRoleForm').addEventListener('submit', function(event) {
                const roleNameInput = document.getElementById('edit_role_name');

                const isRoleNameValid = validateField(roleNameInput);

                if (!isRoleNameValid) {
                    event.preventDefault();
                    showToast('Silakan isi semua field yang diperlukan', 'error');
                }
            });

            // Function to validate field and show error styling
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

            // Add input event listeners to clear error styling when typing
            document.querySelector('[name="role_name"]').addEventListener('input', function() {
                this.classList.remove('border-red-500');
                const errorElement = this.closest('.space-y-2').querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });

            document.getElementById('edit_role_name').addEventListener('input', function() {
                this.classList.remove('border-red-500');
                const errorElement = this.closest('.space-y-2').querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });

            // Add event listener for delete role button
            document.querySelectorAll('.delete-role-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const roleId = button.getAttribute('data-role-id');

                    // Set the form action for delete
                    document.getElementById('deleteRoleForm').action = `{{ url('roles') }}/${roleId}`;

                    // Open the delete modal
                    openModal(deleteRoleModal, document.getElementById('deleteRoleModalContent'));
                });
            });
        });
    </script>
@endsection
