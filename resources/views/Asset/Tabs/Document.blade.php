<div class="p-3 md:p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-[#213268]">DOKUMEN</h2>
        @if(hasPermission('asset:document:create'))
        <button id="addDocumentBtn" class="bg-[#213268] text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-[#162249] transition-colors flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            DOKUMEN
        </button>
        @endif
    </div>

    <!-- Loading indicator -->
    <div id="documentLoadingIndicator" class="flex justify-center items-center py-6 hidden">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#213268]"></div>
        <span class="ml-2 text-gray-600">Memuat data dokumen...</span>
    </div>

    <!-- Document Table -->
    <div class="overflow-x-auto -mx-3 sm:mx-0 rounded-md">
        <table class="w-full min-w-[500px] border-collapse">
            <thead>
                <tr>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left">Judul</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left">Nama File</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left">Catatan</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-left">Tanggal Upload</th>
                    <th class="bg-[#213268] text-white p-2 md:p-3 font-bold text-xs text-center w-16 md:w-20">Aksi</th>
                </tr>
            </thead>
            <tbody id="documentTableBody">
                <!-- Document rows will be loaded here dynamically -->
            </tbody>
        </table>
    </div>

    <!-- Add Document Modal -->
    @if(hasPermission('asset:document:create'))
    <div id="addDocumentModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[700px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                    id="addDocumentModalContent">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 pb-0">
                        <h2 class="text-xl sm:text-2xl font-semibold text-[#28356B]">TAMBAH DOKUMEN BARU</h2>
                        <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200" data-modal="addDocumentModal">
                            <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                        <div class="p-6">
                        <form id="addDocumentForm" action="{{ route('asset-documents.createAssetDocument', ['assetId' => $asset['asset_id'] ?? '']) }}" method="POST" data-no-loading enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <!-- Document Title -->
                                <div>
                                    <label for="document_title" class="block text-sm font-medium text-gray-700 mb-1">Judul Dokumen <span class="text-red-500">*</span></label>
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
                                                    <div id="file-icon-container">
                                                        <!-- Icon akan diisi oleh JavaScript -->
                                                </div>
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
                                            <p class="mt-1 text-sm text-gray-600">Seret file Anda atau <span class="text-[#213268] font-semibold">pilih file</span></p>
                                            <p class="mt-1 text-xs text-gray-500">Format yang diterima: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG</p>
                                            <p class="mt-1 text-xs text-[#213268] font-medium">Klik di mana saja di area ini untuk memilih file</p>
                                        </div>
                                        <input type="file" id="file" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                                    </div>
                                                </div>

                                <!-- Notes -->
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                    <textarea id="notes" name="notes" rows="3"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-[#28356B] focus:ring focus:ring-[#28356B] focus:ring-opacity-20"></textarea>
                                            </div>

                                <!-- Form Actions -->
                                <div class="flex justify-end space-x-3 mt-6">
                                    <button type="submit" class="w-full h-[45px] bg-[#28356B] text-white rounded-lg text-base hover:bg-[#1d2754]">
                                        <span class="flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                            Tambah Dokumen
                                        </span>
                                    </button>
                                </div>

                                <!-- Upload Progress Indicator (initially hidden) -->
                                <div id="uploadProgressContainer" class="hidden mt-4">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-[#213268]">Mengupload dokumen...</span>
                                        <span id="uploadProgressText" class="text-sm font-medium text-[#213268]">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div id="uploadProgressBar" class="bg-[#213268] h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                                        </div>
                                        <div id="uploadStatusMessage" class="mt-2 text-sm text-gray-600"></div>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
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
                <p class="font-bold">${type === 'success' ? 'Berhasil!' : 'Error!'}</p>
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
        const documentForm = document.getElementById('addDocumentForm');
        const imagePreview = document.getElementById('image-preview');
        const fileNamePreview = document.getElementById('file-name');

        if (modal && modalContent) {
            // Reset form and previews
            if (documentForm) {
                documentForm.reset();
            }

            // Hide all preview elements
            if (imagePreview) {
                imagePreview.classList.add('hidden');
            }

            if (fileNamePreview) {
                fileNamePreview.classList.add('hidden');
            }

            // Reset progress bar if visible
            const progressContainer = document.getElementById('uploadProgressContainer');
            const progressBar = document.getElementById('uploadProgressBar');
            if (progressContainer) {
                progressContainer.classList.add('hidden');
            }
            if (progressBar) {
                progressBar.style.width = '0%';
                progressBar.classList.remove('bg-red-500', 'bg-green-500');
                progressBar.classList.add('bg-[#213268]');
            }

            // Open modal
            modal.classList.remove('hidden');
            setTimeout(function() {
                modalContent.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                modalContent.classList.add('scale-100', 'opacity-100', 'translate-y-0');
            }, 10);
        }
    };

    // File upload preview functionality
    const fileInput = document.getElementById('file');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const fileNamePreview = document.getElementById('file-name');
    const fileNameText = document.getElementById('file-name-text');
    const fileIconContainer = document.getElementById('file-icon-container');
    const removeFileBtn = document.getElementById('remove-file');
    const removeImageBtn = document.getElementById('remove-image');

    // Check if a file is an image
    function isImageFile(file) {
        return file && file.type.match(/^image\/(jpeg|jpg|png|gif|webp)$/i);
    }

    // Handle file selection
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const fileName = file.name;
                const fileSize = (file.size / 1024).toFixed(1) + ' KB';
                const fileExt = fileName.split('.').pop().toLowerCase();

                // Check if it's an image file
                if (isImageFile(file)) {
                    // Show image preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                        fileNamePreview.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Show file icon based on extension
                    fileNameText.textContent = fileName;

                    // Get the appropriate icon based on file type
                    fileIconContainer.innerHTML = DocumentSystem.getFileIconByType(fileExt);

                    // Show file preview
                    fileNamePreview.classList.remove('hidden');
                    imagePreview.classList.add('hidden');
                }
            } else {
                // No file selected, hide previews
                imagePreview.classList.add('hidden');
                fileNamePreview.classList.add('hidden');
            }
        });
    }

    // Remove file buttons
    if (removeFileBtn) {
        removeFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            fileNamePreview.classList.add('hidden');
        });
    }

    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
            fileInput.value = '';
            imagePreview.classList.add('hidden');
        });
    }

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
            statusMessage.textContent = 'Mempersiapkan untuk mengupload...';

            // Use XMLHttpRequest for better progress tracking
            const xhr = new XMLHttpRequest();

            // Set up upload progress handler
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percentComplete + '%';
                    progressText.textContent = percentComplete + '%';

                    if (percentComplete < 100) {
                        statusMessage.textContent = 'Mengupload file ke server...';
                    } else {
                        statusMessage.textContent = 'Memproses upload ke server...';
                    }
                }
            });

            // Handle response
            xhr.addEventListener('load', function() {
                let result;
                try {
                    result = JSON.parse(xhr.responseText);

                    // Check if there's a message property in the response
                    if (xhr.status >= 200 && xhr.status < 300) {
                        // Successful API call
                        if (result.success === true) {
                            // Success response
                            progressBar.classList.remove('bg-[#213268]', 'bg-red-500');
                            progressBar.classList.add('bg-green-500');
                            statusMessage.textContent = 'Upload berhasil!';
                            showToast(result.message || 'Dokumen berhasil diupload!', 'success');

                            // Close modal and reload after success
                            setTimeout(() => {
                                const modal = document.getElementById('addDocumentModal');
                                if (modal) {
                                    const modalContent = document.getElementById('addDocumentModalContent');
                                    closeModal(modal, modalContent);
                                }

                                // Reset form
                                documentForm.reset();

                                // Reload documents
                                if (typeof DocumentSystem !== 'undefined' &&
                                    typeof DocumentSystem.loadDocuments === 'function') {
                                    DocumentSystem.loadDocuments();
                                } else {
                                    location.reload();
                                }
                            }, 1000);
                        } else {
                            // API returned error status
                            progressBar.classList.remove('bg-[#213268]');
                            progressBar.classList.add('bg-red-500');

                            // Extract error message - handle both errors object and string
                            let errorMessage = 'Server error';
                            if (result.errors) {
                                if (typeof result.errors === 'string') {
                                    errorMessage = result.errors;
                                } else if (typeof result.errors === 'object') {
                                    // Get first error message from the object
                                    const firstErrorKey = Object.keys(result.errors)[0];
                                    if (firstErrorKey) {
                                        const firstError = result.errors[firstErrorKey];
                                        errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                                    }
                                }
                            }

                            statusMessage.textContent = 'Error: ' + errorMessage;
                            statusMessage.classList.add('text-red-600');
                            showToast(errorMessage || 'Gagal mengupload dokumen', 'error');
                        }
                    } else {
                        // HTTP error
                        progressBar.classList.remove('bg-[#213268]');
                        progressBar.classList.add('bg-red-500');

                        // Try to get error message from response if available
                        let errorMessage = 'Server error: ' + xhr.status;
                        if (result.errors) {
                            if (typeof result.errors === 'string') {
                                errorMessage = result.errors;
                            } else if (typeof result.errors === 'object') {
                                // Get first error message from the object
                                const firstErrorKey = Object.keys(result.errors)[0];
                                if (firstErrorKey) {
                                    const firstError = result.errors[firstErrorKey];
                                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                                }
                            }
                        }

                        statusMessage.textContent = 'Error: ' + errorMessage;
                        statusMessage.classList.add('text-red-600');
                        showToast(errorMessage, 'error');
                    }
                } catch (e) {
                    // Response parse error
                    console.error('Error parsing server response:', e);
                    progressBar.classList.remove('bg-[#213268]');
                    progressBar.classList.add('bg-red-500');
                    statusMessage.textContent = 'Error: Tidak dapat memparsing respons server';
                    statusMessage.classList.add('text-red-600');
                    showToast('Server error: Format respons tidak valid', 'error');
                }

                // Reset button state
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Simpan';
                }, 1000);
            });

            // Handle network errors
            xhr.addEventListener('error', function() {
                progressBar.classList.remove('bg-[#213268]');
                progressBar.classList.add('bg-red-500');
                progressBar.style.width = '100%';
                statusMessage.textContent = 'Error jaringan selama penguploadan file';
                statusMessage.classList.add('text-red-600');
                showToast('Error jaringan selama penguploadan file', 'error');

                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Simpan';
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

    // Add JavaScript to hide elements based on permissions
    document.addEventListener('DOMContentLoaded', function() {
        // Hide elements if user doesn't have create permission
        if (!{{ hasPermission('asset:document:create') ? 'true' : 'false' }}) {
            const addButtons = document.querySelectorAll('#addDocumentBtn');
            addButtons.forEach(btn => {
                if (btn) {
                    btn.style.display = 'none';
                }
            });
        }
    });

    // Rest of your original code...
    const DocumentSystem = {
        initialized: false,
        assetId: {{ $asset['asset_id'] ?? 'null' }},
        apiBaseUrl: "{{ config('app.api_url', '') }}",
        // Add permission flags
        hasCreatePermission: {{ hasPermission('asset:document:create') ? 'true' : 'false' }},
        hasDownloadPermission: {{ hasPermission('asset:document:download') ? 'true' : 'false' }}, // ID: 123 - Mengunduh dokumen aset
        selectors: {
            addBtn: '#addDocumentBtn',
            addForm: '#addDocumentForm',
            addModal: '#addDocumentModal',
            addModalContent: '#addDocumentModalContent',
            tableBody: '#documentTableBody',
            fileInput: '#file',
            previewContainer: '#image-preview',
            previewImage: '#preview-img',
            submitBtn: '#addDocumentSubmitBtn'
        },

        init() {
            if (this.initialized) return;

            this.setupModalHelpers();
            this.setupEventListeners();
            this.setupFilePreview();

            // Initialize the main loading indicator as hidden on start
            const loadingIndicator = document.getElementById('documentLoadingIndicator');
            if (loadingIndicator) {
                loadingIndicator.classList.add('hidden');
            }

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
            if (this.hasCreatePermission && addBtn) {
                // Ensure we don't duplicate click handlers
                addBtn.onclick = null;
                addBtn.addEventListener('click', () => {
                    window.openDocumentModal();
                });
            }

            // Close Modal Buttons
            document.querySelectorAll('.close-modal').forEach(button => {
                button.addEventListener('click', () => {
                    const modalId = button.getAttribute('data-modal') || button.closest('[id$="Modal"]').id;
                    const modal = document.getElementById(modalId);
                    const content = document.getElementById(modalId + 'Content');

                    // Reset form if it's the document modal
                    if (modalId === 'addDocumentModal') {
                        const form = document.getElementById('addDocumentForm');
                        if (form) form.reset();

                        // Hide previews
                        const imagePreview = document.getElementById('image-preview');
                        const fileNamePreview = document.getElementById('file-name');
                        if (imagePreview) imagePreview.classList.add('hidden');
                        if (fileNamePreview) fileNamePreview.classList.add('hidden');

                        // Reset progress elements
                        const progressContainer = document.getElementById('uploadProgressContainer');
                        if (progressContainer) progressContainer.classList.add('hidden');
                    }

                    if (modal && content) {
                        closeModal(modal, content);
                    }
                });
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
                previewIcon.innerHTML = this.getFileIconByType(fileExt);

                // Show preview container
                previewContainer.classList.remove('hidden');
            });
        },

        getFileIconByType(fileExt) {
            // Get appropriate icon based on file extension
            if (['pdf'].includes(fileExt)) {
                return this.getPdfIcon();
            } else if (['doc', 'docx'].includes(fileExt)) {
                return this.getWordIcon();
            } else if (['xls', 'xlsx', 'csv'].includes(fileExt)) {
                return this.getExcelIcon();
            } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt)) {
                return this.getImageIcon();
            } else {
                return this.getDocumentIcon();
            }
        },

        // Document icon SVG templates
        getDocumentIcon() {
            return `<svg class="w-8 h-8 text-gray-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>`;
        },

        getPdfIcon() {
            return `<svg class="w-8 h-8 text-red-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                <text x="12" y="16" font-family="Arial" font-size="6" fill="currentColor" text-anchor="middle">PDF</text>
            </svg>`;
        },

        getWordIcon() {
            return `<svg class="w-8 h-8 text-blue-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                <text x="12" y="16" font-family="Arial" font-size="5" fill="currentColor" text-anchor="middle">DOC</text>
            </svg>`;
        },

        getExcelIcon() {
            return `<svg class="w-8 h-8 text-green-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                <text x="12" y="16" font-family="Arial" font-size="5" fill="currentColor" text-anchor="middle">XLS</text>
            </svg>`;
        },

        getImageIcon() {
            return `<svg class="w-8 h-8 text-purple-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                <text x="12" y="16" font-family="Arial" font-size="5" fill="currentColor" text-anchor="middle">IMG</text>
            </svg>`;
        },

        loadDocuments() {
            const tableBody = document.querySelector(this.selectors.tableBody);
            if (!tableBody) return;

            // Display loading indicator
            this.showLoadingIndicator(tableBody);

            // Fetch documents using the new endpoint
            fetch(`/asset-documents/asset/${this.assetId}/all-documents`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(result => {
                console.log('Document API response:', result);

                if (!result.success) {
                    console.error('Document API error:', result);

                    // Extract error message from the response
                    let errorMessage = 'Error loading documents';
                    if (result.errors) {
                        if (typeof result.errors === 'string') {
                            errorMessage = result.errors;
                        } else if (typeof result.errors === 'object') {
                            // Get first error message from the object
                            const firstErrorKey = Object.keys(result.errors)[0];
                            if (firstErrorKey) {
                                const firstError = result.errors[firstErrorKey];
                                errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                            }
                        }
                    }

                    this.showErrorMessage(tableBody, errorMessage);
                    return;
                }

                // Get all document types from the response
                const regularDocuments = result.data.documents || [];
                const calibrationDocuments = result.data.calibrationDocuments || [];
                const maintenanceDocuments = result.data.maintenanceDocuments || [];

                // Combine all document types with a source identifier
                const allDocuments = [
                    ...regularDocuments.map(doc => ({...doc, source: 'regular'})),
                    ...calibrationDocuments.map(doc => ({...doc, source: 'calibration'})),
                    ...maintenanceDocuments.map(doc => ({...doc, source: 'maintenance'}))
                ];

                if (allDocuments.length === 0) {
                    this.showEmptyMessage(tableBody);
                    return;
                }

                this.renderDocuments(tableBody, allDocuments);
            })
            .catch(error => {
                console.error('Error fetching documents:', error);
                this.showErrorMessage(tableBody, 'Error loading documents. Please try again.');
            });
        },

        showLoadingIndicator(tableBody) {
            // Show the main loading indicator
            document.getElementById('documentLoadingIndicator').classList.remove('hidden');

            // Clear the table body content
            tableBody.innerHTML = '';
        },

        hideLoadingIndicator() {
            document.getElementById('documentLoadingIndicator').classList.add('hidden');
        },

        showErrorMessage(tableBody, message) {
            document.getElementById('documentLoadingIndicator').classList.add('hidden');

            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center text-red-500">
                        Error: ${message}
                    </td>
                </tr>
            `;
        },

        showEmptyMessage(tableBody) {
            document.getElementById('documentLoadingIndicator').classList.add('hidden');

            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="p-3 text-xs border-t border-[#EEF1F4] text-center">
                        Tidak ada dokumen ditemukan untuk aset ini
                    </td>
                </tr>
            `;
        },

        renderDocuments(tableBody, documents) {
            this.hideLoadingIndicator();
            let html = '';
            documents.forEach(doc => {
                const fileName = doc.file_path ? doc.file_path.split('/').pop() : 'Unknown file';
                const uploadDate = doc.upload_date ? new Date(doc.upload_date).toLocaleDateString() : '-';
                const fileExt = fileName.split('.').pop().toLowerCase();

                // Determine the proper URL based on file type and source
                let previewUrl;
                if (doc.full_path) {
                    // Use full path if provided
                    previewUrl = doc.full_path;
                } else {
                    // Otherwise construct URL based on file type
                    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                        // Use image URL format with standard naming pattern
                        previewUrl = `http://localhost:5000/public/images/${fileName}`;
                        // If the filename doesn't contain proper image identifier pattern, use the example format
                        if (!fileName.includes('image-')) {
                            previewUrl = `http://localhost:5000/public/images/image-${Date.now()}-${doc.id || 'default'}.${fileExt}`;
                        }
                    } else {
                        // Use document URL format with standard naming pattern
                        previewUrl = `http://localhost:5000/public/documents/${fileName}`;
                        // If the filename doesn't contain proper document identifier pattern, use the example format
                        if (!fileName.includes('asset-doc-')) {
                            previewUrl = `http://localhost:5000/public/documents/asset-doc-${Date.now()}-${doc.id || 'default'}.${fileExt}`;
                        }
                    }
                }

                // Add badge for document type
                let badgeHtml = '';
                if (doc.source === 'calibration') {
                    badgeHtml = '<span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Kalibrasi</span>';
                } else if (doc.source === 'maintenance') {
                    badgeHtml = '<span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Maintenance</span>';
                }

                html += `
                    <tr data-document-id="${doc.id}" data-document-source="${doc.source}">
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                            <div class="flex items-center">
                                <span>${doc.document_title || '-'}</span>
                                ${badgeHtml}
                            </div>
                        </td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">
                            <span class="break-all">${fileName}</span>
                        </td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${doc.notes || '-'}</td>
                        <td class="p-3 text-xs border-t border-[#EEF1F4]">${uploadDate}</td>
                        <td class="p-3 border-t border-[#EEF1F4] text-center">
                            <div class="flex justify-center items-center space-x-2">
                                ${this.hasDownloadPermission ? `
                                <a href="${previewUrl}" target="_blank" class="text-[#3D3D3D] bg-gray-100 hover:bg-[#213268] hover:text-white p-1.5 rounded-md transition-colors flex items-center" title="Unduh File">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;
        },
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
                if (!result.success) {
                    console.error('Form submission error:', result);
                }

                if (result.success) {
                    // Success
                    showToast('Dokumen berhasil diupload!', 'success');

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
                    let errorMessage = 'Gagal mengupload dokumen';
                    if (result.errors) {
                        if (typeof result.errors === 'string') {
                            errorMessage = result.errors;
                        } else if (typeof result.errors === 'object') {
                            // Get first error message from the object
                            const firstErrorKey = Object.keys(result.errors)[0];
                            if (firstErrorKey) {
                                const firstError = result.errors[firstErrorKey];
                                errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                            }
                        }
                    }
                    showToast(errorMessage, 'error');
                }
            })
            .catch(error => {
                // Network or other error
                console.error('Upload error:', error);
                showToast('Error mengupload dokumen: ' + (error.message || 'Error tidak diketahui'), 'error');
            })
            .finally(() => {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Simpan';
            });
        });
    }
});
</script>