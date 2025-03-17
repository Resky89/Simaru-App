<div class="p-3">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 gap-2">
        <h2 class="text-base font-bold text-[#213268]">DOCUMENT</h2>
        <button id="addDocumentBtn" class="flex items-center justify-center gap-1 px-3 py-1.5 bg-[#213268] rounded-lg text-white text-xs">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Document</span>
        </button>
    </div>

    <!-- Document Table -->
    <div class="overflow-x-auto -mx-3 sm:mx-0 rounded-md">
        <table class="w-full min-w-[500px] border-collapse">
            <thead>
                <tr>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left">Title</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left">File name</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left">Type</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left">Upload Date</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-center w-16 md:w-20">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 border-t border-[#EEF1F4] text-center">
                        <div class="flex justify-center items-center space-x-2">
                            <button class="text-[#3D3D3D] hover:text-[#213268]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </button>
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
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">-</td>
                    <td class="p-3 border-t border-[#EEF1F4] text-center">
                        <div class="flex justify-center items-center space-x-2">
                            <button class="text-[#3D3D3D] hover:text-[#213268]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </button>
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
</div>
