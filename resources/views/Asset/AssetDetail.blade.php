@extends('Layout.app')

@section('title', 'Asset Detail')

@section('content')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Asset Details Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h1 class="text-xl md:text-2xl lg:text-[32px] font-semibold text-[#213268]">DETAIL ASSET</h1>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-2 md:gap-3 w-full md:w-auto justify-start md:justify-end">
                            @if($asset['current_status'] === 'dispose')
                                <!-- When status is disposed, show only Edit button -->
                                <a href="javascript:void(0)" id="editAssetBtn"
                                    class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    <span class="text-sm">Ubah</span>
                                </a>
                            @elseif($asset['current_status'] === 'available')
                                <!-- When status is available: Check Out, Dispose, Lost, Edit buttons -->
                                <a href="javascript:void(0)" id="checkoutAssetBtn"
                                    class="flex items-center justify-center gap-1 md:gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-[#203268] rounded-lg text-white text-xs md:text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Pinjam</span>
                                </a>
                                <a href="javascript:void(0)" id="disposeAssetBtn"
                                    class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="text-sm">Hapuskan</span>
                                </a>
                                <a href="javascript:void(0)" id="lostAssetBtn"
                                    class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span class="text-sm">Hilang</span>
                                </a>
                                <a href="javascript:void(0)" id="editAssetBtn"
                                    class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    <span class="text-sm">Ubah</span>
                                </a>
                            @elseif($asset['current_status'] === 'check out')
                                <!-- When status is check out: Check In, Dispose, Lost, Edit buttons -->
                                <a href="javascript:void(0)" id="checkinAssetBtn"
                                    class="flex items-center justify-center gap-1 md:gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-[#203268] rounded-lg text-white text-xs md:text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Kembalikan</span>
                                </a>
                                <a href="#"
                                    class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="text-sm">Hapuskan</span>
                                </a>
                                <a href="javascript:void(0)" id="lostAssetBtn"
                                    class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span class="text-sm">Hilang</span>
                                </a>
                                <a href="javascript:void(0)" id="editAssetBtn"
                                    class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    <span class="text-sm">Ubah</span>
                                </a>
                            @elseif($asset['current_status'] === 'lost')
                                <!-- When status is lost: Found, Edit buttons -->
                                <a href="javascript:void(0)" id="foundAssetBtn"
                                    class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <span class="text-sm">Ditemukan</span>
                                </a>
                                <a href="javascript:void(0)" id="editAssetBtn"
                                    class="flex items-center justify-center gap-2 px-4 py-2 bg-[#203268] rounded-lg text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    <span class="text-sm">Ubah</span>
                                </a>
                            @endif
                            <!-- Add this button alongside the other action buttons -->
                            <a href="{{ route('asset.export-pdf', ['id' => $asset['asset_id'] ?? '']) }}"
                               class="flex items-center px-3 py-2 bg-[#213268] text-white rounded-lg text-sm hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200"
                               target="_blank">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
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
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#213268]"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                                <img src="{{ $qrImageUrl }}" alt="Asset QR Code"
                                                    class="w-full h-full object-contain"
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

                                        <div
                                            class="absolute top-1 left-1 text-xs font-semibold bg-gray-200 rounded-lg px-2 py-1">
                                            {{ $asset['asset_code'] ?? '-' }}
                                        </div>

                                        <!-- Icon to flip back to image -->
                                        <button
                                            class="flip-btn absolute top-2 right-2 p-2 bg-white bg-opacity-70 hover:bg-opacity-100 rounded-full shadow-md z-10 transition-all duration-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#213268]"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                <p class="text-sm font-medium text-center mt-2">{{ $asset['asset_master_name'] ?? $asset['asset_master']['asset_name'] ?? '-' }}</p>
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
                                        }
                                    }
                                @endphp
                                <div
                                    class="{{ $statusColor }} py-0.5 px-3 rounded-md w-full max-w-[120px] text-center mt-2">
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
                        <div class="flex-1 mt-4 lg:mt-0">
                            <div class="mb-8">
                                <h2 class="text-xl font-semibold text-[#203268] mb-5">Informasi Master Asset</h2>
                                <div class="grid grid-cols-1 gap-4">
                                <div class="space-y-4">
                                        <div class="flex items-center">
                                            <span class="w-[180px] font-semibold">Asset Master Code</span>
                                            <span>{{ $asset['asset_master']['asset_master_code'] ?? '-' }}</span>
                                    </div>
                                        <div class="flex items-center">
                                            <span class="w-[180px] font-semibold">Tipe Asset</span>
                                            <span>{{ $asset['asset_master']['asset_type'] ?? '-' }}</span>
                                    </div>
                                        <div class="flex items-center">
                                            <span class="w-[180px] font-semibold">Sub Kategori</span>
                                            <span>{{ $asset['asset_master']['subcategory_name'] ?? '-' }}</span>
                                    </div>
                                        <div class="flex items-center">
                                            <span class="w-[180px] font-semibold">Merek</span>
                                            <span>{{ $asset['asset_master']['brand_name'] ?? '-' }}</span>
                                    </div>
                                        <div class="flex items-start">
                                            <span class="w-[180px] font-semibold pt-0.5">Deskripsi</span>
                                            <span class="flex-1">{{ $asset['asset_master']['description'] ?? 'Tidak ada deskripsi' }}</span>
                                        </div>
                                    </div>
                                    </div>
                                    </div>

                            <div class="mb-8">
                                <h2 class="text-xl font-semibold text-[#203268] mb-5">Informasi Asset</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                                <div class="space-y-4">
                                        <div class="flex items-center">
                                            <span class="w-[180px] font-semibold">Ruangan</span>
                                            <span>{{ $asset['room_name'] ?? '-' }}</span>
                                    </div>
                                        <div class="flex items-start">
                                            <span class="w-[180px] font-semibold pt-0.5">Gedung</span>
                                            <span>{{ $asset['building_name'] ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-[180px] font-semibold">Kondisi</span>
                                            <span>{{ ucfirst($asset['condition'] ?? '-') }}</span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:items-center">
                                            <span class="w-full sm:w-[180px] font-semibold mb-1 sm:mb-0">Tanggal Berakhir Garansi</span>
                                            <span>{{ $asset['warranty_end_date'] ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <span class="w-[180px] font-semibold">Nomor Seri</span>
                                            <span>{{ $asset['serial_number'] ?? '-' }}</span>
                                        </div>
                                        <!-- Add Employee Number for Responsible User -->
                                        <div class="flex items-center">
                                            <span class="w-[180px] font-semibold">Karyawan Penanggung Jawab</span>
                                            <span>
                                                @if(isset($asset['user_id']) && $asset['user_id'])
                                                    @if(isset($asset['user']['employee_number']) && $asset['user']['employee_number'])
                                                        {{ $asset['user']['employee_number'] }}
                                                        @if(isset($asset['user']['name']) && $asset['user']['name'])
                                                            - {{ $asset['user']['name'] }}
                                                        @endif
                                                    @elseif(isset($asset['user']['name']) && $asset['user']['name'])
                                                        {{ $asset['user']['name'] }}
                                                    @else
                                                        User ID: {{ $asset['user_id'] }}
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-[180px] font-semibold">Harga Beli</span>
                                            <span>{{ number_format((float) ($asset['purchase_cost'] ?? 0), 2) }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-[180px] font-semibold">Tanggal Beli</span>
                                            <span>{{ $asset['purchase_date'] ?? '-' }}</span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:items-center">
                                            <span class="w-full sm:w-[180px] font-semibold mb-1 sm:mb-0">Tanggal Dimusnahkan</span>
                                            <span>{{ $asset['current_status'] === 'disposed' ? ($asset['updated_at'] ?? '-') : '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Lost Date section if applicable -->
                            @if($asset['current_status'] === 'lost')
                            <div class="mb-6">
                                <div class="flex flex-col sm:flex-row sm:items-center">
                                    <span class="w-full sm:w-[180px] font-semibold mb-1 sm:mb-0">Tanggal Hilang</span>
                                    <span>
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
                    </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tabs Section -->
                    <div class="mt-4">
                        <!-- Tabs Navigation -->
                        <div class="card bg-base-100 shadow-xl overflow-hidden">
                            <div
                                class="flex flex-nowrap border-b border-[#EEF1F4] bg-white w-full overflow-x-auto hide-scrollbar">
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
                            </div>

                            <!-- Tab Content -->
                            <div id="tab-content" class="overflow-y-auto max-h-[500px] border border-gray-200 rounded-md">
                                <div class="tab-pane" id="document">
                                    @include('Asset.Tabs.Document')
                                </div>
                                <div class="tab-pane hidden" id="history">
                                    @include('Asset.Tabs.History')
                                </div>
                                <div class="tab-pane hidden" id="mutation">
                                    @include('Asset.Tabs.Mutation')
                                </div>
                                <div class="tab-pane hidden" id="depreciation">
                                    @include('Asset.Tabs.Depreciation')
                                </div>
                                <div class="tab-pane hidden" id="finance">
                                    @include('Asset.Tabs.Finance')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Asset Modal -->
    <div id="editAssetModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="editAssetModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">EDIT ASSET</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="editAssetForm" method="POST"
                        action="{{ route('asset.update', ['id' => $asset['asset_id'] ?? '']) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Asset Information Section -->
                                <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Asset Information</h3>

                                <!-- Basic Asset Details -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label for="edit_asset_master_search" class="block text-base font-semibold text-[#666666]">Asset Master</label>
                                        <div class="relative">
                                            <input type="text" id="edit_asset_master_search"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                placeholder="Search asset master..." autocomplete="off">
                                            <input type="hidden" name="asset_master_id" id="edit_selected_asset_master_id">
                                            <input type="hidden" id="edit_selected_is_depreciable" value="false">

                                            <!-- Dropdown -->
                                            <div id="edit_asset_master_dropdown" class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                <div id="edit_asset_master_loading" class="p-2 text-gray-500 text-center">
                                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                                    <span>Loading asset masters...</span>
                                        </div>
                                                <ul id="edit_asset_master_list" class="py-1"></ul>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="edit_serial_number" class="block text-base font-semibold text-[#666666]">Serial Number</label>
                                        <input type="text" name="serial_number" id="edit_serial_number"
                                            value="{{ $asset['serial_number'] ?? '' }}"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="Serial number">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Purchase Date</label>
                                        <input type="date" name="purchase_date" id="edit_purchase_date"
                                            value="{{ $asset['purchase_date'] ?? '' }}"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Purchase Cost</label>
                                        <input type="number" name="purchase_cost" id="edit_purchase_cost" step="0.01"
                                            value="{{ $asset['purchase_cost'] ?? '0.00' }}"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="0.00">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Warranty End Date</label>
                                        <input type="date" name="warranty_end_date" id="edit_warranty_end_date"
                                            value="{{ $asset['warranty_end_date'] ?? '' }}"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                    </div>
                                    <!-- Room Dropdown -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Room</label>
                                        <div class="relative">
                                            <input type="text" id="edit_room_search"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                placeholder="Search for room..." autocomplete="off">
                                            <input type="hidden" name="room_id" id="edit_selected_room_id" value="{{ $asset['room_id'] ?? '' }}">
                                            <div id="edit_room_dropdown" class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                <div id="edit_room_loading" class="p-2 text-gray-500 text-center">
                                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <span>Loading rooms...</span>
                                    </div>
                                                <ul id="edit_room_list" class="py-1"></ul>
                                </div>
                                    </div>
                                        <div id="edit_selected_room_display" class="hidden">
                                            <span id="edit_selected_room_name"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Condition</label>
                                        <select name="condition" id="edit_condition" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                            <option value="good" {{ $asset['condition'] == 'good' ? 'selected' : '' }}>Good</option>
                                            <option value="slightly damage" {{ $asset['condition'] == 'slightly damage' ? 'selected' : '' }}>Slightly Damage</option>
                                            <option value="high damage" {{ $asset['condition'] == 'high damage' ? 'selected' : '' }}>Highly Damage</option>
                                        </select>
                                    </div>
                                    <!-- User ID Field -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Karyawan yang Bertanggung Jawab</label>
                                        <div class="relative">
                                            <input type="text" id="edit_user_search"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                placeholder="Cari karyawan (nomor karyawan)..." autocomplete="off">
                                            <input type="hidden" name="user_id" id="edit_selected_user_id" value="{{ $asset['user_id'] ?? '' }}">
                                            <div id="edit_user_dropdown" class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                <div id="edit_user_loading" class="p-2 text-gray-500 text-center">
                                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <span>Loading users...</span>
                                    </div>
                                                <ul id="edit_user_list" class="py-1"></ul>
                                </div>
                                </div>
                                    </div>
                                </div>

                                <!-- Depreciation Fields Section -->
                                <div id="edit_depreciation_fields" class="space-y-4 border rounded-lg p-4 border-dashed border-gray-300 {{ $asset['asset_master']['is_depreciable'] ? '' : 'hidden' }}">
                                    <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Depreciation Information</h3>

                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Depreciation Method</label>
                                        <select name="depreciation_method" id="edit_depreciation_method"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                            <option value="Straight Line" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == 'Straight Line' ? 'selected' : '' }}>Straight Line</option>
                                            <option value="Double Declining Balance" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == 'Double Declining Balance' ? 'selected' : '' }}>Double Declining Balance</option>
                                            <option value="150% Declining Balance" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == '150% Declining Balance' ? 'selected' : '' }}>150% Declining Balance</option>
                                            <option value="Sum of the Year's Digits" {{ isset($asset['depreciation']) && $asset['depreciation']['depreciation_method'] == "Sum of the Year's Digits" ? 'selected' : '' }}>Sum of the Year's Digits</option>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">Acquisition Cost</label>
                                            <input type="number" step="0.01" name="acquisition_cost" id="edit_acquisition_cost"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                placeholder="0.00" value="{{ isset($asset['depreciation']) ? $asset['depreciation']['acquisition_cost'] : '' }}">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">Salvage Value</label>
                                            <input type="number" step="0.01" name="salvage_value" id="edit_salvage_value"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                placeholder="0.00" value="{{ isset($asset['depreciation']) ? $asset['depreciation']['salvage_value'] : '' }}">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">Asset Life (months)</label>
                                            <input type="number" name="asset_life_months" id="edit_asset_life_months"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                value="{{ isset($asset['depreciation']) ? $asset['depreciation']['asset_life_months'] : '' }}">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">Date Acquired</label>
                                            <input type="date" name="date_acquired" id="edit_date_acquired"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                value="{{ isset($asset['depreciation']) ? $asset['depreciation']['date_acquired'] : '' }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Asset Modal -->
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
                    <form id="checkoutAssetForm" method="POST" action="{{ route('asset.checkout') }}">
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
                                        <div id="checkout_user_dropdown" class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                            <div id="checkout_user_loading" class="p-2 text-gray-500 text-center">
                                                <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <span>Loading users...</span>
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
                                            <div id="pinjam_building_dropdown" class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                <div id="pinjam_building_loading" class="p-2 text-gray-500 text-center">
                                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <span>Loading buildings...</span>
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
                                            <div id="pinjam_room_dropdown" class="absolute z-10 w-full mt-1 max-h-60 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                                                <div id="pinjam_room_loading" class="p-2 text-gray-500 text-center">
                                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <span>Loading rooms...</span>
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

    <!-- Checkin Asset Modal -->
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
                    <form id="checkinAssetForm" method="POST" action="{{ route('asset.checkin') }}">
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

    <!-- Report Asset as Lost Modal -->
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
                    <form id="reportLostForm" method="POST" action="{{ route('asset.lost') }}">
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

    <!-- Found Asset Modal -->
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
                    <form id="foundAssetForm" method="POST" action="{{ route('asset.found') }}">
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

    <!-- Dispose Asset Modal -->
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
                    <form id="disposeAssetForm" method="POST" action="{{ route('asset.dispose') }}">
                        @csrf
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Hidden asset ID field -->
                                <input type="hidden" name="asset_id" value="{{ $asset['asset_id'] ?? '' }}">
                                <input type="hidden" name="transfer_type" value="DISPOSAL">

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

                                <!-- Dispose Date -->
                                <div class="space-y-2">
                                    <label class="block text-base font-medium text-[#666666]">Tanggal Penghapusan</label>
                                    <input type="date" name="dispose_date" required readonly
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268] bg-gray-100"
                                        value="{{ date('Y-m-d') }}">
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


    <!-- Toast Notifications -->
    @if(session('success'))
    <div id="successNotification" class="fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50" role="alert">
        <div class="flex items-center">
            <div class="py-1">
                <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="font-bold">Success!</p>
                <p>{{ session('success') }}</p>
            </div>
            <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
        </div>
    </div>

    <script>
        setTimeout(function() {
            const notification = document.getElementById('successNotification');
            if (notification) {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(function() {
                    notification.remove();
                }, 500);
            }
        }, 5000); // Hide after 5 seconds
    </script>
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
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Global data and utility functions
            window.usersData = @json($users ?? []);

            // Debounce utility function
            function debounce(func, wait) {
                let timeout;
                return function(...args) {
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

                // Reset forms inside the modal
                const forms = modal.querySelectorAll('form');
                forms.forEach(form => {
                    form.reset();

                    // Reset hidden inputs that might not be affected by form.reset()
                    const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
                    hiddenInputs.forEach(input => {
                        input.value = '';
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

                    // Reset depreciation fields if present
                    const depreciationFields = form.querySelector('#depreciation_fields') || form.querySelector('#edit_depreciation_fields');
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
                });

                // Animate closing
                content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
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
                        // Check content type before trying to parse as JSON
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json().then(data => {
                                // Enhanced success detection
                                if (data.success === true) {
                                    successCallback(data);
                                } else if (data.message &&
                                          (data.message.toLowerCase().includes('success') ||
                                           data.message.toLowerCase().includes('successfully'))) {
                                    // Handle responses that indicate success in the message but have success: false
                                    data.success = true; // Fix the success flag
                                    successCallback(data);
                                } else {
                                    errorCallback(data.message || 'Operation failed');
                                }
                            });
                        } else {
                            // Not JSON, handle as error
                            return response.text().then(text => {
                                console.error('Received non-JSON response:', text);
                                showToast('Terjadi kesalahan pada server', 'error');
                                errorCallback('Received non-JSON response from server');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan, silakan coba lagi', 'error');
                        errorCallback('An error occurred. Please try again.');
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
                searchInput.addEventListener('focus', function() {
                    dropdown.classList.remove('hidden');
                    onSearch(this.value);
                });

                // Filter on input with debounce
                searchInput.addEventListener('input', debounce(function(e) {
                    onSearch(e.target.value);
                }, 300));

                // Hide dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }

            // Initialize search components with a unified approach
            function initSearchComponents() {
                // Room search
                initRoomSearch(
                    document.getElementById('edit_room_search'),
                    document.getElementById('edit_room_dropdown'),
                    document.getElementById('edit_room_list'),
                    document.getElementById('edit_room_loading'),
                    document.getElementById('edit_selected_room_id'),
                    document.getElementById('edit_selected_room_name'),
                    document.getElementById('edit_selected_room_display')
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
                initDropdown(searchInput, dropdown, roomList, function(searchTerm) {
                    loadRooms(searchTerm, roomList, loadingIndicator, selectedRoomId, selectedRoomName, roomDisplay, searchInput, dropdown);
                });
            }

            function initUserSearch(searchInput, dropdown, userList, loadingIndicator, selectedUserId) {
                initDropdown(searchInput, dropdown, userList, function(searchTerm) {
                    loadUsers(searchTerm, userList, loadingIndicator, selectedUserId, searchInput, dropdown);
                });
            }

            function initAssetMasterSearch(searchInput, dropdown, assetMasterList, loadingIndicator, selectedAssetMasterId, selectedIsDepreciable, depreciationFields) {
                initDropdown(searchInput, dropdown, assetMasterList, function(searchTerm) {
                    loadAssetMasters(searchTerm, assetMasterList, loadingIndicator, selectedAssetMasterId, selectedIsDepreciable, depreciationFields, searchInput, dropdown);
                });
            }

            // Data loading functions
            async function loadRooms(searchTerm, roomList, loadingIndicator, selectedRoomId, selectedRoomName, roomDisplay, searchInput, dropdown) {
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                roomList.innerHTML = '';

                try {
                    const rooms = @json($rooms ?? []);
                    let filteredRooms = rooms;

                    if (searchTerm) {
                        const search = searchTerm.toLowerCase();
                        filteredRooms = rooms.filter(room =>
                            (room.room_name && room.room_name.toLowerCase().includes(search)) ||
                            (room.room_number && room.room_number.toLowerCase().includes(search)) ||
                            (room.building && room.building.building_name && room.building.building_name.toLowerCase().includes(search))
                        );
                    }

                    if (filteredRooms.length === 0) {
                        roomList.appendChild(createDropdownItem('No rooms found', 'px-4 py-2 text-gray-500 italic'));
                    } else {
                        filteredRooms.forEach(item => {
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

                            li.addEventListener('click', function() {
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
                    let users = window.usersData || [];

                    if (searchTerm) {
                        searchTerm = searchTerm.toLowerCase();
                        users = users.filter(user =>
                            (user.employee_number && user.employee_number.toLowerCase().includes(searchTerm)) ||
                            (user.name && user.name.toLowerCase().includes(searchTerm)) ||
                            (user.user_id && user.user_id.toString().includes(searchTerm))
                        );
                    }

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

                            li.addEventListener('click', function() {
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
                    const response = await fetch(`{{ route('asset-master.data') }}${searchTerm ? '?search=' + encodeURIComponent(searchTerm) : ''}`);

                    if (!response.ok) throw new Error('Failed to fetch asset masters');

                    const result = await response.json();
                    let assetMasters = result.masterAssets || [];

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

                            li.addEventListener('click', function() {
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
                btn.addEventListener('click', function() {
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
                button.addEventListener('click', function() {
                    const modal = this.closest('[id$="Modal"]');
                    const content = modal.querySelector('[id$="ModalContent"]');
                    if (modal && content) closeModal(modal, content);
                });
            });

            // Initialize each modal
            Object.keys(modals).forEach(key => {
                const modalObj = modals[key];
                if (modalObj.btn && modalObj.modal && modalObj.content) {
                    modalObj.btn.addEventListener('click', function(e) {
                        if (e.preventDefault) e.preventDefault();

                        if (key === 'edit') {
                            // Special handling for edit modal - fetch data first
                            const assetId = '{{ $asset["asset_id"] ?? "" }}';
                            if (!assetId) {
                                console.error('Asset ID not found');
                                return;
                            }

                            modalObj.btn.classList.add('opacity-50', 'pointer-events-none');

                            fetch(`{{ route('assets.get', '') }}/${assetId}`, {
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
                    modalObj.modal.addEventListener('click', function(e) {
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
                            closeBtn.addEventListener('click', function() {
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
                depreciableToggle.addEventListener('change', function() {
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
                purchaseCostInput.addEventListener('input', function() {
                    if (document.getElementById('edit_is_depreciable').checked) {
                        document.getElementById('edit_acquisition_cost').value = this.value;
                    }
                });
            }

            // Building selector for room filtering
            const buildingSelector = document.getElementById('building_selector');
            if (buildingSelector) {
                const allRooms = @json($rooms);

                buildingSelector.addEventListener('change', function() {
                    const selectedBuildingId = parseInt(this.value);
                    const roomDropdown = document.getElementById('location_id');

                    roomDropdown.innerHTML = '';

                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.text = 'Select a room';
                    defaultOption.disabled = true;
                    defaultOption.selected = true;
                    roomDropdown.appendChild(defaultOption);

                    const filteredRooms = allRooms.filter(room =>
                        room.building_id === selectedBuildingId ||
                        (room.building && parseInt(room.building.building_id) === selectedBuildingId)
                    );

                    filteredRooms.forEach(room => {
                        const option = document.createElement('option');
                        option.value = room.room_id;
                        option.text = room.room_name;
                        roomDropdown.appendChild(option);
                    });
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
                employeeRadio.addEventListener('change', function() {
                    if (this.checked) {
                        employeeDropdown.classList.remove('hidden');
                        locationDropdown.classList.add('hidden');
                        document.getElementById('checkout_selected_user_id').setAttribute('required', '');
                        document.getElementById('location_id').removeAttribute('required');
                    }
                });

                locationRadio.addEventListener('change', function() {
                    if (this.checked) {
                        employeeDropdown.classList.add('hidden');
                        locationDropdown.classList.remove('hidden');
                        document.getElementById('location_id').setAttribute('required', '');
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
            window.setupWithData = function(asset) {
                console.log('Setting up edit asset form with data:', asset);

                const form = document.getElementById('editAssetForm');
                if (!form) {
                    console.error('Edit asset form not found');
                    return;
                }

                form.action = "{{ route('asset.update', '') }}/" + asset.asset_id;
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
                if (assetMasterId) {
                    document.getElementById('edit_selected_asset_master_id').value = assetMasterId;

                    let assetMasterName = '';
                    if (asset.asset_master && asset.asset_master.asset_name) {
                        assetMasterName = asset.asset_master.asset_name;
                    } else if (asset.asset_master && asset.asset_master.asset_master_name) {
                        assetMasterName = asset.asset_master.asset_master_name;
                    } else if (asset.asset_master_name) {
                        assetMasterName = asset.asset_master_name;
                    } else {
                        assetMasterName = 'Asset Master ID: ' + assetMasterId;
                    }

                    document.getElementById('edit_asset_master_search').value = assetMasterName;

                    const assetMasterLoading = document.getElementById('edit_asset_master_loading');
                    if (assetMasterLoading) assetMasterLoading.classList.add('hidden');

                    const isDepreciable = asset.asset_master && asset.asset_master.is_depreciable === true;
                    document.getElementById('edit_selected_is_depreciable').value = isDepreciable.toString();

                    const editDepreciationFields = document.getElementById('edit_depreciation_fields');
                    if (editDepreciationFields) {
                        toggleDepreciationFields(editDepreciationFields, isDepreciable);
                    }
                }

                // Set room information
                if (asset.room_id) {
                    document.getElementById('edit_selected_room_id').value = asset.room_id;

                    let roomName = "Room ID: " + asset.room_id;
                    if (asset.room) {
                        const buildingName = asset.room.building ? asset.room.building.building_name :
                                            (asset.room.building_name ? asset.room.building_name : 'Unknown Building');
                        roomName = `${asset.room.room_name} (${buildingName})`;
                    } else if (asset.room_name) {
                        roomName = `${asset.room_name} (${asset.building_name || 'Unknown Building'})`;
                    } else {
                        const rooms = @json($rooms ?? []);
                        const selectedRoom = rooms.find(room => room.room_id == asset.room_id);
                        if (selectedRoom) {
                            const buildingName = selectedRoom.building ? selectedRoom.building.building_name :
                                                (selectedRoom.building_name || 'Unknown Building');
                            roomName = `${selectedRoom.room_name} (${buildingName})`;
                        }
                    }

                    document.getElementById('edit_room_search').value = roomName;

                    const roomLoading = document.getElementById('edit_room_loading');
                    if (roomLoading) roomLoading.classList.add('hidden');
                }

                // Set user information
                if (asset.user_id) {
                    document.getElementById('edit_selected_user_id').value = asset.user_id;

                    let userDisplay = `User ID: ${asset.user_id}`;

                    if (asset.user) {
                        if (asset.user.employee_number) {
                            userDisplay = asset.user.employee_number;
                            if (asset.user.name) userDisplay += ` - ${asset.user.name}`;
                        } else if (asset.user.name) {
                            userDisplay = asset.user.name;
                        }
                    } else {
                        const user = window.usersData.find(u => u.user_id == asset.user_id);
                        if (user) {
                            if (user.employee_number) {
                                userDisplay = user.employee_number;
                                if (user.name) userDisplay += ` - ${user.name}`;
                            } else if (user.name) {
                                userDisplay = user.name;
                            }
                        }
                    }

                    document.getElementById('edit_user_search').value = userDisplay;

                    const userLoading = document.getElementById('edit_user_loading');
                    if (userLoading) userLoading.classList.add('hidden');
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
                function(searchTerm) {
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

            // Function to load buildings
            async function loadBuildings(searchTerm, buildingList, loadingIndicator, selectedBuildingId, searchInput, dropdown, roomSearchInput) {
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                buildingList.innerHTML = '';

                try {
                    const buildings = @json($buildings ?? []);
                    let filteredBuildings = buildings;

                    if (searchTerm) {
                        const search = searchTerm.toLowerCase();
                        filteredBuildings = buildings.filter(building =>
                            (building.building_name && building.building_name.toLowerCase().includes(search)) ||
                            (building.address && building.address.toLowerCase().includes(search))
                        );
                    }

                    if (filteredBuildings.length === 0) {
                        const noResults = document.createElement('li');
                        noResults.className = 'px-4 py-2 text-gray-500 italic';
                        noResults.textContent = 'No buildings found';
                        buildingList.appendChild(noResults);
                    } else {
                        filteredBuildings.forEach(building => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                            li.textContent = building.building_name;
                            li.setAttribute('data-id', building.building_id);
                            li.setAttribute('data-name', building.building_name);

                            li.addEventListener('click', function() {
                                // Set the selected building ID and name
                                selectedBuildingId.value = this.getAttribute('data-id');
                                searchInput.value = this.getAttribute('data-name');

                                // Enable room search and update placeholder
                                roomSearchInput.disabled = false;
                                roomSearchInput.placeholder = "Search for room...";

                                // Clear room selection
                                document.getElementById('pinjam_selected_room_id').value = '';
                                document.getElementById('pinjam_room_search').value = '';

                                // Hide dropdown
                                dropdown.classList.add('hidden');
                            });

                            buildingList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading buildings:', error);
                    const errorItem = document.createElement('li');
                    errorItem.className = 'px-4 py-2 text-red-500';
                    errorItem.textContent = 'Error loading buildings';
                    buildingList.appendChild(errorItem);
                } finally {
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }

            // Initialize room search
            initDropdown(
                document.getElementById('pinjam_room_search'),
                document.getElementById('pinjam_room_dropdown'),
                document.getElementById('pinjam_room_list'),
                function(searchTerm) {
                    const buildingId = document.getElementById('pinjam_selected_building_id').value;
                    if (buildingId) {
                        loadRoomsForBuilding(
                            searchTerm,
                            buildingId,
                            document.getElementById('pinjam_room_list'),
                            document.getElementById('pinjam_room_loading'),
                            document.getElementById('pinjam_selected_room_id'),
                            document.getElementById('pinjam_room_search'),
                            document.getElementById('pinjam_room_dropdown')
                        );
                    }
                }
            );

            // Function to load rooms for the selected building
            async function loadRoomsForBuilding(searchTerm, buildingId, roomList, loadingIndicator, selectedRoomId, searchInput, dropdown) {
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                roomList.innerHTML = '';

                try {
                    const rooms = @json($rooms ?? []);

                    // Filter rooms by building ID
                    let filteredRooms = rooms.filter(room =>
                        (room.building_id && room.building_id == buildingId) ||
                        (room.building && room.building.building_id == buildingId)
                    );

                    // Further filter by search term if provided
                    if (searchTerm) {
                        const search = searchTerm.toLowerCase();
                        filteredRooms = filteredRooms.filter(room =>
                            (room.room_name && room.room_name.toLowerCase().includes(search)) ||
                            (room.room_number && room.room_number.toLowerCase().includes(search))
                        );
                    }

                    if (filteredRooms.length === 0) {
                        const noResults = document.createElement('li');
                        noResults.className = 'px-4 py-2 text-gray-500 italic';
                        noResults.textContent = 'No rooms found in this building';
                        roomList.appendChild(noResults);
                    } else {
                        filteredRooms.forEach(room => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                            li.textContent = room.room_name;
                            li.setAttribute('data-id', room.room_id);
                            li.setAttribute('data-name', room.room_name);

                            li.addEventListener('click', function() {
                                // Set the selected room ID and name
                                selectedRoomId.value = this.getAttribute('data-id');
                                searchInput.value = this.getAttribute('data-name');

                                // Hide dropdown
                                dropdown.classList.add('hidden');
                            });

                            roomList.appendChild(li);
                        });
                    }
                } catch (error) {
                    console.error('Error loading rooms:', error);
                    const errorItem = document.createElement('li');
                    errorItem.className = 'px-4 py-2 text-red-500';
                    errorItem.textContent = 'Error loading rooms';
                    roomList.appendChild(errorItem);
                } finally {
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                }
            }
        });
    </script>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle toast notifications
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        // Function to show toast notifications
        window.showToast = function(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 flex items-center';

            if (type === 'success') {
                toast.classList.add('bg-green-100', 'border-l-4', 'border-green-500', 'text-green-700');
            } else {
                toast.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700');
            }

            toast.innerHTML = `
                <div class="py-1">
                    <svg class="h-6 w-6 mr-4 ${type === 'success' ? 'text-green-500' : 'text-red-500'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        ${type === 'success'
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2 2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />'}
                    </svg>
                </div>
                <div>
                    <p class="font-bold">${type === 'success' ? 'Success!' : 'Error!'}</p>
                    <p>${message}</p>
                </div>
                <span class="ml-4 cursor-pointer" onclick="this.parentElement.remove()">×</span>
            `;

            document.body.appendChild(toast);

            // Auto-remove the toast after 5 seconds
            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => {
                    toast.remove();
                }, 500);
            }, 5000);
        }
    });
</script>
@endpush
