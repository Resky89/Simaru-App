@extends('Layout.app')

@section('title', 'Detail Permintaan')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="h-full space-y-4 md:space-y-6">
    <!-- Request Detail Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center">
                        <a href="{{ route('procurement.request') }}" class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DETAIL PERMINTAAN</h1>
                    </div>
                </div>

                @if(isset($procurement) && !empty($procurement))
                <!-- Request Details -->
                <div class="grid grid-cols-1 gap-5">
                    <!-- Request Number -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Nomor Permintaan</p>
                        <p class="text-[#666666]">: <span id="requestNumber">{{ $procurement['procurement_code'] }}</span></p>
                    </div>

                    <!-- Request Name -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Judul Permintaan</p>
                        <p class="text-[#666666]">: <span id="requestName">{{ $procurement['title'] }}</span></p>
                    </div>

                    <!-- User Input -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Pemohon</p>
                        <p class="text-[#666666]">: <span id="userInput">{{ $procurement['requester']['employee_number'] }}</span></p>
                    </div>

                    <!-- Input Date -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Tanggal Permintaan</p>
                        <p class="text-[#666666]">: <span id="inputDate">{{ \Carbon\Carbon::parse($procurement['request_date'])->locale('id')->translatedFormat('d F Y') }}</span></p>
                    </div>

                    <!-- Priority -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Prioritas</p>
                        <p class="text-[#666666]">: <span>{{ $procurement['priority'] }}</span></p>
                    </div>

                    <!-- Justification -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Justifikasi</p>
                        <p class="text-[#666666]">: <span>{{ $procurement['justification'] }}</span></p>
                    </div>

                    <!-- Notes (if available) -->
                    @if($procurement['notes'])
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Catatan</p>
                        <p class="text-[#666666]">: <span>{{ $procurement['notes'] }}</span></p>
                    </div>
                    @endif

                    <!-- Status -->
                    <div class="flex items-start gap-2">
                        <p class="w-40 text-[#666666] font-medium">Status</p>
                        <p class="text-[#666666]">:
                            <span class="px-2 py-1 rounded-full text-xs inline-block ml-1
                                @if($procurement['status'] == 'Submitted') bg-blue-100 text-blue-800
                                @elseif($procurement['status'] == 'Under Review') bg-yellow-100 text-yellow-800
                                @elseif($procurement['status'] == 'Approved') bg-green-100 text-green-800
                                @elseif($procurement['status'] == 'Rejected') bg-red-100 text-red-800
                                @elseif($procurement['status'] == 'Procured') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                @if($procurement['status'] == 'Submitted') Diajukan
                                @elseif($procurement['status'] == 'Under Review') Dalam Peninjauan
                                @elseif($procurement['status'] == 'Approved') Disetujui
                                @elseif($procurement['status'] == 'Rejected') Ditolak
                                @elseif($procurement['status'] == 'Procured') Dibeli
                                @else {{ $procurement['status'] }}
                                @endif
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Item List -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-[#666666]">DAFTAR ASET</h2>

                        <!-- Action Buttons - Repositioned -->
                        <div class="flex flex-wrap gap-3">
                            @if($procurement['status'] == 'Submitted')
                            <button id="managerApprovalBtn" type="button" class="px-6 py-2 border border-green-600 text-green-600 rounded-lg text-base hover:bg-green-50 transform active:scale-[0.98] transition-all duration-200">
                                SETUJU
                            </button>
                            @endif

                            @if($procurement['status'] == 'Under Review' && $procurement['estimated_grand_total'] > 50000000)
                            <button id="directorApprovalBtn" type="button" class="px-6 py-2 border border-green-600 text-green-600 rounded-lg text-base hover:bg-green-50 transform active:scale-[0.98] transition-all duration-200">
                                SETUJU
                            </button>
                            @endif

                            @if($procurement['status'] == 'Submitted' || $procurement['status'] == 'Under Review')
                            <button id="rejectBtn" type="button" class="px-6 py-2 border border-red-600 text-red-600 rounded-lg text-base hover:bg-red-50 transform active:scale-[0.98] transition-all duration-200">
                                TOLAK
                            </button>
                            @endif
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Nama Aset</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Spesifikasi</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-center">Jumlah</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Harga Satuan</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Total</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-center">Perbandingan Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($procurement['details'] as $detail)
                                <tr class="border-t border-[#EEF1F4]">
                                    <td class="p-3 text-sm text-[#666666]">{{ $detail['asset_name'] }}</td>
                                    <td class="p-3 text-sm text-[#666666]">{{ $detail['specifications'] }}</td>
                                    <td class="p-3 text-sm text-center text-[#666666]">{{ $detail['quantity'] }}</td>
                                    <td class="p-3 text-sm text-left text-[#666666]">{{ number_format($detail['estimated_unit_price'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-sm text-left text-[#666666]">{{ number_format($detail['estimated_total_price'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-sm text-center">
                                        <span class="px-2 py-1 rounded-full text-sm inline-block
                                            @if($detail['status_price_comparison'] == 'Waiting') bg-red-100 text-red-800
                                            @elseif($detail['status_price_comparison'] == 'In Progress') bg-blue-100 text-blue-800
                                            @elseif($detail['status_price_comparison'] == 'Done') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            @if($detail['status_price_comparison'] == 'Waiting') Menunggu
                                            @elseif($detail['status_price_comparison'] == 'In Progress') Dalam Proses
                                            @elseif($detail['status_price_comparison'] == 'Done') Selesai
                                            @else {{ $detail['status_price_comparison'] }}
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach

                                <!-- Grand Total -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td colspan="4" class="p-3 text-sm font-medium text-right text-[#666666]">Total Keseluruhan</td>
                                    <td class="p-3 text-sm font-medium text-left text-[#666666]">{{ number_format($procurement['estimated_grand_total'], 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($procurement['status'] == 'Approved')
                    @php
                        $hasIncompleteComparison = false;
                        foreach($procurement['details'] as $detail) {
                            if($detail['status_price_comparison'] != 'Done') {
                                $hasIncompleteComparison = true;
                                break;
                            }
                        }
                    @endphp

                    @if($hasIncompleteComparison)
                    <!-- Comparison Title -->
                    <div class="space-y-2">
                        <label class="block text-base font-semibold text-[#666666]">Buat Perbandingan Harga</label>
                        <input type="text" id="comparison_title"
                            class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                            placeholder="Masukkan judul perbandingan">
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex gap-4 mt-8">
                        <button id="createComparisonBtn" type="button" class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                            BUAT PERBANDINGAN HARGA
                        </button>
                    </div>
                    @endif
                @endif
                @else
                <!-- Not Found State -->
                <div class="flex flex-col items-center justify-center py-8">
                    <svg class="w-16 h-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Permintaan Tidak Ditemukan</h2>
                    <p class="text-gray-600 mb-8">{{ $error ?? 'Data permintaan yang diminta tidak dapat ditemukan atau telah dihapus.' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                id="rejectModalContent">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 pb-0">
                    <h2 class="text-xl sm:text-2xl font-semibold text-[#213268]">TOLAK PENGADAAN</h2>
                    <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <form id="rejectForm" novalidate>
                        <div class="space-y-4 max-w-[400px] mx-auto">
                            <!-- Rejection Reason Input -->
                            <div class="space-y-2">
                                <label class="block text-base font-semibold text-[#666666]">
                                    Alasan Penolakan <span class="text-red-500">*</span>
                                </label>
                                <textarea id="rejection_reason" name="rejected_reason" rows="4" required
                                    class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                    placeholder="Silakan berikan alasan penolakan"></textarea>
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Alasan penolakan harus diisi</div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-3 mt-6">
                                <button type="button" class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                    Batal
                                </button>
                                <button type="submit" class="w-1/2 h-[45px] bg-red-600 text-white rounded-lg text-base hover:bg-red-700 transform active:scale-[0.98] transition-all duration-200">
                                    Tolak
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const procurementId = {{ $procurement['procurement_id'] ?? 'null' }};
        if (!procurementId) {
            return; // Exit early if procurement ID is not available
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Define the showToast function first
        window.showToast = function(message, type = 'success') {
            // Create the notification element
            const notification = document.createElement('div');
            notification.id = type + 'Notification' + Date.now(); // Unique ID to allow multiple notifications
            notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
            notification.role = 'alert';

            // Check if message contains HTML
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
                        <div class="flex-1">
                            <p class="font-bold">Berhasil!</p>
                            <div>${message}</div>
                        </div>
                        <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                    </div>
                `;
            } else {
                notification.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700', 'overflow-auto');

                // Structure for the notification
                const wrapper = document.createElement('div');
                wrapper.className = 'flex items-start';

                // Icon container
                const iconContainer = document.createElement('div');
                iconContainer.className = 'py-1 flex-shrink-0';
                iconContainer.innerHTML = `
                    <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                `;

                // Content container
                const contentContainer = document.createElement('div');
                contentContainer.className = 'flex-grow max-w-xs sm:max-w-sm md:max-w-md';

                // Title
                const title = document.createElement('p');
                title.className = 'font-bold';
                title.textContent = 'Gagal!';
                contentContainer.appendChild(title);

                // Message container
                const messageContainer = document.createElement('div');
                messageContainer.className = 'error-message';

                // Handle HTML content
                if (hasHTML) {
                    messageContainer.innerHTML = message;
                } else {
                    messageContainer.textContent = message;
                }

                contentContainer.appendChild(messageContainer);

                // Close button
                const closeBtn = document.createElement('span');
                closeBtn.className = 'ml-4 cursor-pointer flex-shrink-0';
                closeBtn.textContent = '×';
                closeBtn.onclick = function() {
                    notification.remove();
                };

                // Assemble the notification
                wrapper.appendChild(iconContainer);
                wrapper.appendChild(contentContainer);
                wrapper.appendChild(closeBtn);
                notification.appendChild(wrapper);
            }

            // Add to document
            document.body.appendChild(notification);

            // Auto-remove notification after 5 seconds for success, 10 seconds for error
            setTimeout(() => {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => notification.remove(), 500);
            }, type === 'success' ? 5000 : 10000);
        };

        // Add slide-in animation and styling for error messages to CSS
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

        // Modal functionality
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

        // Manager Approval Button
        const managerApprovalBtn = document.getElementById('managerApprovalBtn');
        if (managerApprovalBtn) {
            managerApprovalBtn.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menyetujui pengadaan ini sebagai manajer?')) {
                    const managerApprovalUrl = "{{ route('procurement.manager-approval', ['id' => ':id']) }}".replace(':id', procurementId);
                    console.log('Sending manager approval request to:', managerApprovalUrl);

                    fetch(managerApprovalUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            if (response.status === 405) {
                                console.error('Method Not Allowed Error: The server rejected the POST request. Route might be misconfigured.');
                                throw new Error('Route not configured for POST method');
                            }
                            return response.text().then(text => {
                                try {
                                    // Try to parse as JSON
                                    return JSON.parse(text);
                                } catch (e) {
                                    // If not JSON, log the response and throw error
                                    console.error('Server response was not JSON:', text);
                                    throw new Error('Invalid response format');
                                }
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const grandTotal = {{ $procurement['estimated_grand_total'] ?? 0 }};
                            let message = data.message || 'Pengadaan telah disetujui oleh manajer';

                            // If grand total is <= 50 million, auto-approve without director approval
                            if (grandTotal <= 50000000) {
                                message += '. Persetujuan direktur tidak diperlukan karena total kurang dari atau sama dengan 50 juta.';
                            }

                            showToast(message, 'success');
                            window.location.reload();
                        } else {
                            const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : 'Gagal menyetujui pengadaan';
                            showToast(errorMsg, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat memproses permintaan Anda', 'error');
                    });
                }
            });
        }

        // Director Approval Button
        const directorApprovalBtn = document.getElementById('directorApprovalBtn');
        if (directorApprovalBtn) {
            directorApprovalBtn.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menyetujui pengadaan ini sebagai direktur?')) {
                    const directorApprovalUrl = "{{ route('procurement.director-approval', ['id' => ':id']) }}".replace(':id', procurementId);
                    console.log('Sending director approval request to:', directorApprovalUrl);

                    fetch(directorApprovalUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            if (response.status === 405) {
                                console.error('Method Not Allowed Error: The server rejected the POST request. Route might be misconfigured.');
                                throw new Error('Route not configured for POST method');
                            }
                            return response.text().then(text => {
                                try {
                                    // Try to parse as JSON
                                    return JSON.parse(text);
                                } catch (e) {
                                    // If not JSON, log the response and throw error
                                    console.error('Server response was not JSON:', text);
                                    throw new Error('Invalid response format');
                                }
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || 'Pengadaan telah disetujui oleh direktur', 'success');
                            window.location.reload();
                        } else {
                            const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : 'Gagal menyetujui pengadaan';
                            showToast(errorMsg, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat memproses permintaan Anda', 'error');
                    });
                }
            });
        }

        // Rejection Modal
        const rejectModal = document.getElementById('rejectModal');
        const rejectModalContent = document.getElementById('rejectModalContent');
        const rejectBtn = document.getElementById('rejectBtn');
        const rejectForm = document.getElementById('rejectForm');
        const rejectionReasonField = document.getElementById('rejection_reason');
        const closeModalBtns = document.querySelectorAll('.close-modal');

        // Open modal
        if (rejectBtn) {
            rejectBtn.addEventListener('click', function() {
                openModal(rejectModal, rejectModalContent);
            });
        }

        // Close modal
        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                closeModal(rejectModal, rejectModalContent);
                // Reset form and validation state
                if (rejectForm) {
                    rejectForm.reset();
                    resetValidation(rejectionReasonField);
                }
            });
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !rejectModal.classList.contains('hidden')) {
                closeModal(rejectModal, rejectModalContent);
                if (rejectForm) {
                    rejectForm.reset();
                    resetValidation(rejectionReasonField);
                }
            }
        });

        // Close when clicking on overlay
        rejectModal.addEventListener('click', function(e) {
            if (e.target === rejectModal || e.target === rejectModal.querySelector('.fixed.inset-0.z-50.overflow-y-auto')) {
                closeModal(rejectModal, rejectModalContent);
                if (rejectForm) {
                    rejectForm.reset();
                    resetValidation(rejectionReasonField);
                }
            }
        });

        // Form validation functions
        function validateField(field) {
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                field.nextElementSibling.classList.remove('hidden');
                return false;
            } else {
                field.classList.remove('border-red-500');
                field.nextElementSibling.classList.add('hidden');
                return true;
            }
        }

        function resetValidation(field) {
            field.classList.remove('border-red-500');
            field.nextElementSibling.classList.add('hidden');
        }

        // Add input event listener to clear error styling when typing
        rejectionReasonField.addEventListener('input', function() {
            resetValidation(this);
        });

        // Submit rejection
        rejectForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate the rejection reason
            const isValid = validateField(rejectionReasonField);
            if (!isValid) {
                showToast('Silakan berikan alasan penolakan', 'error');
                return;
            }

            const rejectionReason = rejectionReasonField.value;

            // Show loading state on button
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <div class="flex items-center justify-center">
                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                    <span>Memproses...</span>
                </div>
            `;

            // Submit the rejection
            const rejectUrl = "{{ route('procurement.reject', ['id' => ':id']) }}".replace(':id', procurementId);
            console.log('Sending reject request to:', rejectUrl);

            fetch(rejectUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    rejected_reason: rejectionReason
                })
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 405) {
                        console.error('Method Not Allowed Error: The server rejected the POST request. Route might be misconfigured.');
                        throw new Error('Route not configured for POST method');
                    }
                    return response.text().then(text => {
                        try {
                            // Try to parse as JSON
                            return JSON.parse(text);
                        } catch (e) {
                            // If not JSON, log the response and throw error
                            console.error('Server response was not JSON:', text);
                            throw new Error('Invalid response format');
                        }
                    });
                }
                return response.json();
            })
            .then(data => {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;

                if (data.success) {
                    // Close the modal
                    closeModal(rejectModal, rejectModalContent);
                    showToast(data.message || 'Pengadaan berhasil ditolak', 'success');

                    // Reload after a short delay to allow toast to be seen
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : 'Gagal menolak pengadaan';
                    showToast(errorMsg, 'error');
                }
            })
            .catch(error => {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;

                console.error('Error:', error);
                showToast('Terjadi kesalahan saat memproses permintaan Anda', 'error');
            });
        });

        // Create Price Comparison
        const createComparisonBtn = document.getElementById('createComparisonBtn');
        const comparisonTitleInput = document.getElementById('comparison_title');

        if (createComparisonBtn) {
            createComparisonBtn.addEventListener('click', function() {
                // Validate input
                if (!comparisonTitleInput.value.trim()) {
                    showToast('Silakan masukkan judul perbandingan harga', 'error');
                    comparisonTitleInput.classList.add('border-red-500');
                    return;
                }

                // Reset validation styling
                comparisonTitleInput.classList.remove('border-red-500');

                // Show loading state
                const originalBtnText = createComparisonBtn.innerHTML;
                createComparisonBtn.disabled = true;
                createComparisonBtn.innerHTML = `
                    <div class="flex items-center justify-center">
                        <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                        <span>Memproses...</span>
                    </div>
                `;

                // Make the API call
                const createUrl = "{{ route('procurement.create-price-comparison-from-detail') }}";

                fetch(createUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        procurement_id: procurementId,
                        title: comparisonTitleInput.value.trim()
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Server response was not JSON:', text);
                                throw new Error('Invalid response format');
                            }
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Reset button state
                    createComparisonBtn.disabled = false;
                    createComparisonBtn.innerHTML = originalBtnText;

                    if (data.success) {
                        showToast(data.message || 'Perbandingan harga berhasil dibuat', 'success');

                        // If there's a redirect URL, navigate to it
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            // Otherwise just reload the page
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    } else {
                        const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : 'Gagal membuat perbandingan harga';
                        showToast(errorMsg, 'error');
                    }
                })
                .catch(error => {
                    // Reset button state
                    createComparisonBtn.disabled = false;
                    createComparisonBtn.innerHTML = originalBtnText;

                    console.error('Error:', error);
                    showToast('Terjadi kesalahan saat memproses permintaan Anda', 'error');
                });
            });

            // Add input event listener to clear error styling when typing
            comparisonTitleInput.addEventListener('input', function() {
                this.classList.remove('border-red-500');
            });
        }
    });
</script>
@endpush



