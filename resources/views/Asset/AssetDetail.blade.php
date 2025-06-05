@extends('Layout.app')

@section('title', 'Detil Aset')

@section('content')
    @include('Layout.loading')
    <div class="h-full">
        <!-- Asset Details Section -->
        <div class="flex flex-col gap-6 p-4 md:p-7 bg-base-100 rounded-xl">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center">
                    <a href="{{ route('assets') }}"
                        class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-xl md:text-2xl lg:text-[32px] font-semibold text-[#213268]">DETIL ASSET</h1>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2 md:gap-3 w-full md:w-auto justify-start md:justify-end">
                    @if($asset['current_status'] === 'dispose')
                        <!-- When status is disposed, show only Edit button -->
                        @if(hasPermission('asset:edit'))
                            <a href="javascript:void(0)" id="editAssetBtn"
                                class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span>Ubah</span>
                            </a>
                        @endif
                    @elseif($asset['current_status'] === 'available')
                        <!-- When status is available: Check Out, Dispose, Lost, Edit buttons -->
                        @if(hasPermission('asset:checkout'))
                            <button type="button" id="checkoutAssetBtn"
                                class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Pinjam</span>
                            </button>
                        @endif
                        @if(hasPermission('asset:dispose'))
                            <button type="button" id="disposeAssetBtn"
                                class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Hapuskan</span>
                            </button>
                        @endif
                        @if(hasPermission('asset:report-loss'))
                            <button type="button" id="lostAssetBtn"
                                class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Hilang</span>
                            </button>
                        @endif
                        @if(hasPermission('asset:edit'))
                            <button type="button" id="editAssetBtn"
                                class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span>Ubah</span>
                            </button>
                        @endif
                    @elseif($asset['current_status'] === 'check out')
                        <!-- When status is check out: Check In, Dispose, Lost, Edit buttons -->
                        @if(hasPermission('asset:checkout'))
                            <button type="button" id="checkinAssetBtn"
                                class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                <span>Kembalikan</span>
                            </button>
                        @endif
                        @if(hasPermission('asset:dispose'))
                            <button type="button" id="disposeAssetBtn"
                                class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Hapuskan</span>
                            </button>
                        @endif
                        @if(hasPermission('asset:report-loss'))
                            <button type="button" id="lostAssetBtn"
                                class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Hilang</span>
                            </button>
                        @endif
                        @if(hasPermission('asset:edit'))
                            <button type="button" id="editAssetBtn"
                                class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span>Ubah</span>
                            </button>
                        @endif
                    @elseif($asset['current_status'] === 'lost')
                        <!-- When status is lost: Found, Edit buttons -->
                        @if(hasPermission('asset:report-found'))
                            <button type="button" id="foundAssetBtn"
                                class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span>Ditemukan</span>
                            </button>
                        @endif
                        @if(hasPermission('asset:edit'))
                            <button type="button" id="editAssetBtn"
                                class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span>Ubah</span>
                            </button>
                        @endif
                    @endif
                    <!-- Add this button alongside the other action buttons -->
                    <a href="{{ route('asset.export-pdf', ['id' => $asset['asset_id'] ?? '']) }}"
                        class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200"
                        target="_blank">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Export PDF</span>
                    </a>
                </div>
            </div>

            <!-- Asset Details Main Content -->
            <div class="flex flex-col lg:flex-row gap-4 md:gap-8">
                <!-- Asset Image and Status -->
                <div class="w-full lg:w-[350px] xl:w-[400px]">
                    <div class="flip-card-container relative h-[180px] md:h-[268px] w-full">
                        <div class="flip-card w-full h-full transition-transform duration-700">
                            <!-- Front side (Asset image) -->
                            <div
                                class="flip-card-front bg-[#D9D9D9] rounded-[20px] shadow-md flex items-center justify-center overflow-hidden relative w-full h-full">
                                @if(isset($asset['asset_master']['reference_image_path']) && $asset['asset_master']['reference_image_path'])
                                    <img src="{{ config('app.backend_url') }}/public{{ $asset['asset_master']['reference_image_path'] }}"
                                        alt="Asset Image" class="absolute inset-0 w-full h-full object-cover p-0"
                                        style="object-position: center;"
                                        onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.remove('object-cover'); this.classList.add('object-contain', 'p-4'); this.style.position='relative';">
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-10 transition-all duration-300 rounded-[20px]">
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm">Tidak ada gambar</span>
                                    </div>
                                @endif
                                <!-- Icon to flip to QR code -->
                                <button
                                    class="flip-btn absolute top-2 right-2 p-2 bg-white bg-opacity-70 hover:bg-opacity-100 rounded-full shadow-md z-10 transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#213268]" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1v-2a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Back side (QR Code) -->
                            <div
                                class="flip-card-back bg-white rounded-[20px] shadow-md flex items-center justify-center overflow-hidden relative w-full h-full">
                                <div class="flex flex-col items-center justify-center w-3/4 h-3/4">
                                    @if(isset($asset['qr_base64']))
                                        <img src="{{ $asset['qr_base64'] }}" alt="Asset QR Code"
                                            class="w-full h-full object-contain">
                                    @elseif(isset($asset['qr_code']))
                                        @php
                                            $backendUrl = rtrim(config('app.backend_url'), '/');
                                            $qrImageUrl = $backendUrl . '/public' . $asset['qr_code'];
                                        @endphp
                                        <img src="{{ $qrImageUrl }}" alt="Asset QR Code" class="w-full h-full object-contain"
                                            onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.add('p-4');">
                                    @else
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1v-2a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                            </svg>
                                            <span class="text-sm">QR Code tidak tersedia</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="absolute top-1 left-1 text-xs font-semibold bg-gray-200 rounded-lg px-2 py-1">
                                    {{ $asset['asset_code'] ?? '-' }}
                                </div>

                                <!-- Icon to flip back to image -->
                                <button
                                    class="flip-btn absolute top-2 right-2 p-2 bg-white bg-opacity-70 hover:bg-opacity-100 rounded-full shadow-md z-10 transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#213268]" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Asset identification - mobile layout with smaller text -->
                    <div class="flex flex-col items-center mt-4">
                        <p class="text-sm font-medium text-center mt-1">{{ $asset['asset_code'] ?? '-' }}</p>
                        <p class="text-sm font-medium text-center mt-2">
                            {{ $asset['asset_master_name'] ?? $asset['asset_master']['asset_name'] ?? '-' }}</p>
                        @php
                            $statusColor = 'bg-gray-500';
                            if (isset($asset['current_status'])) {
                                switch (strtolower($asset['current_status'])) {
                                    case 'available':
                                        $statusColor = 'bg-[#659B09]';
                                        break;
                                    case 'check out':
                                        $statusColor = 'bg-[#F59E0B]';
                                        break;
                                    case 'lost':
                                        $statusColor = 'bg-[#EF4444]';
                                        break;
                                    case 'dispose':
                                        $statusColor = 'bg-[#ACC3EF]';
                                        break;
                                    case 'under repair':
                                        $statusColor = 'bg-[#25B1FF]';
                                        break;
                                }
                            }
                        @endphp
                        <div class="{{ $statusColor }} py-0.5 px-3 rounded-md w-full max-w-[120px] text-center mt-2">
                            <p class="text-xs text-white">
                                @php
                                    $statusText = 'UNKNOWN';
                                    if (isset($asset['current_status'])) {
                                        switch (strtolower($asset['current_status'])) {
                                            case 'available':
                                                $statusText = 'TERSEDIA';
                                                break;
                                            case 'check out':
                                                $statusText = 'DIPINJAM';
                                                break;
                                            case 'lost':
                                                $statusText = 'HILANG';
                                                break;
                                            case 'dispose':
                                                $statusText = 'DIHAPUSKAN';
                                                break;
                                            case 'under repair':
                                                $statusText = 'PERBAIKAN';
                                                break;
                                            default:
                                                $statusText = strtoupper($asset['current_status']);
                                        }
                                    }
                                @endphp
                                {{ $statusText }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Asset Information -->
                <div class="flex-1 lg:max-h-[268px] overflow-y-auto custom-scrollbar pr-1">
                    <!-- Master Asset Information -->
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-[#203268] sticky top-0 bg-white py-2 z-10">Informasi Master
                            Aset</h2>
                        <div class="grid grid-cols-1 gap-3">
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Kode Master Aset</span>
                                <span class="text-sm">{{ $asset['asset_master']['asset_master_code'] ?? '-' }}</span>
                            </div>
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Tipe Aset</span>
                                <span class="text-sm">
                                    @php
                                        $assetType = $asset['asset_master']['asset_type'] ?? '-';
                                        if (strtolower($assetType) === 'medical') {
                                            echo 'Medis';
                                        } elseif (strtolower($assetType) === 'non_medical') {
                                            echo 'Non Medis';
                                        } else {
                                            echo $assetType;
                                        }
                                    @endphp
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Kategori</span>
                                <span class="text-sm">{{ $asset['asset_master']['subcategory_name'] ?? '-' }}</span>
                            </div>
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Merek</span>
                                <span class="text-sm">{{ $asset['asset_master']['brand_name'] ?? '-' }}</span>
                            </div>
                            <div class="flex flex-wrap items-start">
                                <span class="w-[150px] font-semibold text-sm pt-0.5">Deskripsi</span>
                                <span
                                    class="flex-1 text-sm">{{ $asset['asset_master']['description'] ?? 'Tidak ada deskripsi' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Asset Information -->
                    <div>
                        <h2 class="text-lg font-semibold text-[#203268] sticky top-0 bg-white py-2 z-10">Informasi Aset</h2>
                        <div class="grid grid-cols-1 gap-y-3">
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Ruangan</span>
                                <span class="text-sm">{{ $asset['room_name'] ?? '-' }}</span>
                            </div>
                            <div class="flex flex-wrap items-start">
                                <span class="w-[150px] font-semibold text-sm pt-0.5">Gedung</span>
                                <span class="text-sm">{{ $asset['building_name'] ?? '-' }}</span>
                            </div>
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Kondisi</span>
                                <span class="text-sm">
                                    @php
                                        $condition = $asset['condition'] ?? '-';
                                        if (strtolower($condition) === 'good') {
                                            echo 'Baik';
                                        } elseif (strtolower($condition) === 'slighly damage') {
                                            echo 'Sedikit Rusak';
                                        } elseif (strtolower($condition) === 'high damage') {
                                            echo 'Sangat Rusak';
                                        } else {
                                            echo ucfirst($condition);
                                        }
                                    @endphp
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Garansi Berakhir</span>
                                <span class="text-sm">{{ $asset['warranty_end_date'] ?? '-' }}</span>
                            </div>
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Nomor Seri</span>
                                <span class="text-sm">{{ $asset['serial_number'] ?? '-' }}</span>
                            </div>
                            <!-- Add Employee Number for Responsible User -->
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Penanggung Jawab</span>
                                <span class="text-sm">{{ $asset['employee_number'] ?? '-' }}</span>
                            </div>
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Harga Beli</span>
                                <span class="text-sm">{{ number_format((float) ($asset['purchase_cost'] ?? 0), 2) }}</span>
                            </div>
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Tanggal Beli</span>
                                <span class="text-sm">{{ $asset['purchase_date'] ?? '-' }}</span>
                            </div>

                            @if($asset['current_status'] === 'dispose')
                                <div class="flex flex-wrap items-center">
                                    <span class="w-[150px] font-semibold text-sm">Tanggal Dimusnahkan</span>
                                    <span class="text-sm">{{ $asset['updated_at'] ?? '-' }}</span>
                                </div>
                            @endif

                            @if($asset['current_status'] === 'lost')
                                <div class="flex flex-wrap items-center">
                                    <span class="w-[150px] font-semibold text-sm">Tanggal Hilang</span>
                                    <span class="text-sm">
                                        @if(isset($asset['updated_at']))
                                            @php
                                                // Convert the timestamp to a more readable format
                                                $lostDate = \Carbon\Carbon::parse($asset['updated_at'])->format('Y-m-d');
                                            @endphp
                                            {{ $lostDate }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Section -->
            <div class="mt-4">
                <div class="bg-base-100 shadow-xl overflow-hidden rounded-lg">
                    <!-- Tabs Navigation -->
                    <div class="flex flex-nowrap border-b border-[#EEF1F4] bg-white w-full overflow-x-auto hide-scrollbar">
                        @if(hasPermission('asset:view'))
                            <button
                                class="tab-btn whitespace-nowrap flex-none md:flex-1 flex items-center justify-center gap-1 md:gap-2 px-3 py-2 md:px-2 md:py-3 text-[#213268] border-b-2 border-[#213268] font-medium text-xs md:text-sm active"
                                data-tab="document">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Dokumen</span>
                            </button>
                            <button
                                class="tab-btn flex-1 flex items-center justify-center gap-2 px-2 py-3 text-gray-500 hover:text-[#213268]"
                                data-tab="history">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Riwayat
                            </button>
                            <button
                                class="tab-btn flex-1 flex items-center justify-center gap-2 px-2 py-3 text-gray-500 hover:text-[#213268]"
                                data-tab="mutation">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                                Mutasi
                            </button>
                        @endif
                        @if(hasPermission('asset:depreciation:view'))
                            <button
                                class="tab-btn flex-1 flex items-center justify-center gap-2 px-2 py-3 text-gray-500 hover:text-[#213268]"
                                data-tab="depreciation">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                                Penyusutan
                            </button>
                        @endif
                        @if(hasPermission('asset:transaction:view'))
                            <button
                                class="tab-btn flex-1 flex items-center justify-center gap-2 px-2 py-3 text-gray-500 hover:text-[#213268]"
                                data-tab="finance">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Keuangan
                            </button>
                        @endif
                    </div>

                    <!-- Tab Content -->
                    <div id="tab-content" class="overflow-y-auto max-h-[500px] border border-gray-200 rounded-md">
                        @if(hasPermission('asset:view'))
                            <div class="tab-pane" id="document">
                                @include('Asset.Tabs.Document')
                            </div>
                            <div class="tab-pane hidden" id="history">
                                @include('Asset.Tabs.History')
                            </div>
                            <div class="tab-pane hidden" id="mutation">
                                @include('Asset.Tabs.Mutation')
                            </div>
                        @endif
                        @if(hasPermission('asset:depreciation:view'))
                            <div class="tab-pane hidden" id="depreciation">
                                @include('Asset.Tabs.Depreciation')
                            </div>
                        @endif
                        @if(hasPermission('asset:transaction:view'))
                            <div class="tab-pane hidden" id="finance">
                                @include('Asset.Tabs.Finance')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Asset Modal -->
    @if(hasPermission('asset:edit'))
        <div id="editAssetModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="editAssetModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-4 border-b">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">UBAH ASET</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="editAssetForm" method="POST"
                            action="{{ route('asset.update', ['id' => $asset['asset_id'] ?? '']) }}"
                            enctype="multipart/form-data" data-no-loading novalidate>
                            @csrf
                            @method('PUT')
                            <div class="p-6">
                                <div class="space-y-6">
                                    <!-- Asset Information Section -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-[#213268] mb-4">Informasi Aset</h3>

                                        <!-- Master Asset selection -->
                                        <div class="mb-5 space-y-2">
                                            <label for="edit_asset_master_search"
                                                class="block text-base font-semibold text-[#666666] mb-2">Master Aset <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <input type="text" id="edit_asset_master_search"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="Cari master aset..." autocomplete="off" required>
                                                <input type="hidden" name="asset_master_id" id="edit_selected_asset_master_id"
                                                    required>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Master aset harus
                                                    dipilih</div>
                                                <input type="hidden" id="edit_selected_is_depreciable" value="false">

                                                <!-- Dropdown -->
                                                <div id="edit_asset_master_dropdown"
                                                    class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                    <div id="edit_asset_master_loading" class="p-2 text-gray-500 text-center">
                                                        <svg class="animate-spin h-5 w-5 mx-auto"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                        <span>Memuat master aset...</span>
                                                    </div>
                                                    <ul id="edit_asset_master_list" class="py-1"></ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Serial Number -->
                                        <div class="mb-5 space-y-2">
                                            <label for="edit_serial_number"
                                                class="block text-base font-semibold text-[#666666] mb-2">Nomor Seri</label>
                                            <input type="text" name="serial_number" id="edit_serial_number"
                                                value="{{ $asset['serial_number'] ?? '' }}"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                placeholder="Masukkan nomor seri">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Nomor seri harus diisi
                                            </div>
                                        </div>

                                        <!-- Purchase Information -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Tanggal
                                                    Pembelian</label>
                                                <input type="date" name="purchase_date" id="edit_purchase_date"
                                                    value="{{ $asset['purchase_date'] ?? '' }}"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal pembelian
                                                    harus diisi</div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Biaya
                                                    Pembelian</label>
                                                <input type="number" name="purchase_cost" id="edit_purchase_cost" step="0.01"
                                                    value="{{ $asset['purchase_cost'] ?? '0.00' }}"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="0.00">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Biaya pembelian
                                                    harus diisi</div>
                                            </div>
                                        </div>

                                        <!-- Warranty -->
                                        <div class="mb-5 space-y-2">
                                            <label class="block text-base font-semibold text-[#666666] mb-2">Tanggal Berakhir
                                                Garansi</label>
                                            <input type="date" name="warranty_end_date" id="edit_warranty_end_date"
                                                value="{{ $asset['warranty_end_date'] ?? '' }}"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal berakhir garansi
                                                harus diisi</div>
                                        </div>

                                        <!-- Location Information -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Gedung <span
                                                        class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="text" id="edit_building_search"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="Cari gedung..." autocomplete="off" required>
                                                    <input type="hidden" name="building_id" id="edit_selected_building_id"
                                                        required>
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Gedung harus
                                                        dipilih</div>
                                                    <div id="edit_building_dropdown"
                                                        class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                        <div id="edit_building_loading" class="p-2 text-gray-500 text-center">
                                                            <svg class="animate-spin h-5 w-5 mx-auto"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                            <span>Memuat Gedung...</span>
                                                        </div>
                                                        <ul id="edit_building_list" class="py-1"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Ruangan <span
                                                        class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="text" id="edit_room_search"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="Pilih gedung terlebih dahulu" autocomplete="off" disabled
                                                        required>
                                                    <input type="hidden" name="room_id" id="edit_selected_room_id"
                                                        value="{{ $asset['room_id'] ?? '' }}" required>
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Ruangan harus
                                                        dipilih</div>
                                                    <div id="edit_room_dropdown"
                                                        class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                        <div id="edit_room_loading" class="p-2 text-gray-500 text-center">
                                                            <svg class="animate-spin h-5 w-5 mx-auto"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                            <span>Memuat ruangan...</span>
                                                        </div>
                                                        <ul id="edit_room_list" class="py-1"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Condition and Responsibility -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Kondisi</label>
                                                <select name="condition" id="edit_condition" required
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                                    <option value="good" {{ $asset['condition'] == 'good' ? 'selected' : '' }}>
                                                        Baik</option>
                                                    <option value="slightly damage" {{ $asset['condition'] == 'slightly damage' ? 'selected' : '' }}>Sedikit Rusak</option>
                                                    <option value="high damage" {{ $asset['condition'] == 'high damage' ? 'selected' : '' }}>Sangat Rusak</option>
                                                </select>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Kondisi harus
                                                    dipilih</div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Karyawan yang
                                                    Bertanggung Jawab</label>
                                                <div class="relative">
                                                    <input type="text" id="edit_user_search"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="Cari karyawan (nomor karyawan)..." autocomplete="off">
                                                    <input type="hidden" name="user_id" id="edit_selected_user_id"
                                                        value="{{ $asset['user_id'] ?? '' }}">
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Karyawan harus
                                                        dipilih</div>
                                                    <div id="edit_user_dropdown"
                                                        class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                        <div id="edit_user_loading" class="p-2 text-gray-500 text-center">
                                                            <svg class="animate-spin h-5 w-5 mx-auto"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                            <span> Memuat Pengguna...</span>
                                                        </div>
                                                        <ul id="edit_user_list" class="py-1"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Depreciation Fields Section -->
                                    <div id="edit_depreciation_fields"
                                        class="space-y-5 border rounded-lg p-5 border-dashed border-gray-300 {{ $asset['asset_master']['is_depreciable'] ? '' : 'hidden' }}">
                                        <h3 class="text-lg font-semibold text-[#213268] mb-3">Informasi Penyusutan</h3>

                                        <div class="mb-4 space-y-2">
                                            <label class="block text-base font-semibold text-[#666666] mb-2">Metode Penyusutan
                                                <span class="text-red-500">*</span></label>
                                            <select name="depreciation_method" id="edit_depreciation_method"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                required>
                                                <option value="Straight Line" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == 'Straight Line' ? 'selected' : '' }}>Garis Lurus</option>
                                                <option value="Declining Balance" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == 'Declining Balance' ? 'selected' : '' }}>Penyusutan Dua Kali</option>
                                                <option value="Double Declining Balance" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == 'Double Declining Balance' ? 'selected' : '' }}>Dua Kali Penyusutan</option>
                                                <option value="150% Declining Balance" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == '150% Declining Balance' ? 'selected' : '' }}>150% Penyusutan</option>
                                                <option value="Sum of the Year's Digits" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == "Sum of the Year's Digits" ? 'selected' : '' }}>Jumlah Tahun</option>
                                            </select>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Metode penyusutan harus
                                                dipilih</div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Biaya Akusisi
                                                    <span class="text-red-500">*</span></label>
                                                <input type="number" step="0.01" name="acquisition_cost"
                                                    id="edit_acquisition_cost"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="0.00"
                                                    value="{{ isset($asset['depreciation']) ? $asset['depreciation']['acquisition_cost'] : '' }}"
                                                    required>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Biaya akusisi harus
                                                    diisi</div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Nilai Sisa
                                                    <span class="text-red-500">*</span></label>
                                                <input type="number" step="0.01" name="salvage_value" id="edit_salvage_value"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="0.00"
                                                    value="{{ isset($asset['depreciation']) ? $asset['depreciation']['salvage_value'] : '' }}"
                                                    required>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Nilai sisa harus
                                                    diisi</div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Usia Aset
                                                    (bulan) <span class="text-red-500">*</span></label>
                                                <input type="number" name="asset_life_months" id="edit_asset_life_months"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    value="{{ isset($asset['depreciation']) ? $asset['depreciation']['asset_life_months'] : '' }}"
                                                    required>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Usia aset harus
                                                    diisi</div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Tanggal
                                                    Pengadaan <span class="text-red-500">*</span></label>
                                                <input type="date" name="date_acquired" id="edit_date_acquired"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    value="{{ isset($asset['depreciation']) ? $asset['depreciation']['date_acquired'] : '' }}"
                                                    required>
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal pengadaan
                                                    harus diisi</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit"
                                        class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                        Perbarui
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Checkout Asset Modal -->
    @if(hasPermission('asset:checkout'))
        <div id="checkoutAssetModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="checkoutAssetModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Pinjam Asset</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="checkoutAssetForm" method="POST" action="{{ route('asset.checkout') }}" data-no-loading>
                            @csrf
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Hidden asset ID field -->
                                    <input type="hidden" name="asset_id" value="{{ $asset['asset_id'] ?? '' }}">

                                    <!-- Check Out Date -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Tanggal Pinjam</label>
                                        <input type="date" name="checkout_date" required readonly
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268] bg-gray-100"
                                            value="{{ date('Y-m-d') }}">
                                    </div>

                                    <!-- Check Out To -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Pinjam Ke</label>
                                        <div class="flex items-center gap-8 mt-2">
                                            <div class="flex items-center">
                                                <input type="radio" id="employee" name="checkout_to_type" value="employee"
                                                    class="w-4 h-4 text-[#213268]" checked>
                                                <label for="employee"
                                                    class="ml-2 text-sm font-medium text-[#666666]">Karyawan</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="radio" id="location" name="checkout_to_type" value="location"
                                                    class="w-4 h-4 text-[#213268]">
                                                <label for="location"
                                                    class="ml-2 text-sm font-medium text-[#666666]">Ruangan</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Employee Dropdown (shown when Employee radio is selected) -->
                                    <div id="employeeDropdown" class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Pilih Karyawan</label>
                                        <div class="relative">
                                            <input type="text" id="checkout_user_search"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                                placeholder="Cari karyawan (nomor karyawan)..." autocomplete="off">
                                            <input type="hidden" name="assigned_to" id="checkout_selected_user_id">
                                            <div id="checkout_user_dropdown"
                                                class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                <div id="checkout_user_loading" class="p-2 text-gray-500 text-center">
                                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                            stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                    <span>Memuat Pengguna...</span>
                                                </div>
                                                <ul id="checkout_user_list" class="py-1"></ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location Dropdown (hidden by default) -->
                                    <div id="locationDropdown" class="space-y-4 hidden">
                                        <!-- Building Search Dropdown -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-medium text-[#666666]">Pilih Gedung</label>
                                            <div class="relative">
                                                <input type="text" id="pinjam_building_search"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="Cari gedung..." autocomplete="off">
                                                <input type="hidden" name="building_id" id="pinjam_selected_building_id">
                                                <div id="pinjam_building_dropdown"
                                                    class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                    <div id="pinjam_building_loading" class="p-2 text-gray-500 text-center">
                                                        <svg class="animate-spin h-5 w-5 mx-auto"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                        <span>Memuat Gedung...</span>
                                                    </div>
                                                    <ul id="pinjam_building_list" class="py-1"></ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Room Search Dropdown -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-medium text-[#666666]">Pilih Ruangan</label>
                                            <div class="relative">
                                                <input type="text" id="pinjam_room_search"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="Pilih gedung terlebih dahulu" autocomplete="off" disabled>
                                                <input type="hidden" name="room_id" id="pinjam_selected_room_id">
                                                <div id="pinjam_room_dropdown"
                                                    class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                    <div id="pinjam_room_loading" class="p-2 text-gray-500 text-center">
                                                        <svg class="animate-spin h-5 w-5 mx-auto"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                        <span>Memuat Ruangan...</span>
                                                    </div>
                                                    <ul id="pinjam_room_list" class="py-1"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Catatan</label>
                                        <textarea name="checkout_notes" rows="3"
                                            class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                            placeholder="Masukkan Catatan"></textarea>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" id="submitCheckout"
                                        class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                        Pinjam
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Checkin Asset Modal -->
    @if(hasPermission('asset:checkout'))
        <div id="checkinAssetModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="checkinAssetModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Kembalikan Asset</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="checkinAssetForm" method="POST" action="{{ route('asset.checkin') }}" data-no-loading>
                            @csrf
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Hidden asset ID field -->
                                    <input type="hidden" name="asset_id" value="{{ $asset['asset_id'] ?? '' }}">

                                    <!-- Return Date -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Tanggal Kembali</label>
                                        <input type="date" name="return_date" required readonly
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268] bg-gray-100"
                                            value="{{ date('Y-m-d') }}">
                                    </div>

                                    <!-- Asset Condition -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Kondisi Asset</label>
                                        <select name="condition" id="return_condition" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
                                            <option value="GOOD">Baik</option>
                                            <option value="DAMAGED">Rusak</option>
                                            <option value="NEEDS_REPAIR">Perlu Perbaikan</option>
                                        </select>
                                    </div>

                                    <!-- Notes -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Catatan Kembali</label>
                                        <textarea name="return_notes" rows="3" required
                                            class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                            placeholder="Masukkan detail tentang pengembalian"></textarea>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" id="submitCheckin"
                                        class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                        Kembalikan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Report Asset as Lost Modal -->
    @if(hasPermission('asset:report-loss'))
        <div id="reportLostModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="reportLostModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Laporan Asset Hilang</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="reportLostForm" method="POST" action="{{ route('asset.lost') }}" data-no-loading>
                            @csrf
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Hidden asset ID field -->
                                    <input type="hidden" name="asset_id" value="{{ $asset['asset_id'] ?? '' }}">

                                    <!-- Loss Date -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Tanggal Hilang</label>
                                        <input type="date" name="loss_date" required readonly
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268] bg-gray-100"
                                            value="{{ date('Y-m-d') }}">
                                    </div>

                                    <!-- Loss Reason -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Alasan Hilang</label>
                                        <textarea name="loss_reason" rows="3" required
                                            class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                            placeholder="Masukkan detail tentang alasan asset hilang"></textarea>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" id="submitLostReport"
                                        class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                        Laporan Hilang
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Found Asset Modal -->
    @if(hasPermission('asset:report-found'))
        <div id="foundAssetModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="foundAssetModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Laporan Asset Ditemukan</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="foundAssetForm" method="POST" action="{{ route('asset.found') }}" data-no-loading>
                            @csrf
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Hidden asset ID field -->
                                    <input type="hidden" name="asset_id" value="{{ $asset['asset_id'] ?? '' }}">

                                    <!-- Notes -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Catatan Ditemukan</label>
                                        <textarea name="found_notes" rows="3" required
                                            class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                            placeholder="Detail tempat dan cara asset ditemukan"></textarea>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" id="submitFound"
                                        class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                        Laporan Ditemukan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Dispose Asset Modal -->
    @if(hasPermission('asset:dispose'))
        <div id="disposeAssetModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="disposeAssetModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Penghapusan Asset</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="disposeAssetForm" method="POST" action="{{ route('asset.dispose') }}" data-no-loading>
                            @csrf
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Hidden asset ID field -->
                                    <input type="hidden" name="asset_id" value="{{ $asset['asset_id'] ?? '' }}">
                                    <input type="hidden" name="transfer_type" value="DISPOSAL">

                                    <!-- Dispose Date -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Tanggal Penghapusan</label>
                                        <input type="date" name="dispose_date" required readonly
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268] bg-gray-100"
                                            value="{{ date('Y-m-d') }}">
                                    </div>

                                    <!-- Disposal Method -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Metode Penghapusan</label>
                                        <select name="disposal_method" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
                                            <option value="SOLD">Terjual</option>
                                            <option value="DONATED">Donasi</option>
                                            <option value="RECYCLED">Daur Ulang</option>
                                            <option value="DESTROYED">Hancurkan</option>
                                            <option value="OTHER">Lainnya</option>
                                        </select>
                                    </div>

                                    <!-- Disposal Reason -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Alasan Penghapusan</label>
                                        <textarea name="disposal_reason" rows="3" required
                                            class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                            placeholder="Masukkan alasan penghapusan"></textarea>
                                    </div>

                                    <!-- Additional Notes -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Catatan Tambahan</label>
                                        <textarea name="disposal_notes" rows="3"
                                            class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                            placeholder="Masukkan informasi tambahan tentang penghapusan"></textarea>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" id="submitDispose"
                                        class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                        Penghapusan Asset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        /* Flip card styling */
        .flip-card-container {
            perspective: 1000px;
        }

        .flip-card {
            position: relative;
            transform-style: preserve-3d;
        }

        .flip-card.flipped {
            transform: rotateY(180deg);
        }

        .flip-card-front,
        .flip-card-back {
            position: absolute;
            backface-visibility: hidden;
        }

        .flip-card-back {
            transform: rotateY(180deg);
        }

        /* Custom scrollbar styles */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* For Firefox */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Permission-based initialization
            const assetId = '{{ $asset['asset_id'] ?? "" }}';
            const currentStatus = '{{ $asset['current_status'] ?? "" }}';

            // Function to initialize buttons based on permissions
            function initializeButtons() {
                @if(!hasPermission('asset:edit'))
                    // Hide edit buttons if user doesn't have edit permission
                    document.querySelectorAll('#editAssetBtn').forEach(btn => {
                        if (btn) btn.style.display = 'none';
                    });
                @endif

                @if(!hasPermission('asset:checkout'))
                    // Hide checkout buttons if user doesn't have checkout permission
                    document.querySelectorAll('#checkoutAssetBtn').forEach(btn => {
                        if (btn) btn.style.display = 'none';
                    });
                @endif

                @if(!hasPermission('asset:checkout'))
                    // Hide checkin buttons if user doesn't have checkout permission
                    document.querySelectorAll('#checkinAssetBtn').forEach(btn => {
                        if (btn) btn.style.display = 'none';
                    });
                @endif

                @if(!hasPermission('asset:dispose'))
                    // Hide dispose buttons if user doesn't have dispose permission
                    document.querySelectorAll('#disposeAssetBtn').forEach(btn => {
                        if (btn) btn.style.display = 'none';
                    });
                @endif

                @if(!hasPermission('asset:report-loss'))
                    // Hide lost buttons if user doesn't have report-loss permission
                    document.querySelectorAll('#lostAssetBtn').forEach(btn => {
                        if (btn) btn.style.display = 'none';
                    });
                @endif

                @if(!hasPermission('asset:report-found'))
                    // Hide found buttons if user doesn't have report-found permission
                    document.querySelectorAll('#foundAssetBtn').forEach(btn => {
                        if (btn) btn.style.display = 'none';
                    });
                @endif
                }

            // Call initialization function
            initializeButtons();

            // Function to show toast notifications
            function showToast(message, type = 'success') {
                // Create the notification element
                const notification = document.createElement('div');
                notification.id = type + 'Notification' + Date.now(); // Unique ID to allow multiple notifications
                notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
                notification.role = 'alert';

                // Check if message contains HTML
                const hasHTML = /<[a-z][\s\S]*>/i.test(message);

                if (type === 'success') {
                    notification.classList.add('bg-green-100', 'border-l-4', 'border-green-500', 'text-green-700');
                    notification.innerHTML = `
                            <div class="flex items-start">
                                <div class="py-1">
                                    <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold">Berhasil!</p>
                                    <div>${message}</div>
                                </div>
                                <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                            </div>
                        `;
                } else {
                    notification.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700', 'overflow-auto');

                    // Structure for the notification
                    const wrapper = document.createElement('div');
                    wrapper.className = 'flex items-start';

                    // Icon container
                    const iconContainer = document.createElement('div');
                    iconContainer.className = 'py-1 flex-shrink-0';
                    iconContainer.innerHTML = `
                            <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        `;

                    // Content container
                    const contentContainer = document.createElement('div');
                    contentContainer.className = 'flex-grow max-w-xs sm:max-w-sm md:max-w-md';

                    // Title
                    const title = document.createElement('p');
                    title.className = 'font-bold';
                    title.textContent = 'Error!';
                    contentContainer.appendChild(title);

                    // Message container
                    const messageContainer = document.createElement('div');
                    messageContainer.className = 'error-message';

                    // Handle HTML content
                    if (hasHTML) {
                        messageContainer.innerHTML = message;
                    } else {
                        messageContainer.textContent = message;
                    }

                    contentContainer.appendChild(messageContainer);

                    // Close button
                    const closeBtn = document.createElement('span');
                    closeBtn.className = 'ml-4 cursor-pointer flex-shrink-0';
                    closeBtn.textContent = '×';
                    closeBtn.onclick = function () {
                        notification.remove();
                    };

                    // Assemble the notification
                    wrapper.appendChild(iconContainer);
                    wrapper.appendChild(contentContainer);
                    wrapper.appendChild(closeBtn);
                    notification.appendChild(wrapper);
                }

                // Add to document
                document.body.appendChild(notification);

                // Auto-remove notification after 5 seconds
                setTimeout(() => {
                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => notification.remove(), 500);
                }, 5000);
            }

            // Add slide-in animation and styling for error messages to CSS
            document.head.insertAdjacentHTML('beforeend', `
                    <style>
                        @keyframes slideInRight {
                            from { transform: translateX(100%); }
                            to { transform: translateX(0); }
                        }
                        .animate-slide-in-right {
                            animation: slideInRight 0.3s ease-out forwards;
                        }

                        /* Styling for error messages with HTML content */
                        .error-message ul {
                            margin-top: 0.5rem;
                            padding-left: 1.5rem;
                        }
                        .error-message ul li {
                            margin-bottom: 0.25rem;
                        }
                        .error-message ul li:last-child {
                            margin-bottom: 0;
                        }
                    </style>
                `);

            // Show toast notifications for session messages on page load
            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif

                @if(session('error'))
                    showToast("{{ session('error') }}", 'error');
                @endif

            // Initialize cache variables
            window.usersCache =[];
            window.roomsCache = [];
            window.buildingsCache = [];
            window.assetMastersCache = [];

            // Debounce utility function
            function debounce(func, wait) {
                let timeout;
                return function (...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }
            window.debounce = debounce;

            // Modal functionality
            function openModal(modal, content) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                    content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                }, 10);
            }

            // Function to close modal with animation and reset form fields
            function closeModal(modal, content) {
                if (!modal || !content) return;

                // Get modal ID to determine which reset function to call
                const modalId = modal.id;

                // Reset forms inside the modal
                const forms = modal.querySelectorAll('form');
                forms.forEach(form => {
                    // Call specific reset function based on modal type
                    switch (modalId) {
                        case 'editAssetModal':
                            resetEditAssetForm(form);
                            break;
                        case 'checkoutAssetModal':
                            resetCheckoutForm(form);
                            break;
                        case 'checkinAssetModal':
                            resetCheckinForm(form);
                            break;
                        case 'reportLostModal':
                            resetLostForm(form);
                            break;
                        case 'foundAssetModal':
                            resetFoundForm(form);
                            break;
                        case 'disposeAssetModal':
                            resetDisposeForm(form);
                            break;
                        default:
                            resetGenericForm(form);
                    }
                });

                // Animate closing
                content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            // Generic form reset
            function resetGenericForm(form) {
                if (!form) return;

                form.reset();

                // Reset hidden inputs that might not be affected by form.reset()
                const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
                hiddenInputs.forEach(input => {
                    // Keep asset ID inputs
                    if (!input.name.includes('asset_id')) {
                        input.value = '';
                    }
                });

                // Reset all text inputs
                const textInputs = form.querySelectorAll('input[type="text"], input[type="search"]');
                textInputs.forEach(input => {
                    input.value = '';
                });

                // Reset select elements
                const selects = form.querySelectorAll('select');
                selects.forEach(select => {
                    if (select.options.length > 0) {
                        select.selectedIndex = 0;
                    }
                });

                // Hide any open dropdowns
                const dropdowns = form.querySelectorAll('[id$="_dropdown"]');
                dropdowns.forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });

                // Enable any disabled submit buttons
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                }

                // Reset error states
                const errorFields = form.querySelectorAll('.error, .border-red-500');
                errorFields.forEach(field => {
                    field.classList.remove('error', 'border-red-500');
                });

                // Hide error messages
                const errorMessages = form.querySelectorAll('.error-message');
                errorMessages.forEach(message => {
                    message.textContent = '';
                    message.classList.add('hidden');
                });
            }

            // Edit Asset form reset
            function resetEditAssetForm(form) {
                resetGenericForm(form);

                // Reset depreciation fields if present
                const depreciationFields = form.querySelector('#edit_depreciation_fields');
                if (depreciationFields) {
                    depreciationFields.classList.add('hidden');
                    const depInputs = depreciationFields.querySelectorAll('input, select');
                    depInputs.forEach(input => {
                        input.disabled = true;
                        input.required = false;
                        if (input.tagName === 'INPUT') {
                            input.value = '';
                        } else if (input.tagName === 'SELECT' && input.options.length > 0) {
                            input.selectedIndex = 0;
                        }
                    });
                }
            }

            // Checkout form reset
            function resetCheckoutForm(form) {
                resetGenericForm(form);

                // Reset checkout-specific elements
                document.getElementById('employeeDropdown').classList.remove('hidden');
                document.getElementById('locationDropdown').classList.add('hidden');

                // Set current date
                const dateField = form.querySelector('input[name="checkout_date"]');
                if (dateField) {
                    dateField.value = new Date().toISOString().split('T')[0];
                }

                // Reset radio buttons
                const employeeRadio = document.getElementById('employee');
                if (employeeRadio) {
                    employeeRadio.checked = true;
                }
            }

            // Check-in form reset
            function resetCheckinForm(form) {
                resetGenericForm(form);

                // Set current date for return date
                const dateField = form.querySelector('input[name="return_date"]');
                if (dateField) {
                    dateField.value = new Date().toISOString().split('T')[0];
                }
            }

            // Lost form reset
            function resetLostForm(form) {
                resetGenericForm(form);

                // Set current date for loss date
                const dateField = form.querySelector('input[name="loss_date"]');
                if (dateField) {
                    dateField.value = new Date().toISOString().split('T')[0];
                }
            }

            // Found form reset
            function resetFoundForm(form) {
                resetGenericForm(form);
            }

            // Dispose form reset
            function resetDisposeForm(form) {
                resetGenericForm(form);

                // Set current date for dispose date
                const dateField = form.querySelector('input[name="dispose_date"]');
                if (dateField) {
                    dateField.value = new Date().toISOString().split('T')[0];
                }
            }

            // Common fetch handler
            function fetchWithAuth(url, method, data, successCallback, errorCallback) {
                const options = {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                };

                if (data) {
                    options.headers['Content-Type'] = 'application/json';
                    options.body = JSON.stringify(data);
                }

                fetch(url, options)
                    .then(response => {
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json().then(data => {
                                if (data.success === true) {
                                    successCallback(data);
                                } else if (data.message &&
                                    (data.message.toLowerCase().includes('success') ||
                                        data.message.toLowerCase().includes('successfully'))) {
                                    data.success = true;
                                    successCallback(data);
                                } else {
                                    errorCallback(data.message || 'Operasi gagal');
                                }
                            });
                        } else {
                            return response.text().then(text => {
                                console.error('Received non-JSON response:', text);
                                showToast('Terjadi kesalahan pada server', 'error');
                                errorCallback('Terjadi kesalahan pada server');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan, silakan coba lagi', 'error');
                        errorCallback('Terjadi kesalahan, silakan coba lagi');
                    });
            }

            // Toggle depreciation fields
            function toggleDepreciationFields(depreciationFields, isVisible) {
                if (!depreciationFields) return;

                depreciationFields.classList.toggle('hidden', !isVisible);

                const inputs = depreciationFields.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.disabled = !isVisible;
                    input.required = isVisible;
                    input.classList.toggle('bg-gray-100', !isVisible);
                });
            }

            // Helper to set dropdown values
            function setSelectValue(selectId, value) {
                const select = document.getElementById(selectId);
                if (!select || value === undefined || value === null) return;

                const valueStr = String(value);
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].value === valueStr) {
                        select.selectedIndex = i;
                        return;
                    }
                }
            }

            // Create dropdown item
            function createDropdownItem(text, className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer') {
                const li = document.createElement('li');
                li.className = className;
                li.textContent = text;
                return li;
            }

            // Generic dropdown initialization
            function initDropdown(searchInput, dropdown, list, onSearch) {
                if (!searchInput || !dropdown || !list) return;

                // Show dropdown on focus
                searchInput.addEventListener('focus', function () {
                    dropdown.classList.remove('hidden');
                    onSearch(this.value);
                });

                // Filter on input with debounce
                searchInput.addEventListener('input', debounce(function (e) {
                    onSearch(e.target.value);
                }, 300));

                // Hide dropdown when clicking outside
                document.addEventListener('click', function (e) {
                    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }

            // Initialize search components with a unified approach
            function initSearchComponents() {
                // Building search for edit asset
                initDropdown(
                    document.getElementById('edit_building_search'),
                    document.getElementById('edit_building_dropdown'),
                    document.getElementById('edit_building_list'),
                    function (searchTerm) {
                        loadBuildings(
                            searchTerm,
                            document.getElementById('edit_building_list'),
                            document.getElementById('edit_building_loading'),
                            document.getElementById('edit_selected_building_id'),
                            document.getElementById('edit_building_search'),
                            document.getElementById('edit_building_dropdown'),
                            document.getElementById('edit_room_search')
                        );
                    }
                );

                // Room search for edit asset - now depends on building selection first
                initDropdown(
                    document.getElementById('edit_room_search'),
                    document.getElementById('edit_room_dropdown'),
                    document.getElementById('edit_room_list'),
                    function (searchTerm) {
                        const buildingId = document.getElementById('edit_selected_building_id').value;
                        if (buildingId) {
                            loadRoomsForBuilding(
                                searchTerm,
                                buildingId,
                                document.getElementById('edit_room_list'),
                                document.getElementById('edit_room_loading'),
                                document.getElementById('edit_selected_room_id'),
                                document.getElementById('edit_room_search'),
                                document.getElementById('edit_room_dropdown')
                            );
                        } else {
                            // If no building selected, show message
                            const roomList = document.getElementById('edit_room_list');
                            if (roomList) {
                                roomList.innerHTML = '';
                                roomList.appendChild(createDropdownItem('Pilih gedung terlebih dahulu', 'px-4 py-2 text-gray-500 italic'));
                            }
                        }
                    }
                );

                // User search
                initUserSearch(
                    document.getElementById('edit_user_search'),
                    document.getElementById('edit_user_dropdown'),
                    document.getElementById('edit_user_list'),
                    document.getElementById('edit_user_loading'),
                    document.getElementById('edit_selected_user_id')
                );

                // Checkout modal user search
                initUserSearch(
                    document.getElementById('checkout_user_search'),
                    document.getElementById('checkout_user_dropdown'),
                    document.getElementById('checkout_user_list'),
                    document.getElementById('checkout_user_loading'),
                    document.getElementById('checkout_selected_user_id')
                );

                // Asset master search
                initAssetMasterSearch(
                    document.getElementById('edit_asset_master_search'),
                    document.getElementById('edit_asset_master_dropdown'),
                    document.getElementById('edit_asset_master_list'),
                    document.getElementById('edit_asset_master_loading'),
                    document.getElementById('edit_selected_asset_master_id'),
                    document.getElementById('edit_selected_is_depreciable'),
                    document.getElementById('edit_depreciation_fields')
                );
            }

            // Unified dropdown initialization functions
            function initRoomSearch(searchInput, dropdown, roomList, loadingIndicator, selectedRoomId, selectedRoomName, roomDisplay) {
                initDropdown(searchInput, dropdown, roomList, function (searchTerm) {
                    loadRooms(searchTerm, roomList, loadingIndicator, selectedRoomId, selectedRoomName, roomDisplay, searchInput, dropdown);
                });
            }

            function initUserSearch(searchInput, dropdown, userList, loadingIndicator, selectedUserId) {
                initDropdown(searchInput, dropdown, userList, function (searchTerm) {
                    loadUsers(searchTerm, userList, loadingIndicator, selectedUserId, searchInput, dropdown);
                });
            }

            function initAssetMasterSearch(searchInput, dropdown, assetMasterList, loadingIndicator, selectedAssetMasterId, selectedIsDepreciable, depreciationFields) {
                initDropdown(searchInput, dropdown, assetMasterList, function (searchTerm) {
                    loadAssetMasters(searchTerm, assetMasterList, loadingIndicator, selectedAssetMasterId, selectedIsDepreciable, depreciationFields, searchInput, dropdown);
                });
            }

            // Data loading functions
            async function loadRooms(searchTerm, roomList, loadingIndicator, selectedRoomId, selectedRoomName, roomDisplay, searchInput, dropdown) {
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                roomList.innerHTML = '';

                try {
                    const response = await fetch(`{{ route('rooms') }}?building_id=&search=${encodeURIComponent(searchTerm || '')}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to fetch rooms');

                    const result = await response.json();
                    let rooms = result.rooms || result.data || [];

                    if (rooms.length === 0) {
                        roomList.appendChild(createDropdownItem('No rooms found', 'px-4 py-2 text-gray-500 italic'));
                    } else {
                        rooms.forEach(item => {
                            let buildingName = 'Unknown Building';

                            if (item.building && item.building.building_name) {
                                buildingName = item.building.building_name;
                            } else if (item.building_name) {
                                buildingName = item.building_name;
                            }

                            const roomName = `${item.room_name} (${buildingName})`;
                            const li = createDropdownItem(roomName);

                            li.setAttribute('data-id', item.room_id);
                            li.setAttribute('data-name', roomName);

                            li.addEventListener('click', function () {
                                selectedRoomId.value = this.getAttribute('data-id');
                                if (selectedRoomName) selectedRoomName.value = this.getAttribute('data-name');
                                if (roomDisplay) roomDisplay.textContent = this.getAttribute('data-name');
                                searchInput.value = this.getAttribute('data-name');
                                dropdown.classList.add('hidden');
                            });

                            roomList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading rooms:', error);
                    roomList.appendChild(createDropdownItem('Error loading rooms', 'px-4 py-2 text-red-500'));
                } finally {
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }

            async function loadUsers(searchTerm, userList, loadingIndicator, selectedUserId, searchInput, dropdown) {
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                userList.innerHTML = '';

                try {
                    const response = await fetch(`{{ route('user') }}?search=${encodeURIComponent(searchTerm || '')}&status=active`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to fetch users');

                    const result = await response.json();
                    let users = result.users || result.data || [];

                    if (users.length === 0) {
                        userList.appendChild(createDropdownItem('No users found', 'px-4 py-2 text-gray-500 italic'));
                    } else {
                        users.forEach(user => {
                            let displayText = '';
                            if (user.employee_number) {
                                displayText = user.employee_number;
                                if (user.name) displayText += ` - ${user.name}`;
                            } else if (user.name) {
                                displayText = user.name;
                            } else {
                                displayText = `User ID: ${user.user_id}`;
                            }

                            const li = createDropdownItem(displayText);
                            li.setAttribute('data-id', user.user_id);
                            li.setAttribute('data-name', displayText);

                            li.addEventListener('click', function () {
                                selectedUserId.value = this.getAttribute('data-id');
                                searchInput.value = this.getAttribute('data-name');
                                dropdown.classList.add('hidden');
                            });

                            userList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading users:', error);
                    userList.appendChild(createDropdownItem('Error loading users', 'px-4 py-2 text-red-500'));
                } finally {
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }

            async function loadAssetMasters(searchTerm, assetMasterList, loadingIndicator, selectedAssetMasterId, selectedIsDepreciable, depreciationFields, searchInput, dropdown) {
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                assetMasterList.innerHTML = '';

                try {
                    const response = await fetch(`{{ route('asset-master') }}?search=${encodeURIComponent(searchTerm || '')}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to fetch asset masters');

                    const result = await response.json();
                    let assetMasters = result.masterAssets || result.data || [];

                    if (assetMasters.length === 0) {
                        assetMasterList.appendChild(createDropdownItem('No asset masters found', 'px-4 py-2 text-gray-500 italic'));
                    } else {
                        assetMasters.forEach(item => {
                            const assetMasterName = item.asset_name || 'Unknown';
                            const li = createDropdownItem(assetMasterName);

                            const isDepreciable = item.is_depreciable === true;
                            li.setAttribute('data-id', item.asset_master_id);
                            li.setAttribute('data-name', assetMasterName);
                            li.setAttribute('data-depreciable', isDepreciable);

                            li.addEventListener('click', function () {
                                selectedAssetMasterId.value = this.getAttribute('data-id');
                                searchInput.value = this.getAttribute('data-name');

                                const isDepreciable = this.getAttribute('data-depreciable') === 'true';
                                selectedIsDepreciable.setAttribute('value', isDepreciable.toString());

                                const event = new Event('change');
                                selectedIsDepreciable.dispatchEvent(event);

                                toggleDepreciationFields(depreciationFields, isDepreciable);
                                dropdown.classList.add('hidden');
                            });

                            assetMasterList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading asset masters:', error);
                    assetMasterList.appendChild(createDropdownItem('Error loading asset masters', 'px-4 py-2 text-red-500'));
                } finally {
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }

            // Initialize UI elements and event handlers
            const flipCard = document.querySelector('.flip-card');
            const flipBtns = document.querySelectorAll('.flip-btn');

            flipBtns.forEach(btn => {
                btn.addEventListener('click', () => flipCard.classList.toggle('flipped'));
            });

            // Initialize tabs
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            if (tabPanes.length > 0) {
                tabPanes.forEach(pane => pane.classList.add('hidden'));
                const firstTab = document.getElementById(tabButtons[0].getAttribute('data-tab'));
                if (firstTab) firstTab.classList.remove('hidden');
            }

            tabButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    tabButtons.forEach(button => {
                        button.classList.remove('active', 'text-[#213268]', 'border-b-2', 'border-[#213268]');
                        button.classList.add('text-gray-500');
                    });

                    this.classList.remove('text-gray-500');
                    this.classList.add('active', 'text-[#213268]', 'border-b-2', 'border-[#213268]');

                    tabPanes.forEach(pane => pane.classList.add('hidden'));

                    const tabName = this.getAttribute('data-tab');
                    const selectedTab = document.getElementById(tabName);
                    if (selectedTab) selectedTab.classList.remove('hidden');
                });
            });

            // Setup modals
            const modals = {
                edit: {
                    btn: document.getElementById('editAssetBtn'),
                    modal: document.getElementById('editAssetModal'),
                    content: document.getElementById('editAssetModalContent'),
                    form: document.getElementById('editAssetForm')
                },
                checkout: {
                    btn: document.getElementById('checkoutAssetBtn'),
                    modal: document.getElementById('checkoutAssetModal'),
                    content: document.getElementById('checkoutAssetModalContent'),
                    form: document.getElementById('checkoutAssetForm')
                },
                checkin: {
                    btn: document.getElementById('checkinAssetBtn'),
                    modal: document.getElementById('checkinAssetModal'),
                    content: document.getElementById('checkinAssetModalContent'),
                    form: document.getElementById('checkinAssetForm')
                },
                lost: {
                    btn: document.getElementById('lostAssetBtn'),
                    modal: document.getElementById('reportLostModal'),
                    content: document.getElementById('reportLostModalContent'),
                    form: document.getElementById('reportLostForm')
                },
                found: {
                    btn: document.getElementById('foundAssetBtn'),
                    modal: document.getElementById('foundAssetModal'),
                    content: document.getElementById('foundAssetModalContent'),
                    form: document.getElementById('foundAssetForm')
                },
                dispose: {
                    btn: document.getElementById('disposeAssetBtn'),
                    modal: document.getElementById('disposeAssetModal'),
                    content: document.getElementById('disposeAssetModalContent'),
                    form: document.getElementById('disposeAssetForm')
                }
            };

            // Close modal event handlers
            const closeButtons = document.querySelectorAll('.close-modal');
            closeButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const modal = this.closest('[id$="Modal"]');
                    const content = modal.querySelector('[id$="ModalContent"]');
                    if (modal && content) closeModal(modal, content);
                });
            });

            // Initialize each modal
            Object.keys(modals).forEach(key => {
                const modalObj = modals[key];
                if (modalObj.btn && modalObj.modal && modalObj.content) {
                    modalObj.btn.addEventListener('click', function (e) {
                        if (e.preventDefault) e.preventDefault();

                        if (key === 'edit') {
                            // Special handling for edit modal - fetch data first
                            const assetId = '{{ $asset["asset_id"] ?? "" }}';
                            if (!assetId) {
                                console.error('Asset ID not found');
                                return;
                            }

                            modalObj.btn.classList.add('opacity-50', 'pointer-events-none');

                            fetch(`{{ url('/assets') }}/${assetId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                                .then(response => response.json())
                                .then(data => {
                                    modalObj.btn.classList.remove('opacity-50', 'pointer-events-none');

                                    if (data.error) {
                                        console.error('Error fetching asset:', data.error);
                                        return;
                                    }

                                    if (!data.success) {
                                        console.error('Failed to fetch asset data:', data.message);
                                        return;
                                    }

                                    setupWithData(data.data);
                                    openModal(modalObj.modal, modalObj.content);
                                })
                                .catch(error => {
                                    modalObj.btn.classList.remove('opacity-50', 'pointer-events-none');
                                    console.error('Error fetching asset:', error);
                                });
                        } else {
                            // Normal modal opening
                            openModal(modalObj.modal, modalObj.content);
                        }
                    });

                    // Close on outside click
                    modalObj.modal.addEventListener('click', function (e) {
                        if (e.target === this) {
                            closeModal(modalObj.modal, modalObj.content);
                        }
                    });

                    // Set up form submission handlers
                    if (modalObj.form) {
                        // Set the correct action URLs for each form type
                        if (modalObj.form.id === 'checkoutAssetForm') {
                            modalObj.form.action = "{{ route('asset.checkout') }}";
                        } else if (modalObj.form.id === 'checkinAssetForm') {
                            modalObj.form.action = "{{ route('asset.checkin') }}";
                        } else if (modalObj.form.id === 'reportLostForm') {
                            modalObj.form.action = "{{ route('asset.lost') }}";
                        } else if (modalObj.form.id === 'foundAssetForm') {
                            modalObj.form.action = "{{ route('asset.found') }}";
                        } else if (modalObj.form.id === 'disposeAssetForm') {
                            modalObj.form.action = "{{ route('asset.dispose') }}";
                        }

                        // Add a close button event handler after form submission
                        const closeBtn = modalObj.modal.querySelector('.close-modal');
                        if (closeBtn) {
                            closeBtn.addEventListener('click', function () {
                                closeModal(modalObj.modal, modalObj.content);
                            });
                        }
                    }
                }
            });

            // Handle depreciation toggle
            const depreciableToggle = document.getElementById('edit_is_depreciable');
            const depreciationFields = document.getElementById('edit_depreciation_fields');

            if (depreciableToggle && depreciationFields) {
                depreciableToggle.addEventListener('change', function () {
                    const isChecked = this.checked;
                    const statusText = document.querySelector('.depreciation-status');

                    if (statusText) statusText.textContent = isChecked ? 'Yes' : 'No';
                    toggleDepreciationFields(depreciationFields, isChecked);

                    // If depreciation is being enabled, set acquisition cost to match purchase cost
                    if (isChecked) {
                        const purchaseCost = document.getElementById('edit_purchase_cost').value || '0';
                        document.getElementById('edit_acquisition_cost').value = purchaseCost;

                        const purchaseDate = document.getElementById('edit_purchase_date').value;
                        if (purchaseDate) {
                            document.getElementById('edit_date_acquired').value = purchaseDate;
                        }
                    }
                });
            }

            // Update acquisition cost when purchase cost changes
            const purchaseCostInput = document.getElementById('edit_purchase_cost');
            if (purchaseCostInput) {
                purchaseCostInput.addEventListener('input', function () {
                    if (document.getElementById('edit_is_depreciable').checked) {
                        document.getElementById('edit_acquisition_cost').value = this.value;
                    }
                });
            }

            // Initialize all components
            initSearchComponents();

            // Employee/location toggle in checkout modal
            const employeeRadio = document.getElementById('employee');
            const locationRadio = document.getElementById('location');
            const employeeDropdown = document.getElementById('employeeDropdown');
            const locationDropdown = document.getElementById('locationDropdown');

            if (employeeRadio && locationRadio && employeeDropdown && locationDropdown) {
                employeeRadio.addEventListener('change', function () {
                    if (this.checked) {
                        employeeDropdown.classList.remove('hidden');
                        locationDropdown.classList.add('hidden');
                        document.getElementById('checkout_selected_user_id').setAttribute('required', '');
                        document.getElementById('pinjam_selected_room_id').removeAttribute('required');
                    }
                });

                locationRadio.addEventListener('change', function () {
                    if (this.checked) {
                        employeeDropdown.classList.add('hidden');
                        locationDropdown.classList.remove('hidden');
                        document.getElementById('pinjam_selected_room_id').setAttribute('required', '');
                        document.getElementById('checkout_selected_user_id').removeAttribute('required');
                    }
                });
            }

            // Set up depreciation fields toggle based on asset_master data
            const editDepreciationFields = document.getElementById('edit_depreciation_fields');
            const isDepreciable = '{{ $asset["asset_master"]["is_depreciable"] ?? false }}' === '1';
            if (editDepreciationFields) {
                toggleDepreciationFields(editDepreciationFields, isDepreciable);
            }

            // Set current date for checkout form
            const checkoutDateInput = document.querySelector('input[name="checkout_date"]');
            if (checkoutDateInput) {
                checkoutDateInput.value = new Date().toISOString().split('T')[0];
            }

            // Global function to setup form with asset data
            window.setupWithData = function (asset) {
                const form = document.getElementById('editAssetForm');
                if (!form) {
                    console.error('Edit asset form not found');
                    return;
                }

                form.action = "{{ url('/assets') }}/" + asset.asset_id;
                form.reset();

                // Fill basic inputs
                document.getElementById('edit_serial_number').value = asset.serial_number || '';
                document.getElementById('edit_purchase_cost').value = asset.purchase_cost || '';

                // Handle dates
                if (asset.purchase_date) {
                    document.getElementById('edit_purchase_date').value = asset.purchase_date.split(' ')[0];
                }

                if (asset.warranty_end_date) {
                    document.getElementById('edit_warranty_end_date').value = asset.warranty_end_date.split(' ')[0];
                }

                setSelectValue('edit_condition', asset.condition);

                // Set asset master information
                const assetMasterId = asset.asset_master_id || (asset.asset_master && asset.asset_master.asset_master_id);
                const assetMasterName = asset.asset_master_name || (asset.asset_master && asset.asset_master.asset_name) || '';

                if (assetMasterId) {
                    document.getElementById('edit_selected_asset_master_id').value = assetMasterId;
                    document.getElementById('edit_asset_master_search').value = assetMasterName;

                    const isDepreciable = asset.asset_master && asset.asset_master.is_depreciable === true;
                    document.getElementById('edit_selected_is_depreciable').value = isDepreciable.toString();

                    const editDepreciationFields = document.getElementById('edit_depreciation_fields');
                    if (editDepreciationFields) {
                        toggleDepreciationFields(editDepreciationFields, isDepreciable);
                    }
                }

                // Set building and room information
                // Directly set the building name from the current displayed asset data
                document.getElementById('edit_selected_building_id').value = '{{ $asset["building_id"] ?? "" }}';
                document.getElementById('edit_building_search').value = '{{ $asset["building_name"] ?? "" }}';

                // Enable room search since we have a building
                const roomSearch = document.getElementById('edit_room_search');
                if (roomSearch) {
                    roomSearch.disabled = false;
                    roomSearch.placeholder = "Cari ruangan...";
                }

                // Set room information
                document.getElementById('edit_selected_room_id').value = '{{ $asset["room_id"] ?? "" }}';
                document.getElementById('edit_room_search').value = '{{ $asset["room_name"] ?? "" }}';

                // Set user information
                if (asset.user_id) {
                    document.getElementById('edit_selected_user_id').value = asset.user_id;

                    let userDisplay = '';
                    if (asset.user) {
                        if (asset.user.employee_number) {
                            userDisplay = asset.user.employee_number;
                            if (asset.user.name) userDisplay += ` - ${asset.user.name}`;
                        } else if (asset.user.name) {
                            userDisplay = asset.user.name;
                        }
                    }

                    document.getElementById('edit_user_search').value = userDisplay;
                }

                // Set depreciation data
                if (asset.depreciation || (asset.asset_master && asset.asset_master.is_depreciable)) {
                    const depreciationFields = document.getElementById('edit_depreciation_fields');

                    if (depreciationFields) {
                        depreciationFields.classList.remove('hidden');

                        const inputs = depreciationFields.querySelectorAll('input, select');
                        inputs.forEach(input => {
                            input.disabled = false;
                            input.required = asset.asset_master && asset.asset_master.is_depreciable;
                        });

                        if (asset.depreciation) {
                            document.getElementById('edit_depreciation_method').value = asset.depreciation.depreciation_method || 'Straight Line';
                            document.getElementById('edit_acquisition_cost').value = asset.depreciation.acquisition_cost || asset.purchase_cost || '0';
                            document.getElementById('edit_salvage_value').value = asset.depreciation.salvage_value || '0';
                            document.getElementById('edit_asset_life_months').value = asset.depreciation.asset_life_months || '12';

                            if (asset.depreciation.date_acquired) {
                                document.getElementById('edit_date_acquired').value = asset.depreciation.date_acquired.split(' ')[0];
                            } else if (asset.purchase_date) {
                                document.getElementById('edit_date_acquired').value = asset.purchase_date.split(' ')[0];
                            }
                        } else {
                            document.getElementById('edit_depreciation_method').value = 'Straight Line';
                            document.getElementById('edit_acquisition_cost').value = asset.purchase_cost || '0';
                            document.getElementById('edit_salvage_value').value = '0';
                            document.getElementById('edit_asset_life_months').value = '12';

                            if (asset.purchase_date) {
                                document.getElementById('edit_date_acquired').value = asset.purchase_date.split(' ')[0];
                            }
                        }
                    }
                }
            };

            // Initialize building search
            initDropdown(
                document.getElementById('pinjam_building_search'),
                document.getElementById('pinjam_building_dropdown'),
                document.getElementById('pinjam_building_list'),
                function (searchTerm) {
                    loadBuildings(
                        searchTerm,
                        document.getElementById('pinjam_building_list'),
                        document.getElementById('pinjam_building_loading'),
                        document.getElementById('pinjam_selected_building_id'),
                        document.getElementById('pinjam_building_search'),
                        document.getElementById('pinjam_building_dropdown'),
                        document.getElementById('pinjam_room_search')
                    );
                }
            );

            // Function to load buildings with API fetch
            async function loadBuildings(searchTerm, buildingList, loadingIndicator, selectedBuildingId, searchInput, dropdown, roomSearchInput) {
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                buildingList.innerHTML = '';

                try {
                    const response = await fetch(`{{ route('buildings') }}?search=${encodeURIComponent(searchTerm || '')}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load buildings');
                    }

                    const data = await response.json();
                    const buildings = data.data || [];

                    if (buildings.length === 0) {
                        buildingList.appendChild(createDropdownItem('Tidak ada gedung yang ditemukan', 'px-4 py-2 text-gray-500'));
                    } else {
                        buildings.forEach(building => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                            li.textContent = building.building_name;
                            li.setAttribute('data-id', building.building_id);
                            li.setAttribute('data-name', building.building_name);

                            li.addEventListener('click', function () {
                                // Set the selected building ID and name
                                selectedBuildingId.value = this.getAttribute('data-id');
                                searchInput.value = this.getAttribute('data-name');

                                // Enable room search and update placeholder
                                if (roomSearchInput) {
                                    roomSearchInput.disabled = false;
                                    roomSearchInput.placeholder = "Cari ruangan...";
                                    // Clear room selection
                                    document.getElementById('pinjam_selected_room_id').value = '';
                                    document.getElementById('pinjam_room_search').value = '';

                                    // Show loading indicator in room search
                                    const roomLoadingIndicator = document.getElementById('pinjam_room_loading');
                                    if (roomLoadingIndicator) {
                                        roomLoadingIndicator.classList.remove('hidden');
                                    }

                                    // Load rooms for this building immediately
                                    loadRoomsForBuilding(
                                        '',
                                        this.getAttribute('data-id'),
                                        document.getElementById('pinjam_room_list'),
                                        document.getElementById('pinjam_room_loading'),
                                        document.getElementById('pinjam_selected_room_id'),
                                        roomSearchInput,
                                        document.getElementById('pinjam_room_dropdown')
                                    );

                                    // Show the room dropdown
                                    document.getElementById('pinjam_room_dropdown').classList.remove('hidden');
                                }

                                // Hide dropdown
                                dropdown.classList.add('hidden');
                            });

                            buildingList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading buildings:', error);
                    buildingList.appendChild(createDropdownItem('Galat memuat gedung', 'px-4 py-2 text-red-500'));
                } finally {
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }

            // Initialize room search with debounce
            const roomSearchInput = document.getElementById('pinjam_room_search');
            if (roomSearchInput) {
                // Initially disable room search
                roomSearchInput.disabled = true;
                roomSearchInput.placeholder = 'Pilih gedung terlebih dahulu';

                initDropdown(
                    roomSearchInput,
                    document.getElementById('pinjam_room_dropdown'),
                    document.getElementById('pinjam_room_list'),
                    function (searchTerm) {
                        const buildingId = document.getElementById('pinjam_selected_building_id').value;
                        if (buildingId) {
                            loadRoomsForBuilding(
                                searchTerm,
                                buildingId,
                                document.getElementById('pinjam_room_list'),
                                document.getElementById('pinjam_room_loading'),
                                document.getElementById('pinjam_selected_room_id'),
                                roomSearchInput,
                                document.getElementById('pinjam_room_dropdown')
                            );
                        } else {
                            // If no building selected, show message
                            const roomList = document.getElementById('pinjam_room_list');
                            if (roomList) {
                                roomList.innerHTML = '';
                                roomList.appendChild(createDropdownItem('Pilih gedung terlebih dahulu', 'px-4 py-2 text-gray-500 italic'));
                            }
                        }
                    }
                );
            }

            // Function to load rooms for the selected building
            async function loadRoomsForBuilding(searchTerm, buildingId, roomList, loadingIndicator, selectedRoomId, searchInput, dropdown) {
                if (!buildingId) {
                    searchInput.value = '';
                    searchInput.placeholder = 'Pilih gedung terlebih dahulu';
                    searchInput.disabled = true;
                    return;
                }

                searchInput.disabled = false;
                searchInput.placeholder = "Cari ruangan...";

                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                roomList.innerHTML = '';

                // Show the dropdown while loading
                if (dropdown) dropdown.classList.remove('hidden');

                try {
                    const apiUrl = `{{ route('rooms') }}?building_id=${encodeURIComponent(buildingId)}&search=${encodeURIComponent(searchTerm || '')}`;
                    const response = await fetch(apiUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`Failed to load rooms: ${response.status} ${response.statusText}`);
                    }

                    const data = await response.json();

                    let rooms = [];
                    if (Array.isArray(data)) {
                        rooms = data;
                    } else if (data.data && Array.isArray(data.data)) {
                        rooms = data.data;
                    } else if (data.rooms && Array.isArray(data.rooms)) {
                        rooms = data.rooms;
                    } else {
                        console.error('Unexpected API response format:', data);
                        throw new Error('Invalid response format from server');
                    }

                    if (rooms.length === 0) {
                        roomList.appendChild(createDropdownItem('Tidak ada ruangan ditemukan untuk gedung ini', 'px-4 py-2 text-gray-500 italic'));
                    } else {
                        rooms.forEach(room => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            // Extract room properties with fallbacks
                            const roomName = room.room_name || room.name || '';
                            const roomId = room.room_id || room.id || '';

                            if (!roomName || !roomId) {
                                console.warn('Room missing required properties:', room);
                                return; // Skip this room
                            }

                            li.textContent = roomName;
                            li.setAttribute('data-id', roomId);
                            li.setAttribute('data-name', roomName);

                            li.addEventListener('click', function () {
                                selectedRoomId.value = this.getAttribute('data-id');
                                searchInput.value = this.getAttribute('data-name');
                                dropdown.classList.add('hidden');
                            });

                            roomList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading rooms:', error);
                    roomList.innerHTML = '';
                    roomList.appendChild(createDropdownItem(`Error: ${error.message}`, 'px-4 py-2 text-red-500'));
                } finally {
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }

            // Edit asset functionality
            document.querySelectorAll('.edit-asset-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const assetId = '{{ $asset["asset_id"] ?? "" }}';
                    const editModal = document.getElementById('editAssetModal');
                    const editContent = document.getElementById('editAssetModalContent');
                    const loadingIndicator = document.getElementById('edit-loading');
                    const formContent = document.getElementById('edit-form-content');
                    const submitBtn = document.getElementById('edit-submit-btn');

                    if (editModal && editContent) {
                        // Reset form and show loading
                        document.getElementById('editAssetForm').reset();
                        document.getElementById('editAssetForm').action = `{{ url('assets') }}/${assetId}`;

                        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                        if (formContent) formContent.classList.add('hidden');
                        if (submitBtn) submitBtn.disabled = true;

                        openModal(editModal, editContent);

                        fetch(`{{ route('asset.details', ['id' => $asset['asset_id']]) }}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin'
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (!data || !data.data) {
                                    throw new Error('Invalid response data structure');
                                }

                                const asset = data.data;

                                // Use the standard setupWithData function
                                setupWithData(asset);

                                // Hide loading and show form
                                if (loadingIndicator) loadingIndicator.classList.add('hidden');
                                if (formContent) formContent.classList.remove('hidden');
                                if (submitBtn) submitBtn.disabled = false;
                            })
                            .catch(error => {
                                console.error('Error fetching asset data:', error);

                                if (loadingIndicator) loadingIndicator.classList.add('hidden');

                                if (formContent) {
                                    formContent.innerHTML = `
                                        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                                            <p class="font-medium">Failed to load asset data</p>
                                            <p>${error.message}</p>
                                            <p class="mt-2">Please try again or contact support if the problem persists.</p>
                                        </div>
                                        <button type="button" class="close-modal w-full h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300">
                                            Close
                                        </button>
                                    `;
                                    formContent.classList.remove('hidden');
                                }

                                // Reattach close event listeners
                                formContent.querySelectorAll('.close-modal').forEach(btn => {
                                    btn.addEventListener('click', function () {
                                        closeModal(editModal, editContent);
                                    });
                                });
                            });
                    }
                });
            });

            // Prevent multiple submissions for all forms
            function setupFormSubmissionHandling(formId, loadingText = 'Processing...') {
                const form = document.getElementById(formId);
                if (!form) return;

                form.addEventListener('submit', function (event) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        // Save original button text
                        const originalText = submitBtn.innerHTML;

                        // Disable button and show loading state
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>${loadingText}</span></div>`;

                        // Set timeout to re-enable button after 10 seconds (in case of network issues)
                        setTimeout(() => {
                            if (submitBtn.disabled) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            }
                        }, 10000);
                    }
                });
            }

            // Setup form submission handling for all asset action forms except editAssetForm
            setupFormSubmissionHandling('checkoutAssetForm', 'Meminjam...');
            setupFormSubmissionHandling('checkinAssetForm', 'Mengembalikan...');
            setupFormSubmissionHandling('reportLostForm', 'Melaporkan...');
            setupFormSubmissionHandling('foundAssetForm', 'Melaporkan ditemukan...');
            setupFormSubmissionHandling('disposeAssetForm', 'Menghapuskan...');

            // Function to validate fields before form submission
            function validateEditForm() {
                const form = document.getElementById('editAssetForm');
                if (!form) return true;

                // Fields to validate
                const assetMasterSearch = document.getElementById('edit_asset_master_search');
                const selectedAssetMasterId = document.getElementById('edit_selected_asset_master_id');
                const buildingSearch = document.getElementById('edit_building_search');
                const selectedBuildingId = document.getElementById('edit_selected_building_id');
                const roomSearch = document.getElementById('edit_room_search');
                const selectedRoomId = document.getElementById('edit_selected_room_id');
                const depreciationFields = document.getElementById('edit_depreciation_fields');
                const isDepreciable = !depreciationFields.classList.contains('hidden');

                // Validate only mandatory fields
                const isAssetMasterValid = validateField(assetMasterSearch, selectedAssetMasterId.value ? true : false);
                const isBuildingValid = validateField(buildingSearch, selectedBuildingId.value ? true : false);
                const isRoomValid = validateField(roomSearch, selectedRoomId.value ? true : false);

                // Track validation status
                let isValid = isAssetMasterValid && isBuildingValid && isRoomValid;

                // If depreciation is enabled, validate depreciation fields
                if (isDepreciable) {
                    const depreciation_method = document.getElementById('edit_depreciation_method');
                    const acquisition_cost = document.getElementById('edit_acquisition_cost');
                    const salvage_value = document.getElementById('edit_salvage_value');
                    const asset_life_months = document.getElementById('edit_asset_life_months');
                    const date_acquired = document.getElementById('edit_date_acquired');

                    // Validate all required depreciation fields
                    const isDepreciationMethodValid = validateField(depreciation_method);
                    const isAcquisitionCostValid = validateField(acquisition_cost);
                    const isSalvageValueValid = validateField(salvage_value);
                    const isAssetLifeMonthsValid = validateField(asset_life_months);
                    const isDateAcquiredValid = validateField(date_acquired);

                    // Update overall validation status
                    isValid = isValid && isDepreciationMethodValid && isAcquisitionCostValid &&
                        isSalvageValueValid && isAssetLifeMonthsValid && isDateAcquiredValid;
                }

                if (!isValid) {
                    showToast('Silakan lengkapi semua field yang wajib diisi', 'error');
                }

                return isValid;
            }

            // Function to validate field and show/hide error message
            function validateField(field, customCheck = null) {
                if (!field) return true; // Skip if field doesn't exist

                let isValid = true;
                if (customCheck !== null) {
                    isValid = customCheck;
                } else if (field.tagName.toLowerCase() === 'select') {
                    isValid = field.value !== '';
                } else {
                    isValid = field.value.trim() !== '';
                }

                // Find the error message element
                const errorElement = field.closest('.space-y-2')?.querySelector('.error-message');

                if (!isValid) {
                    field.classList.add('border-red-500');
                    if (errorElement) errorElement.classList.remove('hidden');
                } else {
                    field.classList.remove('border-red-500');
                    if (errorElement) errorElement.classList.add('hidden');
                }

                return isValid;
            }

            // Add event listeners to clear error styling when typing
            function clearErrorOnInput(inputId) {
                const input = document.getElementById(inputId);
                if (input) {
                    input.addEventListener('input', function () {
                        this.classList.remove('border-red-500');
                        const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    });
                }
            }

            // Apply input event listeners to all fields
            clearErrorOnInput('edit_asset_master_search');
            clearErrorOnInput('edit_building_search');
            clearErrorOnInput('edit_room_search');
            clearErrorOnInput('edit_serial_number');
            clearErrorOnInput('edit_purchase_date');
            clearErrorOnInput('edit_purchase_cost');
            clearErrorOnInput('edit_warranty_end_date');
            clearErrorOnInput('edit_condition');
            clearErrorOnInput('edit_user_search');
            clearErrorOnInput('edit_depreciation_method');
            clearErrorOnInput('edit_acquisition_cost');
            clearErrorOnInput('edit_salvage_value');
            clearErrorOnInput('edit_asset_life_months');
            clearErrorOnInput('edit_date_acquired');

            // Modify the editAssetForm submit handler with integrated validation and multiple submission prevention
            document.getElementById('editAssetForm')?.addEventListener('submit', function (event) {
                // Prevent default submission to validate first
                event.preventDefault();

                // Validate the form
                if (validateEditForm()) {
                    // Get the submit button
                    const submitBtn = this.querySelector('button[type="submit"]');

                    // Prevent multiple submissions
                    if (submitBtn && !submitBtn.disabled) {
                        // Save original button text
                        const originalText = submitBtn.innerHTML;

                        // Disable button and show loading state
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Memperbarui...</span></div>`;

                        // Set timeout to re-enable button after 10 seconds (in case of network issues)
                        setTimeout(() => {
                            if (submitBtn.disabled) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            }
                        }, 10000);
                    }

                    // If valid, submit the form
                    this.submit();
                }
            });
        });
    </script>
@endpush