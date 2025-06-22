@extends('Layout.app')

@section('title', 'Detail Opname')

@section('styles')
<style>
    .active-filter {
        background-color: #213268;
        color: white;
        border-color: #213268;
    }

    .status-filter-btn:not(.active-filter) {
        background-color: white;
        color: #213268;
    }

    .status-filter-btn:hover:not(.active-filter) {
        background-color: #F8F9FA;
    }

    .status-filter-btn {
        position: relative;
        overflow: hidden;
        transform: translateZ(0);
    }

    @keyframes ripple {
        0% {
            transform: scale(0);
            opacity: 0.8;
        }
        100% {
            transform: scale(4);
            opacity: 0;
        }
    }

    .animate-ripple {
        animation: ripple 0.6s linear;
        transform-origin: center;
    }
</style>
@endsection

@section('content')
@include('Layout.loading')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Opname Detail Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header with Back Button and Title -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center">
                        <a href="{{ route('report.opname') }}" class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">
                                Detail Opname
                            </h1>
                            <p class="text-sm text-gray-500 mt-1">Lihat informasi detail tentang opname ini</p>
                        </div>
                    </div>

                    <!-- Export Buttons -->
                    <div class="flex gap-2">
                        <a href="{{ route('opnames.export.pdf', ['id' => $opnameId]) }}" target="_blank"
                           class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Ekspor PDF
                        </a>
                    </div>
                </div>

                <!-- Opname Info Card -->
                <div class="bg-[#F8F9FA] p-5 rounded-lg border border-[#E9ECEF]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Opname Information -->
                        <div>
                            <h3 class="text-[#213268] font-semibold text-lg mb-3">Informasi Opname</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Kode Opname</p>
                                    <p class="text-lg font-semibold text-[#213268]">{{ $opnameInfo['opname_code'] ?? $opnameCode ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Tanggal Dibuat</p>
                                    <p class="text-lg font-semibold text-[#213268]">
                                        {{ isset($opnameInfo['opname_created_at']) ? \Carbon\Carbon::parse($opnameInfo['opname_created_at'])->locale('id')->isoFormat('D MMMM Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div>
                            <h3 class="text-[#213268] font-semibold text-lg mb-3">Lokasi</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Gedung</p>
                                    <p class="text-lg font-semibold text-[#213268]">{{ $roomInfo['building'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Ruangan</p>
                                    <p class="text-lg font-semibold text-[#213268]">{{ $roomInfo['room_name'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Lantai</p>
                                    <p class="text-lg font-semibold text-[#213268]">{{ $roomInfo['floor'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards Section Title -->
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-[#213268] mb-4">Ringkasan Aset</h2>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                    <div class="bg-gradient-to-br from-[#E9ECEF] to-[#F8F9FA] p-5 rounded-lg shadow-sm border border-[#E9ECEF]">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-base font-medium text-gray-700">Total Aset</p>
                            <div class="bg-white p-1.5 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#213268]" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1v1h-1v-1H6v1H5v-1a1 1 0 01-1-1V4zm3 1h6v4H7V5zm6 6H7v2h6v-2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $summary['total_assets'] ?? 0 }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-[#E9ECEF] to-[#F8F9FA] p-5 rounded-lg shadow-sm border border-[#E9ECEF]">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-base font-medium text-gray-700">Terscan</p>
                            <div class="bg-white p-1.5 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#213268]" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $summary['scanned_assets'] ?? 0 }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-[#DCFCE7] to-[#F0FDF4] p-5 rounded-lg shadow-sm border border-[#DCFCE7]">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-base font-medium text-green-700">Ditemukan</p>
                            <div class="bg-white p-1.5 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-green-600">{{ $summary['found_assets'] ?? 0 }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-[#FEE2E2] to-[#FEF2F2] p-5 rounded-lg shadow-sm border border-[#FEE2E2]">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-base font-medium text-red-700">Hilang</p>
                            <div class="bg-white p-1.5 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-red-600">{{ $summary['missing_assets'] ?? 0 }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-[#FEF3C7] to-[#FFFBEB] p-5 rounded-lg shadow-sm border border-[#FEF3C7]">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-base font-medium text-amber-700">Salah Tempat</p>
                            <div class="bg-white p-1.5 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-amber-500">{{ $summary['misplaced_assets'] ?? 0 }}</p>
                    </div>
                </div>

                <!-- Asset Details Section Title -->
                <div class="flex justify-between items-center">
                    <h2 class="text-xl md:text-2xl font-bold text-[#213268]">Detail Aset</h2>

                    <!-- Search Input -->
                    <div class="relative max-w-xs">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="table-search" class="bg-white border border-[#D8DAE5] text-gray-900 text-sm rounded-md focus:ring-[#213268] focus:border-[#213268] block w-full pl-10 p-2.5" placeholder="Cari aset...">
                    </div>
                </div>

                <!-- Status Filter Buttons -->
                <div class="flex flex-wrap gap-2">
                    <button data-url="{{ request()->fullUrlWithQuery(['scan_status' => '']) }}"
                       class="status-filter-btn px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 {{ !request()->has('scan_status') || request()->input('scan_status') == '' ? 'active-filter bg-[#213268] text-white hover:bg-[#1a2857]' : 'bg-white border border-[#D8DAE5] hover:bg-[#F8F9FA]' }}">
                        Semua Status
                    </button>
                    <button data-url="{{ request()->fullUrlWithQuery(['scan_status' => 'found']) }}"
                       class="status-filter-btn px-4 py-2 rounded-md border border-[#D8DAE5] text-sm font-medium transition-colors duration-200 {{ request()->input('scan_status') == 'found' ? 'active-filter bg-[#213268] text-white hover:bg-[#1a2857]' : 'bg-white hover:bg-[#F8F9FA]' }}">
                        <span class="inline-flex items-center">
                            <span class="h-2 w-2 rounded-full bg-green-600 mr-1.5"></span>
                            Ditemukan
                        </span>
                    </button>
                    <button data-url="{{ request()->fullUrlWithQuery(['scan_status' => 'missing']) }}"
                       class="status-filter-btn px-4 py-2 rounded-md border border-[#D8DAE5] text-sm font-medium transition-colors duration-200 {{ request()->input('scan_status') == 'missing' ? 'active-filter bg-[#213268] text-white hover:bg-[#1a2857]' : 'bg-white hover:bg-[#F8F9FA]' }}">
                        <span class="inline-flex items-center">
                            <span class="h-2 w-2 rounded-full bg-red-600 mr-1.5"></span>
                            Hilang
                        </span>
                    </button>
                    <button data-url="{{ request()->fullUrlWithQuery(['scan_status' => 'misplaced']) }}"
                       class="status-filter-btn px-4 py-2 rounded-md border border-[#D8DAE5] text-sm font-medium transition-colors duration-200 {{ request()->input('scan_status') == 'misplaced' ? 'active-filter bg-[#213268] text-white hover:bg-[#1a2857]' : 'bg-white hover:bg-[#F8F9FA]' }}">
                        <span class="inline-flex items-center">
                            <span class="h-2 w-2 rounded-full bg-amber-500 mr-1.5"></span>
                            Salah Tempat
                        </span>
                    </button>
                </div>

                <!-- Assets Table -->
                <div class="overflow-x-auto rounded-md border border-[#EEF1F4]">
                    <table class="w-full" id="assetsTable">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Kode Aset</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Nama Aset</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Tanggal Scan</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Status</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Lokasi Seharusnya</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Lokasi Aktual</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Discan Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details as $asset)
                                <tr class="hover:bg-[#F8F9FA] transition-all duration-150 asset-row">
                                    <td class="p-3 text-sm border-t border-[#EEF1F4] font-medium">{{ $asset['asset_code'] ?? '-' }}</td>
                                    <td class="p-3 text-sm border-t border-[#EEF1F4]">{{ $asset['asset_name'] ?? '-' }}</td>
                                    <td class="p-3 text-sm border-t border-[#EEF1F4]">
                                        @if(isset($asset['scan_date']))
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ \Carbon\Carbon::parse($asset['scan_date'])->locale('id')->isoFormat('D MMMM Y HH:mm') }}
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-3 text-sm border-t border-[#EEF1F4]">
                                        @if(isset($asset['scan_status']))
                                            @if($asset['scan_status'] == 'found')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-green-600 mr-1.5"></span>
                                                    Ditemukan
                                                </span>
                                            @elseif($asset['scan_status'] == 'missing')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-red-600 mr-1.5"></span>
                                                    Hilang
                                                </span>
                                            @elseif($asset['scan_status'] == 'misplaced')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                                    Salah Tempat
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ ucfirst(str_replace('_', ' ', $asset['scan_status'])) }}
                                                </span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-3 text-sm border-t border-[#EEF1F4]">
                                        @if(isset($asset['expected_location_name']))
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $asset['expected_location_name'] }}
                                            </div>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="p-3 text-sm border-t border-[#EEF1F4]">
                                        @if(isset($asset['actual_location_name']))
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $asset['actual_location_name'] }}
                                            </div>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="p-3 text-sm border-t border-[#EEF1F4]">
                                        @if(isset($asset['scanner_name']))
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ $asset['scanner_name'] }}
                                            </div>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr id="no-results-row" style="display: none;">
                                    <td colspan="7" class="p-6 text-sm border-t border-[#EEF1F4] text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p>Tidak ada aset yang sesuai dengan kriteria pencarian</p>
                                            <button id="clear-search" class="mt-3 text-sm text-[#213268] font-medium hover:underline">Hapus Pencarian</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="empty-table-row">
                                    <td colspan="7" class="p-6 text-sm border-t border-[#EEF1F4] text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p>Tidak ditemukan detail aset untuk opname ini</p>
                                            <button class="mt-3 text-sm text-[#213268] font-medium hover:underline" onclick="window.location.reload()">Segarkan Data</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Asset Details -->
                @if(isset($pagination) && ($pagination['total_items'] ?? 0) > 0)
                <div class="flex flex-col md:flex-row justify-between items-center mt-6 bg-[#F8F9FA] p-3 rounded-md border border-[#EEF1F4]">
                    <div class="flex items-center space-x-2">
                        <a href="{{ request()->fullUrlWithQuery(['page' => max(1, ($pagination['current_page'] ?? 1) - 1)]) }}"
                            class="flex items-center gap-2 px-4 py-2 bg-white border border-[#D8DAE5] rounded-md text-[#213268] text-sm font-medium hover:bg-[#F8F9FA] transition-all duration-200 {{ !($pagination['has_prev'] ?? false) ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </a>
                        <div class="flex gap-2">
                            @php
                                $currentPage = $pagination['current_page'] ?? 1;
                                $totalPages = $pagination['total_pages'] ?? 1;
                                $maxPagesShown = 5;
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $startPage + $maxPagesShown - 1);

                                if ($endPage - $startPage + 1 < $maxPagesShown) {
                                    $startPage = max(1, $endPage - $maxPagesShown + 1);
                                }
                            @endphp

                            @if($startPage > 1)
                                <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}"
                                    class="h-9 w-9 flex items-center justify-center bg-white border border-[#D8DAE5] text-[#213268] rounded-md text-sm font-medium hover:bg-[#F8F9FA] transition-all duration-200">
                                    1
                                </a>
                                @if($startPage > 2)
                                    <span class="flex items-center justify-center text-sm text-gray-500">
                                        ...
                                    </span>
                                @endif
                            @endif

                            @for ($i = $startPage; $i <= $endPage; $i++)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                                    class="h-9 w-9 flex items-center justify-center border {{ $i == $currentPage ? 'bg-[#213268] text-white' : 'bg-white text-[#213268] border-[#D8DAE5] hover:bg-[#F8F9FA]' }} rounded-md text-sm font-medium transition-all duration-200">
                                    {{ $i }}
                                </a>
                            @endfor

                            @if($endPage < $totalPages)
                                @if($endPage < $totalPages - 1)
                                    <span class="flex items-center justify-center text-sm text-gray-500">
                                        ...
                                    </span>
                                @endif
                                <a href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}"
                                    class="h-9 w-9 flex items-center justify-center bg-white border border-[#D8DAE5] text-[#213268] rounded-md text-sm font-medium hover:bg-[#F8F9FA] transition-all duration-200">
                                    {{ $totalPages }}
                                </a>
                            @endif
                        </div>
                        <a href="{{ request()->fullUrlWithQuery(['page' => min($totalPages, ($currentPage + 1))]) }}"
                            class="flex items-center gap-2 px-4 py-2 bg-white border border-[#D8DAE5] rounded-md text-[#213268] text-sm font-medium hover:bg-[#F8F9FA] transition-all duration-200 {{ !($pagination['has_next'] ?? false) ? 'opacity-50 cursor-not-allowed' : '' }}">
                            Selanjutnya
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <div class="flex items-center gap-3 mt-4 md:mt-0">
                        <span class="text-sm font-medium text-gray-600">
                            Menampilkan {{ ($currentPage - 1) * ($pagination['limit'] ?? 10) + 1 }} sampai
                            {{ min($currentPage * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0) }} dari
                            {{ $pagination['total_items'] ?? 0 }} data
                        </span>
                        <select id="perPageSelect"
                            class="px-3 py-2 bg-white border border-[#D8DAE5] rounded-md text-[#213268] text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#213268]"
                            onchange="changePerPage(this.value)">
                            <option value="10" {{ ($pagination['limit'] ?? 10) == 10 ? 'selected' : '' }}>10 per halaman</option>
                            <option value="25" {{ ($pagination['limit'] ?? 10) == 25 ? 'selected' : '' }}>25 per halaman</option>
                            <option value="50" {{ ($pagination['limit'] ?? 10) == 50 ? 'selected' : '' }}>50 per halaman</option>
                        </select>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('table-search');
        const assetsTable = document.getElementById('assetsTable');
        const assetRows = document.querySelectorAll('.asset-row');
        const noResultsRow = document.getElementById('no-results-row');
        const emptyTableRow = document.getElementById('empty-table-row');
        const clearSearchButton = document.getElementById('clear-search');
        const statusFilterButtons = document.querySelectorAll('.status-filter-btn');

        statusFilterButtons.forEach(button => {
            button.addEventListener('click', function() {
                this.classList.add('opacity-75');
                const ripple = document.createElement('span');
                ripple.classList.add('absolute', 'inset-0', 'bg-white', 'bg-opacity-30', 'rounded-md', 'animate-ripple');
                this.appendChild(ripple);
                const url = this.getAttribute('data-url');
                setTimeout(() => {
                    window.location.href = url;
                }, 150);
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        const filterTable = debounce(function() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let matchFound = false;

            if (searchTerm === '') {
                assetRows.forEach(row => {
                    row.style.display = '';
                });
                if (noResultsRow) noResultsRow.style.display = 'none';
                return;
            }

            assetRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    matchFound = true;
                } else {
                    row.style.display = 'none';
                }
            });

            if (!matchFound && assetRows.length > 0) {
                if (noResultsRow) noResultsRow.style.display = 'table-row';
                if (emptyTableRow) emptyTableRow.style.display = 'none';
            } else {
                if (noResultsRow) noResultsRow.style.display = 'none';
                if (emptyTableRow) emptyTableRow.style.display = assetRows.length === 0 ? 'table-row' : 'none';
            }
        }, 300);

        if (searchInput) {
            searchInput.addEventListener('input', filterTable);
        }
        if (clearSearchButton) {
            clearSearchButton.addEventListener('click', function() {
                if (searchInput) searchInput.value = '';
                filterTable();
            });
        }
    });
</script>
@endpush
@endsection
