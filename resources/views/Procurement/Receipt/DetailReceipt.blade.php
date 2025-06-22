@extends('Layout.app')

@section('title', 'Detail Penerimaan')

@section('content')
    @include('Layout.loading')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Receipt Detail Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 md:p-7">
                <div class="flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center">
                            <a href="{{ route('procurement.receipt') }}"
                                class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                            <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DETAIL PENERIMAAN</h1>
                        </div>

                        @if(isset($receipt))
                            @if(hasPermission('receipt:export'))
                            <a href="{{ route('procurement.receipt.export-pdf', ['id' => $receipt['receipt_id']]) }}"
                                id="exportPdfBtn"
                                class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200"
                                target="_blank" rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Ekspor PDF
                            </a>
                            @endif
                        @endif
                    </div>

                    @if(isset($receipt))
                        <!-- Receipt Details - Two Column Layout -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column - Receipt Information -->
                            <div class="space-y-5">
                                <h2 class="text-lg font-semibold text-[#666666]">Informasi Penerimaan</h2>

                                <!-- Receipt Number -->
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <p class="w-40 sm:w-48 text-[#666666] font-medium">Nomor Penerimaan</p>
                                    <p class="text-[#666666]"><span class="sm">: </span>{{ $receipt['receipt_code'] ?? 'N/A' }}</p>
                                </div>

                                <!-- Order Number -->
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <p class="w-40 sm:w-48 text-[#666666] font-medium">Nomor Pemesanan</p>
                                    <p class="text-[#666666]"><span class="sm">: </span>{{ $receipt['purchase_order_code'] ?? 'N/A' }}
                                    </p>
                                </div>
                                
                                <!-- Receipt Date -->
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <p class="w-40 sm:w-48 text-[#666666] font-medium">Tanggal Penerimaan</p>
                                    <p class="text-[#666666]"><span class="sm">:
                                        </span>{{ $receipt['receipt_date'] ? \Carbon\Carbon::parse($receipt['receipt_date'])->locale('id')->translatedFormat('d F Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Right Column - Personnel Information -->
                            <div class="space-y-5">
                                <h2 class="text-lg font-semibold text-[#666666]">Informasi Personil</h2>

                                <!-- Delivered by -->
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <p class="w-40 sm:w-48 text-[#666666] font-medium">Dikirim Oleh</p>
                                    <p class="text-[#666666]"><span class="sm">: </span>{{ $receipt['delivered_by'] ?? 'N/A' }}</p>
                                </div>

                                <!-- Received by -->
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <p class="w-40 sm:w-48 text-[#666666] font-medium">Diterima Oleh</p>
                                    <p class="text-[#666666]"><span class="sm">: </span>{{ $receipt['receiver_name'] ?? 'N/A' }}</p>
                                </div>

                                <!-- User Input -->
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <p class="w-40 sm:w-48 text-[#666666] font-medium">Diinput Oleh</p>
                                    <p class="text-[#666666]"><span class="sm">: </span>{{ $receipt['creator_name'] ?? 'N/A' }}</p>
                                </div>

                                <!-- Input Date -->
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <p class="w-40 sm:w-48 text-[#666666] font-medium">Tanggal Input</p>
                                    <p class="text-[#666666]"><span class="sm">:
                                        </span>{{ $receipt['created_at'] ? \Carbon\Carbon::parse($receipt['created_at'])->locale('id')->translatedFormat('d F Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Item List -->
                        <div class="space-y-4">
                            <h2 class="text-lg font-semibold text-[#666666]">DAFTAR ASET</h2>

                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">NAMA ASET</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-sm text-center">TANGGAL
                                                PENERIMAAN</th>
                                            <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">CATATAN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($receipt['items']) && count($receipt['items']) > 0)
                                            @foreach($receipt['items'] as $item)
                                                <tr class="border-t border-[#EEF1F4]">
                                                    <td class="p-3 text-sm text-[#666666]">{{ $item['procurement_item_name'] ?? 'N/A' }}
                                                    </td>
                                                    <td class="p-3 text-sm text-center text-[#666666]">
                                                        {{ isset($item['created_at']) ? \Carbon\Carbon::parse($item['created_at'])->locale('id')->translatedFormat('d F Y') : 'N/A' }}
                                                    </td>
                                                    <td class="p-3 text-sm text-[#666666]">{{ $item['notes'] ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="border-t border-[#EEF1F4]">
                                                <td colspan="3" class="p-3 text-center text-gray-500">Tidak ada item yang ditemukan
                                                    untuk penerimaan ini.</td>
                                            </tr>
                                        @endif

                                        <!-- Notes Row -->
                                        @if(isset($receipt['notes']) && !empty($receipt['notes']))
                                            <tr class="border-t border-[#EEF1F4] bg-[#E9ECF6]">
                                                <td class="p-3 text-sm font-medium text-left text-[#213268]">Catatan</td>
                                                <td colspan="2" class="p-3 text-sm text-[#666666]">
                                                    {{ $receipt['notes'] ?? 'Tidak ada data' }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <!-- Not Found State -->
                        <div class="flex flex-col items-center justify-center py-8">
                            <svg class="w-16 h-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">Penerimaan Tidak Ditemukan</h2>
                            <p class="text-gray-600 mb-8">
                                {{ $error ?? 'Data penerimaan yang diminta tidak dapat ditemukan atau telah dihapus.' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

                if (!document.getElementById('animate-css')) {
                    const animateLink = document.createElement('link');
                    animateLink.id = 'animate-css';
                    animateLink.rel = 'stylesheet';
                    animateLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';
                    document.head.appendChild(animateLink);
                }

                return Swal.fire(mergedOptions);
            }

            @if(session('success'))
                showSweetAlert("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                showSweetAlert("{{ session('error') }}", 'error');
            @endif

            @if(!hasPermission('receipt:export'))
                const exportPdfBtn = document.getElementById('exportPdfBtn');
                if (exportPdfBtn) {
                    exportPdfBtn.style.display = 'none';
                }
            @endif

            const exportPdfBtn = document.getElementById('exportPdfBtn');
            if (exportPdfBtn) {
                exportPdfBtn.addEventListener('click', function () {
                    showSweetAlert('Mengunduh file PDF...', 'info', {
                        timer: 1500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                });
            }
        });
    </script>
@endpush