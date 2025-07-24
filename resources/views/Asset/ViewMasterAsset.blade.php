
@extends('Layout.app')

@section('title', 'Detail Aset Master')

@section('content')
@include('Layout.loading')
<div class="p-4 md:p-6">
    <!-- Header with title and back button -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center">
            <a href="{{ route('asset-master') }}" class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-2xl md:text-[32px] font-semibold text-[#28356B]">
                DETAIL ASSET MASTER
            </h1>
        </div>
        <div class="flex flex-row overflow-x-auto gap-2 pb-2 w-full md:w-auto md:gap-3 justify-start md:justify-end no-scrollbar">
            @if(hasPermission('asset-master:edit'))
            <button data-master-asset-id="{{ $masterAsset['asset_master_id'] ?? '' }}" class="edit-master-asset-btn flex-shrink-0 flex items-center justify-center gap-2 px-2 py-2 md:px-4 md:py-3 border-2 border-[#28356B] rounded-lg text-[#28356B] hover:bg-[#28356B] hover:text-white transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                <span class="hidden md:inline">Ubah</span>
            </button>
            @endif
            @if(hasPermission('asset-master:export'))
            <a href="{{ route('export-view-master-asset-pdf', ['id' => $masterAsset['asset_master_id'] ?? '']) }}" target="_blank" class="flex-shrink-0 flex items-center justify-center gap-2 px-2 py-2 md:px-4 md:py-3 border-2 border-[#28356B] rounded-lg text-[#28356B] hover:bg-[#28356B] hover:text-white transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span class="hidden md:inline">Expor PDF</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Master Asset Information -->
    <div class="mb-6">
        <div class="bg-[#28356B] rounded-t-lg p-4">
            <h2 class="text-white font-semibold">Informasi Aset Master</h2>
        </div>
        <div class="bg-white p-6 rounded-b-lg border border-t-0 border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Asset Image -->
                <div>
                    @if(isset($masterAsset['reference_image_path']) && !empty($masterAsset['reference_image_path']))
                        <img src="{{ config('app.backend_url') }}/public{{ $masterAsset['reference_image_path'] }}"
                            alt="{{ $masterAsset['asset_name'] }}"
                            class="w-full h-auto object-cover rounded-md bg-gray-50 p-2"
                            onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.classList.remove('object-cover'); this.classList.add('object-contain', 'p-4');">
                    @else
                        <div class="w-full h-48 bg-gray-100 rounded-md flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-gray-400">Tidak ada gambar</span>
                        </div>
                    @endif
                </div>

                <!-- Asset Details - First Column -->
                <div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Nama Aset</p>
                        <p class="font-medium">{{ $masterAsset['asset_name'] ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Kode Aset</p>
                        <p class="font-medium">{{ $masterAsset['asset_master_code'] ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Jenis Aset</p>
                        @if(isset($masterAsset['asset_type']))
                            @if(strtolower($masterAsset['asset_type']) == 'medical')
                                <p class="font-medium">Medis</p>
                            @elseif(strtolower($masterAsset['asset_type']) == 'non_medical')
                                <p class="font-medium">Non Medis</p>
                            @else
                                <p class="font-medium">{{ $masterAsset['asset_type'] ?? 'N/A' }}</p>
                            @endif
                        @else
                            <p class="font-medium">{{ $masterAsset['asset_type'] ?? 'N/A' }}</p>
                        @endif
                    </div>
                </div>

                <!-- Asset Details - Second Column -->
                <div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Kategori</p>
                        <p class="font-medium">{{ $masterAsset['subcategory_name'] ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Tanggal Dibuat</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($masterAsset['created_at'] ?? now())->locale('id')->translatedFormat('d F Y') }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Karakteristik</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @if(isset($masterAsset['is_depreciable']) && $masterAsset['is_depreciable'])
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Aset Dapat Mengalami Depresiasi</span>
                            @endif

                            @if(isset($masterAsset['needs_calibration']) && $masterAsset['needs_calibration'])
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">Aset Memerlukan Kalibrasi</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description (Full Width) -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-500">Deskripsi</p>
                <p class="mt-1">{{ $masterAsset['description'] ?? 'Tidak ada deskripsi' }}</p>
            </div>
        </div>
    </div>

    <!-- Asset Units Section -->
    <div>
        <div class="bg-[#28356B] rounded-t-lg p-4 flex justify-between items-center">
            <h2 class="text-white font-semibold">Unit Aset</h2>
            <div class="bg-white text-[#28356B] text-sm font-bold px-3 py-1 rounded-full">
                Total: {{ count($masterAsset['linked_assets'] ?? []) }} unit
            </div>
        </div>

        @if(isset($masterAsset['linked_assets']) && count($masterAsset['linked_assets']) > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-left w-1/4">Kode Aset</th>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-center w-1/6">Kondisi</th>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-center w-1/6">Status</th>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-left w-1/3">Lokasi</th>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-center w-1/12">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($masterAsset['linked_assets'] as $asset)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 text-sm border-t border-gray-200 font-medium">
                                    {{ $asset['asset_code'] }}
                                </td>
                                <td class="p-4 text-sm border-t border-gray-200 text-center">
                                    @php
                                        $conditionClass = 'bg-gray-100 text-gray-800';
                                        $conditionText = 'Tidak Diketahui';

                                        if(isset($asset['condition'])) {
                                            switch(strtolower($asset['condition'])) {
                                                case 'good':
                                                    $conditionClass = 'bg-green-100 text-green-800';
                                                    $conditionText = 'Baik';
                                                    break;
                                                case 'slighly damage':
                                                    $conditionClass = 'bg-yellow-100 text-yellow-800';
                                                    $conditionText = 'Rusak Ringan';
                                                    break;
                                                case 'high damage':
                                                    $conditionClass = 'bg-red-100 text-red-800';
                                                    $conditionText = 'Rusak Berat';
                                                    break;
                                                default:
                                                    $conditionText = ucfirst($asset['condition']);
                                            }
                                        }
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $conditionClass }}">
                                        {{ $conditionText }}
                                    </span>
                                </td>
                                <td class="p-4 text-sm border-t border-gray-200 text-center">
                                    @php
                                        $statusClass = 'bg-gray-100 text-gray-800';

                                        if(isset($asset['current_status'])) {
                                            switch(strtolower($asset['current_status'])) {
                                                case 'available':
                                                    $statusClass = 'bg-[#659B09] text-white';
                                                    break;
                                                case 'check out':
                                                    $statusClass = 'bg-[#F59E0B] text-white';
                                                    break;
                                                case 'under repair':
                                                    $statusClass = 'bg-[#25B1FF] text-white';
                                                    break;
                                                case 'dispose':
                                                    $statusClass = 'bg-[#ACC3EF] text-white';
                                                    break;
                                                case 'lost':
                                                    $statusClass = 'bg-[#EF4444] text-white';
                                                    break;
                                            }
                                        }
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
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
                                                    case 'under repair':
                                                        $statusText = 'PERBAIKAN';
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
                                    </span>
                                </td>
                                <td class="p-4 text-sm border-t border-gray-200">
                                    <div class="font-medium">{{ $asset['room_name'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $asset['building_name'] ?? 'N/A' }}</div>
                                </td>
                                <td class="p-4 border-t border-gray-200 text-center">
                                    <a href="{{ route('asset.details', $asset['asset_id']) }}"
                                       class="inline-flex items-center justify-center w-10 h-10 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors"
                                       title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white p-4 text-center border border-t-0 border-gray-200 rounded-b-lg">
                <p class="text-gray-500">Tidak ada aset terhubung ke aset master ini.</p>
            </div>
        @endif
    </div>

    <!-- Edit Master Asset Modal -->
    @if(hasPermission('asset-master:edit'))
    <div id="editMasterAssetModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="editMasterAssetModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">UBAH DATA ASET MASTER</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="editMasterAssetForm" method="POST" action="{{ route('view-asset-master.update', $masterAsset['asset_master_id']) }}" data-no-loading enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="p-6">
                            <!-- Loading indicator -->
                            <div class="text-center" id="edit-loading">
                                <div class="inline-block w-8 h-8 border-4 border-[#213268] border-t-transparent rounded-full animate-spin"></div>
                                <p class="mt-2 text-gray-600">Memuat data aset...</p>
                            </div>

                            <div id="edit-form-content" class="space-y-4 hidden">
                                <!-- Asset Information Section -->
                                <h3 class="text-lg font-semibold text-[#213268] border-b pb-2">Informasi Aset</h3>

                                <!-- Image upload -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Gambar Aset</label>
                                    <div class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                        <!-- Current image preview -->
                                        <div id="edit-image-container" class="mt-2 mb-4 w-full hidden">
                                            <div class="relative bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                <img src="" class="w-full h-auto max-h-64 object-contain mx-auto rounded" alt="Asset Image">
                                                <button type="button" class="remove-image-btn absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="mt-1 text-sm text-gray-600">Seret gambar atau <span class="text-blue-600">pilih file</span></p>
                                            <p class="mt-1 text-xs text-gray-500">Format yang diterima: jpg, jpeg, png (Ukuran maks: 5MB)</p>
                                        </div>
                                        <input type="file" id="edit_image_file" name="image_file" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    </div>
                                </div>

                                <!-- Basic Asset Details -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="edit_asset_name" class="block text-base font-semibold text-[#666666]">Nama Aset <span class="text-red-500">*</span></label>
                                        <input type="text" name="asset_name" id="edit_asset_name" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                            placeholder="Nama Aset">
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Nama Aset harus diisi</div>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="edit_asset_type" class="block text-base font-semibold text-[#666666]">Tipe Aset <span class="text-red-500">*</span></label>
                                        <select name="asset_type" id="edit_asset_type" required
                                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]">
                                            <option value="">Pilih Tipe Aset</option>
                                            <option value="medical">Medis</option>
                                            <option value="non_medical">Non Medis</option>
                                        </select>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Tipe Aset harus dipilih</div>
                                    </div>
                                </div>

                                <!-- Subcategory Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Kategori <span
                                        class="text-red-500">*</span> <span class="text-xs text-blue-600">(Pilih
                                        tipe aset terlebih dahulu)</span></label>
                                    <div class="custom-select-container relative">
                                        <input type="text"
                                            class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] disabled:bg-gray-100 disabled:cursor-not-allowed"
                                            placeholder="Pilih tipe aset terlebih dahulu" disabled
                                            id="edit_subcategory_search">
                                        <input type="hidden" name="subcategory_id" id="edit_subcategory_id">
                                        <div
                                            class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer dropdown-icon">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                        <div
                                            class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                            <div class="p-2 text-center text-gray-500"
                                                id="edit-subcategory-loading-message">Pilih tipe aset terlebih dahulu
                                           </div>
                                            <!-- Subcategories will be loaded dynamically based on asset_type -->
                                        </div>
                                    </div>
                                    <div class="error-message text-red-500 text-sm mt-1 hidden">Kategori harus dipilih
                                    </div>
                                </div>


                                <div class="space-y-2">
                                    <label for="edit_description" class="block text-base font-semibold text-[#666666]">Deskripsi</label>
                                    <textarea name="description" id="edit_description"
                                        class="w-full h-[100px] px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] resize-none"
                                        placeholder="Deskripsi Aset"></textarea>
                                </div>

                                <!-- Custom toggles -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Depreciation Toggle Switch -->
                                <div class="flex items-center justify-between">
                                    <label for="edit_is_depreciable" class="text-base font-semibold text-[#666666]">Aktifkan Penyusutan Aset</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_depreciable" id="edit_is_depreciable" class="sr-only peer depreciation-toggle" value="1">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                            peer-checked:after:border-white after:content-[''] after:absolute
                                            after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                            after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                            peer-checked:bg-[#213268]"></div>
                                        <span class="ml-2 text-sm font-medium text-gray-900 depreciation-status">Tidak</span>
                                    </label>
                                </div>

                                <!-- Calibration Toggle Switch -->
                                <div class="flex items-center justify-between">
                                    <label for="edit_needs_calibration" class="text-base font-semibold text-[#666666]">Kalibrasi</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="needs_calibration" id="edit_needs_calibration" class="sr-only peer calibration-toggle" value="1">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                            peer-checked:after:border-white after:content-[''] after:absolute
                                            after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300
                                            after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                            peer-checked:bg-[#213268]"></div>
                                        <span class="ml-2 text-sm font-medium text-gray-900 calibration-status">Tidak</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" id="edit-submit-btn" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200 mt-6">
                                Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(!hasPermission('asset-master:edit'))
        const editButtons = document.querySelectorAll('.edit-master-asset-btn');
        editButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif

        function initCustomSelects() {
            document.querySelectorAll('.custom-select-container').forEach(container => {
                const searchInput = container.querySelector('.search-input');
                const hiddenInput = container.querySelector('input[type="hidden"]');
                const optionsContainer = container.querySelector('.options-container');
                const isSubcategory = hiddenInput.id === 'edit_subcategory_id';

                searchInput.addEventListener('click', () => {
                    optionsContainer.classList.remove('hidden');

                    if (isSubcategory) {
                        const assetType = document.getElementById('edit_asset_type').value;
                        if (assetType) {
                            fetchCategories(assetType, " ", hiddenInput.id);
                        }
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!container.contains(e.target)) {
                        optionsContainer.classList.add('hidden');
                    }
                });

                let debounceTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimeout);

                    debounceTimeout = setTimeout(() => {
                        const searchValue = this.value.toLowerCase().trim();

                        if (isSubcategory) {
                            const assetType = document.getElementById('edit_asset_type').value;
                            if (assetType) {
                                fetchCategories(assetType, searchValue, hiddenInput.id);
                            }
                        }
                    }, 300);
                });
            });
        }

        const editButtons = document.querySelectorAll('.edit-master-asset-btn');
        const editModal = document.getElementById('editMasterAssetModal');
        const editModalContent = document.getElementById('editMasterAssetModalContent');
        const closeButtons = document.querySelectorAll('.close-modal');
        const editForm = document.getElementById('editMasterAssetForm');
        const editFormSpinner = document.getElementById('edit-loading');
        const editFormContent = document.getElementById('edit-form-content');

        function fetchMasterAssetDetails(assetId) {
            fetch(`/view-asset-master/${assetId}?json=true`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw errorData;
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.masterAsset) {
                    document.getElementById('edit_asset_type').value = data.masterAsset.asset_type || '';

                    initCustomSelects();

                    if (data.masterAsset.asset_type) {
                        const subcategoryContainer = document.querySelector('#edit_subcategory_id').closest('.custom-select-container');
                        const subcategorySearchInput = subcategoryContainer.querySelector('.search-input');
                        subcategorySearchInput.disabled = false;
                        subcategorySearchInput.placeholder = "Cari kategori...";

                        fetchCategories(data.masterAsset.asset_type, '', 'edit_subcategory_id');
                    }

                    populateEditForm(data.masterAsset);

                    editFormSpinner.classList.add('hidden');
                    editFormContent.classList.remove('hidden');
                } else {
                    console.error('Gagal mengambil detail aset master');
                    closeEditModal();
                    showToast('Gagal memuat detail aset master. Silakan coba lagi.', 'error');
                }
            })
            .catch(error => {
                console.error('Error fetching master asset details:', error);

                let errorMessage = 'Gagal memuat detail aset master. Silakan coba lagi.';
                if (typeof error === 'object' && error !== null) {
                    if (error.message) {
                        errorMessage = error.message;
                    } else if (error.errors) {
                        const errorMessages = [];
                        for (const field in error.errors) {
                            if (Array.isArray(error.errors[field])) {
                                errorMessages.push(...error.errors[field]);
                            } else if (typeof error.errors[field] === 'string') {
                                errorMessages.push(error.errors[field]);
                            }
                        }
                        errorMessage = errorMessages.join('\n');
                    }
                }

                closeEditModal();
                showToast(errorMessage, 'error');
            });
        }

        editForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const assetNameInput = document.getElementById('edit_asset_name');
            const assetTypeInput = document.getElementById('edit_asset_type');
            const subcategoryInput = document.getElementById('edit_subcategory_id');

            let isValid = true;

            if (!assetNameInput.value.trim()) {
                assetNameInput.classList.add('border-red-500');
                const errorElement = assetNameInput.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) {
                    errorElement.textContent = 'Nama Aset harus diisi';
                    errorElement.classList.remove('hidden');
                }
                isValid = false;
            }

            if (!assetTypeInput.value) {
                assetTypeInput.classList.add('border-red-500');
                const errorElement = assetTypeInput.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) {
                    errorElement.textContent = 'Tipe Aset harus dipilih';
                    errorElement.classList.remove('hidden');
                }
                isValid = false;
            }

            const subcategoryContainer = subcategoryInput.closest('.custom-select-container');
            const subcategorySearchInput = subcategoryContainer.querySelector('.search-input');
            if (!subcategoryInput.value) {
                subcategorySearchInput.classList.add('border-red-500');
                const errorElement = subcategoryContainer.closest('.space-y-2')?.querySelector('.error-message');
                if (errorElement) {
                    errorElement.textContent = 'Kategori harus dipilih';
                    errorElement.classList.remove('hidden');
                }
                isValid = false;
            }

            if (!isValid) {
                showToast('Silakan isi semua field yang diperlukan', 'error');
                return;
            }

            const submitBtn = document.getElementById('edit-submit-btn');
            const formData = new FormData(this);
            const actionUrl = this.action;

            if (submitBtn && !submitBtn.disabled) {
                const originalText = submitBtn.innerHTML;
                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyimpan...
                `;

                // Ensure _method is set for Laravel's method spoofing
                if (this.getAttribute('method').toUpperCase() === 'PUT' || this.querySelector('input[name="_method"][value="PUT"]')) {
                    formData.set('_method', 'PUT');
                }

                fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                    submitBtn.innerHTML = originalText;

                    if (data.success) {
                        // Close modal on success
                        closeEditModal();
                        resetEditMasterAssetForm();

                        // Show success message
                        showToast(data.message || 'Data aset master berhasil diperbarui', 'success');

                        // Reload the page after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        // Clear previous errors first
                        document.querySelectorAll('#editMasterAssetModal .error-message').forEach(el => {
                            el.classList.add('hidden');
                        });
                        document.querySelectorAll('#editMasterAssetModal input, #editMasterAssetModal select, #editMasterAssetModal textarea').forEach(el => {
                            el.classList.remove('border-red-500');
                        });

                        // Handle validation errors
                        if (data.errors) {
                            let errorMessage = '';

                            // Process each error field
                            Object.keys(data.errors).forEach(field => {
                                const errorMessages = data.errors[field];
                                const fieldElement = document.getElementById('edit_' + field);

                                if (fieldElement) {
                                    // Highlight the field
                                    fieldElement.classList.add('border-red-500');

                                    // Find and show error message container
                                    const errorContainer = fieldElement.closest('.space-y-2')?.querySelector('.error-message');
                                    if (errorContainer) {
                                        errorContainer.textContent = Array.isArray(errorMessages) ? errorMessages[0] : errorMessages;
                                        errorContainer.classList.remove('hidden');
                                    }
                                }

                                // Special handling for subcategory field (custom select)
                                if (field === 'subcategory_id') {
                                    const container = document.querySelector('#edit_subcategory_id').closest('.custom-select-container');
                                    const searchInput = container.querySelector('.search-input');
                                    searchInput.classList.add('border-red-500');

                                    const errorContainer = container.closest('.space-y-2')?.querySelector('.error-message');
                                    if (errorContainer) {
                                        errorContainer.textContent = Array.isArray(errorMessages) ? errorMessages[0] : errorMessages;
                                        errorContainer.classList.remove('hidden');
                                    }
                                }

                                // Build error message for toast
                                if (Array.isArray(errorMessages)) {
                                    errorMessage += errorMessages.join(', ');
                                } else {
                                    errorMessage += errorMessages;
                                }
                            });

                            // Show toast with all error messages
                            showToast(errorMessage || data.message || 'Gagal memperbarui data aset master', 'error');
                        } else {
                            // Show generic error message
                        showToast(data.message || 'Gagal memperbarui data aset master', 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating asset:', error);
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                    submitBtn.innerHTML = originalText;
                    showToast('Terjadi kesalahan saat memperbarui aset master', 'error');
                });
            }
        });

        function showFieldError(field, message = null) {
            field.classList.add('border-red-500');
            const errorElement = field.closest('.space-y-2').querySelector('.error-message');
            if (errorElement) {
                if (message) {
                    errorElement.textContent = message;
                }
                errorElement.classList.remove('hidden');
            }
        }

        function clearFieldError(field) {
            field.classList.remove('border-red-500');
            const errorElement = field.closest('.space-y-2').querySelector('.error-message');
            if (errorElement) {
                // Reset to default error message
                const defaultMessage = field.id === 'edit_asset_name' ? 'Nama Aset harus diisi' :
                                      field.id === 'edit_asset_type' ? 'Tipe Aset harus dipilih' :
                                      'Field ini harus diisi';
                errorElement.textContent = defaultMessage;
                errorElement.classList.add('hidden');
            }
        }

        document.getElementById('edit_asset_name').addEventListener('input', function() {
            clearFieldError(this);
        });

        document.getElementById('edit_asset_type').addEventListener('change', function() {
            const selectedType = this.value;
            clearFieldError(this);

            const container = document.querySelector('#edit_subcategory_id').closest('.custom-select-container');
            const searchInput = container.querySelector('.search-input');
            const hiddenInput = document.getElementById('edit_subcategory_id');

            searchInput.value = '';
            hiddenInput.value = '';

            if (selectedType) {
                searchInput.disabled = false;
                searchInput.placeholder = "Cari kategori...";
                fetchCategories(selectedType, " ", 'edit_subcategory_id');
            } else {
                searchInput.disabled = true;
                searchInput.placeholder = "Pilih tipe aset terlebih dahulu";
            }
        });

        const openModal = function(modal, content) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
            }, 10);
        };

        const closeModal = function(modal, content) {
            content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
            content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        };

        function closeEditModal() {
            closeModal(editModal, editModalContent);
        }

        function resetEditMasterAssetForm() {
            editForm.reset();

            const imagePreview = document.getElementById('edit-image-container');
            if (imagePreview) {
                imagePreview.classList.add('hidden');
            }

            const removeImageInput = editForm.querySelector('input[name="remove_image"]');
            if (removeImageInput) {
                removeImageInput.remove();
            }
        }

        editModal.addEventListener('mousedown', function(event) {
            if (event.target === this) {
                closeEditModal();
                resetEditMasterAssetForm();
                resetSubmitButton();
            }
        });

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const assetId = this.getAttribute('data-master-asset-id');
                if (!assetId) {
                    console.error('No asset ID provided');
                    return;
                }

                editFormSpinner.classList.remove('hidden');
                editFormContent.classList.add('hidden');
                editForm.action = `/view-asset-master/${assetId}`;
                openModal(editModal, editModalContent);

                fetchMasterAssetDetails(assetId);
            });
        });

        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                closeEditModal();
                resetEditMasterAssetForm();
                resetSubmitButton();
            });
        });

        function resetSubmitButton() {
            const submitBtn = document.getElementById('edit-submit-btn');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Perbarui';
        }

        function populateEditForm(masterAsset) {
            document.getElementById('edit_asset_name').value = masterAsset.asset_name || '';
            document.getElementById('edit_description').value = masterAsset.description || '';

            setTimeout(() => {
                if (masterAsset.subcategory_id && masterAsset.subcategory_name) {
                    setSelectValue('edit_subcategory_id', masterAsset.subcategory_id, masterAsset.subcategory_name);
                }
            }, 500);

            const isDepreciable = document.getElementById('edit_is_depreciable');
            isDepreciable.checked = !!masterAsset.is_depreciable;
            document.querySelector('#editMasterAssetModal .depreciation-status').textContent =
                masterAsset.is_depreciable ? 'Ya' : 'Tidak';

            const needsCalibration = document.getElementById('edit_needs_calibration');
            needsCalibration.checked = !!masterAsset.needs_calibration;
            document.querySelector('#editMasterAssetModal .calibration-status').textContent =
                masterAsset.needs_calibration ? 'Ya' : 'Tidak';

            if (masterAsset.reference_image_path) {
                const imageContainer = document.getElementById('edit-image-container');
                const imageElement = imageContainer.querySelector('img');
                imageContainer.classList.remove('hidden');
                imageElement.src = `${window.appConfig.backendUrl}/public${masterAsset.reference_image_path}`;
                imageElement.onerror = function() {
                    this.onerror = null;
                };
            } else {
                document.getElementById('edit-image-container').classList.add('hidden');
            }
        }

        function setSelectValue(id, value, displayText) {
            const hiddenInput = document.getElementById(id);
            if (!hiddenInput) return;

            const container = hiddenInput.closest('.custom-select-container');
            const searchInput = container.querySelector('.search-input');

            hiddenInput.value = value;
            searchInput.value = displayText;

            searchInput.classList.remove('border-red-500');
            const errorElement = container.closest('.space-y-2').querySelector('.error-message');
            if (errorElement) errorElement.classList.add('hidden');
        }

        document.getElementById('edit_image_file').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const imageContainer = document.getElementById('edit-image-container');
                    const imageElement = imageContainer.querySelector('img');

                    imageElement.src = e.target.result;
                    imageContainer.classList.remove('hidden');

                    const removeCheckbox = document.getElementById('edit_remove_image');
                    if (removeCheckbox) {
                        removeCheckbox.checked = false;
                    }
                };

                reader.readAsDataURL(file);
            }
        });

        const removeImageBtns = document.querySelectorAll('.remove-image-btn');
        removeImageBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const imageContainer = document.getElementById('edit-image-container');
                imageContainer.classList.add('hidden');

                document.getElementById('edit_image_file').value = '';

                let removeImageInput = editForm.querySelector('input[name="remove_image"]');
                if (!removeImageInput) {
                    removeImageInput = document.createElement('input');
                    removeImageInput.type = 'hidden';
                    removeImageInput.name = 'remove_image';
                    editForm.appendChild(removeImageInput);
                }
                removeImageInput.value = '1';
            });
        });

        document.getElementById('edit_is_depreciable').addEventListener('change', function() {
            const statusText = document.querySelector('#editMasterAssetModal .depreciation-status');
            statusText.textContent = this.checked ? 'Ya' : 'Tidak';
        });

        document.getElementById('edit_needs_calibration').addEventListener('change', function() {
            const statusText = document.querySelector('#editMasterAssetModal .calibration-status');
            statusText.textContent = this.checked ? 'Ya' : 'Tidak';
        });

        window.appConfig = {
            backendUrl: '{{ config('app.backend_url') }}'
        };

        initCustomSelects();

        window.testModal = function() {
            const modal = document.getElementById('editMasterAssetModal');
            const content = document.getElementById('editMasterAssetModalContent');
            modal.style.display = 'block';
            content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
            content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
        }

        function showToast(message, type = 'success') {
            const notification = document.createElement('div');
            notification.id = type + 'Notification' + Date.now();
            notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
            notification.role = 'alert';

            const hasHTML = typeof message === 'string' && /<[a-z][\s\S]*>/i.test(message);
            const isObject = typeof message === 'object' && message !== null;

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
                            <div>${isObject ? message.message || 'Operasi berhasil' : message}</div>
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

                if (isObject) {
                    let errorContent = '';

                    if (message.message) {
                        errorContent = `<p>${message.message}</p>`;
                    } else {
                        errorContent = '<p>Terjadi kesalahan</p>';
                    }

                    if (message.errors) {
                        errorContent += '<ul class="mt-2 ml-4 list-disc">';

                        if (Array.isArray(message.errors)) {
                            message.errors.forEach(err => {
                                if (typeof err === 'string') {
                                    errorContent += `<li>${err}</li>`;
                                } else if (typeof err === 'object' && err !== null) {
                                    if (err.path && err.message) {
                                        errorContent += `<li>${err.path}: ${err.message}</li>`;
                                    } else if (err.message) {
                                        errorContent += `<li>${err.message}</li>`;
                                    } else {
                                        const values = Object.values(err).filter(v => typeof v === 'string');
                                        if (values.length > 0) {
                                            errorContent += `<li>${values.join(': ')}</li>`;
                                        }
                                    }
                                }
                            });
                        } else if (typeof message.errors === 'object') {
                            Object.entries(message.errors).forEach(([field, fieldErrors]) => {
                                if (Array.isArray(fieldErrors)) {
                                    fieldErrors.forEach(err => errorContent += `<li>${field}: ${err}</li>`);
                                } else if (typeof fieldErrors === 'string') {
                                    errorContent += `<li>${field}: ${fieldErrors}</li>`;
                                }
                            });
                        }

                        errorContent += '</ul>';
                    }

                    if (message.data && message.data.errors) {
                        errorContent += '<ul class="mt-2 ml-4 list-disc">';

                        if (Array.isArray(message.data.errors)) {
                            message.data.errors.forEach(err => {
                                if (typeof err === 'string') {
                                    errorContent += `<li>${err}</li>`;
                                } else if (typeof err === 'object' && err !== null) {
                                    if (err.path && err.message) {
                                        errorContent += `<li>${err.path}: ${err.message}</li>`;
                                    } else if (err.message) {
                                        errorContent += `<li>${err.message}</li>`;
                                    } else if (err.reason) {
                                        errorContent += `<li>${err.reason}</li>`;
                                    }
                                }
                            });
                        } else if (typeof message.data.errors === 'object') {
                            Object.entries(message.data.errors).forEach(([field, fieldErrors]) => {
                                if (Array.isArray(fieldErrors)) {
                                    fieldErrors.forEach(err => errorContent += `<li>${field}: ${err}</li>`);
                                } else if (typeof fieldErrors === 'string') {
                                    errorContent += `<li>${field}: ${fieldErrors}</li>`;
                                }
                            });
                        }

                        errorContent += '</ul>';
                    }

                    messageContainer.innerHTML = errorContent;
                } else {
                    if (hasHTML) {
                        messageContainer.innerHTML = message;
                    } else {
                        messageContainer.textContent = message;
                    }
                }

                contentContainer.appendChild(messageContainer);

                const closeBtn = document.createElement('span');
                closeBtn.className = 'ml-4 cursor-pointer flex-shrink-0';
                closeBtn.textContent = '×';
                closeBtn.onclick = function() {
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

        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        function fetchCategories(assetType, searchTerm = '', targetId = '') {
            if (!assetType || !targetId) return;

            const container = document.getElementById(targetId).closest('.custom-select-container');
            const optionsContainer = container.querySelector('.options-container');
            const searchInput = container.querySelector('.search-input');
            const hiddenInput = container.querySelector('input[type="hidden"]');
            const isShowAll = searchTerm === " ";

            if (!searchTerm.trim() && !isShowAll) {
                optionsContainer.classList.add('hidden');
                return;
            }

            optionsContainer.innerHTML = '<div class="p-2 text-center text-gray-500">Memuat kategori...</div>';
            optionsContainer.classList.remove('hidden');

            // Add these variables for lazy loading
            let page = 1;
            const perPage = 15;
            let isLoading = false;
            let hasMoreData = true;
            let allCategories = [];

            // Function to load categories with pagination
            function loadCategories(page, searchValue) {
                if (isLoading || !hasMoreData) return;

                isLoading = true;

                let queryParams = new URLSearchParams();
                queryParams.append('json', 'true');
                queryParams.append('asset_type', assetType);
                queryParams.append('page', page);
                queryParams.append('limit', perPage);

                if (searchValue.trim() && !isShowAll) {
                    queryParams.append('search', searchValue.trim());
                }

                fetch(`/categories?${queryParams.toString()}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Server responded with status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        let categories = [];
                        let pagination = null;

                        if (Array.isArray(data)) {
                            categories = data;
                        } else if (data.subcategories && Array.isArray(data.subcategories)) {
                            categories = data.subcategories;
                            pagination = data.pagination || null;
                        } else if (data.data && Array.isArray(data.data)) {
                            categories = data.data;
                            pagination = data.pagination || data.meta || null;
                        }

                        allCategories = [...allCategories, ...categories];

                        // Check if we have more data to load
                        if (pagination) {
                            hasMoreData = pagination.current_page < pagination.last_page;
                        } else {
                            hasMoreData = categories.length >= perPage;
                        }

                        displayCategoryResults(allCategories, optionsContainer, hiddenInput, searchInput, assetType, true);

                        isLoading = false;
                    })
                    .catch(error => {
                        console.error('Error fetching categories:', error);
                        if (page === 1) {
                            optionsContainer.innerHTML = `
                            <div class="p-3 text-sm text-red-500 text-center">
                                <p>Gagal memuat kategori</p>
                                <p class="text-xs mt-1 text-red-400">${error.message}</p>
                                <button class="mt-2 px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 text-xs" onclick="this.closest('.options-container').classList.add('hidden')">Tutup</button>
                            </div>`;
                        }
                        isLoading = false;
                    });
            }

            // Initial load
            loadCategories(page, searchTerm);

            // Remove any existing scroll event listeners
            optionsContainer.removeEventListener('scroll', categoryScrollHandler);

            // Scroll event handler for lazy loading
            function categoryScrollHandler() {
                const { scrollTop, scrollHeight, clientHeight } = optionsContainer;

                // Load more data when user scrolls to 80% of the container
                if (scrollTop + clientHeight >= scrollHeight * 0.8 && hasMoreData && !isLoading) {
                    page++;
                    loadCategories(page, searchTerm);
                }
            }

            // Add scroll event listener
            optionsContainer.addEventListener('scroll', categoryScrollHandler);
        }

        function displayCategoryResults(categories, resultsElem, idInputElem, searchInputElem, assetType, isLazyLoad = false) {
            // Always clear the initial loading message
            const initialLoadingMessage = resultsElem.querySelector('div:not(.option):not(.loading-indicator):not(.dropdown-header)');
            if (initialLoadingMessage && initialLoadingMessage.textContent.includes('Memuat kategori')) {
                initialLoadingMessage.remove();
            }

            if (!isLazyLoad) {
                resultsElem.innerHTML = '';
            } else {
                // Remove loading indicator if it exists
                const loadingIndicator = resultsElem.querySelector('.loading-indicator');
                if (loadingIndicator) {
                    loadingIndicator.remove();
                }
            }

            if (categories.length === 0 && !isLazyLoad) {
                resultsElem.innerHTML = `
                    <div class="p-4 text-center">
                        <p class="text-gray-500 mb-2">Tidak ada kategori yang ditemukan</p>
                        <button class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 text-xs" onclick="this.closest('.options-container').classList.add('hidden')">Tutup</button>
                    </div>
                `;
                return;
            }

            // Only add the header if it doesn't exist yet
            const existingHeader = resultsElem.querySelector('.dropdown-header');
            if (!existingHeader && categories.length > 0) {
                const typeTitle = document.createElement('div');
                typeTitle.className = 'dropdown-header p-2 text-sm font-medium text-gray-600 border-b sticky top-0 bg-white z-10';
                typeTitle.textContent = `Kategori ${assetType === 'medical' ? 'Medis' : 'Non-Medis'}`;
                resultsElem.insertBefore(typeTitle, resultsElem.firstChild);
            }

            if (searchInputElem && searchInputElem.value.trim() && !isLazyLoad) {
                const searchTerm = searchInputElem.value.trim().toLowerCase();
                categories.sort((a, b) => {
                    const aName = a.subcategory_name?.toLowerCase() || '';
                    const bName = b.subcategory_name?.toLowerCase() || '';

                    if (aName === searchTerm) return -1;
                    if (bName === searchTerm) return 1;

                    const aStarts = aName.startsWith(searchTerm);
                    const bStarts = bName.startsWith(searchTerm);
                    if (aStarts && !bStarts) return -1;
                    if (bStarts && !aStarts) return 1;

                    return aName.localeCompare(bName);
                });
            }

            const existingOptions = new Set();
            const existingOptionElements = resultsElem.querySelectorAll('.option');

            existingOptionElements.forEach(element => {
                existingOptions.add(element.getAttribute('data-value'));
            });

            categories.forEach((category) => {
                // Skip duplicates that might occur during lazy loading
                if (existingOptions.has(category.subcategory_id?.toString())) {
                    return;
                }

                const div = document.createElement('div');
                div.className = 'option p-3 hover:bg-gray-100 cursor-pointer text-[#666666]';
                div.textContent = category.subcategory_name || 'Unknown Category';
                div.setAttribute('data-value', category.subcategory_id || '');
                div.setAttribute('data-type', category.asset_type || assetType);

                div.addEventListener('click', function() {
                    idInputElem.value = this.getAttribute('data-value');
                    searchInputElem.value = this.textContent;
                    searchInputElem.classList.remove('border-red-500');
                    const errorElement = searchInputElem.closest('.space-y-2').querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');
                    resultsElem.classList.add('hidden');
                    const event = new Event('change', { bubbles: true });
                    idInputElem.dispatchEvent(event);
                });

                resultsElem.appendChild(div);
                existingOptions.add(category.subcategory_id?.toString());
            });

            // Add loading indicator at the bottom for lazy loading
            if (isLazyLoad) {
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'loading-indicator p-2 text-xs text-gray-500 text-center';
                loadingDiv.textContent = 'Memuat kategori lainnya...';
                resultsElem.appendChild(loadingDiv);
            }

            // Add max height and scrollable class if not already set
            if (!resultsElem.classList.contains('scrollable-dropdown')) {
                resultsElem.classList.add('scrollable-dropdown');
                resultsElem.style.maxHeight = '250px';
                resultsElem.style.overflowY = 'auto';
            }
        }
    });
</script>
@endpush

