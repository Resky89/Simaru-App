@extends('Layout.app')

@section('title', 'Request Form')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Request Form Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">REQUEST FORM</h1>
                </div>

                <!-- Form -->
                <form id="requestForm" class="w-full space-y-6">
                    @csrf
                    <input type="hidden" id="procurement_id" name="procurement_id">

                    <!-- Title -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Request Title</label>
                        <input type="text" id="title" name="title"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Request Title" required>
                    </div>

                    <!-- Priority  -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Priority</label>
                        <select id="priority" name="priority"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            required>
                            <option value="" disabled selected>Select priority</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>

                     <!-- Justification -->
                     <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Justification</label>
                        <textarea id="justification" name="justification"
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Justification" rows="3" required></textarea>
                    </div>

                    <!-- Item List -->
                    <div class="space-y-4">
                        <label class="block text-base font-semibold text-[#666666]">List Barang</label>

                        <div id="itemContainer" class="space-y-6">
                            <div class="item-entry p-6 border border-[#CCCCCC] rounded-lg relative">
                                <!-- Delete Button (On Border) - Initially hidden for first item -->
                                <button type="button" class="remove-item-btn absolute -top-4 -right-4 w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transform active:scale-[0.98] transition-all duration-200 shadow-md z-10 hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <!-- Item Name -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-[#666666]">Asset Name</label>
                                        <input type="text" name="details[0][asset_name]"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 asset-name"
                                            placeholder="Asset Name" required>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-[#666666]">Qty</label>
                                        <input type="number" name="details[0][quantity]"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 quantity"
                                            placeholder="Qty" min="1" required>
                                    </div>

                                    <!-- Unit Price -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-[#666666]">Unit Price</label>
                                        <input type="number" name="details[0][estimated_unit_price]"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 unit-price"
                                            placeholder="Unit Price" min="0" required>
                                    </div>
                                </div>

                                <!-- Specifications -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-[#666666]">Specifications</label>
                                    <textarea name="details[0][specifications]"
                                        class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 specifications"
                                        placeholder="Specifications" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Add Item Button -->
                        <div class="flex justify-end">
                            <button type="button" id="addItemBtn" class="w-12 h-12 rounded-full bg-[#213268] text-white flex items-center justify-center shadow-lg hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex gap-4 mt-8">
                        <a href="{{ route('procurement.request') }}" class="px-6 py-3 bg-[#333333] text-white rounded-lg text-base hover:bg-gray-800 transform active:scale-[0.98] transition-all duration-200">
                            CANCEL
                        </a>
                        <button type="submit" class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                            SUBMIT
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="fixed inset-0 bg-black opacity-50"></div>
    <div class="bg-white p-6 rounded-lg shadow-xl z-10 w-full max-w-md">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Success!</h3>
            <p class="mt-2 text-sm text-gray-500" id="successMessage">Your request has been submitted successfully.</p>
            <div class="mt-4">
                <button id="successModalClose" class="px-4 py-2 bg-[#213268] text-white rounded-md hover:bg-[#152451]">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="fixed inset-0 bg-black opacity-50"></div>
    <div class="bg-white p-6 rounded-lg shadow-xl z-10 w-full max-w-md">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Error!</h3>
            <p class="mt-2 text-sm text-gray-500" id="errorMessage">An error occurred. Please try again.</p>
            <div class="mt-4">
                <button id="errorModalClose" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addItemBtn = document.getElementById('addItemBtn');
        const itemContainer = document.getElementById('itemContainer');
        const requestForm = document.getElementById('requestForm');
        const successModal = document.getElementById('successModal');
        const errorModal = document.getElementById('errorModal');
        const successModalClose = document.getElementById('successModalClose');
        const errorModalClose = document.getElementById('errorModalClose');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');

        // Get procurement ID from URL if present (for edit mode)
        const urlParams = new URLSearchParams(window.location.search);
        const procurementId = urlParams.get('id');

        // If procurement ID exists, we're in edit mode
        if (procurementId) {
            // Set the hidden procurement_id field
            document.getElementById('procurement_id').value = procurementId;

            // Load procurement data
            loadProcurementData(procurementId);
        }

        // Function to load existing procurement data
        function loadProcurementData(id) {
            fetch(`/procurement/procurements/${id}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch procurement data');
                }
                return response.json();
            })
            .then(data => {
                if (data.status && data.data) {
                    const procurement = data.data;

                    // Fill in basic info
                    document.getElementById('title').value = procurement.title;
                    document.getElementById('priority').value = procurement.priority;
                    document.getElementById('justification').value = procurement.justification;

                    // Clear existing item entries
                    itemContainer.innerHTML = '';

                    // Add item entries for each detail
                    procurement.details.forEach((detail, index) => {
                        addItemEntry(index, detail);
                    });

                    // Update delete buttons visibility
                    updateDeleteButtons();
                } else {
                    showError('Failed to load procurement data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Failed to load procurement data: ' + error.message);
            });
        }

        // Function to update delete buttons visibility
        function updateDeleteButtons() {
            const items = itemContainer.querySelectorAll('.item-entry');

            // If there's only one item, hide its delete button
            if (items.length === 1) {
                items[0].querySelector('.remove-item-btn').classList.add('hidden');
            } else {
                // Show delete buttons for all items
                items.forEach(item => {
                    item.querySelector('.remove-item-btn').classList.remove('hidden');
                });
            }
        }

        // Add new item entry
        function addItemEntry(index, data = null) {
            const newItem = document.createElement('div');
            newItem.className = 'item-entry p-6 border border-[#CCCCCC] rounded-lg relative';
            newItem.innerHTML = `
                <!-- Delete Button (On Border) -->
                <button type="button" class="remove-item-btn absolute -top-4 -right-4 w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transform active:scale-[0.98] transition-all duration-200 shadow-md z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Item Name -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#666666]">Asset Name</label>
                        <input type="text" name="details[${index}][asset_name]"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 asset-name"
                            placeholder="Asset name" required value="${data ? data.asset_name : ''}">
                    </div>

                    <!-- Quantity -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#666666]">Qty</label>
                        <input type="number" name="details[${index}][quantity]"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 quantity"
                            placeholder="Qty" min="1" required value="${data ? data.quantity : ''}">
                    </div>

                    <!-- Unit Price -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#666666]">Unit Price</label>
                        <input type="number" name="details[${index}][estimated_unit_price]"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 unit-price"
                            placeholder="Unit Price" min="0" required value="${data ? data.estimated_unit_price : ''}">
                    </div>
                </div>

                <!-- Specifications -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-[#666666]">Specifications</label>
                    <textarea name="details[${index}][specifications]"
                        class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200 specifications"
                        placeholder="Specifications" rows="2">${data ? data.specifications || '' : ''}</textarea>
                </div>
            `;

            itemContainer.appendChild(newItem);

            // Add event listener to the new remove button
            const removeBtn = newItem.querySelector('.remove-item-btn');
            removeBtn.addEventListener('click', function() {
                newItem.remove();
                // Update delete buttons after removing an item
                updateDeleteButtons();
                // Update input names
                updateInputNames();
            });
        }

        // Add new item when clicking the add button
        addItemBtn.addEventListener('click', function() {
            const itemCount = itemContainer.querySelectorAll('.item-entry').length;
            addItemEntry(itemCount);
            // Update delete buttons after adding a new item
            updateDeleteButtons();
        });

        // Function to update input names after removing items
        function updateInputNames() {
            const items = itemContainer.querySelectorAll('.item-entry');
            items.forEach((item, index) => {
                const assetName = item.querySelector('.asset-name');
                const quantity = item.querySelector('.quantity');
                const unitPrice = item.querySelector('.unit-price');
                const specifications = item.querySelector('.specifications');
                const assetJustification = item.querySelector('.asset-justification');

                assetName.name = `details[${index}][asset_name]`;
                quantity.name = `details[${index}][quantity]`;
                unitPrice.name = `details[${index}][estimated_unit_price]`;
                specifications.name = `details[${index}][specifications]`;
                assetJustification.name = `details[${index}][justification]`;
            });
        }

        // Show success modal
        function showSuccess(message) {
            successMessage.textContent = message;
            successModal.classList.remove('hidden');
        }

        // Show error modal
        function showError(message) {
            errorMessage.textContent = message;
            errorModal.classList.remove('hidden');
        }

        // Close success modal and redirect
        successModalClose.addEventListener('click', function() {
            successModal.classList.add('hidden');
            window.location.href = '{{ route("procurement.request") }}';
        });

        // Close error modal
        errorModalClose.addEventListener('click', function() {
            errorModal.classList.add('hidden');
        });

        // Initialize delete buttons visibility
        updateDeleteButtons();

        // Handle form submission
        requestForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Collect form data
            const formData = new FormData(requestForm);
            const data = {};

            // Convert FormData to object
            for (const [key, value] of formData.entries()) {
                // Handle nested objects (for details array)
                if (key.includes('[')) {
                    const mainKey = key.substring(0, key.indexOf('['));
                    const subKey = key.substring(key.indexOf('[') + 1, key.indexOf(']'));
                    const subSubKey = key.includes('][') ? key.substring(key.lastIndexOf('[') + 1, key.lastIndexOf(']')) : null;

                    if (!data[mainKey]) {
                        data[mainKey] = [];
                    }

                    if (!data[mainKey][subKey]) {
                        data[mainKey][subKey] = {};
                    }

                    // Convert numeric fields to numbers
                    let processedValue = value;
                    if (subSubKey === 'quantity' || subSubKey === 'estimated_unit_price') {
                        processedValue = Number(value);
                    }

                    if (subSubKey) {
                        data[mainKey][subKey][subSubKey] = processedValue;
                    } else {
                        data[mainKey][subKey] = processedValue;
                    }
                } else {
                    data[key] = value;
                }
            }

            // Convert details object to array
            if (data.details) {
                const detailsArray = [];
                Object.keys(data.details).forEach(key => {
                    detailsArray.push(data.details[key]);
                });
                data.details = detailsArray;
            }

            // Determine if this is a create or update operation
            const isUpdate = procurementId ? true : false;
            const url = isUpdate
                ? `/procurement/procurements/${procurementId}`
                : '/procurement/procurements';
            const method = isUpdate ? 'PUT' : 'POST';

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send the request
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-HTTP-Method-Override': isUpdate ? 'PUT' : 'POST'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'An error occurred');
                    });
                }
                return response.json();
            })
            .then(result => {
                if (result.status) {
                    showSuccess(isUpdate ? 'Procurement updated successfully' : 'Procurement created successfully');
                } else {
                    showError(result.message || 'An error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError(error.message || 'An error occurred while processing your request');
            });
        });
    });
</script>
@endpush
