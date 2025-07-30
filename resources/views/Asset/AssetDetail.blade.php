@extends('Layout.app')

@section('title', 'Detail Aset')

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
                    <h1 class="text-xl md:text-2xl lg:text-[32px] font-semibold text-[#213268]">DETAIL ASSET</h1>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex flex-row overflow-x-auto gap-2 pb-2 w-full md:w-auto md:gap-3 justify-start md:justify-end no-scrollbar">
                    @if($asset['current_status'] === 'dispose')
                        <!-- When status is disposed, show only Edit button -->
                        @if(hasPermission('asset:edit'))
                            <a href="javascript:void(0)" id="editAssetBtn"
                                class="flex-shrink-0 flex items-center justify-center gap-2 px-2 py-2 md:px-4 md:py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span class="hidden md:inline">Ubah</span>
                            </a>
                        @endif
                    @elseif($asset['current_status'] === 'available')
                        <!-- When status is available: Check Out and Edit buttons -->
                        @if(hasPermission('asset:checkout'))
                            <button type="button" id="checkoutAssetBtn"
                                class="flex-shrink-0 flex items-center justify-center gap-2 px-2 py-2 md:px-4 md:py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="hidden md:inline">Pinjam</span>
                            </button>
                        @endif
                        @if(hasPermission('asset:edit'))
                            <button type="button" id="editAssetBtn"
                                class="flex-shrink-0 flex items-center justify-center gap-2 px-2 py-2 md:px-4 md:py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span class="hidden md:inline">Ubah</span>
                            </button>
                        @endif
                    @elseif($asset['current_status'] === 'check out')
                        <!-- When status is check out: Check In and Edit buttons -->
                        @if(hasPermission('asset:checkout'))
                            <button type="button" id="checkinAssetBtn"
                                class="flex-shrink-0 flex items-center justify-center gap-2 px-2 py-2 md:px-4 md:py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                <span class="hidden md:inline">Kembalikan</span>
                            </button>
                        @endif
                        @if(hasPermission('asset:edit'))
                            <button type="button" id="editAssetBtn"
                                class="flex-shrink-0 flex items-center justify-center gap-2 px-2 py-2 md:px-4 md:py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span class="hidden md:inline">Ubah</span>
                            </button>
                        @endif
                    @elseif($asset['current_status'] === 'lost')
                        <!-- When status is lost: Edit button only -->
                        @if(hasPermission('asset:edit'))
                            <button type="button" id="editAssetBtn"
                                class="flex-shrink-0 flex items-center justify-center gap-2 px-2 py-2 md:px-4 md:py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span class="hidden md:inline">Ubah</span>
                            </button>
                        @endif
                    @elseif($asset['current_status'] === 'under repair')
                        <!-- When status is under repair: Edit button -->
                        @if(hasPermission('asset:edit'))
                            <button type="button" id="editAssetBtn"
                                class="flex-shrink-0 flex items-center justify-center gap-2 px-2 py-2 md:px-4 md:py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span class="hidden md:inline">Ubah</span>
                            </button>
                        @endif
                    @endif
                    <!-- Add this button alongside the other action buttons -->
                    @if(hasPermission('asset:export'))
                        <a href="{{ route('asset.export-pdf', ['id' => $asset['asset_id'] ?? '']) }}"
                            class="flex-shrink-0 flex items-center justify-center gap-2 px-2 py-2 md:px-4 md:py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200"
                            target="_blank">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <span class="hidden md:inline">Export PDF</span>
                        </a>
                    @endif
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
                                class="flip-card-front bg-[#D9D9D9] rounded-[20px] shadow-md flex items-center justify-center overflow-hidden w-full h-full">
                                @if(isset($asset['asset_master']['reference_image_path']) && $asset['asset_master']['reference_image_path'])
                                    <img src="{{ config('app.backend_url') }}/public{{ $asset['asset_master']['reference_image_path'] }}"
                                        alt="Asset Image" class="absolute inset-0 w-full h-full object-cover p-0 rounded-[20px]"
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
                                class="flip-card-back bg-white rounded-[20px] shadow-md flex items-center justify-center overflow-hidden w-full h-full">
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
                            {{ $asset['asset_master_name'] ?? $asset['asset_master']['asset_name'] ?? '-' }}
                        </p>
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
                                <span class="w-[150px] font-semibold text-sm">Merk</span>
                                <span class="text-sm">{{ $asset['brand_name'] ?? '-' }}</span>
                            </div>
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Model</span>
                                <span class="text-sm">{{ $asset['model'] ?? '-' }}</span>
                            </div>
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
                                <span
                                    class="text-sm">{{ $asset['warranty_end_date'] ? \Carbon\Carbon::parse($asset['warranty_end_date'])->locale('id')->translatedFormat('d F Y') : '-' }}</span>
                            </div>
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Nomor Seri</span>
                                <span class="text-sm">{{ $asset['serial_number'] ?? '-' }}</span>
                            </div>
                            <!-- Add Employee Number for Responsible User -->
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Penanggung Jawab</span>
                                <span class="text-sm">{{ $asset['employee_name'] ?? '-' }}</span>
                            </div>
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Harga Beli</span>
                                <span class="text-sm">{{ number_format((float) ($asset['purchase_cost'] ?? 0), 2) }}</span>
                            </div>
                            <div class="flex flex-wrap items-center">
                                <span class="w-[150px] font-semibold text-sm">Tanggal Beli</span>
                                <span
                                    class="text-sm">{{ $asset['purchase_date'] ? \Carbon\Carbon::parse($asset['purchase_date'])->locale('id')->translatedFormat('d F Y') : '-' }}</span>
                            </div>

                            @if($asset['current_status'] === 'dispose')
                                <div class="flex flex-wrap items-center">
                                    <span class="w-[150px] font-semibold text-sm">Tanggal Dimusnahkan</span>
                                    <span class="text-sm">
                                        @if(isset($asset['updated_at']))
                                            @php
                                                // Convert the timestamp to a more readable format without time
                                                $disposedDate = \Carbon\Carbon::parse($asset['updated_at'])->locale('id')->translatedFormat('d F Y');
                                            @endphp
                                            {{ $disposedDate }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            @endif

                            @if($asset['current_status'] === 'lost')
                                <div class="flex flex-wrap items-center">
                                    <span class="w-[150px] font-semibold text-sm">Tanggal Hilang</span>
                                    <span class="text-sm">
                                        @if(isset($asset['updated_at']))
                                            @php
                                                // Convert the timestamp to a more readable format
                                                $lostDate = \Carbon\Carbon::parse($asset['updated_at'])->locale('id')->translatedFormat('d F Y');
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
            <div class="fixed inset-0 z-50 overflow-visible modal-container">
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
                                                    placeholder="Cari master aset..." autocomplete="off">
                                                <input type="hidden" name="asset_master_id" id="edit_selected_asset_master_id">
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
                                                    <!-- Load more indicator for infinite scroll -->
                                                    <div id="edit_asset_master_load_more"
                                                        class="p-2 text-gray-500 text-center hidden">
                                                        <svg class="animate-spin h-5 w-5 mx-auto"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                        <span>Memuat lebih banyak...</span>
                                                    </div>
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

                                        <!-- Brand Dropdown -->
                                        <div class="mb-5 space-y-2">
                                            <label class="block text-base font-semibold text-[#666666] mb-2">Merk <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <input type="text" id="edit_brand_search"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="Cari merk..." autocomplete="off">
                                                <input type="hidden" name="brand_id" id="edit_selected_brand_id">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Merk harus
                                                    dipilih</div>
                                                <div id="edit_brand_dropdown"
                                                    class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                    <div id="edit_brand_loading" class="p-2 text-gray-500 text-center">
                                                        <svg class="animate-spin h-5 w-5 mx-auto"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                        <span>Memuat merk...</span>
                                                    </div>
                                                    <ul id="edit_brand_list" class="py-1"></ul>
                                                    <!-- Load more indicator for brand dropdown -->
                                                    <div id="edit_brand_load_more" class="p-2 text-gray-500 text-center hidden">
                                                        <svg class="animate-spin h-5 w-5 mx-auto"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                        <span>Memuat lebih banyak...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Model field -->
                                        <div class="mb-5 space-y-2">
                                            <label for="edit_model"
                                                class="block text-base font-semibold text-[#666666] mb-2">Model</label>
                                            <input type="text" name="model" id="edit_model" value="{{ $asset['model'] ?? '' }}"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                placeholder="Masukkan model">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Model harus diisi</div>
                                        </div>

                                        <!-- Purchase Information -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Tanggal
                                                    Pembelian</label>
                                                <input type="text" name="purchase_date" id="edit_purchase_date"
                                                    value="{{ $asset['purchase_date'] ?? '' }}"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="Pilih Tanggal">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal pembelian
                                                    harus diisi</div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Tanggal Berakhir
                                                    Garansi</label>
                                                <input type="text" name="warranty_end_date" id="edit_warranty_end_date"
                                                    value="{{ $asset['warranty_end_date'] ?? '' }}"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="Pilih Tanggal">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Tanggal berakhir garansi
                                                    harus diisi</div>
                                            </div>
                                        </div>

                                        <!-- Biaya Pembelian -->
                                        <div class="mb-5 space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Biaya
                                                    Pembelian</label>
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                        <span class="text-gray-500">Rp</span>
                                                    </div>
                                                    <input type="text" name="purchase_cost" id="edit_purchase_cost"
                                                        value="{{ number_format($asset['purchase_cost'] ?? 0, 0, '', '.') }}"
                                                        class="currency-input w-full h-[45px] pl-10 pr-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="0" data-type="currency" onkeyup="formatCurrency(this)"
                                                        onblur="formatCurrency(this, 'blur')">
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Biaya pembelian
                                                        harus diisi</div>
                                            </div>
                                        </div>



                                        <!-- Location Information -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Gedung <span
                                                        class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="text" id="edit_building_search"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="Cari gedung..." autocomplete="off">
                                                    <input type="hidden" name="building_id" id="edit_selected_building_id">
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
                                                        <!-- Load more indicator for building dropdown -->
                                                        <div id="edit_building_load_more"
                                                            class="p-2 text-gray-500 text-center hidden">
                                                            <svg class="animate-spin h-5 w-5 mx-auto"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                            <span>Memuat lebih banyak...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Ruangan <span
                                                        class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="text" id="edit_room_search"
                                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="Pilih gedung terlebih dahulu" autocomplete="off" disabled>
                                                    <input type="hidden" name="room_id" id="edit_selected_room_id">
                                                    <input type="hidden" name="room_id" id="edit_selected_room_id"
                                                        value="{{ $asset['room_id'] ?? '' }}">
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
                                                        <!-- Load more indicator for room dropdown -->
                                                        <div id="edit_room_load_more"
                                                            class="p-2 text-gray-500 text-center hidden">
                                                            <svg class="animate-spin h-5 w-5 mx-auto"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                            <span>Memuat lebih banyak...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Condition and Responsibility -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Kondisi</label>
                                                <select name="condition" id="edit_condition"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                                    <option value="good" {{ $asset['condition'] == 'good' ? 'selected' : '' }}>
                                                        Baik</option>
                                                    <option value="slightly damage" {{ $asset['condition'] == 'slighly damage' ? 'selected' : '' }}>Sedikit Rusak</option>
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
                                                    <input type="hidden" name="user_id" id="edit_selected_user_id">
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
                                                        <!-- Load more indicator for user dropdown -->
                                                        <div id="edit_user_load_more"
                                                            class="p-2 text-gray-500 text-center hidden">
                                                            <svg class="animate-spin h-5 w-5 mx-auto"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                    stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                            <span>Memuat lebih banyak...</span>
                                                        </div>
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
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                                <option value="Straight Line" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == 'Straight Line' ? 'selected' : '' }}>Garis Lurus (Straight Line)</option>
                                                <option value="Declining Balance" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == 'Declining Balance' ? 'selected' : '' }}>Saldo Menurun (Declining Balance)</option>
                                                <option value="Double Declining Balance" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == 'Double Declining Balance' ? 'selected' : '' }}>Saldo Menurun Ganda (Double Declining Balance)</option>
                                                <option value="150% Declining Balance" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == '150% Declining Balance' ? 'selected' : '' }}>Saldo Menurun 150% (150% Declining Balance)</option>
                                                <option value="Sum of the Year's Digits" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == "Sum of the Year's Digits" ? 'selected' : '' }}>Jumlah Digit Tahun (Sum of Year's Digits)</option>
                                            </select>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Metode penyusutan harus
                                                dipilih</div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Biaya Perolehan
                                                    <span class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                        <span class="text-gray-500">Rp</span>
                                                    </div>
                                                    <input type="text" name="acquisition_cost" id="edit_acquisition_cost"
                                                        class="currency-input w-full h-[45px] pl-10 pr-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="0" data-type="currency" onkeyup="formatCurrency(this)"
                                                        onblur="formatCurrency(this, 'blur')"
                                                        value="{{ isset($asset['depreciation']) ? number_format($asset['depreciation']['acquisition_cost'], 0, '', '.') : '0' }}">
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Biaya perolehan
                                                        harus diisi</div>
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Nilai Sisa
                                                    <span class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                        <span class="text-gray-500">Rp</span>
                                                    </div>
                                                    <input type="text" name="salvage_value" id="edit_salvage_value"
                                                        class="currency-input w-full h-[45px] pl-10 pr-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                        placeholder="0" data-type="currency" onkeyup="formatCurrency(this)"
                                                        onblur="formatCurrency(this, 'blur')"
                                                        value="{{ isset($asset['depreciation']) ? number_format($asset['depreciation']['salvage_value'], 0, '', '.') : '0' }}">
                                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Nilai sisa harus
                                                        diisi</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Usia Aset
                                                    (bulan) <span class="text-red-500">*</span></label>
                                                <input type="number" name="asset_life_months" id="edit_asset_life_months"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    value="{{ isset($asset['depreciation']) ? $asset['depreciation']['asset_life_months'] : '' }}">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Usia aset harus
                                                    diisi</div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-base font-semibold text-[#666666] mb-2">Tanggal
                                                    Pengadaan <span class="text-red-500">*</span></label>
                                                                                            <input type="text" name="date_acquired" id="edit_date_acquired"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                value="{{ isset($asset['depreciation']) ? $asset['depreciation']['date_acquired'] : '' }}"
                                                placeholder="Pilih Tanggal">
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
            <div class="fixed inset-0 z-50 overflow-visible modal-container">
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
                                        <label class="block text-base font-medium text-[#666666]">Tanggal Pinjam <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="checkout_date" id="checkout_date" readonly tabindex="-1"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            value="{{ date('Y-m-d') }}" placeholder="Tanggal Hari Ini">
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
                                        <label class="block text-base font-medium text-[#666666]">Pilih Karyawan <span
                                                class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <input type="text" id="checkout_user_search"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                                placeholder="Cari karyawan (nomor karyawan)..." autocomplete="off">
                                            <input type="hidden" name="assigned_to" id="checkout_selected_user_id">
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Silakan pilih karyawan
                                            </div>
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
                                                <!-- Load more indicator for checkout user dropdown -->
                                                <div id="checkout_user_load_more" class="p-2 text-gray-500 text-center hidden">
                                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                            stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                    <span>Memuat lebih banyak...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location Dropdown (hidden by default) -->
                                    <div id="locationDropdown" class="space-y-4 hidden">
                                        <!-- Building Search Dropdown -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-medium text-[#666666]">Pilih Gedung <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <input type="text" id="pinjam_building_search"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="Cari gedung..." autocomplete="off">
                                                <input type="hidden" name="building_id" id="pinjam_selected_building_id">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Silakan pilih gedung
                                                </div>
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
                                                    <!-- Load more indicator for pinjam building dropdown -->
                                                    <div id="pinjam_building_load_more"
                                                        class="p-2 text-gray-500 text-center hidden">
                                                        <svg class="animate-spin h-5 w-5 mx-auto"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                        <span>Memuat lebih banyak...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Room Search Dropdown -->
                                        <div class="space-y-2">
                                            <label class="block text-base font-medium text-[#666666]">Pilih Ruangan <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <input type="text" id="pinjam_room_search"
                                                    class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                    placeholder="Pilih gedung terlebih dahulu" autocomplete="off" disabled>
                                                <input type="hidden" name="room_id" id="pinjam_selected_room_id">
                                                <div class="error-message text-red-500 text-sm mt-1 hidden">Silakan pilih
                                                    ruangan</div>
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
                                                    <!-- Load more indicator for pinjam room dropdown -->
                                                    <div id="pinjam_room_load_more"
                                                        class="p-2 text-gray-500 text-center hidden">
                                                        <svg class="animate-spin h-5 w-5 mx-auto"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                        <span>Memuat lebih banyak...</span>
                                                    </div>
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
            <div class="fixed inset-0 z-50 overflow-visible modal-container">
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
                                        <label class="block text-base font-medium text-[#666666]">Tanggal Kembali <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="return_date" id="return_date" readonly tabindex="-1"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            value="{{ date('Y-m-d') }}" placeholder="Tanggal Hari Ini">
                                    </div>

                                    <!-- Asset Condition -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Kondisi Asset <span
                                                class="text-red-500">*</span></label>
                                        <select name="condition" id="return_condition"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
                                            <option value="GOOD">Baik</option>
                                            <option value="DAMAGED">Rusak</option>
                                            <option value="NEEDS_REPAIR">Perlu Perbaikan</option>
                                        </select>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Silakan pilih kondisi asset
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Catatan Kembali</label>
                                        <textarea name="return_notes" rows="3"
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

    <style>
        /* Flip card styling */
        .flip-card-container {
            perspective: 1000px;
        }

        .flip-card {
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.7s;
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

        /* Fix dropdown positioning */
        #user_dropdown,
        #edit_user_dropdown,
        #building_dropdown,
        #edit_building_dropdown,
        #room_dropdown,
        #edit_room_dropdown,
        #asset_master_dropdown,
        #edit_asset_master_dropdown,
        #checkout_user_dropdown,
        #pinjam_building_dropdown,
        #pinjam_room_dropdown {
            position: absolute;
            z-index: 9999;
        }

        /* Fix parent container to allow overflow */
        .relative {
            position: relative;
            overflow: visible !important;
        }

        /* Fix for modals to allow dropdowns to appear outside */
        .modal-container {
            overflow-y: auto !important;
            /* Enable vertical scrolling */
            height: 100vh;
            /* Use full viewport height */
            /* Hide scrollbar but keep functionality */
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE and Edge */
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .modal-container::-webkit-scrollbar {
            display: none;
        }

        /* Keep dropdowns visible */
        .fixed.inset-0.z-50 {
            overflow-y: auto !important;
        }

        .fixed.inset-0.z-50 .min-h-full {
            min-height: auto !important;
            padding: 2rem 0;
        }

        /* Add spacing at the bottom for long forms */
        .modal-container>div {
            padding-bottom: 2rem;
        }

        /* Reset inner content scrolling */
        #editAssetModalContent,
        #checkoutAssetModalContent,
        #checkinAssetModalContent,
        #reportLostModalContent,
        #foundAssetModalContent,
        #disposeAssetModalContent {
            overflow-y: visible !important;
            max-height: none !important;
        }

        /* Asset Master Dropdown Styling */
        .asset-master-item,
        .building-item {
            display: flex;
            flex-direction: column;
        }

        .asset-master-item .name,
        .building-item .name {
            font-weight: 500;
            color: #666;
        }

        .asset-master-item .code {
            font-size: 0.85em;
            color: #666;
        }

        /* User Dropdown Styling */
        .user-item {
            display: flex;
            flex-direction: column;
        }

        .user-item .name {
            font-weight: 500;
            color: #666;
        }

        .user-item .code {
            font-size: 0.85em;
            color: #666;
        }

        /* Room Dropdown Styling */
        .room-item {
            display: flex;
            flex-direction: column;
        }

        .room-item .name {
            font-weight: 500;
            color: #666;
        }

        /* Dropdown Scroll Loading Indicator */
        #edit_asset_master_load_more,
        #edit_room_load_more,
        #edit_building_load_more,
        #edit_user_load_more {
            padding: 8px 0;
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const assetId = '{{ $asset['asset_id'] ?? "" }}';
            const currentStatus = '{{ $asset['current_status'] ?? "" }}';

            // Currency formatting function from UnitAsset.blade.php
            function formatCurrency(input, blur) {
                let value = input.value.replace(/[^\d]/g, '');

                if (value === '') {
                    input.value = '';
                    return;
                }

                if (value.length > 15) {
                    value = value.substring(0, 15);
                }

                let formattedValue = '';
                let counter = 0;

                for (let i = value.length - 1; i >= 0; i--) {
                    counter++;
                    formattedValue = value.charAt(i) + formattedValue;
                    if (counter % 3 === 0 && i > 0) {
                        formattedValue = '.' + formattedValue;
                    }
                }

                input.value = formattedValue;
            }
            window.formatCurrency = formatCurrency;

            function parseFormattedNumber(value) {
                return value.replace(/\./g, '').replace(/[^\d]/g, '');
            }
            window.parseFormattedNumber = parseFormattedNumber;

            // Initialize Flatpickr date pickers
            function initDatepickers() {
                // Define Indonesian locale
                const indonesianLocale = {
                    weekdays: {
                        shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                        longhand: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
                    },
                    months: {
                        shorthand: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"],
                        longhand: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
                    },
                    firstDayOfWeek: 1,
                    rangeSeparator: " sampai ",
                    weekAbbreviation: "Minggu",
                    scrollTitle: "Gulir untuk menambah",
                    toggleTitle: "Klik untuk beralih",
                    time_24hr: true,
                };

                // Dynamically load Flatpickr if not already available
                function loadFlatpickr() {
                    if (typeof flatpickr === 'undefined') {
                        // Create link for CSS
                        const cssLink = document.createElement('link');
                        cssLink.rel = 'stylesheet';
                        cssLink.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css';
                        document.head.appendChild(cssLink);

                        // Create script for Flatpickr core
                        const script = document.createElement('script');
                        script.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
                        script.onload = function() {
                            if (flatpickr && flatpickr.l10ns) {
                                flatpickr.l10ns.id = indonesianLocale;
                                initAllDatepickers();
                            }
                        };
                        document.head.appendChild(script);
                    } else {
                        if (flatpickr.l10ns) {
                            flatpickr.l10ns.id = indonesianLocale;
                        }
                        initAllDatepickers();
                    }
                }

                function initAllDatepickers() {
                    // Editable date fields (edit modal)
                    const dateFields = [
                        'edit_purchase_date',
                        'edit_warranty_end_date',
                        'edit_date_acquired'
                    ];

                    dateFields.forEach(fieldId => {
                        const dateField = document.getElementById(fieldId);
                        if (dateField) {
                            initFlatpickr(dateField, false);
                        }
                    });

                    // Read-only date fields (with today's date)
                    const readonlyDateFields = [
                        'checkout_date',
                        'return_date'
                    ];

                    readonlyDateFields.forEach(fieldId => {
                        const dateField = document.getElementById(fieldId);
                        if (dateField) {
                            // Initialize with readonly false to allow opening the calendar but with custom styling
                            initFlatpickr(dateField, true); // clickOpens: false
                        }
                    });
                }

                function initFlatpickr(dateInput, isReadonly) {
                    if (!dateInput) return;

                    const fpInstance = flatpickr(dateInput, {
                        locale: 'id',
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "j F Y",
                        static: true,
                        disableMobile: true,
                        allowInput: false,
                        clickOpens: !isReadonly,
                        // Ensure field styling is consistent
                        onReady: function(selectedDates, dateStr, instance) {
                            if (instance.altInput) {
                                instance.altInput.style.width = "100%";
                                instance.altInput.style.display = "block";

                                // Maintain container width
                                const parentWrapper = instance.altInput.closest('.flatpickr-wrapper');
                                if (parentWrapper) {
                                    parentWrapper.style.width = "100%";
                                    parentWrapper.style.display = "block";
                                }

                                // Inherit original input's styling
                                instance.altInput.className = dateInput.className;
                            }

                            // Override date display with Indonesian format
                            if (selectedDates && selectedDates.length > 0) {
                                const date = selectedDates[0];
                                const day = date.getDate();
                                const monthsInIndonesian = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                                           "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                const month = monthsInIndonesian[date.getMonth()];
                                const year = date.getFullYear();

                                if (instance.altInput) {
                                    instance.altInput.value = `${day} ${month} ${year}`;
                                }
                            }
                        },
                        onChange: function(selectedDates, dateStr, instance) {
                            if (selectedDates && selectedDates.length > 0) {
                                const date = selectedDates[0];
                                const day = date.getDate();
                                const monthsInIndonesian = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                                           "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                const month = monthsInIndonesian[date.getMonth()];
                                const year = date.getFullYear();

                                if (instance.altInput) {
                                    instance.altInput.value = `${day} ${month} ${year}`;
                                }
                            }
                        },
                        formatDate: (date, format) => {
                            if (format === "Y-m-d") {
                                const localDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
                                const year = localDate.getFullYear();
                                const month = String(localDate.getMonth() + 1).padStart(2, '0');
                                const day = String(localDate.getDate()).padStart(2, '0');
                                return `${year}-${month}-${day}`;
                            }

                            if (format === "j F Y") {
                                const day = date.getDate();
                                const monthsInIndonesian = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                                            "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                const month = monthsInIndonesian[date.getMonth()];
                                const year = date.getFullYear();
                                return `${day} ${month} ${year}`;
                            }

                            return flatpickr.formatDate(date, format);
                        },
                        parseDate: (datestr, format) => {
                            if (format === "Y-m-d") {
                                const [year, month, day] = datestr.split("-").map(Number);
                                return new Date(year, month - 1, day);
                            }
                            return flatpickr.parseDate(datestr, format);
                        }
                    });

                    return fpInstance;
                }

                loadFlatpickr();
            }

            initDatepickers();

            function initializeButtons() {
                @if(!hasPermission('asset:edit'))
                    document.querySelectorAll('#editAssetBtn').forEach(btn => {
                        if (btn) btn.style.display = 'none';
                    });
                @endif

                @if(!hasPermission('asset:checkout'))
                    document.querySelectorAll('#checkoutAssetBtn').forEach(btn => {
                        if (btn) btn.style.display = 'none';
                    });
                @endif

                @if(!hasPermission('asset:checkout'))
                    document.querySelectorAll('#checkinAssetBtn').forEach(btn => {
                        if (btn) btn.style.display = 'none';
                    });
                @endif


                }

            initializeButtons();

            // Flip card functionality
            const flipCard = document.querySelector('.flip-card');
            const flipBtns = document.querySelectorAll('.flip-btn');

            flipBtns.forEach(btn => {
                btn.addEventListener('click', () => flipCard.classList.toggle('flipped'));
            });

            // Tab functionality
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

            function showToast(message, type = 'success') {
                const notification = document.createElement('div');
                notification.id = type + 'Notification' + Date.now();
                notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
                notification.role = 'alert';

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

                    const wrapper = document.createElement('div');
                    wrapper.className = 'flex items-start';

                    const iconContainer = document.createElement('div');
                    iconContainer.className = 'py-1 flex-shrink-0';
                    iconContainer.innerHTML = `
                                                <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            `;

                    const contentContainer = document.createElement('div');
                    contentContainer.className = 'flex-grow max-w-xs sm:max-w-sm md:max-w-md';

                    const title = document.createElement('p');
                    title.className = 'font-bold';
                    title.textContent = 'Gagal!';
                    contentContainer.appendChild(title);

                    const messageContainer = document.createElement('div');
                    messageContainer.className = 'error-message';

                    if (hasHTML) {
                        messageContainer.innerHTML = message;
                    } else {
                        messageContainer.textContent = message;
                    }

                    contentContainer.appendChild(messageContainer);

                    const closeBtn = document.createElement('span');
                    closeBtn.className = 'ml-4 cursor-pointer flex-shrink-0';
                    closeBtn.textContent = '×';
                    closeBtn.onclick = function () {
                        notification.remove();
                    };

                    wrapper.appendChild(iconContainer);
                    wrapper.appendChild(contentContainer);
                    wrapper.appendChild(closeBtn);
                    notification.appendChild(wrapper);
                }

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => notification.remove(), 500);
                }, 5000);
            }

            document.head.insertAdjacentHTML('beforeend', `
                                    <style>
                                        @keyframes slideInRight {
                                            from { transform: translateX(100%); }
                                            to { transform: translateX(0); }
                                        }
                                        .animate-slide-in-right {
                                            animation: slideInRight 0.3s ease-out forwards;
                                        }

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

            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                 showToast("{{ session('error') }}", 'error');
            @endif

            window.usersCache =[];
            window.roomsCache = [];
            window.buildingsCache = [];
            window.assetMastersCache = [];

            function debounce(func, wait) {
                let timeout;
                return function (...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }
            window.debounce = debounce;

            function openModal(modal, content) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                    content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                }, 10);
            }

            function closeModal(modal, content) {
                if (!modal || !content) return;

                const modalId = modal.id;

                const forms = modal.querySelectorAll('form');
                forms.forEach(form => {
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

                        default:
                            resetGenericForm(form);
                    }
                });

                content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            function resetGenericForm(form) {
                if (!form) return;

                form.reset();

                const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
                hiddenInputs.forEach(input => {
                    if (!input.name.includes('asset_id')) {
                        input.value = '';
                    }
                });

                const textInputs = form.querySelectorAll('input[type="text"], input[type="search"]');
                textInputs.forEach(input => {
                    input.value = '';
                });

                const selects = form.querySelectorAll('select');
                selects.forEach(select => {
                    if (select.options.length > 0) {
                        select.selectedIndex = 0;
                    }
                });

                const dropdowns = form.querySelectorAll('[id$="_dropdown"]');
                dropdowns.forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });

                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                }

                const errorFields = form.querySelectorAll('.error, .border-red-500');
                errorFields.forEach(field => {
                    field.classList.remove('error', 'border-red-500');
                });

                const errorMessages = form.querySelectorAll('.error-message');
                errorMessages.forEach(message => {
                    message.textContent = '';
                    message.classList.add('hidden');
                });
            }

            function resetEditAssetForm(form) {
                resetGenericForm(form);

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

            function resetCheckoutForm(form) {
                resetGenericForm(form);

                document.getElementById('employeeDropdown').classList.remove('hidden');
                document.getElementById('locationDropdown').classList.add('hidden');

                // Reset date using Flatpickr if available
                const dateField = document.getElementById('checkout_date');
                if (dateField && dateField._flatpickr) {
                    const today = new Date();
                    dateField._flatpickr.setDate(today);
                } else if (dateField) {
                    dateField.value = new Date().toISOString().split('T')[0];
                }

                const employeeRadio = document.getElementById('employee');
                if (employeeRadio) {
                    employeeRadio.checked = true;
                }
            }

            function resetCheckinForm(form) {
                resetGenericForm(form);

                // Reset date using Flatpickr if available
                const dateField = document.getElementById('return_date');
                if (dateField && dateField._flatpickr) {
                    const today = new Date();
                    dateField._flatpickr.setDate(today);
                } else if (dateField) {
                    dateField.value = new Date().toISOString().split('T')[0];
                }
            }



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

            function createDropdownItem(text, className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer') {
                const li = document.createElement('li');
                li.className = className;
                li.textContent = text;
                return li;
            }

            function initDropdown(searchInput, dropdown, list, onSearch) {
                if (!searchInput || !dropdown || !list) return;

                searchInput.addEventListener('focus', function () {
                    dropdown.classList.remove('hidden');
                    if (list.children.length === 0) {
                        onSearch('');
                    }
                });

                searchInput.addEventListener('input', function (e) {
                    // Clear any existing timeout
                    if (this.searchTimeout) {
                        clearTimeout(this.searchTimeout);
                    }

                    // Set a new timeout
                    this.searchTimeout = setTimeout(() => {
                        // Reset pagination when searching
                        list.dataset.page = "1";
                        list.dataset.hasMoreData = "true";
                        list.dataset.searchTerm = e.target.value;
                        onSearch(e.target.value);
                    }, 300);
                });

                document.addEventListener('click', function (e) {
                    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });

                // Add scroll event listener for lazy loading
                dropdown.addEventListener('scroll', function () {
                    // Check if we're already loading or if there's no more data
                    if (list.dataset.loading === "true" || list.dataset.hasMoreData === "false") return;

                    const { scrollTop, scrollHeight, clientHeight } = dropdown;
                    // When user is near the bottom (20px threshold)
                    if (scrollTop + clientHeight >= scrollHeight - 20) {
                        // Get appropriate load more indicator
                        const loadMoreId = list.id.replace('list', 'load_more');
                        const loadMoreElement = document.getElementById(loadMoreId);

                        if (loadMoreElement) {
                            loadMoreElement.classList.remove('hidden');
                            // Load more data after a small delay for better UX
                            setTimeout(() => {
                                onSearch(list.dataset.searchTerm || '');
                            }, 100);
                        } else {
                            onSearch(list.dataset.searchTerm || '');
                        }
                    }
                });
            }

            function initSearchComponents() {
                // Building search
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

                // Brand search
                initDropdown(
                    document.getElementById('edit_brand_search'),
                    document.getElementById('edit_brand_dropdown'),
                    document.getElementById('edit_brand_list'),
                    function (searchTerm) {
                        loadBrands(
                            searchTerm,
                            document.getElementById('edit_brand_list'),
                            document.getElementById('edit_brand_loading'),
                            document.getElementById('edit_selected_brand_id'),
                            document.getElementById('edit_brand_search'),
                            document.getElementById('edit_brand_dropdown')
                        );
                    }
                );

                // Room search
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
                            const roomList = document.getElementById('edit_room_list');
                            if (roomList) {
                                roomList.innerHTML = '';
                                roomList.appendChild(createDropdownItem('Pilih gedung terlebih dahulu', 'px-4 py-2 text-gray-500 italic'));
                            }
                        }
                    }
                );

                initUserSearch(
                    document.getElementById('edit_user_search'),
                    document.getElementById('edit_user_dropdown'),
                    document.getElementById('edit_user_list'),
                    document.getElementById('edit_user_loading'),
                    document.getElementById('edit_selected_user_id')
                );

                initUserSearch(
                    document.getElementById('checkout_user_search'),
                    document.getElementById('checkout_user_dropdown'),
                    document.getElementById('checkout_user_list'),
                    document.getElementById('checkout_user_loading'),
                    document.getElementById('checkout_selected_user_id')
                );

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

            function initRoomSearch(searchInput, dropdown, roomList, loadingIndicator, selectedRoomId, selectedRoomName, roomDisplay) {
                initDropdown(searchInput, dropdown, roomList, function (searchTerm) {
                    loadRooms(searchTerm, roomList, loadingIndicator, selectedRoomId, selectedRoomName, roomDisplay, searchInput, dropdown);
                });
            }

            function initUserSearch(searchInput, dropdown, userList, loadingIndicator, selectedUserId) {
                if (!searchInput || !dropdown || !userList) return;

                searchInput.addEventListener('focus', function () {
                    dropdown.classList.remove('hidden');
                    if (userList.children.length === 0) {
                        loadUsers('', userList, loadingIndicator, selectedUserId, searchInput, dropdown);
                    }
                });

                searchInput.addEventListener('input', function (e) {
                    // Clear any existing timeout
                    if (this.searchTimeout) {
                        clearTimeout(this.searchTimeout);
                    }

                    // Set a new timeout
                    this.searchTimeout = setTimeout(() => {
                        // Reset pagination for new search
                        userList.dataset.page = "1";
                        userList.dataset.hasMoreData = "true";
                        userList.dataset.searchTerm = e.target.value;
                        loadUsers(e.target.value, userList, loadingIndicator, selectedUserId, searchInput, dropdown);
                    }, 300);
                });

                document.addEventListener('click', function (e) {
                    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });

                // Add scroll event listener for lazy loading
                dropdown.addEventListener('scroll', function () {
                    // Check if we're already loading or if there's no more data
                    if (userList.dataset.loading === "true" || userList.dataset.hasMoreData === "false") return;

                    const { scrollTop, scrollHeight, clientHeight } = dropdown;
                    // When user is near the bottom (20px threshold)
                    if (scrollTop + clientHeight >= scrollHeight - 20) {
                        loadUsers(userList.dataset.searchTerm || '', userList, loadingIndicator, selectedUserId, searchInput, dropdown);
                    }
                });
            }

            function initAssetMasterSearch(searchInput, dropdown, assetMasterList, loadingIndicator, selectedAssetMasterId, selectedIsDepreciable, depreciationFields) {
                if (!searchInput || !dropdown || !assetMasterList) return;

                searchInput.addEventListener('focus', function () {
                    dropdown.classList.remove('hidden');
                    if (assetMasterList.children.length === 0) {
                        loadAssetMasters('', assetMasterList, loadingIndicator, selectedAssetMasterId, selectedIsDepreciable, depreciationFields, searchInput, dropdown);
                    }
                });

                searchInput.addEventListener('input', function (e) {
                    // Clear any existing timeout
                    if (this.searchTimeout) {
                        clearTimeout(this.searchTimeout);
                    }

                    // Set a new timeout
                    this.searchTimeout = setTimeout(() => {
                        // Reset pagination for new search
                        assetMasterList.dataset.page = "1";
                        assetMasterList.dataset.hasMoreData = "true";
                        assetMasterList.dataset.searchTerm = e.target.value;
                        loadAssetMasters(e.target.value, assetMasterList, loadingIndicator, selectedAssetMasterId, selectedIsDepreciable, depreciationFields, searchInput, dropdown);
                    }, 300);
                });

                document.addEventListener('click', function (e) {
                    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });

                // Add scroll event listener for lazy loading
                dropdown.addEventListener('scroll', function () {
                    // Check if we're already loading or if there's no more data
                    if (assetMasterList.dataset.loading === "true" || assetMasterList.dataset.hasMoreData === "false") return;

                    const { scrollTop, scrollHeight, clientHeight } = dropdown;
                    // When user is near the bottom (20px threshold)
                    if (scrollTop + clientHeight >= scrollHeight - 20) {
                        loadAssetMasters(assetMasterList.dataset.searchTerm || '', assetMasterList, loadingIndicator, selectedAssetMasterId, selectedIsDepreciable, depreciationFields, searchInput, dropdown);
                    }
                });
            }

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
                // Setup for lazy loading
                let page = userList.dataset.page ? parseInt(userList.dataset.page) : 1;
                let isLoading = userList.dataset.loading === "true";
                let hasMoreData = userList.dataset.hasMoreData !== "false";
                let resetList = page === 1 || userList.dataset.searchTerm !== searchTerm;

                // Save current search term
                userList.dataset.searchTerm = searchTerm;

                if (isLoading) return;

                // Set loading state
                userList.dataset.loading = "true";

                // Use different loading indicators based on whether we're resetting or loading more
                if (resetList) {
                    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                    userList.innerHTML = '';
                }

                try {
                    const response = await fetch(`{{ route('user') }}?search=${encodeURIComponent(searchTerm || '')}&status=active&page=${page}&limit=20`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to fetch users');

                    const result = await response.json();
                    let users = result.users || result.data || [];

                    // Check if we have more data to load
                    hasMoreData = users.length === 20;

                    // Save next page number and has more data state
                    userList.dataset.page = page + 1;
                    userList.dataset.hasMoreData = hasMoreData.toString();

                    if (users.length === 0 && userList.children.length === 0) {
                        userList.appendChild(createDropdownItem('No users found', 'px-4 py-2 text-gray-500 italic'));
                    } else {
                        users.forEach(user => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            const itemContainer = document.createElement('div');
                            itemContainer.className = 'user-item';

                            let displayName = '';
                            let displayId = '';

                            if (user.employee_name) {
                                displayName = user.employee_name;
                                if (user.name) {
                                    displayId = user.name;
                                }
                            } else {
                                displayName = user.name || `User ID: ${user.user_id}`;
                            }

                            const nameSpan = document.createElement('div');
                            nameSpan.className = 'name text-black font-medium';
                            nameSpan.style.color = 'black';
                            nameSpan.textContent = displayName;
                            itemContainer.appendChild(nameSpan);

                            if (displayId) {
                                const idSpan = document.createElement('div');
                                idSpan.className = 'code';
                                idSpan.textContent = displayId;
                                itemContainer.appendChild(idSpan);
                            }

                            li.appendChild(itemContainer);

                            li.setAttribute('data-id', user.user_id);
                            li.setAttribute('data-employee-name', user.employee_name || '');
                            li.setAttribute('data-name', displayName);

                            li.addEventListener('click', function () {
                                selectedUserId.value = this.getAttribute('data-id');

                                const employeeName = this.getAttribute('data-employee-name');
                                if (employeeName) {
                                    searchInput.value = employeeName;
                                } else {
                                    searchInput.value = this.getAttribute('data-name');
                                }

                                dropdown.classList.add('hidden');
                            });

                            userList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading users:', error);
                    if (userList.children.length === 0) {
                        userList.appendChild(createDropdownItem('Error loading users', 'px-4 py-2 text-red-500'));
                    }
                } finally {
                    // Reset loading state
                    userList.dataset.loading = "false";
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }

            async function loadAssetMasters(searchTerm, assetMasterList, loadingIndicator, selectedAssetMasterId, selectedIsDepreciable, depreciationFields, searchInput, dropdown) {
                // Setup for lazy loading
                let page = assetMasterList.dataset.page ? parseInt(assetMasterList.dataset.page) : 1;
                let isLoading = assetMasterList.dataset.loading === "true";
                let hasMoreData = assetMasterList.dataset.hasMoreData !== "false";
                let resetList = page === 1 || assetMasterList.dataset.searchTerm !== searchTerm;

                // Get loading indicators
                const initialLoadingIndicator = loadingIndicator;
                const loadMoreIndicator = document.getElementById('edit_asset_master_load_more');

                // Save current search term
                assetMasterList.dataset.searchTerm = searchTerm;

                if (isLoading) return;

                // Set loading state
                assetMasterList.dataset.loading = "true";

                // Show appropriate loading indicator
                if (resetList) {
                    if (initialLoadingIndicator) initialLoadingIndicator.classList.remove('hidden');
                    assetMasterList.innerHTML = '';

                    if (loadMoreIndicator) loadMoreIndicator.classList.add('hidden');
                } else {
                    // Show bottom loading indicator when loading more items
                    if (loadMoreIndicator) loadMoreIndicator.classList.remove('hidden');
                }

                try {
                    const response = await fetch(`{{ route('asset-master') }}?search=${encodeURIComponent(searchTerm || '')}&page=${page}&limit=20`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to fetch asset masters');

                    const result = await response.json();
                    let assetMasters = result.masterAssets || result.data || [];

                    // Check if we have more data to load
                    hasMoreData = assetMasters.length === 20;

                    // Save next page number and has more data state
                    assetMasterList.dataset.page = page + 1;
                    assetMasterList.dataset.hasMoreData = hasMoreData.toString();

                    if (assetMasters.length === 0 && assetMasterList.children.length === 0) {
                        assetMasterList.appendChild(createDropdownItem('Master aset tidak ditemukan', 'px-4 py-2 text-gray-500 italic'));
                    } else {
                        assetMasters.forEach(item => {
                            const assetMasterName = item.asset_name || 'Unknown';
                            const assetMasterCode = item.asset_master_code || '';

                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            // Create more detailed display with code and name
                            if (assetMasterCode) {
                                const itemContainer = document.createElement('div');
                                itemContainer.className = 'asset-master-item';

                                // Create name element
                                const nameSpan = document.createElement('div');
                                nameSpan.className = 'name text-black font-medium';
                                nameSpan.style.color = 'black';
                                nameSpan.textContent = assetMasterName;

                                // Create code element
                                const codeSpan = document.createElement('div');
                                codeSpan.className = 'code text-gray-600';
                                codeSpan.textContent = assetMasterCode;

                                // Append name first, then code
                                itemContainer.appendChild(nameSpan);
                                itemContainer.appendChild(codeSpan);
                                li.appendChild(itemContainer);
                            } else {
                                li.textContent = assetMasterName;
                            }

                            const isDepreciable = item.is_depreciable === true;
                            li.setAttribute('data-id', item.asset_master_id);
                            li.setAttribute('data-name', assetMasterName);
                            li.setAttribute('data-code', assetMasterCode || '');
                            li.setAttribute('data-depreciable', isDepreciable);

                            li.addEventListener('click', function () {
                                selectedAssetMasterId.value = this.getAttribute('data-id');

                                // Display code and name in search field for better UX
                                const code = this.getAttribute('data-code');
                                const name = this.getAttribute('data-name');
                                searchInput.value = code ? `${name} - ${code}` : name;

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
                    if (assetMasterList.children.length === 0) {
                        assetMasterList.appendChild(createDropdownItem('Error loading asset masters', 'px-4 py-2 text-red-500'));
                    }
                } finally {
                    // Reset loading state
                    assetMasterList.dataset.loading = "false";

                    // Hide all loading indicators
                    if (initialLoadingIndicator) initialLoadingIndicator.classList.add('hidden');
                    if (loadMoreIndicator) loadMoreIndicator.classList.add('hidden');
                }
            }

            async function loadBuildings(searchTerm, buildingList, loadingIndicator, selectedBuildingId, searchInput, dropdown, roomSearchInput) {
                // Setup for lazy loading
                let page = buildingList.dataset.page ? parseInt(buildingList.dataset.page) : 1;
                let isLoading = buildingList.dataset.loading === "true";
                let hasMoreData = buildingList.dataset.hasMoreData !== "false";
                let resetList = page === 1 || buildingList.dataset.searchTerm !== searchTerm;

                // Save current search term
                buildingList.dataset.searchTerm = searchTerm;

                if (isLoading) return;

                // Set loading state
                buildingList.dataset.loading = "true";

                // Use different loading indicators based on whether we're resetting or loading more
                if (resetList) {
                    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                    buildingList.innerHTML = '';
                }

                try {
                    const response = await fetch(`{{ route('buildings') }}?search=${encodeURIComponent(searchTerm || '')}&page=${page}&limit=20`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load buildings');
                    }

                    const data = await response.json();
                    const buildings = data.buildings || [];

                    // Check if we have more data to load
                    hasMoreData = buildings.length === 20;

                    // Save next page number and has more data state
                    buildingList.dataset.page = page + 1;
                    buildingList.dataset.hasMoreData = hasMoreData.toString();

                    if (buildings.length === 0 && buildingList.children.length === 0) {
                        buildingList.appendChild(createDropdownItem('Tidak ada gedung yang ditemukan', 'px-4 py-2 text-gray-500'));
                    } else {
                        buildings.forEach(building => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            const buildingName = building.building_name || 'Unnamed Building';
                            const buildingId = building.building_id || '';

                            const itemContainer = document.createElement('div');
                            itemContainer.className = 'building-item';

                            const nameSpan = document.createElement('div');
                            nameSpan.className = 'name text-black font-medium';
                            nameSpan.style.color = 'black';
                            nameSpan.textContent = buildingName;

                            itemContainer.appendChild(nameSpan);
                            li.appendChild(itemContainer);

                            li.setAttribute('data-id', buildingId);
                            li.setAttribute('data-name', buildingName);

                            li.addEventListener('click', function () {
                                selectedBuildingId.value = this.getAttribute('data-id');
                                searchInput.value = this.getAttribute('data-name');

                                if (roomSearchInput) {
                                    roomSearchInput.disabled = false;
                                    roomSearchInput.placeholder = "Cari ruangan...";

                                    // Clear room selection
                                    const isEditModal = roomSearchInput.id === 'edit_room_search';
                                    const roomIdField = isEditModal ? 'edit_selected_room_id' : 'pinjam_selected_room_id';
                                    document.getElementById(roomIdField).value = '';
                                    roomSearchInput.value = '';

                                    const roomLoadingId = isEditModal ? 'edit_room_loading' : 'pinjam_room_loading';
                                    const roomLoadingIndicator = document.getElementById(roomLoadingId);
                                    if (roomLoadingIndicator) {
                                        roomLoadingIndicator.classList.remove('hidden');
                                    }

                                    const roomListId = isEditModal ? 'edit_room_list' : 'pinjam_room_list';
                                    const roomDropdownId = isEditModal ? 'edit_room_dropdown' : 'pinjam_room_dropdown';

                                    loadRoomsForBuilding(
                                        '',
                                        this.getAttribute('data-id'),
                                        document.getElementById(roomListId),
                                        document.getElementById(roomLoadingId),
                                        document.getElementById(roomIdField),
                                        roomSearchInput,
                                        document.getElementById(roomDropdownId)
                                    );

                                    document.getElementById(roomDropdownId).classList.remove('hidden');
                                }

                                dropdown.classList.add('hidden');
                            });

                            buildingList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading buildings:', error);
                    if (buildingList.children.length === 0) {
                        buildingList.appendChild(createDropdownItem('Galat memuat gedung', 'px-4 py-2 text-red-500'));
                    }
                } finally {
                    // Reset loading state
                    buildingList.dataset.loading = "false";
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }

            const roomSearchInput = document.getElementById('pinjam_room_search');
            if (roomSearchInput) {
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

            async function loadRoomsForBuilding(searchTerm, buildingId, roomList, loadingIndicator, selectedRoomId, searchInput, dropdown) {
                if (!buildingId) {
                    searchInput.value = '';
                    searchInput.placeholder = 'Pilih gedung terlebih dahulu';
                    searchInput.disabled = true;
                    return;
                }

                searchInput.disabled = false;
                searchInput.placeholder = "Cari ruangan...";

                // Setup for lazy loading
                let page = roomList.dataset.page ? parseInt(roomList.dataset.page) : 1;
                let isLoading = roomList.dataset.loading === "true";
                let resetList = page === 1 || roomList.dataset.searchTerm !== searchTerm;

                // Save current search term
                roomList.dataset.searchTerm = searchTerm;

                if (isLoading) return;

                // Set loading state
                roomList.dataset.loading = "true";

                // Show/hide appropriate loading indicators
                if (resetList) {
                    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                    roomList.innerHTML = '';
                }

                // Show dropdown
                if (dropdown) dropdown.classList.remove('hidden');

                try {
                    const apiUrl = `{{ route('rooms') }}?building_id=${encodeURIComponent(buildingId)}&search=${encodeURIComponent(searchTerm || '')}&page=${page}&limit=20`;
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
                    } else if (data.rooms && Array.isArray(data.rooms)) {
                        rooms = data.rooms;
                    } else {
                        console.error('Unexpected API response format:', data);
                        throw new Error('Invalid response format from server');
                    }

                    // Check if we have more data to load
                    let hasMoreData = rooms.length === 20;

                    // Save next page number and has more data state
                    roomList.dataset.page = page + 1;
                    roomList.dataset.hasMoreData = hasMoreData.toString();

                    if (rooms.length === 0 && (!roomList.children.length || resetList)) {
                        roomList.appendChild(createDropdownItem('Tidak ada ruangan ditemukan untuk gedung ini', 'px-4 py-2 text-gray-500 italic'));
                    } else {
                        rooms.forEach(room => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            const roomName = room.room_name || room.name || '';
                            const roomId = room.room_id || room.id || '';

                            if (!roomName || !roomId) {
                                console.warn('Room missing required properties:', room);
                                return;
                            }

                            // Create the room item with styling similar to building and user items
                            const itemContainer = document.createElement('div');
                            itemContainer.className = 'room-item';

                            const nameSpan = document.createElement('div');
                            nameSpan.className = 'name text-black font-medium';
                            nameSpan.style.color = 'black';
                            nameSpan.textContent = roomName;

                            itemContainer.appendChild(nameSpan);
                            li.appendChild(itemContainer);

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
                    if (!roomList.children.length || resetList) {
                        roomList.innerHTML = '';
                        roomList.appendChild(createDropdownItem(`Error: ${error.message}`, 'px-4 py-2 text-red-500'));
                    }
                } finally {
                    // Reset loading state
                    roomList.dataset.loading = "false";

                    // Hide loading indicator
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }

            async function loadBrands(searchTerm, brandList, loadingIndicator, selectedBrandId, searchInput, dropdown) {
                // Setup for lazy loading
                let page = brandList.dataset.page ? parseInt(brandList.dataset.page) : 1;
                let isLoading = brandList.dataset.loading === "true";
                let hasMoreData = brandList.dataset.hasMoreData !== "false";
                let resetList = page === 1 || brandList.dataset.searchTerm !== searchTerm;

                // Save current search term
                brandList.dataset.searchTerm = searchTerm;

                if (isLoading) return;

                // Set loading state
                brandList.dataset.loading = "true";

                // Show loading indicator
                if (resetList) {
                    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                    brandList.innerHTML = '';
                }

                try {
                    // Construct URL with query parameters
                    let queryParams = new URLSearchParams();
                    queryParams.append('json', 'true');
                    queryParams.append('page', page);
                    queryParams.append('limit', 20);
                    if (searchTerm && searchTerm.trim()) {
                        queryParams.append('search', searchTerm.trim());
                    }

                    const response = await fetch(`/brands?${queryParams.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load brands');
                    }

                    const data = await response.json();
                    let brands = [];
                    let pagination = null;

                    // Handle different response formats
                    if (Array.isArray(data)) {
                        brands = data;
                    } else if (data.brands && Array.isArray(data.brands)) {
                        brands = data.brands;
                        pagination = data.pagination || null;
                    } else if (data.data && Array.isArray(data.data)) {
                        brands = data.data;
                        pagination = data.pagination || data.meta || null;
                    }

                    // Check if we have more data to load
                    if (pagination) {
                        hasMoreData = pagination.current_page < pagination.last_page;
                    } else {
                        hasMoreData = brands.length >= 20;
                    }

                    // Save next page number and has more data state
                    brandList.dataset.page = page + 1;
                    brandList.dataset.hasMoreData = hasMoreData.toString();

                    if (brands.length === 0 && brandList.children.length === 0) {
                        brandList.appendChild(createDropdownItem('Tidak ada merk yang ditemukan', 'px-4 py-2 text-gray-500'));
                    } else {
                        brands.forEach(brand => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';

                            const brandName = brand.brand_name || 'Unnamed Brand';
                            const brandId = brand.brand_id || '';

                            const itemContainer = document.createElement('div');
                            itemContainer.className = 'brand-item';

                            const nameSpan = document.createElement('div');
                            nameSpan.className = 'name text-black font-medium';
                            nameSpan.style.color = 'black';
                            nameSpan.textContent = brandName;

                            itemContainer.appendChild(nameSpan);
                            li.appendChild(itemContainer);

                            li.setAttribute('data-id', brandId);
                            li.setAttribute('data-name', brandName);

                            li.addEventListener('click', function () {
                                selectedBrandId.value = this.getAttribute('data-id');
                                searchInput.value = this.getAttribute('data-name');
                                dropdown.classList.add('hidden');
                            });

                            brandList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading brands:', error);
                    if (brandList.children.length === 0) {
                        brandList.appendChild(createDropdownItem('Galat memuat merk', 'px-4 py-2 text-red-500'));
                    }
                } finally {
                    brandList.dataset.loading = "false";
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }

            initSearchComponents();

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

            const closeButtons = document.querySelectorAll('.close-modal');
            closeButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const modal = this.closest('[id$="Modal"]');
                    const content = modal.querySelector('[id$="ModalContent"]');
                    if (modal && content) closeModal(modal, content);
                });
            });

            Object.keys(modals).forEach(key => {
                const modalObj = modals[key];
                if (modalObj.btn && modalObj.modal && modalObj.content) {
                    modalObj.btn.addEventListener('click', function (e) {
                        if (e.preventDefault) e.preventDefault();

                        if (key === 'edit') {
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
                            openModal(modalObj.modal, modalObj.content);
                        }
                    });

                    modalObj.modal.addEventListener('click', function (e) {
                        if (e.target === this) {
                            closeModal(modalObj.modal, modalObj.content);
                        }
                    });

                    if (modalObj.form) {
                        if (modalObj.form.id === 'checkoutAssetForm') {
                            modalObj.form.action = "{{ route('asset.checkout') }}";
                        } else if (modalObj.form.id === 'checkinAssetForm') {
                            modalObj.form.action = "{{ route('asset.checkin') }}";
                        }

                        const closeBtn = modalObj.modal.querySelector('.close-modal');
                        if (closeBtn) {
                            closeBtn.addEventListener('click', function () {
                                closeModal(modalObj.modal, modalObj.content);
                            });
                        }
                    }
                }
            });

            const depreciableToggle = document.getElementById('edit_is_depreciable');
            const depreciationFields = document.getElementById('edit_depreciation_fields');

            if (depreciableToggle && depreciationFields) {
                depreciableToggle.addEventListener('change', function () {
                    const isChecked = this.checked;
                    const statusText = document.querySelector('.depreciation-status');

                    if (statusText) statusText.textContent = isChecked ? 'Yes' : 'No';
                    toggleDepreciationFields(depreciationFields, isChecked);

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

            const purchaseCostInput = document.getElementById('edit_purchase_cost');
            if (purchaseCostInput) {
                purchaseCostInput.addEventListener('input', function () {
                    formatCurrency(this);

                    if (document.getElementById('edit_selected_is_depreciable').value === 'true') {
                        const acquisitionCostInput = document.getElementById('edit_acquisition_cost');
                        if (acquisitionCostInput) {
                            acquisitionCostInput.value = this.value;
                        }
                    }
                });
            }

            initSearchComponents();

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

            const editDepreciationFields = document.getElementById('edit_depreciation_fields');
            const isDepreciable = '{{ $asset["asset_master"]["is_depreciable"] ?? false }}' === '1';
            if (editDepreciationFields) {
                toggleDepreciationFields(editDepreciationFields, isDepreciable);
            }

            const checkoutDateInput = document.querySelector('input[name="checkout_date"]');
            if (checkoutDateInput) {
                checkoutDateInput.value = new Date().toISOString().split('T')[0];
            }

            window.setupWithData = function (asset) {
                const form = document.getElementById('editAssetForm');
                if (!form) {
                    console.error('Edit asset form not found');
                    return;
                }

                form.action = "{{ url('/assets') }}/" + asset.asset_id;
                form.reset();

                document.getElementById('edit_serial_number').value = asset.serial_number || '';

                const purchaseCost = asset.purchase_cost || '0';
                const formattedPurchaseCost = Number(purchaseCost).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                document.getElementById('edit_purchase_cost').value = formattedPurchaseCost;

                if (asset.purchase_date) {
                    const purchaseDateInput = document.getElementById('edit_purchase_date');
                    if (purchaseDateInput && purchaseDateInput._flatpickr) {
                        purchaseDateInput._flatpickr.setDate(asset.purchase_date.split(' ')[0]);
                    } else {
                        purchaseDateInput.value = asset.purchase_date.split(' ')[0];
                    }
                }

                if (asset.warranty_end_date) {
                    const warrantyEndInput = document.getElementById('edit_warranty_end_date');
                    if (warrantyEndInput && warrantyEndInput._flatpickr) {
                        warrantyEndInput._flatpickr.setDate(asset.warranty_end_date.split(' ')[0]);
                    } else {
                        warrantyEndInput.value = asset.warranty_end_date.split(' ')[0];
                    }
                }

                setSelectValue('edit_condition', asset.condition);

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

                const buildingName = asset.building_name || '';

                document.getElementById('edit_building_search').value = buildingName;

                const roomSearch = document.getElementById('edit_room_search');
                if (roomSearch) {
                    roomSearch.disabled = false;
                    roomSearch.placeholder = "Cari ruangan...";
                }

                const roomId = asset.room_id || '';
                const roomName = asset.room_name || '';
                document.getElementById('edit_selected_room_id').value = roomId;
                document.getElementById('edit_room_search').value = roomName;

                if (buildingName) {
                    let buildingId = asset.building_id || '';

                    if (buildingId) {
                        document.getElementById('edit_selected_building_id').value = buildingId;

                        loadRoomsForBuilding(
                            '',
                            buildingId,
                            document.getElementById('edit_room_list'),
                            document.getElementById('edit_room_loading'),
                            document.getElementById('edit_selected_room_id'),
                            roomSearch,
                            document.getElementById('edit_room_dropdown')
                        );
                    } else {
                        fetch(`{{ route('buildings') }}?search=${encodeURIComponent(buildingName)}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                const buildings = data.buildings || [];
                                const matchingBuilding = buildings.find(b =>
                                    b.building_name.toLowerCase() === buildingName.toLowerCase()
                                );

                                if (matchingBuilding) {
                                    document.getElementById('edit_selected_building_id').value = matchingBuilding.building_id;

                                    loadRoomsForBuilding(
                                        '',
                                        matchingBuilding.building_id,
                                        document.getElementById('edit_room_list'),
                                        document.getElementById('edit_room_loading'),
                                        document.getElementById('edit_selected_room_id'),
                                        roomSearch,
                                        document.getElementById('edit_room_dropdown')
                                    );
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching buildings:', error);
                            });
                    }
                }

                if (asset.user_id) {
                    document.getElementById('edit_selected_user_id').value = asset.user_id;

                    let userDisplay = '';
                    if (asset.user) {
                        if (asset.user.employee_name) {
                            userDisplay = asset.user.employee_name;
                            if (asset.user.name) userDisplay += ` - ${asset.user.name}`;
                        } else if (asset.user.name) {
                            userDisplay = asset.user.name;
                        }
                    } else if (asset.employee_name) {
                        userDisplay = asset.employee_name;
                    }

                    document.getElementById('edit_user_search').value = userDisplay;
                }

                document.getElementById('edit_model').value = asset.model || '';

                const brandId = asset.brand_id || '';
                const brandName = asset.brand_name || '';
                document.getElementById('edit_selected_brand_id').value = brandId;
                document.getElementById('edit_brand_search').value = brandName;

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

                            const acquisitionCost = asset.depreciation.acquisition_cost || asset.purchase_cost || '0';
                            const formattedAcquisitionCost = Number(acquisitionCost).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            document.getElementById('edit_acquisition_cost').value = formattedAcquisitionCost;

                            const salvageValue = asset.depreciation.salvage_value || '0';
                            const formattedSalvageValue = Number(salvageValue).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            document.getElementById('edit_salvage_value').value = formattedSalvageValue;

                            document.getElementById('edit_asset_life_months').value = asset.depreciation.asset_life_months || '12';

                            if (asset.depreciation.date_acquired || asset.purchase_date) {
                                const dateAcquired = asset.depreciation.date_acquired || asset.purchase_date;
                                const dateAcquiredInput = document.getElementById('edit_date_acquired');

                                if (dateAcquiredInput && dateAcquiredInput._flatpickr) {
                                    dateAcquiredInput._flatpickr.setDate(dateAcquired.split(' ')[0]);
                                } else {
                                    dateAcquiredInput.value = dateAcquired.split(' ')[0];
                                }
                            }
                        } else {
                            document.getElementById('edit_depreciation_method').value = 'Straight Line';

                            const acquisitionCost = asset.purchase_cost || '0';
                            const formattedAcquisitionCost = Number(acquisitionCost).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            document.getElementById('edit_acquisition_cost').value = formattedAcquisitionCost;

                            document.getElementById('edit_salvage_value').value = '0';

                            document.getElementById('edit_asset_life_months').value = '12';

                            if (asset.purchase_date) {
                                const dateAcquiredInput = document.getElementById('edit_date_acquired');
                                if (dateAcquiredInput && dateAcquiredInput._flatpickr) {
                                    dateAcquiredInput._flatpickr.setDate(asset.purchase_date.split(' ')[0]);
                                } else {
                                    dateAcquiredInput.value = asset.purchase_date.split(' ')[0];
                                }
                            }
                        }
                    }
                }
            };

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

            function setupFormSubmissionHandling(formId, loadingText = 'Processing...') {
                const form = document.getElementById(formId);
                if (!form) return;

                form.addEventListener('submit', function (event) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>${loadingText}</span></div>`;

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

            setupFormSubmissionHandling('reportLostForm', 'Melaporkan...');
            setupFormSubmissionHandling('foundAssetForm', 'Melaporkan ditemukan...');

            document.getElementById('checkoutAssetForm')?.addEventListener('submit', function (event) {
                event.preventDefault();

                if (validateCheckoutForm()) {
                    const submitBtn = this.querySelector('button[type="submit"]');

                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Meminjam...</span></div>`;

                        setTimeout(() => {
                            if (submitBtn.disabled) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            }
                        }, 10000);
                    }

                    this.submit();
                }
            });

            document.getElementById('checkinAssetForm')?.addEventListener('submit', function (event) {
                event.preventDefault();

                if (validateCheckinForm()) {
                    const submitBtn = this.querySelector('button[type="submit"]');

                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Mengembalikan...</span></div>`;

                        setTimeout(() => {
                            if (submitBtn.disabled) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            }
                        }, 10000);
                    }

                    this.submit();
                }
            });

            document.getElementById('disposeAssetForm')?.addEventListener('submit', function (event) {
                event.preventDefault();

                if (validateDisposeForm()) {
                    const submitBtn = this.querySelector('button[type="submit"]');

                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Menghapuskan...</span></div>`;

                        setTimeout(() => {
                            if (submitBtn.disabled) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            }
                        }, 10000);
                    }

                    this.submit();
                }
            });

            function validateEditForm() {
                const form = document.getElementById('editAssetForm');
                if (!form) return true;

                const assetMasterSearch = document.getElementById('edit_asset_master_search');
                const selectedAssetMasterId = document.getElementById('edit_selected_asset_master_id');
                const buildingSearch = document.getElementById('edit_building_search');
                const selectedBuildingId = document.getElementById('edit_selected_building_id');
                const roomSearch = document.getElementById('edit_room_search');
                const selectedRoomId = document.getElementById('edit_selected_room_id');
                const brandSearch = document.getElementById('edit_brand_search');
                const selectedBrandId = document.getElementById('edit_selected_brand_id');
                const modelInput = document.getElementById('edit_model');
                const depreciationFields = document.getElementById('edit_depreciation_fields');

                const isAssetMasterValid = validateField(assetMasterSearch, selectedAssetMasterId.value ? true : false);
                if (!isAssetMasterValid) {
                    const errorElement = assetMasterSearch.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = 'Master aset harus dipilih';
                        errorElement.classList.remove('hidden');
                    }
                }

                const isBuildingValid = validateField(buildingSearch, selectedBuildingId.value ? true : false);
                if (!isBuildingValid) {
                    const errorElement = buildingSearch.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = 'Gedung harus dipilih';
                        errorElement.classList.remove('hidden');
                    }
                }

                const isRoomValid = validateField(roomSearch, selectedRoomId.value ? true : false);
                if (!isRoomValid) {
                    const errorElement = roomSearch.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = 'Ruangan harus dipilih';
                        errorElement.classList.remove('hidden');
                    }
                }

                const isBrandValid = validateField(brandSearch, selectedBrandId.value ? true : false);
                if (!isBrandValid) {
                    const errorElement = brandSearch.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = 'Merk harus dipilih';
                        errorElement.classList.remove('hidden');
                    }
                }

                const isModelValid = validateField(modelInput);
                if (!isModelValid) {
                    const errorElement = modelInput.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = 'Model harus diisi';
                        errorElement.classList.remove('hidden');
                    }
                }

                let isValid = isAssetMasterValid && isBuildingValid && isRoomValid && isBrandValid && isModelValid;

                const isDepreciable = !depreciationFields.classList.contains('hidden');
                if (isDepreciable) {
                    const depreciation_method = document.getElementById('edit_depreciation_method');
                    const acquisition_cost = document.getElementById('edit_acquisition_cost');
                    const salvage_value = document.getElementById('edit_salvage_value');
                    const asset_life_months = document.getElementById('edit_asset_life_months');
                    const date_acquired = document.getElementById('edit_date_acquired');

                    const isDepreciationMethodValid = validateField(depreciation_method);
                    if (!isDepreciationMethodValid) {
                        const errorElement = depreciation_method.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) {
                            errorElement.textContent = 'Metode penyusutan harus dipilih';
                            errorElement.classList.remove('hidden');
                        }
                    }

                    const isAcquisitionCostValid = validateField(acquisition_cost);
                    if (!isAcquisitionCostValid) {
                        const errorElement = acquisition_cost.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) {
                            errorElement.textContent = 'Biaya perolehan harus diisi';
                            errorElement.classList.remove('hidden');
                        }
                    }

                    const isSalvageValueValid = validateField(salvage_value);
                    if (!isSalvageValueValid) {
                        const errorElement = salvage_value.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) {
                            errorElement.textContent = 'Nilai sisa harus diisi';
                            errorElement.classList.remove('hidden');
                        }
                    }

                    const isAssetLifeMonthsValid = validateField(asset_life_months);
                    if (!isAssetLifeMonthsValid) {
                        const errorElement = asset_life_months.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) {
                            errorElement.textContent = 'Usia aset harus diisi';
                            errorElement.classList.remove('hidden');
                        }
                    }

                    const isDateAcquiredValid = validateField(date_acquired);
                    if (!isDateAcquiredValid) {
                        const errorElement = date_acquired.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) {
                            errorElement.textContent = 'Tanggal pengadaan harus diisi';
                            errorElement.classList.remove('hidden');
                        }
                    }

                    isValid = isValid && isDepreciationMethodValid && isAcquisitionCostValid &&
                        isSalvageValueValid && isAssetLifeMonthsValid && isDateAcquiredValid;
                }

                if (!isValid) {
                    showToast('Silakan lengkapi semua field yang wajib diisi', 'error');
                }

                return isValid;
            }

            function validateCheckoutForm() {
                const form = document.getElementById('checkoutAssetForm');
                if (!form) return true;

                const employeeRadio = document.getElementById('employee');
                const isEmployeeSelected = employeeRadio && employeeRadio.checked;

                let isValid = true;

                if (isEmployeeSelected) {
                    const userSearch = document.getElementById('checkout_user_search');
                    const selectedUserId = document.getElementById('checkout_selected_user_id');
                    const isUserValid = validateField(userSearch, selectedUserId.value ? true : false);

                    if (!isUserValid) {
                        const errorElement = userSearch.closest('.space-y-2')?.querySelector('.error-message');
                        if (!errorElement) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message text-red-500 text-sm mt-1';
                            errorDiv.textContent = 'Silakan pilih karyawan';
                            userSearch.parentNode.appendChild(errorDiv);
                        } else {
                            errorElement.classList.remove('hidden');
                            errorElement.textContent = 'Silakan pilih karyawan';
                        }
                    }

                    isValid = isUserValid;
                } else {
                    const buildingSearch = document.getElementById('pinjam_building_search');
                    const selectedBuildingId = document.getElementById('pinjam_selected_building_id');
                    const roomSearch = document.getElementById('pinjam_room_search');
                    const selectedRoomId = document.getElementById('pinjam_selected_room_id');

                    const isBuildingValid = validateField(buildingSearch, selectedBuildingId.value ? true : false);
                    const isRoomValid = validateField(roomSearch, selectedRoomId.value ? true : false);

                    if (!isBuildingValid) {
                        const errorElement = buildingSearch.closest('.space-y-2')?.querySelector('.error-message');
                        if (!errorElement) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message text-red-500 text-sm mt-1';
                            errorDiv.textContent = 'Silakan pilih gedung';
                            buildingSearch.parentNode.appendChild(errorDiv);
                        } else {
                            errorElement.classList.remove('hidden');
                            errorElement.textContent = 'Silakan pilih gedung';
                        }
                    }

                    if (!isRoomValid) {
                        const errorElement = roomSearch.closest('.space-y-2')?.querySelector('.error-message');
                        if (!errorElement) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message text-red-500 text-sm mt-1';
                            errorDiv.textContent = 'Silakan pilih ruangan';
                            roomSearch.parentNode.appendChild(errorDiv);
                        } else {
                            errorElement.classList.remove('hidden');
                            errorElement.textContent = 'Silakan pilih ruangan';
                        }
                    }

                    isValid = isBuildingValid && isRoomValid;
                }

                if (!isValid) {
                    showToast('Silakan lengkapi semua field yang wajib diisi', 'error');
                }

                return isValid;
            }

            function validateCheckinForm() {
                const form = document.getElementById('checkinAssetForm');
                if (!form) return true;

                let isValid = true;

                const conditionSelect = document.getElementById('return_condition');
                const isConditionValid = validateField(conditionSelect);

                if (!isConditionValid) {
                    const errorElement = conditionSelect.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.classList.remove('hidden');
                    }
                }

                isValid = isConditionValid;

                if (!isValid) {
                    showToast('Silakan lengkapi semua field yang wajib diisi', 'error');
                }

                return isValid;
            }

            function validateDisposeForm() {
                const form = document.getElementById('disposeAssetForm');
                if (!form) return true;

                let isValid = true;

                const disposalMethodSelect = form.querySelector('select[name="disposal_method"]');
                const isMethodValid = validateField(disposalMethodSelect);

                const disposalReason = form.querySelector('textarea[name="disposal_reason"]');
                const isReasonValid = validateField(disposalReason);

                if (!isMethodValid) {
                    const errorElement = disposalMethodSelect.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.classList.remove('hidden');
                    }
                }

                if (!isReasonValid) {
                    const errorElement = disposalReason.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.classList.remove('hidden');
                    }
                }

                isValid = isMethodValid && isReasonValid;

                if (!isValid) {
                    showToast('Silakan lengkapi semua field yang wajib diisi', 'error');
                }

                return isValid;
            }

            function validateField(field, customCheck = null) {
                if (!field) return true;

                let errorElement = field.closest('.space-y-2')?.querySelector('.error-message');
                if (!errorElement) {
                    errorElement = field.closest('.mb-5')?.querySelector('.error-message');
                }

                if (field.tagName.toLowerCase() === 'select') {
                    if (!field.value) {
                        field.classList.add('border-red-500');
                        if (errorElement) errorElement.classList.remove('hidden');
                        return false;
                    } else {
                        field.classList.remove('border-red-500');
                        if (errorElement) errorElement.classList.add('hidden');
                        return true;
                    }
                } else if (customCheck !== null) {
                    if (!customCheck) {
                        field.classList.add('border-red-500');
                        if (errorElement) errorElement.classList.remove('hidden');
                        return false;
                    } else {
                        field.classList.remove('border-red-500');
                        if (errorElement) errorElement.classList.add('hidden');
                        return true;
                    }
                } else {
                    if (!field.value.trim()) {
                        field.classList.add('border-red-500');
                        if (errorElement) errorElement.classList.remove('hidden');
                        return false;
                    } else {
                        field.classList.remove('border-red-500');
                        if (errorElement) errorElement.classList.add('hidden');
                        return true;
                    }
                }
            }

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

            clearErrorOnInput('edit_asset_master_search');
            clearErrorOnInput('edit_building_search');
            clearErrorOnInput('edit_room_search');
            clearErrorOnInput('edit_serial_number');
            clearErrorOnInput('edit_purchase_date');
            clearErrorOnInput('edit_purchase_cost');
            clearErrorOnInput('edit_warranty_end_date');
            clearErrorOnInput('edit_condition');
            clearErrorOnInput('edit_user_search');
            clearErrorOnInput('edit_brand_search');
            clearErrorOnInput('edit_model');
            clearErrorOnInput('edit_depreciation_method');
            clearErrorOnInput('edit_acquisition_cost');
            clearErrorOnInput('edit_salvage_value');
            clearErrorOnInput('edit_asset_life_months');
            clearErrorOnInput('edit_date_acquired');
            clearErrorOnInput('checkout_user_search');
            clearErrorOnInput('pinjam_building_search');
            clearErrorOnInput('pinjam_room_search');
            clearErrorOnInput('return_condition');

            const returnNotes = document.querySelector('textarea[name="return_notes"]');
            if (returnNotes) {
                returnNotes.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });
            }

            document.getElementById('employee')?.addEventListener('change', function () {
                if (this.checked) {
                    const buildingSearch = document.getElementById('pinjam_building_search');
                    const roomSearch = document.getElementById('pinjam_room_search');
                    if (buildingSearch) {
                        buildingSearch.classList.remove('border-red-500');
                        const errorElement = buildingSearch.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    }
                    if (roomSearch) {
                        roomSearch.classList.remove('border-red-500');
                        const errorElement = roomSearch.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    }
                }
            });

            document.getElementById('location')?.addEventListener('change', function () {
                if (this.checked) {
                    const userSearch = document.getElementById('checkout_user_search');
                    if (userSearch) {
                        userSearch.classList.remove('border-red-500');
                        const errorElement = userSearch.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) errorElement.classList.add('hidden');
                    }
                }
            });

            document.getElementById('editAssetForm')?.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission

                // Process currency inputs
                const formData = new FormData(this);

                // Parse currency inputs to ensure full values
                this.querySelectorAll('.currency-input').forEach(input => {
                    if (input.value) {
                        const numericValue = parseFormattedNumber(input.value);
                        formData.set(input.name, numericValue);
                    }
                });

                // Process specific fields for depreciation
                const depreciationFieldsDiv = document.getElementById('edit_depreciation_fields');
                if (depreciationFieldsDiv && !depreciationFieldsDiv.classList.contains('hidden')) {
                    const fieldsToCheck = [
                        { id: 'edit_depreciation_method', name: 'depreciation_method' },
                        { id: 'edit_asset_life_months', name: 'asset_life_months' },
                        { id: 'edit_date_acquired', name: 'date_acquired' }
                    ];

                    fieldsToCheck.forEach(field => {
                        const element = document.getElementById(field.id);
                        if (element && element.value && !formData.has(field.name)) {
                            formData.append(field.name, element.value);
                        }
                    });
                }

                if (validateEditForm()) {
                    const submitBtn = this.querySelector('button[type="submit"]');

                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Memperbarui...</span></div>`;

                        // Add method override for PUT
                        formData.append('_method', 'PUT');

                        // Send AJAX request
                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalText;

                            if (data.success) {
                                const modal = document.getElementById('editAssetModal');
                                closeModal(modal, modal.querySelector('[id$="ModalContent"]'));
                                resetEditAssetForm(document.getElementById('editAssetForm'));
                                showToast(data.message || 'Aset berhasil diperbarui', 'success');

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                // Handle validation errors
                                let errorMessage = data.message || 'Terjadi kesalahan saat memperbarui aset';

                                if (data.errors) {
                                    // Display each validation error
                                    Object.entries(data.errors).forEach(([field, errors]) => {
                                        if (Array.isArray(errors)) {
                                            const fieldInput = document.querySelector(`[name="${field}"]`);
                                            if (fieldInput) {
                                                fieldInput.classList.add('border-red-500');
                                                const errorElement = fieldInput.closest('.space-y-2')?.querySelector('.error-message');
                                                if (errorElement) {
                                                    errorElement.textContent = errors[0];
                                                    errorElement.classList.remove('hidden');
                                                }
                                            }
                                        }
                                    });
                                }

                                showToast(errorMessage, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalText;
                            showToast('Terjadi kesalahan saat menghubungi server', 'error');
                        });
                    }
                }
            });

            clearErrorOnInput('return_condition');

            const disposalReason = document.querySelector('textarea[name="disposal_reason"]');
            if (disposalReason) {
                disposalReason.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });
            }

            const disposalMethod = document.querySelector('select[name="disposal_method"]');
            if (disposalMethod) {
                disposalMethod.addEventListener('change', function () {
                    this.classList.remove('border-red-500');
                    const errorElement = this.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                });
            }

            function validateLostForm() {
                const form = document.getElementById('reportLostForm');
                if (!form) return true;

                let isValid = true;

                const lossDate = form.querySelector('input[name="loss_date"]');
                const isDateValid = validateField(lossDate);

                if (!isDateValid) {
                    const errorElement = lossDate.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.classList.remove('hidden');
                    }
                }

                isValid = isDateValid;

                if (!isValid) {
                    showToast('Silakan lengkapi semua field yang wajib diisi', 'error');
                }

                return isValid;
            }

            document.getElementById('reportLostForm')?.addEventListener('submit', function (event) {
                event.preventDefault();

                if (validateLostForm()) {
                    const submitBtn = this.querySelector('button[type="submit"]');

                    if (submitBtn && !submitBtn.disabled) {
                        const originalText = submitBtn.innerHTML;

                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        submitBtn.innerHTML = `<div class="flex items-center justify-center"><div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Melaporkan...</span></div>`;

                        setTimeout(() => {
                            if (submitBtn.disabled) {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;
                            }
                        }, 10000);
                    }

                    this.submit();
                }
            });
        });
    </script>
@endpush
