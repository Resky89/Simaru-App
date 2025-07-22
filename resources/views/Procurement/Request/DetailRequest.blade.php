@extends('Layout.app')

@section('title', 'Detail Permintaan')

@section('content')
    @include('Layout.loading')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Request Detail Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center">
                            <a href="{{ route('procurement.request') }}"
                                class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                            <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DETAIL PERMINTAAN</h1>
                        </div>
                    </div>

                    @if(isset($procurement) && !empty($procurement))
                        <!-- Request Details - Two Column Layout -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column - Request Information -->
                            <div class="space-y-5">
                                <h2 class="text-lg font-semibold text-[#666666]">Informasi Permintaan</h2>

                                <table class="w-full">
                                    <tbody>
                                        <!-- Request Number -->
                                        <tr>
                                            <td class="py-1 align-top w-48 font-medium text-[#666666]">Nomor Permintaan</td>
                                            <td class="py-1 align-top text-[#666666]">: <span id="requestNumber">{{ $procurement['procurement_code'] }}</span></td>
                                        </tr>

                                        <!-- Request Name -->
                                        <tr>
                                            <td class="py-1 align-top font-medium text-[#666666]">Judul Permintaan</td>
                                            <td class="py-1 align-top text-[#666666]">: <span id="requestName">{{ $procurement['title'] }}</span></td>
                                        </tr>

                                        <!-- Input Date -->
                                        <tr>
                                            <td class="py-1 align-top font-medium text-[#666666]">Tanggal Permintaan</td>
                                            <td class="py-1 align-top text-[#666666]">: <span id="inputDate">{{ \Carbon\Carbon::parse($procurement['request_date'])->locale('id')->translatedFormat('d F Y') }}</span></td>
                                        </tr>

                                        <!-- Priority -->
                                        <tr>
                                            <td class="py-1 align-top font-medium text-[#666666]">Prioritas</td>
                                            <td class="py-1 align-top text-[#666666]">:
                                                <span>
                                                    @if($procurement['priority'] == 'High') Tinggi
                                                    @elseif($procurement['priority'] == 'Medium') Sedang
                                                    @elseif($procurement['priority'] == 'Low') Rendah
                                                    @else {{ $procurement['priority'] }}
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>

                                        <!-- Justification -->
                                        <tr>
                                            <td class="py-1 align-top font-medium text-[#666666]">Justifikasi</td>
                                            <td class="py-1 align-top text-[#666666]">: <span>{{ $procurement['justification'] }}</span></td>
                                        </tr>

                                        <!-- Notes (if available) -->
                                        @if($procurement['notes'])
                                            <tr>
                                                <td class="py-1 align-top font-medium text-[#666666]">Catatan</td>
                                                <td class="py-1 align-top text-[#666666]">: <span>{{ $procurement['notes'] }}</span></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Right Column - Personnel Information -->
                            <div class="space-y-5">
                                <h2 class="text-lg font-semibold text-[#666666]">Informasi Personil & Status</h2>

                                <table class="w-full">
                                    <tbody>
                                        <!-- User Input -->
                                        <tr>
                                            <td class="py-1 align-top w-48 font-medium text-[#666666]">Pemohon</td>
                                            <td class="py-1 align-top text-[#666666]">: <span id="userInput">{{ $procurement['requester']['employee_name'] }}</span></td>
                                        </tr>

                                        <!-- Status -->
                                        <tr>
                                            <td class="py-1 align-top font-medium text-[#666666]">Status</td>
                                            <td class="py-1 align-top text-[#666666]">:
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
                                                    @elseif($procurement['status'] == 'Procured') Diadakan
                                                    @else {{ $procurement['status'] }}
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>

                                        <!-- Manager Approval (if available) -->
                                        @if(isset($procurement['approved_by_manager']) && !empty($procurement['approved_by_manager']))
                                            <tr>
                                                <td class="py-1 align-top font-medium text-[#666666]">Disetujui Manajer</td>
                                                <td class="py-1 align-top text-[#666666]">:
                                                    <span>{{ $procurement['approved_by_manager']['employee_name'] }}</span>
                                                    @if(isset($procurement['manager_approval_date']))
                                                        <span class="text-xs text-gray-500 ml-2">({{ \Carbon\Carbon::parse($procurement['manager_approval_date'])->locale('id')->translatedFormat('d F Y') }})</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif

                                        <!-- Director Approval (if available) -->
                                        @if(isset($procurement['approved_by_director']) && !empty($procurement['approved_by_director']))
                                            <tr>
                                                <td class="py-1 align-top font-medium text-[#666666]">Disetujui Direktur</td>
                                                <td class="py-1 align-top text-[#666666]">:
                                                    <span>{{ $procurement['approved_by_director']['employee_name'] }}</span>
                                                    @if(isset($procurement['director_approval_date']))
                                                        <span class="text-xs text-gray-500 ml-2">({{ \Carbon\Carbon::parse($procurement['director_approval_date'])->locale('id')->translatedFormat('d F Y') }})</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif

                                        <!-- Rejected By (if available) -->
                                        @if(isset($procurement['rejected_by']) && !empty($procurement['rejected_by']))
                                            <tr>
                                                <td class="py-1 align-top font-medium text-[#666666]">Ditolak Oleh</td>
                                                <td class="py-1 align-top text-[#666666]">:
                                                    <span>{{ $procurement['rejected_by']['employee_name'] }}</span>
                                                    @if(isset($procurement['rejected_date']))
                                                        <span class="text-xs text-gray-500 ml-2">({{ \Carbon\Carbon::parse($procurement['rejected_date'])->locale('id')->translatedFormat('d F Y') }})</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif

                                        <!-- Rejection Reason (if available) -->
                                        @if(isset($procurement['rejected_reason']) && !empty($procurement['rejected_reason']))
                                            <tr>
                                                <td class="py-1 align-top font-medium text-[#666666]">Alasan Penolakan</td>
                                                <td class="py-1 align-top text-[#666666]">: <span>{{ $procurement['rejected_reason'] }}</span></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Item List -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-[#666666]">DAFTAR ASET</h2>

                                <!-- Action Buttons - Repositioned -->
                                <div class="flex flex-wrap gap-3">
                                    @if(
                                        $procurement['status'] == 'Under Review' &&
                                        !isset($procurement['approved_by_manager']) &&
                                        hasPermission('procurement:approve:manager')
                                    )
                                        <button id="managerApprovalBtn" type="button"
                                            class="px-6 py-2 border border-green-600 text-green-600 rounded-lg text-base hover:bg-green-50 transform active:scale-[0.98] transition-all duration-200">
                                            SETUJU
                                        </button>
                                    @endif

                                    @if(
                                            $procurement['status'] == 'Under Review' &&
                                            isset($procurement['approved_by_manager']) &&
                                            !empty($procurement['approved_by_manager']) &&
                                            $procurement['estimated_grand_total'] > 50000000 &&
                                            !isset($procurement['approved_by_director']) &&
                                            hasPermission('procurement:approve:director')
                                        )
                                        <button id="directorApprovalBtn" type="button"
                                            class="px-6 py-2 border border-green-600 text-green-600 rounded-lg text-base hover:bg-green-50 transform active:scale-[0.98] transition-all duration-200">
                                            SETUJU
                                        </button>
                                    @endif

                                    @if(
                                            $procurement['status'] == 'Under Review' &&
                                            (
                                                hasPermission('procurement:reject') ||
                                                hasPermission('procurement:approve:manager') ||
                                                hasPermission('procurement:approve:director')
                                            )
                                        )
                                        <button id="rejectBtn" type="button"
                                            class="px-6 py-2 border border-red-600 text-red-600 rounded-lg text-base hover:bg-red-50 transform active:scale-[0.98] transition-all duration-200">
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
                                            <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Harga Satuan
                                            </th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">Total</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-sm text-center">Perbandingan
                                                Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($procurement['details'] as $detail)
                                            <tr class="border-t border-[#EEF1F4]">
                                                <td class="p-3 text-sm text-[#666666]">{{ $detail['asset_name'] }}</td>
                                                <td class="p-3 text-sm text-[#666666]">{{ $detail['specifications'] }}</td>
                                                <td class="p-3 text-sm text-center text-[#666666]">{{ $detail['quantity'] }}</td>
                                                <td class="p-3 text-sm text-left text-[#666666]">
                                                    {{ number_format($detail['estimated_unit_price'], 0, ',', '.') }}</td>
                                                <td class="p-3 text-sm text-left text-[#666666]">
                                                    {{ number_format($detail['estimated_total_price'], 0, ',', '.') }}</td>
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
                                            <td colspan="4" class="p-3 text-sm font-medium text-right text-[#666666]">Total
                                                Keseluruhan</td>
                                            <td class="p-3 text-sm font-medium text-left text-[#666666]">
                                                {{ number_format($procurement['estimated_grand_total'], 0, ',', '.') }}</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($procurement['status'] == 'Approved')
                            @php
                                $hasWaiting = false;
                                $hasInProgress = false;
                                foreach ($procurement['details'] as $detail) {
                                    if ($detail['status_price_comparison'] == 'Waiting') {
                                        $hasWaiting = true;
                                    }
                                    if ($detail['status_price_comparison'] == 'In Progress') {
                                        $hasInProgress = true;
                                    }
                                }
                            @endphp

                            @if($hasWaiting && !$hasInProgress && hasPermission('price-comparison:create'))
                                <!-- Comparison Title -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">Buat Perbandingan Harga</label>
                                    <input type="text" id="comparison_title"
                                        class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Masukkan judul perbandingan">
                                </div>

                                <!-- Form Buttons -->
                                <div class="flex gap-4 mt-8">
                                    <button id="createComparisonBtn" type="button"
                                        class="px-6 py-3 bg-[#213268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                        BUAT PERBANDINGAN HARGA
                                    </button>
                                </div>
                            @endif
                        @endif
                    @else
                        <!-- Not Found State -->
                        <div class="flex flex-col items-center justify-center py-8">
                            <svg class="w-16 h-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">Permintaan Tidak Ditemukan</h2>
                            <p class="text-gray-600 mb-8">
                                {{ $error ?? 'Data permintaan yang diminta tidak dapat ditemukan atau telah dihapus.' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    @if(hasPermission('procurement:reject') || hasPermission('procurement:approve:manager') || hasPermission('procurement:approve:director'))
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <div class="p-6">
                        <form id="rejectForm" novalidate data-no-loading>
                            <div class="space-y-4 max-w-[400px] mx-auto">
                                <!-- Rejection Reason Input -->
                                <div class="space-y-2">
                                    <label class="block text-base font-semibold text-[#666666]">
                                        Alasan Penolakan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="rejection_reason" name="rejected_reason" rows="4" required
                                        class="w-full px-4 py-3 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#213268] focus:ring-2 focus:ring-[#213268] focus:ring-opacity-20 transition-all duration-200"
                                        placeholder="Silakan berikan alasan penolakan"></textarea>
                                    <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Alasan penolakan harus
                                        diisi</div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3 mt-6">
                                    <button type="button"
                                        class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="w-1/2 h-[45px] bg-red-600 text-white rounded-lg text-base hover:bg-red-700 transform active:scale-[0.98] transition-all duration-200">
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
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if (!document.getElementById('animate-css')) {
            const animateLink = document.createElement('link');
            animateLink.id = 'animate-css';
            animateLink.rel = 'stylesheet';
            animateLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';
            document.head.appendChild(animateLink);
        }
        if (!document.getElementById('swal-custom-styles')) {
            const styleTag = document.createElement('style');
            styleTag.id = 'swal-custom-styles';
            styleTag.innerHTML = `
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
                .swal-custom-error-content {
                    font-size: 1rem;
                    color: #555;
                    margin-top: 0.5rem;
                    text-align: left;
                    max-width: 100%;
                    word-wrap: break-word;
                }
                .swal-custom-error-content ul {
                    list-style-type: disc;
                    padding-left: 20px;
                    text-align: left;
                    max-width: 100%;
                    word-wrap: break-word;
                }
                .swal-custom-error-content li {
                    margin-bottom: 0.25rem;
                    word-break: break-word;
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

            const mergedOptions = { ...defaultOptions, ...options };

            if (type === 'success' && options.timer === undefined) {
                mergedOptions.timer = 2500;
                mergedOptions.timerProgressBar = true;
            } else if (type === 'error' && options.showCloseButton === undefined) {
                mergedOptions.confirmButtonColor = '#d33';
                mergedOptions.showCloseButton = true;
            }

            return Swal.fire(mergedOptions);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const procurementId = {{ $procurement['procurement_id'] ?? 'null' }};
            if (!procurementId) {
                return;
            }
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            @php
                $procurementStatus = $procurement['status'] ?? '';
            @endphp

            @if(
                !hasPermission('procurement:approve:manager') ||
                $procurementStatus !== 'Under Review' ||
                isset($procurement['approved_by_manager'])
            )
                const managerApprovalBtnElement = document.getElementById('managerApprovalBtn');
                if (managerApprovalBtnElement) managerApprovalBtnElement.style.display = 'none';
            @endif

            @if(!hasPermission('procurement:approve:director') || $procurementStatus !== 'Under Review')
                    const directorApprovalBtn = document.getElementById('directorApprovalBtn');
                    if (directorApprovalBtn) directorApprovalBtn.style.display = 'none';
                @endif

            @if(!hasPermission('procurement:reject') && !hasPermission('procurement:approve:manager') && !hasPermission('procurement:approve:director') || $procurementStatus !== 'Under Review')
                const rejectBtnElement = document.getElementById('rejectBtn');
                if (rejectBtnElement) rejectBtnElement.style.display = 'none';
                @endif

                @if(!hasPermission('price-comparison:create'))
                const createComparisonBtnElement = document.getElementById('createComparisonBtn');
                if (createComparisonBtnElement) createComparisonBtnElement.style.display = 'none';
                @endif

            const managerApprovalBtn = document.getElementById('managerApprovalBtn');
            if (managerApprovalBtn) {
                managerApprovalBtn.addEventListener('click', function () {
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
                                        // Helper function to safely extract error messages
                                        const extractErrorMessages = (errors) => {
                                            if (Array.isArray(errors)) {
                                                return errors.map(error =>
                                                    typeof error === 'string'
                                                        ? error
                                                        : (error.message || JSON.stringify(error))
                                                );
                                            } else if (typeof errors === 'object') {
                                                return Object.values(errors).flat().map(error =>
                                                    typeof error === 'string'
                                                        ? error
                                                        : (error.message || JSON.stringify(error))
                                                );
                                            } else if (typeof errors === 'string') {
                                                return [errors];
                                            }
                                            return [];
                                        };

                                        let errorMessage = 'Terjadi kesalahan saat menyetujui pengadaan:';
                                        let errorList = extractErrorMessages(data.errors);

                                        // If no specific errors, use a generic message
                                        if (errorList.length === 0) {
                                            errorList.push('Gagal menyetujui pengadaan. Silakan coba lagi.');
                                        }

                                        // Format error message
                                        if (errorList.length > 0) {
                                            errorMessage += '<ul class="mt-2 list-disc list-inside text-left">';
                                            errorList.forEach(err => {
                                                errorMessage += `<li>${err}</li>`;
                                            });
                                            errorMessage += '</ul>';
                                        }

                                        // Display error message
                                        showSweetAlert(errorMessage, 'error', {
                                            title: 'Gagal Menyetujui Pengadaan',
                                            customClass: {
                                                htmlContainer: 'swal-custom-error-content'
                                            }
                                        });
                                    }
                                })
                                .catch(error => {
                                    showSweetAlert('Terjadi kesalahan saat memproses permintaan Anda', 'error');
                                });
                        }
                    });
                });
            }

            const directorApprovalBtnElement = document.getElementById('directorApprovalBtn');
            if (directorApprovalBtnElement) {
                directorApprovalBtnElement.addEventListener('click', function () {
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
                                        // Helper function to safely extract error messages
                                        const extractErrorMessages = (errors) => {
                                            if (Array.isArray(errors)) {
                                                return errors.map(error =>
                                                    typeof error === 'string'
                                                        ? error
                                                        : (error.message || JSON.stringify(error))
                                                );
                                            } else if (typeof errors === 'object') {
                                                return Object.values(errors).flat().map(error =>
                                                    typeof error === 'string'
                                                        ? error
                                                        : (error.message || JSON.stringify(error))
                                                );
                                            } else if (typeof errors === 'string') {
                                                return [errors];
                                            }
                                            return [];
                                        };

                                        let errorMessage = 'Terjadi kesalahan saat menyetujui pengadaan:';
                                        let errorList = extractErrorMessages(data.errors);

                                        // If no specific errors, use a generic message
                                        if (errorList.length === 0) {
                                            errorList.push('Gagal menyetujui pengadaan. Silakan coba lagi.');
                                        }

                                        // Format error message
                                        if (errorList.length > 0) {
                                            errorMessage += '<ul class="mt-2 list-disc list-inside text-left">';
                                            errorList.forEach(err => {
                                                errorMessage += `<li>${err}</li>`;
                                            });
                                            errorMessage += '</ul>';
                                        }

                                        // Display error message
                                        showSweetAlert(errorMessage, 'error', {
                                            title: 'Gagal Menyetujui Pengadaan',
                                            customClass: {
                                                htmlContainer: 'swal-custom-error-content'
                                            }
                                        });
                                    }
                                })
                                .catch(error => {
                                    showSweetAlert('Terjadi kesalahan saat memproses permintaan Anda', 'error');
                                });
                        }
                    });
                });
            }

            const rejectBtn = document.getElementById('rejectBtn');
            const rejectModal = document.getElementById('rejectModal');

            if (rejectBtn && rejectModal) {
            const rejectModalContent = document.getElementById('rejectModalContent');
            const rejectForm = document.getElementById('rejectForm');
            const rejectionReasonField = document.getElementById('rejection_reason');

            function closeRejectModal() {
                rejectModalContent.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                rejectModalContent.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                setTimeout(() => {
                    rejectModal.classList.add('hidden');
                    rejectForm.reset();
                    rejectionReasonField.classList.remove('border-red-500');
                    const errorField = rejectForm.querySelector('.invalid-feedback');
                    if (errorField) errorField.classList.add('hidden');
                }, 300);
            }

                rejectBtn.addEventListener('click', function () {
                    rejectModal.classList.remove('hidden');
                    setTimeout(() => {
                        rejectModalContent.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                        rejectModalContent.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                    }, 10);
                });

            document.querySelectorAll('.close-modal').forEach(button => {
                button.addEventListener('click', closeRejectModal);
            });

            rejectModal.addEventListener('click', function(e) {
                const overlayArea = this.querySelector('.fixed.inset-0.z-50.overflow-y-auto');
                const bgOverlay = this.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');
                if (e.target === overlayArea || e.target === bgOverlay) {
                    closeRejectModal();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !rejectModal.classList.contains('hidden')) {
                    closeRejectModal();
                }
            });

            rejectForm.addEventListener('submit', function (e) {
                e.preventDefault();
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
                            closeRejectModal();
                            showSweetAlert(data.message || 'Pengadaan berhasil ditolak', 'success', {
                                timer: 1500,
                                timerProgressBar: true,
                                showConfirmButton: false,
                                willClose: () => {
                                    window.location.reload();
                                }
                            });
                        } else {
                            // Helper function to safely extract error messages
                            const extractErrorMessages = (errors) => {
                                if (Array.isArray(errors)) {
                                    return errors.map(error =>
                                        typeof error === 'string'
                                            ? error
                                            : (error.message || JSON.stringify(error))
                                    );
                                } else if (typeof errors === 'object') {
                                    return Object.values(errors).flat().map(error =>
                                        typeof error === 'string'
                                            ? error
                                            : (error.message || JSON.stringify(error))
                                    );
                                } else if (typeof errors === 'string') {
                                    return [errors];
                                }
                                return [];
                            };

                            let errorMessage = 'Terjadi kesalahan saat menolak pengadaan:';
                            let errorList = extractErrorMessages(data.errors);

                            // Check for specific rejected_reason errors
                            const reasonErrors = Array.isArray(data.errors)
                                ? data.errors.filter(err => err.path === 'rejected_reason')
                                : [];

                            // Highlight rejected_reason field if there are specific errors
                            if (reasonErrors.length > 0) {
                                        rejectionReasonField.classList.add('border-red-500');
                                        const errorElement = rejectForm.querySelector('.invalid-feedback');
                                        if (errorElement) {
                                    errorElement.textContent = reasonErrors[0].message;
                                            errorElement.classList.remove('hidden');
                                        }
                                    }

                            // If no specific errors, use a generic message
                            if (errorList.length === 0) {
                                errorList.push('Gagal menolak pengadaan. Silakan coba lagi.');
                            }

                            // Format error message
                            if (errorList.length > 0) {
                                errorMessage += '<ul class="mt-2 list-disc list-inside text-left">';
                                errorList.forEach(err => {
                                    errorMessage += `<li>${err}</li>`;
                                });
                                errorMessage += '</ul>';
                            }

                            // Display error message
                            showSweetAlert(errorMessage, 'error', {
                                title: 'Gagal Menolak Pengadaan',
                                customClass: {
                                    htmlContainer: 'swal-custom-error-content'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        showSweetAlert('Terjadi kesalahan saat memproses permintaan Anda', 'error');
                    });
            });
            }

            const createComparisonBtn = document.getElementById('createComparisonBtn');
            const comparisonTitleInput = document.getElementById('comparison_title');

            if (createComparisonBtn) {
                createComparisonBtn.addEventListener('click', function () {
                    if (!comparisonTitleInput.value.trim()) {
                        showSweetAlert('Silakan masukkan judul perbandingan harga', 'error');
                        comparisonTitleInput.classList.add('border-red-500');
                        return;
                    }

                    comparisonTitleInput.classList.remove('border-red-500');

                    const originalBtnText = createComparisonBtn.innerHTML;
                    createComparisonBtn.disabled = true;
                    createComparisonBtn.innerHTML = `
                        <div class="flex items-center justify-center">
                            <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                            <span>Memproses...</span>
                        </div>
                    `;

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
                            createComparisonBtn.disabled = false;
                            createComparisonBtn.innerHTML = originalBtnText;

                            if (data.success) {
                                showSweetAlert(data.message || 'Perbandingan harga berhasil dibuat', 'success', {
                                    timer: 1500,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                    willClose: () => {
                                        if (data.redirect_url) {
                                            window.location.href = data.redirect_url;
                                        } else {
                                            window.location.reload();
                                        }
                                    }
                                });
                            } else {
                                // Helper function to safely extract error messages
                                const extractErrorMessages = (errors) => {
                                    if (Array.isArray(errors)) {
                                        return errors.map(error =>
                                            typeof error === 'string'
                                                ? error
                                                : (error.message || JSON.stringify(error))
                                        );
                                    } else if (typeof errors === 'object') {
                                        return Object.values(errors).flat().map(error =>
                                            typeof error === 'string'
                                                ? error
                                                : (error.message || JSON.stringify(error))
                                        );
                                    } else if (typeof errors === 'string') {
                                        return [errors];
                                    }
                                    return [];
                                };

                                let errorMessage = 'Gagal membuat perbandingan harga:';
                                let errorList = extractErrorMessages(data.errors);

                                // If no specific errors, use a generic message
                                if (errorList.length === 0) {
                                    errorList.push('Gagal membuat perbandingan harga. Silakan coba lagi.');
                                }

                                // Format error message
                                if (errorList.length > 0) {
                                    errorMessage += '<ul class="mt-2 list-disc list-inside text-left">';
                                    errorList.forEach(err => {
                                        errorMessage += `<li>${err}</li>`;
                                    });
                                    errorMessage += '</ul>';
                                }

                                showSweetAlert(errorMessage, 'error', {
                                    title: 'Gagal Membuat Perbandingan Harga',
                                    customClass: {
                                        htmlContainer: 'swal-custom-error-content'
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            createComparisonBtn.disabled = false;
                            createComparisonBtn.innerHTML = originalBtnText;

                            console.error('Error:', error);
                            showSweetAlert('Terjadi kesalahan saat memproses permintaan Anda', 'error');
                        });
                });

                comparisonTitleInput.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                });
            }

            @if(session('success'))
                showSweetAlert("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                let errorMessage = 'Terjadi kesalahan:';
                let errorList = [];

                @if(is_array(session('error')))
                    @foreach(session('error') as $error)
                        errorList.push('{{ $error }}');
                    @endforeach
                @else
                    errorList.push('{{ session('error') }}');
                @endif

                if (errorList.length > 0) {
                    errorMessage += '<ul class="mt-2 list-disc list-inside text-left">';
                    errorList.forEach(err => {
                        errorMessage += `<li>${err}</li>`;
                    });
                    errorMessage += '</ul>';
                }

                showSweetAlert(errorMessage, 'error', {
                    title: 'Gagal Memproses Permintaan',
                    customClass: {
                        htmlContainer: 'swal-custom-error-content'
                    }
                });
            @endif
        });
    </script>
@endpush

