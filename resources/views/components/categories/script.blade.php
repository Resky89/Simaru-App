        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if(!hasPermission('asset-subcategory:create'))
                    const addButtons = document.querySelectorAll('#addSubCategoryBtn');
                    addButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                    @if(!hasPermission('asset-subcategory:import'))
                        const importButtons = document.querySelectorAll('#importCategoryBtn, #preview-btn');
                        importButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                    @if(!hasPermission('asset-subcategory:edit'))
                        const editButtons = document.querySelectorAll('.edit-subcategory-btn');
                        editButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                    @if(!hasPermission('asset-subcategory:delete'))
                        const deleteButtons = document.querySelectorAll('.delete-subcategory-btn');
                        deleteButtons.forEach(btn => {
                            if (btn) {
                                btn.style.display = 'none';
                            }
                        });
                    @endif

                // Column header sorting has been moved to event delegation

                const addSubCategoryBtn = document.getElementById('addSubCategoryBtn');
                const addSubCategoryModal = document.getElementById('addSubCategoryModal');
                const editSubCategoryModal = document.getElementById('editSubCategoryModal');
                const deleteSubCategoryModal = document.getElementById('deleteSubCategoryModal');
                const importCategoryModal = document.getElementById('importCategoryModal');
                const closeButtons = document.querySelectorAll('.close-modal');
                const createSubCategoryForm = document.getElementById('createSubCategoryForm');
                const editSubCategoryForm = document.getElementById('editSubCategoryForm');
                const deleteSubCategoryForm = document.getElementById('deleteSubCategoryForm');

                function preventMultipleSubmits(form, buttonSelector) {
                    if (!form) return;

                    form.addEventListener('submit', function (e) {
                        if (this.checkValidity()) {
                            const submitBtn = this.querySelector(buttonSelector);
                            if (submitBtn && !submitBtn.disabled) {
                                const originalText = submitBtn.innerHTML;

                                submitBtn.disabled = true;
                                submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = `
                                    <div class="flex items-center justify-center">
                                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                        <span>Memproses...</span>
                                    </div>
                                `;

                                setTimeout(() => {
                                    if (submitBtn) {
                                        submitBtn.disabled = false;
                                        submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                        submitBtn.innerHTML = originalText;
                                    }
                                }, 10000);
                            }
                        }
                    });
                }

                @if(session('success'))
                    showToast("{{ session('success') }}", 'success');
                @endif

                @if(session('error'))
                    showToast("{{ session('error') }}", 'error');
                @endif

                // Removed window.changePerPage in favor of global changeItemPerPage

            if (addSubCategoryBtn) {
                    addSubCategoryBtn.addEventListener('click', () => {
                        openModal(addSubCategoryModal, addSubCategoryModal.querySelector('[id$="ModalContent"]'));
                    });
                }

                const importCategoryBtn = document.getElementById('importCategoryBtn');
                if (importCategoryBtn) {
                    importCategoryBtn.addEventListener('click', () => {
                        openModal(importCategoryModal, importCategoryModal.querySelector('[id$="ModalContent"]'));
                    });
                }

                window.refreshTable = function(targetUrl = null) {
                    const fetchUrl = new URL(targetUrl || window.location.href);
                    
                    if (targetUrl) {
                        window.history.pushState({path: fetchUrl.toString()}, '', fetchUrl.toString());
                    }

                    fetchUrl.searchParams.set('_t', new Date().getTime());

                    fetch(fetchUrl.toString())
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTableContainer = doc.getElementById('categoryTableContainer');
                        const currentTableContainer = document.getElementById('categoryTableContainer');
                        if (newTableContainer && currentTableContainer) {
                            currentTableContainer.innerHTML = newTableContainer.innerHTML;
                        }
                    })
                    .catch(err => console.error('Error refreshing table:', err));
                };

                window.addEventListener('popstate', function(event) {
                    refreshTable(window.location.href);
                });

                document.addEventListener('click', function(e) {
                    // Sort Column Delegation
                    const sortBtn = e.target.closest('#sortByName');
                    if (sortBtn) {
                        const url = new URL(window.location.href);
                        let currentSort = url.searchParams.get('sort');

                        let newSort = 'name_asc';
                        if (currentSort === 'name_asc') {
                            newSort = 'name_desc';
                        }

                        url.searchParams.set('sort', newSort);
                        url.searchParams.set('page', 1);
                        refreshTable(url.toString());
                    }

                    // Edit Button Delegation
                    const editBtn = e.target.closest('.edit-subcategory-btn');
                    if (editBtn) {
                        const subcategoryId = editBtn.getAttribute('data-subcategory-id');
                        const assetType = editBtn.getAttribute('data-asset-type');
                        const subcategoryName = editBtn.getAttribute('data-subcategory-name');
                        const description = editBtn.getAttribute('data-description');

                        const formAction = "{{ url('categories/update') }}/" + subcategoryId;
                        const editForm = document.getElementById('editSubCategoryForm');
                        if (editForm) {
                            editForm.action = formAction;
                            console.log('Edit form action set to:', formAction);
                        }

                        const idField = document.getElementById('editSubCategoryId');
                        const typeField = document.getElementById('editAssetType');
                        const nameField = document.getElementById('editSubCategoryName');
                        const descField = document.getElementById('editDescription');

                        if (idField) idField.value = subcategoryId;
                        if (typeField) typeField.value = assetType;
                        if (nameField) nameField.value = subcategoryName;
                        if (descField) descField.value = description || '';

                        const editSubCategoryModal = document.getElementById('editSubCategoryModal');
                        if (editSubCategoryModal) {
                            const modalContent = editSubCategoryModal.querySelector('[id$="ModalContent"]');
                            if (modalContent) {
                                openModal(editSubCategoryModal, modalContent);
                            }
                        }
                    }

                    // Delete Button Delegation
                    const deleteBtn = e.target.closest('.delete-subcategory-btn');
                    if (deleteBtn) {
                        const subcategoryId = deleteBtn.getAttribute('data-subcategory-id');

                        const formAction = "{{ url('categories/delete') }}/" + subcategoryId;
                        document.getElementById('deleteSubCategoryForm').action = formAction;
                        console.log('Delete form action set to:', formAction);

                        document.getElementById('deleteSubCategoryId').value = subcategoryId;

                        const deleteSubCategoryModal = document.getElementById('deleteSubCategoryModal');
                        if (deleteSubCategoryModal) {
                            openModal(deleteSubCategoryModal, deleteSubCategoryModal.querySelector('[id$="ModalContent"]'));
                        }
                    }
                });

                function clearModalForms(modal) {
                    if (!modal) return;

                    const forms = modal.querySelectorAll('form');

                    forms.forEach(form => {
                        form.reset();
                        // Use the new clearFieldErrors function
                        clearFieldErrors(form);
                    });

                    if (modal.id === 'importCategoryModal') {
                        const fileInput = modal.querySelector('#category_excel_file');
                        if (fileInput) fileInput.value = '';

                        const fileNameContainer = modal.querySelector('#category-excel-file-name');
                        if (fileNameContainer) fileNameContainer.classList.add('hidden');

                        const previewBtn = modal.querySelector('#category-preview-btn');
                        if (previewBtn) previewBtn.disabled = true;

                        const errorDiv = modal.querySelector('#category-excel-error');
                        if (errorDiv) errorDiv.classList.add('hidden');

                        document.getElementById('import-category-step-1')?.classList.remove('hidden');
                        document.getElementById('import-category-step-2')?.classList.add('hidden');
                    }
                }

                closeButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const modal = button.closest('[id$="Modal"]');
                        const content = modal.querySelector('[id$="ModalContent"]');
                        closeModal(modal, content);
                        clearModalForms(modal);
                    });
                });

                [addSubCategoryModal, editSubCategoryModal, deleteSubCategoryModal, importCategoryModal].forEach(modal => {
                    if (modal) {
                        modal.addEventListener('click', function (e) {
                            if (e.target === this.querySelector('.fixed.inset-0.z-50.overflow-y-auto') ||
                                e.target === this.querySelector('.fixed.inset-0.bg-black.bg-opacity-50')) {
                                const content = this.querySelector('[id$="ModalContent"]');
                                closeModal(this, content);
                                clearModalForms(this);
                            }
                        });
                    }
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        [addSubCategoryModal, editSubCategoryModal, deleteSubCategoryModal, importCategoryModal].forEach(modal => {
                            if (modal && !modal.classList.contains('hidden')) {
                                const content = modal.querySelector('[id$="ModalContent"]');
                                closeModal(modal, content);
                                clearModalForms(modal);
                            }
                        });
                    }
                });

                if (createSubCategoryForm) {
                    createSubCategoryForm.addEventListener('submit', function (event) {
                        event.preventDefault();

                        const assetTypeInput = document.getElementById('add_asset_type');
                        const subcategoryNameInput = document.getElementById('add_subcategory_name');
                        const descriptionInput = document.getElementById('add_description');

                        const isAssetTypeValid = validateField(assetTypeInput);
                        const isSubcategoryNameValid = validateField(subcategoryNameInput);

                        if (descriptionInput && descriptionInput.value === null) {
                            descriptionInput.value = '';
                        }

                        if (!isAssetTypeValid || !isSubcategoryNameValid) {
                            showToast('Silakan isi semua field yang diperlukan', 'error');
                            return;
                        }

                        const formData = new FormData(this);

                        const submitBtn = this.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            const originalText = submitBtn.innerHTML;
                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = `
                                <div class="flex items-center justify-center">
                                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                    <span>Memproses...</span>
                                </div>
                            `;

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
                                    const modal = document.getElementById('addSubCategoryModal');
                                    closeModal(modal, modal.querySelector('[id$="ModalContent"]'));
                                    clearModalForms(modal);
                                    showToast(data.message || 'Kategori berhasil ditambahkan', 'success');

                                    refreshTable();
                                } else {
                                    // Show field-specific errors
                                    if (data.errors) {
                                        showFieldErrors(this, data.errors);

                                        // Extract only error messages for toast (without field paths)
                                        let errorMessages = [];
                                        if (Array.isArray(data.errors)) {
                                            errorMessages = data.errors.map(error => error.message).filter(msg => msg);
                                        } else if (typeof data.errors === 'object') {
                                            Object.values(data.errors).forEach(value => {
                                                if (Array.isArray(value)) {
                                                    errorMessages.push(...value);
                                                } else {
                                                    errorMessages.push(value);
                                                }
                                            });
                                        }

                                        if (errorMessages.length > 0) {
                                            const errorMessage = errorMessages.join('; ');
                                            showToast(errorMessage, 'error');
                                } else {
                                    showToast(data.message || 'Terjadi kesalahan saat menyimpan kategori', 'error');
                                        }
                                    } else {
                                        // Show general error message
                                        const errorMessage = data.message || 'Terjadi kesalahan saat menyimpan kategori';
                                        showToast(errorMessage, 'error');
                                    }
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
                    });
                }

                if (editSubCategoryForm) {
                    editSubCategoryForm.addEventListener('submit', function (event) {
                        event.preventDefault();

                        const assetTypeInput = document.getElementById('editAssetType');
                        const subcategoryNameInput = document.getElementById('editSubCategoryName');
                        const descriptionInput = document.getElementById('editDescription');

                        const isAssetTypeValid = validateField(assetTypeInput);
                        const isSubcategoryNameValid = validateField(subcategoryNameInput);

                        if (descriptionInput && descriptionInput.value === null) {
                            descriptionInput.value = '';
                        }

                        if (!isAssetTypeValid || !isSubcategoryNameValid) {
                            showToast('Silakan isi semua field yang diperlukan', 'error');
                            return;
                        }

                        const formData = new FormData(this);
                        formData.append('_method', 'PUT');

                        const submitBtn = this.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            const originalText = submitBtn.innerHTML;
                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = `
                                <div class="flex items-center justify-center">
                                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                    <span>Memproses...</span>
                                </div>
                            `;

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
                                    const modal = document.getElementById('editSubCategoryModal');
                                    closeModal(modal, modal.querySelector('[id$="ModalContent"]'));
                                    clearModalForms(modal);
                                    showToast(data.message || 'Kategori berhasil diperbarui', 'success');

                                    refreshTable();
                                                                } else {
                                    // Show field-specific errors
                                    if (data.errors) {
                                        showFieldErrors(this, data.errors);

                                        // Extract only error messages for toast (without field paths)
                                        let errorMessages = [];
                                        if (Array.isArray(data.errors)) {
                                            errorMessages = data.errors.map(error => error.message).filter(msg => msg);
                                        } else if (typeof data.errors === 'object') {
                                            Object.values(data.errors).forEach(value => {
                                                if (Array.isArray(value)) {
                                                    errorMessages.push(...value);
                                                } else {
                                                    errorMessages.push(value);
                                                }
                                            });
                                        }

                                        if (errorMessages.length > 0) {
                                            const errorMessage = errorMessages.join('; ');
                                            showToast(errorMessage, 'error');
                                } else {
                                    showToast(data.message || 'Terjadi kesalahan saat memperbarui kategori', 'error');
                                        }
                                    } else {
                                        // Show general error message
                                        const errorMessage = data.message || 'Terjadi kesalahan saat memperbarui kategori';
                                        showToast(errorMessage, 'error');
                                    }
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
                    });
                }

                function validateField(field) {
                    if (!field) return true;

                    let errorElement = field.closest('.space-y-2')?.querySelector('.error-message');

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

                // Function to clear all field errors
                function clearFieldErrors(form) {
                    if (!form) return;

                    const fields = form.querySelectorAll('input, select, textarea');
                    fields.forEach(field => {
                        field.classList.remove('border-red-500');
                        const errorElement = field.closest('.space-y-2')?.querySelector('.error-message');
                        if (errorElement) {
                            errorElement.classList.add('hidden');
                            errorElement.textContent = '';
                        }
                    });
                }

                // Function to show field errors from server response
                function showFieldErrors(form, errors) {
                    if (!form || !errors) return;

                    // Clear existing errors first
                    clearFieldErrors(form);

                    // Handle different error formats
                    let errorList = [];

                    if (Array.isArray(errors)) {
                        errorList = errors;
                    } else if (typeof errors === 'object') {
                        // Convert object errors to array format
                        Object.entries(errors).forEach(([key, value]) => {
                            if (Array.isArray(value)) {
                                value.forEach(msg => {
                                    errorList.push({ path: key, message: msg });
                                });
                            } else {
                                errorList.push({ path: key, message: value });
                            }
                        });
                    }

                    // Apply errors to fields
                    errorList.forEach(error => {
                        let fieldName = error.path || error.field;
                        let message = error.message;

                        if (!fieldName || !message) return;

                        // Find the field by name or id
                        let field = form.querySelector(`[name="${fieldName}"]`) ||
                                   form.querySelector(`#${fieldName}`) ||
                                   form.querySelector(`#add_${fieldName}`) ||
                                   form.querySelector(`#edit${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)}`);

                        if (field) {
                            // Add error styling
                            field.classList.add('border-red-500');

                            // Find and update error message element
                            const errorElement = field.closest('.space-y-2')?.querySelector('.error-message');
                            if (errorElement) {
                                errorElement.textContent = message;
                                errorElement.classList.remove('hidden');
                            }
                        }
                    });
                }

                // Function to clear field error on user interaction
                function clearFieldError(field) {
                    if (!field) return;
                    field.classList.remove('border-red-500');
                    const errorElement = field.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.classList.add('hidden');
                        errorElement.textContent = '';
                    }
                }

                // Add error clearing for form fields
                const formFields = [
                    { id: 'add_asset_type', event: 'change' },
                    { id: 'add_subcategory_name', event: 'input' },
                    { id: 'add_description', event: 'input' },
                    { id: 'editAssetType', event: 'change' },
                    { id: 'editSubCategoryName', event: 'input' },
                    { id: 'editDescription', event: 'input' }
                ];

                formFields.forEach(fieldConfig => {
                    const field = document.getElementById(fieldConfig.id);
                    if (field) {
                        field.addEventListener(fieldConfig.event, function () {
                            clearFieldError(this);
                        });
                    }
                });

                const searchInput = document.getElementById('searchInput');
                const assetTypeFilter = document.getElementById('assetTypeFilter');
                const sortOrder = document.getElementById('sortOrder');

                function applyFilters() {
                    const searchValue = searchInput?.value.trim() || '';
                    const typeValue = assetTypeFilter?.value || '';
                    const sortValue = sortOrder?.value || '';

                    const url = new URL(window.location.href);

                    ['search', 'asset_type', 'sort', 'page'].forEach(param => {
                        url.searchParams.delete(param);
                    });

                    if (searchValue) url.searchParams.set('search', searchValue);
                    if (typeValue) url.searchParams.set('asset_type', typeValue);
                    if (sortValue) url.searchParams.set('sort', sortValue);

                    url.searchParams.set('page', 1);

                    refreshTable(url.toString());
                }

                let searchTimeout;
                searchInput?.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(applyFilters, 500);
                });

                assetTypeFilter?.addEventListener('change', applyFilters);
                sortOrder?.addEventListener('change', applyFilters);

                const urlParams = new URLSearchParams(window.location.search);
                if (searchInput) searchInput.value = urlParams.get('search') || '';
                if (assetTypeFilter) {
                    const typeValue = urlParams.get('asset_type');
                    if (typeValue) {
                        assetTypeFilter.value = typeValue;
                    }
                }
                if (sortOrder) {
                    const sortValue = urlParams.get('sort');
                    if (sortValue) {
                        sortOrder.value = sortValue;
                    }
                }

            ;

                // (Deleted changePerPage)

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

                        /* Styling for error messages with HTML content */
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

                function debounce(func, wait, immediate) {
                    let timeout;
                    return function () {
                        const context = this, args = arguments;
                        const later = function () {
                            timeout = null;
                            if (!immediate) func.apply(context, args);
                        };
                        const callNow = immediate && !timeout;
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                        if (callNow) func.apply(context, args);
                    };
                }

                const categoryExcelFile = document.getElementById('category_excel_file');
                const categoryFileNameContainer = document.getElementById('category-excel-file-name');
                const categoryFileNameText = document.getElementById('category-file-name-text');
                const removeCategoryExcel = document.getElementById('remove-category-excel');
                const categoryPreviewBtn = document.getElementById('category-preview-btn');
                const categoryExcelError = document.getElementById('category-excel-error');
                const categoryExcelLoading = document.getElementById('category-excel-loading');

                if (categoryExcelFile) {
                    categoryExcelFile.addEventListener('change', function (e) {
                        if (categoryExcelError) categoryExcelError.classList.add('hidden');

                        if (this.files && this.files[0]) {
                            const file = this.files[0];
                            const fileExt = file.name.split('.').pop().toLowerCase();

                            if (!['xlsx', 'xls', 'csv'].includes(fileExt)) {
                                if (categoryExcelError) {
                                    categoryExcelError.textContent = 'Tipe file tidak valid. Silakan unggah file Excel (.xlsx, .xls) atau CSV.';
                                    categoryExcelError.classList.remove('hidden');
                                }
                                this.value = '';
                                if (categoryFileNameContainer) categoryFileNameContainer.classList.add('hidden');
                                if (categoryPreviewBtn) categoryPreviewBtn.disabled = true;
                                return;
                            }

                            if (categoryFileNameText) categoryFileNameText.textContent = file.name;
                            if (categoryFileNameContainer) categoryFileNameContainer.classList.remove('hidden');
                            if (categoryPreviewBtn) categoryPreviewBtn.disabled = false;
                        } else {
                            if (categoryFileNameContainer) categoryFileNameContainer.classList.add('hidden');
                            if (categoryPreviewBtn) categoryPreviewBtn.disabled = true;
                        }
                    });
                }

                if (removeCategoryExcel) {
                    removeCategoryExcel.addEventListener('click', function () {
                        if (categoryExcelFile) categoryExcelFile.value = '';
                        if (categoryFileNameContainer) categoryFileNameContainer.classList.add('hidden');
                        if (categoryPreviewBtn) categoryPreviewBtn.disabled = true;
                        if (categoryExcelError) categoryExcelError.classList.add('hidden');
                    });
                }

                if (categoryPreviewBtn) {
                    categoryPreviewBtn.addEventListener('click', function () {
                        if (!categoryExcelFile || !categoryExcelFile.files || !categoryExcelFile.files[0]) {
                            if (categoryExcelError) {
                                categoryExcelError.textContent = 'Silakan pilih file terlebih dahulu.';
                                categoryExcelError.classList.remove('hidden');
                            }
                            return;
                        }

                        const file = categoryExcelFile.files[0];

                        if (categoryExcelLoading) categoryExcelLoading.classList.remove('hidden');
                        if (categoryExcelError) categoryExcelError.classList.add('hidden');

                        const reader = new FileReader();

                        reader.onload = function (e) {
                            try {
                                const data = new Uint8Array(e.target.result);
                                const workbook = XLSX.read(data, { type: 'array' });

                                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];

                                const rows = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

                                if (rows.length < 2) {
                                    throw new Error('File tidak memiliki data atau header yang hilang.');
                                }

                                processCategoryExcelData(rows);

                                if (categoryExcelLoading) categoryExcelLoading.classList.add('hidden');

                                document.getElementById('import-category-step-1').classList.add('hidden');
                                document.getElementById('import-category-step-2').classList.remove('hidden');
                            } catch (error) {
                                console.error('Excel parsing error:', error);
                                if (categoryExcelLoading) categoryExcelLoading.classList.add('hidden');
                                if (categoryExcelError) {
                                    categoryExcelError.textContent = 'Gagal memproses file: ' + error.message;
                                    categoryExcelError.classList.remove('hidden');
                                }
                            }
                        };

                        reader.onerror = function () {
                            console.error('FileReader error:', reader.error);
                            if (categoryExcelLoading) categoryExcelLoading.classList.add('hidden');
                            if (categoryExcelError) {
                                categoryExcelError.textContent = 'Gagal membaca file. Silakan coba file lainnya.';
                                categoryExcelError.classList.remove('hidden');
                            }
                        };

                        reader.readAsArrayBuffer(file);
                    });
                }

                const categoryBackBtn = document.getElementById('category-back-to-upload-btn');
                if (categoryBackBtn) {
                    categoryBackBtn.addEventListener('click', function () {
                        document.getElementById('import-category-step-2').classList.add('hidden');
                        document.getElementById('import-category-step-1').classList.remove('hidden');
                    });
                }

                function processCategoryExcelData(data) {
                    const headers = data[0];
                    const rows = data.slice(1).filter(row => row.length > 0 && row.some(cell => cell !== null && cell !== ''));

                    const headerMap = {};
                    headers.forEach((header, index) => {
                        if (header) {
                            const normalizedHeader = String(header).toLowerCase().trim()
                                .replace(/\s+/g, '_')
                                .replace(/[^a-z0-9_]/g, '');
                            headerMap[normalizedHeader] = index;
                        }
                    });

                    const previewData = [];
                    const warnings = [];

                    rows.forEach((row, rowIndex) => {
                        const item = {};

                        const getValue = (possibleNames) => {
                            for (const name of possibleNames) {
                                const normalizedName = name.toLowerCase().trim()
                                    .replace(/\s+/g, '_')
                                    .replace(/[^a-z0-9_]/g, '');

                                if (headerMap[normalizedName] !== undefined) {
                                    return row[headerMap[normalizedName]];
                                }
                            }
                            return null;
                        };

                        item.asset_type = getValue(['asset_type', 'asset type', 'assettype', 'type', 'category', 'tipe aset', 'tipe_aset']);
                        item.subcategory_name = getValue(['subcategory_name', 'subcategory name', 'sub category name', 'sub_category_name', 'name', 'nama kategori', 'nama_kategori']);
                        item.description = getValue(['description', 'desc', 'notes', 'deskripsi']);

                        if (item.asset_type) {
                            const typeStr = item.asset_type.toString().toLowerCase().trim();
                            if (typeStr === 'medical' || typeStr === 'med') {
                                item.asset_type = 'medical';
                            } else if (typeStr === 'non medical' || typeStr === 'non-medical' || typeStr === 'nonmedical' || typeStr === 'nmed' || typeStr === 'non_medis') {
                                item.asset_type = 'non_medical';
                            }
                        }

                        if (!item.asset_type) {
                            warnings.push(`Row ${rowIndex + 2}: Tipe Aset tidak boleh kosong`);
                        } else if (item.asset_type !== 'medical' && item.asset_type !== 'non_medical') {
                            warnings.push(`Row ${rowIndex + 2}: Tipe Aset tidak valid. Harus "medical" atau "non_medical"`);
                        }

                        if (!item.subcategory_name) {
                            warnings.push(`Row ${rowIndex + 2}: Nama Kategori tidak boleh kosong`);
                        }

                        item._rowNum = rowIndex + 2;

                        previewData.push(item);
                    });

                    const subcategoryNameMap = {};
                    previewData.forEach(item => {
                        if (item.subcategory_name && item.asset_type) {
                            const key = `${item.asset_type}|${item.subcategory_name.toLowerCase()}`;
                            if (!subcategoryNameMap[key]) {
                                subcategoryNameMap[key] = [];
                            }
                            subcategoryNameMap[key].push(item._rowNum);
                        }
                    });

                    Object.entries(subcategoryNameMap).forEach(([key, rows]) => {
                        if (rows.length > 1) {
                            const [assetType, subcategoryName] = key.split('|');
                            warnings.push(`Kategori duplikat "${subcategoryName}" untuk Tipe Aset "${assetType}" ditemukan di baris: ${rows.join(', ')}`);
                        }
                    });

                    document.getElementById('category_excel_data').value = JSON.stringify(previewData);

                    showCategoryDataPreview(previewData, warnings);
                }

                function showCategoryDataPreview(data, warnings) {
                    const previewTableBody = document.getElementById('category-preview-table-body');
                    const previewCount = document.getElementById('category-preview-count');
                    const warningsContainer = document.getElementById('category-preview-warnings');
                    const warningsList = document.getElementById('category-warning-list');

                    if (!previewTableBody || !previewCount) return;

                    previewTableBody.innerHTML = '';
                    if (warningsList) warningsList.innerHTML = '';
                    if (warningsContainer) warningsContainer.classList.add('hidden');

                    previewCount.textContent = `${data.length} item ditemukan`;

                    data.forEach((item, index) => {
                        const row = document.createElement('tr');
                        row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';

                        const indexCell = document.createElement('td');
                        indexCell.className = 'p-3 text-xs border-t border-[#EEF1F4]';
                        indexCell.textContent = index + 1;
                        row.appendChild(indexCell);

                        const fields = ['asset_type', 'subcategory_name', 'description'];

                        fields.forEach(field => {
                            const cell = document.createElement('td');
                            cell.className = 'p-3 text-xs border-t border-[#EEF1F4]';

                            if (field === 'asset_type' && item[field]) {
                                cell.textContent = item[field] === 'medical' ? 'Medical' :
                                    item[field] === 'non_medical' ? 'Non Medical' :
                                        item[field];
                            } else {
                                cell.textContent = item[field] || '-';
                            }

                            row.appendChild(cell);
                        });

                        previewTableBody.appendChild(row);
                    });

                    if (warnings && warnings.length > 0 && warningsList && warningsContainer) {
                        warnings.forEach(warning => {
                            const li = document.createElement('li');
                            li.textContent = warning;
                            warningsList.appendChild(li);
                        });
                        warningsContainer.classList.remove('hidden');

                        const importBtn = document.getElementById('category-import-btn');
                        const hasCriticalWarnings = warnings.some(warning =>
                            warning.includes('Missing Asset Type') ||
                            warning.includes('Missing Category Name') ||
                            warning.includes('Invalid Asset Type')
                        );

                        if (importBtn && hasCriticalWarnings) {
                            importBtn.disabled = true;
                            importBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        } else if (importBtn) {
                            importBtn.disabled = false;
                            importBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    }
                }

                const categoryImportForm = document.getElementById('category-import-form');
                categoryImportForm?.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    const originalFileInput = document.getElementById('category_excel_file');
                    if (originalFileInput && originalFileInput.files.length > 0) {
                        formData.append('excel_file', originalFileInput.files[0]);
                    }

                    const importBtn = document.getElementById('category-import-btn');
                    const originalBtnText = importBtn.innerHTML;
                    importBtn.disabled = true;
                    importBtn.innerHTML = `
                        <div class="flex items-center justify-center">
                            <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                            <span>Memproses...</span>
                        </div>
                    `;

                    fetch('{{ route('categories.import') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => {
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                return response.json().then(data => {
                                    data.status = response.status;
                                    return data;
                                });
                            } else {
                                throw new Error('Invalid response format');
                            }
                        })
                        .then(data => {
                            importBtn.disabled = false;
                            importBtn.innerHTML = originalBtnText;

                            if (data.success === true || (data.status >= 200 && data.status < 300)) {
                                console.log('Import successful:', data);

                                const modal = document.getElementById('importCategoryModal');
                                closeModal(modal, modal.querySelector('[id$="ModalContent"]'));

                                showToast('Kategori aset berhasil diimpor!', 'success');

                                refreshTable();
                            } else {
                                console.error('Import error:', data);

                                console.error('Import error details:', data);

                                let errorMessage = data.message || 'Terjadi kesalahan selama pengimporan.';
                                let errorDetails = [];

                                if (data.data && data.data.errors) {
                                    console.log('Server returned detailed errors:', data.data.errors);

                                    if (Array.isArray(data.data.errors)) {
                                        data.data.errors.forEach(error => {
                                            if (typeof error === 'string') {
                                                errorDetails.push(error);
                                            } else if (error.message) {
                                                errorDetails.push(error.message);
                                            } else if (error.subcategory_name && error.reason) {
                                                errorDetails.push(`"${error.subcategory_name}" - ${error.reason}`);
                                            } else if (error.row && error.reason) {
                                                errorDetails.push(`Baris ${error.row}: ${error.reason}`);
                                            } else if (error.reason) {
                                                errorDetails.push(error.reason);
                                            }
                                        });
                                    }
                                }

                                if (errorDetails.length > 0) {
                                    errorMessage = `${errorMessage}<ul class="mt-2 ml-4 list-disc">`;
                                    errorDetails.forEach(detail => {
                                        errorMessage += `<li>${detail}</li>`;
                                    });
                                    errorMessage += '</ul>';
                                }

                                showToast(errorMessage, 'error');
                            }
                        })
                        .catch(error => {
                            importBtn.disabled = false;
                            importBtn.innerHTML = originalBtnText;

                            console.error('Import fetch error:', error);

                            let errorMessage = 'Terjadi kesalahan yang tidak diketahui. Silakan coba lagi.';

                            if (error.response) {
                                try {
                                    error.response.json().then(data => {
                                        if (data.message) {
                                            errorMessage = data.message;

                                            if (data.errors) {
                                                errorMessage += '<ul class="mt-2 ml-4 list-disc">';
                                                if (typeof data.errors === 'object') {
                                                    Object.values(data.errors).flat().forEach(err => {
                                                        errorMessage += `<li>${err}</li>`;
                                                    });
                                                } else if (Array.isArray(data.errors)) {
                                                    data.errors.forEach(err => {
                                                        errorMessage += `<li>${err}</li>`;
                                                    });
                                                }
                                                errorMessage += '</ul>';
                                            }

                                            showToast(errorMessage, 'error');
                                        }
                                    }).catch(() => {
                                        showToast(`Error: ${error.response.statusText || errorMessage}`, 'error');
                                    });
                                } catch (e) {
                                    showToast(errorMessage, 'error');
                                }
                            } else {
                                showToast(error.message || errorMessage, 'error');
                            }
                        });
                });

                // Validate field for edit form fields...

                if (deleteSubCategoryForm) {
                    deleteSubCategoryForm.addEventListener('submit', function (event) {
                        event.preventDefault();

                        // Submit the form using fetch
                        const formData = new FormData(this);
                        formData.append('_method', 'DELETE'); // For DELETE method

                        const submitBtn = this.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            const originalText = submitBtn.innerHTML;
                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = `
                                <div class="flex items-center justify-center">
                                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                    <span>Memproses...</span>
                                </div>
                            `;

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
                                    const modal = document.getElementById('deleteSubCategoryModal');
                                    closeModal(modal, modal.querySelector('[id$="ModalContent"]'));
                                    clearModalForms(modal);
                                    showToast(data.message || 'Kategori berhasil dihapus', 'success');

                                    refreshTable();
                                } else {
                                    showToast(data.message || 'Terjadi kesalahan saat menghapus kategori', 'error');
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
                    });
                }
            });
        </script>
