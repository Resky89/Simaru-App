@extends('Layout.app')

@section('title', 'Depreciation Report')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Generate Depreciation Report Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">GENERATE DEPRECIATION REPORT</h1>
                </div>

                <!-- Filter Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Building</label>
                        <div class="relative">
                            <select class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 pr-8 appearance-none text-[#213268]">
                                <option>Select Building</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#213268]">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                        <div class="relative">
                            <select class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 pr-8 appearance-none text-[#213268]">
                                <option>Select Room</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#213268]">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categories</label>
                        <div class="relative">
                            <select class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 pr-8 appearance-none text-[#213268]">
                                <option>Select categories</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#213268]">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sub Categories</label>
                        <div class="relative">
                            <select class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 pr-8 appearance-none text-[#213268]">
                                <option>Select Sub Categories</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#213268]">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Month and Year</label>
                        <div class="relative">
                            <input type="text" class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 text-[#213268]" placeholder="Choose Month and Year">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#213268]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Book value at month end</label>
                        <input type="text" class="w-full border border-[#D8DAE5] rounded-md py-2 px-3 text-[#213268]" placeholder="Type here">
                    </div>
                </div>

                <!-- Apply Button -->
                <div class="mt-4">
                    <button class="w-full bg-[#213268] text-white py-3 rounded-md hover:bg-[#1a275a] transition-colors">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Depreciation Report Results Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DEPRECIATION REPORT</h1>

                    <!-- Download PDF Button -->
                    <button id="downloadBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span class="text-base">Download PDF</span>
                    </button>
                </div>

                <!-- Depreciation Report Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">Asset ID</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Name</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Date Acquired</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Purchase Cost</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Salvage Value</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Life (Month)</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Depreciation Method</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Month and Year</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Book Value at month end</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Building</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Room</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Categories</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Sub Categories</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">1</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                            </tr>
                            <tr>
                                <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">2</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                    <div class="flex items-center space-x-2">
                        <button class="flex items-center gap-2 px-3 py-1 border border-[#D8DAE5] rounded-md text-[#213268] text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Prev
                        </button>

                        <div class="flex">
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
        const downloadBtn = document.getElementById('downloadBtn');

        // Add click event for the Download PDF button
        downloadBtn.addEventListener('click', () => {
            // Code to handle PDF download functionality
            console.log('Download PDF button clicked');
        });
    });
</script>
@endpush
@endsection
