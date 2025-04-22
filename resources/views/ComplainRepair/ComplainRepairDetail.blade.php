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

                    @if(!empty($complaint['complaint_picture_path']))
                    <button id="viewImageBtn" onclick="viewImage('{{ $complaint['complaint_picture_path'] }}')" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4 4m0 0l4-4m-4 4V4" />
                        </svg>
                        <span class="text-base">View Image</span>
                    </button>
                    @endif
                </div>

                <!-- Complaint Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold text-[#213268] mb-4">Complaint Information</h2>
                        <div class="space-y-3">
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Asset Name</div>
                                <div class="text-sm">{{ $complaint['asset_name'] ?? 'N/A' }}</div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Asset ID</div>
                                <div class="text-sm">{{ $complaint['asset_id'] ?? 'N/A' }}</div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Description</div>
                                <div class="text-sm">{{ $complaint['description'] ?? 'N/A' }}</div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Status</div>
                                <div class="text-sm">
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
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Complaint Date</div>
                                <div class="text-sm">{{ isset($complaint['complaint_date']) ? date('d M Y H:i', strtotime($complaint['complaint_date'])) : 'N/A' }}</div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Finished Date</div>
                                <div class="text-sm">{{ isset($complaint['finished_date']) && $complaint['finished_date'] ? date('d M Y H:i', strtotime($complaint['finished_date'])) : 'N/A' }}</div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Reported By</div>
                                <div class="text-sm">ID: {{ $complaint['reporter_number'] ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    @if(!empty($complaint['repair']))
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold text-[#213268] mb-4">Repair Information</h2>
                        <div class="space-y-3">
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Repair Date</div>
                                <div class="text-sm">{{ isset($complaint['repair']['repair_date']) ? date('d M Y H:i', strtotime($complaint['repair']['repair_date'])) : 'N/A' }}</div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Result</div>
                                <div class="text-sm">{{ $complaint['repair']['final_result'] ?? 'N/A' }}</div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Description</div>
                                <div class="text-sm">{{ $complaint['repair']['repair_description'] ?? 'N/A' }}</div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Cost</div>
                                <div class="text-sm">{{ isset($complaint['repair']['repair_cost']) ? 'Rp ' . number_format((float)$complaint['repair']['repair_cost'], 0, ',', '.') : 'N/A' }}</div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Technician</div>
                                <div class="text-sm">ID: {{ $complaint['repair']['technician_number'] ?? 'N/A' }}</div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Parts Replaced</div>
                                <div class="text-sm">{{ $complaint['repair']['parts_replaced'] ?? 'N/A' }}</div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Completion Date</div>
                                <div class="text-sm">{{ isset($complaint['repair']['completion_date']) ? date('d M Y H:i', strtotime($complaint['repair']['completion_date'])) : 'N/A' }}</div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Approved By</div>
                                <div class="text-sm">ID: {{ $complaint['repair']['approver_number'] ?? 'N/A' }}</div>
                            </div>
                            <div class="grid grid-cols-2">
                                <div class="text-sm font-medium text-gray-500">Approval Date</div>
                                <div class="text-sm">{{ isset($complaint['repair']['approval_date']) ? date('d M Y H:i', strtotime($complaint['repair']['approval_date'])) : 'N/A' }}</div>
                            </div>
                            @if(!empty($complaint['repair']['repair_picture_path']))
                            <div class="mt-2">
                                <button onclick="viewImage('{{ $complaint['repair']['repair_picture_path'] }}')" class="px-2 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md text-sm transition-all duration-200">
                                    View Repair Image
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold text-[#213268] mb-4">Repair Information</h2>
                        <p class="text-gray-500">No repair information available yet.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for displaying image -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-11/12 md:w-4/5 lg:w-3/5 xl:w-1/2 p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-[#213268]">Image</h3>
            <button onclick="closeImageModal()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="flex justify-center">
            <img id="modalImage" src="" alt="Image" class="max-h-[70vh] max-w-full">
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="closeImageModal()" class="px-4 py-2 bg-[#213268] text-white rounded-lg">Close</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function viewImage(imagePath) {
        const imageModal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');

        // Set the image source
        modalImage.src = imagePath;

        // Show the modal
        imageModal.classList.remove('hidden');
        imageModal.classList.add('flex');
    }

    function closeImageModal() {
        const imageModal = document.getElementById('imageModal');
        imageModal.classList.add('hidden');
        imageModal.classList.remove('flex');
    }

    // Close modal when clicking outside the content
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });
</script>
@endpush
@endsection
