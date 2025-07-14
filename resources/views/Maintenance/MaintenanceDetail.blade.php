@extends('Layout.app')

@section('title', 'Detail Pemeliharaan')

@section('content')
    @include('Layout.loading')
    <div class="h-full">
        <!-- Maintenance Details Section -->
        <div class="flex flex-col gap-6 p-4 md:p-7 bg-base-100 rounded-xl">
            <!-- Header with Back Button and Title -->
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center">
                    <a href="{{ route('maintenance') }}"
                        class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DETAIL PEMELIHARAAN</h1>
                </div>

                <!-- Export Button -->
                @if(hasPermission('maintenance:export'))
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
                @endif
            </div>

            <!-- Status Banner -->
            @php
                $statusClass = 'bg-gray-100 text-gray-800 border-gray-200';
                $status = $maintenance['status'] ?? '';
                $statusText = 'Tidak Diketahui';
                $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                $statusDescription = '';

                if ($status == 'new') {
                    $statusClass = 'bg-blue-100 text-blue-800 border-blue-200';
                    $statusText = 'Baru';
                    $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                    $statusDescription = 'Pemeliharaan baru dibuat dan belum dimulai';
                } elseif ($status == 'in_progress') {
                    $statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                    $statusText = 'Dalam Proses';
                    $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />';
                    $statusDescription = 'Pemeliharaan sedang dalam proses pengerjaan';
                } elseif ($status == 'finished') {
                    $statusClass = 'bg-green-100 text-green-800 border-green-200';
                    $statusText = 'Selesai';
                    $statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                    $statusDescription = 'Pemeliharaan telah selesai dilakukan';
                }
            @endphp

            <div class="w-full rounded-lg p-4 mb-4 border flex items-center gap-3 {{ $statusClass }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $statusIcon !!}
                </svg>
                <div>
                    <div class="font-semibold">Status: {{ $statusText }}</div>
                    <div class="text-sm">{{ $statusDescription }}</div>
                </div>
            </div>

            <!-- Main Content Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Asset Information Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Informasi Aset
                            </h2>

                            <!-- Asset Image -->
                            <div class="w-full h-40 bg-white mb-4 rounded-lg border border-gray-200 overflow-hidden relative flex items-center justify-center">
                                <img src="{{ isset($maintenance['asset_image_path']) ? (config('app.backend_url') . '/public' . $maintenance['asset_image_path']) : asset('images/placeholder.png') }}"
                                    alt="Asset Image" class="w-full h-full object-contain p-2"
                                    onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('object-contain', 'p-4');">
                            </div>

                            <div class="space-y-4">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Nama Aset</span>
                            <span class="font-medium text-lg">{{ $maintenance['asset_name'] ?? 'N/A' }}</span>
                                </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Kode Aset</span>
                            <span class="font-medium">{{ $maintenance['asset_code'] ?? 'N/A' }}</span>
                                </div>
                                @if(isset($maintenance['asset']) && isset($maintenance['asset']['location']))
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Lokasi</span>
                                <span class="font-medium">{{ $maintenance['asset']['location']['room_name'] ?? 'N/A' }},
                                            {{ $maintenance['asset']['location']['building_name'] ?? '' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Schedule Information Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                        Jadwal Pemeliharaan
                            </h2>
                            <div class="space-y-4">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Interval</span>
                                    <span class="font-medium">
                                        @php
                                            $intervalText = $maintenance['interval'] ?? 'N/A';
                                            $translations = [
                                                'ONCE' => 'Sekali',
                                                'DAILY' => 'Harian',
                                                'WEEKLY' => 'Mingguan',
                                                '2 WEEKS' => '2 Minggu',
                                                'MONTHLY' => 'Bulanan',
                                                '2 MONTHS' => '2 Bulan',
                                                '3 MONTHS' => '3 Bulan',
                                                '4 MONTHS' => '4 Bulan',
                                                '6 MONTHS' => '6 Bulan',
                                                'YEARLY' => 'Tahunan'
                                            ];

                                            if (array_key_exists($intervalText, $translations)) {
                                                $intervalText = $translations[$intervalText];
                                            }
                                        @endphp
                                        {{ $intervalText }}
                                    </span>
                                </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Tanggal Mulai</span>
                            <span class="font-medium">{{ isset($maintenance['start_date']) ? \Carbon\Carbon::parse($maintenance['start_date'])->locale('id')->isoFormat('D MMMM Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Tanggal Selesai</span>
                            <span class="font-medium">{{ isset($maintenance['end_date']) ? \Carbon\Carbon::parse($maintenance['end_date'])->locale('id')->isoFormat('D MMMM Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                        <!-- Assignment Information Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
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
                                    <span class="font-medium">{{ $maintenance['assigned_to_employee_name'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Dijadwalkan Oleh</span>
                            <span class="font-medium">{{ $maintenance['scheduled_by_employee_name'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-500">Vendor</span>
                            <span class="font-medium">{{ $maintenance['vendor_name'] ?? 'N/A' }}</span>
                        </div>
                                </div>
                            </div>
                        </div>

            <!-- Notes & Documents Section -->
            @if((isset($maintenance['notes']) && !empty($maintenance['notes']) && $maintenance['notes'] != 'No notes available') || (isset($maintenance['document_file_path']) && !empty($maintenance['document_file_path'])))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mt-2">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Catatan & Dokumen
                                </h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    @if(isset($maintenance['notes']) && !empty($maintenance['notes']) && $maintenance['notes'] != 'No notes available')
                                        <div class="flex flex-col">
                                <span class="text-sm text-gray-500 mb-2">Catatan</span>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                    <p class="whitespace-pre-wrap">{{ $maintenance['notes'] }}</p>
                                </div>
                                        </div>
                                    @endif

                                    @if(isset($maintenance['document_file_path']) && !empty($maintenance['document_file_path']))
                                        <div class="flex flex-col">
                                <span class="text-sm text-gray-500 mb-2">Dokumen Terlampir</span>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 flex items-center">
                                    <svg class="w-10 h-10 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium">{{ basename($maintenance['document_file_path']) }}</p>
                                            <a href="{{ $maintenance['document_file_path'] }}" target="_blank"
                                            class="text-blue-600 hover:underline text-sm flex items-center mt-1">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                Lihat Dokumen
                                            </a>
                                        </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Maintenance Report Section -->
                @if(isset($maintenance['maintenance_report']))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mt-2">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
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
                                <span class="font-medium">{{ isset($maintenance['maintenance_report']['maintenance_date']) ? \Carbon\Carbon::parse($maintenance['maintenance_report']['maintenance_date'])->locale('id')->isoFormat('D MMMM Y') : 'N/A' }}</span>
                                    </div>

                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Dilaporkan Oleh</span>
                                <span class="font-medium">{{ $maintenance['maintenance_report']['reported_by_employee_number'] ?? 'N/A' }}</span>
                                    </div>

                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Laporan Dibuat Pada</span>
                                <span class="font-medium">{{ isset($maintenance['maintenance_report']['created_at']) ? \Carbon\Carbon::parse($maintenance['maintenance_report']['created_at'])->locale('id')->isoFormat('D MMMM Y') : 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Deskripsi</span>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        <p class="whitespace-pre-wrap">
                                            {{ $maintenance['maintenance_report']['description'] ?? 'Tidak ada deskripsi tersedia' }}
                                        </p>
                                </div>
                                    </div>
                                </div>
                            </div>

                            @if(isset($maintenance['maintenance_report']['attachment_path']) && !empty($maintenance['maintenance_report']['attachment_path']))
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <span class="text-sm text-gray-500 block mb-3">Lampiran</span>
                                    <div class="flex justify-center">
                                        <div class="max-w-md">
                                            <img src="https://web-magangunbin2025.rsummi.co.id/api/public/images/{{ basename($maintenance['maintenance_report']['attachment_path']) }}"
                                                alt="Maintenance Report Image"
                                                class="max-w-full h-auto rounded-lg border border-gray-200 shadow-md"
                                                style="max-height: 350px;"
                                                onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('p-4');">
                                        </div>
                                    </div>
                                </div>
                            @endif
                    </div>
                @endif

            <!-- History Section -->
                @php
                    $hasHistory = isset($maintenance['history']) && is_array($maintenance['history']) && !empty($maintenance['history']);
                @endphp

                @if($hasHistory)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mt-2">
                    <h2 class="font-bold text-lg text-[#213268] border-b pb-2 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
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
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Diubah Oleh</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Bidang</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Dari</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Menjadi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(is_array($maintenance['history']) ? $maintenance['history'] : [] as $historyItem)
                                            <tr class="hover:bg-gray-50">
                                                <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                                    {{ isset($historyItem['created_at']) ? \Carbon\Carbon::parse($historyItem['created_at'])->locale('id')->isoFormat('D MMMM Y HH:mm') : 'N/A' }}
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
                @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(!hasPermission('maintenance:export'))
                const exportButton = document.querySelector('a[href*="maintenance.export.detail.pdf"]');
                if (exportButton) {
                    exportButton.style.display = 'none';
                }
            @endif
        });
    </script>
@endpush
