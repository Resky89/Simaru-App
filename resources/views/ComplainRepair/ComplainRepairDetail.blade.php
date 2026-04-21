@extends('Layout.app')

@section('title', 'Detail Keluhan & Perbaikan')

@section('content')
    @include('Layout.loading')
    <div class="h-full">
        <!-- Complaint Detail Section -->
        <div class="flex flex-col gap-6 p-4 md:p-7 bg-base-100 rounded-xl">
            <!-- Header with status banner -->
            <div class="relative">
                <!-- Header with back button -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="flex items-center">
                        <a href="{{ route('complaint.index') }}"
                            class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DETAIL KELUHAN</h1>
                    </div>

                    <!-- Export Button -->
                    @if(hasPermission('complaint:export'))
                        <a href="{{ route('complaint.detail.export.pdf', ['id' => $complaint['id']]) }}" target="_blank"
                            class="flex-shrink-0 flex items-center justify-center gap-2 px-2 py-2 md:px-4 md:py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Ekspor PDF</span>
                        </a>
                    @endif
                </div>

                <!-- Status banner -->
                @php
                    $statusClass = 'bg-gray-100 text-gray-800 border-gray-200';
                    $statusText = 'Tidak Diketahui';
                    $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                    $statusDescription = '';
                    $status = $complaint['status'] ?? '';

                    if ($status == 'new') {
                        $statusClass = 'bg-blue-100 text-blue-800 border-blue-200';
                        $statusText = 'Baru';
                        $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />';
                        $statusDescription = 'Keluhan baru dibuat';
                    } elseif ($status == 'in progress') {
                        $statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                        $statusText = 'Sedang Diproses';
                        $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />';
                        $statusDescription = 'Keluhan sedang dalam proses perbaikan';
                    } elseif ($status == 'finished') {
                        $statusClass = 'bg-green-100 text-green-800 border-green-200';
                        $statusText = 'Selesai';
                        $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                        $statusDescription = 'Keluhan telah selesai diperbaiki';
                    } elseif ($status == 'approved') {
                        $statusClass = 'bg-green-100 text-green-800 border-green-200';
                        $statusText = 'Disetujui';
                        $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                        $statusDescription = 'Keluhan telah disetujui untuk diperbaiki';
                    } else {
                        $statusClass = 'bg-gray-100 text-gray-800 border-gray-200';
                        $statusText = 'Tidak Diketahui';
                        $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                        $statusDescription = 'Status keluhan tidak diketahui';
                    }
                @endphp

                <div class="w-full rounded-lg p-4 mb-6 {{ $statusClass }} border flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $statusIcon !!}
                    </svg>
                    <div>
                        <div class="font-semibold">Status: {{ $statusText }}</div>
                        <div class="text-sm">{{ $statusDescription }}</div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Complaint Image Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Gambar Keluhan
                    </h2>

                    @if(!empty($complaint['complaint_picture_path']))
                        <div
                            class="flex-grow flex items-center justify-center bg-gray-50 p-2 border rounded-lg overflow-hidden">
                            <img src="{{ api_public_url('images/' . basename($complaint['complaint_picture_path'])) }}"
                                alt="Gambar Keluhan" class="w-full object-contain rounded-lg" style="max-height: 350px;"
                                onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('p-4');">
                        </div>
                    @else
                        <div class="flex-grow flex items-center justify-center bg-gray-50 p-4 border rounded-lg h-64">
                            <div class="text-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p>Gambar Keluhan Tidak Tersedia</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Asset Information Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                        Informasi Aset
                    </h2>

                    <!-- Asset Image -->
                    <div
                        class="w-full h-40 bg-white mb-4 rounded-lg border border-gray-200 overflow-hidden relative flex items-center justify-center">
                        <img src="{{ isset($complaint['asset_image_path']) ? api_public_url($complaint['asset_image_path']) : asset('images/placeholder.png') }}"
                            alt="Asset Image" class="w-full h-full object-contain p-2"
                            onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('object-contain', 'p-4');">
                    </div>

                    <div class="space-y-4">
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Nama Aset</span>
                            <span class="font-medium text-lg">{{ $complaint['asset_name'] ?? 'N/A' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Kode Aset</span>
                            <span class="font-medium">{{ $complaint['asset_code'] ?? 'N/A' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Nomor Seri</span>
                            <span class="font-medium">{{ $complaint['serial_number'] ?? 'N/A' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Model</span>
                            <span class="font-medium">{{ $complaint['model'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Complaint Details Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Detail Keluhan
                    </h2>
                    <div class="space-y-4">
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Keluhan Oleh</span>
                            <span class="font-medium">{{ $complaint['reporter_name'] ?? 'N/A' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Deskripsi</span>
                            <span class="font-medium break-words">{{ $complaint['description'] ?? 'N/A' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Tanggal Keluhan</span>
                            <span class="font-medium">
                                {{ isset($complaint['complaint_date']) ? \Carbon\Carbon::parse($complaint['complaint_date'])->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                            </span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500">Tanggal Selesai</span>
                            <span class="font-medium">
                                @if(isset($complaint['finished_date']) && $complaint['finished_date'] && $complaint['finished_date'] != '-')
                                    {{ \Carbon\Carbon::parse($complaint['finished_date'])->locale('id')->isoFormat('D MMMM YYYY') }}
                                @else
                                    Belum selesai
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Repair Section -->
            <div class="mt-4">
                <h2 class="text-xl font-semibold text-[#213268] mb-4 pb-2 border-b flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Informasi Perbaikan
                </h2>

                @if(!empty($complaint['repair']))
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Repair Image Section -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                            <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Gambar Perbaikan
                            </h2>

                            @if(!empty($complaint['repair']['repair_picture_path']))
                                <div
                                    class="flex-grow flex items-center justify-center bg-gray-50 p-2 border rounded-lg overflow-hidden">
                                    <img src="{{ api_public_url('images/' . basename($complaint['repair']['repair_picture_path'])) }}"
                                        alt="Repair Image" class="w-full object-contain rounded-lg" style="max-height: 350px;"
                                        onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('p-4');">
                                </div>
                            @else
                                <div class="flex-grow flex items-center justify-center bg-gray-50 p-4 border rounded-lg h-64">
                                    <div class="text-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p>Gambar Perbaikan Tidak Tersedia</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Repair Details Card -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                            <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Detail Perbaikan
                            </h2>

                            <div class="space-y-4">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Hasil</span>
                                    <span class="font-medium">
                                        @php
                                            $finalResult = $complaint['repair']['final_result'] ?? 'N/A';
                                            $translations = [
                                                'Good' => 'Baik',
                                                'Slightly Damage' => 'Kerusakan Ringan',
                                                'Heavy Damage' => 'Kerusakan Berat',
                                                'Waiting for Part' => 'Menunggu Suku Cadang'
                                            ];
                                            $translatedResult = $translations[$finalResult] ?? $finalResult;
                                        @endphp
                                        {{ $translatedResult }}
                                    </span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Biaya</span>
                                    <span
                                        class="font-medium">{{ isset($complaint['repair']['repair_cost']) ? 'Rp ' . number_format((float) $complaint['repair']['repair_cost'], 0, ',', '.') : 'N/A' }}</span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Deskripsi</span>
                                    <span
                                        class="font-medium break-words">{{ $complaint['repair']['repair_description'] ?? 'N/A' }}</span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Teknisi</span>
                                    <span class="font-medium">{{ $complaint['repair']['technician_name'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Repair Timeline Card -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                            <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Waktu Perbaikan
                            </h2>

                            <div class="space-y-4">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Tanggal Perbaikan</span>
                                    <span class="font-medium">
                                        {{ isset($complaint['repair']['repair_date']) ? \Carbon\Carbon::parse($complaint['repair']['repair_date'])->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                    </span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Tanggal Selesai</span>
                                    <span class="font-medium">
                                        {{ isset($complaint['repair']['completion_date']) ? \Carbon\Carbon::parse($complaint['repair']['completion_date'])->locale('id')->isoFormat('D MMMM YYYY') : 'N/A' }}
                                    </span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Tanggal Disetujui</span>
                                    <span class="font-medium">
                                        {{ isset($complaint['repair']['approval_date']) ? \Carbon\Carbon::parse($complaint['repair']['approval_date'])->locale('id')->isoFormat('D MMMM YYYY') : 'Belum disetujui' }}
                                    </span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Bagian yang Diganti</span>
                                    <span class="font-medium">{{ $complaint['repair']['parts_replaced'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 p-8 rounded-lg border border-gray-200 text-center">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(!hasPermission('complaint:export'))
                const exportButtons = document.querySelectorAll('a[href*="export.pdf"]');
                exportButtons.forEach(btn => {
                    if (btn) {
                        btn.style.display = 'none';
                    }
                });
            @endif
            });
    </script>
@endsection
