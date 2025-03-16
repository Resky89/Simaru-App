@extends('Layout.app')

@section('title', 'Receipt Form')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Receipt Form Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">RECEIPT FORM</h1>
                </div>

                <!-- Receipt Form -->
                <form id="receiptForm" class="w-full space-y-6">
                    <!-- Top Row: Receipt Date, Delivered by, Received by -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Receipt Date -->
                        <div class="form-control">
                            <label class="block text-base font-medium text-[#666666] mb-2">Receipt Date</label>
                            <input type="date" value="2024-06-30"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                        </div>

                        <!-- Delivered by -->
                        <div class="form-control">
                            <label class="block text-base font-medium text-[#666666] mb-2">Delivered by</label>
                            <div class="flex">
                                <input type="text" value="John Doe"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            </div>
                        </div>

                        <!-- Received by -->
                        <div class="form-control">
                            <label class="block text-base font-medium text-[#666666] mb-2">Received by</label>
                            <input type="text" value="Finance"
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                        </div>
                    </div>

                    <!-- Bottom Row: Order Number and Comparison Number -->
                    <div class="space-y-6">
                        <!-- Comparison Number -->
                        <div class="form-control">
                            <label class="block text-base font-medium text-[#666666] mb-2">Quotation Number</label>
                            <div class="flex">
                                <input type="text" placeholder="Enter Comparison Number" id="comparisonNumber"
                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-l-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                <button type="button" id="searchBtn" class="bg-[#213268] text-white px-4 rounded-r-lg hover:bg-[#152451]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Order Details (Initially Hidden) -->
                    <div id="poDetails" class="border border-[#CCCCCC] rounded-lg p-4 bg-[#F9FAFB] mt-6 hidden">
                        <!-- PO Information -->
                        <div class="grid grid-cols-1 gap-3">
                            <!-- PO Number -->
                            <div class="flex items-start gap-2">
                                <p class="w-24 text-[#666666] font-medium">Quotation Number</p>
                                <p class="text-[#666666]">: <span id="poNumber">PB2406001</span></p>
                            </div>

                            <!-- Supplier -->
                            <div class="flex items-start gap-2">
                                <p class="w-24 text-[#666666] font-medium">Vendor</p>
                                <p class="text-[#666666]">: <span id="supplier">PT Setiawan Tbk</span></p>
                            </div>

                            <!-- PIC -->
                            <div class="flex items-start gap-2">
                                <p class="w-24 text-[#666666] font-medium">PIC</p>
                                <p class="text-[#666666]">: <span id="pic">Hartana Budiman</span></p>
                            </div>

                            <!-- PIC Contact -->
                            <div class="flex items-start gap-2">
                                <p class="w-24 text-[#666666] font-medium">PIC Contact</p>
                                <p class="text-[#666666]">: <span id="picContact">082403714145</span></p>
                            </div>

                            <!-- Input Date -->
                            <div class="flex items-start gap-2">
                                <p class="w-24 text-[#666666] font-medium">Quotation Date</p>
                                <p class="text-[#666666]">: <span id="inputDate">2024-06-30 07:03:46</span></p>
                            </div>
                        </div>

                        <!-- ASSET LIST -->
                        <div class="space-y-4">
                            <h2 class="text-lg font-semibold text-[#666666]">ASSET LIST</h2>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">ASSET NAME</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">SPECIFICATION</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">QTY</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">NOTES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Item 1 -->
                                        <tr class="border-t border-[#EEF1F4]">
                                            <td class="p-3 text-xs text-[#666666]">Motherboard</td>
                                            <td class="p-3 text-xs text-[#666666]">ATX</td>
                                            <td class="p-3 text-xs text-center text-[#666666]">1</td>
                                            <td class="p-3 text-xs text-[#666666]">
                                                <input type="text" placeholder="Add notes" class="w-full p-2 border border-[#CCCCCC] rounded-md text-[#666666]">
                                            </td>
                                        </tr>

                                        <!-- Item 2 -->
                                        <tr class="border-t border-[#EEF1F4]">
                                            <td class="p-3 text-xs text-[#666666]">CPU core i5</td>
                                            <td class="p-3 text-xs text-[#666666]">Gen 13</td>
                                            <td class="p-3 text-xs text-center text-[#666666]">1</td>
                                            <td class="p-3 text-xs text-[#666666]">
                                                <input type="text" placeholder="Add notes" class="w-full p-2 border border-[#CCCCCC] rounded-md text-[#666666]">
                                            </td>
                                        </tr>

                                        <!-- Item 3 -->
                                        <tr class="border-t border-[#EEF1F4]">
                                            <td class="p-3 text-xs text-[#666666]">RAM DDR4 8gb</td>
                                            <td class="p-3 text-xs text-[#666666]">3200MHz</td>
                                            <td class="p-3 text-xs text-center text-[#666666]">3</td>
                                            <td class="p-3 text-xs text-[#666666]">
                                                <input type="text" placeholder="Add notes" class="w-full p-2 border border-[#CCCCCC] rounded-md text-[#666666]">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex gap-4 mt-8">
                        <a href="{{ route('procurement.receipt') }}" class="px-6 py-3 bg-[#333333] text-white rounded-lg text-base hover:bg-gray-800 transform active:scale-[0.98] transition-all duration-200 uppercase">
                            BACK
                        </a>
                        <button type="submit" class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 uppercase">
                            SAVE
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
        const form = document.getElementById('receiptForm');
        const searchBtn = document.getElementById('searchBtn');
        const poDetails = document.getElementById('poDetails');

        // Search button functionality
        if (searchBtn) {
            searchBtn.addEventListener('click', function() {
                // In a real app, you would fetch data from the server based on the comparison number
                // For now, we'll just show the hidden section
                poDetails.classList.remove('hidden');
            });
        }

        // Form submission
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate form here

                // Show success message
                alert('Receipt has been saved successfully!');
                window.location.href = "{{ route('procurement.receipt') }}";
            });
        }
    });
</script>
@endpush
