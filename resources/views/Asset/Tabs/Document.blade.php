<div class="p-3">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 gap-2">
        <h2 class="text-base font-bold text-[#213268]">DOCUMENT</h2>
        <button id="addDocumentBtn" class="flex items-center justify-center gap-1 px-3 py-1.5 bg-[#213268] rounded-lg text-white text-xs">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Document</span>
        </button>
    </div>

    <!-- Document Table -->
    <div class="overflow-x-auto -mx-3 sm:mx-0 rounded-md">
        <table class="w-full min-w-[500px] border-collapse">
            <thead>
                <tr>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left">Title</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left">File name</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left">Notes</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left">Upload Date</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-center w-16 md:w-20">Action</th>
                </tr>
            </thead>
            <tbody id="documentTableBody">
                <tr class="document-loading-row">
                    <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                        <div class="flex justify-center items-center">
                            <svg class="animate-spin h-5 w-5 text-[#213268] mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading documents...
                        </div>
                    </td>
                </tr>
                <!-- Document rows will be loaded here dynamically -->
            </tbody>
        </table>
    </div>

    <!-- Add Document Modal -->
    <div id="addDocumentModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="addDocumentModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">ADD NEW DOCUMENT</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="addDocumentModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="addDocumentForm" action="{{ route('asset-documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="asset_id" value="{{ $asset['asset_id'] ?? '' }}">
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Title Input -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Title</label>
                                    <input type="text" name="document_title" required
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Document title">
                                </div>

                                <!-- Notes Input -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Notes</label>
                                    <textarea name="notes" rows="3"
                                        class="w-full px-4 py-2 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Document notes"></textarea>
                                </div>

                                <!-- File Upload -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Insert Your File</label>
                                    <p class="text-sm text-gray-500">Maximum file size allowed: 10 MB</p>

                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 relative flex flex-col items-center justify-center">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="mt-1 text-sm text-gray-600">Drag your file(s) or <span class="text-blue-600">browse</span></p>
                                            <p class="mt-1 text-xs text-gray-500">jpg, jpeg, png, docx, doc, pdf or csv</p>
                                        </div>
                                        <input type="file" id="document_file" name="document" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    </div>
                                    <div id="document-preview-container" class="mt-2 hidden">
                                        <img id="document-preview-image" class="max-h-40 rounded-lg" alt="Document Preview">
                                    </div>
                                </div>

                                <!-- Save Button -->
                                <div class="pt-4">
                                    <button type="submit" id="addDocumentSubmitBtn" class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Preview Modal -->
    <div id="previewDocumentModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[800px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="previewDocumentModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]" id="previewDocumentTitle">DOCUMENT PREVIEW</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="previewDocumentModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Preview Content -->
                    <div class="p-6 space-y-4">
                        <div id="document-preview-content" class="flex flex-col items-center justify-center min-h-[300px] p-4 border border-gray-200 rounded-lg">
                            <!-- Preview will be loaded here -->
                            <div class="text-center" id="preview-placeholder">
                                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">Loading document preview...</p>
                            </div>
                            <!-- Image Preview -->
                            <img id="preview-image" class="max-w-full max-h-[500px] object-contain hidden" alt="Document Preview">
                            <!-- PDF Preview -->
                            <iframe id="preview-pdf" class="w-full h-[500px] hidden" src="" title="PDF Preview"></iframe>
                            <!-- File Icon for non-previewable documents -->
                            <div id="preview-file-icon" class="text-center hidden">
                                <div id="file-type-icon" class="mx-auto h-32 w-32 flex items-center justify-center rounded-lg border-2 border-dashed border-gray-300 mb-4">
                                    <!-- File type icon will be placed here -->
                                </div>
                                <p id="preview-filename" class="text-lg font-medium"></p>
                                <p class="mt-2 text-sm text-gray-500">This file type cannot be previewed</p>
                                <a id="download-link" href="#" target="_blank" class="mt-4 inline-flex items-center px-4 py-2 bg-[#213268] rounded-md text-white hover:bg-[#152349]">
                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download File
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Document Modal -->
    <div id="deleteDocumentModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="deleteDocumentModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">DELETE DOCUMENT</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="deleteDocumentModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <form id="deleteDocumentForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="p-6">
                            <div class="space-y-6 max-w-[400px] mx-auto">
                                <div class="flex flex-col items-center">
                                    <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-base text-gray-600 text-center">Are you sure you want to delete this document? This action cannot be undone.</p>
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200" data-modal="deleteDocumentModal">
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
</div>

