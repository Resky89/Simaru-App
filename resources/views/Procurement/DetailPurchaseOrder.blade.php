@extends('Layout.app')

@section('title', 'Purchase Order Detail')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Purchase Order Detail Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PURCHASE ORDER DETAIL</h1>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <a href="{{ route('procurement.purchase-order') }}" class="px-4 py-2 bg-[#213268] text-white rounded-md hover:bg-[#152451] transition-all duration-200 uppercase text-sm font-medium">
                        Back
                    </a>
                </div>

                <!-- PO Details -->
                <div class="grid grid-cols-1 gap-5">
                    <!-- Procurement ID -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Procurement ID</p>
                        <p class="text-[#666666]">: <span>PPB2406001</span></p>
                    </div>

                    <!-- PO ID -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">PO ID</p>
                        <p class="text-[#666666]">: <span>PB2406001</span></p>
                    </div>

                    <!-- Title Procurement -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Request Title</p>
                        <p class="text-[#666666]">: <span>Pembelian Toner Printer</span></p>
                    </div>

                    <!-- Requester -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Requester</p>
                        <p class="text-[#666666]">: <span>Manager Finance</span></p>
                    </div>

                    <!-- PIC -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">PIC</p>
                        <p class="text-[#666666]">: <span>Hartana Budiman</span></p>
                    </div>

                    <!-- PIC Contact -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">PIC Contact</p>
                        <p class="text-[#666666]">: <span>082403714145</span></p>
                    </div>

                    <!-- PO Date -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">PO Date</p>
                        <p class="text-[#666666]">: <span>2024-06-30 07:03:46</span></p>
                    </div>
                </div>

                <!-- Item List -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-[#666666]">ASSET LIST</h2>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">ASSET NAME</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">SPECIFICATION</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">QTY</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">UNIT PRICE</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">TOTAL</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">RECEIPT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Item 1 -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td class="p-3 text-xs text-[#666666]">Motherboard</td>
                                    <td class="p-3 text-xs text-[#666666]">ATX</td>
                                    <td class="p-3 text-xs text-center text-[#666666]">1</td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">2,475,000</div>
                                    </td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">2,475,000</div>
                                    </td>
                                    <td class="p-3 text-xs text-center text-red-500">Not Yet</td>
                                </tr>

                                <!-- Item 2 -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td class="p-3 text-xs text-[#666666]">CPU core i5</td>
                                    <td class="p-3 text-xs text-[#666666]">Gen 13</td>
                                    <td class="p-3 text-xs text-center text-[#666666]">1</td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">3,450,000</div>
                                    </td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">3,450,000</div>
                                    </td>
                                    <td class="p-3 text-xs text-center text-red-500">Not Yet</td>
                                </tr>

                                <!-- Item 3 -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td class="p-3 text-xs text-[#666666]">RAM DDR4 8gb</td>
                                    <td class="p-3 text-xs text-[#666666]">3200MHz</td>
                                    <td class="p-3 text-xs text-center text-[#666666]">3</td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">245,000</div>
                                    </td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">735,000</div>
                                    </td>
                                    <td class="p-3 text-xs text-center text-red-500">Not Yet</td>
                                </tr>

                                <!-- Grand Total -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td colspan="4" class="p-3 text-xs font-medium text-right text-[#666666]">Grand Total</td>
                                    <td class="p-3 text-xs text-[#666666]">
                                        <div class="text-sm font-medium">6,660,000</div>
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex gap-4 mt-8">
                    <button type="button" class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 uppercase">
                        PRINT
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
        // Handle Print button
        const printBtn = document.querySelector('button.uppercase');

        if (printBtn) {
            printBtn.addEventListener('click', function() {
                window.print();
            });
        }
    });
</script>
@endpush
