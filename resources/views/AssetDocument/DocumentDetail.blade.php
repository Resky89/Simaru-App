@extends('Layout.app')

@section('title', 'Detail Dokumen')

@section('content')
    @include('Layout.loading')
    <!-- Hidden CSRF token field -->
    <form id="csrf-form" style="display: none;">
        @csrf
    </form>
    <div class="p-4 md:p-6">
        <!-- Header with title and back button -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <a href="{{ route('asset-documents') }}"
                    class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">
                    DOKUMEN
                </h1>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                @if(hasPermission('document:edit'))
                    <button id="editDocumentBtn"
                        class="flex items-center justify-center gap-2 px-3 py-2 border border-[#213268] text-[#213268] rounded-lg hover:bg-[#213268] hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span class="text-sm md:text-base">Ubah</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Document Information Card -->
        <div class="mb-6">
            <div class="bg-[#213268] rounded-t-lg p-4">
                <h2 class="text-white font-semibold">Informasi Dokumen</h2>
            </div>
            <div class="bg-white p-6 rounded-b-lg border border-t-0 border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Document Details - First Column -->
                    <div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Judul Dokumen</p>
                            <p class="font-medium">{{ $document['document_title'] ?? 'N/A' }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Tanggal Upload</p>
                            <p class="font-medium">
                                @if(isset($document['upload_date']))
                                    {{ \Carbon\Carbon::parse($document['upload_date'])->locale('id')->isoFormat('D MMMM YYYY') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Document Details - Second Column -->
                    <div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Dibuat Oleh</p>
                            <p class="font-medium">
                                @if(isset($document['uploader']) && isset($document['uploader']['employee_name']))
                                    {{ $document['uploader']['employee_name'] }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="mt-4">
                    <p class="text-sm text-gray-500">Catatan</p>
                    <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                        <p>{{ $document['notes'] ?? 'Tidak ada catatan' }}</p>
                    </div>
                </div>

                <!-- Document File Preview Section -->
                @if(isset($document['file_path']) && !empty($document['file_path']))
                    <div class="mt-6">
                        <p class="text-sm text-gray-500 mb-2">File Dokumen</p>
                        @php
                            $filePath = $document['file_path'];
                            $fileName = pathinfo($filePath, PATHINFO_BASENAME);
                            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                            $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp

                        @if($isImage)
                            <!-- Image Preview -->
                            <div class="mt-2 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex justify-center">
                                    <img src="{{ config('app.backend_url') }}/public{{ $filePath }}"
                                        alt="{{ $document['document_title'] }}" class="max-w-full max-h-80 object-contain rounded">
                                </div>
                                <p class="text-sm text-center mt-2 text-gray-600">{{ $fileName }}</p>
                                <div class="flex justify-center mt-3">
                                    @if(hasPermission('document:download'))
                                        <a href="{{ config('app.backend_url') }}/public{{ $filePath }}"
                                            class="bg-[#213268] text-white px-3 py-2 rounded-md hover:bg-[#1d2754] transition-colors"
                                            target="_blank" download>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                Unduh Gambar
                                            </span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <!-- Document File with Icon -->
                            <div class="mt-2 bg-gray-50 p-4 rounded-lg border border-gray-200 flex items-center">
                                @if(in_array($fileExtension, ['pdf']))
                                    <svg class="w-10 h-10 text-red-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium">Dokumen PDF</p>
                                        <p class="text-sm text-gray-600">{{ $fileName }}</p>
                                    </div>
                                    @if(hasPermission('document:download'))
                                        <a href="{{ config('app.backend_url') }}/public{{ $filePath }}"
                                            class="ml-auto bg-[#213268] text-white px-3 py-2 rounded-md hover:bg-[#1d2754] transition-colors"
                                            target="_blank" download>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                Unduh
                                            </span>
                                        </a>
                                    @endif
                                @elseif(in_array($fileExtension, ['doc', 'docx']))
                                    <svg class="w-10 h-10 text-blue-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium">Dokumen Word</p>
                                        <p class="text-sm text-gray-600">{{ $fileName }}</p>
                                    </div>
                                    @if(hasPermission('document:download'))
                                        <a href="{{ config('app.backend_url') }}/public{{ $filePath }}"
                                            class="ml-auto bg-[#213268] text-white px-3 py-2 rounded-md hover:bg-[#1d2754] transition-colors"
                                            target="_blank" download>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Unduh
                                            </span>
                                        </a>
                                    @endif
                                @elseif(in_array($fileExtension, ['xls', 'xlsx', 'csv']))
                                    <svg class="w-10 h-10 text-green-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium">Spreadsheet Excel</p>
                                        <p class="text-sm text-gray-600">{{ $fileName }}</p>
                                    </div>
                                    @if(hasPermission('document:download'))
                                        <a href="{{ config('app.backend_url') }}/public{{ $filePath }}"
                                            class="ml-auto bg-[#213268] text-white px-3 py-2 rounded-md hover:bg-[#1d2754] transition-colors"
                                            target="_blank" download>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Unduh
                                            </span>
                                        </a>
                                    @endif
                                @else
                                    <svg class="w-10 h-10 text-gray-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium">File Dokumen</p>
                                        <p class="text-sm text-gray-600">{{ $fileName }}</p>
                                    </div>
                                    @if(hasPermission('document:download'))
                                        <a href="{{ config('app.backend_url') }}/public{{ $filePath }}"
                                            class="ml-auto bg-[#213268] text-white px-3 py-2 rounded-md hover:bg-[#1d2754] transition-colors"
                                            target="_blank" download>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                Unduh
                                            </span>
                                        </a>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Associated Assets Section -->
        <div class="mb-6">
            <div class="bg-[#213268] rounded-t-lg p-4 flex justify-between items-center">
                <h2 class="text-white font-semibold">Asset Terkait</h2>
                @if(hasPermission('document:assign'))
                    <button id="link-document-btn"
                        class="flex items-center justify-center gap-2 px-3 py-1.5 bg-white text-[#213268] rounded-lg hover:bg-gray-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.172 13.828a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.102 1.101" />
                        </svg>
                        <span class="text-sm">Hubungkan Asset</span>
                    </button>
                @endif
            </div>
            <div class="bg-white p-6 rounded-b-lg border border-t-0 border-gray-200">
                @if(isset($document['assets']) && count($document['assets']) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 text-left text-sm font-medium">Kode Asset</th>
                                    <th class="bg-[#213268] text-white p-3 text-left text-sm font-medium">Nama Asset</th>
                                    <th class="bg-[#213268] text-white p-3 text-center text-sm font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($document['assets'] as $asset)
                                    <tr>
                                        <td class="p-3 text-sm border-t border-gray-200">
                                            {{ $asset['asset_code'] }}
                                        </td>
                                        <td class="p-3 text-sm border-t border-gray-200">
                                            {{ $asset['asset_name'] }}
                                        </td>
                                        <td class="p-3 text-sm border-t border-gray-200 text-center">
                                            @if(hasPermission('document:unlink'))
                                                <button type="button" data-asset-id="{{ $asset['asset_id'] }}"
                                                        data-asset-code="{{ $asset['asset_code'] }}"
                                                        data-asset-name="{{ $asset['asset_name'] }}"
                                                        data-document-id="{{ $document['document_id'] }}"
                                                        class="unlink-asset-btn bg-red-100 text-red-700 px-3 py-1 rounded-md hover:bg-red-200 transition-colors">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Putuskan
                                                    </span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        <p>Tidak ada asset terkait dengan dokumen ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Edit Document Modal (Placeholder) -->
    @if(hasPermission('document:edit'))
        <div id="editDocumentModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="editDocumentModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#28356B]">UBAH DOKUMEN</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="editDocumentForm" method="POST" data-no-loading enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="edit_document_id" name="document_id"
                                    value="{{ $document['document_id'] ?? '' }}">
                                <div class="space-y-4">
                                    <!-- Document Title -->
                                    <div>
                                        <label for="edit_document_title"
                                            class="block text-sm font-medium text-gray-700 mb-1">Judul Dokumen <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" id="edit_document_title" name="document_title"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#28356B] focus:ring focus:ring-[#28356B] focus:ring-opacity-20" placeholder="Masukkan judul dokumen"
                                            value="{{ $document['document_title'] ?? '' }}">
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Judul dokumen harus diisi</div>
                                    </div>

                                    <!-- File Upload -->
                                    <div>
                                        <label for="edit_file" class="block text-sm font-medium text-gray-700 mb-1">Ganti File <span class="text-red-500">*</span></label>
                                        <div
                                            class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                            <!-- Current File Info (if any) -->
                                            <div id="edit_current_file" class="mb-4 w-full">
                                                <!-- Current file is an image -->
                                                <div id="edit_current_image"
                                                    class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto hidden">
                                                    <div class="relative">
                                                        <img id="edit_current_img" src=""
                                                            class="w-full h-auto max-h-64 object-contain mx-auto rounded"
                                                            alt="Current Document Image">
                                                        <button type="button" id="edit_remove_current_file"
                                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Current file is not an image -->
                                                <div id="edit_current_file_icon"
                                                    class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto hidden">
                                                    <div class="flex items-center">
                                                        <svg class="w-6 h-6 text-gray-600 mr-2"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <span id="edit_file_name" class="text-sm text-gray-700 truncate"></span>
                                                        <button type="button" id="edit_remove_current_file_icon"
                                                            class="ml-auto text-red-500 hover:text-red-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- New File preview -->
                                            <!-- Image preview for new file -->
                                            <div id="edit_image_preview" class="mt-2 mb-4 w-full hidden">
                                                <div
                                                    class="relative bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                    <img id="edit_preview_img" src=""
                                                        class="w-full h-auto max-h-64 object-contain mx-auto rounded"
                                                        alt="Selected Image">
                                                    <button type="button" id="edit_remove_image"
                                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- File preview (non-image) for new file -->
                                            <div id="edit_file_preview" class="mt-2 mb-4 w-full hidden">
                                                <div
                                                    class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                                    <div class="flex items-center">
                                                        <svg class="w-6 h-6 text-gray-600 mr-2"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <span id="edit_file_preview_text"
                                                            class="text-sm text-gray-700 truncate"></span>
                                                        <button type="button" id="edit_remove_file"
                                                            class="ml-auto text-red-500 hover:text-red-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <svg class="mx-auto h-12 w-12 text-[#213268]" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                <p class="mt-1 text-sm text-gray-600">Seret file Anda atau <span
                                                        class="text-[#213268] font-semibold">pilih file</span></p>
                                                <p class="mt-1 text-xs text-gray-500">Format yang diterima: PDF, DOC, DOCX, XLS,
                                                    XLSX, JPG, JPEG, PNG (Ukuran maks: 10MB)</p>
                                                <p class="mt-1 text-xs text-[#213268] font-medium">Klik di mana saja di area ini
                                                    untuk memilih file</p>
                                            </div>
                                            <input type="file" id="edit_file" name="file"
                                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        </div>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">File harus dipilih</div>
                                    </div>

                                    <!-- Notes -->
                                    <div>
                                        <label for="edit_notes"
                                            class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                        <textarea id="edit_notes" name="notes" rows="3"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#28356B] focus:ring focus:ring-[#28356B] focus:ring-opacity-20" placeholder="Masukkan catatan">{{ $document['notes'] ?? '' }}</textarea>
                                    </div>

                                    <!-- Associated Assets (hidden for future use) -->
                                    <div class="hidden">
                                        <label for="edit_asset_ids" class="block text-sm font-medium text-gray-700 mb-1">Aset
                                            Terkait</label>
                                        <select id="edit_asset_ids" name="asset_ids[]" multiple
                                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#28356B] focus:ring focus:ring-[#28356B] focus:ring-opacity-20">
                                            <!-- Options would be populated dynamically -->
                                        </select>
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="flex justify-end mt-6">
                                        <button type="submit"
                                            class="w-full h-[45px] bg-[#28356B] text-white rounded-lg text-base hover:bg-[#1d2754]">
                                            <span class="flex items-center justify-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                Perbarui Dokumen
                                            </span>
                                        </button>
                                    </div>

                                    <!-- Upload Progress Indicator (initially hidden) -->
                                    <div id="editUploadProgressContainer" class="hidden mt-4">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-medium text-[#213268]">Mengupload dokumen...</span>
                                            <span id="editUploadProgressText"
                                                class="text-sm font-medium text-[#213268]">0%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div id="editUploadProgressBar"
                                                class="bg-green-500 h-2.5 rounded-full transition-all duration-300"
                                                style="width: 0%"></div>
                                        </div>
                                        <div id="editUploadStatusMessage" class="mt-2 text-sm text-gray-600">Upload berhasil!
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div id="successNotification"
            class="fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-[70]"
            role="alert">
            <div class="flex items-center">
                <div class="py-1">
                    <svg class="h-6 w-6 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Berhasil!</p>
                    <p>{{ session('success') }}</p>
                </div>
                <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
            </div>
        </div>
    @endif

    @if(session('error') || isset($error))
        <div id="errorNotification"
            class="fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md z-[70]"
            role="alert">
            <div class="flex items-center">
                <div class="py-1">
                    <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Gagal!</p>
                    <p>{!! session('error') ?? $error ?? 'Terjadi kesalahan' !!}</p>
                </div>
                <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
            </div>
        </div>
    @endif

    <!-- Link Assets Modal -->
    @if(hasPermission('document:assign'))
        <div id="linkAssetsModal" class="fixed inset-0 z-[60] hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[1200px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="linkAssetsModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">Pilih Asset</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <!-- Search and Filter -->
                            <div class="flex flex-col md:flex-row gap-4 mb-4">
                                <div class="relative flex-grow">
                                    <input type="text" id="asset-search"
                                        placeholder="Cari berdasarkan nama, kode, atau nomor seri..."
                                        class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Assets Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center w-[40px]">
                                                <input type="checkbox" id="select-all-link-assets" class="checkbox checkbox-sm">
                                            </th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Kode Asset</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Asset</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Deskripsi</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Jenis Asset</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Kategori
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="assets-table-body">
                                        <!-- Assets will be loaded here via AJAX -->
                                        <tr>
                                            <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                Memuat asset...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                                <div class="flex items-center space-x-2">
                                    <button id="prev-page" class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                            </svg>
                                            Sebelumnya
                                    </button>

                                    <div id="pagination-numbers" class="flex gap-2">
                                        <!-- Page numbers will be generated here -->
                                    </div>

                                    <button id="next-page" class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Selanjutnya
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                    </button>
                                </div>

                                <div class="flex items-center gap-2 mt-4 md:mt-0">
                                    <span class="text-sm text-gray-600" id="pagination-info">
                                        Menampilkan 1 sampai 10 dari 0 Data
                                    </span>
                                    <select id="per-page" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm">
                                        <option value="10">10 per halaman</option>
                                        <option value="25">25 per halaman</option>
                                        <option value="50">50 per halaman</option>
                                        <option value="100">100 per halaman</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Button Group -->
                            <div class="pt-4 flex justify-end gap-4">
                                <button type="button" id="link-selected-assets"
                                    class="w-full px-6 py-2.5 bg-[#213268] text-white rounded-lg hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                    Tautkan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Unlink Asset Confirmation Modal -->
    @if(hasPermission('document:unlink'))
    <div id="unlinkAssetModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="unlinkAssetModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">PUTUSKAN HUBUNGAN ASSET</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="unlink-form" method="POST" data-no-loading>
                        @csrf
                        @method('DELETE')
                        <div class="p-6">
                            <div class="space-y-6 max-w-[400px] mx-auto">
                                <div class="flex flex-col items-center">
                                    <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin memutuskan hubungan dokumen dengan asset ini?</p>
                                    <p id="unlink-asset-info" class="text-base font-semibold text-center mt-2"></p>
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                        Batal
                                    </button>
                                    <button type="submit" class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                        Putuskan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(!hasPermission('document:edit'))
                const editButtons = document.querySelectorAll('#editDocumentBtn');
                editButtons.forEach(btn => {
                    if (btn) {
                        btn.style.display = 'none';
                    }
                });
            @endif

                @if(!hasPermission('document:assign'))
                    const assignButtons = document.querySelectorAll('#linkAssetsModal');
                    assignButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                @if(!hasPermission('document:download'))
                    const downloadButtons = document.querySelectorAll('#downloadDocumentBtn');
                    downloadButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                @if(!hasPermission('document:unlink'))
                    const unlinkButtons = document.querySelectorAll('form[action*="asset-documents/asset"]');
                    unlinkButtons.forEach(form => {
                        if (form) {
                            form.style.display = 'none';
                        }
                    });
                @endif


            function showToast(message, type = 'success') {
                let toastContainer = document.getElementById('toast-container');
                if (!toastContainer) {
                    toastContainer = document.createElement('div');
                    toastContainer.id = 'toast-container';
                    toastContainer.className = 'fixed top-4 right-4 z-[70] flex flex-col gap-2';
                    document.body.appendChild(toastContainer);
                }

                const toast = document.createElement('div');

                const hasHTML = /<[a-z][\s\S]*>/i.test(message);

                if (type === 'success') {
                    toast.className = 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md flex items-center animate-slide-in-right';

                    toast.innerHTML = `
                            <div class="py-1">
                                <svg class="h-6 w-6 mr-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold">Berhasil!</p>
                                <div>${message}</div>
                            </div>
                            <button class="ml-auto text-gray-400 hover:text-gray-500" onclick="this.parentElement.remove()">×</button>
                        `;
                } else {
                    toast.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md flex items-center overflow-auto max-w-md animate-slide-in-right';

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
                    closeBtn.onclick = function() {
                        toast.remove();
                    };

                    wrapper.appendChild(iconContainer);
                    wrapper.appendChild(contentContainer);
                    wrapper.appendChild(closeBtn);
                    toast.appendChild(wrapper);
                }

                toastContainer.appendChild(toast);

                setTimeout(() => {
                    toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => {
                        toast.remove();
                    }, 500);
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
                    </style>
                `);

            function clearModal(modalId) {
                const modal = document.getElementById(modalId);
                if (!modal) return;

                const form = modal.querySelector('form');
                if (form) form.reset();

                const imagePreview = modal.querySelector('#edit_image_preview');
                if (imagePreview) imagePreview.classList.add('hidden');

                const filePreview = modal.querySelector('#edit_file_preview');
                if (filePreview) filePreview.classList.add('hidden');

                const progressContainer = modal.querySelector('#editUploadProgressContainer');
                if (progressContainer) progressContainer.classList.add('hidden');

                const errorMessages = modal.querySelectorAll('.error-message');
                errorMessages.forEach(msg => {
                    if (msg) msg.classList.add('hidden');
                });

                const inputs = modal.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    input.classList.remove('border-red-500');
                });
            }

            const openModal = function (modal, content) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                    content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                }, 10);
            };

            const closeModal = function (modal, content) {
                content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                setTimeout(() => {
                    modal.classList.add('hidden');

                    if (modal.id === 'editDocumentModal') {
                        clearModal('editDocumentModal');
                    }

                    if (modal.id === 'linkAssetsModal') {
                        selectedAssets = [];

                        document.querySelectorAll('.asset-checkbox').forEach(checkbox => {
                            checkbox.checked = false;
                        });

                        const selectAllCheckbox = document.getElementById('select-all-link-assets');
                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = false;
                            selectAllCheckbox.indeterminate = false;
                        }


                    }
                }, 300);
            };

            const isImageFile = function (file) {
                return file && file.type.match(/^image\/(jpeg|jpg|png)$/i);
            };

            const hasImageExtension = function (filename) {
                if (!filename) return false;
                return /\.(jpg|jpeg|png|gif|webp)$/i.test(filename);
            };

            const getFileIcon = function (filename) {
                if (!filename) return getDocumentIcon();

                const extension = filename.split('.').pop().toLowerCase();

                if (['pdf'].includes(extension)) {
                    return getPdfIcon();
                } else if (['doc', 'docx'].includes(extension)) {
                    return getWordIcon();
                } else if (['xls', 'xlsx', 'csv'].includes(extension)) {
                    return getExcelIcon();
                } else {
                    return getDocumentIcon();
                }
            };

            const getDocumentIcon = function () {
                return `<svg class="w-6 h-6 text-gray-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>`;
            };

            const getPdfIcon = function () {
                return `<svg class="w-6 h-6 text-red-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        <text x="12" y="16" font-family="Arial" font-size="6" fill="currentColor" text-anchor="middle">PDF</text>
                    </svg>`;
            };

            const getWordIcon = function () {
                return `<svg class="w-6 h-6 text-blue-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        <text x="12" y="16" font-family="Arial" font-size="5" fill="currentColor" text-anchor="middle">DOC</text>
                    </svg>`;
            };

            const getExcelIcon = function () {
                return `<svg class="w-6 h-6 text-green-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        <text x="12" y="16" font-family="Arial" font-size="5" fill="currentColor" text-anchor="middle">XLS</text>
                    </svg>`;
            };

            const updateFileIcon = function (containerSelector, fileName) {
                const container = document.querySelector(containerSelector);
                if (container) {
                    const iconContainer = container.querySelector('.flex');
                    if (iconContainer) {
                        const existingIcon = iconContainer.querySelector('svg');
                        if (existingIcon) {
                            existingIcon.remove();
                        }
                        const temp = document.createElement('div');
                        temp.innerHTML = getFileIcon(fileName);
                        iconContainer.insertBefore(temp.firstChild, iconContainer.firstChild);
                    }
                }
            };

            document.getElementById('editDocumentBtn')?.addEventListener('click', function () {
                const modal = document.getElementById('editDocumentModal');
                const content = document.getElementById('editDocumentModalContent');
                if (modal && content) {
                    initEditForm();
                    openModal(modal, content);
                }
            });

            function initEditForm() {
                const documentId = '{{ $document["document_id"] ?? "" }}';
                const editForm = document.getElementById('editDocumentForm');
                editForm.action = `{{ url('asset-documents') }}/${documentId}`;
                const filePath = '{{ $document["file_path"] ?? "" }}';
                const currentFileSection = document.getElementById('edit_current_file');
                const currentImageSection = document.getElementById('edit_current_image');
                const currentFileIconSection = document.getElementById('edit_current_file_icon');
                const fileNameDisplay = document.getElementById('edit_file_name');
                const currentImg = document.getElementById('edit_current_img');
                currentImageSection.classList.add('hidden');
                currentFileIconSection.classList.add('hidden');

                if (filePath && filePath.trim() !== '') {
                    const filename = filePath.split('/').pop();

                    if (hasImageExtension(filename)) {
                        currentImg.src = `{{ config('app.backend_url') }}/public{{ $document["file_path"] ?? "" }}`;
                        currentImageSection.classList.remove('hidden');
                    } else {
                        fileNameDisplay.textContent = filename || 'Document File';
                        updateFileIcon('#edit_current_file_icon', filename);
                        currentFileIconSection.classList.remove('hidden');
                    }

                    currentFileSection.classList.remove('hidden');
                } else {
                    currentFileSection.classList.add('hidden');
                }

                document.getElementById('edit_file').value = '';
                document.getElementById('edit_file_preview').classList.add('hidden');
                document.getElementById('edit_image_preview').classList.add('hidden');
                document.getElementById('editUploadProgressContainer').classList.add('hidden');
            }

            document.querySelectorAll('.close-modal').forEach(closeButton => {
                closeButton.addEventListener('click', function () {
                    const modal = this.closest('[id$="Modal"]');
                    const content = modal.querySelector('[id$="Content"]');
                    if (modal && content) {
                        closeModal(modal, content);
                    }
                });
            });

            document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                modal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        const content = this.querySelector('[id$="Content"]');
                        if (content) {
                            closeModal(this, content);
                        }
                    }
                });
            });

            const editFileInput = document.getElementById('edit_file');
            const editFilePreview = document.getElementById('edit_file_preview');
            const editImagePreview = document.getElementById('edit_image_preview');
            const editFilePreviewText = document.getElementById('edit_file_preview_text');
            const editPreviewImg = document.getElementById('edit_preview_img');
            const editCurrentFile = document.getElementById('edit_current_file');

            if (editFileInput) {
                editFileInput.addEventListener('change', function () {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const fileName = file.name;

                        if (editCurrentFile) {
                            editCurrentFile.classList.add('hidden');
                        }

                        if (isImageFile(file)) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                editPreviewImg.src = e.target.result;
                                editImagePreview.classList.remove('hidden');
                                editFilePreview.classList.add('hidden');
                            };
                            reader.readAsDataURL(file);
                        } else {
                            editFilePreviewText.textContent = fileName;
                            updateFileIcon('#edit_file_preview', fileName);
                            editFilePreview.classList.remove('hidden');
                            editImagePreview.classList.add('hidden');
                        }
                    } else {
                        editFilePreview.classList.add('hidden');
                        editImagePreview.classList.add('hidden');
                        if (editCurrentFile) {
                            editCurrentFile.classList.remove('hidden');
                        }
                    }
                });
            }

            document.getElementById('edit_remove_file')?.addEventListener('click', function () {
                if (editFileInput) {
                    editFileInput.value = '';
                }
                editFilePreview.classList.add('hidden');

                if (editCurrentFile) {
                    editCurrentFile.classList.remove('hidden');
                }
            });

            document.getElementById('edit_remove_image')?.addEventListener('click', function () {
                if (editFileInput) {
                    editFileInput.value = '';
                }
                editImagePreview.classList.add('hidden');

                if (editCurrentFile) {
                    editCurrentFile.classList.remove('hidden');
                }
            });

            const setupRemoveCurrentFile = (buttonId) => {
                document.getElementById(buttonId)?.addEventListener('click', function () {
                    document.getElementById('edit_current_file').classList.add('hidden');
                    document.getElementById('edit_current_image').classList.add('hidden');
                    document.getElementById('edit_current_file_icon').classList.add('hidden');
                    const removeFileInput = document.createElement('input');
                    removeFileInput.type = 'hidden';
                    removeFileInput.name = 'remove_file';
                    removeFileInput.value = '1';
                    document.getElementById('editDocumentForm').appendChild(removeFileInput);
                    const fileInput = document.getElementById('edit_file');
                    const fileErrorElement = fileInput.closest('div').nextElementSibling;
                    if (fileErrorElement && (!fileInput.files || fileInput.files.length === 0)) {
                        fileErrorElement.classList.remove('hidden');
                    }
                });
            };

            setupRemoveCurrentFile('edit_remove_current_file');
            setupRemoveCurrentFile('edit_remove_current_file_icon');

            const editDocumentForm = document.getElementById('editDocumentForm');
            if (editDocumentForm) {
                editDocumentForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const titleInput = this.querySelector('#edit_document_title');
                    const fileInput = this.querySelector('#edit_file');
                    const titleErrorElement = titleInput.closest('div').querySelector('.error-message');
                    const fileErrorElement = fileInput.closest('div').nextElementSibling;
                    const hasCurrentFile = !document.getElementById('edit_current_file').classList.contains('hidden');
                    const hasRemoveFileInput = this.querySelector('input[name="remove_file"]');

                    let isValid = true;

                    titleInput.classList.remove('border-red-500');
                    if (titleErrorElement) titleErrorElement.classList.add('hidden');

                    if (fileErrorElement) fileErrorElement.classList.add('hidden');

                    if (!titleInput.value.trim()) {
                        titleInput.classList.add('border-red-500');
                        if (titleErrorElement) titleErrorElement.classList.remove('hidden');
                        titleInput.focus();
                        isValid = false;
                    }

                    if ((!hasCurrentFile || hasRemoveFileInput) && (!fileInput.files || fileInput.files.length === 0)) {
                        if (fileErrorElement) fileErrorElement.classList.remove('hidden');
                        if (isValid) fileInput.focus();
                        isValid = false;
                    } else {
                        if (fileErrorElement) fileErrorElement.classList.add('hidden');
                    }

                    if (!isValid) {
                        return;
                    }

                    const submitBtn = this.querySelector('button[type="submit"]');
                    const progressContainer = document.getElementById('editUploadProgressContainer');
                    const progressBar = document.getElementById('editUploadProgressBar');
                    const progressText = document.getElementById('editUploadProgressText');
                    const statusMessage = document.getElementById('editUploadStatusMessage');

                    progressBar.style.width = '0%';
                    progressText.textContent = '0%';
                    statusMessage.textContent = 'Memulai pembaruan...';
                    progressBar.classList.remove('bg-red-500');
                    progressBar.classList.add('bg-green-500');
                    statusMessage.classList.remove('text-red-600');
                    progressContainer.classList.remove('hidden');
                    submitBtn.disabled = true;
                    const formData = new FormData(this);
                    const xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            const percentComplete = Math.round((e.loaded / e.total) * 100);
                            progressBar.style.width = percentComplete + '%';
                            progressText.textContent = percentComplete + '%';

                            if (percentComplete < 100) {
                                statusMessage.textContent = 'Mengupload pembaruan...';
                            } else {
                                statusMessage.textContent = 'Memproses pembaruan...';
                            }
                        }
                    });

                    xhr.addEventListener('load', function () {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                progressBar.style.width = '100%';
                                progressText.textContent = '100%';
                                statusMessage.textContent = 'Pembaruan berhasil!';

                                showToast('Dokumen berhasil diperbarui!', 'success');

                                setTimeout(function () {
                                    editDocumentForm.reset();

                                    const modal = document.getElementById('editDocumentModal');
                                    const content = document.getElementById('editDocumentModalContent');
                                    closeModal(modal, content);

                                    location.reload();
                                }, 1000);
                            } catch (error) {
                                console.error('Error parsing response:', error);
                                showToast('Terjadi kesalahan saat memproses respons server', 'error');
                                progressBar.classList.remove('bg-green-500');
                                progressBar.classList.add('bg-red-500');
                                statusMessage.textContent = 'Error: Format respons tidak valid';
                                submitBtn.disabled = false;
                            }
                        } else {
                            let errorMessage = 'Gagal memperbarui dokumen';
                            try {
                                const response = JSON.parse(xhr.responseText);

                                // Ambil pesan error langsung dari server
                                if (response.data && response.data.errors) {
                                    errorMessage = '';

                                    if (Array.isArray(response.data.errors)) {
                                        response.data.errors.forEach(error => {
                                            if (typeof error === 'string') {
                                                errorMessage += `${error}. `;
                                            } else if (typeof error === 'object') {
                                                if (error.reason) errorMessage += `${error.reason}. `;
                                                else if (error.message) errorMessage += `${error.message}. `;
                                            }
                                        });
                                    } else if (typeof response.data.errors === 'string') {
                                        errorMessage = response.data.errors;
                                    } else if (typeof response.data.errors === 'object') {
                                        Object.entries(response.data.errors).forEach(([field, fieldErrors]) => {
                                            if (Array.isArray(fieldErrors)) {
                                                fieldErrors.forEach(error => errorMessage += `${error}. `);
                                            } else if (typeof fieldErrors === 'string') {
                                                errorMessage += `${fieldErrors}. `;
                                            }
                                        });
                                    }
                                } else if (response.errors) {
                                    errorMessage = '';

                                    if (Array.isArray(response.errors)) {
                                        response.errors.forEach(error => {
                                            if (typeof error === 'string') {
                                                errorMessage += `${error}. `;
                                            } else if (typeof error === 'object') {
                                                if (error.reason) errorMessage += `${error.reason}. `;
                                                else if (error.message) errorMessage += `${error.message}. `;
                                            }
                                        });
                                    } else if (typeof response.errors === 'string') {
                                        errorMessage = response.errors;
                                    } else if (typeof response.errors === 'object' && !Array.isArray(response.errors)) {
                                        Object.entries(response.errors).forEach(([field, fieldErrors]) => {
                                            if (Array.isArray(fieldErrors)) {
                                                fieldErrors.forEach(error => errorMessage += `${error}. `);
                                            } else if (typeof fieldErrors === 'string') {
                                                errorMessage += `${fieldErrors}. `;
                                            }
                                        });
                                    }
                                } else if (response.message) {
                                    errorMessage = response.message;
                                } else {
                                    errorMessage = 'Gagal memperbarui dokumen';
                                }
                            } catch (e) {
                                console.error('Error parsing error response:', e);
                            }

                            progressBar.classList.remove('bg-green-500');
                            progressBar.classList.add('bg-red-500');
                            statusMessage.textContent = 'Error: ' + errorMessage.replace(/<[^>]*>/g, '');

                            showToast(errorMessage, 'error');

                            submitBtn.disabled = false;
                        }
                    });

                    xhr.addEventListener('error', function () {
                        progressBar.classList.remove('bg-green-500');
                        progressBar.classList.add('bg-red-500');
                        progressBar.style.width = '100%';
                        statusMessage.textContent = 'Error jaringan! Silakan coba lagi.';

                        showToast('Error jaringan! Silakan coba lagi.', 'error');

                        submitBtn.disabled = false;
                    });

                    xhr.open('POST', editDocumentForm.action);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')?.content || '');
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.send(formData);
                });
            }

            setTimeout(function () {
                const notifications = document.querySelectorAll('#successNotification, #errorNotification');
                notifications.forEach(notification => {
                    if (notification) {
                        notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                        setTimeout(() => notification.remove(), 500);
                    }
                });
            }, 5000);

            let currentPage = 1;
            let totalPages = 1;
            let perPage = 10;
            let searchTerm = '';
            let totalAssets = 0;
            let selectedAssets = [];
            let loadedAssets = [];

            const documentId = {{ $document['document_id'] ?? 0 }};

            const linkedAssetIds = [];
                @if(isset($document['assets']) && count($document['assets']) > 0)
                    @foreach($document['assets'] as $asset)
                        linkedAssetIds.push({{ $asset['asset_id'] }});
                    @endforeach
                @endif

            document.getElementById('link-document-btn')?.addEventListener('click', function () {
                const modal = document.getElementById('linkAssetsModal');
                const content = document.getElementById('linkAssetsModalContent');
                if (modal && content) {
                    currentPage = 1;
                    searchTerm = '';
                    document.getElementById('asset-search').value = '';
                    loadAssets();
                    openModal(modal, content);
                }
            });

            document.getElementById('link-assets-btn')?.addEventListener('click', function () {
                const modal = document.getElementById('linkAssetsModal');
                const content = document.getElementById('linkAssetsModalContent');
                if (modal && content) {
                    currentPage = 1;
                    searchTerm = '';
                    document.getElementById('asset-search').value = '';
                    loadAssets();
                    openModal(modal, content);
                }
            });

            const searchInput = document.getElementById('asset-search');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        searchTerm = this.value.trim();
                        currentPage = 1;
                        loadAssets();
                    }, 300);
                });
            }

            document.getElementById('per-page')?.addEventListener('change', function () {
                perPage = parseInt(this.value);
                currentPage = 1;
                loadAssets();
            });

            document.getElementById('prev-page')?.addEventListener('click', function () {
                if (currentPage > 1) {
                    currentPage--;
                    loadAssets();
                }
            });

            document.getElementById('next-page')?.addEventListener('click', function () {
                if (currentPage < totalPages) {
                    currentPage++;
                    loadAssets();
                }
            });

            document.getElementById('select-all-link-assets')?.addEventListener('change', function () {
                const isChecked = this.checked;

                document.querySelectorAll('.asset-checkbox').forEach(checkbox => {
                    checkbox.checked = isChecked;
                });

                if (isChecked) {
                    loadedAssets.forEach(asset => {
                        if (!selectedAssets.includes(asset.asset_id)) {
                            selectedAssets.push(asset.asset_id);
                        }
                    });
                } else {
                    selectedAssets = selectedAssets.filter(id => !loadedAssets.some(asset => asset.asset_id === id));
                }

            });

            document.getElementById('link-selected-assets')?.addEventListener('click', function () {
                if (selectedAssets.length === 0) {
                    showToast('Silakan pilih setidaknya satu asset', 'error');
                    return;
                }

                linkAssets();
            });

            document.addEventListener('DOMContentLoaded', function() {
                const perPageSelect = document.getElementById('per-page');
                if (perPageSelect) {

                    perPageSelect.value = perPage.toString();

                    perPageSelect.addEventListener('change', function() {
                        perPage = parseInt(this.value);
                        currentPage = 1;
                        loadAssets();
                    });
                }

                loadAssets();
            });

            function loadAssets() {
                const tableBody = document.getElementById('assets-table-body');
                const paginationInfo = document.getElementById('pagination-info');
                const prevPageBtn = document.getElementById('prev-page');
                const nextPageBtn = document.getElementById('next-page');
                const paginationNumbers = document.getElementById('pagination-numbers');

                const perPageSelect = document.getElementById('per-page');
                if (perPageSelect) {
                    perPage = parseInt(perPageSelect.value);
                }

                if (tableBody) {
                    tableBody.innerHTML = `
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-500">
                                    <div class="flex justify-center items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-[#213268]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Memuat asset...
                                    </div>
                                </td>
                            </tr>
                        `;
                }

                const params = new URLSearchParams({
                    page: currentPage,
                    limit: perPage,
                    search: searchTerm,
                    exclude_document_id: documentId
                });

                if (linkedAssetIds.length > 0) {
                    params.append('exclude_asset_ids', linkedAssetIds.join(','));
                }

                fetch(`/assets?${params.toString()}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (tableBody) {
                            tableBody.innerHTML = '';
                        }

                    let assets = [];
                    let pagination = {};

                    if (data.assets_pagination) {
                        pagination = data.assets_pagination;
                        assets = data.assets || [];
                    } else if (data.assets && data.assets.data) {
                        assets = data.assets.data;
                        pagination = {
                            current_page: data.assets.current_page,
                            last_page: data.assets.last_page,
                            per_page: data.assets.per_page,
                            total: data.assets.total
                        };
                    } else if (data.data && data.meta) {
                        assets = data.data;
                        pagination = {
                            current_page: data.meta.current_page,
                            last_page: data.meta.last_page,
                            per_page: data.meta.per_page,
                            total: data.meta.total
                        };
                    } else if (data.pagination) {
                        pagination = data.pagination;
                        assets = data.assets || data.data || [];
                    } else {
                        if (Array.isArray(data.assets)) {
                            assets = data.assets;
                        } else if (data.assets && Array.isArray(data.assets.data)) {
                            assets = data.assets.data;
                        } else if (Array.isArray(data.data)) {
                            assets = data.data;
                        } else {
                            assets = [];
                        }

                        pagination = data.pagination || data.assets_pagination || {};
                        if (!pagination.total && data.assets && data.assets.total) {
                            pagination.total = data.assets.total;
                            pagination.current_page = data.assets.current_page || currentPage;
                            pagination.last_page = data.assets.last_page || Math.ceil(pagination.total / perPage);
                            pagination.per_page = data.assets.per_page || perPage;
                        }

                        if (!pagination.total) {
                            pagination = {
                                current_page: currentPage,
                                last_page: Math.max(1, Math.ceil(assets.length / perPage)),
                                per_page: perPage,
                                total: assets.length
                            };
                        }
                    }

                    if (pagination.per_page) {
                        perPage = parseInt(pagination.per_page);
                        const perPageSelect = document.getElementById('per-page');
                        if (perPageSelect && perPageSelect.value != perPage) {
                            perPageSelect.value = perPage.toString();
                        }
                    }

                    currentPage = parseInt(pagination.current_page) || 1;
                    totalPages = parseInt(pagination.last_page) || 1;
                    totalAssets = parseInt(pagination.total) || assets.length;

                    if (paginationInfo) {
                        const start = Math.min((currentPage - 1) * perPage + 1, totalAssets);
                        const end = Math.min(currentPage * perPage, totalAssets);
                        paginationInfo.textContent = `Menampilkan ${start} sampai ${end} dari ${totalAssets} Data`;
                    }

                    loadedAssets = assets;

                        if (linkedAssetIds.length > 0) {
                            loadedAssets = loadedAssets.filter(asset => !linkedAssetIds.includes(asset.asset_id));
                        }

                        if (loadedAssets.length === 0) {
                            if (tableBody) {
                                tableBody.innerHTML = `
                                        <tr>
                                            <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                                Tidak ada asset yang ditemukan
                                            </td>
                                        </tr>
                                    `;
                            }
                        } else {
                        if (tableBody) {
                            tableBody.innerHTML = '';
                        }

                            loadedAssets.forEach(asset => {
                                const row = document.createElement('tr');
                                row.classList.add('hover:bg-gray-50');
                                const isChecked = selectedAssets.includes(asset.asset_id);

                            const assetName = asset.asset_name ||
                                    (asset.asset_master && asset.asset_master.asset_name) ||
                                asset.asset_master_name || '-';

                            const description = asset.description ||
                                (asset.asset_master && asset.asset_master.description) || '-';

                            let assetType = asset.asset_type_name ||
                                (asset.asset_type && asset.asset_type.asset_type_name) ||
                                'Non Medical';

                            if (!assetType && asset.asset_master && asset.asset_master.asset_master_code) {
                                    const code = asset.asset_master.asset_master_code;
                                assetType = code.startsWith('MED-') ? 'Medical' : 'Non Medical';
                                    }

                                const categoryName = asset.category_name ||
                                (asset.category && asset.category.category_name) ||
                                (asset.asset_master && asset.asset_master.subcategory_name) || '-';

                                row.innerHTML = `
                                        <td class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                            <input type="checkbox" class="asset-checkbox" value="${asset.asset_id}" ${isChecked ? 'checked' : ''}>
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            ${asset.asset_code || '-'}
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                            <div class="flex flex-col">
                                                <span class="font-medium">${assetName}</span>
                                            </div>
                                        </td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${description}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${assetType}</td>
                                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${categoryName}</td>
                                    `;

                                if (tableBody) {
                                    tableBody.appendChild(row);
                                }
                            });

                            document.querySelectorAll('.asset-checkbox').forEach(checkbox => {
                                checkbox.addEventListener('change', function () {
                                    const assetId = parseInt(this.value);

                                    if (this.checked) {
                                        if (!selectedAssets.includes(assetId)) {
                                            selectedAssets.push(assetId);
                                        }
                                    } else {
                                        selectedAssets = selectedAssets.filter(id => id !== assetId);
                                    }

                                    updateSelectAllCheckbox();
                                });
                            });

                            updateSelectAllCheckbox();
                        }

                    if (prevPageBtn) {
                        prevPageBtn.disabled = currentPage <= 1;
                    }
                    if (nextPageBtn) {
                        nextPageBtn.disabled = currentPage >= totalPages;
                    }

                    if (paginationNumbers) {
                        paginationNumbers.innerHTML = '';

                        let startPage = Math.max(1, currentPage - 2);
                        let endPage = Math.min(totalPages, startPage + 4);

                        if (endPage - startPage < 4) {
                            startPage = Math.max(1, endPage - 4);
                        }

                        if (startPage > 1) {
                            const btn = document.createElement('button');
                            btn.classList.add('h-8', 'w-8', 'flex', 'items-center', 'justify-center', 'border', 'border-[#D8DAE5]', 'text-[#213268]', 'rounded', 'hover:bg-gray-100');
                            btn.textContent = '1';
                            btn.addEventListener('click', () => {
                                currentPage = 1;
                                loadAssets();
                            });
                            paginationNumbers.appendChild(btn);

                            if (startPage > 2) {
                                const ellipsis = document.createElement('span');
                                ellipsis.classList.add('flex', 'items-center', 'justify-center');
                                ellipsis.textContent = '...';
                                paginationNumbers.appendChild(ellipsis);
                            }
                        }

                        for (let i = startPage; i <= endPage; i++) {
                            const btn = document.createElement('button');
                            if (i === currentPage) {
                                btn.classList.add('h-8', 'w-8', 'flex', 'items-center', 'justify-center', 'border', 'border-[#213268]', 'bg-[#213268]', 'text-white', 'rounded');
                            } else {
                                btn.classList.add('h-8', 'w-8', 'flex', 'items-center', 'justify-center', 'border', 'border-[#D8DAE5]', 'text-[#213268]', 'rounded', 'hover:bg-gray-100');
                            }
                            btn.textContent = i;
                            btn.addEventListener('click', () => {
                                currentPage = i;
                                loadAssets();
                            });
                            paginationNumbers.appendChild(btn);
                        }

                        if (endPage < totalPages) {
                            if (endPage < totalPages - 1) {
                                const ellipsis = document.createElement('span');
                                ellipsis.classList.add('flex', 'items-center', 'justify-center');
                                ellipsis.textContent = '...';
                                paginationNumbers.appendChild(ellipsis);
                            }

                            const btn = document.createElement('button');
                            btn.classList.add('h-8', 'w-8', 'flex', 'items-center', 'justify-center', 'border', 'border-[#D8DAE5]', 'text-[#213268]', 'rounded', 'hover:bg-gray-100');
                            btn.textContent = totalPages;
                            btn.addEventListener('click', () => {
                                currentPage = totalPages;
                                loadAssets();
                            });
                            paginationNumbers.appendChild(btn);
                        }
                    }
                    })
                    .catch(error => {
                        console.error('Error loading assets:', error);
                        if (tableBody) {
                            tableBody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center text-red-500">
                                            Error loading assets: ${error.message || 'Unknown error'}. Please try again.
                                        </td>
                                    </tr>
                                `;
                        }
                    });
            }

            function updateSelectAllCheckbox() {
                const selectAllCheckbox = document.getElementById('select-all-link-assets');
                if (!selectAllCheckbox) return;

                const checkboxes = document.querySelectorAll('.asset-checkbox');
                if (checkboxes.length === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                    return;
                }

                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                const someChecked = Array.from(checkboxes).some(cb => cb.checked);

                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }



            function linkAssets() {
                if (selectedAssets.length === 0) return;

                const assetsToLink = selectedAssets.filter(assetId => !linkedAssetIds.includes(assetId));

                if (assetsToLink.length === 0) {
                    showToast('Semua asset yang dipilih sudah terhubung dengan dokumen ini.', 'error');
                    return;
                }

                const data = {
                    asset_ids: assetsToLink
                };

                const linkButton = document.getElementById('link-selected-assets');
                if (linkButton) {
                    const originalText = linkButton.innerHTML;
                    linkButton.disabled = true;
                    linkButton.innerHTML = `
                            <div class="flex items-center justify-center w-full">
                                <svg class="animate-spin h-5 w-5 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Menautkan...</span>
                            </div>
                        `;

                    fetch(`/asset-documents/${documentId}/assign`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                const modal = document.getElementById('linkAssetsModal');
                                const content = document.getElementById('linkAssetsModalContent');
                                if (modal && content) {
                                    closeModal(modal, content);
                                }

                            showToast(result.message || 'Asset berhasil ditautkan!', 'success');

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                            showToast(result.message || 'Gagal menautkan asset', 'error');

                                if (linkButton) {
                                    linkButton.disabled = false;
                                    linkButton.innerHTML = originalText;
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error linking assets:', error);
                            let errorMessage = 'Terjadi kesalahan saat menautkan asset.';
                            if (error.message) {
                                errorMessage += ' ' + error.message;
                            }

                        showToast(errorMessage, 'error');

                            // Reset button
                            if (linkButton) {
                                linkButton.disabled = false;
                                linkButton.innerHTML = originalText;
                            }
                        });
                }
            }

            // Add this to the existing DOMContentLoaded event handler
            document.querySelectorAll('.unlink-asset-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const assetId = this.dataset.assetId;
                    const assetCode = this.dataset.assetCode;
                    const assetName = this.dataset.assetName;
                    const documentId = this.dataset.documentId;

                    const unlinkModal = document.getElementById('unlinkAssetModal');
                    const unlinkContent = document.getElementById('unlinkAssetModalContent');

                    if (unlinkModal && unlinkContent) {
                        document.getElementById('unlink-asset-info').textContent = `${assetCode} - ${assetName}`;
                        document.getElementById('unlink-form').action =
                            `{{ url('asset-documents/asset') }}/${assetId}/documents/${documentId}?redirect={{ url()->current() }}`;

                        openModal(unlinkModal, unlinkContent);
                    }
                });
            });

            document.getElementById('unlink-form')?.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

                fetch(this.action, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const modal = document.getElementById('unlinkAssetModal');
                    const content = document.getElementById('unlinkAssetModalContent');
                    closeModal(modal, content);

                    if (data.success) {
                        showToast('Hubungan asset dengan dokumen berhasil diputus', 'success');

                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast(data.message || 'Gagal memutus hubungan asset', 'error');

                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const modal = document.getElementById('unlinkAssetModal');
                    const content = document.getElementById('unlinkAssetModalContent');
                    closeModal(modal, content);
                    showToast('Terjadi kesalahan, silakan coba lagi', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
            });

            document.getElementById('edit_document_title')?.addEventListener('input', function() {
                this.classList.remove('border-red-500');
                const errorElement = this.closest('div').querySelector('.error-message');
                if (errorElement) errorElement.classList.add('hidden');
            });

            document.getElementById('edit_file')?.addEventListener('change', function() {
                const errorElement = this.closest('div').nextElementSibling;
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.classList.add('hidden');
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('[id$="Modal"]:not(.hidden)').forEach(modal => {
                        const content = modal.querySelector('[id$="Content"]');
                        if (content) {
                            closeModal(modal, content);
                        }
                    });
                }
            });
        });
    </script>
    <div id="toast-container" class="fixed top-4 right-4 z-[70] flex flex-col gap-2"></div>
@endpush
