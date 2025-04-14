<div class="p-3 md:p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-[#213268]">DOCUMENT</h2>
        <button id="addDocumentBtn" class="bg-[#213268] text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-[#162249] transition-colors flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            DOCUMENT
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
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 relative">
                                        <input type="file" id="document_file" name="document" required
                                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="mt-1 text-sm text-gray-600">Drag your file(s) or <span class="text-blue-600">browse</span></p>
                                            <p class="mt-1 text-xs text-gray-500">jpg, jpeg, png, docx, doc, pdf or csv</p>
                                        </div>
                                    </div>
                                    <div id="document-preview-container" class="mt-2 hidden">
                                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                            <div class="flex items-center flex-grow overflow-hidden">
                                                <div id="document-preview-icon" class="flex-shrink-0 mr-3"></div>
                                                <div class="overflow-hidden">
                                                    <p id="document-preview-name" class="text-sm font-medium truncate"></p>
                                                    <p id="document-preview-size" class="text-xs text-gray-500"></p>
                                                </div>
                                            </div>
                                            <button type="button" id="document-clear-btn" class="ml-2 p-1 text-gray-500 hover:text-red-500 transition-colors rounded-full hover:bg-gray-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Save Button -->
                                <div class="pt-4">
                                    <button type="submit" id="addDocumentSubmitBtn"
                                            class="w-full h-[45px] bg-[#213268] text-white rounded-lg text-base hover:bg-[#152349] transform active:scale-[0.98] transition-all duration-200">
                                        Save
                                    </button>

                                    <!-- Upload Progress Indicator (initially hidden) -->
                                    <div id="uploadProgressContainer" class="hidden mt-4">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-medium text-[#213268]">Uploading document...</span>
                                            <span id="uploadProgressText" class="text-sm font-medium text-[#213268]">0%</span>
                                </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div id="uploadProgressBar" class="bg-[#213268] h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                                        </div>
                                        <div id="uploadStatusMessage" class="mt-2 text-sm text-gray-600"></div>
                                    </div>
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

<!-- Toast Notification Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to show toast notifications
    function showToast(message, type = 'success') {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
            document.body.appendChild(toastContainer);
        }

        // Create the toast element
        const toast = document.createElement('div');

        // Set classes based on type
        if (type === 'success') {
            toast.className = 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md flex items-center';
        } else {
            toast.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md flex items-center';
        }

        // Add content
        toast.innerHTML = `
            <div class="py-1">
                <svg class="h-6 w-6 mr-4 ${type === 'success' ? 'text-green-500' : 'text-red-500'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    ${type === 'success'
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />'}
                </svg>
            </div>
            <div>
                <p class="font-bold">${type === 'success' ? 'Success!' : 'Error!'}</p>
                <p>${message}</p>
            </div>
            <button class="ml-auto text-gray-400 hover:text-gray-500" onclick="this.parentElement.remove()">×</button>
        `;

        // Add to container
        toastContainer.appendChild(toast);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => {
                toast.remove();
            }, 500);
        }, 5000);
    }

    // Make showToast available to the DocumentSystem
    window.showToast = showToast;

    // Define a global openDocumentModal function first
    window.openDocumentModal = function() {
        const modal = document.getElementById('addDocumentModal');
        const modalContent = document.getElementById('addDocumentModalContent');
        if (modal && modalContent) {
            modal.classList.remove('hidden');
            setTimeout(function() {
                modalContent.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                modalContent.classList.add('scale-100', 'opacity-100', 'translate-y-0');
            }, 10);
        }
    };

    // Set up the document form handler
    const documentForm = document.getElementById('addDocumentForm');
    if (documentForm) {
        documentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const progressContainer = document.getElementById('uploadProgressContainer');
            const progressBar = document.getElementById('uploadProgressBar');
            const progressText = document.getElementById('uploadProgressText');
            const statusMessage = document.getElementById('uploadStatusMessage');
            const formData = new FormData(this);

            // Validate required fields manually
            if (!formData.get('document_title')) {
                showToast('Document title is required', 'error');
                return;
            }

            if (!formData.get('document') || !(formData.get('document') instanceof File) || formData.get('document').size === 0) {
                showToast('Please select a valid file', 'error');
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            // Show progress container
            progressContainer.classList.remove('hidden');
            progressBar.style.width = '0%';
            progressText.textContent = '0%';
            statusMessage.textContent = 'Preparing to upload...';

            // Use XMLHttpRequest for better progress tracking
            const xhr = new XMLHttpRequest();

            // Set up upload progress handler
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percentComplete + '%';
                    progressText.textContent = percentComplete + '%';

                    if (percentComplete < 100) {
                        statusMessage.textContent = 'Uploading file to server...';
                    } else {
                        statusMessage.textContent = 'Processing upload on server...';
                    }
                }
            });

            // Handle response
            xhr.addEventListener('load', function() {
                let result;
                try {
                    result = JSON.parse(xhr.responseText);
                    if (!(xhr.status >= 200 && xhr.status < 300 && result.status)) {
                        console.error('Upload error:', result);
                    }

                    if (xhr.status >= 200 && xhr.status < 300 && result.status) {
                        // Success
                        progressBar.classList.remove('bg-[#213268]', 'bg-red-500');
                        progressBar.classList.add('bg-green-500');
                        statusMessage.textContent = 'Upload successful!';
                        showToast('Document uploaded successfully!', 'success');

                        // Close modal and reload after success
                        setTimeout(() => {
                            const modal = document.getElementById('addDocumentModal');
                            if (modal) modal.classList.add('hidden');

                            // Reset form
                            form.reset();

                            // Reload documents
                            if (typeof DocumentSystem !== 'undefined' &&
                                typeof DocumentSystem.loadDocuments === 'function') {
                                DocumentSystem.loadDocuments();
                            } else {
                                location.reload();
                            }
                        }, 1000);
                    } else {
                        // Server returned error
                        progressBar.classList.remove('bg-[#213268]');
                        progressBar.classList.add('bg-red-500');
                        statusMessage.textContent = 'Error: ' + (result.message || 'Server error');
                        statusMessage.classList.add('text-red-600');
                        showToast(result.message || 'Failed to upload document', 'error');
                    }
                } catch (e) {
                    // Response parse error
                    console.error('Error parsing server response:', e);
                    progressBar.classList.remove('bg-[#213268]');
                    progressBar.classList.add('bg-red-500');
                    statusMessage.textContent = 'Error: Could not parse server response';
                    statusMessage.classList.add('text-red-600');
                    showToast('Server error: Invalid response format', 'error');
                }

                // Reset button state
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Save';
                }, 1000);
            });

            // Handle network errors
            xhr.addEventListener('error', function() {
                progressBar.classList.remove('bg-[#213268]');
                progressBar.classList.add('bg-red-500');
                progressBar.style.width = '100%';
                statusMessage.textContent = 'Network error during file upload';
                statusMessage.classList.add('text-red-600');
                showToast('Network error during file upload', 'error');

                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save';
            });

            // Open and send request
            xhr.open('POST', this.action);
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')?.content || '');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.send(formData);
        });
    }

    // Direct approach for button click handling
    var addBtn = document.getElementById('addDocumentBtn');

    if (addBtn) {
        addBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.openDocumentModal();
        });
    }

    // Rest of your original code...
    const DocumentSystem = {
        initialized: false,
        assetId: {{ $asset['asset_id'] ?? 'null' }},
        apiBaseUrl: "{{ config('app.api_url', '') }}",
        selectors: {
            addBtn: '#addDocumentBtn',
            addForm: '#addDocumentForm',
            addModal: '#addDocumentModal',
            addModalContent: '#addDocumentModalContent',
            tableBody: '#documentTableBody',
            fileInput: '#document_file',
            previewContainer: '#document-preview-container',
            previewImage: '#document-preview-image',
            submitBtn: '#addDocumentSubmitBtn'
        },

        init() {
            if (this.initialized) return;

            this.setupModalHelpers();
            this.setupEventListeners();
            this.setupFilePreview();
            this.loadDocuments();

            this.initialized = true;
        },

        setupModalHelpers() {
            // Define openModal and closeModal functions if not already defined
            window.openModal = window.openModal || function(modal, content) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0', 'translate-y-4', 'sm:translate-y-0');
                    content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                }, 10);
            };

            window.closeModal = window.closeModal || function(modal, content) {
                content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                content.classList.add('scale-95', 'opacity-0', 'translate-y-4', 'sm:translate-y-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            };
        },

        setupEventListeners() {
            // Add Document Button - we already handled this above, so just ensure it works with the system
            const addBtn = document.querySelector(this.selectors.addBtn);
            if (addBtn) {
                // Ensure we don't duplicate click handlers
                addBtn.onclick = null;
                addBtn.addEventListener('click', () => {
                    const modal = document.querySelector(this.selectors.addModal);
                    const content = document.querySelector(this.selectors.addModalContent);
                    if (modal && content) {
                        document.querySelector(this.selectors.addForm).reset();
                        document.querySelector(this.selectors.previewContainer).classList.add('hidden');
                        openModal(modal, content);
                    }
                });
            }

            // Close Modal Buttons
            document.querySelectorAll('.close-modal').forEach(button => {
                button.addEventListener('click', () => {
                    const modalId = button.getAttribute('data-modal') || button.closest('[id$="Modal"]').id;
                    const modal = document.getElementById(modalId);
                    const content = document.getElementById(modalId + 'Content');
                    if (modal && content) {
                        closeModal(modal, content);
                    }
                });
            });

            // Event delegation for preview and delete buttons
            document.addEventListener('click', (e) => {
                // Delete button handling
                if (e.target.closest('.delete-document-btn')) {
                    e.preventDefault();
                    const deleteBtn = e.target.closest('.delete-document-btn');
                    const documentId = deleteBtn.getAttribute('data-id');
                    this.deleteDocument(documentId);
                }

                // Preview button handling
                if (e.target.closest('.preview-document-btn')) {
                    e.preventDefault();
                    const previewBtn = e.target.closest('.preview-document-btn');
                    const docId = previewBtn.getAttribute('data-id');
                    const docTitle = previewBtn.getAttribute('data-title');
                    const filePath = previewBtn.getAttribute('data-path');
                    const fileName = previewBtn.getAttribute('data-filename');
                    const fileType = previewBtn.getAttribute('data-type');
                    this.previewDocument(docId, docTitle, filePath, fileName, fileType);
                }
            });
        },

        setupFilePreview() {
            const fileInput = document.querySelector(this.selectors.fileInput);
            const previewContainer = document.querySelector(this.selectors.previewContainer);
            const previewIcon = document.getElementById('document-preview-icon');
            const previewName = document.getElementById('document-preview-name');
            const previewSize = document.getElementById('document-preview-size');
            const clearBtn = document.getElementById('document-clear-btn');

            if (!fileInput || !previewContainer) return;

            // Set up clear button functionality
            if (clearBtn) {
                clearBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Clear the file input
                    fileInput.value = '';

                    // Hide the preview container
                    previewContainer.classList.add('hidden');

                    // Reset required state
                    fileInput.required = true;
                });
            }

            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) {
                    previewContainer.classList.add('hidden');
                    return;
                }

                // Update preview container with file details
                previewName.textContent = file.name;
                previewSize.textContent = `${(file.size / 1024).toFixed(1)} KB`;

                // Set appropriate icon based on file type
                const fileExt = file.name.split('.').pop().toLowerCase();
                previewIcon.innerHTML = DocumentSystem.getFileIconByType(fileExt);

                // Show preview container
                previewContainer.classList.remove('hidden');
            });
        },

        getFileIconByType(fileExt) {
            // Helper to generate appropriate icon based on file type
            if (['pdf'].includes(fileExt)) {
                return `<svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>`;
            } else if (['doc', 'docx'].includes(fileExt)) {
                return `<svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>`;
            } else if (['xls', 'xlsx', 'csv'].includes(fileExt)) {
                return `<svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>`;
            } else {
                return `<svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>`;
            }
        },

        loadDocuments() {
            const tableBody = document.querySelector(this.selectors.tableBody);
            if (!tableBody) return;

            // Display loading indicator
            this.showLoadingIndicator(tableBody);

            // Fetch documents
            fetch(`/asset-documents/asset/${this.assetId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(result => {
                if (!result.status) {
                    console.error('Document API error:', result);
                }

                const documents = result.data || [];

                if (documents.length === 0) {
                    this.showEmptyMessage(tableBody);
                    return;
                }

                this.renderDocuments(tableBody, documents);
            })
            .catch(error => {
                console.error('Error fetching documents:', error);
                this.showErrorMessage(tableBody, 'Error loading documents. Please try again.');
            });
        },

        showLoadingIndicator(tableBody) {
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
        },

        showErrorMessage(tableBody, message) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center text-red-500">
                        Error: ${message}
                    </td>
                </tr>
            `;
        },

        showEmptyMessage(tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                        No documents found for this asset
                    </td>
                </tr>
            `;
        },

        renderDocuments(tableBody, documents) {
            let html = '';
            documents.forEach(doc => {
                const fileName = doc.file_path ? doc.file_path.split('/').pop() : 'Unknown file';
                const uploadDate = doc.upload_date ? new Date(doc.upload_date).toLocaleDateString() : '-';
                const fileExt = fileName.split('.').pop().toLowerCase();
                const fileIcon = this.getSmallFileIconByType(fileExt);
                // Update the preview URL format
                const previewUrl = `http://localhost:5000/public/documents/${fileName}`;

                html += `
                    <tr data-document-id="${doc.id}">
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${doc.document_title || '-'}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                            <div class="flex items-center">
                                ${fileIcon}
                                <span class="break-all">${fileName}</span>
                            </div>
                        </td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${doc.notes || '-'}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${uploadDate}</td>
                        <td class="p-3 border-t border-[#EEF1F4] text-center">
                            <div class="flex justify-center items-center space-x-2">
                                <button class="text-[#3D3D3D] hover:text-[#213268] preview-document-btn"
                                        data-id="${doc.id}"
                                        data-title="${doc.document_title || 'Document Preview'}"
                                        data-path="${previewUrl}"
                                        data-filename="${fileName}"
                                        data-type="${fileExt}"
                                        title="Preview">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <a href="${previewUrl}" target="_blank" class="text-[#3D3D3D] hover:text-[#213268]" title="Download">
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
        },

        getSmallFileIconByType(fileExt) {
            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                return `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>`;
            } else if (fileExt === 'pdf') {
                return `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>`;
            } else if (['doc', 'docx'].includes(fileExt)) {
                return `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>`;
            } else if (['xls', 'xlsx', 'csv'].includes(fileExt)) {
                return `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>`;
            } else {
                return `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>`;
            }
        },

        deleteDocument(docId) {
            const modal = document.getElementById('deleteDocumentModal');
            const content = document.getElementById('deleteDocumentModalContent');
            const form = document.getElementById('deleteDocumentForm');

            // Set the form action
            form.action = `/asset-documents/${docId}`;

            // Reset form event handlers
            const newForm = form.cloneNode(true);
            form.parentNode.replaceChild(newForm, form);

            // Add submit handler
            newForm.addEventListener('submit', (e) => {
                e.preventDefault();

                // Show loading state
                const submitBtn = newForm.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                `;

                // Send delete request
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
                    // Close modal
                    closeModal(modal, content);

                    if (result.status) {
                        // Reload documents on success
                        this.loadDocuments();
                        showToast('Document deleted successfully', 'success');
                    } else {
                        showToast('Error: ' + (result.message || 'Failed to delete document'), 'error');
                    }
                })
                .catch(error => {
                    closeModal(modal, content);
                    console.error('Error deleting document:', error);
                    showToast('Error deleting document. Please try again.', 'error');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
            });

            // Open delete confirmation modal
            openModal(modal, content);
        },

        previewDocument(docId, docTitle, filePath, fileName, fileType) {
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

            // Reset preview elements
            previewImage.classList.add('hidden');
            previewPdf.classList.add('hidden');
            previewFileIcon.classList.add('hidden');
            placeholder.classList.remove('hidden');

            // Set document title and download link
            titleEl.textContent = docTitle || 'Document Preview';
            // Use the direct URL for preview and download
            const fileUrl = filePath; // filePath now contains the complete URL
            downloadLink.href = fileUrl;

            // Open modal
            openModal(modal, content);

            // Set preview based on file type
            fileType = fileType.toLowerCase();

            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
                // Handle image preview
                const img = new Image();
                img.onload = () => {
                    previewImage.src = fileUrl;
                    previewImage.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                };
                img.onerror = () => this.showFileIconPreview(fileType, fileName, fileTypeIcon, previewFilename, previewFileIcon, placeholder);
                img.src = fileUrl;
            } else if (fileType === 'pdf') {
                // Handle PDF preview
                previewPdf.src = fileUrl;
                previewPdf.classList.remove('hidden');
                placeholder.classList.add('hidden');
                previewPdf.onerror = () => this.showFileIconPreview(fileType, fileName, fileTypeIcon, previewFilename, previewFileIcon, placeholder);
            } else {
                // Handle other file types
                this.showFileIconPreview(fileType, fileName, fileTypeIcon, previewFilename, previewFileIcon, placeholder);
            }
        },

        showFileIconPreview(fileType, fileName, iconContainer, filenameEl, iconWrapper, placeholder) {
            let iconHtml = '';

            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
                iconHtml = `<svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>`;
            } else if (fileType === 'pdf') {
                iconHtml = `<svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>`;
            } else if (['doc', 'docx'].includes(fileType)) {
                iconHtml = `<svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>`;
            } else if (['xls', 'xlsx', 'csv'].includes(fileType)) {
                iconHtml = `<svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>`;
            } else {
                iconHtml = `<svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>`;
            }

            iconContainer.innerHTML = iconHtml;
            filenameEl.textContent = fileName;
            iconWrapper.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
    };

    // Initialize the Document System
    DocumentSystem.init();

    // Ensure DocumentSystem is made available globally
    window.DocumentSystem = DocumentSystem;

    // Emergency fix for document form submission
    const submitBtn = document.getElementById('addDocumentSubmitBtn');
    const form = document.getElementById('addDocumentForm');

    if (submitBtn && form) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Get the CSRF token
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const formData = new FormData(form);

            // Create loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            // Use fetch to submit the form with AJAX
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (!result.status) {
                    console.error('Form submission error:', result);
                }

                if (result.status) {
                    // Success
                    showToast('Document uploaded successfully!', 'success');

                    // Close modal and reload documents
                    setTimeout(() => {
                        const modal = document.getElementById('addDocumentModal');
                        if (modal) modal.classList.add('hidden');

                        // Reset form
                        form.reset();

                        // Reload documents
                        if (typeof DocumentSystem !== 'undefined' && typeof DocumentSystem.loadDocuments === 'function') {
                            DocumentSystem.loadDocuments();
                        } else {
                            location.reload();
                        }
                    }, 1000);
                } else {
                    // Error from server
                    showToast(result.message || 'Failed to upload document', 'error');
                }
            })
            .catch(error => {
                // Network or other error
                console.error('Upload error:', error);
                showToast('Error uploading document: ' + (error.message || 'Unknown error'), 'error');
            })
            .finally(() => {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save';
            });
        });
    }
});
</script>
