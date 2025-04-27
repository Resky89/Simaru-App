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
                                        @if(isset($asset['picture_path']) && $asset['picture_path'])
                                            <img src="http://localhost:5000/public{{ $asset['picture_path'] }}"
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
                                <p class="text-sm font-medium text-center mt-2">{{ $asset['asset_name'] ?? '-' }}</p>
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
                            <h2 class="text-xl font-semibold text-[#203268] mb-4">Informasi Asset</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-4">
                                    <div class="flex">
                                        <span class="w-[140px] font-semibold">Kategori</span>
                                        <span>{{ $asset['subcategory']['asset_type'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="w-[140px] font-semibold">Sub Kategori</span>
                                        <span>{{ $asset['subcategory']['subcategory_name'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="w-[140px] font-semibold">Ruangan</span>
                                        <span>{{ $asset['room']['room_name'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="w-[140px] font-semibold">Gedung</span>
                                        <span>{{ $asset['room']['building']['building_name'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="w-[140px] font-semibold">Merek</span>
                                        <span>{{ $asset['brand']['brand_name'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="w-[140px] font-semibold">Tanggal Berakhir Garansi</span>
                                        <span>{{ $asset['warranty_end_date'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="w-[140px] font-semibold">Tanggal Hilang</span>
                                        <span>
                                            @if($asset['current_status'] === 'lost' && isset($asset['updated_at']))
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
                                <div class="space-y-4">
                                    <div class="flex">
                                        <span class="w-[140px] font-semibold">Nomor Model</span>
                                        <span>{{ $asset['model_number'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="w-[140px] font-semibold">Nomor Seri</span>
                                        <span>{{ $asset['serial_number'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="w-[140px] font-semibold">Harga Beli</span>
                                        <span>{{ number_format((float) ($asset['purchase_cost'] ?? 0), 2) }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="w-[140px] font-semibold">Tanggal Beli</span>
                                        <span>{{ $asset['purchase_date'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="w-[140px] font-semibold">Tanggal Dimusnahkan</span>
                                        <span>{{ $asset['current_status'] === 'disposed' ? ($asset['updated_at'] ?? '-') : '-' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="w-[140px] font-semibold">Kondisi</span>
                                        <span>{{ ucfirst($asset['condition'] ?? '-') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description Section -->
                    <div class="mt-4">
                        <h2 class="text-xl font-semibold text-black mb-4">Deskripsi</h2>
                        <p class="text-[#000000]">{{ $asset['description'] ?? '-' }}</p>
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
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Ubah Asset</h2>
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
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Asset Information Section -->
                                <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Informasi Asset</h3>

                                <!-- Image upload -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Gambar Asset</label>
                                    <div
                                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 relative flex flex-col items-center justify-center">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="mt-1 text-sm text-gray-600">Unggah gambar Anda atau <span
                                                    class="text-blue-600">cari</span></p>
                                            <p class="mt-1 text-xs text-gray-500">jpg, jpeg, png</p>
                                        </div>
                                        <input type="file" id="edit_image_file" name="image_file" accept=".jpg,.jpeg,.png"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        <!-- Preview image in container -->
                                        <div id="edit_preview-container" class="mt-4 w-full hidden">
                                            <img id="edit_image_preview" class="max-h-40 mx-auto rounded-lg object-contain"
                                                alt="Asset Image">
                                        </div>
                                    </div>
                                </div>

                                <!-- Basic Asset Details -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Nama Asset</label>
                                        <input type="text" name="asset_name" id="edit_asset_name" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                            placeholder="Nama Asset">
                                    </div>

                                    <!-- Subcategory Dropdown -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Sub Kategori</label>
                                        <select name="subcategory_id" id="edit_subcategory_id" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
                                            <option value="" disabled selected>Pilih sub kategori</option>
                                            @if(isset($subcategories))
                                                @foreach($subcategories as $subcategory)
                                                    <option value="{{ $subcategory['subcategory_id'] }}">
                                                        {{ $subcategory['subcategory_name'] }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Kondisi</label>
                                        <select name="condition" id="edit_condition" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
                                            <option value="baik">Baik</option>
                                            <option value="sedikit rusak">Sedikit Rusak</option>
                                            <option value="sangat rusak">Sangat Rusak</option>
                                        </select>
                                    </div>

                                    <!-- Room Dropdown -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Ruangan</label>
                                        <select name="room_id" id="edit_room_id" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
                                            <option value="" disabled selected>Pilih ruangan</option>
                                            @foreach($rooms as $room)
                                                <option value="{{ $room['room_id'] }}">
                                                    {{ $room['room_name'] }} ({{ $room['building']['building_name'] ?? '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Deskripsi</label>
                                    <textarea name="description" id="edit_description"
                                        class="w-full h-[100px] px-4 py-2 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268] resize-none"
                                        placeholder="Deskripsi Asset"></textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Nomor Model</label>
                                        <input type="text" name="model_number" id="edit_model_number"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                            placeholder="Nomor Model">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Nomor Seri</label>
                                        <input type="text" name="serial_number" id="edit_serial_number"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                            placeholder="Nomor Seri">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Tanggal Beli</label>
                                        <input type="date" name="purchase_date" id="edit_purchase_date"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Harga Beli</label>
                                        <input type="number" name="purchase_cost" id="edit_purchase_cost" step="0.01"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                            placeholder="0.00">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Tanggal Berakhir
                                            Garansi</label>
                                        <input type="date" name="warranty_end_date" id="edit_warranty_end_date"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
                                    </div>
                                    <!-- Brand Dropdown -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Merek</label>
                                        <select name="brand_id" id="edit_brand_id" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
                                            <option value="" disabled selected>Pilih merek</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand['brand_id'] }}">{{ $brand['brand_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Depreciation Toggle Switch -->
                                <div class="flex items-center justify-between border-t pt-4">
                                    <label for="edit_is_depreciable" class="text-base font-semibold text-[#666666]">Aktifkan
                                        Penyusutan Asset</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_depreciable" id="edit_is_depreciable"
                                            class="sr-only peer depreciation-toggle" value="1">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                            peer-checked:after:border-white after:content-[''] after:absolute
                                            after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                            after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                            peer-checked:bg-[#213268]"></div>
                                        <span class="ml-2 text-sm font-medium text-gray-900 depreciation-status">No</span>
                                    </label>
                                </div>

                                <!-- Depreciation Fields (Hidden by default) -->
                                <div id="edit_depreciation_fields"
                                    class="space-y-4 hidden border rounded-lg p-4 border-dashed border-gray-300">
                                    <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Informasi Penyusutan</h3>

                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">Metode
                                            Penyusutan</label>
                                        <select name="depreciation_method" id="edit_depreciation_method"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
                                            <option value="Straight Line">Straight Line</option>
                                            <option value="Double Declining Balance">Double Declining Balance</option>
                                            <option value="150% Declining Balance">150% Declining Balance</option>
                                            <option value="Sum of the Year's Digits">Sum of the Year's Digits</option>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">Harga
                                                Perolehan</label>
                                            <input type="number" step="0.01" name="acquisition_cost"
                                                id="edit_acquisition_cost"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                                placeholder="0.00">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">Nilai Sisa</label>
                                            <input type="number" step="0.01" name="salvage_value" id="edit_salvage_value"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                                placeholder="0.00">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">Usia Asset
                                                (Bulan)</label>
                                            <input type="number" name="asset_life_months" id="edit_asset_life_months"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]"
                                                placeholder="0">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-base font-semibold text-[#666666]">Tanggal
                                                Perolehan</label>
                                            <input type="date" name="date_acquired" id="edit_date_acquired"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
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
                    <form id="checkoutAssetForm" method="POST">
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
                                    <select name="assigned_to" id="assigned_to" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
                                        <option value="" disabled selected>Pilih Karyawan</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee['employee_id'] }}">{{ $employee['first_name'] }}
                                                {{ $employee['last_name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Location Dropdown (hidden by default) -->
                                <div id="locationDropdown" class="space-y-4 hidden">
                                    <!-- Building Dropdown -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Pilih Gedung</label>
                                        <select id="building_selector"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
                                            <option value="" disabled selected>Pilih Gedung</option>
                                            @foreach($buildings as $building)
                                                <option value="{{ $building['building_id'] }}">{{ $building['building_name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Room Dropdown -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-medium text-[#666666]">Pilih Ruangan</label>
                                        <select name="location_id" id="location_id"
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-black focus:outline-none focus:border-[#213268]">
                                            <option value="" disabled selected>Pilih Gedung terlebih dahulu</option>
                                        </select>
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
                    <form id="checkinAssetForm" method="POST">
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
                    <form id="reportLostForm" method="POST">
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
                    <form id="foundAssetForm" method="POST">
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
                    <form id="disposeAssetForm" method="POST">
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
        document.addEventListener('DOMContentLoaded', function () {
            // Get flip card elements
            const flipCard = document.querySelector('.flip-card');
            const flipBtns = document.querySelectorAll('.flip-btn');

            // Add click event to all flip buttons
            flipBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    flipCard.classList.toggle('flipped');
                });
            });

            // Tab functionality
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            // Show the first tab by default
            if (tabPanes.length > 0) {
                tabPanes.forEach(pane => pane.classList.add('hidden'));
                const firstTab = document.getElementById(tabButtons[0].getAttribute('data-tab'));
                if (firstTab) firstTab.classList.remove('hidden');
            }

            tabButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    // Remove active class from all buttons
                    tabButtons.forEach(button => {
                        button.classList.remove('active', 'text-[#213268]', 'border-b-2', 'border-[#213268]');
                        button.classList.add('text-gray-500');
                    });

                    // Add active class to clicked button
                    this.classList.remove('text-gray-500');
                    this.classList.add('active', 'text-[#213268]', 'border-b-2', 'border-[#213268]');

                    // Hide all tab content
                    tabPanes.forEach(pane => {
                        pane.classList.add('hidden');
                    });

                    // Show selected tab content
                    const tabName = this.getAttribute('data-tab');
                    const selectedTab = document.getElementById(tabName);
                    if (selectedTab) selectedTab.classList.remove('hidden');
                });
            });

            // Asset Edit Modal Functionality
            function openModal(modal, content) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                    content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                }, 10);
            }

            function closeModal(modal, content) {
                content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            // Setup Edit button
            const editBtn = document.getElementById('editAssetBtn');
            const editModal = document.getElementById('editAssetModal');
            const editModalContent = document.getElementById('editAssetModalContent');

            if (editBtn && editModal && editModalContent) {
                editBtn.addEventListener('click', function () {
                    // Get asset ID from current page
                    const assetId = '{{ $asset["asset_id"] ?? "" }}';

                    if (!assetId) {
                        console.error('Asset ID not found');
                        return;
                    }

                    // Show loading state
                    editBtn.classList.add('opacity-50', 'pointer-events-none');

                    // Fetch asset data including subcategories, rooms, and brands from server
                    fetch(`{{ route('assets.get', '') }}/${assetId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Reset loading state
                            editBtn.classList.remove('opacity-50', 'pointer-events-none');

                            if (data.error) {
                                console.error('Error fetching asset:', data.error);
                                alert('Failed to load asset data: ' + data.error);
                                return;
                            }

                            // Populate dropdowns with the fetched data
                            populateSubcategories(data.subcategories);
                            populateRooms(data.rooms);
                            populateBrands(data.brands);

                            // Set up the edit form with the data
                            setupEditAssetForm(data.asset);

                            // Open the modal
                            openModal(editModal, editModalContent);
                        })
                        .catch(error => {
                            // Reset loading state
                            editBtn.classList.remove('opacity-50', 'pointer-events-none');
                            console.error('Error fetching asset:', error);
                            alert('Failed to load asset data. Please try again.');
                        });
                });
            }

            // Setup close buttons
            const closeButtons = document.querySelectorAll('.close-modal');
            closeButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const modal = this.closest('[id$="Modal"]');
                    const content = modal.querySelector('[id$="ModalContent"]');
                    if (modal && content) {
                        closeModal(modal, content);
                    }
                });
            });

            // Close on outside click
            if (editModal) {
                editModal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        closeModal(editModal, editModalContent);
                    }
                });
            }

            // Handle depreciation toggle
            const depreciableToggle = document.getElementById('edit_is_depreciable');
            const depreciationFields = document.getElementById('edit_depreciation_fields');

            if (depreciableToggle && depreciationFields) {
                depreciableToggle.addEventListener('change', function () {
                    const isChecked = this.checked;
                    const statusText = document.querySelector('.depreciation-status');

                    if (statusText) {
                        statusText.textContent = isChecked ? 'Yes' : 'No';
                    }

                    if (isChecked) {
                        depreciationFields.classList.remove('hidden');

                        // Enable input fields
                        const inputs = depreciationFields.querySelectorAll('input, select');
                        inputs.forEach(input => {
                            input.disabled = false;
                            input.classList.remove('bg-gray-100');
                        });
                    } else {
                        depreciationFields.classList.add('hidden');

                        // Disable input fields
                        const inputs = depreciationFields.querySelectorAll('input, select');
                        inputs.forEach(input => {
                            input.disabled = true;
                            input.classList.add('bg-gray-100');
                        });
                    }
                });
            }

            // Add this event listener to handle automatic acquisition cost update when enabling depreciation
            if (depreciableToggle) {
                depreciableToggle.addEventListener('change', function () {
                    // If depreciation is being enabled, set acquisition cost to match purchase cost
                    if (this.checked) {
                        const purchaseCost = document.getElementById('edit_purchase_cost').value || '0';
                        document.getElementById('edit_acquisition_cost').value = purchaseCost;

                        // Also set date acquired to match purchase date if available
                        const purchaseDate = document.getElementById('edit_purchase_date').value;
                        if (purchaseDate) {
                            document.getElementById('edit_date_acquired').value = purchaseDate;
                        }
                    }
                });
            }

            // Also update acquisition cost whenever purchase cost changes
            document.getElementById('edit_purchase_cost').addEventListener('input', function () {
                // Only update if depreciation is enabled
                if (document.getElementById('edit_is_depreciable').checked) {
                    document.getElementById('edit_acquisition_cost').value = this.value;
                }
            });

            // Handle image preview
            const fileInput = document.getElementById('edit_image_file');
            const imagePreview = document.getElementById('edit_image_preview');
            const previewContainer = document.getElementById('edit_preview-container');

            if (fileInput && imagePreview) {
                fileInput.addEventListener('change', function () {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            imagePreview.src = e.target.result;
                            previewContainer.classList.remove('hidden');
                        }
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }

            // Function to set up the edit form with data
            window.setupEditAssetForm = function (asset) {
                console.log('Setting up edit asset form with data:', asset);

                // Get form element
                const form = document.getElementById('editAssetForm');
                if (!form) {
                    console.error('Edit asset form not found');
                    return;
                }

                // Set form action
                form.action = `{{ route('asset.update', '') }}/${asset.asset_id}`;

                // Reset form first to clear any previous data
                form.reset();

                // Fill basic text inputs
                document.getElementById('edit_asset_name').value = asset.asset_name || '';
                document.getElementById('edit_description').value = asset.description || '';
                document.getElementById('edit_model_number').value = asset.model_number || '';
                document.getElementById('edit_serial_number').value = asset.serial_number || '';
                document.getElementById('edit_purchase_cost').value = asset.purchase_cost || '';

                // Handle dates (ensure formatting is correct)
                if (asset.purchase_date) {
                    const purchaseDate = asset.purchase_date.split(' ')[0]; // Get just the date part
                    document.getElementById('edit_purchase_date').value = purchaseDate;
                }

                if (asset.warranty_end_date) {
                    const warrantyDate = asset.warranty_end_date.split(' ')[0]; // Get just the date part
                    document.getElementById('edit_warranty_end_date').value = warrantyDate;
                }

                // Set dropdown values
                setSelectValue('edit_subcategory_id', asset.subcategory?.subcategory_id);
                setSelectValue('edit_room_id', asset.room?.room_id);
                setSelectValue('edit_brand_id', asset.brand?.brand_id);
                setSelectValue('edit_condition', asset.condition);

                // Set depreciation toggle
                const depreciableToggle = document.getElementById('edit_is_depreciable');
                const depreciationStatus = document.querySelector('.depreciation-status');
                const depreciationFields = document.getElementById('edit_depreciation_fields');

                if (depreciableToggle && depreciationFields) {
                    // Set the toggle state based on asset data
                    depreciableToggle.checked = asset.is_depreciable == 1;

                    // Update the status text
                    if (depreciationStatus) {
                        depreciationStatus.textContent = asset.is_depreciable == 1 ? 'Yes' : 'No';
                    }

                    // Show/hide depreciation fields based on toggle state
                    if (asset.is_depreciable == 1) {
                        depreciationFields.classList.remove('hidden');

                        // Enable fields
                        const inputs = depreciationFields.querySelectorAll('input, select');
                        inputs.forEach(input => {
                            input.disabled = false;
                            input.classList.remove('bg-gray-100');
                        });

                        // Fill depreciation data if available
                        if (asset.depreciation) {
                            // Set depreciation method
                            setSelectValue('edit_depreciation_method', asset.depreciation.depreciation_method);

                            // Fill other depreciation fields
                            document.getElementById('edit_acquisition_cost').value = asset.depreciation.acquisition_cost || '';
                            document.getElementById('edit_salvage_value').value = asset.depreciation.salvage_value || '';
                            document.getElementById('edit_asset_life_months').value = asset.depreciation.asset_life_months || '';

                            // Handle date acquired
                            if (asset.depreciation.date_acquired) {
                                const dateAcquired = asset.depreciation.date_acquired.split(' ')[0];
                                document.getElementById('edit_date_acquired').value = dateAcquired;
                            }
                        }
                    } else {
                        depreciationFields.classList.add('hidden');

                        // Disable fields
                        const inputs = depreciationFields.querySelectorAll('input, select');
                        inputs.forEach(input => {
                            input.disabled = true;
                            input.classList.add('bg-gray-100');
                        });
                    }
                }

                // Handle image preview if available
                if (asset.picture_path) {
                    const imagePreview = document.getElementById('edit_image_preview');
                    const previewContainer = document.getElementById('edit_preview-container');

                    if (imagePreview && previewContainer) {
                        // Define the base URL - using the confirmed server location
                        const baseUrl = "http://localhost:5000/public";

                        // Process the image URL
                        let imageUrl = asset.picture_path;

                        // If URL is not absolute, prepend the base URL
                        if (!imageUrl.startsWith('http://') && !imageUrl.startsWith('https://') && !imageUrl.startsWith('//')) {
                            // Remove leading slash if present to avoid double slashes
                            if (imageUrl.startsWith('/')) {
                                imageUrl = imageUrl.substring(1);
                            }
                            imageUrl = `${baseUrl}/${imageUrl}`;
                        }

                        // Set the image source and display it
                        imagePreview.src = imageUrl;
                        imagePreview.onerror = function () {
                            this.onerror = null;
                            this.src = '{{ asset('images/no-image.png') }}';
                            this.classList.add('object-contain', 'p-4');
                        };
                        previewContainer.classList.remove('hidden');
                    }
                } else {
                    // Hide the preview if no image
                    const previewContainer = document.getElementById('edit_preview-container');
                    if (previewContainer) {
                        previewContainer.classList.add('hidden');
                    }
                }
            }

            // Helper function to set dropdown values
            function setSelectValue(selectId, value) {
                const select = document.getElementById(selectId);
                if (!select || value === undefined || value === null) {
                    return;
                }

                // Convert to string for comparison
                const valueStr = String(value);

                // Find the matching option
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].value === valueStr) {
                        select.selectedIndex = i;
                        return;
                    }
                }
            }

            // Helper functions to populate dropdowns
            function populateSubcategories(subcategories) {
                const select = document.getElementById('edit_subcategory_id');
                if (!select || !subcategories) return;

                // Clear existing options except the first one
                while (select.options.length > 1) {
                    select.remove(1);
                }

                // Add new options
                subcategories.forEach(subcategory => {
                    const option = document.createElement('option');
                    option.value = subcategory.subcategory_id;
                    option.textContent = subcategory.subcategory_name;
                    select.appendChild(option);
                });
            }

            // Perbaikan fungsi populateRooms di AssetDetail.blade.php
            function populateRooms(rooms) {
                const select = document.getElementById('edit_room_id');
                if (!select || !rooms) return;

                // Clear existing options except the first one
                while (select.options.length > 1) {
                    select.remove(1);
                }

                // Add new options
                rooms.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.room_id;

                    // Cek berbagai kemungkinan struktur data untuk memastikan nama gedung ditampilkan
                    let buildingName = '-';

                    if (room.building && room.building.building_name) {
                        // Format bersarang normal
                        buildingName = room.building.building_name;
                    } else if (room.building_name) {
                        // Format datar (flat) langsung di objek room
                        buildingName = room.building_name;
                    } else if (room.building_id) {
                        // Mencoba mencari gedung berdasarkan building_id dari daftar gedung yang tersedia
                        const building = findBuildingById(room.building_id);
                        if (building) {
                            buildingName = building.building_name;
                        }
                    }

                    option.textContent = `${room.room_name} (${buildingName})`;
                    select.appendChild(option);
                });
            }

            // Fungsi helper untuk mencari gedung berdasarkan ID
            function findBuildingById(buildingId) {
                // Jika ada variabel global dengan daftar gedung
                if (typeof buildings !== 'undefined' && Array.isArray(buildings)) {
                    return buildings.find(b => b.building_id == buildingId);
                }
                return null;
            }

            function populateBrands(brands) {
                const select = document.getElementById('edit_brand_id');
                if (!select || !brands) return;

                // Clear existing options except the first one
                while (select.options.length > 1) {
                    select.remove(1);
                }

                // Add new options
                brands.forEach(brand => {
                    const option = document.createElement('option');
                    option.value = brand.brand_id;
                    option.textContent = brand.brand_name;
                    select.appendChild(option);
                });
            }

            // Checkout Modal Functionality
            const checkoutBtn = document.getElementById('checkoutAssetBtn');
            const checkoutModal = document.getElementById('checkoutAssetModal');
            const checkoutModalContent = document.getElementById('checkoutAssetModalContent');
            const checkoutForm = document.getElementById('checkoutAssetForm');

            if (checkoutBtn && checkoutModal && checkoutModalContent && checkoutForm) {
                // Set the current date as default
                const today = new Date().toISOString().split('T')[0];
                document.querySelector('input[name="checkout_date"]').value = today;

                // Set the form action
                checkoutForm.action = "{{ route('asset.checkout') }}";

                // Open checkout modal
                checkoutBtn.addEventListener('click', function () {
                    // Open modal
                    openModal(checkoutModal, checkoutModalContent);
                });

                // Handle checkout form submission
                checkoutForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const submitBtn = document.getElementById('submitCheckout');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Processing...';

                    // Get form data
                    const formData = new FormData(checkoutForm);
                    const assetId = formData.get('asset_id');
                    const checkoutNotes = formData.get('checkout_notes');
                    const checkoutToType = formData.get('checkout_to_type');

                    // Prepare request data
                    const requestData = {
                        asset_id: parseInt(assetId),
                        checkout_notes: checkoutNotes
                    };

                    // Add either assigned_to or room_id based on selection - never both
                    if (checkoutToType === 'location') {
                        requestData.room_id = parseInt(formData.get('location_id') || 0);
                    } else {
                        requestData.assigned_to = parseInt(formData.get('assigned_to') || 0);
                    }

                    console.log('Sending checkout request:', requestData);

                    // Send AJAX request
                    fetch("{{ route('asset.checkout') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(requestData)
                    })
                        .then(response => response.json())
                        .then(data => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Checkout';

                            if (data.status === true) {
                                // Success - just close the modal and reload without alert
                                closeModal(checkoutModal, checkoutModalContent);
                                window.location.reload();
                            } else {
                                // Error - keep alert for error messages
                                alert('Error: ' + (data.message || 'Failed to checkout asset'));
                            }
                        })
                        .catch(error => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Checkout';
                            console.error('Error checking out asset:', error);
                            alert('Failed to checkout asset. Please try again.');
                        });
                });

                // Toggle between employee and location
                const employeeRadio = document.getElementById('employee');
                const locationRadio = document.getElementById('location');
                const employeeDropdown = document.getElementById('employeeDropdown');
                const locationDropdown = document.getElementById('locationDropdown');

                if (employeeRadio && locationRadio && employeeDropdown && locationDropdown) {
                    employeeRadio.addEventListener('change', function () {
                        if (this.checked) {
                            employeeDropdown.classList.remove('hidden');
                            locationDropdown.classList.add('hidden');
                            document.getElementById('assigned_to').setAttribute('required', '');
                            document.getElementById('location_id').removeAttribute('required');
                        }
                    });

                    locationRadio.addEventListener('change', function () {
                        if (this.checked) {
                            employeeDropdown.classList.add('hidden');
                            locationDropdown.classList.remove('hidden');
                            document.getElementById('location_id').setAttribute('required', '');
                            document.getElementById('assigned_to').removeAttribute('required');
                        }
                    });
                }
            }

            // Checkin Modal Functionality
            const checkinBtn = document.getElementById('checkinAssetBtn');
            const checkinModal = document.getElementById('checkinAssetModal');
            const checkinModalContent = document.getElementById('checkinAssetModalContent');
            const checkinForm = document.getElementById('checkinAssetForm');

            if (checkinBtn && checkinModal && checkinModalContent && checkinForm) {
                // Set the form action
                checkinForm.action = "{{ route('asset.checkin') }}";

                // Open checkin modal
                checkinBtn.addEventListener('click', function () {
                    // Open modal
                    openModal(checkinModal, checkinModalContent);
                });

                // Handle checkin form submission
                checkinForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const submitBtn = document.getElementById('submitCheckin');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Processing...';

                    // Get form data
                    const formData = new FormData(checkinForm);
                    const assetId = formData.get('asset_id');
                    const returnNotes = formData.get('return_notes');
                    const condition = formData.get('condition');

                    // Prepare request data
                    const requestData = {
                        asset_id: parseInt(assetId),
                        return_notes: returnNotes,
                        condition: condition
                    };

                    console.log('Sending check-in request:', requestData);

                    // Send AJAX request
                    fetch("{{ route('asset.checkin') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(requestData)
                    })
                        .then(response => response.json())
                        .then(data => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Check In';

                            if (data.status === true) {
                                // Success - just close the modal and reload without alert
                                closeModal(checkinModal, checkinModalContent);
                                window.location.reload();
                            } else {
                                // Error - keep alert for error messages
                                alert('Error: ' + (data.message || 'Failed to check in asset'));
                            }
                        })
                        .catch(error => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Check In';
                            console.error('Error checking in asset:', error);
                            alert('Failed to check in asset. Please try again.');
                        });
                });
            }

            // Report Lost Modal Functionality
            const lostBtn = document.getElementById('lostAssetBtn');
            const lostModal = document.getElementById('reportLostModal');
            const lostModalContent = document.getElementById('reportLostModalContent');
            const lostForm = document.getElementById('reportLostForm');

            if (lostBtn && lostModal && lostModalContent && lostForm) {
                // Set the form action
                lostForm.action = "{{ route('asset.lost') }}";

                // Open lost modal
                lostBtn.addEventListener('click', function (e) {
                    e.preventDefault(); // Prevent the default link behavior
                    // Open modal
                    openModal(lostModal, lostModalContent);
                });

                // Handle lost form submission
                lostForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const submitBtn = document.getElementById('submitLostReport');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Processing...';

                    // Get form data
                    const formData = new FormData(lostForm);
                    const assetId = formData.get('asset_id');
                    const lossReason = formData.get('loss_reason');

                    // Prepare request data
                    const requestData = {
                        asset_id: parseInt(assetId),
                        loss_reason: lossReason
                    };

                    console.log('Sending lost report request:', requestData);

                    // Send AJAX request
                    fetch("{{ route('asset.lost') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(requestData)
                    })
                        .then(response => response.json())
                        .then(data => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Report as Lost';

                            if (data.status === true) {
                                // Success - close the modal and reload without alert
                                closeModal(lostModal, lostModalContent);
                                window.location.reload();
                            } else {
                                // Error - keep alert for error messages
                                alert('Error: ' + (data.message || 'Failed to report asset as lost'));
                            }
                        })
                        .catch(error => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Report as Lost';
                            console.error('Error reporting asset as lost:', error);
                            alert('Failed to report asset as lost. Please try again.');
                        });
                });
            }

            // Found Asset Modal Functionality
            const foundBtn = document.getElementById('foundAssetBtn');
            const foundModal = document.getElementById('foundAssetModal');
            const foundModalContent = document.getElementById('foundAssetModalContent');
            const foundForm = document.getElementById('foundAssetForm');

            if (foundBtn && foundModal && foundModalContent && foundForm) {
                // Set the form action
                foundForm.action = "{{ route('asset.found') }}";

                // Open found modal
                foundBtn.addEventListener('click', function () {
                    // Open modal
                    openModal(foundModal, foundModalContent);
                });

                // Handle found form submission
                foundForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const submitBtn = document.getElementById('submitFound');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Processing...';

                    // Get form data
                    const formData = new FormData(foundForm);
                    const assetId = formData.get('asset_id');
                    const foundNotes = formData.get('found_notes');

                    // Prepare request data
                    const requestData = {
                        asset_id: parseInt(assetId),
                        found_notes: foundNotes
                    };

                    // Send AJAX request
                    fetch("{{ route('asset.found') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(requestData)
                    })
                        .then(response => response.json())
                        .then(data => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Report as Found';

                            if (data.status === true) {
                                // Success - close the modal and reload without alert
                                closeModal(foundModal, foundModalContent);
                                window.location.reload();
                            } else {
                                // Error
                                alert('Error: ' + (data.message || 'Failed to report asset as found'));
                            }
                        })
                        .catch(error => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Report as Found';
                            console.error('Error reporting asset as found:', error);
                            alert('Failed to report asset as found. Please try again.');
                        });
                });
            }

            // Dispose Asset Modal Functionality
            const disposeBtn = document.getElementById('disposeAssetBtn');
            const disposeModal = document.getElementById('disposeAssetModal');
            const disposeModalContent = document.getElementById('disposeAssetModalContent');
            const disposeForm = document.getElementById('disposeAssetForm');

            if (disposeBtn && disposeModal && disposeModalContent && disposeForm) {
                console.log('Dispose elements found');
                // Set the form action
                disposeForm.action = "{{ route('asset.dispose') }}";
                console.log('Form action set to:', disposeForm.action);

                // Open dispose modal
                disposeBtn.addEventListener('click', function (e) {
                    e.preventDefault(); // Important - prevent default link behavior
                    console.log('Dispose button clicked');
                    openModal(disposeModal, disposeModalContent);
                });

                // Handle form submission
                disposeForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    console.log('Form submitting to:', this.action);

                    const submitBtn = document.getElementById('submitDispose');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Processing...';

                    // Get form data
                    const formData = new FormData(disposeForm);
                    const assetId = formData.get('asset_id');
                    const disposalMethod = formData.get('disposal_method');
                    const disposalReason = formData.get('disposal_reason');
                    const disposalNotes = formData.get('disposal_notes');

                    // Prepare request data
                    const requestData = {
                        asset_id: parseInt(assetId),
                        transfer_type: 'DISPOSAL',
                        disposal_method: disposalMethod,
                        disposal_reason: disposalReason,
                        disposal_notes: disposalNotes
                    };

                    console.log('Sending dispose request:', requestData);

                    // Send AJAX request
                    fetch("{{ route('asset.dispose') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(requestData)
                    })
                        .then(response => response.json())
                        .then(data => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Dispose Asset';

                            if (data.status === true) {
                                // Success - close the modal and reload without alert
                                closeModal(disposeModal, disposeModalContent);
                                window.location.reload();
                            } else {
                                // Error - keep alert for error messages
                                alert('Error: ' + (data.message || 'Failed to dispose asset'));
                            }
                        })
                        .catch(error => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Dispose Asset';
                            console.error('Error disposing asset:', error);
                            alert('Failed to dispose asset. Please try again.');
                        });
                });
            } else {
                console.error('Some dispose elements not found:', {
                    disposeBtn: !!disposeBtn,
                    disposeModal: !!disposeModal,
                    disposeModalContent: !!disposeModalContent,
                    disposeForm: !!disposeForm
                });
            }

            // Handle building selection and filter rooms
            const buildingSelector = document.getElementById('building_selector');
            if (buildingSelector) {
                // Store all rooms from the controller
                const allRooms = @json($rooms);

                buildingSelector.addEventListener('change', function () {
                    const selectedBuildingId = parseInt(this.value);
                    const roomDropdown = document.getElementById('location_id');

                    // Clear existing options
                    roomDropdown.innerHTML = '';

                    // Add default option
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.text = 'Select a room';
                    defaultOption.disabled = true;
                    defaultOption.selected = true;
                    roomDropdown.appendChild(defaultOption);

                    // Filter rooms by selected building
                    const filteredRooms = allRooms.filter(room =>
                        room.building_id === selectedBuildingId ||
                        (room.building && parseInt(room.building.building_id) === selectedBuildingId)
                    );

                    // Add filtered room options
                    filteredRooms.forEach(room => {
                        const option = document.createElement('option');
                        option.value = room.room_id;
                        option.text = room.room_name;
                        roomDropdown.appendChild(option);
                    });
                });
            }
        });
    </script>

@endsection