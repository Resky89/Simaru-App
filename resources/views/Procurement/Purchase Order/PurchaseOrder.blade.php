@extends('Layout.app')

@section('title', 'Purchase Order')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Purchase Order Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PURCHASE ORDER</h1>

                    <div class="flex gap-3">
                        <!-- Add Create Button -->
                        <a href="{{ route('procurement.form-purchase-order') }}" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white hover:bg-[#152451] transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-base">Add PO</span>
                        </a>

                    </div>
                </div>

                <!-- Purchase Order Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                    <input type="checkbox" class="checkbox checkbox-sm" />
                                </th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">PO Number</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Quotation</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Vendor</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">PIC</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Qty</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">User Input</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">PO Date</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Status</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                    <input type="checkbox" class="checkbox checkbox-sm" />
                                </td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">1</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">-</td>
                                <td class="p-3 border-t border-[#EEF1F4] text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <a href="{{ route('procurement.detail-purchase-order', ['id' => 1]) }}" class="text-[#3D3D3D] hover:text-[#213268]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <button class="text-[#3D3D3D] hover:text-[#213268]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button class="text-[#3D3D3D] hover:text-red-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                    <input type="checkbox" class="checkbox checkbox-sm" />
                                </td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">2</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">-</td>
                                <td class="p-3 border-t border-[#EEF1F4] text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <a href="{{ route('procurement.detail-purchase-order', ['id' => 2]) }}" class="text-[#3D3D3D] hover:text-[#213268]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <button class="text-[#3D3D3D] hover:text-[#213268]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button class="text-[#3D3D3D] hover:text-red-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center space-x-2">
                        <button class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Prev
                        </button>

                        <div class="flex mx-2">
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded-md mx-1 text-[#213268] text-sm">1</button>
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded-md mx-1 text-[#213268] text-sm">2</button>
                            <button class="w-8 h-8 flex items-center justify-center bg-[#213268] text-white rounded-md mx-1 text-sm">3</button>
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded-md mx-1 text-[#213268] text-sm">4</button>
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded-md mx-1 text-[#213268] text-sm">5</button>
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded-md mx-1 text-[#213268] text-sm">6</button>
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded-md mx-1 text-[#213268] text-sm">7</button>
                            <span class="mx-1 flex items-center">...</span>
                            <button class="w-8 h-8 flex items-center justify-center border border-[#D8DAE5] rounded-md mx-1 text-[#213268] text-sm">20</button>
                        </div>

                        <button class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm">
                            Next
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <div class="relative">
                        <select class="border border-[#D8DAE5] rounded-md py-1 px-3 pr-8 appearance-none text-[#213268] text-sm">
                            <option>10 per page</option>
                            <option>25 per page</option>
                            <option>50 per page</option>
                            <option>100 per page</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#213268]">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const manageBtn = document.getElementById('manageBtn');

        // Add click event for the Manage button
        manageBtn.addEventListener('click', () => {
            // Code to handle manage functionality
            console.log('Manage button clicked');
        });
    });
</script>
@endpush
@endsection
