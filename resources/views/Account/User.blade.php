@extends('Layout.app')

@section('title', 'User Management')

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
                    <button id="addRoleBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span class="text-base">Add Role</span>
                    </button>
                </div>

                <!-- Role Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                    <input type="checkbox" class="checkbox checkbox-sm" />
                                </th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Role ID</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Role</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Description</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles['data'] ?? [] as $role)
                                <tr>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                        <input type="checkbox" class="checkbox checkbox-sm" />
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $role['role_id'] ?? '-' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $role['role_name'] ?? '-' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $role['description'] ?? '-' }}</td>
                                    <td class="p-3 border-t border-[#EEF1F4]">
                                        <div class="flex justify-center gap-2">
                                            <button class="text-[#3D3D3D] hover:text-[#213268] edit-role-btn"
                                                    data-role-id="{{ $role['role_id'] }}"
                                                    data-role-name="{{ $role['role_name'] }}"
                                                    data-description="{{ $role['description'] ?? '' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button class="text-[#3D3D3D] hover:text-red-500 delete-role-btn"
                                                    data-role-id="{{ $role['role_id'] }}">
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
                            @empty
                                <tr>
                                    <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No roles found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for ROLE section -->
                <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                    <div class="flex items-center space-x-2">
                        <a href="{{ $roles['pagination']['prev_page_url'] ?? '#' }}" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($roles['pagination']['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Prev
                        </a>
                        <div class="flex gap-2">
                            @php
                                $currentPage = $roles['pagination']['current_page'] ?? 1;
                                $lastPage = $roles['pagination']['last_page'] ?? 1;
                            @endphp

                            @for ($i = 1; $i <= $lastPage; $i++)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                    {{ $i }}
                                </a>
                            @endfor
                        </div>
                        <a href="{{ $roles['pagination']['next_page_url'] ?? '#' }}" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($roles['pagination']['current_page'] ?? 1) >= ($roles['pagination']['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}">
                            Next
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
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
                                Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                            @else
                                Showing 1 to {{ count($roles['data'] ?? []) }} of {{ count($roles['data'] ?? []) }} entries
                            @endif
                        </span>
                        <select id="rolePerPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changeRolePerPage(this.value)">
                            <option value="10" {{ isset($roles['pagination']['per_page']) && $roles['pagination']['per_page'] == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ isset($roles['pagination']['per_page']) && $roles['pagination']['per_page'] == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ isset($roles['pagination']['per_page']) && $roles['pagination']['per_page'] == 50 ? 'selected' : '' }}>50 per page</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Section -->
    <div class="card bg-base-100 shadow-xl mt-8">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">USER</h1>

                    <!-- Button Add User -->
                    <button id="addUserBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span class="text-base">Add User</span>
                    </button>
                </div>

                <!-- User Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                    <input type="checkbox" class="checkbox checkbox-sm" />
                                </th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">User ID</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Email</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Employee</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Role</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Status</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users ?? [] as $user)
                                <tr>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                        <input type="checkbox" class="checkbox checkbox-sm" />
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $user['user_id'] ?? '-' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $user['email'] ?? '-' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $user['employee_name'] ?? '-' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $user['role_name'] ?? '-' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        <span class="px-2 py-1 rounded text-xs {{ ($user['is_active'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ($user['is_active'] ?? false) ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="p-3 border-t border-[#EEF1F4]">
                                        <div class="flex justify-center gap-2">
                                            <button class="text-[#3D3D3D] hover:text-[#213268] edit-user-btn"
                                                    data-user-id="{{ $user['user_id'] }}"
                                                    data-email="{{ $user['email'] }}"
                                                    data-employee-id="{{ $user['employee_id'] }}"
                                                    data-role-id="{{ $user['role_id'] }}"
                                                    data-is-active="{{ $user['is_active'] ? 'true' : 'false' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button class="text-[#3D3D3D] hover:text-red-500 delete-user-btn"
                                                    data-user-id="{{ $user['user_id'] }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>

                                            <!-- Verification Icon -->
                                            @if($user['verified'] ?? false)
                                                <button class="text-green-500 hover:text-green-600" title="Verified">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>
                                            @else
                                                <button class="text-yellow-500 hover:text-yellow-600" title="Not Verified">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for USERS section -->
                <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                    <div class="flex items-center space-x-2">
                        <a href="{{ $user_pagination['prev_page_url'] ?? '#' }}" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($user_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Prev
                        </a>
                        <div class="flex gap-2">
                            @php
                                $currentPage = $user_pagination['current_page'] ?? 1;
                                $lastPage = $user_pagination['last_page'] ?? 1;
                            @endphp

                            @for ($i = 1; $i <= $lastPage; $i++)
                                <a href="{{ request()->fullUrlWithQuery(['user_page' => $i]) }}" class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                    {{ $i }}
                                </a>
                            @endfor
                        </div>
                        <a href="{{ $user_pagination['next_page_url'] ?? '#' }}" class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm {{ ($user_pagination['current_page'] ?? 1) >= ($user_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}">
                            Next
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
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
                                Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                            @else
                                Showing 1 to {{ count($users ?? []) }} of {{ count($users ?? []) }} entries
                            @endif
                        </span>
                        <select id="userPerPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changeUserPerPage(this.value)">
                            <option value="10" {{ isset($user_pagination['per_page']) && $user_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ isset($user_pagination['per_page']) && $user_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ isset($user_pagination['per_page']) && $user_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per page</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- All modals should be outside the main content section -->
    <!-- Add Role Modal -->
    <div id="addRoleModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="addRoleModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">ADD ROLE</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="addRoleModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <div class="p-6">
                        <form id="addRoleForm" action="{{ route('roles.store') }}" method="POST">
                            @csrf
                            <div class="space-y-4 max-w-[400px] mx-auto">
                                <!-- Role Name Input -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Role Name</label>
                                    <input type="text" name="role_name"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Enter role name" required>
                                </div>

                                <!-- Description Input -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Description</label>
                                    <textarea name="description"
                                        class="w-full h-[100px] py-3 px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 resize-none"
                                        placeholder="Enter role description"></textarea>
                                </div>

                                <!-- Button Group -->
                                <div class="pt-4">
                                    <button type="submit" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
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

    <!-- Edit Role Modal -->
    <div id="editRoleModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="editRoleModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT ROLE</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="editRoleModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <div class="p-6">
                        <form id="editRoleForm" action="" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4 max-w-[400px] mx-auto">
                                <!-- Role Name Input -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Role Name</label>
                                    <input type="text" id="edit_role_name" name="role_name"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Enter role name" required>
                                </div>

                                <!-- Description Input -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Description</label>
                                    <textarea id="edit_description" name="description"
                                        class="w-full h-[100px] py-3 px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 resize-none"
                                        placeholder="Enter role description"></textarea>
                                </div>

                                <!-- Button Group -->
                                <div class="pt-4">
                                    <button type="submit" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Role Modal -->
    <div id="deleteRoleModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="deleteRoleModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">DELETE ROLE</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="deleteRoleModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                                    <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-base text-gray-600 text-center">Are you sure you want to delete this role? This action cannot be undone.</p>
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200" data-modal="deleteRoleModal">
                                        Cancel
                                    </button>
                                    <button type="submit" class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="addUserModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">ADD USER</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="addUserModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <div class="p-6">
                        <form id="addUserForm" action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="space-y-4 max-w-[400px] mx-auto">
                                <!-- Email Input -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Email</label>
                                    <input type="email" name="email"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Enter email address" required>
                                </div>

                                <!-- Role Selection -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Role</label>
                                    <select name="role_id"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        required>
                                        <option value="" disabled selected>Select a role</option>
                                        @foreach($roles['data'] as $role)
                                        <option value="{{ $role['role_id'] }}">{{ $role['role_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Employee Selection -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Employee</label>
                                    <select name="employee_id"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        required>
                                        <option value="" disabled selected>Select an employee</option>
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee['employee_id'] }}">{{ $employee['first_name'] }} {{ $employee['last_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Button Group -->
                                <div class="pt-4">
                                    <button type="submit" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
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

    <!-- Edit User Modal -->
    <div id="editUserModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="editUserModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT USER</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="editUserModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <div class="p-6">
                        <form id="editUserForm" action="" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4 max-w-[400px] mx-auto">
                                <!-- Email Input -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Email</label>
                                    <input type="email" id="edit_email" name="email"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Enter email address" required>
                                </div>

                                <!-- Role Selection -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Role</label>
                                    <select id="edit_role_id" name="role_id"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        required>
                                        <option value="" disabled>Select a role</option>
                                        @foreach($roles['data'] as $role)
                                        <option value="{{ $role['role_id'] }}">{{ $role['role_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Employee Selection -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Employee</label>
                                    <select id="edit_employee_id" name="employee_id"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        required>
                                        <option value="" disabled>Select an employee</option>
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee['employee_id'] }}">{{ $employee['first_name'] }} {{ $employee['last_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Active Status -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Status</label>
                                    <select id="edit_is_active" name="is_active"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <!-- Button Group -->
                                <div class="pt-4">
                                    <button type="submit" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                        Update
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
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">DELETE USER</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="deleteUserModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                                    <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-base text-gray-600 text-center">Are you sure you want to delete this user? This action cannot be undone.</p>
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200" data-modal="deleteUserModal">
                                        Cancel
                                    </button>
                                    <button type="submit" class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                        Delete
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
document.addEventListener('DOMContentLoaded', function() {
    // Function to change items per page for roles
    window.changeRolePerPage = function(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('role_limit', limit);
        url.searchParams.set('role_page', 1); // Reset to first page when changing limit
        window.location.href = url.toString();
    }

    // Function to change items per page for users
    window.changeUserPerPage = function(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('user_limit', limit);
        window.location.href = url.toString();
    }

    // Toast container
    const toastContainer = document.createElement('div');
    toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-4';
    document.body.appendChild(toastContainer);

    // Get all modal elements
    const addRoleModal = document.getElementById('addRoleModal');
    const editRoleModal = document.getElementById('editRoleModal');
    const deleteRoleModal = document.getElementById('deleteRoleModal');
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
    }

    // Add Role Modal
    document.getElementById('addRoleBtn').addEventListener('click', () => {
        openModal(addRoleModal, addRoleModal.querySelector('[id$="ModalContent"]'));
    });

    // Add User Modal
    document.getElementById('addUserBtn').addEventListener('click', () => {
        openModal(addUserModal, addUserModal.querySelector('[id$="ModalContent"]'));
    });

    // Edit Role Modal
    document.querySelectorAll('.edit-role-btn').forEach(button => {
        button.addEventListener('click', () => {
            const roleId = button.getAttribute('data-role-id');
            const roleName = button.getAttribute('data-role-name');
            const description = button.getAttribute('data-description');

            document.getElementById('editRoleForm').action = `{{ route('roles.update', '') }}/${roleId}`;
            document.getElementById('edit_role_name').value = roleName;
            document.getElementById('edit_description').value = description || '';

            openModal(editRoleModal, editRoleModal.querySelector('[id$="ModalContent"]'));
        });
    });

    // Edit User Modal
    document.querySelectorAll('.edit-user-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const email = this.getAttribute('data-email');
            const employeeId = this.getAttribute('data-employee-id');
            const roleId = this.getAttribute('data-role-id');
            const isActive = this.getAttribute('data-is-active');

            console.log('Button data-is-active:', isActive); // Debug

            // Use the global function to set up the form
            window.setupEditUserForm(userId, email, employeeId, roleId, isActive);
        });
    });

    // Delete Role Modal
    document.querySelectorAll('.delete-role-btn').forEach(button => {
        button.addEventListener('click', () => {
            const roleId = button.getAttribute('data-role-id');
            document.getElementById('deleteRoleForm').action = `{{ route('roles.destroy', '') }}/${roleId}`;

            openModal(deleteRoleModal, deleteRoleModal.querySelector('[id$="ModalContent"]'));
        });
    });

    // Delete User Modal
    document.querySelectorAll('.delete-user-btn').forEach(button => {
        button.addEventListener('click', () => {
            const userId = button.getAttribute('data-user-id');
            document.getElementById('deleteUserForm').action = `{{ route('users.destroy', '') }}/${userId}`;

            openModal(deleteUserModal, deleteUserModal.querySelector('[id$="ModalContent"]'));
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
    [addRoleModal, editRoleModal, deleteRoleModal, addUserModal, editUserModal, deleteUserModal].forEach(modal => {
        modal.addEventListener('click', function(e) {
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
            [addRoleModal, editRoleModal, deleteRoleModal, addUserModal, editUserModal, deleteUserModal].forEach(modal => {
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
        toast.innerHTML = `
            <div class="flex items-center">
                <div class="py-1">
                    ${icon}
                </div>
                <div>
                    <p class="font-bold">${type.charAt(0).toUpperCase() + type.slice(1)}</p>
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

    // For the add user form
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            // Convert select values to numbers
            const roleSelect = this.querySelector('[name="role_id"]');
            const employeeSelect = this.querySelector('[name="employee_id"]');

            roleSelect.value = parseInt(roleSelect.value, 10);
            employeeSelect.value = parseInt(employeeSelect.value, 10);
        });
    }

    // For the edit user form
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', function(e) {
            // Convert select values to numbers
            const roleSelect = this.querySelector('[name="role_id"]');
            const employeeSelect = this.querySelector('[name="employee_id"]');
            const isActiveSelect = this.querySelector('[name="is_active"]');

            roleSelect.value = parseInt(roleSelect.value, 10);
            employeeSelect.value = parseInt(employeeSelect.value, 10);
            isActiveSelect.value = parseInt(isActiveSelect.value, 10);
        });
    }

    // Add this function to window scope for handling edit user form setup
    window.setupEditUserForm = function(userId, email, employeeId, roleId, isActive) {
        console.log('isActive received:', isActive); // Debug

        const form = document.getElementById('editUserForm');
        form.action = `{{ route('users.update', '') }}/${userId}`;

        document.getElementById('edit_email').value = email;

        // Set the values for dropdown selects - make sure they are strings for HTML selects
        document.getElementById('edit_employee_id').value = employeeId.toString();
        document.getElementById('edit_role_id').value = roleId.toString();

        // For is_active, convert boolean string to select option value
        // The value should be "1" for true/active and "0" for false/inactive
        document.getElementById('edit_is_active').value = (isActive === 'true') ? '1' : '0';

        // Open the modal
        const modal = document.getElementById('editUserModal');
        const content = document.getElementById('editUserModalContent');
        openModal(modal, content);
    };
});
</script>

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
@endsection
