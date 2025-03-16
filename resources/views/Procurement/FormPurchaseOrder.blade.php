@extends('Layout.app')

@section('title', 'Purchase Order Form')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Purchase Order Form Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PURCHASE ORDER</h1>
                </div>

                <!-- Search Section -->
                <div class="space-y-4">
                    <label class="block text-base font-semibold text-[#666666]">Quotation Number</label>
                    <div class="flex">
                        <input type="text" value="PH2406001"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-l-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                        <button class="bg-[#213268] text-white px-4 rounded-r-lg hover:bg-[#152451]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="grid grid-cols-1 gap-4">
                    <!-- Nomor -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Quotation Number</p>
                        <p class="text-[#666666]">: <span>PH2406001</span></p>
                    </div>

                    <!-- Judul -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Request Title</p>
                        <p class="text-[#666666]">: <span>Pembelian Komputer IT</span></p>
                    </div>

                    <!-- User Input -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">User Input</p>
                        <p class="text-[#666666]">: <span>Staff</span></p>
                    </div>

                    <!-- Tanggal Input -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Quotation Date</p>
                        <p class="text-[#666666]">: <span>2024-06-30 06:52:12</span></p>
                    </div>
                </div>

                <form id="purchaseOrderForm" class="w-full space-y-6">
                    <!-- Item List -->
                    <div class="space-y-4">
                        <label class="block text-base font-semibold text-[#666666]">ASSET LIST</label>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">ASSET NAME</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">QTY</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">ESTIMATED PRICE</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">PT WIBOWO (PERSERO) TBK</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">PT SETIAWAN</th>
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
                                        <td class="p-3">
                                            <div class="flex items-center">
                                                <input type="radio" name="vendor_item1" value="1" class="mr-2">
                                                <div>
                                                    <div class="text-sm font-medium text-[#666666]">2,450,000</div>
                                                    <span class="text-xs text-gray-500">@2,450,000</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <div class="flex items-center">
                                                <input type="radio" name="vendor_item1" value="2" class="mr-2">
                                                <div>
                                                    <div class="text-sm font-medium text-[#666666]">2,475,000</div>
                                                    <span class="text-xs text-gray-500">@2,475,000</span>
                                                </div>
                                            </div>
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
                                        <td class="p-3">
                                            <div class="flex items-center">
                                                <input type="radio" name="vendor_item2" value="1" class="mr-2">
                                                <div>
                                                    <div class="text-sm font-medium text-[#666666]">3,400,000</div>
                                                    <span class="text-xs text-gray-500">@3,400,000</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <div class="flex items-center">
                                                <input type="radio" name="vendor_item2" value="2" class="mr-2">
                                                <div>
                                                    <div class="text-sm font-medium text-[#666666]">3,450,000</div>
                                                    <span class="text-xs text-gray-500">@3,450,000</span>
                                                </div>
                                            </div>
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
                                        <td class="p-3">
                                            <div class="flex items-center">
                                                <input type="radio" name="vendor_item3" value="1" class="mr-2">
                                                <div>
                                                    <div class="text-sm font-medium text-[#666666]">720,000</div>
                                                    <span class="text-xs text-gray-500">@240,000</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <div class="flex items-center">
                                                <input type="radio" name="vendor_item3" value="2" class="mr-2">
                                                <div>
                                                    <div class="text-sm font-medium text-[#666666]">735,000</div>
                                                    <span class="text-xs text-gray-500">@245,000</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Payment Terms -->
                                    <tr class="border-t border-[#EEF1F4] bg-[#E9ECF6]">
                                        <td class="p-3 text-xs font-medium text-[#213268]">Payment Terms</td>
                                        <td colspan="2" class="p-3"></td>
                                        <td class="p-3 text-xs text-[#666666]">pembayaran melalui bank bri</td>
                                        <td class="p-3 text-xs text-[#666666]">pembayaran melalui bank niaga</td>
                                    </tr>

                                    <!-- Delivery Terms -->
                                    <tr class="border-t border-[#EEF1F4] bg-[#E9ECF6]">
                                        <td class="p-3 text-xs font-medium text-[#213268]">Delivery Terms</td>
                                        <td colspan="2" class="p-3"></td>
                                        <td class="p-3 text-xs text-[#666666]">pengiriman ke alamat kantor</td>
                                        <td class="p-3 text-xs text-[#666666]">pengiriman dalam waktu 1 minggu</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Notes</label>
                        <textarea
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Add Notes (optional)" rows="3"></textarea>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex gap-4 mt-8">
                        <a href="{{ route('procurement.price-comparison') }}" class="px-6 py-3 bg-[#333333] text-white rounded-lg text-base hover:bg-gray-800 transform active:scale-[0.98] transition-all duration-200 uppercase">
                            CANCEL
                        </a>
                        <button type="submit" class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 uppercase">
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
        const form = document.getElementById('purchaseOrderForm');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate that a vendor has been selected for each item
                const items = [
                    document.querySelectorAll('input[name="vendor_item1"]:checked').length,
                    document.querySelectorAll('input[name="vendor_item2"]:checked').length,
                    document.querySelectorAll('input[name="vendor_item3"]:checked').length
                ];

                if (items.includes(0)) {
                    alert('Please select a vendor for each item.');
                    return;
                }

                alert('Purchase Order has been created successfully!');
                window.location.href = "{{ route('procurement.price-comparison') }}";
            });
        }
    });
</script>
@endpush
