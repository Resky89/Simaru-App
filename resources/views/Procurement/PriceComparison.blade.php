@extends('Layout.app')

@section('title', 'Price Comparison')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Price Comparison Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">PRICE COMPARISON</h1>

                    <!-- Button Manage -->
                    <button id="manageBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-base">Manage</span>
                    </button>
                </div>

                <!-- Price Comparison Table -->
                <div class="overflow-x-auto"></div>
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                    <input type="checkbox" class="checkbox checkbox-sm" />
                                </th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Quotation Number</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Request ID</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Title</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Quotation by</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Quotation Date</th>
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
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 border-t border-[#EEF1F4] text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <button class="text-[#3D3D3D] hover:text-[#213268] edit-comparison-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button class="text-[#3D3D3D] hover:text-red-500 delete-comparison-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <a href="{{ route('procurement.detail-comparison', ['id' => 1]) }}" class="text-[#3D3D3D] hover:text-[#213268]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s3-6 10-6 10 6 10 6-3 6-10 6-10-6-10-6z" />
                                            </svg>
                                        </a>
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
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 border-t border-[#EEF1F4] text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <button class="text-[#3D3D3D] hover:text-[#213268] edit-comparison-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button class="text-[#3D3D3D] hover:text-red-500 delete-comparison-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <a href="{{ route('procurement.detail-comparison', ['id' => 1]) }}" class="text-[#3D3D3D] hover:text-[#213268]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s3-6 10-6 10 6 10 6-3 6-10 6-10-6-10-6z" />
                                            </svg>
                                        </a>
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
        const editBtns = document.querySelectorAll('.edit-comparison-btn');
        const deleteBtns = document.querySelectorAll('.delete-comparison-btn');

        // Add click event for the Manage button
        manageBtn.addEventListener('click', () => {
            // Code to handle manage functionality
            console.log('Manage button clicked');
        });

        // Add click events for Edit buttons
        editBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Code to handle edit functionality
                console.log('Edit button clicked');
            });
        });

        // Add click events for Delete buttons
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Code to handle delete functionality
                console.log('Delete button clicked');
            });
        });
    });
</script>
@endpush
@endsection
