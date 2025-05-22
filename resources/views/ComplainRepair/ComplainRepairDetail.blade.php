@extends('Layout.app')

@section('title', 'Detail Keluhan & Perbaikan')

@section('content')
@include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Complaint Detail Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header with back button -->
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center">
                            <a href="{{ route('complaint.index') }}"
                                class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                            <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DETAIL KELUHAN</h1>
                        </div>

                        <!-- Export Button -->
                        <a href="{{ route('complaint.detail.export.pdf', ['id' => $complaint['id']]) }}" target="_blank"
                            class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Expor PDF
                        </a>
                    </div>

                    <!-- Main Content Area -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Complaint Image Section (Left Column on Desktop) -->
                        <div class="lg:col-span-1 order-2 lg:order-1">
                            @if(!empty($complaint['complaint_picture_path']))
                                <div class="bg-gray-50 p-4 rounded-lg h-full flex flex-col">
                                    <h2 class="text-lg font-semibold text-[#213268] mb-3 pb-2 border-b">Gambar Keluhan</h2>
                                    <div
                                        class="flex-grow flex items-center justify-center bg-white p-2 border rounded-lg overflow-hidden">
                                        <img src="http://localhost:5000/public/images/{{ basename($complaint['complaint_picture_path']) }}"
                                            alt="Gambar Keluhan" class="w-full object-contain rounded-lg"
                                            style="max-height: 350px;"
                                            onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('p-4');">
                                    </div>
                                </div>
                            @else
                                <div class="bg-gray-50 p-4 rounded-lg h-full flex flex-col">
                                    <h2 class="text-lg font-semibold text-[#213268] mb-3 pb-2 border-b">Gambar Keluhan</h2>
                                    <div class="flex-grow flex items-center justify-center bg-white p-4 border rounded-lg">
                                        <div class="text-center text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p>Gambar Keluhan Tidak Tersedia</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Complaint Information (Right Column on Desktop) -->
                        <div class="lg:col-span-2 order-1 lg:order-2">
                            <div class="bg-gray-50 p-4 rounded-lg h-full">
                                <h2 class="text-lg font-semibold text-[#213268] mb-3 pb-2 border-b">Informasi Keluhan</h2>

                                <!-- Basic Information Section -->
                                <div class="mb-5">
                                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Detail Keluhan</h3>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-white p-3 rounded-lg border border-gray-100">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-gray-500">Nama Aset</span>
                                            <span class="font-medium">{{ $complaint['asset_name'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-gray-500">Kode Aset</span>
                                            <span class="font-medium">{{ $complaint['asset_code'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex flex-col md:col-span-2">
                                            <span class="text-xs font-medium text-gray-500">Deskripsi</span>
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
                                            <span class="text-xs font-medium text-gray-500">Keluhan Oleh</span>
                                            <span>{{ $complaint['reporter_number'] ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Timeline Section -->
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Waktu</h3>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-white p-3 rounded-lg border border-gray-100">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-gray-500">Tanggal Keluhan</span>
                                            <span>{{ isset($complaint['complaint_date']) ? \Carbon\Carbon::parse($complaint['complaint_date'])->locale('id')->isoFormat('DD MMMM YYYY') : 'N/A' }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-gray-500">Tanggal Selesai</span>
                                            <span>{{ isset($complaint['finished_date']) && $complaint['finished_date'] ? \Carbon\Carbon::parse($complaint['finished_date'])->locale('id')->isoFormat('DD MMMM YYYY') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Repair Section -->
                    <div class="mt-2">
                        <h2 class="text-xl font-semibold text-[#213268] mb-4 pb-2 border-b">Informasi Perbaikan</h2>

                        @if(!empty($complaint['repair']))
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Repair Image (Left Column on Desktop) -->
                                <div class="lg:col-span-1 order-2 lg:order-1">
                                    @if(!empty($complaint['repair']['repair_picture_path']))
                                        <div class="bg-gray-50 p-4 rounded-lg h-full flex flex-col">
                                            <h3 class="text-md font-semibold text-[#213268] mb-3 pb-2 border-b">Gambar Perbaikan
                                            </h3>
                                            <div
                                                class="flex-grow flex items-center justify-center bg-white p-2 border rounded-lg overflow-hidden">
                                                <img src="http://localhost:5000/public/images/{{ basename($complaint['repair']['repair_picture_path']) }}"
                                                    alt="Repair Image" class="w-full object-contain rounded-lg"
                                                    style="max-height: 350px;"
                                                    onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('p-4');">
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-gray-50 p-4 rounded-lg h-full flex flex-col">
                                            <h3 class="text-md font-semibold text-[#213268] mb-3 pb-2 border-b">Gambar Perbaikan
                                            </h3>
                                            <div class="flex-grow flex items-center justify-center bg-white p-4 border rounded-lg">
                                                <div class="text-center text-gray-400">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-2"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <p>Gambar Perbaikan Tidak Tersedia</p>
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
                                            <h3 class="text-sm font-semibold text-gray-600 mb-2">Detail Perbaikan</h3>
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-white p-3 rounded-lg border border-gray-100">
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-medium text-gray-500">Hasil</span>
                                                    <span
                                                        class="font-medium">{{ $complaint['repair']['final_result'] ?? 'N/A' }}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-medium text-gray-500">Biaya</span>
                                                    <span
                                                        class="font-medium">{{ isset($complaint['repair']['repair_cost']) ? 'Rp ' . number_format((float) $complaint['repair']['repair_cost'], 0, ',', '.') : 'N/A' }}</span>
                                                </div>
                                                <div class="flex flex-col md:col-span-2">
                                                    <span class="text-xs font-medium text-gray-500">Deskripsi</span>
                                                    <span>{{ $complaint['repair']['repair_description'] ?? 'N/A' }}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-medium text-gray-500">Teknisi</span>
                                                    <span>ID: {{ $complaint['repair']['technician_number'] ?? 'N/A' }}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-medium text-gray-500">Bagian yang Diganti</span>
                                                    <span>{{ $complaint['repair']['parts_replaced'] ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Repair Timeline -->
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-600 mb-2">Waktu</h3>
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-white p-3 rounded-lg border border-gray-100">
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-medium text-gray-500">Tanggal Perbaikan</span>
                                                    <span>{{ isset($complaint['repair']['repair_date']) ? \Carbon\Carbon::parse($complaint['repair']['repair_date'])->locale('id')->isoFormat('DD MMMM YYYY') : 'N/A' }}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-medium text-gray-500">Tanggal Selesai</span>
                                                    <span>{{ isset($complaint['repair']['completion_date']) ? \Carbon\Carbon::parse($complaint['repair']['completion_date'])->locale('id')->isoFormat('DD MMMM YYYY') : 'N/A' }}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-medium text-gray-500">Tanggal Disetujui</span>
                                                    <span>{{ isset($complaint['repair']['approval_date']) ? \Carbon\Carbon::parse($complaint['repair']['approval_date'])->locale('id')->isoFormat('DD MMMM YYYY') : 'N/A' }}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-medium text-gray-500">Disetujui Oleh</span>
                                                    <span>ID: {{ $complaint['repair']['approver_number'] ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50 p-8 rounded-lg text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-gray-500 text-lg">Belum ada informasi perbaikan.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection