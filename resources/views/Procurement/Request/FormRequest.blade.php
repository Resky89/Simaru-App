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
                    <!-- Title -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Request Title</label>
                        <input type="text"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Request Title">
                    </div>

                    <!-- Priority  -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Priority</label>
                        <select
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="" disabled selected>Select priority</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>

                     <!-- Justification -->
                     <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Justification</label>
                        <textarea
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Justification" rows="3"></textarea>
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
                                        <input type="text"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Asset Name">
                                    </div>

                                    <!-- Quantity -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-[#666666]">Qty</label>
                                        <input type="number"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Qty">
                                    </div>

                                    <!-- Unit Price -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-[#666666]">Unit Price</label>
                                        <input type="number"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Unit Price">
                                    </div>
                                </div>

                                <!-- Specifications -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-[#666666]">Specifications</label>
                                    <textarea
                                        class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addItemBtn = document.getElementById('addItemBtn');
        const itemContainer = document.getElementById('itemContainer');
        const requestForm = document.getElementById('requestForm');

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
        addItemBtn.addEventListener('click', function() {
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
                        <input type="text"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Asset name">
                    </div>

                    <!-- Quantity -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#666666]">Qty</label>
                        <input type="number"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Qty">
                    </div>

                    <!-- Unit Price -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-[#666666]">Unit Price</label>
                        <input type="number"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Unit Price">
                    </div>
                </div>

                <!-- Specifications -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-[#666666]">Specifications</label>
                    <textarea
                        class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                        placeholder="Specifications" rows="2"></textarea>
                </div>
            `;

            itemContainer.appendChild(newItem);

            // Add event listener to the new remove button
            const removeBtn = newItem.querySelector('.remove-item-btn');
            removeBtn.addEventListener('click', function() {
                newItem.remove();
                // Update delete buttons after removing an item
                updateDeleteButtons();
            });

            // Update delete buttons after adding a new item
            updateDeleteButtons();
        });

        // Delegate event listener for delete buttons
        itemContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-btn')) {
                const items = itemContainer.querySelectorAll('.item-entry');

                // Don't allow deletion if there's only one item
                if (items.length > 1) {
                    e.target.closest('.item-entry').remove();
                    updateDeleteButtons();
                }
            }
        });

        // Initialize delete buttons visibility
        updateDeleteButtons();

        // Handle form submission
        requestForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Here you would collect all the form data and send it to the server
            alert('Form submitted! In a real application, this would save the request.');

            // You can implement AJAX submission or form redirect as needed
        });
    });
</script>
@endpush
