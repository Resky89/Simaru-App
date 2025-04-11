@extends('Layout.app')

@section('title', 'Detail Request')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Request Detail Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
             <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DETAIL REQUEST</h1>
            </div>
             <!-- Request Details -->
                <div class="grid grid-cols-1 gap-5">
                    <!-- Request Number -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Request ID</p>
                        <p class="text-[#666666]">: <span id="requestNumber">PPB2406001</span></p>
                    </div>

                    <!-- Request Name -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Request Title</p>
                        <p class="text-[#666666]">: <span id="requestName">Pembelian Komputer IT</span></p>
                    </div>

                    <!-- User Input -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Requester</p>
                        <p class="text-[#666666]">: <span id="userInput">Karyawan</span></p>
                    </div>

                    <!-- Input Date -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Request Date</p>
                        <p class="text-[#666666]">: <span id="inputDate">2024-06-30 06:52:12</span></p>
                    </div>
                </div>

                <!-- Item List -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-[#666666]">Asset List</h2>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Name</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Specification</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Qty</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Unit Price</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Total</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Price Comparison</th>
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
                                    <td class="p-3 text-xs text-center text-red-500">Waiting</td>
                                </tr>

                                <!-- Item 2 -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td class="p-3 text-xs text-[#666666]">CPU core i5</td>
                                    <td class="p-3 text-xs text-[#666666]">intel</td>
                                    <td class="p-3 text-xs text-center text-[#666666]">1</td>
                                    <td class="p-3 text-xs text-left text-[#666666]">3,500,000</td>
                                    <td class="p-3 text-xs text-left text-[#666666]">3,500,000</td>
                                    <td class="p-3 text-xs text-center text-red-500">Waiting</td>
                                </tr>

                                <!-- Item 3 -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td class="p-3 text-xs text-[#666666]">RAM DDR4 8gb</td>
                                    <td class="p-3 text-xs text-[#666666]">corsair</td>
                                    <td class="p-3 text-xs text-center text-[#666666]">3</td>
                                    <td class="p-3 text-xs text-left text-[#666666]">250,000</td>
                                    <td class="p-3 text-xs text-left text-[#666666]">750,000</td>
                                    <td class="p-3 text-xs text-center text-red-500">Waiting</td>
                                </tr>

                                <!-- Grand Total -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td colspan="4" class="p-3 text-xs font-medium text-right text-[#666666]">Grand Total</td>
                                    <td class="p-3 text-xs font-medium text-right text-[#666666]">6,750,000</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Comparison Title -->
                <div class="space-y-2">
                    <label class="block text-base font-semibold text-[#666666]">Make Price Comparison</label>
                    <input type="text"
                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                        placeholder="Enter comparison title">
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
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // If you need to load the data dynamically, you can do it here
        // For example, fetching the request details from an API

        // Example:
        fetch('/api/procurement/request/1')
            .then(response => response.json())
            .then(data => {
                document.getElementById('requestNumber').textContent = data.number;
                document.getElementById('requestName').textContent = data.name;
                document.getElementById('userInput').textContent = data.user;
                document.getElementById('inputDate').textContent = data.date;

                // Populate the items table
                // ...
            });
    });
</script>
@endpush
