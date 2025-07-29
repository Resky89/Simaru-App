@extends('Layout.app')

@section('title', 'Detail Berita Acara')

@section('content')
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Header with Back Button -->
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('official-report.index') }}"
               class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DETAIL BERITA ACARA</h1>
        </div>

        @if(isset($error))
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body p-4 md:p-7">
                    <div class="text-center py-8">
                        <svg class="mx-auto h-16 w-16 text-red-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Terjadi Kesalahan</h3>
                        <p class="text-gray-600">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @elseif(isset($official_report))
            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Report Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information Card -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body p-4 md:p-7">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                                <h2 class="text-xl font-semibold text-[#213268]">Informasi Berita Acara</h2>
                                <div class="flex gap-2">
                                    <!-- Action Buttons -->
                                    @if(hasPermission('official-report:edit') && in_array($official_report['status'], ['PENDING', 'SUBMITTED']))
                                        <button class="edit-report-btn px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 transition-colors text-sm font-medium"
                                                data-report-id="{{ $official_report['official_report_id'] }}"
                                                data-report-type="{{ $official_report['report_type'] }}"
                                                data-notes="{{ $official_report['notes'] ?? '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                    @endif

                                    @if(hasPermission('official-report:approve') && $official_report['status'] == 'SUBMITTED')
                                        <button class="approve-report-btn px-4 py-2 bg-green-100 text-green-800 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium"
                                                data-report-id="{{ $official_report['official_report_id'] }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Setujui
                                        </button>
                                    @endif

                                    @if(hasPermission('official-report:reject') && $official_report['status'] == 'SUBMITTED')
                                        <button class="reject-report-btn px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium"
                                                data-report-id="{{ $official_report['official_report_id'] }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Tolak
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">ID Berita Acara</label>
                                        <p class="text-base font-semibold text-gray-900">#{{ $official_report['official_report_id'] }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Tipe</label>
                                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                                            @if($official_report['report_type'] == 'DISPOSAL') bg-red-100 text-red-800
                                            @elseif($official_report['report_type'] == 'TRANSFER') bg-blue-100 text-blue-800
                                            @elseif($official_report['report_type'] == 'MAINTENANCE') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $official_report['report_type'] }}
                                        </span>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                                            @if($official_report['status'] == 'APPROVED') bg-green-100 text-green-800
                                            @elseif($official_report['status'] == 'REJECTED') bg-red-100 text-red-800
                                            @elseif($official_report['status'] == 'SUBMITTED') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $official_report['status'] }}
                                        </span>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Rilis</label>
                                        <p class="text-base text-gray-900">{{ $official_report['release_date'] !== 'N/A' ? $official_report['release_date'] : '-' }}</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Dibuat Oleh</label>
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-[#213268] rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-medium">
                                                    {{ substr($official_report['created_by']['employee_name'] ?? 'U', 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-base font-medium text-gray-900">{{ $official_report['created_by']['employee_name'] ?? '-' }}</p>
                                                <p class="text-sm text-gray-600">{{ $official_report['created_by']['employee_number'] ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Dibuat</label>
                                        <p class="text-base text-gray-900">{{ $official_report['created_at'] ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($official_report['notes'])
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-600 mb-2">Catatan</label>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-gray-700">{{ $official_report['notes'] }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Items List Card -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body p-4 md:p-7">
                            <h2 class="text-xl font-semibold text-[#213268] mb-6">Daftar Aset</h2>

                            @if(isset($official_report['items']) && count($official_report['items']) > 0)
                                <div class="space-y-4">
                                    @foreach($official_report['items'] as $item)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                            <div class="flex flex-col md:flex-row gap-4">
                                                <div class="flex-shrink-0">
                                                    <img src="{{ $item['asset']['asset_image'] ?? asset('images/placeholder.png') }}"
                                                         alt="Asset Image"
                                                         class="w-20 h-20 object-cover rounded-lg bg-gray-100">
                                                </div>
                                                <div class="flex-grow">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <h3 class="font-semibold text-gray-900 mb-1">{{ $item['asset']['asset_name'] ?? '-' }}</h3>
                                                            <p class="text-sm text-gray-600 mb-2">{{ $item['asset']['asset_code'] ?? '-' }}</p>
                                                            <div class="space-y-1">
                                                                <p class="text-xs text-gray-500">
                                                                    <span class="font-medium">Merk:</span> {{ $item['asset']['brand_name'] ?? '-' }}
                                                                </p>
                                                                <p class="text-xs text-gray-500">
                                                                    <span class="font-medium">Model:</span> {{ $item['asset']['model'] ?? '-' }}
                                                                </p>
                                                                <p class="text-xs text-gray-500">
                                                                    <span class="font-medium">Serial:</span> {{ $item['asset']['serial_number'] ?? '-' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="space-y-1">
                                                                <p class="text-xs text-gray-500">
                                                                    <span class="font-medium">Tanggal Pembelian:</span> {{ $item['asset']['purchase_date'] ?? '-' }}
                                                                </p>
                                                                <p class="text-xs text-gray-500">
                                                                    <span class="font-medium">Harga Pembelian:</span>
                                                                    @if($item['asset']['purchase_cost'])
                                                                        Rp {{ number_format($item['asset']['purchase_cost'], 0, ',', '.') }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </p>
                                                            </div>
                                                            @if($item['item_notes'])
                                                                <div class="mt-2">
                                                                    <p class="text-xs font-medium text-gray-700 mb-1">Catatan Item:</p>
                                                                    <p class="text-xs text-gray-600">{{ $item['item_notes'] }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-gray-500">Tidak ada aset dalam berita acara ini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Approval Flow -->
                <div class="space-y-6">
                    <!-- Approval Status Card -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body p-4 md:p-7">
                            <h2 class="text-xl font-semibold text-[#213268] mb-6">Status Persetujuan</h2>

                            <div class="space-y-4">
                                <!-- Approval 1 -->
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-1">
                                        @if($official_report['approval_1_status'] == 'APPROVED')
                                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        @elseif($official_report['approval_1_status'] == 'REJECTED')
                                            <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </div>
                                        @elseif($official_report['approval_1_status'] == 'PENDING')
                                            <div class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 bg-gray-400 rounded-full"></div>
                                        @endif
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-medium text-gray-900">Persetujuan 1</h3>
                                            <span class="text-xs px-2 py-1 rounded-full
                                                @if($official_report['approval_1_status'] == 'APPROVED') bg-green-100 text-green-800
                                                @elseif($official_report['approval_1_status'] == 'REJECTED') bg-red-100 text-red-800
                                                @elseif($official_report['approval_1_status'] == 'PENDING') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $official_report['approval_1_status'] }}
                                            </span>
                                        </div>
                                        @if($official_report['approval_1_by']['employee_name'])
                                            <p class="text-sm text-gray-600 mt-1">{{ $official_report['approval_1_by']['employee_name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $official_report['approval_1_by']['employee_number'] }}</p>
                                        @endif
                                        @if($official_report['approval_1_date'] !== 'N/A')
                                            <p class="text-xs text-gray-500 mt-1">{{ $official_report['approval_1_date'] }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Approval 2 -->
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-1">
                                        @if($official_report['approval_2_status'] == 'APPROVED')
                                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        @elseif($official_report['approval_2_status'] == 'REJECTED')
                                            <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </div>
                                        @elseif($official_report['approval_2_status'] == 'PENDING')
                                            <div class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 bg-gray-400 rounded-full"></div>
                                        @endif
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-medium text-gray-900">Persetujuan 2</h3>
                                            <span class="text-xs px-2 py-1 rounded-full
                                                @if($official_report['approval_2_status'] == 'APPROVED') bg-green-100 text-green-800
                                                @elseif($official_report['approval_2_status'] == 'REJECTED') bg-red-100 text-red-800
                                                @elseif($official_report['approval_2_status'] == 'PENDING') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $official_report['approval_2_status'] }}
                                            </span>
                                        </div>
                                        @if($official_report['approval_2_by']['employee_name'])
                                            <p class="text-sm text-gray-600 mt-1">{{ $official_report['approval_2_by']['employee_name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $official_report['approval_2_by']['employee_number'] }}</p>
                                        @endif
                                        @if($official_report['approval_2_date'] !== 'N/A')
                                            <p class="text-xs text-gray-500 mt-1">{{ $official_report['approval_2_date'] }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Rejection Info -->
                                @if($official_report['status'] == 'REJECTED' && $official_report['rejected_by']['employee_name'])
                                    <div class="border-t pt-4 mt-4">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 mt-1">
                                                <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-grow">
                                                <h3 class="text-sm font-medium text-red-800">Ditolak Oleh</h3>
                                                <p class="text-sm text-gray-600 mt-1">{{ $official_report['rejected_by']['employee_name'] }}</p>
                                                <p class="text-xs text-gray-500">{{ $official_report['rejected_by']['employee_number'] }}</p>
                                                @if($official_report['rejected_date'] !== 'N/A')
                                                    <p class="text-xs text-gray-500 mt-1">{{ $official_report['rejected_date'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    @if(hasPermission('official-report:delete') && $official_report['status'] != 'APPROVED')
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body p-4 md:p-7">
                                <h2 class="text-xl font-semibold text-[#213268] mb-4">Aksi Berbahaya</h2>
                                <button class="delete-report-btn w-full px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium"
                                        data-report-id="{{ $official_report['official_report_id'] }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Berita Acara
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Include the same modals from the main page (but without add modal) -->
    @if(hasPermission('official-report:edit'))
        <div id="editOfficialReportModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="editOfficialReportModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">EDIT BERITA ACARA</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="editOfficialReportForm" action="" method="POST" data-no-loading novalidate>
                                @csrf
                                @method('PUT')
                                <div class="space-y-4 max-w-[400px] mx-auto">
                                    <!-- Hidden ID -->
                                    <input type="hidden" id="editReportId" name="report_id">

                                    <!-- Report Type -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">
                                            Tipe Berita Acara <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <select id="editReportType" name="report_type"
                                                class="w-full h-[45px] px-4 border border-[#CCCCCC] rounded-lg text-[#666666] appearance-none focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200 bg-white cursor-pointer"
                                                required>
                                                <option value="">Pilih Tipe</option>
                                                <option value="DISPOSAL">Disposal</option>
                                                <option value="TRANSFER">Transfer</option>
                                                <option value="MAINTENANCE">Maintenance</option>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                                <svg class="w-4 h-4 text-[#203268]" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                            <div class="error-message text-red-500 text-sm mt-1 hidden">Tipe harus dipilih</div>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">
                                            Catatan
                                        </label>
                                        <textarea id="editNotes" name="notes"
                                            class="w-full p-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Ketik catatan di sini" rows="3"></textarea>
                                    </div>

                                    <button type="submit"
                                        class="w-full h-[45px] bg-[#203268] text-white rounded-lg text-base hover:bg-[#152451] transform active:scale-[0.98] transition-all duration-200">
                                        Perbarui
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(hasPermission('official-report:delete'))
        <div id="deleteOfficialReportModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="deleteOfficialReportModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">HAPUS BERITA ACARA</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <div class="space-y-6 max-w-[400px] mx-auto">
                                <div class="flex flex-col items-center">
                                    <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menghapus berita acara
                                        ini? Tindakan ini tidak dapat dibatalkan.</p>
                                </div>
                                <div class="flex gap-3">
                                    <button
                                        class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                        Batal
                                    </button>
                                    <form id="deleteOfficialReportForm" action="" method="POST" data-no-loading class="w-1/2">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" id="deleteReportId" name="report_id">
                                        <button type="submit"
                                            class="w-full h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(hasPermission('official-report:approve'))
        <div id="approveOfficialReportModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="approveOfficialReportModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">SETUJUI BERITA ACARA</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <div class="space-y-6 max-w-[400px] mx-auto">
                                <div class="flex flex-col items-center">
                                    <svg class="mb-4 w-16 h-16 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menyetujui berita acara ini?</p>
                                </div>
                                <div class="flex gap-3">
                                    <button
                                        class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                        Batal
                                    </button>
                                    <form id="approveOfficialReportForm" action="" method="POST" data-no-loading class="w-1/2">
                                        @csrf
                                        <input type="hidden" id="approveReportId" name="report_id">
                                        <button type="submit"
                                            class="w-full h-[45px] bg-green-500 text-white rounded-lg text-base hover:bg-green-600 transform active:scale-[0.98] transition-all duration-200">
                                            Setujui
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(hasPermission('official-report:reject'))
        <div id="rejectOfficialReportModal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-[15px] bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[500px] scale-95 opacity-0 translate-y-4 sm:translate-y-0 duration-300"
                        id="rejectOfficialReportModalContent">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 pb-0">
                            <h2 class="text-xl sm:text-2xl font-semibold text-[#203268]">TOLAK BERITA ACARA</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="rejectOfficialReportForm" action="" method="POST" data-no-loading novalidate>
                                @csrf
                                <div class="space-y-4 max-w-[400px] mx-auto">
                                    <!-- Hidden ID -->
                                    <input type="hidden" id="rejectReportId" name="report_id">

                                    <!-- Rejection Reason -->
                                    <div class="space-y-2">
                                        <label class="block text-base font-semibold text-[#666666]">
                                            Alasan Penolakan <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="rejection_reason" id="rejection_reason"
                                            class="w-full p-4 border border-[#CCCCCC] rounded-lg text-[#666666] focus:outline-none focus:border-[#203268] focus:ring-2 focus:ring-[#203268] focus:ring-opacity-20 transition-all duration-200"
                                            placeholder="Tuliskan alasan penolakan" rows="4" required></textarea>
                                        <div class="error-message text-red-500 text-sm mt-1 hidden">Alasan penolakan harus diisi</div>
                                    </div>

                                    <div class="flex gap-3">
                                        <button type="button"
                                            class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="w-1/2 h-[45px] bg-red-500 text-white rounded-lg text-base hover:bg-red-600 transform active:scale-[0.98] transition-all duration-200">
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Modal elements
                const editOfficialReportModal = document.getElementById('editOfficialReportModal');
                const deleteOfficialReportModal = document.getElementById('deleteOfficialReportModal');
                const approveOfficialReportModal = document.getElementById('approveOfficialReportModal');
                const rejectOfficialReportModal = document.getElementById('rejectOfficialReportModal');
                const closeButtons = document.querySelectorAll('.close-modal');

                // Form elements
                const editOfficialReportForm = document.getElementById('editOfficialReportForm');
                const deleteOfficialReportForm = document.getElementById('deleteOfficialReportForm');
                const approveOfficialReportForm = document.getElementById('approveOfficialReportForm');
                const rejectOfficialReportForm = document.getElementById('rejectOfficialReportForm');

                // Show success/error messages
                @if(session('success'))
                    showToast("{{ session('success') }}", 'success');
                @endif

                @if(session('error'))
                    showToast("{{ session('error') }}", 'error');
                @endif

                // Modal functions
                function openModal(modal, content) {
                    if (!modal || !content) return;
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        content.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                        content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                    }, 10);
                }

                function closeModal(modal, content) {
                    if (!modal || !content) return;
                    content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
                    content.classList.add('scale-95', 'opacity-0', 'translate-y-4');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                    }, 300);
                }

                function clearModalForms(modal) {
                    if (!modal) return;
                    const forms = modal.querySelectorAll('form');
                    forms.forEach(form => {
                        form.reset();
                        const inputs = form.querySelectorAll('input, select, textarea');
                        inputs.forEach(input => {
                            input.classList.remove('border-red-500');
                            const errorElement = input.closest('.space-y-2')?.querySelector('.error-message');
                            if (errorElement) errorElement.classList.add('hidden');
                        });
                    });
                }

                // Edit buttons
                document.querySelectorAll('.edit-report-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const reportId = button.getAttribute('data-report-id');
                        const reportType = button.getAttribute('data-report-type');
                        const notes = button.getAttribute('data-notes');

                        const formAction = "{{ url('official-reports') }}/" + reportId;
                        const editForm = document.getElementById('editOfficialReportForm');
                        if (editForm) editForm.action = formAction;

                        document.getElementById('editReportId').value = reportId;
                        document.getElementById('editReportType').value = reportType;
                        document.getElementById('editNotes').value = notes || '';

                        openModal(editOfficialReportModal, editOfficialReportModal.querySelector('[id$="ModalContent"]'));
                    });
                });

                // Delete buttons
                document.querySelectorAll('.delete-report-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const reportId = button.getAttribute('data-report-id');
                        const formAction = "{{ url('official-reports') }}/" + reportId;
                        document.getElementById('deleteOfficialReportForm').action = formAction;
                        document.getElementById('deleteReportId').value = reportId;
                        openModal(deleteOfficialReportModal, deleteOfficialReportModal.querySelector('[id$="ModalContent"]'));
                    });
                });

                // Approve buttons
                document.querySelectorAll('.approve-report-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const reportId = button.getAttribute('data-report-id');
                        const formAction = "{{ url('official-reports') }}/" + reportId + "/approve";
                        document.getElementById('approveOfficialReportForm').action = formAction;
                        document.getElementById('approveReportId').value = reportId;
                        openModal(approveOfficialReportModal, approveOfficialReportModal.querySelector('[id$="ModalContent"]'));
                    });
                });

                // Reject buttons
                document.querySelectorAll('.reject-report-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const reportId = button.getAttribute('data-report-id');
                        const formAction = "{{ url('official-reports') }}/" + reportId + "/reject";
                        document.getElementById('rejectOfficialReportForm').action = formAction;
                        document.getElementById('rejectReportId').value = reportId;
                        openModal(rejectOfficialReportModal, rejectOfficialReportModal.querySelector('[id$="ModalContent"]'));
                    });
                });

                // Close modal buttons
                closeButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const modal = button.closest('[id$="Modal"]');
                        const content = modal.querySelector('[id$="ModalContent"]');
                        closeModal(modal, content);
                        clearModalForms(modal);
                    });
                });

                // Modal backdrop clicks
                [editOfficialReportModal, deleteOfficialReportModal, approveOfficialReportModal, rejectOfficialReportModal].forEach(modal => {
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

                // Escape key to close modals
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        [editOfficialReportModal, deleteOfficialReportModal, approveOfficialReportModal, rejectOfficialReportModal].forEach(modal => {
                            if (modal && !modal.classList.contains('hidden')) {
                                const content = modal.querySelector('[id$="ModalContent"]');
                                closeModal(modal, content);
                                clearModalForms(modal);
                            }
                        });
                    }
                });

                // Form submissions
                function handleFormSubmission(form, successMessage, redirectToIndex = false) {
                    if (!form) return;

                    form.addEventListener('submit', function (event) {
                        event.preventDefault();

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
                                    const modal = form.closest('[id$="Modal"]');
                                    closeModal(modal, modal.querySelector('[id$="ModalContent"]'));
                                    clearModalForms(modal);
                                    showToast(data.message || successMessage, 'success');

                                    setTimeout(() => {
                                        if (redirectToIndex) {
                                            window.location.href = "{{ route('official-report.index') }}";
                                        } else {
                                            window.location.reload();
                                        }
                                    }, 1000);
                                } else {
                                    showToast(data.message || 'Terjadi kesalahan', 'error');
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

                // Apply form handlers
                handleFormSubmission(editOfficialReportForm, 'Berita acara berhasil diperbarui');
                handleFormSubmission(deleteOfficialReportForm, 'Berita acara berhasil dihapus', true); // Redirect to index after delete
                handleFormSubmission(approveOfficialReportForm, 'Berita acara berhasil disetujui');
                handleFormSubmission(rejectOfficialReportForm, 'Berita acara berhasil ditolak');

                // Toast notification function
                function showToast(message, type = 'success') {
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 p-4 rounded shadow-md z-50 animate-slide-in-right max-w-md overflow-y-auto max-h-[80vh]';
                    notification.role = 'alert';

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
                        notification.classList.add('bg-red-100', 'border-l-4', 'border-red-500', 'text-red-700');
                        notification.innerHTML = `
                            <div class="flex items-start">
                                <div class="py-1">
                                    <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold">Gagal!</p>
                                    <div>${message}</div>
                                </div>
                                <span class="ml-4 cursor-pointer" onclick="this.parentElement.parentElement.remove()">×</span>
                            </div>
                        `;
                    }

                    document.body.appendChild(notification);
                    setTimeout(() => {
                        notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                        setTimeout(() => notification.remove(), 500);
                    }, 5000);
                }

                // CSS for animations
                document.head.insertAdjacentHTML('beforeend', `
                    <style>
                        @keyframes slideInRight {
                            from { transform: translateX(100%); }
                            to { transform: translateX(0); }
                        }
                        .animate-slide-in-right {
                            animation: slideInRight 0.3s ease-out forwards;
                        }
                    </style>
                `);
            });
        </script>
    @endpush
@endsection
