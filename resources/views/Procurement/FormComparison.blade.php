@extends('Layout.app')

@section('title', 'Price Comparison Form')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Price Comparison Form Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PRICE COMPARISON FORM</h1>
                </div>

                <!-- Search Section -->
                <div class="space-y-4">
                    <!-- Quotation Title -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Quotation Title</label>
                        <input type="text" id="comparisonTitle" value=""
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Pembelian toner printer">
                    </div>

                    <!-- Request Number -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Request Number</label>
                        <div class="flex">
                            <input type="text" id="requestNumber" value=""
                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-l-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                placeholder="PPB2406002">
                            <button id="searchBtn" type="button" class="bg-[#213268] text-white px-4 rounded-r-lg hover:bg-[#152451]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Request Details Section - Hidden by default -->
                <div id="requestDetails" class="hidden">
                    <!-- Request Details -->
                    <div class="grid grid-cols-1 gap-5">
                        <!-- Request Number -->
                        <div class="flex items-start gap-2">
                            <p class="w-32 text-[#666666] font-medium">Request Number</p>
                            <p class="text-[#666666]">: <span id="displayRequestNumber">PPB2406002</span></p>
                        </div>

                        <!-- Request Title -->
                        <div class="flex items-start gap-2">
                            <p class="w-32 text-[#666666] font-medium">Request Title</p>
                            <p class="text-[#666666]">: <span id="displayRequestName">Pembelian Komputer IT</span></p>
                        </div>

                        <!-- Requester -->
                        <div class="flex items-start gap-2">
                            <p class="w-32 text-[#666666] font-medium">Requester</p>
                            <p class="text-[#666666]">: <span id="displayUserInput">Karyawan</span></p>
                        </div>

                        <!-- Request Date -->
                        <div class="flex items-start gap-2">
                            <p class="w-32 text-[#666666] font-medium">Request Date</p>
                            <p class="text-[#666666]">: <span id="displayInputDate">2024-06-30 06:56:02</span></p>
                        </div>
                    </div>

                    <!-- Asset List -->
                    <div class="space-y-4 mt-6">
                        <h2 class="text-lg font-semibold text-[#666666]">Asset List</h2>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">ASSET NAME</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">SPESIFICATION</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">QTY</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">UNIT PRICE</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Item 1 -->
                                    <tr class="border-t border-[#EEF1F4]">
                                        <td class="p-3 text-xs text-[#666666]">Motherboard</td>
                                        <td class="p-3 text-xs text-[#666666]">gigabyte</td>
                                        <td class="p-3 text-xs text-center text-[#666666]">1</td>
                                        <td class="p-3 text-xs text-left text-[#666666]">2,500,000</td>
                                        <td class="p-3 text-xs text-left text-[#666666]">2,500,000</td>
                                    </tr>

                                    <!-- Item 2 -->
                                    <tr class="border-t border-[#EEF1F4]">
                                        <td class="p-3 text-xs text-[#666666]">CPU core i5</td>
                                        <td class="p-3 text-xs text-[#666666]">intel</td>
                                        <td class="p-3 text-xs text-center text-[#666666]">1</td>
                                        <td class="p-3 text-xs text-left text-[#666666]">3,500,000</td>
                                        <td class="p-3 text-xs text-left text-[#666666]">3,500,000</td>
                                    </tr>

                                    <!-- Item 3 -->
                                    <tr class="border-t border-[#EEF1F4]">
                                        <td class="p-3 text-xs text-[#666666]">RAM DDR4 8gb</td>
                                        <td class="p-3 text-xs text-[#666666]">corsair</td>
                                        <td class="p-3 text-xs text-center text-[#666666]">3</td>
                                        <td class="p-3 text-xs text-left text-[#666666]">250,000</td>
                                        <td class="p-3 text-xs text-left text-[#666666]">750,000</td>
                                    </tr>

                                    <!-- Grand Total -->
                                    <tr class="border-t border-[#EEF1F4]">
                                        <td colspan="4" class="p-3 text-xs font-medium text-right text-[#666666]">Grand Total</td>
                                        <td class="p-3 text-xs font-medium text-left text-[#666666]">6,750,000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex gap-4 mt-8">
                        <a href="{{ route('procurement.request') }}" class="px-6 py-3 bg-[#333333] text-white rounded-lg text-base hover:bg-gray-800 transform active:scale-[0.98] transition-all duration-200 uppercase">
                            BACK
                        </a>
                        <button type="button" id="submitBtn" class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 uppercase">
                            SUBMIT
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchBtn = document.getElementById('searchBtn');
        const requestDetails = document.getElementById('requestDetails');
        const requestNumber = document.getElementById('requestNumber');
        const comparisonTitle = document.getElementById('comparisonTitle');
        const submitBtn = document.getElementById('submitBtn');

        // Search button click event
        searchBtn.addEventListener('click', function() {
            // Validate inputs
            if (!requestNumber.value.trim()) {
                alert('Mohon masukkan Nomor Pengajuan');
                return;
            }

            // In a real application, this would fetch data from an API
            // For now, show hardcoded data similar to the DetailRequest.blade.php example

            // Update displayed request details
            document.getElementById('displayRequestNumber').textContent = requestNumber.value;
            document.getElementById('displayRequestName').textContent = comparisonTitle.value || 'Pembelian Komputer IT';
            document.getElementById('displayUserInput').textContent = 'Karyawan';
            document.getElementById('displayInputDate').textContent = '2024-06-30 06:56:02';

            // Show the request details section
            requestDetails.classList.remove('hidden');
        });

        // Submit button click event
        if (submitBtn) {
            submitBtn.addEventListener('click', function() {
                alert('Price comparison has been created successfully!');
                // In a real app, you'd submit the form or redirect
                window.location.href = "{{ route('procurement.price-comparison') }}";
            });
        }
    });
</script>
@endpush
