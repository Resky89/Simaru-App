@extends('Layout.app')

@section('title', 'Detail Aset Master')

@section('content')
@include('Layout.loading')
<div class="p-4 md:p-6">
    <!-- Header with title and back button -->
    <div class="flex justify-between items-center mb-6">
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
        <div class="flex gap-2">
            @if(hasPermission('asset-master:edit'))
            <button data-master-asset-id="{{ $masterAsset['asset_master_id'] ?? '' }}" class="edit-master-asset-btn flex items-center gap-2 px-4 py-3 border-2 border-[#28356B] rounded-lg text-[#28356B] hover:bg-[#28356B] hover:text-white transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Ubah
            </button>
            @endif
            @if(hasPermission('asset-master:export'))
            <a href="{{ route('export-view-master-asset-pdf', ['id' => $masterAsset['asset_master_id'] ?? '']) }}" target="_blank" class="flex items-center gap-2 px-4 py-3 border-2 border-[#28356B] rounded-lg text-[#28356B] hover:bg-[#28356B] hover:text-white transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Expor PDF
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
                        <p class="font-medium">{{ isset($masterAsset['asset_type']) ? ucwords(str_replace('_', ' ', $masterAsset['asset_type'])) : 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Kategori</p>
                        <p class="font-medium">{{ $masterAsset['subcategory_name'] ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Asset Details - Second Column -->
                <div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">Merk</p>
                        <p class="font-medium">{{ $masterAsset['brand_name'] ?? 'N/A' }}</p>
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
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-left">Kode Aset</th>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-left">Kondisi</th>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-left">Status</th>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-left">Lokasi</th>
                            <th class="bg-[#28356B] text-white p-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($masterAsset['linked_assets'] as $asset)
                            <tr>
                                <td class="p-3 text-sm border-t border-gray-200">
                                    {{ $asset['asset_code'] }}
                                </td>
                                <td class="p-3 text-sm border-t border-gray-200">
                                    @php
                                        $conditionClass = 'bg-gray-100 text-gray-800';

                                        if(isset($asset['condition'])) {
                                            switch(strtolower($asset['condition'])) {
                                                case 'good':
                                                    $conditionClass = 'bg-green-100 text-green-800';
                                                    break;
                                                case 'fair':
                                                case 'slightly damage':
                                                    $conditionClass = 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'poor':
                                                    $conditionClass = 'bg-orange-100 text-orange-800';
                                                    break;
                                                case 'high damage':
                                                    $conditionClass = 'bg-red-100 text-red-800';
                                                    break;
                                            }
                                        }
                                    @endphp
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $conditionClass }}">
                                        {{ ucfirst($asset['condition'] ?? 'Unknown') }}
                                    </span>
                                </td>
                                <td class="p-3 text-sm border-t border-gray-200">
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
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
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
                                <td class="p-3 text-sm border-t border-gray-200">
                                    <div>{{ $asset['room_name'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $asset['building_name'] ?? 'N/A' }}</div>
                                </td>
                                <td class="p-3 border-t border-gray-200 text-center">
                                    <a href="{{ route('asset.details', $asset['asset_id']) }}" class="text-[#28356B] hover:text-blue-800">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">UBAH ASSET MASTER</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="editMasterAssetForm" method="POST" action="{{ route('asset-master.update', $masterAsset['asset_master_id']) }}" data-no-loading enctype="multipart/form-data">
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
                                            <p class="mt-1 text-xs text-gray-500">jpg, jpeg, png</p>
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

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Subcategory Dropdown -->
                                    <div class="space-y-2">
                                        <label for="edit_subcategory_id" class="block text-base font-semibold text-[#666666]">Kategori <span class="text-red-500">*</span> <span class="text-xs text-blue-600">(Pilih tipe aset terlebih dahulu)</span></label>
                                        <div class="custom-select-container relative">
                                            <input type="text" class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] disabled:bg-gray-100 disabled:cursor-not-allowed"
                                                placeholder="Pilih tipe aset terlebih dahulu" disabled id="edit_subcategory_search">
                                            <input type="hidden" name="subcategory_id" id="edit_subcategory_id" required>
                                            <div class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                                <div class="p-2 text-center text-gray-500" id="edit-subcategory-loading-message">Pilih tipe aset terlebih dahulu</div>
                                                <!-- Subcategories will be loaded dynamically based on asset_type -->
                                            </div>
                                        </div>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Kategori harus dipilih</div>
                                    </div>

                                    <!-- Brand Dropdown -->
                                    <div class="space-y-2">
                                        <label for="edit_brand_id" class="block text-base font-semibold text-[#666666]">Merk <span class="text-red-500">*</span></label>
                                        <div class="custom-select-container relative">
                                            <input type="text" class="search-input w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268]"
                                                placeholder="Cari merk...">
                                            <input type="hidden" name="brand_id" id="edit_brand_id" required>
                                            <div class="options-container hidden absolute z-10 w-full mt-1 bg-white border border-[#CCCCCC] rounded-lg max-h-60 overflow-y-auto">
                                                <div class="p-2 text-center text-gray-500" id="edit-brand-loading-message">Memuat data merk...</div>
                                                <!-- Brands will be loaded dynamically -->
                                            </div>
                                        </div>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Merk harus dipilih</div>
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
        // Permission-aware initialization
        @if(!hasPermission('asset-master:edit'))
        // Disable edit functionality if user doesn't have permission
        const editButtons = document.querySelectorAll('.edit-master-asset-btn');
        editButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif

        // Custom select dropdown functionality
        function initCustomSelects() {
            document.querySelectorAll('.custom-select-container').forEach(container => {
                const searchInput = container.querySelector('.search-input');
                const hiddenInput = container.querySelector('input[type="hidden"]');
                const optionsContainer = container.querySelector('.options-container');

                // Determine what kind of dropdown this is
                const isSubcategory = hiddenInput.id === 'edit_subcategory_id';
                const isBrand = hiddenInput.id === 'edit_brand_id';

                // Show options when input is clicked (not on focus)
                searchInput.addEventListener('click', () => {
                    optionsContainer.classList.remove('hidden');

                    // Load data if needed
                    if (isSubcategory) {
                        const assetType = document.getElementById('edit_asset_type').value;
                        if (assetType) {
                            fetchCategories(assetType, " ", hiddenInput.id);
                        }
                    } else if (isBrand) {
                        fetchBrands(" ", hiddenInput.id);
                    }
                });

                // Hide options when clicking outside
                document.addEventListener('click', (e) => {
                    if (!container.contains(e.target)) {
                        optionsContainer.classList.add('hidden');
                    }
                });

                // Search functionality with debounce
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
                        } else if (isBrand) {
                            fetchBrands(searchValue, hiddenInput.id);
                        }
                    }, 300); // 300ms debounce
                });
            });
        }

        // Edit Master Asset Functionality
        const editButtons = document.querySelectorAll('.edit-master-asset-btn');
        const editModal = document.getElementById('editMasterAssetModal');
        const editModalContent = document.getElementById('editMasterAssetModalContent');
        const closeButtons = document.querySelectorAll('.close-modal');
        const editForm = document.getElementById('editMasterAssetForm');
        const editFormSpinner = document.getElementById('edit-loading');
        const editFormContent = document.getElementById('edit-form-content');

        // Form validation for Edit Master Asset
        editForm.addEventListener('submit', function(event) {
            const assetNameInput = document.getElementById('edit_asset_name');
            const assetTypeInput = document.getElementById('edit_asset_type');
            const subcategoryInput = document.getElementById('edit_subcategory_id');
            const brandInput = document.getElementById('edit_brand_id');

            let isValid = true;

            // Validate asset name
            if (!assetNameInput.value.trim()) {
                showFieldError(assetNameInput);
                isValid = false;
            }

            // Validate asset type
            if (!assetTypeInput.value) {
                showFieldError(assetTypeInput);
                isValid = false;
            }

            // Validate subcategory
            if (!subcategoryInput.value) {
                const subcategoryContainer = subcategoryInput.closest('.custom-select-container');
                const searchInput = subcategoryContainer.querySelector('.search-input');
                showFieldError(searchInput);
                isValid = false;
            }

            // Validate brand
            if (!brandInput.value) {
                const brandContainer = brandInput.closest('.custom-select-container');
                const searchInput = brandContainer.querySelector('.search-input');
                showFieldError(searchInput);
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
                // Show error toast
                showToast('Silakan isi semua field yang diperlukan', 'error');
                // Reset submit button if validation fails
                resetSubmitButton();
                return;
            }

            // Prevent multiple form submissions
            const submitBtn = document.getElementById('edit-submit-btn');
            if (submitBtn.disabled) {
                event.preventDefault();
                return; // Form is already being submitted
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            `;

            // Let the form submit proceed
        });

        // Function to show field error
        function showFieldError(field) {
            field.classList.add('border-red-500');
            const errorElement = field.closest('.space-y-2').querySelector('.error-message');
            if (errorElement) errorElement.classList.remove('hidden');
        }

        // Function to clear field error
        function clearFieldError(field) {
            field.classList.remove('border-red-500');
            const errorElement = field.closest('.space-y-2').querySelector('.error-message');
            if (errorElement) errorElement.classList.add('hidden');
        }

        // Add input event listeners to clear error styling when typing
        document.getElementById('edit_asset_name').addEventListener('input', function() {
            clearFieldError(this);
        });

        // Asset type change should filter subcategories
        document.getElementById('edit_asset_type').addEventListener('change', function() {
            const selectedType = this.value;
            clearFieldError(this);

            // Reset subcategory selection
            const container = document.querySelector('#edit_subcategory_id').closest('.custom-select-container');
            const searchInput = container.querySelector('.search-input');
            const hiddenInput = document.getElementById('edit_subcategory_id');

            searchInput.value = '';
            hiddenInput.value = '';

            // Enable or disable subcategory search based on asset type
            if (selectedType) {
                searchInput.disabled = false;
                searchInput.placeholder = "Cari kategori...";
                // Load subcategories for the new asset type
                fetchCategories(selectedType, " ", 'edit_subcategory_id');
            } else {
                searchInput.disabled = true;
                searchInput.placeholder = "Pilih tipe aset terlebih dahulu";
            }
        });

        // Modal Open/Close functions
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

            // Hide image preview if exists
            const imagePreview = document.getElementById('edit-image-container');
            if (imagePreview) {
                imagePreview.classList.add('hidden');
            }

            // Remove any hidden input for image removal
            const removeImageInput = editForm.querySelector('input[name="remove_image"]');
            if (removeImageInput) {
                removeImageInput.remove();
            }
        }

        // Close modal when clicking on the background
        editModal.addEventListener('mousedown', function(event) {
            if (event.target === this) {
                closeEditModal();
                resetEditMasterAssetForm();
                resetSubmitButton(); // Reset submit button state
            }
        });

        // Open edit modal when clicking edit button
        editButtons.forEach(button => {
            console.log('Button attached:', button);
            button.addEventListener('click', function() {
                console.log('Button clicked');
                const assetId = this.getAttribute('data-master-asset-id');
                console.log('Asset ID:', assetId);
                if (!assetId) {
                    console.error('No asset ID provided');
                    return;
                }

                // Show modal & spinner first
                editFormSpinner.classList.remove('hidden');
                editFormContent.classList.add('hidden');
                editForm.action = `/asset-master/${assetId}`;
                openModal(editModal, editModalContent);

                // Fetch asset data
                fetchMasterAssetDetails(assetId);
            });
        });

        // Close modal when clicking close button
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                closeEditModal();
                resetEditMasterAssetForm();
                resetSubmitButton(); // Reset submit button state
            });
        });

        // Reset submit button to original state
        function resetSubmitButton() {
            const submitBtn = document.getElementById('edit-submit-btn');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Perbarui';
        }

        // Fetch master asset details from the server
        function fetchMasterAssetDetails(assetId) {
            fetch(`/asset-master/${assetId}/edit`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.masterAsset) {
                    // First set the asset type to filter subcategories properly
                    document.getElementById('edit_asset_type').value = data.masterAsset.asset_type || '';

                    // Initialize custom selects
                    initCustomSelects();

                    // Load brands
                    fetchBrands('', 'edit_brand_id');

                    // Load subcategories based on the asset type
                    if (data.masterAsset.asset_type) {
                        // Enable subcategory input
                        const subcategoryContainer = document.querySelector('#edit_subcategory_id').closest('.custom-select-container');
                        const subcategorySearchInput = subcategoryContainer.querySelector('.search-input');
                        subcategorySearchInput.disabled = false;
                        subcategorySearchInput.placeholder = "Cari kategori...";

                        fetchCategories(data.masterAsset.asset_type, '', 'edit_subcategory_id');
                    }

                    // Then populate other form fields
                    populateEditForm(data.masterAsset);

                    // Hide spinner, show form
                    editFormSpinner.classList.add('hidden');
                    editFormContent.classList.remove('hidden');
                } else {
                    console.error('Gagal mengambil detail aset master');
                    closeEditModal();
                    alert('Gagal memuat detail aset master. Silakan coba lagi.');
                }
            })
            .catch(error => {
                console.error('Error fetching master asset details:', error);
                closeEditModal();
                alert('Gagal memuat detail aset master. Silakan coba lagi.');
            });
        }

        // Populate the edit form with data
        function populateEditForm(masterAsset) {
            // Basic fields
            document.getElementById('edit_asset_name').value = masterAsset.asset_name || '';
            document.getElementById('edit_description').value = masterAsset.description || '';

            // Wait a bit for dropdowns to load their data
            setTimeout(() => {
                // Set subcategory and brand using helper functions
                if (masterAsset.subcategory_id && masterAsset.subcategory_name) {
                    setSelectValue('edit_subcategory_id', masterAsset.subcategory_id, masterAsset.subcategory_name);
                }

                if (masterAsset.brand_id && masterAsset.brand_name) {
                    setSelectValue('edit_brand_id', masterAsset.brand_id, masterAsset.brand_name);
                }
            }, 500);

            // Checkboxes with toggle switch display
            const isDepreciable = document.getElementById('edit_is_depreciable');
            isDepreciable.checked = !!masterAsset.is_depreciable;
            document.querySelector('#editMasterAssetModal .depreciation-status').textContent =
                masterAsset.is_depreciable ? 'Ya' : 'Tidak';

            const needsCalibration = document.getElementById('edit_needs_calibration');
            needsCalibration.checked = !!masterAsset.needs_calibration;
            document.querySelector('#editMasterAssetModal .calibration-status').textContent =
                masterAsset.needs_calibration ? 'Ya' : 'Tidak';

            // Image preview
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

        // Helper function to set dropdown values
        function setSelectValue(id, value, displayText) {
            const hiddenInput = document.getElementById(id);
            if (!hiddenInput) return;

            const container = hiddenInput.closest('.custom-select-container');
            const searchInput = container.querySelector('.search-input');

            hiddenInput.value = value;
            searchInput.value = displayText;

            // Clear any error styling
            searchInput.classList.remove('border-red-500');
            const errorElement = container.closest('.space-y-2').querySelector('.error-message');
            if (errorElement) errorElement.classList.add('hidden');
        }

        // Image change
        document.getElementById('edit_image_file').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const imageContainer = document.getElementById('edit-image-container');
                    const imageElement = imageContainer.querySelector('img');

                    imageElement.src = e.target.result;
                    imageContainer.classList.remove('hidden');

                    // If there's a remove image checkbox, uncheck it
                    const removeCheckbox = document.getElementById('edit_remove_image');
                    if (removeCheckbox) {
                        removeCheckbox.checked = false;
                    }
                };

                reader.readAsDataURL(file);
            }
        });

        // Handle remove image button
        const removeImageBtns = document.querySelectorAll('.remove-image-btn');
        removeImageBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const imageContainer = document.getElementById('edit-image-container');
                imageContainer.classList.add('hidden');

                // Clear the file input
                document.getElementById('edit_image_file').value = '';

                // Add a hidden input to signal image removal
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

        // Toggle switches
        document.getElementById('edit_is_depreciable').addEventListener('change', function() {
            const statusText = document.querySelector('#editMasterAssetModal .depreciation-status');
            statusText.textContent = this.checked ? 'Ya' : 'Tidak';
        });

        document.getElementById('edit_needs_calibration').addEventListener('change', function() {
            const statusText = document.querySelector('#editMasterAssetModal .calibration-status');
            statusText.textContent = this.checked ? 'Ya' : 'Tidak';
        });

        // Initialize back-end URL from meta tag
        window.appConfig = {
            backendUrl: '{{ config('app.backend_url') }}'
        };

        // Initialize custom selects on page load
        initCustomSelects();

        // Tambahkan di bagian akhir script
        window.testModal = function() {
            console.log('Testing modal manually');
            const modal = document.getElementById('editMasterAssetModal');
            const content = document.getElementById('editMasterAssetModalContent');
            modal.style.display = 'block'; // Force display block
            content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
            content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
        }

        // Function to show toast notifications
        function showToast(message, type = 'success') {
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
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />'}
                    </svg>
                </div>
                <div>
                    <p class="font-bold">${type === 'success' ? 'Success!' : 'Gagal!'}</p>
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

        // Show toast notifications for session messages on page load
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        // Function to fetch brands with search parameter
        function fetchBrands(searchTerm = '', targetId = '') {
            if (!targetId) return;

            const container = document.getElementById(targetId).closest('.custom-select-container');
            const optionsContainer = container.querySelector('.options-container');
            const searchInput = container.querySelector('.search-input');
            const hiddenInput = container.querySelector('input[type="hidden"]');

            // Special case: If it's just a space, we'll treat it as a request to show all options
            const isShowAll = searchTerm === " ";

            // Don't show loading or open dropdown if no search term (except for our special case)
            if (!searchTerm.trim() && !isShowAll) {
                optionsContainer.classList.add('hidden');
                return;
            }

            optionsContainer.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat data merk...</div>';
            optionsContainer.classList.remove('hidden');

            // Build query parameters
            let queryParams = new URLSearchParams();
            queryParams.append('json', 'true');
            queryParams.append('limit', '50');

            // Only add search parameter if it's not our special "show all" case
            if (searchTerm.trim() && !isShowAll) {
                queryParams.append('search', searchTerm.trim());
            }

            fetch(`/brands?${queryParams.toString()}`, {
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
                let brands = [];

                if (Array.isArray(data)) {
                    brands = data;
                } else if (data.brands && Array.isArray(data.brands)) {
                    brands = data.brands;
                } else if (data.data && Array.isArray(data.data)) {
                    brands = data.data;
                }

                displayBrandResults(brands, optionsContainer, hiddenInput, searchInput);
            })
            .catch(error => {
                console.error('Error fetching brands:', error);
                optionsContainer.innerHTML = '<div class="p-2 text-sm text-red-500">Gagal memuat data merk</div>';
            });
        }

        function displayBrandResults(brands, resultsElem, idInputElem, searchInputElem) {
            resultsElem.innerHTML = '';

            if (brands.length === 0) {
                resultsElem.innerHTML = '<div class="p-2 text-sm text-gray-500">Tidak ada merk yang ditemukan</div>';
                return;
            }

            // Add search help text
            const searchHelp = document.createElement('div');
            searchHelp.className = 'p-2 text-xs text-gray-500 text-center border-b';
            searchHelp.textContent = 'Ketik untuk mencari merk...';
            resultsElem.appendChild(searchHelp);

            if (searchInputElem && searchInputElem.value.trim()) {
                const searchTerm = searchInputElem.value.trim().toLowerCase();
                brands.sort((a, b) => {
                    const aName = a.brand_name?.toLowerCase() || '';
                    const bName = b.brand_name?.toLowerCase() || '';

                    if (aName === searchTerm) return -1;
                    if (bName === searchTerm) return 1;

                    const aStarts = aName.startsWith(searchTerm);
                    const bStarts = bName.startsWith(searchTerm);
                    if (aStarts && !bStarts) return -1;
                    if (bStarts && !aStarts) return 1;

                    return aName.localeCompare(bName);
                });
            }

            brands.forEach((brand, index) => {
                const div = document.createElement('div');
                div.className = 'option p-3 hover:bg-gray-100 cursor-pointer text-[#666666]';
                div.textContent = brand.brand_name || 'Unknown Brand';
                div.setAttribute('data-value', brand.brand_id || '');

                div.addEventListener('click', function() {
                    idInputElem.value = this.getAttribute('data-value');
                    searchInputElem.value = this.textContent;
                    resultsElem.classList.add('hidden');

                    // Clear any error styling
                    searchInputElem.classList.remove('border-red-500');
                    const errorElement = searchInputElem.closest('.space-y-2').querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');

                    // Trigger change event
                    const event = new Event('change', { bubbles: true });
                    idInputElem.dispatchEvent(event);
                });

                resultsElem.appendChild(div);
            });

            if (brands.length > 10) {
                const countDiv = document.createElement('div');
                countDiv.className = 'p-2 text-xs text-gray-500 text-center border-t';
                countDiv.textContent = `Menampilkan ${brands.length} merk`;
                resultsElem.appendChild(countDiv);
            }
        }

        // Function to fetch categories with search parameter based on asset type
        function fetchCategories(assetType, searchTerm = '', targetId = '') {
            if (!assetType || !targetId) return;

            const container = document.getElementById(targetId).closest('.custom-select-container');
            const optionsContainer = container.querySelector('.options-container');
            const searchInput = container.querySelector('.search-input');
            const hiddenInput = container.querySelector('input[type="hidden"]');

            // Special case: If it's just a space, we'll treat it as a request to show all options
            const isShowAll = searchTerm === " ";

            // Don't show loading or open dropdown if no search term (except for our special case)
            if (!searchTerm.trim() && !isShowAll) {
                optionsContainer.classList.add('hidden');
                return;
            }

            optionsContainer.innerHTML = '<div class="p-2 text-sm text-gray-500">Memuat kategori...</div>';
            optionsContainer.classList.remove('hidden');

            // Build query parameters
            let queryParams = new URLSearchParams();
            queryParams.append('json', 'true');
            queryParams.append('asset_type', assetType);
            queryParams.append('limit', '50');

            // Only add search parameter if it's not our special "show all" case
            if (searchTerm.trim() && !isShowAll) {
                queryParams.append('search', searchTerm.trim());
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

                if (Array.isArray(data)) {
                    categories = data;
                } else if (data.categories && Array.isArray(data.categories)) {
                    categories = data.categories;
                } else if (data.data && Array.isArray(data.data)) {
                    categories = data.data;
                }

                displayCategoryResults(categories, optionsContainer, hiddenInput, searchInput, assetType);
            })
            .catch(error => {
                console.error('Error fetching categories:', error);
                optionsContainer.innerHTML = '<div class="p-2 text-sm text-red-500">Gagal memuat kategori</div>';
            });
        }

        function displayCategoryResults(categories, resultsElem, idInputElem, searchInputElem, assetType) {
            resultsElem.innerHTML = '';

            if (categories.length === 0) {
                resultsElem.innerHTML = '<div class="p-2 text-sm text-gray-500">Tidak ada kategori yang ditemukan</div>';
                return;
            }

            // Add search help text
            const searchHelp = document.createElement('div');
            searchHelp.className = 'p-2 text-xs text-gray-500 text-center border-b';
            searchHelp.textContent = 'Ketik untuk mencari kategori...';
            resultsElem.appendChild(searchHelp);

            if (searchInputElem && searchInputElem.value.trim()) {
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

            // Add asset type title
            const typeTitle = document.createElement('div');
            typeTitle.className = 'p-2 text-sm font-medium text-gray-600 border-b';
            typeTitle.textContent = `Kategori ${assetType === 'medical' ? 'Medis' : 'Non-Medis'}`;
            resultsElem.appendChild(typeTitle);

            categories.forEach((category) => {
                const div = document.createElement('div');
                div.className = 'option p-3 hover:bg-gray-100 cursor-pointer text-[#666666]';
                div.textContent = category.subcategory_name || 'Unknown Category';
                div.setAttribute('data-value', category.subcategory_id || '');
                div.setAttribute('data-type', category.asset_type || assetType);

                div.addEventListener('click', function() {
                    idInputElem.value = this.getAttribute('data-value');
                    searchInputElem.value = this.textContent;

                    // Clear any error styling
                    searchInputElem.classList.remove('border-red-500');
                    const errorElement = searchInputElem.closest('.space-y-2').querySelector('.error-message');
                    if (errorElement) errorElement.classList.add('hidden');

                    // Explicitly hide the dropdown
                    resultsElem.classList.add('hidden');

                    // Trigger change event
                    const event = new Event('change', { bubbles: true });
                    idInputElem.dispatchEvent(event);
                });

                resultsElem.appendChild(div);
            });

            if (categories.length > 10) {
                const countDiv = document.createElement('div');
                countDiv.className = 'p-2 text-xs text-gray-500 text-center border-t';
                countDiv.textContent = `Menampilkan ${categories.length} kategori`;
                resultsElem.appendChild(countDiv);
            }
        }
    });
</script>
@endpush
