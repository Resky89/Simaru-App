@extends('Layout.app')

@section('title', 'Employee')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Employee Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">EMPLOYEE</h1>

                    <!-- Button Add Employee -->
                    <button id="addEmployeeBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 3.33334V12.6667" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M3.33331 8H12.6666" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span class="text-base">Add Employee</span>
                    </button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-[#203268] text-white">
                                <th class="p-3 text-xs font-bold text-left">ID</th>
                                <th class="p-3 text-xs font-bold text-left">First Name</th>
                                <th class="p-3 text-xs font-bold text-left">Last Name</th>
                                <th class="p-3 text-xs font-bold text-left">Departement</th>
                                <th class="p-3 text-xs font-bold text-left">Position</th>
                                <th class="p-3 text-xs font-bold text-left">Birth Date</th>
                                <th class="p-3 text-xs font-bold text-left">Phone Number</th>
                                <th class="p-3 text-xs font-bold text-left">Address</th>
                                <th class="p-3 text-xs font-bold text-center w-[88px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">001</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">John</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">Doe</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">IT</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">Developer</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">1990-01-01</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">123-456-7890</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">123 Main St</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                    <div class="flex justify-center gap-2">
                                        <button class="text-[#3D3D3D] hover:text-[#213268] edit-employee-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button class="text-[#3D3D3D] hover:text-red-500 delete-employee-btn">
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
                            <!-- Add more rows as needed -->
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

<!-- Add Employee Modal -->
<div id="addEmployeeModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div id="addEmployeeModalContent" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full opacity-0 translate-y-4 sm:translate-y-0 scale-95">
            <!-- Modal Header -->
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-[#203268]">ADD EMPLOYEE</h3>
                <button id="closeAddEmployeeModal" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form>
                    <!-- First Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" placeholder="Type here" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Last Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" placeholder="Type here" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Department -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departement</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option selected disabled>Select Departement</option>
                            <option>IT</option>
                            <option>HR</option>
                            <option>Finance</option>
                        </select>
                    </div>

                    <!-- Position -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                        <input type="text" placeholder="Type here" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Birth Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
                        <div class="relative">
                            <input type="text" placeholder="Choose Date" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" placeholder="Type here" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Address -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea rows="3" placeholder="Type here" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <!-- Save Button -->
                    <button type="submit" class="w-full bg-[#213268] text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div id="editEmployeeModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div id="editEmployeeModalContent" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full scale-95 opacity-0 translate-y-4 sm:translate-y-0">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-[#213268]" id="modal-title">EDIT EMPLOYEE</h3>
                    <button id="closeEditEmployeeModal" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form class="space-y-4">
                    <div>
                        <label for="editFirstName" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" name="editFirstName" id="editFirstName" placeholder="Type here" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                    </div>

                    <div>
                        <label for="editLastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" name="editLastName" id="editLastName" placeholder="Type here" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                    </div>

                    <div>
                        <label for="editDepartement" class="block text-sm font-medium text-gray-700">Departement</label>
                        <select name="editDepartement" id="editDepartement" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                            <option value="" disabled>Select Departement</option>
                            <option value="it">IT</option>
                            <option value="hr">HR</option>
                            <option value="finance">Finance</option>
                            <option value="marketing">Marketing</option>
                        </select>
                    </div>

                    <div>
                        <label for="editPosition" class="block text-sm font-medium text-gray-700">Position</label>
                        <input type="text" name="editPosition" id="editPosition" placeholder="Type here" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                    </div>

                    <div>
                        <label for="editBirthDate" class="block text-sm font-medium text-gray-700">Birth Date</label>
                        <div class="relative">
                            <input type="text" name="editBirthDate" id="editBirthDate" placeholder="Choose Date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#213268] focus:border-[#213268]" onfocus="(this.type='date')">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="editPhoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="editPhoneNumber" id="editPhoneNumber" placeholder="Type here" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                    </div>

                    <div>
                        <label for="editAddress" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="editAddress" id="editAddress" rows="4" placeholder="Type here" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#213268] focus:border-[#213268]"></textarea>
                    </div>

                    <div class="mt-5 sm:mt-6">
                        <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-[#213268] hover:bg-[#192652] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#213268]">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Employee Modal -->
<div id="deleteEmployeeModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div id="deleteEmployeeModalContent" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full scale-95 opacity-0 translate-y-4 sm:translate-y-0">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Delete Employee</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Are you sure you want to delete this employee? This action cannot be undone.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Delete</button>
                    <button type="button" id="closeDeleteEmployeeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#213268] sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal elements
        const addEmployeeModal = document.getElementById('addEmployeeModal');
        const addEmployeeModalContent = document.getElementById('addEmployeeModalContent');
        const addEmployeeBtn = document.getElementById('addEmployeeBtn');
        const closeAddEmployeeModal = document.getElementById('closeAddEmployeeModal');

        const editEmployeeModal = document.getElementById('editEmployeeModal');
        const editEmployeeModalContent = document.getElementById('editEmployeeModalContent');
        const editEmployeeButtons = document.querySelectorAll('.edit-employee-btn');
        const closeEditEmployeeModal = document.getElementById('closeEditEmployeeModal');

        const deleteEmployeeModal = document.getElementById('deleteEmployeeModal');
        const deleteEmployeeModalContent = document.getElementById('deleteEmployeeModalContent');
        const deleteEmployeeButtons = document.querySelectorAll('.delete-employee-btn');
        const closeDeleteEmployeeModal = document.getElementById('closeDeleteEmployeeModal');

        // Function to open modal
        function openModal(modal, modalContent) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0', 'translate-y-4', 'sm:translate-y-0');
                modalContent.classList.add('scale-100', 'opacity-100', 'translate-y-0');
            }, 10);
        }

        // Function to close modal
        function closeModal(modal, modalContent) {
            modalContent.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
            modalContent.classList.add('scale-95', 'opacity-0', 'translate-y-4', 'sm:translate-y-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Event listeners for Add Employee modal
        addEmployeeBtn.addEventListener('click', () => openModal(addEmployeeModal, addEmployeeModalContent));
        closeAddEmployeeModal.addEventListener('click', () => closeModal(addEmployeeModal, addEmployeeModalContent));

        // Event listener for Edit Employee modal
        closeEditEmployeeModal.addEventListener('click', () => closeModal(editEmployeeModal, editEmployeeModalContent));

        // Add click event listeners for edit buttons
        editEmployeeButtons.forEach(button => {
            button.addEventListener('click', () => openModal(editEmployeeModal, editEmployeeModalContent));
        });

        // Add click event listeners for delete buttons
        deleteEmployeeButtons.forEach(button => {
            button.addEventListener('click', () => openModal(deleteEmployeeModal, deleteEmployeeModalContent));
        });

        // Close modals when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === addEmployeeModal) closeModal(addEmployeeModal, addEmployeeModalContent);
            if (e.target === editEmployeeModal) closeModal(editEmployeeModal, editEmployeeModalContent);
            if (e.target === deleteEmployeeModal) closeModal(deleteEmployeeModal, deleteEmployeeModalContent);
        });
    });
</script>
@endpush
