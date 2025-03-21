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
                                <th class="p-3 text-xs font-bold text-left">Department</th>
                                <th class="p-3 text-xs font-bold text-left">Position</th>
                                <th class="p-3 text-xs font-bold text-left">Birth Date</th>
                                <th class="p-3 text-xs font-bold text-left">Phone Number</th>
                                <th class="p-3 text-xs font-bold text-left">Address</th>
                                <th class="p-3 text-xs font-bold text-center w-[88px]">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($employees) && count($employees) > 0)
                                @foreach($employees as $employee)
                                <tr>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $employee['employee_id'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $employee['first_name'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $employee['last_name'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $employee['department_name'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $employee['position'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $employee['date_of_birth'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $employee['phone_number'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $employee['address'] }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        <div class="flex justify-center gap-2">
                                            <button class="text-[#3D3D3D] hover:text-[#213268] edit-employee-btn"
                                                    data-id="{{ $employee['employee_id'] }}"
                                                    data-first-name="{{ $employee['first_name'] }}"
                                                    data-last-name="{{ $employee['last_name'] }}"
                                                    data-department-id="{{ $employee['department_id'] }}"
                                                    data-position="{{ $employee['position'] }}"
                                                    data-birth-date="{{ $employee['date_of_birth'] }}"
                                                    data-phone-number="{{ $employee['phone_number'] }}"
                                                    data-address="{{ $employee['address'] }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button class="text-[#3D3D3D] hover:text-red-500 delete-employee-btn"
                                                    data-id="{{ $employee['employee_id'] }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No employees found</td>
                                </tr>
                            @endif
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

<!-- Modal Add Employee -->
<div id="addEmployeeModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                 id="addEmployeeModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">ADD EMPLOYEE</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" id="closeAddEmployeeModal">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <form id="createEmployeeForm" action="{{ route('employees.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- First Name Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">First Name</label>
                                <input type="text" name="first_name"
                                       class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                       placeholder="Type here" required>
                            </div>

                            <!-- Last Name Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Last Name</label>
                                <input type="text" name="last_name"
                                       class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                       placeholder="Type here" required>
                            </div>

                            <!-- Department Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Department</label>
                                <select name="department_id"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                        required>
                                    <option value="">Select Department</option>
                                    @if(isset($departments) && count($departments) > 0)
                                        @foreach($departments as $department)
                                            <option value="{{ $department['department_id'] }}">{{ $department['department_name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- Position Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Position</label>
                                <input type="text" name="position"
                                       class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                       placeholder="Type here" required>
                            </div>

                            <!-- Birth Date Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Birth Date</label>
                                <input type="date" name="date_of_birth"
                                       class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                       required>
                            </div>

                            <!-- Phone Number Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Phone Number</label>
                                <input type="tel" name="phone_number"
                                       class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                       placeholder="Type here" required>
                            </div>

                            <!-- Address Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Address</label>
                                <textarea name="address" rows="3"
                                          class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                          placeholder="Type here" required></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button type="submit"
                                        class="w-full bg-[#213268] text-white py-3 rounded-lg hover:bg-[#162348] transition-colors duration-200">
                                    SAVE
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Employee -->
<div id="editEmployeeModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                 id="editEmployeeModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">EDIT EMPLOYEE</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" id="closeEditEmployeeModal">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <form id="editEmployeeForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editEmployeeId" name="employee_id">

                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- First Name Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">First Name</label>
                                <input type="text" id="editFirstName" name="first_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here" required>
                            </div>

                            <!-- Last Name Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Last Name</label>
                                <input type="text" id="editLastName" name="last_name"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here" required>
                            </div>

                            <!-- Department Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Department</label>
                                <select id="editDepartmentId" name="department_id"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department['department_id'] }}">{{ $department['department_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Position Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Position</label>
                                <input type="text" id="editPosition" name="position"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here" required>
                            </div>

                            <!-- Birth Date Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Birth Date</label>
                                <input type="date" id="editBirthDate" name="date_of_birth"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    required>
                            </div>

                            <!-- Phone Number Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">Phone Number</label>
                                <input type="text" id="editPhoneNumber" name="phone_number"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Type here" required>
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

<!-- Modal Delete Employee -->
<div id="deleteEmployeeModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                 id="deleteEmployeeModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">DELETE EMPLOYEE</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" id="closeDeleteEmployeeModal">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <div class="text-center mb-6">
                        <p class="text-gray-700">Are you sure you want to delete this employee? This action cannot be undone.</p>
                    </div>
                    <form id="deleteEmployeeForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="employee_id" id="delete_employee_id">

                        <div class="flex gap-4">
                            <button type="button" id="cancelDeleteEmployeeBtn" class="flex-1 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                CANCEL
                            </button>
                            <button type="submit" class="flex-1 bg-red-500 text-white py-3 rounded-lg hover:bg-red-600 transition-colors duration-200">
                                DELETE
                            </button>
                        </div>
                    </form>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addEmployeeBtn = document.getElementById('addEmployeeBtn');
        const addEmployeeModal = document.getElementById('addEmployeeModal');
        const editEmployeeModal = document.getElementById('editEmployeeModal');
        const deleteEmployeeModal = document.getElementById('deleteEmployeeModal');
        const closeButtons = document.querySelectorAll('.close-modal');

        // Show toast notifications for session messages on page load
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

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

        // Add Employee Modal
        addEmployeeBtn.addEventListener('click', () => {
            openModal(addEmployeeModal, addEmployeeModal.querySelector('[id$="ModalContent"]'));
        });

        // Edit Employee Modal
        document.querySelectorAll('.edit-employee-btn').forEach(button => {
            button.addEventListener('click', () => {
                const employeeId = button.getAttribute('data-id');

                // Gunakan URL yang benar untuk aksi form
                document.getElementById('editEmployeeForm').action = `{{ route('employees.update', '') }}/${employeeId}`;
                console.log('Edit form action set to:', document.getElementById('editEmployeeForm').action);

                // Set form values
                document.getElementById('editFirstName').value = button.getAttribute('data-first-name');
                document.getElementById('editLastName').value = button.getAttribute('data-last-name');
                document.getElementById('editDepartmentId').value = button.getAttribute('data-department-id');
                document.getElementById('editPosition').value = button.getAttribute('data-position');
                document.getElementById('editBirthDate').value = button.getAttribute('data-birth-date');
                document.getElementById('editPhoneNumber').value = button.getAttribute('data-phone-number');
                document.getElementById('editAddress').value = button.getAttribute('data-address') || '';

                openModal(editEmployeeModal, editEmployeeModal.querySelector('[id$="ModalContent"]'));
            });
        });

        // Delete Employee Modal
        document.querySelectorAll('.delete-employee-btn').forEach(button => {
            button.addEventListener('click', () => {
                const employeeId = button.getAttribute('data-id');

                // Set the correct URL for delete action
                document.getElementById('deleteEmployeeForm').action = `{{ route('employees.destroy', '') }}/${employeeId}`;
                document.getElementById('delete_employee_id').value = employeeId;
                console.log('Delete form action set to:', document.getElementById('deleteEmployeeForm').action);

                openModal(deleteEmployeeModal, deleteEmployeeModal.querySelector('[id$="ModalContent"]'));
            });
        });

        // Close Modal Handlers - Fix for all close buttons
        document.getElementById('closeAddEmployeeModal').addEventListener('click', () => {
            closeModal(addEmployeeModal, addEmployeeModal.querySelector('[id$="ModalContent"]'));
        });

        document.getElementById('closeEditEmployeeModal').addEventListener('click', () => {
            closeModal(editEmployeeModal, editEmployeeModal.querySelector('[id$="ModalContent"]'));
        });

        document.getElementById('closeDeleteEmployeeModal').addEventListener('click', () => {
            closeModal(deleteEmployeeModal, deleteEmployeeModal.querySelector('[id$="ModalContent"]'));
        });

        // Cancel button for delete modal
        document.getElementById('cancelDeleteEmployeeBtn').addEventListener('click', () => {
            closeModal(deleteEmployeeModal, deleteEmployeeModal.querySelector('[id$="ModalContent"]'));
        });

        // Close on outside click - improved implementation
        [addEmployeeModal, editEmployeeModal, deleteEmployeeModal].forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
                    const content = modal.querySelector('[id$="ModalContent"]');
                    closeModal(modal, content);
                }
            });
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                [addEmployeeModal, editEmployeeModal, deleteEmployeeModal].forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        const content = modal.querySelector('[id$="ModalContent"]');
                        closeModal(modal, content);
                    }
                });
            }
        });

        // Add form submit event listeners with debugging
        document.getElementById('createEmployeeForm').addEventListener('submit', function(e) {
            console.log('Create form submitted', this.action);
            // Let the form submit normally
        });

        document.getElementById('editEmployeeForm').addEventListener('submit', function(e) {
            console.log('Edit form submitted', this.action);
            // Let the form submit normally
        });

        document.getElementById('deleteEmployeeForm').addEventListener('submit', function(e) {
            console.log('Delete form submitted', this.action);
            // Let the form submit normally
        });

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