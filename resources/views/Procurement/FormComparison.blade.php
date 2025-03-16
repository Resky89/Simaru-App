@extends('Layout.app')

@section('title', 'Add Vendor Quotation')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Quotation Form Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">VENDOR QUOTATION</h1>
                </div>

                <!-- Form -->
                <form id="vendorQuotationForm" class="w-full space-y-6">
                    <!-- Vendor -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Vendor</label>
                        <select
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="" disabled selected>Select Vendor</option>
                            <option value="1">PT WIBOWO (PERSERO) TBK</option>
                            <option value="2">PT SETIAWAN</option>
                            <option value="3">CV JAYA ABADI</option>
                        </select>
                    </div>

                    <!-- Payment Terms -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Payment Terms</label>
                        <textarea
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Payment Terms" rows="3"></textarea>
                    </div>

                    <!-- Delivery Terms -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Delivery Terms</label>
                        <textarea
                            class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Delivery Terms" rows="3"></textarea>
                    </div>

                    <!-- Item List -->
                    <div class="space-y-4">
                        <label class="block text-base font-semibold text-[#666666]">Asset List</label>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Name</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Qty</th>
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Unit Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Item 1 -->
                                    <tr class="border-t border-[#EEF1F4]">
                                        <td class="p-3 text-xs text-[#666666]">Motherboard</td>
                                        <td class="p-3 text-xs text-center text-[#666666]">1</td>
                                        <td class="p-3">
                                            <input type="text"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Unit Price">
                                        </td>
                                    </tr>

                                    <!-- Item 2 -->
                                    <tr class="border-t border-[#EEF1F4]">
                                        <td class="p-3 text-xs text-[#666666]">CPU core i5</td>
                                        <td class="p-3 text-xs text-center text-[#666666]">1</td>
                                        <td class="p-3">
                                            <input type="text"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Unit Price">
                                        </td>
                                    </tr>

                                    <!-- Item 3 -->
                                    <tr class="border-t border-[#EEF1F4]">
                                        <td class="p-3 text-xs text-[#666666]">RAM DDR4 8gb</td>
                                        <td class="p-3 text-xs text-center text-[#666666]">3</td>
                                        <td class="p-3">
                                            <input type="text"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                                placeholder="Unit Price">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex gap-4 mt-8">
                        <a href="{{ route('procurement.detail-comparison', ['id' => 1]) }}" class="px-6 py-3 bg-[#333333] text-white rounded-lg text-base hover:bg-gray-800 transform active:scale-[0.98] transition-all duration-200 uppercase">
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
        const form = document.getElementById('vendorQuotationForm');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Here you would collect all the form data and send it to the server
                alert('Form submitted! In a real application, this would save the vendor quotation.');

                // Redirect back to detail comparison page after submission
                window.location.href = "{{ route('procurement.detail-comparison', ['id' => 1]) }}";
            });
        }
    });
</script>
@endpush
