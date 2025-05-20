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
                            @if($procurement['status'] == 'Submitted' && hasPermission('procurement:approve:manager'))
                            <button id="managerApprovalBtn" type="button" class="px-6 py-2 border border-green-600 text-green-600 rounded-lg text-base hover:bg-green-50 transform active:scale-[0.98] transition-all duration-200">
                                SETUJU
                            </button>
                            @endif

                            @if($procurement['status'] == 'Under Review' && $procurement['estimated_grand_total'] > 50000000 && hasPermission('procurement:approve:director'))
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

                    @if($hasIncompleteComparison && hasPermission('price-comparison:create'))
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Add animate.css CDN for SweetAlert animations if not already present
    if (!document.getElementById('animate-css')) {
        const animateLink = document.createElement('link');
        animateLink.id = 'animate-css';
        animateLink.rel = 'stylesheet';
        animateLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';
        document.head.appendChild(animateLink);
    }
    // Add custom SweetAlert styles if not already present
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

    function showSweetAlert(message, type = 'success', options = {}) {
        const iconMap = {
            success: 'success',
            error: 'error',
            warning: 'warning',
            info: 'info',
            question: 'question'
        };

        // Default options
        const defaultOptions = {
            title: type === 'success' ? 'Berhasil!' : type === 'error' ? 'Gagal!' : 'Informasi',
            html: message,
            icon: iconMap[type] || 'info',
            confirmButtonText: options.confirmButtonText || 'OK',
            confirmButtonColor: options.confirmButtonColor || '#213268',
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

        // Merge with custom options
        const mergedOptions = { ...defaultOptions, ...options };

        // Add specific options based on alert type
        if (type === 'success' && options.timer === undefined) {
            // Auto close success messages after 2.5 seconds
            mergedOptions.timer = 2500;
            mergedOptions.timerProgressBar = true;
        } else if (type === 'error' && options.showCloseButton === undefined) {
            // Make error alerts more prominent
            mergedOptions.confirmButtonColor = '#d33';
            mergedOptions.showCloseButton = true;
        }

        // Fire the alert and return the Promise for chaining
        return Swal.fire(mergedOptions);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const procurementId = {{ $procurement['procurement_id'] ?? 'null' }};
        if (!procurementId) {
            return; // Exit early if procurement ID is not available
        }
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Permission-based initialization
        @if(!hasPermission('procurement:approve:manager'))
        // Hide manager approval button if user doesn't have permission
        const managerApprovalBtn = document.getElementById('managerApprovalBtn');
        if (managerApprovalBtn) managerApprovalBtn.style.display = 'none';
        @endif

        @if(!hasPermission('procurement:approve:director'))
        // Hide director approval button if user doesn't have permission
        const directorApprovalBtn = document.getElementById('directorApprovalBtn');
        if (directorApprovalBtn) directorApprovalBtn.style.display = 'none';
        @endif

        @if(!hasPermission('price-comparison:create'))
        // Hide create comparison button if user doesn't have permission
        const createComparisonBtn = document.getElementById('createComparisonBtn');
        if (createComparisonBtn) createComparisonBtn.style.display = 'none';
        @endif

        // Manager Approval Button
        const managerApprovalBtn = document.getElementById('managerApprovalBtn');
        if (managerApprovalBtn) {
            managerApprovalBtn.addEventListener('click', function() {
                showSweetAlert('Apakah Anda yakin ingin menyetujui pengadaan ini sebagai manajer?', 'warning', {
                    title: 'Konfirmasi Persetujuan',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Setujui',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#213268',
                    cancelButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                    const managerApprovalUrl = "{{ route('procurement.manager-approval', ['id' => ':id']) }}".replace(':id', procurementId);
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
                                if (response.status === 405) throw new Error('Route not configured for POST method');
                                return response.text().then(text => { try { return JSON.parse(text); } catch (e) { throw new Error('Invalid response format'); } });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const grandTotal = {{ $procurement['estimated_grand_total'] ?? 0 }};
                            let message = data.message || 'Pengadaan telah disetujui oleh manajer';
                            if (grandTotal <= 50000000) {
                                message += '. Persetujuan direktur tidak diperlukan karena total kurang dari atau sama dengan 50 juta.';
                            }
                                showSweetAlert(message, 'success', {
                                    timer: 1500,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                    willClose: () => {
                                        window.location.reload();
                                    }
                                });
                        } else {
                                const errorMsg = data.errors ? Object.values(data.errors).flat().join('<br>') : 'Gagal menyetujui pengadaan';
                                showSweetAlert(errorMsg, 'error');
                        }
                    })
                    .catch(error => {
                            showSweetAlert('Terjadi kesalahan saat memproses permintaan Anda', 'error');
                    });
                }
                });
            });
        }

        // Director Approval Button
        const directorApprovalBtn = document.getElementById('directorApprovalBtn');
        if (directorApprovalBtn) {
            directorApprovalBtn.addEventListener('click', function() {
                showSweetAlert('Apakah Anda yakin ingin menyetujui pengadaan ini sebagai direktur?', 'warning', {
                    title: 'Konfirmasi Persetujuan',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Setujui',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#213268',
                    cancelButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                    const directorApprovalUrl = "{{ route('procurement.director-approval', ['id' => ':id']) }}".replace(':id', procurementId);
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
                                if (response.status === 405) throw new Error('Route not configured for POST method');
                                return response.text().then(text => { try { return JSON.parse(text); } catch (e) { throw new Error('Invalid response format'); } });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                                showSweetAlert(data.message || 'Pengadaan telah disetujui oleh direktur', 'success', {
                                    timer: 1500,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                    willClose: () => {
                                        window.location.reload();
                                    }
                                });
                        } else {
                                const errorMsg = data.errors ? Object.values(data.errors).flat().join('<br>') : 'Gagal menyetujui pengadaan';
                                showSweetAlert(errorMsg, 'error');
                        }
                    })
                    .catch(error => {
                            showSweetAlert('Terjadi kesalahan saat memproses permintaan Anda', 'error');
                    });
                }
                });
            });
        }

        // Rejection Modal
        const rejectBtn = document.getElementById('rejectBtn');
        const rejectModal = document.getElementById('rejectModal');
        const rejectModalContent = document.getElementById('rejectModalContent');
        const rejectForm = document.getElementById('rejectForm');
        const rejectionReasonField = document.getElementById('rejection_reason');
        if (rejectBtn) {
            rejectBtn.addEventListener('click', function() {
                rejectModal.classList.remove('hidden');
                setTimeout(() => {
                    rejectModalContent.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                    rejectModalContent.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                }, 10);
            });
        }

        // Submit rejection
        rejectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Validate the rejection reason
            if (!rejectionReasonField.value.trim()) {
                showSweetAlert('Silakan berikan alasan penolakan', 'error');
                return;
            }
            const rejectionReason = rejectionReasonField.value;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<div class="flex items-center justify-center"><div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div><span>Memproses...</span></div>`;
            const rejectUrl = "{{ route('procurement.reject', ['id' => ':id']) }}".replace(':id', procurementId);
            fetch(rejectUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ rejected_reason: rejectionReason })
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 405) throw new Error('Route not configured for POST method');
                    return response.text().then(text => { try { return JSON.parse(text); } catch (e) { throw new Error('Invalid response format'); } });
                }
                return response.json();
            })
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                if (data.success) {
                    rejectModal.classList.add('hidden');
                    showSweetAlert(data.message || 'Pengadaan berhasil ditolak', 'success', {
                        timer: 1500,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        willClose: () => {
                            window.location.reload();
                        }
                    });
                } else {
                    const errorMsg = data.errors ? Object.values(data.errors).flat().join('<br>') : 'Gagal menolak pengadaan';
                    showSweetAlert(errorMsg, 'error');
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                showSweetAlert('Terjadi kesalahan saat memproses permintaan Anda', 'error');
            });
        });

        // Create Price Comparison
        const createComparisonBtn = document.getElementById('createComparisonBtn');
        const comparisonTitleInput = document.getElementById('comparison_title');

        if (createComparisonBtn) {
            createComparisonBtn.addEventListener('click', function() {
                // Validate input
                if (!comparisonTitleInput.value.trim()) {
                    showSweetAlert('Silakan masukkan judul perbandingan harga', 'error');
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
                        showSweetAlert(data.message || 'Perbandingan harga berhasil dibuat', 'success', {
                            timer: 1500,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            willClose: () => {
                        // If there's a redirect URL, navigate to it
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            // Otherwise just reload the page
                                window.location.reload();
                                }
                        }
                        });
                    } else {
                        const errorMsg = data.errors ? Object.values(data.errors).flat().join('<br>') : 'Gagal membuat perbandingan harga';
                        showSweetAlert(errorMsg, 'error');
                    }
                })
                .catch(error => {
                    // Reset button state
                    createComparisonBtn.disabled = false;
                    createComparisonBtn.innerHTML = originalBtnText;

                    console.error('Error:', error);
                    showSweetAlert('Terjadi kesalahan saat memproses permintaan Anda', 'error');
                });
            });

            // Add input event listener to clear error styling when typing
            comparisonTitleInput.addEventListener('input', function() {
                this.classList.remove('border-red-500');
            });
        }

        // Show SweetAlert notifications for session messages on page load
        @if(session('success'))
            showSweetAlert("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showSweetAlert("{{ session('error') }}", 'error');
        @endif
    });
</script>
@endpush
