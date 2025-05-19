@extends('Layout.app')

@section('title', 'Detail Perbandingan')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                        <p class="w-40 text-[#666666] font-medium">Nomor Penawaran</p>
                        <p class="text-[#666666]">: <span id="requestNumber">{{ $comparison['comparison_code'] ?? 'N/A' }}</span></p>
                    </div>

                    <!-- Request Name -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Judul Permintaan</p>
                        <p class="text-[#666666]">: <span id="requestName">{{ $comparison['title'] ?? 'N/A' }}</span></p>
                    </div>

                    <!-- User Input -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Dibuat oleh</p>
                        <p class="text-[#666666]">: <span id="userInput">{{ isset($comparison['creator']) ? $comparison['creator']['employee_number'] : 'N/A' }}</span></p>
                    </div>

                    <!-- Input Date -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Tanggal Penawaran</p>
                        <p class="text-[#666666]">:
                            @if(isset($comparison['created_at']))
                                @php
                                    $date = \Carbon\Carbon::parse($comparison['created_at']);
                                    $monthsIndonesian = [
                                        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                    ];
                                    echo $date->format('d') . ' ' . $monthsIndonesian[$date->format('n')] . ' ' . $date->format('Y');
                                @endphp
                            @else
                                N/A
                            @endif
                        </p>
                    </div>

                    <!-- Status -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Status</p>
                        <p class="text-[#666666]">:
                            <span class="px-2 py-1 rounded-full text-xs inline-block ml-1
                                @if(isset($comparison['status']) && strtolower($comparison['status']) == 'completed') bg-green-100 text-green-800
                                @elseif(isset($comparison['status']) && strtolower($comparison['status']) == 'in progress') bg-blue-100 text-blue-800
                                @elseif(isset($comparison['status']) && strtolower($comparison['status']) == 'draft') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                @if(isset($comparison['status']))
                                    @if(strtolower($comparison['status']) == 'completed')
                                        Selesai
                                    @elseif(strtolower($comparison['status']) == 'in progress')
                                        Dalam Proses
                                    @elseif(strtolower($comparison['status']) == 'draft')
                                        Draft
                                    @else
                                        {{ $comparison['status'] }}
                                    @endif
                                @else
                                    Tidak Ada
                                @endif
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Item List -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-[#666666]">Perbandingan Harga</h2>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Nama Aset</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-center">Jumlah</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Perkiraan Harga</th>

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
                                        <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">
                                            <div class="flex items-center justify-between">
                                                <span>{{ $vendor['vendor_name'] }}</span>
                                                @if(!isset($comparison['status']) || $comparison['status'] !== 'Completed')
                                                <div class="flex items-center gap-2">
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

                                                    <form id="edit-vendor-form-{{ $vendor['vendor_id'] }}" action="{{ route('procurement.form-vendor-comparison', ['id' => $comparison['comparison_id']]) }}" method="get" class="flex items-center">
                                                        <input type="hidden" name="agreement_id" value="{{ $agreementId }}">

                                                        @foreach($vendorOfferIds as $itemId => $offerId)
                                                            <input type="hidden" name="vo_{{ $itemId }}" value="{{ $offerId }}">
                                                        @endforeach

                                                        <button type="submit" class="p-1 text-white hover:text-gray-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    <button class="p-1 text-white hover:text-gray-200 delete-vendor-btn"
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
                                        <td class="p-3 text-sm text-[#666666]">{{ $item['procurement_item_name'] }}</td>
                                        <td class="p-3 text-sm text-center text-[#666666]">{{ $item['quantity'] }}</td>
                                        <td class="p-3 text-sm text-[#666666]">
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

                                                <td class="p-3 text-sm text-[#666666]">
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
                                    <td class="p-3 text-sm font-medium text-left text-[#213268]">Syarat Pembayaran</td>
                                    <td colspan="2" class="p-3 text-sm text-[#666666]"></td>
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
                                    <td class="p-3 text-sm text-[#666666]">{{ $vendorPaymentTerms ?: 'Tidak ada data' }}</td>
                                    @endforeach
                                </tr>

                                <!-- Delivery Terms Row -->
                                <tr class="border-t border-[#EEF1F4] bg-[#E9ECF6]">
                                    <td class="p-3 text-sm font-medium text-left text-[#213268]">Syarat Pengiriman</td>
                                    <td colspan="2" class="p-3 text-sm text-[#666666]"></td>
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
                                    <td class="p-3 text-sm text-[#666666]">{{ $vendorDeliveryTerms ?: 'Tidak ada data' }}</td>
                                    @endforeach
                                </tr>

                                <!-- Notes Row -->
                                <tr class="border-t border-[#EEF1F4] bg-[#E9ECF6]">
                                    <td class="p-3 text-sm font-medium text-left text-[#213268]">Catatan</td>
                                    <td colspan="2" class="p-3 text-sm text-[#666666]"></td>
                                    @foreach($uniqueVendors as $vendor)
                                    @php
                                        // Find notes for this vendor
                                        $vendorNotes = null;

                                        // Look through all items and their offers
                                        if(isset($comparison['items']) && is_array($comparison['items'])) {
                                            foreach($comparison['items'] as $item) {
                                                if(isset($item['vendor_offers']) && is_array($item['vendor_offers'])) {
                                                    foreach($item['vendor_offers'] as $offer) {
                                                        if(isset($offer['vendor']) && $offer['vendor']['vendor_id'] === $vendor['vendor_id']) {
                                                            // Found an offer from this vendor
                                                            // Try to get notes from vendor offer directly first
                                                            $vendorNotes = $offer['notes'] ?? null;

                                                            // If not found, try to get it from the agreement object
                                                            if(!$vendorNotes && isset($offer['agreement']) && isset($offer['agreement']['notes'])) {
                                                                $vendorNotes = $offer['agreement']['notes'];
                                                            }

                                                            break 2; // Exit both loops
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <td class="p-3 text-sm text-[#666666]">{{ $vendorNotes ?: 'Tidak ada catatan' }}</td>
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
                        <!-- Complete button only shown when not yet completed, has permission, and has vendors -->
                        @if(hasPermission('price-comparison:complete') && $hasVendors)
                        <button id="completeBtn" type="button"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg text-base hover:bg-green-700 transform active:scale-[0.98] transition-all duration-200 uppercase"
                                data-comparison-id="{{ $comparison['comparison_id'] ?? $id }}">
                        SELESAI
                        </button>
                        @elseif(!$hasVendors)
                        <div class="px-6 py-3 bg-gray-300 text-gray-600 rounded-lg text-base cursor-not-allowed">
                            Tambahkan penawaran vendor terlebih dahulu
                        </div>
                        @endif
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

<!-- Toast container (kept for compatibility) -->
<div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2 hidden"></div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(!hasPermission('price-comparison:complete'))
        // Hide edit buttons if user doesn't have permission
        const completeBtn = document.getElementById('completeBtn');
        if (completeBtn) {
            completeBtn.style.display = 'none';
        }
        @endif

        // Function to show SweetAlert notifications
        function showSweetAlert(message, type = 'success') {
            const iconMap = {
                success: 'success',
                error: 'error',
                warning: 'warning',
                info: 'info',
                question: 'question'
            };

            // Default options
            const options = {
                title: type === 'success' ? 'Berhasil!' : type === 'error' ? 'Gagal!' : 'Informasi',
                html: message,
                icon: iconMap[type] || 'info',
                confirmButtonText: 'OK',
                confirmButtonColor: '#213268',
                customClass: {
                    popup: 'swal-custom-popup',
                    title: 'swal-custom-title',
                    htmlContainer: 'swal-custom-content',
                    confirmButton: 'swal-custom-confirm',
                    cancelButton: 'swal-custom-cancel'
                },
                buttonsStyling: true,
                showClass: {
                    popup: 'animate__animated animate__fadeIn animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOut animate__faster'
                }
            };

            // Add specific options based on alert type
            if (type === 'success') {
                // Auto close success messages after 2.5 seconds
                options.timer = 2500;
                options.timerProgressBar = true;
            } else if (type === 'error') {
                // Make error alerts more prominent
                options.confirmButtonColor = '#d33';
                options.showCloseButton = true;
            }

            // Add custom styles for SweetAlert
            if (!document.getElementById('swal-custom-styles')) {
                const styleTag = document.createElement('style');
                styleTag.id = 'swal-custom-styles';
                styleTag.innerHTML = `
                    /* SweetAlert Custom Styles */
                    .swal2-popup {
                        border-radius: 15px;
                        padding: 1.5rem;
                        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                    }
                    .swal-custom-title {
                        font-weight: 600;
                        font-size: 1.5rem;
                        color: #333;
                    }
                    .swal-custom-content {
                        font-size: 1rem;
                        color: #555;
                        margin-top: 0.5rem;
                    }
                    .swal-custom-content ul {
                        text-align: left;
                        margin-top: 1rem;
                        margin-bottom: 1rem;
                    }
                    .swal-custom-confirm {
                        padding: 0.5rem 1.5rem;
                        font-weight: 500;
                    }
                    .swal-custom-cancel {
                        padding: 0.5rem 1.5rem;
                        font-weight: 500;
                    }
                    .swal2-timer-progress-bar {
                        background: rgba(33, 50, 104, 0.5);
                    }
                    .swal2-icon {
                        margin: 1rem auto;
                    }
                `;
                document.head.appendChild(styleTag);
            }

            // Add animate.css CDN for animations if not already loaded
            if (!document.getElementById('animate-css')) {
                const animateLink = document.createElement('link');
                animateLink.id = 'animate-css';
                animateLink.rel = 'stylesheet';
                animateLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';
                document.head.appendChild(animateLink);
            }

            // Fire the alert
            Swal.fire(options);
        }

        // Backward compatibility - map showToast to showSweetAlert
        function showToast(message, type = 'success') {
            showSweetAlert(message, type);
        }

        // Handle delete vendor button clicks
        const deleteButtons = document.querySelectorAll('.delete-vendor-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const agreementId = this.getAttribute('data-agreement-id');
                const vendorName = this.getAttribute('data-vendor-name');
                const comparisonId = this.getAttribute('data-comparison-id');

                if (!agreementId) {
                    showSweetAlert('Error: ID Perjanjian tidak ditemukan. Silakan hubungi administrator.', 'error');
                    return;
                }

                // Use SweetAlert for confirmation
                Swal.fire({
                    title: 'Hapus Penawaran Vendor',
                    html: `<p>Apakah Anda yakin ingin menghapus penawaran dari vendor <strong>${vendorName}</strong>?</p>
                          <p class="mt-2 text-sm">Tindakan ini tidak dapat dibatalkan.</p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#666',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Form data for deletion
                        const formData = {
                            comparison_id: comparisonId
                        };

                        // Send DELETE request
                        fetch(`/procurement/price-comparison/vendor-offer/${agreementId}`, {
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
                            if (data.success) {
                                showSweetAlert(data.message || 'Penawaran vendor berhasil dihapus', 'success');
                                // Reload page after a short delay
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                showSweetAlert(data.errors?.general || 'Gagal menghapus penawaran vendor', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showSweetAlert('Terjadi kesalahan saat menghapus penawaran vendor', 'error');
                        });
                    }
                });
            });
        });

        // Handle Complete button
        const completeBtn = document.getElementById('completeBtn');

        if (completeBtn) {
            let isSubmitting = false;

            completeBtn.addEventListener('click', function() {
                // Prevent multiple submissions
                if (isSubmitting) {
                    return;
                }

                // Use SweetAlert for confirmation
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menyelesaikan perbandingan harga ini? Tindakan ini tidak dapat dibatalkan.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#213268',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Selesaikan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
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
                                showSweetAlert(data.message || 'Perbandingan harga telah berhasil diselesaikan!', 'success');

                                // Reload the page after a short delay to show the updated status
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                // Reset button and show error
                                isSubmitting = false;
                                completeBtn.disabled = false;
                                completeBtn.innerHTML = originalText;

                                showSweetAlert(data.errors?.general || 'Gagal menyelesaikan perbandingan harga', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error completing price comparison:', error);

                            // Reset button and show error
                            isSubmitting = false;
                            completeBtn.disabled = false;
                            completeBtn.innerHTML = originalText;

                            showSweetAlert('Terjadi kesalahan saat menyelesaikan perbandingan harga', 'error');
                        });
                    }
                });
            });
        }
    });
</script>
@endpush