<!-- Add JavaScript for Document Modal functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Document Modal System
    if (typeof window.DocumentModalSystem === 'undefined') {
        window.DocumentModalSystem = {
            initialized: false,
            assetId: {{ $asset['asset_id'] ?? 'null' }},
            apiBaseUrl: "{{ config('app.api_url', '') }}",

            init: function() {
                console.log('DocumentModalSystem: Initializing document modals');
                this.setupModalFunctions();
                this.attachEventHandlers();
                this.setupFilePreview();
                this.loadDocuments();
                this.initialized = true;
            },

            setupModalFunctions: function() {
                // Define openModal function in global scope if not already defined
                if (typeof window.openModal !== 'function') {
                    window.openModal = function(modal, content) {
                        modal.classList.remove('hidden');
                        setTimeout(() => {
                            content.classList.remove('scale-95', 'opacity-0', 'translate-y-4', 'sm:translate-y-0');
                            content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                        }, 10);
                    };
                }

                // Define closeModal function in global scope if not already defined
                if (typeof window.closeModal !== 'function') {
                    window.closeModal = function(modal, content) {
                        content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                        content.classList.add('scale-95', 'opacity-0', 'translate-y-4', 'sm:translate-y-0');
                        setTimeout(() => {
                            modal.classList.add('hidden');
                        }, 300);
                    };
                }
            },

            attachEventHandlers: function() {
                const self = this;

                // Add Document Button
                const addDocumentBtn = document.getElementById('addDocumentBtn');
                if (addDocumentBtn) {
                    addDocumentBtn.addEventListener('click', function() {
                        const modal = document.getElementById('addDocumentModal');
                        const content = document.getElementById('addDocumentModalContent');
                        if (modal && content) {
                            // Reset form before opening
                            document.getElementById('addDocumentForm').reset();
                            // Reset file preview
                            document.getElementById('document-preview-container').classList.add('hidden');
                            openModal(modal, content);
                        }
                    });
                }

                // Add Document Form Submit
                const addDocumentForm = document.getElementById('addDocumentForm');
                if (addDocumentForm) {
                    addDocumentForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        self.submitDocumentForm(this);
                    });
                }

                // Close Modals
                document.querySelectorAll('.close-modal').forEach(button => {
                    button.addEventListener('click', function() {
                        const modalId = this.getAttribute('data-modal') || this.closest('[id$="Modal"]').id;
                        const modal = document.getElementById(modalId);
                        const content = document.getElementById(modalId + 'Content');
                        if (modal && content) {
                            closeModal(modal, content);
                        }
                    });
                });

                // Document delete buttons (will be attached dynamically when documents are loaded)
                // Event delegation for dynamically added elements
                document.addEventListener('click', function(event) {
                    const deleteBtn = event.target.closest('.delete-document-btn');
                    if (deleteBtn) {
                        event.preventDefault();
                        const documentId = deleteBtn.getAttribute('data-id');
                        self.deleteDocument(documentId);
                    }
                });

                // Add event delegation for preview buttons
                document.addEventListener('click', function(event) {
                    const previewBtn = event.target.closest('.preview-document-btn');
                    if (previewBtn) {
                        event.preventDefault();
                        const docId = previewBtn.getAttribute('data-id');
                        const docTitle = previewBtn.getAttribute('data-title');
                        const filePath = previewBtn.getAttribute('data-path');
                        const fileName = previewBtn.getAttribute('data-filename');
                        const fileType = previewBtn.getAttribute('data-type');
                        self.previewDocument(docId, docTitle, filePath, fileName, fileType);
                    }
                });
            },

            setupFilePreview: function() {
                const documentFileInput = document.getElementById('document_file');
                const previewContainer = document.getElementById('document-preview-container');
                const previewImage = document.getElementById('document-preview-image');

                if (documentFileInput && previewContainer && previewImage) {
                    documentFileInput.addEventListener('change', function() {
                        const file = this.files[0];
                        if (file) {
                            // Show preview for image files
                            if (file.type.match('image.*')) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    previewImage.src = e.target.result;
                                    previewContainer.classList.remove('hidden');
                                };
                                reader.readAsDataURL(file);
                            } else {
                                // Show file icon with filename for non-image files
                                previewContainer.classList.remove('hidden');
                                previewImage.src = '';

                                // Create file type icon based on extension
                                const fileExt = file.name.split('.').pop().toLowerCase();
                                let fileIcon = '';

                                if (['pdf'].includes(fileExt)) {
                                    fileIcon = `<svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>`;
                                } else if (['doc', 'docx'].includes(fileExt)) {
                                    fileIcon = `<svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>`;
                                } else if (['xls', 'xlsx', 'csv'].includes(fileExt)) {
                                    fileIcon = `<svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>`;
                                } else {
                                    fileIcon = `<svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>`;
                                }

                                // Display file icon and name
                                previewContainer.innerHTML = `
                                    <div class="flex items-center p-3 border border-gray-200 rounded-lg">
                                        <div class="flex-shrink-0 mr-3">${fileIcon}</div>
                                        <div class="overflow-hidden">
                                            <p class="text-sm font-medium truncate">${file.name}</p>
                                            <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</p>
                                        </div>
                                    </div>
                                `;
                            }
                        } else {
                            previewContainer.classList.add('hidden');
                        }
                    });
                }
            },

            loadDocuments: function() {
                const self = this;
                const tableBody = document.getElementById('documentTableBody');

                if (!tableBody) return;

                // Show loading row
                tableBody.innerHTML = `
                    <tr class="document-loading-row">
                        <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                            <div class="flex justify-center items-center">
                                <svg class="animate-spin h-5 w-5 text-[#213268] mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Loading documents...
                            </div>
                        </td>
                    </tr>
                `;

                console.log('Fetching documents for asset ID:', this.assetId);

                // Fetch documents from API
                fetch(`/asset-documents/asset/${this.assetId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    console.log('Document API response:', result);

                    if (!result.status) {
                        // Show error message in table
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center text-red-500">
                                    Error: ${result.message || 'Failed to load documents'}
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    const documents = result.data || [];

                    if (documents.length === 0) {
                        // Show no documents message
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                                    No documents found for this asset
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    // Render documents
                    let html = '';
                    documents.forEach(doc => {
                        const fileName = doc.file_path ? doc.file_path.split('/').pop() : 'Unknown file';
                        const uploadDate = doc.upload_date ? new Date(doc.upload_date).toLocaleDateString() : '-';
                        const fileExt = fileName.split('.').pop().toLowerCase();

                        // Determine file icon based on extension
                        let fileIcon = `
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`;

                        // Change icon based on file type
                        if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                            fileIcon = `
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>`;
                        } else if (fileExt === 'pdf') {
                            fileIcon = `
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>`;
                        } else if (['doc', 'docx'].includes(fileExt)) {
                            fileIcon = `
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>`;
                        } else if (['xls', 'xlsx', 'csv'].includes(fileExt)) {
                            fileIcon = `
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>`;
                        }

                        html += `
                            <tr data-document-id="${doc.id}">
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">${doc.document_title || '-'}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">
                                    <div class="flex items-center">
                                        ${fileIcon}
                                        <span class="truncate max-w-[200px]">${fileName}</span>
                                    </div>
                                </td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">${doc.notes || '-'}</td>
                                <td class="p-3 text-xs border-t border-[#EEF1F4]">${uploadDate}</td>
                                <td class="p-3 border-t border-[#EEF1F4] text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <button class="text-[#3D3D3D] hover:text-[#213268] preview-document-btn"
                                                data-id="${doc.id}"
                                                data-title="${doc.document_title || 'Document Preview'}"
                                                data-path="${doc.file_path}"
                                                data-filename="${fileName}"
                                                data-type="${fileExt}"
                                                title="Preview">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <a href="${self.apiBaseUrl}${doc.file_path}" target="_blank" class="text-[#3D3D3D] hover:text-[#213268]" title="Download">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>
                                        <button class="text-[#3D3D3D] hover:text-red-500 delete-document-btn" data-id="${doc.id}" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    tableBody.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching documents:', error);
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center text-red-500">
                                Error loading documents. Please try again.
                            </td>
                        </tr>
                    `;
                });
            },

            submitDocumentForm: function(form) {
                const self = this;
                const formData = new FormData(form);
                const submitBtn = document.getElementById('addDocumentSubmitBtn');
                const modal = document.getElementById('addDocumentModal');
                const content = document.getElementById('addDocumentModalContent');

                // Validate file is selected
                const fileInput = document.getElementById('document_file');
                if (fileInput && fileInput.files.length === 0) {
                    alert('Please select a file to upload');
                    return;
                }

                // Get the form action URL
                const actionUrl = form.getAttribute('action');

                // Submit the form via fetch
                fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(result => {
                    if (!result.status) {
                        alert('Error: ' + (result.message || 'Failed to upload document'));
                        return;
                    }

                    // Close modal
                    if (modal && content && typeof window.closeModal === 'function') {
                        window.closeModal(modal, content);
                    }

                    // Reset form
                    form.reset();
                    document.getElementById('document-preview-container').classList.add('hidden');

                    // Reload documents
                    self.loadDocuments();

                    // Show success message
                    alert('Document uploaded successfully');
                })
                .catch(error => {
                    console.error('Error uploading document:', error);
                    alert('Error uploading document: ' + error.message);
                });
            },

            deleteDocument: function(docId) {
                const self = this;
                const modal = document.getElementById('deleteDocumentModal');
                const content = document.getElementById('deleteDocumentModalContent');
                const form = document.getElementById('deleteDocumentForm');

                // Set the form action
                form.action = `/asset-documents/${docId}`;

                // Clear any previous event handlers
                const newForm = form.cloneNode(true);
                form.parentNode.replaceChild(newForm, form);

                // Add submit event handler
                newForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Show loading state on the button
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    `;

                    fetch(`/asset-documents/${docId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(result => {
                        // Close the modal
                        closeModal(modal, content);

                        if (result.status) {
                            // Success, refresh documents
                            self.loadDocuments();

                            // Show success message (you could replace this with a toast notification)
                            alert('Document deleted successfully');
                        } else {
                            // Show error message
                            alert('Error: ' + (result.message || 'Failed to delete document'));
                        }
                    })
                    .catch(error => {
                        // Close the modal
                        closeModal(modal, content);

                        console.error('Error deleting document:', error);
                        alert('Error deleting document. Please try again.');
                    })
                    .finally(() => {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    });
                });

                // Open the modal
                openModal(modal, content);
            },

            previewDocument: function(docId, docTitle, filePath, fileName, fileType) {
                const modal = document.getElementById('previewDocumentModal');
                const content = document.getElementById('previewDocumentModalContent');
                const titleEl = document.getElementById('previewDocumentTitle');
                const previewImage = document.getElementById('preview-image');
                const previewPdf = document.getElementById('preview-pdf');
                const previewFileIcon = document.getElementById('preview-file-icon');
                const placeholder = document.getElementById('preview-placeholder');
                const fileTypeIcon = document.getElementById('file-type-icon');
                const previewFilename = document.getElementById('preview-filename');
                const downloadLink = document.getElementById('download-link');

                if (!modal || !content) return;

                // Reset all preview elements
                previewImage.classList.add('hidden');
                previewPdf.classList.add('hidden');
                previewFileIcon.classList.add('hidden');
                placeholder.classList.remove('hidden');

                // Set document title
                titleEl.textContent = docTitle || 'Document Preview';

                // Set download link
                const fileUrl = this.apiBaseUrl + filePath;
                downloadLink.href = fileUrl;

                // Open modal while loading
                openModal(modal, content);

                // Set the preview based on file type
                fileType = fileType.toLowerCase();

                // Handle image files
                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
                    const img = new Image();
                    img.onload = function() {
                        previewImage.src = fileUrl;
                        previewImage.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                    };
                    img.onerror = function() {
                        // Show file icon if image fails to load
                        showFileIcon();
                    };
                    img.src = fileUrl;
                }
                // Handle PDF files
                else if (fileType === 'pdf') {
                    previewPdf.src = fileUrl;
                    previewPdf.classList.remove('hidden');
                    placeholder.classList.add('hidden');

                    // Add error handler for PDF
                    previewPdf.onerror = function() {
                        showFileIcon();
                    };
                }
                // Handle other file types
                else {
                    showFileIcon();
                }

                function showFileIcon() {
                    // Show appropriate file type icon
                    let iconHtml = '';

                    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
                        iconHtml = `
                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>`;
                    } else if (fileType === 'pdf') {
                        iconHtml = `
                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>`;
                    } else if (['doc', 'docx'].includes(fileType)) {
                        iconHtml = `
                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`;
                    } else if (['xls', 'xlsx', 'csv'].includes(fileType)) {
                        iconHtml = `
                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`;
                    } else {
                        iconHtml = `
                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>`;
                    }

                    fileTypeIcon.innerHTML = iconHtml;
                    previewFilename.textContent = fileName;
                    previewFileIcon.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
            }
        };

        // Global init function
        window.initDocumentModals = function() {
            if (!window.DocumentModalSystem.initialized) {
                window.DocumentModalSystem.init();
            } else {
                console.log('DocumentModalSystem already initialized, refreshing event handlers');
                window.DocumentModalSystem.attachEventHandlers();
            }
        };
    }

    // Initialize when page loads
    setTimeout(function() {
        if (typeof window.initDocumentModals === 'function') {
            window.initDocumentModals();
        }
    }, 100);
});
</script>
