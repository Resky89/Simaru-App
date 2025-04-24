@extends('Layout.app')

@section('title', 'Complaint & Repair Detail')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Header with back button -->
    <div class="flex items-center mb-2">
        <a href="{{ route('complaint.index') }}" class="text-[#213268] hover:text-blue-700 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back to Complaints</span>
        </a>
    </div>

    <!-- Complaint Detail Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">COMPLAINT DETAIL</h1>

                    <!-- Export Button -->
                    <a href="{{ route('complaint.detail.export.pdf', ['id' => $complaint['id']]) }}" target="_blank"
                       class="bg-[#213268] hover:bg-[#152451] text-white py-2 px-4 rounded-lg flex items-center text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export to PDF
                    </a>
                </div>

                <!-- Main Content Area -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Complaint Image Section (Left Column on Desktop) -->
                    <div class="lg:col-span-1 order-2 lg:order-1">
                        @if(!empty($complaint['complaint_picture_path']))
                        <div class="bg-gray-50 p-4 rounded-lg h-full flex flex-col">
                            <h2 class="text-lg font-semibold text-[#213268] mb-3 pb-2 border-b">Complaint Image</h2>
                            <div class="flex-grow flex items-center justify-center bg-white p-2 border rounded-lg overflow-hidden">
                                <img
                                    src="http://localhost:5000/public/images/{{ basename($complaint['complaint_picture_path']) }}"
                                    alt="Complaint Image"
                                    class="w-full object-contain rounded-lg"
                                    style="max-height: 350px;"
                                    onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('p-4');"
                                >
                            </div>
                        </div>
                        @else
                        <div class="bg-gray-50 p-4 rounded-lg h-full flex flex-col">
                            <h2 class="text-lg font-semibold text-[#213268] mb-3 pb-2 border-b">Complaint Image</h2>
                            <div class="flex-grow flex items-center justify-center bg-white p-4 border rounded-lg">
                                <div class="text-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p>No image available</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Complaint Information (Right Column on Desktop) -->
                    <div class="lg:col-span-2 order-1 lg:order-2">
                        <div class="bg-gray-50 p-4 rounded-lg h-full">
                            <h2 class="text-lg font-semibold text-[#213268] mb-3 pb-2 border-b">Complaint Information</h2>

                            <!-- Basic Information Section -->
                            <div class="mb-5">
                                <h3 class="text-sm font-semibold text-gray-600 mb-2">Basic Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-white p-3 rounded-lg border border-gray-100">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-medium text-gray-500">Asset Name</span>
                                        <span class="font-medium">{{ $complaint['asset_name'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-medium text-gray-500">Asset ID</span>
                                        <span class="font-medium">{{ $complaint['asset_id'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex flex-col md:col-span-2">
                                        <span class="text-xs font-medium text-gray-500">Description</span>
                                        <span>{{ $complaint['description'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-medium text-gray-500">Status</span>
                                        <div>
                                            @php
                                                $statusClass = '';
                                                $status = $complaint['status'] ?? '';

                                                if ($status == 'approved' || $status == 'completed') {
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                } elseif ($status == 'pending') {
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                } elseif ($status == 'rejected') {
                                                    $statusClass = 'bg-red-100 text-red-800';
                                                } elseif ($status == 'in_progress') {
                                                    $statusClass = 'bg-blue-100 text-blue-800';
                                                } else {
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                }
                                            @endphp
                                            <span class="px-2 py-1 rounded-full text-xs {{ $statusClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $status ?: 'Unknown')) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-medium text-gray-500">Reported By</span>
                                        <span>ID: {{ $complaint['reporter_number'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline Section -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-600 mb-2">Timeline</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-white p-3 rounded-lg border border-gray-100">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-medium text-gray-500">Complaint Date</span>
                                        <span>{{ isset($complaint['complaint_date']) ? date('d M Y H:i', strtotime($complaint['complaint_date'])) : 'N/A' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-medium text-gray-500">Finished Date</span>
                                        <span>{{ isset($complaint['finished_date']) && $complaint['finished_date'] ? date('d M Y H:i', strtotime($complaint['finished_date'])) : 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Repair Section -->
                <div class="mt-2">
                    <h2 class="text-xl font-semibold text-[#213268] mb-4 pb-2 border-b">Repair Information</h2>

                    @if(!empty($complaint['repair']))
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Repair Image (Left Column on Desktop) -->
                        <div class="lg:col-span-1 order-2 lg:order-1">
                            @if(!empty($complaint['repair']['repair_picture_path']))
                            <div class="bg-gray-50 p-4 rounded-lg h-full flex flex-col">
                                <h3 class="text-md font-semibold text-[#213268] mb-3 pb-2 border-b">Repair Image</h3>
                                <div class="flex-grow flex items-center justify-center bg-white p-2 border rounded-lg overflow-hidden">
                                    <img
                                        src="http://localhost:5000/public/images/{{ basename($complaint['repair']['repair_picture_path']) }}"
                                        alt="Repair Image"
                                        class="w-full object-contain rounded-lg"
                                        style="max-height: 350px;"
                                        onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('p-4');"
                                    >
                                </div>
                            </div>
                            @else
                            <div class="bg-gray-50 p-4 rounded-lg h-full flex flex-col">
                                <h3 class="text-md font-semibold text-[#213268] mb-3 pb-2 border-b">Repair Image</h3>
                                <div class="flex-grow flex items-center justify-center bg-white p-4 border rounded-lg">
                                    <div class="text-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p>No repair image available</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Repair Details (Right Column on Desktop) -->
                        <div class="lg:col-span-2 order-1 lg:order-2">
                            <div class="bg-gray-50 p-4 rounded-lg h-full">
                                <!-- Repair Details Section -->
                                <div class="mb-5">
                                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Repair Details</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-white p-3 rounded-lg border border-gray-100">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-gray-500">Result</span>
                                            <span class="font-medium">{{ $complaint['repair']['final_result'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-gray-500">Cost</span>
                                            <span class="font-medium">{{ isset($complaint['repair']['repair_cost']) ? 'Rp ' . number_format((float)$complaint['repair']['repair_cost'], 0, ',', '.') : 'N/A' }}</span>
                                        </div>
                                        <div class="flex flex-col md:col-span-2">
                                            <span class="text-xs font-medium text-gray-500">Description</span>
                                            <span>{{ $complaint['repair']['repair_description'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-gray-500">Technician</span>
                                            <span>ID: {{ $complaint['repair']['technician_number'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-gray-500">Parts Replaced</span>
                                            <span>{{ $complaint['repair']['parts_replaced'] ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Repair Timeline -->
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Timeline</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-white p-3 rounded-lg border border-gray-100">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-gray-500">Repair Date</span>
                                            <span>{{ isset($complaint['repair']['repair_date']) ? date('d M Y H:i', strtotime($complaint['repair']['repair_date'])) : 'N/A' }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-gray-500">Completion Date</span>
                                            <span>{{ isset($complaint['repair']['completion_date']) ? date('d M Y H:i', strtotime($complaint['repair']['completion_date'])) : 'N/A' }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-gray-500">Approval Date</span>
                                            <span>{{ isset($complaint['repair']['approval_date']) ? date('d M Y H:i', strtotime($complaint['repair']['approval_date'])) : 'N/A' }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-gray-500">Approved By</span>
                                            <span>ID: {{ $complaint['repair']['approver_number'] ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="bg-gray-50 p-8 rounded-lg text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-gray-500 text-lg">No repair information available yet.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
