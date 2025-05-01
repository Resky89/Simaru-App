@extends('Layout.app')

@section('title', 'Asset Documents Management')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Asset Documents Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">ASSET DOCUMENTS</h1>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3">
                        <button id="addDocumentBtn" class="flex items-center justify-center gap-2 px-3 py-3 bg-[#213268] rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-base">Add Document</span>
                        </button>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-grow">
                        <input type="text" id="searchInput" placeholder="Search by document title or notes..."
                            class="w-full h-[45px] px-4 pr-10 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <select id="sortOrder"
                            class="h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200">
                            <option value="" disabled selected>Select Sort Order</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="title_asc">Title (A-Z)</option>
                            <option value="title_desc">Title (Z-A)</option>
                        </select>
                    </div>
                </div>

                <!-- Documents Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Document ID</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Document Title</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Upload Date</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Uploaded By</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Notes</th>
                                <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($documents) && count($documents) > 0)
                                @foreach($documents as $document)
                                <tr data-document-id="{{ $document['document_id'] ?? '' }}">
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $document['document_id'] ?? 'N/A' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">{{ $document['document_title'] ?? '-' }}</td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        @if(isset($document['upload_date']))
                                            {{ \Carbon\Carbon::parse($document['upload_date'])->format('d M Y, H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        @if(isset($document['uploader']) && isset($document['uploader']['employee_number']))
                                            {{ $document['uploader']['employee_number'] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                        <div class="max-w-[250px] truncate" title="{{ $document['notes'] ?? '' }}">
                                            {{ $document['notes'] ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('document.view', $document['document_id']) }}" class="p-2 bg-[#D5E1F7] text-[#213268] rounded-md hover:bg-blue-200 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <button class="edit-document-btn p-2 bg-[#FEF9CF] text-[#7B5804] rounded-md hover:bg-yellow-200 transition-colors" data-id="{{ $document['document_id'] }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button class="delete-document-btn p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors" data-id="{{ $document['document_id'] }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="p-3 text-xs border-t border-[#EEF1F4] text-center">No documents found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Documents -->
                @if(isset($documents_pagination) && $documents_pagination)
                <div class="flex flex-col md:flex-row justify-between items-center mt-4">
                    <div class="flex items-center space-x-2">
                        <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($documents_pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changePage({{ ($documents_pagination['current_page'] ?? 1) - 1 }})"
                               {{ ($documents_pagination['current_page'] ?? 1) <= 1 ? 'disabled' : '' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Prev
                        </button>

                        <div class="flex gap-2">
                            @php
                                $currentPage = $documents_pagination['current_page'] ?? 1;
                                $lastPage = $documents_pagination['last_page'] ?? 1;
                            @endphp

                            @for($i = max(1, $currentPage - 1); $i <= min($lastPage, $currentPage + 1); $i++)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                                   class="h-8 w-8 flex items-center justify-center border {{ $i == $currentPage ? 'border-[#213268] bg-[#213268] text-white' : 'border-[#D8DAE5] text-[#213268]' }} rounded">
                                    {{ $i }}
                                </a>
                            @endfor
                        </div>

                        <button class="flex items-center gap-2 px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm hover:bg-gray-50 {{ ($documents_pagination['current_page'] ?? 1) >= ($documents_pagination['last_page'] ?? 1) ? 'opacity-50 cursor-not-allowed' : '' }}"
                               onclick="changePage({{ ($documents_pagination['current_page'] ?? 1) + 1 }})"
                               {{ ($documents_pagination['current_page'] ?? 1) >= ($documents_pagination['last_page'] ?? 1) ? 'disabled' : '' }}>
                            Next
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">
                            @if(isset($documents_pagination) && is_array($documents_pagination))
                                @php
                                    $currentPage = $documents_pagination['current_page'] ?? 1;
                                    $perPage = $documents_pagination['per_page'] ?? 10;
                                    $total = $documents_pagination['total'] ?? count($documents ?? []);
                                    $from = ($currentPage - 1) * $perPage + 1;
                                    $to = min($currentPage * $perPage, $total);
                                @endphp
                                Showing {{ $from }} to {{ $to }} of {{ $total }} entries
                            @else
                                Showing 1 to {{ count($documents ?? []) }} of {{ count($documents ?? []) }} entries
                            @endif
                        </span>
                        <select id="perPageSelect" class="px-2 h-8 border border-[#D8DAE5] rounded text-[#213268] text-sm" onchange="changePerPage(this.value)">
                            <option value="10" {{ isset($documents_pagination['per_page']) && $documents_pagination['per_page'] == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ isset($documents_pagination['per_page']) && $documents_pagination['per_page'] == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ isset($documents_pagination['per_page']) && $documents_pagination['per_page'] == 50 ? 'selected' : '' }}>50 per page</option>
                        </select>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Document Modal -->
<div id="addDocumentModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="addDocumentModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#28356B]">ADD NEW DOCUMENT</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <form id="addDocumentForm" action="{{ route('asset-documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <!-- Document Title -->
                            <div>
                                <label for="document_title" class="block text-sm font-medium text-gray-700 mb-1">Document Title <span class="text-red-500">*</span></label>
                                <input type="text" id="document_title" name="document_title"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#28356B] focus:ring focus:ring-[#28356B] focus:ring-opacity-20"
                                    required>
                            </div>

                            <!-- File Upload -->
                            <div>
                                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
                                <div class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <!-- Image preview -->
                                    <div id="image-preview" class="mt-2 mb-4 w-full hidden">
                                        <div class="relative bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                            <img id="preview-img" src="" class="w-full h-auto max-h-64 object-contain mx-auto rounded" alt="Selected Image">
                                            <button type="button" id="remove-image" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- File preview (non-image) -->
                                    <div id="file-name" class="mt-2 mb-4 w-full hidden">
                                        <div class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                            <div class="flex items-center">
                                                <svg class="w-6 h-6 text-gray-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span id="file-name-text" class="text-sm text-gray-700 truncate"></span>
                                                <button type="button" id="remove-file" class="ml-auto text-red-500 hover:text-red-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-[#213268]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Drag your file or <span class="text-[#213268] font-semibold">browse files</span></p>
                                        <p class="mt-1 text-xs text-gray-500">Accepted formats: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG</p>
                                        <p class="mt-1 text-xs text-[#213268] font-medium">Click anywhere in this area to select a file</p>
                                    </div>
                                    <input type="file" id="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea id="notes" name="notes" rows="3"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#28356B] focus:ring focus:ring-[#28356B] focus:ring-opacity-20"></textarea>
                            </div>

                            <!-- Associated Assets (optional field for future use) -->
                            <div class="hidden">
                                <label for="asset_ids" class="block text-sm font-medium text-gray-700 mb-1">Associated Assets</label>
                                <select id="asset_ids" name="asset_ids[]" multiple class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#28356B] focus:ring focus:ring-[#28356B] focus:ring-opacity-20">
                                    <!-- Options would be populated dynamically -->
                                </select>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-3 mt-6">
                                <button type="submit" class="w-full h-[45px] bg-[#28356B] text-white rounded-lg text-base hover:bg-[#1d2754]">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Create Document
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="deleteModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">DELETE DOCUMENT</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="p-6">
                        <div class="space-y-6 max-w-[400px] mx-auto">
                            <div class="flex flex-col items-center">
                                <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-base text-gray-600 text-center">Are you sure you want to delete this document? This action cannot be undone.</p>
                                <p id="delete-document-title" class="text-base font-semibold text-center mt-2"></p>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                    Cancel
                                </button>
                                <button type="submit" class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Document Modal -->
<div id="editDocumentModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="editDocumentModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#28356B]">EDIT DOCUMENT</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <form id="editDocumentForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_document_id" name="document_id">
                        <div class="space-y-4">
                            <!-- Document Title -->
                            <div>
                                <label for="edit_document_title" class="block text-sm font-medium text-gray-700 mb-1">Document Title <span class="text-red-500">*</span></label>
                                <input type="text" id="edit_document_title" name="document_title"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#28356B] focus:ring focus:ring-[#28356B] focus:ring-opacity-20"
                                    required>
                            </div>

                            <!-- File Upload -->
                            <div>
                                <label for="edit_file" class="block text-sm font-medium text-gray-700 mb-1">Replace File (Optional)</label>
                                <div class="border-2 border-dashed border-[#213268] rounded-lg p-6 relative flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <!-- Current File Info (if any) -->
                                    <div id="edit_current_file" class="mb-4 w-full">
                                        <!-- Current file is an image -->
                                        <div id="edit_current_image" class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto hidden">
                                            <div class="relative">
                                                <img id="edit_current_img" src="" class="w-full h-auto max-h-64 object-contain mx-auto rounded" alt="Current Document Image">
                                                <button type="button" id="edit_remove_current_file" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Current file is not an image -->
                                        <div id="edit_current_file_icon" class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto hidden">
                                            <div class="flex items-center">
                                                <svg class="w-6 h-6 text-gray-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span id="edit_file_name" class="text-sm text-gray-700 truncate"></span>
                                                <button type="button" id="edit_remove_current_file_icon" class="ml-auto text-red-500 hover:text-red-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- New File preview -->
                                    <!-- Image preview for new file -->
                                    <div id="edit_image_preview" class="mt-2 mb-4 w-full hidden">
                                        <div class="relative bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                            <img id="edit_preview_img" src="" class="w-full h-auto max-h-64 object-contain mx-auto rounded" alt="Selected Image">
                                            <button type="button" id="edit_remove_image" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- File preview (non-image) for new file -->
                                    <div id="edit_file_preview" class="mt-2 mb-4 w-full hidden">
                                        <div class="bg-white p-2 rounded border border-gray-300 w-full max-w-md mx-auto">
                                            <div class="flex items-center">
                                                <svg class="w-6 h-6 text-gray-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span id="edit_file_preview_text" class="text-sm text-gray-700 truncate"></span>
                                                <button type="button" id="edit_remove_file" class="ml-auto text-red-500 hover:text-red-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-[#213268]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Drag your file or <span class="text-[#213268] font-semibold">browse files</span></p>
                                        <p class="mt-1 text-xs text-gray-500">Accepted formats: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG</p>
                                        <p class="mt-1 text-xs text-[#213268] font-medium">Click anywhere in this area to select a file</p>
                                    </div>
                                    <input type="file" id="edit_file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="edit_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea id="edit_notes" name="notes" rows="3"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#28356B] focus:ring focus:ring-[#28356B] focus:ring-opacity-20"></textarea>
                            </div>

                            <!-- Associated Assets (hidden for future use) -->
                            <div class="hidden">
                                <label for="edit_asset_ids" class="block text-sm font-medium text-gray-700 mb-1">Associated Assets</label>
                                <select id="edit_asset_ids" name="asset_ids[]" multiple class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#28356B] focus:ring focus:ring-[#28356B] focus:ring-opacity-20">
                                    <!-- Options would be populated dynamically -->
                                </select>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end mt-6">
                                <button type="submit" class="w-full h-[45px] bg-[#28356B] text-white rounded-lg text-base hover:bg-[#1d2754]">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        Update Document
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
@endif

@if(session('error') || isset($error))
<div id="errorNotification" class="fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md z-50" role="alert">
    <div class="flex items-center">
        <div class="py-1">
            <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="font-bold">Error!</p>
            <p>{!! session('error') ?? $error ?? 'An error occurred' !!}</p>
        </div>
        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal functionality
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

        // Helper function to check if file is an image
        const isImageFile = function(file) {
            return file && file.type.match(/^image\/(jpeg|jpg|png|gif|webp)$/i);
        };

        // Helper function to get file icon based on extension
        const getFileIcon = function(filename) {
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

        // Icon SVG templates
        const getDocumentIcon = function() {
            return `<svg class="w-6 h-6 text-gray-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>`;
        };

        const getPdfIcon = function() {
            return `<svg class="w-6 h-6 text-red-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                <text x="12" y="16" font-family="Arial" font-size="6" fill="currentColor" text-anchor="middle">PDF</text>
            </svg>`;
        };

        const getWordIcon = function() {
            return `<svg class="w-6 h-6 text-blue-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                <text x="12" y="16" font-family="Arial" font-size="5" fill="currentColor" text-anchor="middle">DOC</text>
            </svg>`;
        };

        const getExcelIcon = function() {
            return `<svg class="w-6 h-6 text-green-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                <text x="12" y="16" font-family="Arial" font-size="5" fill="currentColor" text-anchor="middle">XLS</text>
            </svg>`;
        };

        // Function to update file icon - replaces the icon with the appropriate one based on filename
        const updateFileIcon = function(containerSelector, fileName) {
            const container = document.querySelector(containerSelector);
            if (container) {
                const iconContainer = container.querySelector('.flex');
                if (iconContainer) {
                    // Remove existing icon
                    const existingIcon = iconContainer.querySelector('svg');
                    if (existingIcon) {
                        existingIcon.remove();
                    }
                    // Create a temporary element to convert HTML string to DOM element
                    const temp = document.createElement('div');
                    temp.innerHTML = getFileIcon(fileName);
                    // Insert the new icon at the beginning of the flex container
                    iconContainer.insertBefore(temp.firstChild, iconContainer.firstChild);
                }
            }
        };

        // File upload preview for add modal
        const fileInput = document.getElementById('file');
        const fileNameDisplay = document.getElementById('file-name');
        const fileNameText = document.getElementById('file-name-text');
        const imagePreview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const fileName = file.name;

                    // Check if the file is an image
                    if (isImageFile(file)) {
                        // Handle image file
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            imagePreview.classList.remove('hidden');
                            fileNameDisplay.classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // Handle non-image file
                        fileNameText.textContent = fileName;
                        // Update the icon based on file type
                        updateFileIcon('#file-name', fileName);
                        fileNameDisplay.classList.remove('hidden');
                        imagePreview.classList.add('hidden');
                    }
                } else {
                    // No file selected
                    fileNameDisplay.classList.add('hidden');
                    imagePreview.classList.add('hidden');
                }
            });
        }

        // Remove file button for add modal
        document.getElementById('remove-file')?.addEventListener('click', function() {
            if (fileInput) {
                fileInput.value = ''; // Clear the file input
            }
            fileNameDisplay.classList.add('hidden');
        });

        // Remove image button for add modal
        document.getElementById('remove-image')?.addEventListener('click', function() {
            if (fileInput) {
                fileInput.value = ''; // Clear the file input
            }
            imagePreview.classList.add('hidden');
        });

        // Add document button
        document.getElementById('addDocumentBtn')?.addEventListener('click', function() {
            const modal = document.getElementById('addDocumentModal');
            const content = document.getElementById('addDocumentModalContent');
            if (modal && content) {
                openModal(modal, content);
            }
        });

        // Delete document functionality
        document.querySelectorAll('.delete-document-btn').forEach(button => {
            button.addEventListener('click', function() {
                const documentId = this.dataset.id;
                const documentTitle = this.dataset.title;
                const deleteModal = document.getElementById('deleteModal');
                const deleteContent = document.getElementById('deleteModalContent');

                if (deleteModal && deleteContent) {
                    document.getElementById('delete-document-title').textContent = documentTitle;
                    document.getElementById('delete-form').action = `{{ url('asset-documents') }}/${documentId}`;
                    openModal(deleteModal, deleteContent);
                }
            });
        });

        // Modal close buttons
        document.querySelectorAll('.close-modal').forEach(closeButton => {
            closeButton.addEventListener('click', function() {
                const modal = this.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="Content"]');
                if (modal && content) {
                    closeModal(modal, content);
                }
            });
        });

        // Close modal when clicking outside
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    const content = this.querySelector('[id$="Content"]');
                    if (content) {
                        closeModal(this, content);
                    }
                }
            });
        });

        // Search and filter functionality
        const searchInput = document.getElementById('searchInput');
        const sortOrder = document.getElementById('sortOrder');

        // Function to handle search and filtering
        function applyFilters() {
            const searchValue = searchInput?.value.trim() || '';
            const sortValue = sortOrder?.value || '';

            // Create URL with filter parameters
            const url = new URL(window.location.href);

            // Clear existing parameters we're going to set
            ['search', 'sort', 'page'].forEach(param => {
                url.searchParams.delete(param);
            });

            // Add new parameters if they have values
            if (searchValue) url.searchParams.set('search', searchValue);
            if (sortValue) url.searchParams.set('sort', sortValue);

            // Reset to page 1 when filters change
            url.searchParams.set('page', 1);

            // Navigate to the new URL
            window.location.href = url.toString();
        }

        // Add event listeners with debounce for search
        let searchTimeout;
        searchInput?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });

        // Add event listeners for select filters
        sortOrder?.addEventListener('change', applyFilters);

        // Set initial values from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (searchInput) searchInput.value = urlParams.get('search') || '';
        if (sortOrder) {
            const sortValue = urlParams.get('sort');
            if (sortValue) {
                sortOrder.value = sortValue;
            }
        }

        // Pagination helpers
        window.changePage = function(page) {
            if (page < 1) return;

            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        };

        window.changePerPage = function(perPage) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', perPage);
            url.searchParams.set('page', 1); // Reset to page 1 when changing items per page
            window.location.href = url.toString();
        };

        // Auto-hide notifications after 5 seconds
        setTimeout(function() {
            const notifications = document.querySelectorAll('#successNotification, #errorNotification');
            notifications.forEach(notification => {
                if (notification) {
                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => notification.remove(), 500);
                }
            });
        }, 5000);

        // Edit document functionality
        document.querySelectorAll('.edit-document-btn').forEach(button => {
            button.addEventListener('click', function() {
                const documentId = this.dataset.id;
                fetchDocumentDetails(documentId);
            });
        });

        // Function to fetch document details
        function fetchDocumentDetails(documentId) {
            fetch(`/asset-documents/${documentId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        openEditModal(data.data);
                    } else {
                        alert('Failed to fetch document details: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error fetching document details:', error);
                    alert('Error fetching document details. Please try again later.');
                });
        }

        // Helper function to check if filename has image extension
        const hasImageExtension = function(filename) {
            if (!filename) return false;
            return /\.(jpg|jpeg|png|gif|webp)$/i.test(filename);
        };

        // Function to open edit modal and populate form
        function openEditModal(docData) {
            // Set hidden document ID field
            document.getElementById('edit_document_id').value = docData.document_id;

            // Update form action with the correct URL and method
            const editForm = document.getElementById('editDocumentForm');
            editForm.action = `{{ url('asset-documents') }}/${docData.document_id}`;

            // Populate form fields
            document.getElementById('edit_document_title').value = docData.document_title || '';
            document.getElementById('edit_notes').value = docData.notes || '';

            // Handle file display if one exists
            const currentFileSection = document.getElementById('edit_current_file');
            const currentImageSection = document.getElementById('edit_current_image');
            const currentFileIconSection = document.getElementById('edit_current_file_icon');
            const fileNameDisplay = document.getElementById('edit_file_name');
            const currentImg = document.getElementById('edit_current_img');

            // Reset all preview sections
            currentImageSection.classList.add('hidden');
            currentFileIconSection.classList.add('hidden');

            if (docData.file_path && docData.file_path.trim() !== '') {
                // Extract filename from path
                const filename = docData.file_path.split('/').pop();

                // Check if it's an image file
                if (hasImageExtension(filename)) {
                    // Set image source - prefix with the backend URL if needed
                    currentImg.src = `{{ config('app.backend_url') }}/public${docData.file_path}`;
                    currentImageSection.classList.remove('hidden');
                } else {
                    // Show as regular file with appropriate icon
                    fileNameDisplay.textContent = filename || 'Document File';
                    updateFileIcon('#edit_current_file_icon', filename);
                    currentFileIconSection.classList.remove('hidden');
                }

                currentFileSection.classList.remove('hidden');
            } else {
                currentFileSection.classList.add('hidden');
            }

            // Clear any new file selection
            document.getElementById('edit_file').value = '';
            document.getElementById('edit_file_preview').classList.add('hidden');
            document.getElementById('edit_image_preview').classList.add('hidden');

            // Open the modal
            const modal = document.getElementById('editDocumentModal');
            const content = document.getElementById('editDocumentModalContent');
            openModal(modal, content);
        }

        // Edit file upload preview
        const editFileInput = document.getElementById('edit_file');
        const editFilePreview = document.getElementById('edit_file_preview');
        const editImagePreview = document.getElementById('edit_image_preview');
        const editFilePreviewText = document.getElementById('edit_file_preview_text');
        const editPreviewImg = document.getElementById('edit_preview_img');
        const editCurrentFile = document.getElementById('edit_current_file');

        if (editFileInput) {
            editFileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const fileName = file.name;

                    // Hide current file display when a new file is selected
                    if (editCurrentFile) {
                        editCurrentFile.classList.add('hidden');
                    }

                    // Check if the file is an image
                    if (isImageFile(file)) {
                        // Handle image file
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            editPreviewImg.src = e.target.result;
                            editImagePreview.classList.remove('hidden');
                            editFilePreview.classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // Handle non-image file
                        editFilePreviewText.textContent = fileName;
                        // Update the icon based on file type
                        updateFileIcon('#edit_file_preview', fileName);
                        editFilePreview.classList.remove('hidden');
                        editImagePreview.classList.add('hidden');
                    }
                } else {
                    // No file selected
                    editFilePreview.classList.add('hidden');
                    editImagePreview.classList.add('hidden');

                    // Show current file display again if no new file is selected
                    if (editCurrentFile) {
                        editCurrentFile.classList.remove('hidden');
                    }
                }
            });
        }

        // Remove edit file button (for non-image files)
        document.getElementById('edit_remove_file')?.addEventListener('click', function() {
            if (editFileInput) {
                editFileInput.value = ''; // Clear the file input
            }
            editFilePreview.classList.add('hidden');

            // Show current file display again when new file is removed
            if (editCurrentFile) {
                editCurrentFile.classList.remove('hidden');
            }
        });

        // Remove edit image button (for image files)
        document.getElementById('edit_remove_image')?.addEventListener('click', function() {
            if (editFileInput) {
                editFileInput.value = ''; // Clear the file input
            }
            editImagePreview.classList.add('hidden');

            // Show current file display again when new file is removed
            if (editCurrentFile) {
                editCurrentFile.classList.remove('hidden');
            }
        });

        // Remove current file button (for both icon and image views)
        const setupRemoveCurrentFile = (buttonId) => {
            document.getElementById(buttonId)?.addEventListener('click', function() {
                // Hide the current file display
                document.getElementById('edit_current_file').classList.add('hidden');
                document.getElementById('edit_current_image').classList.add('hidden');
                document.getElementById('edit_current_file_icon').classList.add('hidden');

                // Add a hidden input to indicate the file should be removed
                const removeFileInput = document.createElement('input');
                removeFileInput.type = 'hidden';
                removeFileInput.name = 'remove_file';
                removeFileInput.value = '1';

                // Add to the form
                document.getElementById('editDocumentForm').appendChild(removeFileInput);
            });
        };

        setupRemoveCurrentFile('edit_remove_current_file');
        setupRemoveCurrentFile('edit_remove_current_file_icon');
    });
</script>
@endpush
