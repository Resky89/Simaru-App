<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-[#213268]">SCHEDULE</h2>
        <button id="addScheduleBtn" class="flex items-center justify-center gap-2 px-4 py-2 bg-[#213268] rounded-lg text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span class="text-sm">Add Schedule</span>
        </button>
    </div>

    <!-- Schedule Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">No</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">User</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Start Date</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">End Date</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Notes</th>
                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">1</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">John Doe</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">12/03/2023 08:00</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">15/03/2023 17:00</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">Project XYZ presentation</td>
                    <td class="p-3 border-t border-[#EEF1F4] text-center">
                        <div class="flex justify-center items-center space-x-2">
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
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">2</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">Jane Smith</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">18/03/2023 09:30</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">18/03/2023 16:30</td>
                    <td class="p-3 text-xs border-t border-[#EEF1F4]">Client meeting</td>
                    <td class="p-3 border-t border-[#EEF1F4] text-center">
                        <div class="flex justify-center items-center space-x-2">
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

<!-- Add Schedule Modal -->
<div id="addScheduleModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div id="scheduleModalContent" class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transition-all duration-300 transform scale-95 opacity-0 translate-y-4">
        <div class="p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-[#213268]">Add Schedule</h3>
                <button class="close-modal text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                        <option value="">Select User</option>
                        <option value="1">John Doe</option>
                        <option value="2">Jane Smith</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time</label>
                    <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date & Time</label>
                    <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#213268] focus:border-[#213268]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#213268] focus:border-[#213268]" rows="3"></textarea>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="close-modal px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#213268] text-white rounded-md hover:bg-[#1a2855]">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
