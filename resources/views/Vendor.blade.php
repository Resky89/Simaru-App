@extends('Layout.app')

@section('title', 'Vendor')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Vendor Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">VENDOR</h1>

                    <!-- Button Add Vendor -->
                    <button id="addVendorBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span class="text-base">Add Vendor</span>
                    </button>
                </div>

                <!-- Vendor Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                    <input type="checkbox" class="checkbox checkbox-sm" />
                                </th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left w-[15%]">Vendor ID</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Vendor Name</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Contact Person</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Phone Number</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Email</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[100px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendors as $vendor)
                                <tr>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                        <input type="checkbox" class="checkbox checkbox-sm" />
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $vendor['vendor_id'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $vendor['vendor_name'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $vendor['contact_person'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $vendor['phone_number'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $vendor['email'] }}</td>
                                    <td class="p-3 border-t border-[#EEF1F4]">
                                        <div class="flex justify-center gap-2">
                                            <button class="text-[#3D3D3D] hover:text-[#213268] edit-vendor-btn"
                                                   data-vendor-id="{{ $vendor['vendor_id'] }}"
                                                   data-vendor-name="{{ $vendor['vendor_name'] }}"
                                                   data-contact-person="{{ $vendor['contact_person'] }}"
                                                   data-phone-number="{{ $vendor['phone_number'] }}"
                                                   data-email="{{ $vendor['email'] }}"
                                                   data-website="{{ $vendor['website'] ?? '' }}"
                                                   data-address="{{ $vendor['address'] ?? '' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button class="text-[#3D3D3D] hover:text-red-500 delete-vendor-btn"
                                                   data-vendor-id="{{ $vendor['vendor_id'] }}">
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
                                    <td colspan="7" class="p-3 text-center text-gray-500">No vendors found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex gap-2">
                        @if(isset($pagination) && is_array($pagination))
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   onclick="changePage({{ ($pagination['current_page'] ?? 1) - 1 }})"
                                   {{ ($pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Prev
                            </button>
                            <div class="flex gap-2">
                                @php
                                    $currentPage = $pagination['current_page'] ?? 1;
                                    $lastPage = $pagination['last_page'] ?? $currentPage;
                                @endphp

                                @for($i = max(1, $currentPage - 1); $i <= min($lastPage, $currentPage + 1); $i++)
                                    <button onclick="changePage({{ $i }})"
                                            class="w-8 h-8 flex items-center justify-center {{ $i == $currentPage ? 'bg-[#213268] text-white' : 'border border-[#D8DAE5] text-[#213268] hover:bg-gray-50' }} rounded text-sm">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($pagination['current_page'] ?? 1) >= ($pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   onclick="changePage({{ ($pagination['current_page'] ?? 1) + 1 }})"
                                   {{ ($pagination['current_page'] ?? 1) >= ($pagination['last_page'] ?? 1) ? 'disabled' : '' }}>
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @else
                            <!-- Default static pagination if pagination data is not available -->
                            <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 opacity-50 cursor-not-allowed" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Prev
                            </button>
                            <div class="flex gap-2">
                                <button class="w-8 h-8 flex items-center justify-center bg-[#213268] rounded text-white text-sm">1</button>
                            </div>
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
                            @if(isset($pagination) && is_array($pagination))
                                @php
                                    $currentPage = $pagination['current_page'] ?? 1;
                                    $perPage = $pagination['per_page'] ?? 10;
                                    $total = $pagination['total'] ?? count($vendors);
                                    $from = ($currentPage - 1) * $perPage + 1;
                                    $to = min($currentPage * $perPage, $total);
                                @endphp
                                Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                            @else
                                Showing 1 to {{ count($vendors) }} of {{ count($vendors) }} entries
                            @endif
                        </span>
                        <select id="perPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changePerPage(this.value)">
                            <option value="10" {{ isset($pagination['per_page']) && $pagination['per_page'] == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ isset($pagination['per_page']) && $pagination['per_page'] == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ isset($pagination['per_page']) && $pagination['per_page'] == 50 ? 'selected' : '' }}>50 per page</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
@endif
@endsection

<!-- Modal Add Vendor -->
<div id="addVendorModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="vendorModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">ADD VENDOR</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form with JavaScript for debugging -->
                <div class="p-6">
                    <form id="createVendorForm" action="{{ route('vendor.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Vendor Name Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Vendor Name</label>
                                <input type="text" name="vendor_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here" required>
                            </div>

                            <!-- Contact Person Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Contact Person</label>
                                <input type="text" name="contact_person"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here">
                            </div>

                            <!-- Phone Number Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Phone Number</label>
                                <input type="tel" name="phone_number"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here">
                            </div>

                            <!-- Email Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Email</label>
                                <input type="email" name="email"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here">
                            </div>

                            <!-- Website Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Website</label>
                                <input type="url" name="website"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here">
                            </div>

                            <!-- Address Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Address</label>
                                <textarea name="address" rows="3"
                                    class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here"></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" id="submitVendorBtn"
                                class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200"></button>
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Vendor -->
<div id="editVendorModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="editVendorModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">EDIT VENDOR</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <form id="editVendorForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Vendor ID Input (Hidden) -->
                            <input type="hidden" id="editVendorId" name="vendor_id">

                            <!-- Vendor Name Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Vendor Name</label>
                                <input type="text" id="editVendorName" name="vendor_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here">
                            </div>

                            <!-- Contact Person Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Contact Person</label>
                                <input type="text" id="editContactPerson" name="contact_person"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here">
                            </div>

                            <!-- Phone Number Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Phone Number</label>
                                <input type="text" id="editPhoneNumber" name="phone_number"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here">
                            </div>

                            <!-- Email Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Email</label>
                                <input type="email" id="editEmail" name="email"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here">
                            </div>

                            <!-- Website Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Website</label>
                                <input type="text" id="editWebsite" name="website"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here">
                            </div>

                            <!-- Address Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Address</label>
                                <textarea id="editAddress" name="address"
                                    class="w-full p-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 min-h-[100px]"
                                    placeholder="Type here"></textarea>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete Vendor -->
<div id="deleteVendorModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="deleteVendorModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">DELETE VENDOR</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <div class="space-y-6 max-w-[400px] mx-auto">
                        <div class="flex flex-col items-center">
                            <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-base text-gray-600 text-center">Are you sure you want to delete this vendor? This action cannot be undone.</p>
                        </div>
                        <div class="flex gap-3">
                            <button class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                Cancel
                            </button>
                            <form id="deleteVendorForm" action="" method="POST" class="w-1/2">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" id="deleteVendorId" name="vendor_id">
                                <button type="submit" class="w-full h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                    Delete
                                </button>
                            </form>
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
        const addVendorBtn = document.getElementById('addVendorBtn');
        const addVendorModal = document.getElementById('addVendorModal');
        const editVendorModal = document.getElementById('editVendorModal');
        const deleteVendorModal = document.getElementById('deleteVendorModal');
        const closeButtons = document.querySelectorAll('.close-modal');

        // Show toast notifications for session messages on page load
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        // Function to change page
        window.changePage = function(page) {
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        }

        // Function to change items per page
        window.changePerPage = function(limit) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', limit);
            url.searchParams.set('page', 1); // Reset to first page when changing limit
            window.location.href = url.toString();
        }

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

        // Add Vendor Modal
        addVendorBtn.addEventListener('click', () => {
            openModal(addVendorModal, addVendorModal.querySelector('[id$="ModalContent"]'));
        });

        // Edit Vendor Modal
        document.querySelectorAll('.edit-vendor-btn').forEach(button => {
            button.addEventListener('click', () => {
                const vendorId = button.getAttribute('data-vendor-id');
                const vendorName = button.getAttribute('data-vendor-name');
                const contactPerson = button.getAttribute('data-contact-person');
                const phoneNumber = button.getAttribute('data-phone-number');
                const email = button.getAttribute('data-email');
                const website = button.getAttribute('data-website');
                const address = button.getAttribute('data-address');

                // Update form action with the correct route and log it
                const formAction = "{{ url('vendor/update') }}/" + vendorId;
                document.getElementById('editVendorForm').action = formAction;
                console.log('Edit form action set to:', formAction);

                // Set form values
                document.getElementById('editVendorId').value = vendorId;
                document.getElementById('editVendorName').value = vendorName;
                document.getElementById('editContactPerson').value = contactPerson;
                document.getElementById('editPhoneNumber').value = phoneNumber;
                document.getElementById('editEmail').value = email;
                document.getElementById('editWebsite').value = website || '';
                document.getElementById('editAddress').value = address || '';

                openModal(editVendorModal, editVendorModal.querySelector('[id$="ModalContent"]'));
            });
        });

        // Delete Vendor Modal
        document.querySelectorAll('.delete-vendor-btn').forEach(button => {
            button.addEventListener('click', () => {
                const vendorId = button.getAttribute('data-vendor-id');

                // Update form action with the correct route and log it
                const formAction = "{{ url('vendor/delete') }}/" + vendorId;
                document.getElementById('deleteVendorForm').action = formAction;
                console.log('Delete form action set to:', formAction);

                // Set vendor ID di hidden input
                document.getElementById('deleteVendorId').value = vendorId;

                openModal(deleteVendorModal, deleteVendorModal.querySelector('[id$="ModalContent"]'));
            });
        });

        // Close Modal Handlers
        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="ModalContent"]');
                closeModal(modal, content);
            });
        });

        // Close on outside click
        [addVendorModal, editVendorModal, deleteVendorModal].forEach(modal => {
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
                [addVendorModal, editVendorModal, deleteVendorModal].forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        const content = modal.querySelector('[id$="ModalContent"]');
                        closeModal(modal, content);
                    }
                });
            }
        });

        // Debug form submission for the create vendor form
        const createForm = document.getElementById('createVendorForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                console.log('Form submission triggered');
                console.log('Form action:', this.action);
                console.log('Form method:', this.method);

                // Log form data
                const formData = new FormData(this);
                const formDataObj = {};
                formData.forEach((value, key) => {
                    formDataObj[key] = value;
                });
                console.log('Form data:', formDataObj);

                // Continue with form submission
                // If you want to manually handle the form submission with fetch API:
                /*
                e.preventDefault();

                fetch(this.action, {
                    method: this.method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        window.location.href = "{{ route('vendor') }}";
                    } else {
                        alert(data.message || 'Failed to create vendor');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('An error occurred while creating the vendor');
                });
                */
            });
        }

        // Toast notification function
        function showToast(message, type = 'info') {
            // Create toast container if it doesn't exist
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
                document.body.appendChild(toastContainer);
            }

            // Create notification element
            const toast = document.createElement('div');

            // Set classes based on type
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
