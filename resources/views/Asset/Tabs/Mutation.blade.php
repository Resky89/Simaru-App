<div class="p-6">
    <!-- Loading indicator -->
    <div id="mutationLoadingIndicator" class="flex justify-center items-center py-4">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#213268]"></div>
    </div>

    <!-- Error message container -->
    <div id="mutationErrorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
    </div>

    <!-- Content container -->
    <div id="mutationContent" class="hidden">
        <!-- Mutation data will be loaded here dynamically -->
    </div>

    <!-- No data message -->
    <div id="mutationNoDataMessage" class="hidden text-center py-8">
        <p class="text-gray-500">No mutation history available for this asset.</p>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const MutationSystem = {
            initialized: false,
            assetId: {{ $asset['asset_id'] ?? 'null' }},
            // Use a unique system identifier to isolate this system
            systemId: 'mutation-system-' + Math.random().toString(36).substr(2, 9),

            init() {
                if (this.initialized) return;
                console.log(`[${this.systemId}] Initializing Mutation System with Asset ID:`, this.assetId);

                if (!this.assetId) {
                    console.error(`[${this.systemId}] Asset ID is not available`);
                    this.showError('Asset ID tidak tersedia');
                    return;
                }

                this.loadMutationData();
                this.initialized = true;
            },

            showLoading() {
                document.getElementById('mutationLoadingIndicator').classList.remove('hidden');
                document.getElementById('mutationContent').classList.add('hidden');
                document.getElementById('mutationErrorMessage').classList.add('hidden');
                document.getElementById('mutationNoDataMessage').classList.add('hidden');
            },

            hideLoading() {
                document.getElementById('mutationLoadingIndicator').classList.add('hidden');
            },

            showError(message) {
                const errorDiv = document.getElementById('mutationErrorMessage');
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
                document.getElementById('mutationContent').classList.add('hidden');
                document.getElementById('mutationLoadingIndicator').classList.add('hidden');
                document.getElementById('mutationNoDataMessage').classList.add('hidden');
            },

            showNoData() {
                document.getElementById('mutationNoDataMessage').classList.remove('hidden');
                document.getElementById('mutationContent').classList.add('hidden');
                document.getElementById('mutationLoadingIndicator').classList.add('hidden');
                document.getElementById('mutationErrorMessage').classList.add('hidden');
            },

            formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                }).replace(/\//g, '/');
            },

            loadMutationData() {
                if (!this.assetId) {
                    this.showError('Asset ID tidak tersedia');
                    return;
                }

                this.showLoading();
                console.log(`[${this.systemId}] Fetching mutation data for asset ID:`, this.assetId);

                fetch(`/asset-mutations/asset/${this.assetId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(`[${this.systemId}] Received mutation data:`, data);

                    if (!data.status) {
                        throw new Error(data.message || 'Failed to fetch mutation data');
                    }

                    const mutations = data.data.histories || [];

                    if (mutations.length === 0) {
                        this.showNoData();
                    } else {
                        this.renderMutations(mutations);
                        document.getElementById('mutationContent').classList.remove('hidden');
                    }

                    this.hideLoading();
                })
                .catch(error => {
                    console.error(`[${this.systemId}] Error loading mutation data:`, error);
                    this.showError('Gagal memuat data mutasi: ' + error.message);
                });
            },

            getStatusIcon(status) {
                switch(status) {
                    case 'checked out':
                        return `<span class="text-[#DAAE0F]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </span>`;
                    case 'return':
                        return `<span class="text-[#7CB60C]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </span>`;
                    case 'loss':
                        return `<span class="text-[#FF4A2B]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>`;
                    case 'found':
                        return `<span class="text-black">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>`;
                    case 'dispose':
                        return `<span class="text-[#5D76B5]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </span>`;
                    default:
                        return `<span class="text-[#7CB60C]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                </svg>
                            </span>`;
                }
            },

            getStatusColor(status) {
                switch(status) {
                    case 'checked out': return 'text-[#DAAE0F]';
                    case 'return': return 'text-[#7CB60C]';
                    case 'loss': return 'text-[#FF4A2B]';
                    case 'found': return 'text-[#7CB60C]';
                    case 'dispose': return 'text-[#ACC3EF]';
                    default: return 'text-[#7CB60C]';
                }
            },

            renderMutations(mutations) {
                const container = document.getElementById('mutationContent');
                let html = '';

                mutations.forEach(mutation => {
                    const status = mutation.status || '';
                    const icon = this.getStatusIcon(status);
                    const textColor = this.getStatusColor(status);

                    // Define different background colors based on status
                    let bgColor, bgColorLight, borderColor, titleColor;

                    switch(status) {
                        case 'checked out':
                            bgColor = 'bg-amber-100';
                            bgColorLight = 'bg-amber-50';
                            borderColor = 'border-amber-200';
                            titleColor = 'text-amber-700';
                            break;
                        case 'return':
                            bgColor = 'bg-green-100';
                            bgColorLight = 'bg-green-50';
                            borderColor = 'border-green-200';
                            titleColor = 'text-green-700';
                            break;
                        case 'loss':
                            bgColor = 'bg-red-100';
                            bgColorLight = 'bg-red-50';
                            borderColor = 'border-red-200';
                            titleColor = 'text-red-700';
                            break;
                        case 'found':
                            bgColor = 'bg-green-100';
                            bgColorLight = 'bg-green-50';
                            borderColor = 'border-green-200';
                            titleColor = 'text-black';
                            break;
                        case 'dispose':
                            bgColor = 'bg-[#ACC3EF]';
                            bgColorLight = 'bg-[#E1E8F7]';
                            borderColor = 'border-[#ACC3EF]';
                            titleColor = 'text-[#5D76B5]';
                            break;
                        default:
                            bgColor = 'bg-gray-100';
                            bgColorLight = 'bg-gray-50';
                            borderColor = 'border-gray-200';
                            titleColor = 'text-gray-700';
                    }

                    if (status === 'checked out') {
                        const checkoutDate = mutation.detail_info?.checkout_date ? this.formatDate(mutation.detail_info.checkout_date) : '';
                        const checkoutNotes = mutation.detail_info?.checkout_notes || '';

                        // Check if checkout was to employee or location
                        if (mutation.detail_info?.employee) {
                            const employeeName = mutation.detail_info.employee.name || '';
                            const department = mutation.detail_info.employee.department_name || '';
                            const position = mutation.detail_info.employee.position || '';

                            html += `
                            <div class="mb-8 ${bgColorLight} border ${borderColor} border-l-4 rounded-md shadow-md overflow-hidden">
                                <div class="px-4 py-3 ${bgColor} border-b ${borderColor}">
                                    <div class="flex items-center gap-2">
                                        ${icon} <span class="text-lg font-bold ${titleColor}">CHECKED OUT TO EMPLOYEE</span>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="${bgColorLight} rounded">
                                            <div class="px-4 py-2">
                                                <div class="text-gray-500 text-sm">Date</div>
                                                <div class="font-medium text-black">${checkoutDate}</div>
                                            </div>
                                        </div>
                                        <div class="${bgColorLight} rounded">
                                            <div class="px-4 py-2">
                                                <div class="text-gray-500 text-sm">Assigned to</div>
                                                <div class="font-medium text-black">${employeeName}</div>
                                            </div>
                                        </div>
                                        <div class="${bgColorLight} rounded">
                                            <div class="px-4 py-2">
                                                <div class="text-gray-500 text-sm">Department</div>
                                                <div class="font-medium text-black">${department}</div>
                                            </div>
                                        </div>
                                        <div class="${bgColorLight} rounded">
                                            <div class="px-4 py-2">
                                                <div class="text-gray-500 text-sm">Position</div>
                                                <div class="font-medium text-black">${position}</div>
                                            </div>
                                        </div>
                                        ${checkoutNotes ? `
                                        <div class="col-span-1 md:col-span-2 ${bgColorLight} rounded">
                                            <div class="px-4 py-2">
                                                <div class="text-gray-500 text-sm">Notes</div>
                                                <div class="text-black">${checkoutNotes}</div>
                                            </div>
                                        </div>` : ''}
                                    </div>
                                </div>
                            </div>`;
                        } else if (mutation.detail_info?.location) {
                            const roomName = mutation.detail_info.location.room_name || '';
                            const buildingName = mutation.detail_info.location.building_name || '';
                            const floorNumber = mutation.detail_info.location.floor_number || '';

                            html += `
                            <div class="mb-8 ${bgColorLight} border ${borderColor} border-l-4 rounded-md shadow-md overflow-hidden">
                                <div class="px-4 py-3 ${bgColor} border-b ${borderColor}">
                                    <div class="flex items-center gap-2">
                                        ${icon} <span class="text-lg font-bold ${titleColor}">CHECKED OUT TO LOCATION</span>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="${bgColorLight} rounded">
                                            <div class="px-4 py-2">
                                                <div class="text-gray-500 text-sm">Date</div>
                                                <div class="font-medium text-black">${checkoutDate}</div>
                                            </div>
                                        </div>
                                        <div class="${bgColorLight} rounded">
                                            <div class="px-4 py-2">
                                                <div class="text-gray-500 text-sm">Room</div>
                                                <div class="font-medium text-black">${roomName}</div>
                                            </div>
                                        </div>
                                        <div class="${bgColorLight} rounded">
                                            <div class="px-4 py-2">
                                                <div class="text-gray-500 text-sm">Building</div>
                                                <div class="font-medium text-black">${buildingName}</div>
                                            </div>
                                        </div>
                                        <div class="${bgColorLight} rounded">
                                            <div class="px-4 py-2">
                                                <div class="text-gray-500 text-sm">Floor</div>
                                                <div class="font-medium text-black">${floorNumber}</div>
                                            </div>
                                        </div>
                                        ${checkoutNotes ? `
                                        <div class="col-span-1 md:col-span-2 ${bgColorLight} rounded">
                                            <div class="px-4 py-2">
                                                <div class="text-gray-500 text-sm">Notes</div>
                                                <div class="text-black">${checkoutNotes}</div>
                                            </div>
                                        </div>` : ''}
                                    </div>
                                </div>
                            </div>`;
                        }
                    } else if (status === 'return') {
                        const returnDate = mutation.return_date ? this.formatDate(mutation.return_date) : '';
                        const returnNotes = mutation.return_notes || '';

                        html += `
                        <div class="mb-8 ${bgColorLight} border ${borderColor} border-l-4 rounded-md shadow-md overflow-hidden">
                            <div class="px-4 py-3 ${bgColor} border-b ${borderColor}">
                                <div class="flex items-center gap-2">
                                    ${icon} <span class="text-lg font-bold ${titleColor}">RETURN</span>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="${bgColorLight} rounded">
                                        <div class="px-4 py-2">
                                            <div class="text-gray-500 text-sm">Return Date</div>
                                            <div class="font-medium text-black">${returnDate}</div>
                                        </div>
                                    </div>
                                    ${returnNotes ? `
                                    <div class="col-span-1 md:col-span-${returnNotes.length > 100 ? '2' : '1'} ${bgColorLight} rounded">
                                        <div class="px-4 py-2">
                                            <div class="text-gray-500 text-sm">Notes</div>
                                            <div class="text-black">${returnNotes}</div>
                                        </div>
                                    </div>` : ''}
                                </div>
                            </div>
                        </div>`;
                    } else if (status === 'loss') {
                        const lossDate = mutation.loss_date ? this.formatDate(mutation.loss_date) : '';
                        const lossNotes = mutation.loss_notes || '';

                        html += `
                        <div class="mb-8 ${bgColorLight} border ${borderColor} border-l-4 rounded-md shadow-md overflow-hidden">
                            <div class="px-4 py-3 ${bgColor} border-b ${borderColor}">
                                <div class="flex items-center gap-2">
                                    ${icon} <span class="text-lg font-bold ${titleColor}">LOSS</span>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="${bgColorLight} rounded">
                                        <div class="px-4 py-2">
                                            <div class="text-gray-500 text-sm">Loss Date</div>
                                            <div class="font-medium text-black">${lossDate}</div>
                                        </div>
                                    </div>
                                    ${lossNotes ? `
                                    <div class="col-span-1 md:col-span-${lossNotes.length > 100 ? '2' : '1'} ${bgColorLight} rounded">
                                        <div class="px-4 py-2">
                                            <div class="text-gray-500 text-sm">Notes</div>
                                            <div class="text-black">${lossNotes}</div>
                                        </div>
                                    </div>` : ''}
                                </div>
                            </div>
                        </div>`;
                    } else if (status === 'found') {
                        const foundDate = mutation.found_date ? this.formatDate(mutation.found_date) : '';
                        const foundNotes = mutation.found_notes || '';

                        html += `
                        <div class="mb-8 ${bgColorLight} border ${borderColor} border-l-4 rounded-md shadow-md overflow-hidden">
                            <div class="px-4 py-3 ${bgColor} border-b ${borderColor}">
                                <div class="flex items-center gap-2">
                                    ${icon} <span class="text-lg font-bold ${titleColor}">FOUND</span>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="${bgColorLight} rounded">
                                        <div class="px-4 py-2">
                                            <div class="text-gray-500 text-sm">Found Date</div>
                                            <div class="font-medium text-black">${foundDate}</div>
                                        </div>
                                    </div>
                                    ${foundNotes ? `
                                    <div class="col-span-1 md:col-span-${foundNotes.length > 100 ? '2' : '1'} ${bgColorLight} rounded">
                                        <div class="px-4 py-2">
                                            <div class="text-gray-500 text-sm">Notes</div>
                                            <div class="text-black">${foundNotes}</div>
                                        </div>
                                    </div>` : ''}
                                </div>
                            </div>
                        </div>`;
                    } else if (status === 'dispose') {
                        const disposeDate = mutation.dispose_date ? this.formatDate(mutation.dispose_date) : '';
                        const disposeNotes = mutation.dispose_notes || '';

                        html += `
                        <div class="mb-8 ${bgColorLight} border ${borderColor} border-l-4 rounded-md shadow-md overflow-hidden">
                            <div class="px-4 py-3 ${bgColor} border-b ${borderColor}">
                                <div class="flex items-center gap-2">
                                    ${icon} <span class="text-lg font-bold ${titleColor}">DISPOSE</span>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="${bgColorLight} rounded">
                                        <div class="px-4 py-2">
                                            <div class="text-gray-500 text-sm">Dispose Date</div>
                                            <div class="font-medium text-black">${disposeDate}</div>
                                        </div>
                                    </div>
                                    ${disposeNotes ? `
                                    <div class="col-span-1 md:col-span-${disposeNotes.length > 100 ? '2' : '1'} ${bgColorLight} rounded">
                                        <div class="px-4 py-2">
                                            <div class="text-gray-500 text-sm">Notes</div>
                                            <div class="text-black">${disposeNotes}</div>
                                        </div>
                                    </div>` : ''}
                                </div>
                            </div>
                        </div>`;
                    }
                });

                container.innerHTML = html;
            }
        };

        // Initialize the system immediately (or when the tab is clicked)
        MutationSystem.init();
    });
</script>
@endpush
