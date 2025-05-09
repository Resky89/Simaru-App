@extends('Layout.app')

@section('title', 'Detail Perbandingan')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Comparison Detail Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center">
                        <a href="{{ route('procurement.price-comparison') }}" class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DAFTAR HARGA VENDOR</h1>
                    </div>

                    <!-- Add Vendor Button -->
                    @if(isset($comparison) && !empty($comparison) && (!isset($comparison['status']) || $comparison['status'] !== 'Completed'))
                    <a href="{{ route('procurement.form-vendor-comparison', ['id' => $comparison['comparison_id'] ?? $id]) }}"
                       class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Vendor</span>
                    </a>
                    @endif
                </div>

                <!-- Success Message (hidden by default) -->
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md hidden" id="successMessage">
                    <p>Berhasil! Data telah disimpan.</p>
                </div>

                @if(isset($comparison) && !empty($comparison))
                <!-- Request Details -->
                <div class="grid grid-cols-1 gap-5">
                    <!-- Request Number -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">ID Penawaran</p>
                        <p class="text-[#666666]">: <span id="requestNumber">{{ $comparison['comparison_code'] ?? 'N/A' }}</span></p>
                    </div>

                    <!-- Request Name -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Judul Permintaan</p>
                        <p class="text-[#666666]">: <span id="requestName">{{ $comparison['title'] ?? 'N/A' }}</span></p>
                    </div>

                    <!-- User Input -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Dibuat oleh</p>
                        <p class="text-[#666666]">: <span id="userInput">{{ isset($comparison['creator']) ? $comparison['creator']['employee_number'] : 'N/A' }}</span></p>
                    </div>

                    <!-- Input Date -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Tanggal Penawaran</p>
                        <p class="text-[#666666]">: <span id="inputDate">{{ isset($comparison['created_at']) ? \Carbon\Carbon::parse($comparison['created_at'])->format('Y-m-d H:i:s') : 'N/A' }}</span></p>
                    </div>

                    <!-- Status -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Status</p>
                        <p class="text-[#666666]">: <span id="status">{{ $comparison['status'] ?? 'N/A' }}</span></p>
                    </div>
                </div>

                <!-- Item List -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-[#666666]">Perbandingan Harga</h2>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Nama Aset</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Jumlah</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Perkiraan Harga</th>

                                    @php
                                    $uniqueVendors = [];

                                    // Collect all unique vendors across all items
                                    if(isset($comparison['items']) && is_array($comparison['items'])) {
                                        foreach($comparison['items'] as $item) {
                                            if(isset($item['vendor_offers']) && is_array($item['vendor_offers'])) {
                                                foreach($item['vendor_offers'] as $offer) {
                                                    if(isset($offer['vendor']) && isset($offer['vendor']['vendor_id'])) {
                                                        $vendorId = $offer['vendor']['vendor_id'];
                                                        if(!isset($uniqueVendors[$vendorId])) {
                                                            $uniqueVendors[$vendorId] = $offer['vendor'];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $hasVendors = (count($uniqueVendors) > 0);
                                    @endphp

                                    @if($hasVendors)
                                        @foreach($uniqueVendors as $vendor)
                                        <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">
                                            <div class="flex items-center justify-between">
                                                <span>{{ $vendor['vendor_name'] }}</span>
                                                @if(!isset($comparison['status']) || $comparison['status'] !== 'Completed')
                                                <div class="flex gap-2">
                                                    @php
                                                    // Find vendor_offer_id and agreement_id for this vendor
                                                    $vendorOfferId = null;
                                                    $agreementId = null;
                                                    $vendorOfferIds = [];

                                                    if(isset($comparison['items']) && is_array($comparison['items'])) {
                                                        foreach($comparison['items'] as $item) {
                                                            if(isset($item['vendor_offers']) && is_array($item['vendor_offers'])) {
                                                                foreach($item['vendor_offers'] as $offer) {
                                                                    if(isset($offer['vendor']) && $offer['vendor']['vendor_id'] === $vendor['vendor_id']) {
                                                                        $vendorOfferId = $offer['vendor_offer_id'] ?? null;

                                                                        // Store vendor_offer_id for this item
                                                                        if ($vendorOfferId && isset($item['price_comparison_item_id'])) {
                                                                            $vendorOfferIds[$item['price_comparison_item_id']] = $vendorOfferId;
                                                                        }

                                                                        if(isset($offer['agreement']) && isset($offer['agreement']['agreement_id'])) {
                                                                            $agreementId = $offer['agreement']['agreement_id'];
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    @endphp

                                                    <form id="edit-vendor-form-{{ $vendor['vendor_id'] }}" action="{{ route('procurement.form-vendor-comparison', ['id' => $comparison['comparison_id']]) }}" method="get" style="display:inline;">
                                                        <input type="hidden" name="agreement_id" value="{{ $agreementId }}">

                                                        @foreach($vendorOfferIds as $itemId => $offerId)
                                                            <input type="hidden" name="vo_{{ $itemId }}" value="{{ $offerId }}">
                                                        @endforeach

                                                        <button type="submit" class="text-white hover:text-gray-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    <button class="text-white hover:text-gray-200 delete-vendor-btn"
                                                            data-vendor-offer-id="{{ $vendorOfferId }}"
                                                            data-vendor-name="{{ $vendor['vendor_name'] }}"
                                                            data-comparison-id="{{ $comparison['comparison_id'] }}"
                                                            data-agreement-id="{{ $agreementId }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                @endif
                                            </div>
                                        </th>
                                        @endforeach
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($comparison['items']) && is_array($comparison['items']) && count($comparison['items']) > 0)
                                    @foreach($comparison['items'] as $item)
                                    <tr class="border-t border-[#EEF1F4]">
                                        <td class="p-3 text-xs text-[#666666]">{{ $item['procurement_item_name'] }}</td>
                                        <td class="p-3 text-xs text-center text-[#666666]">{{ $item['quantity'] }}</td>
                                        <td class="p-3 text-xs text-[#666666]">
                                            <div class="text-sm font-medium">Rp {{ number_format(floatval($item['estimated_unit_price']) * intval($item['quantity']), 0, ',', '.') }}</div>
                                            <span class="text-xs text-gray-500">@Rp {{ number_format(floatval($item['estimated_unit_price']), 0, ',', '.') }}</span>
                                        </td>

                                        @if($hasVendors)
                                            @foreach($uniqueVendors as $vendor)
                                                @php
                                                $vendorOffer = null;
                                                if(isset($item['vendor_offers']) && is_array($item['vendor_offers'])) {
                                                    foreach($item['vendor_offers'] as $offer) {
                                                        if(isset($offer['vendor']) && $offer['vendor']['vendor_id'] === $vendor['vendor_id']) {
                                                            $vendorOffer = $offer;
                                                            break;
                                                        }
                                                    }
                                                }
                                                @endphp

                                                <td class="p-3 text-xs text-[#666666]">
                                                    @if($vendorOffer)
                                                    <div class="text-sm font-medium">Rp {{ number_format(floatval($vendorOffer['unit_price']) * intval($item['quantity']), 0, ',', '.') }}</div>
                                                    <span class="text-xs text-gray-500">@Rp {{ number_format(floatval($vendorOffer['unit_price']), 0, ',', '.') }}</span>
                                                    @else
                                                    <div class="text-sm font-medium text-gray-400">Tidak Tersedia</div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        @endif
                                    </tr>
                                    @endforeach
                                @else
                                <tr class="border-t border-[#EEF1F4]">
                                    <td colspan="{{ $hasVendors ? (3 + count($uniqueVendors)) : 3 }}" class="p-3 text-center text-[#666666]">Tidak ada item tersedia</td>
                                </tr>
                                @endif

                                <!-- Payment Terms Row -->
                                @if($hasVendors)
                                <tr class="border-t border-[#EEF1F4] bg-[#E9ECF6]">
                                    <td class="p-3 text-xs font-medium text-left text-[#213268]">Syarat Pembayaran</td>
                                    <td colspan="2" class="p-3 text-xs text-[#666666]"></td>
                                    @foreach($uniqueVendors as $vendor)
                                    @php
                                        // Find vendor payment terms for this vendor
                                        $vendorPaymentTerms = null;

                                        // Look through all items and their offers
                                        if(isset($comparison['items']) && is_array($comparison['items'])) {
                                            foreach($comparison['items'] as $item) {
                                                if(isset($item['vendor_offers']) && is_array($item['vendor_offers'])) {
                                                    foreach($item['vendor_offers'] as $offer) {
                                                        if(isset($offer['vendor']) && $offer['vendor']['vendor_id'] === $vendor['vendor_id']) {
                                                            // Found an offer from this vendor
                                                            // Try to get payment terms from vendor offer directly first
                                                            $vendorPaymentTerms = $offer['payment_terms'] ?? null;

                                                            // If not found, try to get it from the agreement object
                                                            if(!$vendorPaymentTerms && isset($offer['agreement']) && isset($offer['agreement']['payment_terms'])) {
                                                                $vendorPaymentTerms = $offer['agreement']['payment_terms'];
                                                            }

                                                            break 2; // Exit both loops
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <td class="p-3 text-xs text-[#666666]">{{ $vendorPaymentTerms ?: 'Tidak ada data' }}</td>
                                    @endforeach
                                </tr>

                                <!-- Delivery Terms Row -->
                                <tr class="border-t border-[#EEF1F4] bg-[#E9ECF6]">
                                    <td class="p-3 text-xs font-medium text-left text-[#213268]">Syarat Pengiriman</td>
                                    <td colspan="2" class="p-3 text-xs text-[#666666]"></td>
                                    @foreach($uniqueVendors as $vendor)
                                    @php
                                        // Find vendor delivery terms for this vendor
                                        $vendorDeliveryTerms = null;

                                        // Look through all items and their offers
                                        if(isset($comparison['items']) && is_array($comparison['items'])) {
                                            foreach($comparison['items'] as $item) {
                                                if(isset($item['vendor_offers']) && is_array($item['vendor_offers'])) {
                                                    foreach($item['vendor_offers'] as $offer) {
                                                        if(isset($offer['vendor']) && $offer['vendor']['vendor_id'] === $vendor['vendor_id']) {
                                                            // Found an offer from this vendor
                                                            // Try to get delivery terms from vendor offer directly first
                                                            $vendorDeliveryTerms = $offer['delivery_terms'] ?? null;

                                                            // If not found, try to get it from the agreement object
                                                            if(!$vendorDeliveryTerms && isset($offer['agreement']) && isset($offer['agreement']['delivery_terms'])) {
                                                                $vendorDeliveryTerms = $offer['agreement']['delivery_terms'];
                                                            }

                                                            break 2; // Exit both loops
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <td class="p-3 text-xs text-[#666666]">{{ $vendorDeliveryTerms ?: 'Tidak ada data' }}</td>
                                    @endforeach
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                @if(!isset($comparison['status']) || $comparison['status'] !== 'Completed')
                <div class="flex flex-wrap gap-4 mt-8">
                        <!-- Complete button only shown when not yet completed -->
                        <button id="completeBtn" type="button"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg text-base hover:bg-green-700 transform active:scale-[0.98] transition-all duration-200 uppercase"
                                data-comparison-id="{{ $comparison['comparison_id'] ?? $id }}">
                        SELESAI
                        </button>
                </div>
                @endif
                @else
                <!-- Not Found State -->
                <div class="flex flex-col items-center justify-center py-8">
                    <svg class="w-16 h-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Perbandingan Harga Tidak Ditemukan</h2>
                    <p class="text-gray-600 mb-8">{{ $error ?? 'Data perbandingan harga yang diminta tidak dapat ditemukan atau telah dihapus.' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Toast container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>

<!-- Delete Vendor Modal -->
<div id="deleteVendorModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="deleteVendorModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">HAPUS PENAWARAN VENDOR</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form id="deleteVendorForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="comparison_id" id="delete_comparison_id">
                    <div class="p-6">
                        <div class="space-y-6 max-w-[400px] mx-auto">
                            <div class="flex flex-col items-center">
                                <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus penawaran dari vendor ini? Tindakan ini tidak dapat dibatalkan.</p>
                                <p id="deleteVendorName" class="text-base font-semibold text-center mt-2"></p>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                    Batal
                                </button>
                                <button type="submit" class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to display toast notifications
        function showToast(message, type = 'success') {
            // Create toast container if it doesn't exist
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
                document.body.appendChild(toastContainer);
            }

            // Create notification element
            const toast = document.createElement('div');
            toast.className = 'p-4 rounded shadow-md animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';

            if (type === 'success') {
                toast.classList.add('bg-green-100', 'border-l-4', 'border-green-500', 'text-green-700');
                toast.innerHTML = `
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
                toast.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700');
                toast.innerHTML = `
                    <div class="flex items-start">
                        <div class="py-1">
                            <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">Error!</p>
                            <div>${message}</div>
                        </div>
                        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                    </div>
                `;
            }

            // Add to container
            toastContainer.appendChild(toast);

            // Auto-remove notification after 5 seconds
            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        }

        // Modal functions
        function openModal(modal, content) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
            }, 10);
        }

        function closeModal(modal, content) {
            content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
            content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Handle delete vendor button clicks
        const deleteButtons = document.querySelectorAll('.delete-vendor-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const agreementId = this.getAttribute('data-agreement-id');
                const vendorName = this.getAttribute('data-vendor-name');
                const comparisonId = this.getAttribute('data-comparison-id');

                if (!agreementId) {
                    showToast('Error: ID Perjanjian tidak ditemukan. Silakan hubungi administrator.', 'error');
                    return;
                }

                // Get modal elements
                const deleteModal = document.getElementById('deleteVendorModal');
                const deleteContent = document.getElementById('deleteVendorModalContent');
                const deleteForm = document.getElementById('deleteVendorForm');
                const deleteVendorName = document.getElementById('deleteVendorName');
                const deleteComparisonId = document.getElementById('delete_comparison_id');

                if (deleteModal && deleteContent && deleteForm) {
                    // Set form action and vendor name
                    deleteForm.action = `/procurement/price-comparison/vendor-offer/${agreementId}`;
                    deleteVendorName.textContent = vendorName;
                    deleteComparisonId.value = comparisonId;

                    // Open modal
                    openModal(deleteModal, deleteContent);
                }
            });
        });

        // Handle close modal events
        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('[id$="Modal"]');
                const content = modal.querySelector('[id$="ModalContent"]');
                if (modal && content) {
                    closeModal(modal, content);
                }
            });
        });

        // Handle click outside modal
        const modals = document.querySelectorAll('[id$="Modal"]');
        modals.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    const content = this.querySelector('[id$="ModalContent"]');
                    if (content) {
                        closeModal(this, content);
                    }
                }
            });
        });

        // Handle vendor deletion form submission
        const deleteVendorForm = document.getElementById('deleteVendorForm');
        if (deleteVendorForm) {
            deleteVendorForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const comparisonId = document.getElementById('delete_comparison_id').value;
                const formData = {
                    comparison_id: comparisonId
                };

                fetch(this.action, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    // Close the modal
                    const modal = document.getElementById('deleteVendorModal');
                    const content = document.getElementById('deleteVendorModalContent');
                    closeModal(modal, content);

                    if (data.success) {
                        showToast(data.message || 'Penawaran vendor berhasil dihapus', 'success');
                        // Reload page after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showToast(data.errors?.general || 'Gagal menghapus penawaran vendor', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan saat menghapus penawaran vendor', 'error');

                    // Close the modal
                    const modal = document.getElementById('deleteVendorModal');
                    const content = document.getElementById('deleteVendorModalContent');
                    closeModal(modal, content);
                });
            });
        }

        // Success message should be hidden by default
        const successMessage = document.getElementById('successMessage');
        if (successMessage) {
            successMessage.classList.add('hidden');
        }

        // Handle Complete button
        const completeBtn = document.getElementById('completeBtn');

        if (completeBtn) {
            let isSubmitting = false;

            completeBtn.addEventListener('click', function() {
                // Prevent multiple submissions
                if (isSubmitting) {
                    return;
                }

                if (!confirm('Apakah Anda yakin ingin menyelesaikan perbandingan harga ini? Tindakan ini tidak dapat dibatalkan.')) {
                    return;
                }

                // Set submitting flag and update button
                isSubmitting = true;
                const originalText = completeBtn.innerHTML;
                completeBtn.disabled = true;
                completeBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    MENYELESAIKAN...
                `;

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // Send the request to complete the price comparison
                fetch('{{ url("procurement/price-comparison/{$id}/complete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showToast(data.message || 'Perbandingan harga telah berhasil diselesaikan!', 'success');

                        // Reload the page after a short delay to show the updated status
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Reset button and show error
                        isSubmitting = false;
                        completeBtn.disabled = false;
                        completeBtn.innerHTML = originalText;

                        showToast(data.errors?.general || 'Gagal menyelesaikan perbandingan harga', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error completing price comparison:', error);

                    // Reset button and show error
                    isSubmitting = false;
                    completeBtn.disabled = false;
                    completeBtn.innerHTML = originalText;

                    showToast('Terjadi kesalahan saat menyelesaikan perbandingan harga', 'error');
                });
            });
        }
    });
</script>
@endpush
