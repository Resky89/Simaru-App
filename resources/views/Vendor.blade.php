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
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                    <input type="checkbox" class="checkbox checkbox-sm" />
                                </td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">V001</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">Tech Solutions</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">John Smith</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">+1 234-567-8901</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">contact@techsolutions.com</td>
                                <td class="p-3 border-t border-[#EEF1F4]">
                                    <div class="flex justify-center gap-2">
                                        <button class="text-[#3D3D3D] hover:text-[#213268] edit-vendor-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button class="text-[#3D3D3D] hover:text-red-500 delete-vendor-btn">
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
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                    <input type="checkbox" class="checkbox checkbox-sm" />
                                </td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">V002</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">Global Supplies</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">Sarah Johnson</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">+1 987-654-3210</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">info@globalsupplies.com</td>
                                <td class="p-3 border-t border-[#EEF1F4]">
                                    <div class="flex justify-center gap-2">
                                        <button class="text-[#3D3D3D] hover:text-[#213268] edit-vendor-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button class="text-[#3D3D3D] hover:text-red-500 delete-vendor-btn">
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
                            <button class="w-8 h-8 flex items-center justify-center bg-[#213268] rounded text-white text-sm">2</button>
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50">3</button>
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

                <!-- Form -->
                <div class="p-6">
                    <div class="space-y-4 max-w-[400px] mx-auto">
                        <!-- Vendor Name Input -->
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Vendor Name</label>
                            <input type="text"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Type here">
                        </div>

                        <!-- Contact Person Input -->
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Contact Person</label>
                            <input type="text"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Type here">
                        </div>

                        <!-- Phone Number Input -->
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Phone Number</label>
                            <input type="tel"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Type here">
                        </div>

                        <!-- Email Input -->
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Email</label>
                            <input type="email"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Type here">
                        </div>

                        <!-- Website Input -->
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Website</label>
                            <input type="url"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Type here">
                        </div>

                        <!-- Address Input -->
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Address</label>
                            <textarea
                                class="w-full p-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 min-h-[100px]"
                                placeholder="Type here"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                            Save
                        </button>
                    </div>
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
                    <div class="space-y-4 max-w-[400px] mx-auto">
                        <!-- Vendor Name Input -->
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Vendor Name</label>
                            <input type="text" id="editVendorName"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Type here">
                        </div>

                        <!-- Contact Person Input -->
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Contact Person</label>
                            <input type="text" id="editContactPerson"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Type here">
                        </div>

                        <!-- Phone Number Input -->
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Phone Number</label>
                            <input type="tel" id="editPhoneNumber"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Type here">
                        </div>

                        <!-- Email Input -->
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Email</label>
                            <input type="email" id="editEmail"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Type here">
                        </div>

                        <!-- Website Input -->
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Website</label>
                            <input type="url" id="editWebsite"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="Type here">
                        </div>

                        <!-- Address Input -->
                        <div class="space-y-2">
                            <label class="block text-base font-semibold text-[#666666]">Address</label>
                            <textarea id="editAddress"
                                class="w-full p-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 min-h-[100px]"
                                placeholder="Type here"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                            Update
                        </button>
                    </div>
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
        const addVendorBtn = document.getElementById('addVendorBtn');
        const addVendorModal = document.getElementById('addVendorModal');
        const editVendorModal = document.getElementById('editVendorModal');
        const deleteVendorModal = document.getElementById('deleteVendorModal');
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

        // Add Vendor Modal
        addVendorBtn.addEventListener('click', () => {
            openModal(addVendorModal, addVendorModal.querySelector('[id$="ModalContent"]'));
        });

        // Edit Vendor Modal
        document.querySelectorAll('.edit-vendor-btn').forEach(button => {
            button.addEventListener('click', () => {
                openModal(editVendorModal, editVendorModal.querySelector('[id$="ModalContent"]'));
            });
        });

        // Delete Vendor Modal
        document.querySelectorAll('.delete-vendor-btn').forEach(button => {
            button.addEventListener('click', () => {
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
                [addVendorModal, editVendorModal, deleteVendorModal].forEach(modal => {
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
