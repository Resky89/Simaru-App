@extends('Layout.app')

@section('title', 'Detail Pemeliharaan')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <!-- Header with Back Button, Title, ID and Status -->
                <div class="flex items-center justify-between gap-3 mb-6">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('maintenance') }}"
                            class="p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DETAIL PEMELIHARAAN</h1>
                            <div class="flex items-center gap-3 mt-1">
                                @php
                                    $statusClass = '';
                                    $status = $maintenance['status'] ?? '';
                                    $statusText = 'Tidak Diketahui';

                                    if ($status == 'new') {
                                        $statusClass = 'bg-blue-100 text-blue-800';
                                        $statusText = 'Baru';
                                    } elseif ($status == 'in_progress') {
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                        $statusText = 'Dalam Proses';
                                    } elseif ($status == 'finished') {
                                        $statusClass = 'bg-green-100 text-green-800';
                                        $statusText = 'Selesai';
                                    }
                                @endphp

                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Export Button -->
                    <a href="{{ route('maintenance.export.detail.pdf', ['id' => $maintenance['id']]) }}" target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Ekspor PDF
                    </a>
                </div>

                <!-- Main Content -->
                <div class="flex flex-col gap-6">
                    <!-- Main Info Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Asset Information Card -->
                            <div class="bg-gray-50 p-5 rounded-lg border border-gray-100">
                                <h2 class="text-lg font-semibold text-[#213268] mb-4 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Informasi Aset
                                </h2>
                                <div class="space-y-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Nama Aset</span>
                                        <span class="font-medium">{{ $maintenance['asset_name'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-500">Kode Aset</span>
                                            <span>{{ $maintenance['asset_code'] ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    @if(isset($maintenance['asset']) && isset($maintenance['asset']['location']))
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-500">Lokasi</span>
                                            <span>{{ $maintenance['asset']['location']['room_name'] ?? 'N/A' }},
                                                {{ $maintenance['asset']['location']['building_name'] ?? '' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Schedule Information Card -->
                            <div class="bg-gray-50 p-5 rounded-lg border border-gray-100">
                                <h2 class="text-lg font-semibold text-[#213268] mb-4 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Informasi Jadwal
                                </h2>
                                <div class="space-y-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Interval</span>
                                        <span class="font-medium">{{ $maintenance['interval'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-500">Tanggal Mulai</span>
                                            <span>{{ isset($maintenance['start_date']) ? date('d F Y', strtotime($maintenance['start_date'])) : 'N/A' }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-500">Tanggal Selesai</span>
                                            <span>{{ isset($maintenance['end_date']) ? date('d F Y', strtotime($maintenance['end_date'])) : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Assignment Information Card -->
                            <div class="bg-gray-50 p-5 rounded-lg border border-gray-100">
                                <h2 class="text-lg font-semibold text-[#213268] mb-4 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Informasi Penugasan
                                </h2>
                                <div class="space-y-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Ditugaskan Kepada</span>
                                        <span class="font-medium">{{ $maintenance['assigned_to_employee_number'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Dijadwalkan Oleh</span>
                                        <span>{{ $maintenance['scheduled_by_employee_number'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Vendor</span>
                                        <span>{{ $maintenance['vendor_name'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes & Documents Card -->
                            @if(
                                    (isset($maintenance['notes']) && !empty($maintenance['notes']) && $maintenance['notes'] != 'No notes available') ||
                                    (isset($maintenance['document_file_path']) && !empty($maintenance['document_file_path']))
                                )
                                <div class="bg-gray-50 p-5 rounded-lg border border-gray-100">
                                    <h2 class="text-lg font-semibold text-[#213268] mb-4 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Catatan & Dokumen
                                    </h2>
                                    <div class="space-y-4">
                                        @if(isset($maintenance['notes']) && !empty($maintenance['notes']) && $maintenance['notes'] != 'No notes available')
                                            <div class="flex flex-col">
                                                <span class="text-sm text-gray-500">Catatan</span>
                                                <p class="font-medium whitespace-pre-wrap">{{ $maintenance['notes'] }}</p>
                                            </div>
                                        @endif

                                        @if(isset($maintenance['document_file_path']) && !empty($maintenance['document_file_path']))
                                            <div class="flex flex-col">
                                                <span class="text-sm text-gray-500">Dokumen Terlampir</span>
                                                <a href="{{ $maintenance['document_file_path'] }}" target="_blank"
                                                    class="text-blue-600 hover:underline flex items-center mt-1">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                                        </path>
                                                    </svg>
                                                    Lihat Dokumen
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Maintenance Report Section - Full Width -->
                    @if(isset($maintenance['maintenance_report']))
                        <div class="mt-2">
                            <div class="bg-blue-50 p-5 rounded-lg border border-blue-100">
                                <h2 class="text-xl font-semibold text-[#213268] mb-4 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    Laporan Pemeliharaan
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-500">Tanggal Laporan</span>
                                            <span
                                                class="font-medium">{{ isset($maintenance['maintenance_report']['maintenance_date']) ? date('d F Y H:i', strtotime($maintenance['maintenance_report']['maintenance_date'])) : 'N/A' }}</span>
                                        </div>

                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-500">Dilaporkan Oleh</span>
                                            <span>ID: {{ $maintenance['maintenance_report']['reported_by'] ?? 'N/A' }}</span>
                                        </div>

                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-500">Laporan Dibuat Pada</span>
                                            <span>{{ isset($maintenance['maintenance_report']['created_at']) ? date('d F Y H:i', strtotime($maintenance['maintenance_report']['created_at'])) : 'N/A' }}</span>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-500">Deskripsi</span>
                                            <p class="whitespace-pre-wrap">
                                                {{ $maintenance['maintenance_report']['description'] ?? 'Tidak ada deskripsi tersedia' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                @if(isset($maintenance['maintenance_report']['attachment_path']) && !empty($maintenance['maintenance_report']['attachment_path']))
                                    <div class="mt-4 pt-4 border-t border-blue-200">
                                        <span class="text-sm text-gray-500 block mb-2">Lampiran</span>
                                        <div class="flex justify-center">
                                            <div class="max-w-md">
                                                <img src="http://localhost:5000/public/images/{{ basename($maintenance['maintenance_report']['attachment_path']) }}"
                                                    alt="Maintenance Report Image"
                                                    class="max-w-full h-auto rounded-lg border border-gray-200 shadow-md"
                                                    style="max-height: 350px;"
                                                    onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('p-4');">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- History Section - Full Width -->
                    @php
                        $hasHistory = isset($maintenance['history']) && is_array($maintenance['history']) && !empty($maintenance['history']);
                    @endphp

                    @if($hasHistory)
                        <div class="mt-2">
                            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                                <h2 class="text-xl font-semibold text-[#213268] mb-4 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Riwayat Pemeliharaan
                                </h2>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="w-full">
                                        <thead>
                                            <tr>
                                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Tanggal</th>
                                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Diubah Oleh
                                                </th>
                                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Bidang</th>
                                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Dari</th>
                                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Menjadi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(is_array($maintenance['history']) ? $maintenance['history'] : [] as $historyItem)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                        {{ isset($historyItem['created_at']) ? date('d F Y H:i', strtotime($historyItem['created_at'])) : 'N/A' }}
                                                    </td>
                                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                        {{ $historyItem['user_name'] ?? 'N/A' }}</td>
                                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                        {{ ucfirst(str_replace('_', ' ', $historyItem['field_name'] ?? 'N/A')) }}
                                                    </td>
                                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                        {{ $historyItem['old_value'] ?? 'N/A' }}</td>
                                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                        {{ $historyItem['new_value'] ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection