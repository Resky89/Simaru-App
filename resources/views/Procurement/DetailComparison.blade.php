@extends('Layout.app')

@section('title', 'Detail Comparison')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Comparison Detail Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">VENDOR PRICE LIST</h1>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <a href="{{ route('procurement.price-comparison') }}" class="px-4 py-2 bg-[#213268] text-white rounded-md hover:bg-[#152451] transition-all duration-200 uppercase text-sm font-medium">
                        Cancel
                    </a>
                    <a href="{{ route('procurement.form-comparison', ['id' => $id ?? 1]) }}" class="px-4 py-2 bg-[#213268] text-white rounded-md hover:bg-[#152451] transition-all duration-200 uppercase text-sm font-medium">
                        Add vendor
                    </a>
                </div>

                <!-- Success Message (hidden by default) -->
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md hidden" id="successMessage">
                    <p>Success! Data has been saved successfully.</p>
                </div>

                <!-- Request Details -->
                <div class="grid grid-cols-1 gap-5">
                    <!-- Request Number -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Quotation ID</p>
                        <p class="text-[#666666]">: <span id="requestNumber">PPB2406001</span></p>
                    </div>

                    <!-- Request Name -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Request Title</p>
                        <p class="text-[#666666]">: <span id="requestName">Pembelian Komputer IT</span></p>
                    </div>

                    <!-- User Input -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Quotation by</p>
                        <p class="text-[#666666]">: <span id="userInput">Karyawan</span></p>
                    </div>

                    <!-- Input Date -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Quotation Date</p>
                        <p class="text-[#666666]">: <span id="inputDate">2024-06-30 06:52:12</span></p>
                    </div>
                </div>

                <!-- Item List -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-[#666666]">Price Comparison</h2>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Name</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Qty</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Estimated Price</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                        <div class="flex items-center justify-between">
                                            <span>PT WIBOWO (PERSERO) TBK</span>
                                            <div class="flex gap-2">
                                                <button class="text-white hover:text-gray-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button class="text-white hover:text-gray-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                        <div class="flex items-center justify-between">
                                            <span>PT SETIAWAN</span>
                                            <div class="flex gap-2">
                                                <button class="text-white hover:text-gray-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button class="text-white hover:text-gray-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Item 1 -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td class="p-3 text-xs text-[#666666]">Motherboard</td>
                                    <td class="p-3 text-xs text-center text-[#666666]">1</td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">2,500,000</div>
                                        <span class="text-xs text-gray-500">@2,500,000</span>
                                    </td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">2,450,000</div>
                                        <span class="text-xs text-gray-500">@2,450,000</span>
                                    </td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">2,475,000</div>
                                        <span class="text-xs text-gray-500">@2,475,000</span>
                                    </td>
                                </tr>

                                <!-- Item 2 -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td class="p-3 text-xs text-[#666666]">CPU core i5</td>
                                    <td class="p-3 text-xs text-center text-[#666666]">1</td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">3,500,000</div>
                                        <span class="text-xs text-gray-500">@3,500,000</span>
                                    </td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">3,400,000</div>
                                        <span class="text-xs text-gray-500">@3,400,000</span>
                                    </td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">3,450,000</div>
                                        <span class="text-xs text-gray-500">@3,450,000</span>
                                    </td>
                                </tr>

                                <!-- Item 3 -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td class="p-3 text-xs text-[#666666]">RAM DDR4 8gb</td>
                                    <td class="p-3 text-xs text-center text-[#666666]">3</td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">750,000</div>
                                        <span class="text-xs text-gray-500">@250,000</span>
                                    </td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">720,000</div>
                                        <span class="text-xs text-gray-500">@240,000</span>
                                    </td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">735,000</div>
                                        <span class="text-xs text-gray-500">@245,000</span>
                                    </td>
                                </tr>

                                <!-- Payment Terms Row -->
                                <tr class="border-t border-[#EEF1F4] bg-[#E9ECF6]">
                                    <td class="p-3 text-xs font-medium text-left text-[#213268]">Payment Terms</td>
                                    <td colspan="2" class="p-3 text-xs text-[#666666]"></td>
                                    <td class="p-3 text-xs text-[#666666]">pembayaran melalui bank bri</td>
                                    <td class="p-3 text-xs text-[#666666]">pembayaran melalui bank niaga</td>
                                </tr>

                                <!-- Delivery Terms Row -->
                                <tr class="border-t border-[#EEF1F4] bg-[#E9ECF6]">
                                    <td class="p-3 text-xs font-medium text-left text-[#213268]">Delivery Terms</td>
                                    <td colspan="2" class="p-3 text-xs text-[#666666]"></td>
                                    <td class="p-3 text-xs text-[#666666]">pengiriman ke alamat kantor</td>
                                    <td class="p-3 text-xs text-[#666666]">pengiriman dalam waktu 1 minggu</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex gap-4 mt-8">
                    <button type="button" class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 uppercase">
                        DONE
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
        // Success message should be hidden by default
        const successMessage = document.getElementById('successMessage');
        if (successMessage) {
            successMessage.classList.add('hidden');
        }

        // Handle DONE button
        const doneBtn = document.querySelector('button.uppercase');

        if (doneBtn) {
            doneBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to mark this request as completed?')) {
                    // Here you would send an AJAX request to update the status

                    // Show success message
                    successMessage.classList.remove('hidden');

                    // Hide success message after 5 seconds
                    setTimeout(function() {
                        successMessage.classList.add('hidden');
                    }, 5000);
                }
            });
        }
    });
</script>
@endpush
