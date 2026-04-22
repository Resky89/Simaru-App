    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(!hasPermission('brand:create'))
                const addButtons = document.querySelectorAll('#addBrandBtn');
                addButtons.forEach(btn => {
                    if (btn) {
                        btn.style.display = 'none';
                    }
                });
            @endif

                @if(!hasPermission('brand:import'))
                    const importButtons = document.querySelectorAll('#importBrandBtn');
                    importButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                @if(!hasPermission('brand:edit'))
                    const editButtons = document.querySelectorAll('.edit-brand-btn');
                    editButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

                @if(!hasPermission('brand:delete'))
                    const deleteButtons = document.querySelectorAll('.delete-brand-btn');
                    deleteButtons.forEach(btn => {
                        if (btn) {
                            btn.style.display = 'none';
                        }
                    });
                @endif

            // Column header sorting (moved to event delegation)

            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif

                            @if(session('error'))
                                showToast({!! json_encode(session('error')) !!}, 'error');
                            @endif

                            const searchInput = document.getElementById('searchInput');
            const sortOrder = document.getElementById('sortOrder');

            function applyFilters() {
                const searchValue = searchInput?.value.trim() || '';
                const sortValue = sortOrder?.value || '';

                const url = new URL(window.location.href);

                ['search', 'sort', 'page'].forEach(param => {
                    url.searchParams.delete(param);
                });

                if (searchValue) url.searchParams.set('search', searchValue);
                if (sortValue) url.searchParams.set('sort', sortValue);

                url.searchParams.set('page', 1);

                refreshTable(url.toString());
            }

            let searchTimeout;
            searchInput?.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 500);
            });

            sortOrder?.addEventListener('change', applyFilters);

            const urlParams = new URLSearchParams(window.location.search);
            if (searchInput) searchInput.value = urlParams.get('search') || '';
            if (sortOrder) {
                const sortValue = urlParams.get('sort');
                if (sortValue) {
                    sortOrder.value = sortValue;
                }
            }

            // Function to refresh the table without reloading the page
            window.refreshTable = function(targetUrl = null) {
                const fetchUrl = new URL(targetUrl || window.location.href);
                
                // Jika URL berubah (misalnya dari filter), update URL browser
                if (targetUrl) {
                    window.history.pushState({path: fetchUrl.toString()}, '', fetchUrl.toString());
                }

                // Tambahkan parameter time untuk mencegah masalah cache browser
                fetchUrl.searchParams.set('_t', new Date().getTime());

                // Lakukan fetch tanpa header XMLHttpRequest agar controller 
                // tidak mengembalikan format JSON, melainkan return view HTML
                fetch(fetchUrl.toString())
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTableContainer = doc.getElementById('brandTableContainer');
                    const currentTableContainer = document.getElementById('brandTableContainer');
                    if (newTableContainer && currentTableContainer) {
                        currentTableContainer.innerHTML = newTableContainer.innerHTML;
                    }
                })
                .catch(err => console.error('Error refreshing table:', err));
            };

            // Menangani ketika user menggunakan tombol back pada browser
            window.addEventListener('popstate', function(event) {
                refreshTable(window.location.href);
            });

            document.addEventListener('click', function(e) {
                // Event delegation for Sort Column
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
                // Event delegation for Edit Brand Button
                const editBtn = e.target.closest('.edit-brand-btn');
                if (editBtn) {
                    const brandId = editBtn.getAttribute('data-brand-id');
                    const brandName = editBtn.getAttribute('data-brand-name');

                    document.getElementById('edit_brand_name').value = brandName;
                    document.getElementById('editBrandForm').action = `/brands/${brandId}`;

                    const modal = document.getElementById('editBrandModal');
                    const content = document.getElementById('editBrandModalContent');
                    if (modal && content) openModal(modal, content);
                }

                // Event delegation for Delete Brand Button
                const deleteBtn = e.target.closest('.delete-brand-btn');
                if (deleteBtn) {
                    const brandId = deleteBtn.getAttribute('data-brand-id');
                    const brandName = deleteBtn.getAttribute('data-brand-name');

                    document.getElementById('delete_brand_name').textContent = brandName;

                    const formAction = "{{ url('brands') }}/" + brandId;
                    document.getElementById('deleteBrandForm').action = formAction;

                    document.getElementById('deleteBrandId').value = brandId;

                    const modal = document.getElementById('deleteBrandModal');
                    const content = document.getElementById('deleteBrandModalContent');
                    if (modal && content) openModal(modal, content);
                }
            });

            const addBrandBtn = document.getElementById('addBrandBtn');
            if (addBrandBtn) {
                addBrandBtn.addEventListener('click', function () {
                    const modal = document.getElementById('addBrandModal');
                    const content = document.getElementById('addBrandModalContent');
                    if (modal && content) openModal(modal, content);
                });
            }

            const importBrandBtn = document.getElementById('importBrandBtn');
            if (importBrandBtn) {
                importBrandBtn.addEventListener('click', function () {
                    const modal = document.getElementById('importBrandModal');
                    const content = document.getElementById('importBrandModalContent');
                    if (modal && content) openModal(modal, content);
                });
            }

            document.querySelectorAll('.close-modal').forEach(button => {
                button.addEventListener('click', function () {
                    const modal = this.closest('[id$="Modal"]');
                    const content = modal.querySelector('[id$="ModalContent"]');
                    if (modal && content) {
                        closeModal(modal, content);
                    }
                });
            });

            document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                modal.addEventListener('click', function (e) {
                    if (e.target === this.querySelector('.fixed.inset-0.z-50.overflow-y-auto') ||
                        e.target === this.querySelector('.fixed.inset-0.bg-black.bg-opacity-50')) {
                        const content = this.querySelector('[id$="ModalContent"]');
                        closeModal(this, content);
                    }
                });
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                        if (!modal.classList.contains('hidden')) {
                            const content = modal.querySelector('[id$="ModalContent"]');
                            closeModal(modal, content);
                        }
                    });
                }
            });

            const brandExcelFile = document.getElementById('brand_excel_file');
            const brandFileNameContainer = document.getElementById('brand-excel-file-name');
            const brandFileNameText = document.getElementById('brand-file-name-text');
            const removeBrandExcel = document.getElementById('remove-brand-excel');
            const brandPreviewBtn = document.getElementById('brand-preview-btn');
            const brandExcelError = document.getElementById('brand-excel-error');
            const brandExcelLoading = document.getElementById('brand-excel-loading');

            if (brandExcelFile) {
                brandExcelFile.addEventListener('change', function (e) {
                    if (brandExcelError) brandExcelError.classList.add('hidden');

                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const fileExt = file.name.split('.').pop().toLowerCase();

                        if (!['xlsx', 'xls', 'csv'].includes(fileExt)) {
                            if (brandExcelError) {
                                brandExcelError.textContent = 'Format file tidak valid. Silakan unggah file Excel (.xlsx, .xls) atau file CSV.';
                                brandExcelError.classList.remove('hidden');
                            }
                            this.value = '';
                            if (brandFileNameContainer) brandFileNameContainer.classList.add('hidden');
                            if (brandPreviewBtn) brandPreviewBtn.disabled = true;
                            return;
                        }

                        if (brandFileNameText) brandFileNameText.textContent = file.name;
                        if (brandFileNameContainer) brandFileNameContainer.classList.remove('hidden');
                        if (brandPreviewBtn) brandPreviewBtn.disabled = false;
                    } else {
                        if (brandFileNameContainer) brandFileNameContainer.classList.add('hidden');
                        if (brandPreviewBtn) brandPreviewBtn.disabled = true;
                    }
                });
            }

            if (removeBrandExcel) {
                removeBrandExcel.addEventListener('click', function () {
                    if (brandExcelFile) brandExcelFile.value = '';
                    if (brandFileNameContainer) brandFileNameContainer.classList.add('hidden');
                    if (brandPreviewBtn) brandPreviewBtn.disabled = true;
                    if (brandExcelError) brandExcelError.classList.add('hidden');
                });
            }

            if (brandPreviewBtn) {
                brandPreviewBtn.addEventListener('click', function () {
                    if (!brandExcelFile || !brandExcelFile.files || !brandExcelFile.files[0]) {
                        if (brandExcelError) {
                            brandExcelError.textContent = 'Silakan pilih file terlebih dahulu.';
                            brandExcelError.classList.remove('hidden');
                        }
                        return;
                    }

                    const file = brandExcelFile.files[0];

                    if (brandExcelLoading) brandExcelLoading.classList.remove('hidden');
                    if (brandExcelError) brandExcelError.classList.add('hidden');

                    const reader = new FileReader();

                    reader.onload = function (e) {
                        try {
                            const data = new Uint8Array(e.target.result);
                            const workbook = XLSX.read(data, { type: 'array' });

                            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];

                            const rows = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

                            if (rows.length < 2) {
                                throw new Error('File tidak berisi data atau header tidak ditemukan.');
                            }

                            processBrandExcelData(rows);

                            if (brandExcelLoading) brandExcelLoading.classList.add('hidden');

                            document.getElementById('import-brand-step-1').classList.add('hidden');
                            document.getElementById('import-brand-step-2').classList.remove('hidden');
                        } catch (error) {
                            console.error('Excel parsing error:', error);
                            if (brandExcelLoading) brandExcelLoading.classList.add('hidden');
                            if (brandExcelError) {
                                brandExcelError.textContent = 'Error memproses file: ' + error.message;
                                brandExcelError.classList.remove('hidden');
                            }
                        }
                    };

                    reader.onerror = function () {
                        console.error('FileReader error:', reader.error);
                        if (brandExcelLoading) brandExcelLoading.classList.add('hidden');
                        if (brandExcelError) {
                            brandExcelError.textContent = 'Error membaca file. Silakan coba file lain.';
                            brandExcelError.classList.remove('hidden');
                        }
                    };

                    reader.readAsArrayBuffer(file);
                });
            }

            const brandBackBtn = document.getElementById('brand-back-to-upload-btn');
            if (brandBackBtn) {
                brandBackBtn.addEventListener('click', function () {
                    document.getElementById('import-brand-step-2').classList.add('hidden');
                    document.getElementById('import-brand-step-1').classList.remove('hidden');
                });
            }

            function processBrandExcelData(data) {
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

                    item.brand_name = getValue(['brand_name', 'brand name', 'name', 'nama brand', 'nama_brand', 'nama merk', 'merk']);

                    if (!item.brand_name) {
                        warnings.push(`Baris ${rowIndex + 2}: Nama Merk tidak ditemukan`);
                    }

                    item._rowNum = rowIndex + 2;

                    previewData.push(item);
                });

                const brandNameMap = {};
                previewData.forEach(item => {
                    if (item.brand_name) {
                        const key = item.brand_name.toLowerCase();
                        if (!brandNameMap[key]) {
                            brandNameMap[key] = [];
                        }
                        brandNameMap[key].push(item._rowNum);
                    }
                });

                Object.entries(brandNameMap).forEach(([key, rows]) => {
                    if (rows.length > 1) {
                        warnings.push(`Nama Merk duplikat "${key}" ditemukan di baris: ${rows.join(', ')}`);
                    }
                });

                document.getElementById('brand_excel_data').value = JSON.stringify(previewData);

                showBrandDataPreview(previewData, warnings);
            }

            function showBrandDataPreview(data, warnings) {
                const previewTableBody = document.getElementById('brand-preview-table-body');
                const previewCount = document.getElementById('brand-preview-count');
                const warningsContainer = document.getElementById('brand-preview-warnings');
                const warningsList = document.getElementById('brand-warning-list');

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

                    const brandNameCell = document.createElement('td');
                    brandNameCell.className = 'p-3 text-xs border-t border-[#EEF1F4]';
                    brandNameCell.textContent = item.brand_name || '-';
                    row.appendChild(brandNameCell);

                    previewTableBody.appendChild(row);
                });

                if (warnings && warnings.length > 0 && warningsList && warningsContainer) {
                    warnings.forEach(warning => {
                        const li = document.createElement('li');
                        li.textContent = warning;
                        warningsList.appendChild(li);
                    });
                    warningsContainer.classList.remove('hidden');

                    const importBtn = document.getElementById('brand-import-btn');
                    const hasCriticalWarnings = warnings.some(warning =>
                        warning.includes('Nama Merk tidak ditemukan')
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

            const brandImportForm = document.getElementById('brand-import-form');
            brandImportForm?.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                const originalFileInput = document.getElementById('brand_excel_file');
                if (originalFileInput && originalFileInput.files.length > 0) {
                    formData.append('excel_file', originalFileInput.files[0]);
                }

                const importBtn = document.getElementById('brand-import-btn');
                const originalBtnText = importBtn.innerHTML;
                importBtn.disabled = true;
                importBtn.innerHTML = `
                                    <div class="flex items-center justify-center">
                                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                        <span>Mengimpor...</span>
                                    </div>
                                `;

                fetch('{{ route('brands.import') }}', {
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
                            throw new Error('Format respons tidak valid');
                        }
                    })
                    .then(data => {
                        importBtn.disabled = false;
                        importBtn.innerHTML = originalBtnText;

                        if (data.success === true || (data.status >= 200 && data.status < 300)) {
                            const modal = document.getElementById('importBrandModal');
                            closeModal(modal, modal.querySelector('[id$="ModalContent"]'));

                            showToast('Merk berhasil diimpor!', 'success');

                            refreshTable();
                        } else {
                            console.error('Import error:', data);

                            let errorMessage = data.message || 'Terjadi kesalahan selama pengimporan.';
                            let errorDetails = [];

                            if (data.data && data.data.errors) {

                                if (Array.isArray(data.data.errors)) {
                                    data.data.errors.forEach(error => {
                                        if (typeof error === 'string') {
                                            errorDetails.push(error);
                                        } else if (error.message) {
                                            errorDetails.push(error.message);
                                        } else if (error.brand_name && error.reason) {
                                            errorDetails.push(`"${error.brand_name}" - ${error.reason}`);
                                        } else if (error.row && error.reason) {
                                            errorDetails.push(`${error.reason}`);
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
                        showToast('Terjadi kesalahan saat mengimpor data: ' + error.message, 'error');
                    });
            });

            const addBrandForm = document.getElementById('addBrandForm');
            if (addBrandForm) {
                addBrandForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const brandNameInput = document.getElementById('add_brand_name');
                    const isValid = validateField(brandNameInput);

                    if (!isValid) {
                        showToast('Silakan isi semua field yang diperlukan', 'error');
                        return;
                    }

                    // If validation passes, submit the form using fetch
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
                                // Success - close modal and show success message
                                const modal = document.getElementById('addBrandModal');
                                closeModal(modal, modal.querySelector('[id$="ModalContent"]'));
                                resetForm('addBrandForm');
                                showToast(data.message || 'Merk berhasil ditambahkan', 'success');

                                // Refresh table dynamically without reloading page
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
                                showToast(data.message || 'Terjadi kesalahan saat menyimpan merk', 'error');
                                    }
                                } else {
                                    // Show general error message
                                    const errorMessage = data.message || 'Terjadi kesalahan saat menyimpan merk';
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

            const editBrandForm = document.getElementById('editBrandForm');
            if (editBrandForm) {
                editBrandForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const brandNameInput = document.getElementById('edit_brand_name');
                    const isValid = validateField(brandNameInput);

                    if (!isValid) {
                        showToast('Silakan isi semua field yang diperlukan', 'error');
                        return;
                    }

                    // If validation passes, submit the form using fetch
                    const formData = new FormData(this);
                    formData.append('_method', 'PUT'); // For PUT method

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
                                // Success - close modal and show success message
                                const modal = document.getElementById('editBrandModal');
                                closeModal(modal, modal.querySelector('[id$="ModalContent"]'));
                                resetForm('editBrandForm');
                                showToast(data.message || 'Merk berhasil diperbarui', 'success');

                                // Refresh table dynamically without reloading page
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
                                showToast(data.message || 'Terjadi kesalahan saat memperbarui merk', 'error');
                                    }
                                } else {
                                    // Show general error message
                                    const errorMessage = data.message || 'Terjadi kesalahan saat memperbarui merk';
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

            const deleteBrandForm = document.getElementById('deleteBrandForm');
            if (deleteBrandForm) {
                deleteBrandForm.addEventListener('submit', function (event) {
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
                                <span>Menghapus...</span>
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
                                // Success - close modal and show success message
                                const modal = document.getElementById('deleteBrandModal');
                                closeModal(modal, modal.querySelector('[id$="ModalContent"]'));
                                showToast(data.message || 'Merk berhasil dihapus', 'success');

                                // Refresh table dynamically without reloading page
                                refreshTable();
                            } else {
                                // Error - keep modal open and show error
                                showToast(data.message || 'Terjadi kesalahan saat menghapus merk', 'error');
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
                if (!field) return false;

                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    if (field.nextElementSibling) {
                        field.nextElementSibling.classList.remove('hidden');
                    }
                    return false;
                } else {
                    field.classList.remove('border-red-500');
                    if (field.nextElementSibling) {
                        field.nextElementSibling.classList.add('hidden');
                    }
                    return true;
                }
            }

            // Function to clear all field errors
            function clearFieldErrors(form) {
                if (!form) return;

                const fields = form.querySelectorAll('input, select, textarea');
                fields.forEach(field => {
                    field.classList.remove('border-red-500');
                    const errorElement = field.closest('.space-y-2')?.querySelector('.error-message') || field.nextElementSibling;
                    if (errorElement && errorElement.classList.contains('error-message')) {
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
                               form.querySelector(`#edit_${fieldName}`);

                    if (field) {
                        // Add error styling
                        field.classList.add('border-red-500');

                        // Find and update error message element
                        const errorElement = field.closest('.space-y-2')?.querySelector('.error-message') || field.nextElementSibling;
                        if (errorElement && errorElement.classList.contains('error-message')) {
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
                const errorElement = field.closest('.space-y-2')?.querySelector('.error-message') || field.nextElementSibling;
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.classList.add('hidden');
                    errorElement.textContent = '';
                }
            }

            // Add error clearing for form fields
            const formFields = [
                { id: 'add_brand_name', event: 'input' },
                { id: 'edit_brand_name', event: 'input' }
            ];

            formFields.forEach(fieldConfig => {
                const field = document.getElementById(fieldConfig.id);
                if (field) {
                    field.addEventListener(fieldConfig.event, function () {
                        clearFieldError(this);
                    });
                }
            });
        });
    </script>
