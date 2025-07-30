@extends('Layout.app')

@section('title', 'Detail Berita Acara')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('Layout.loading')
    <div class="h-full space-y-4 md:space-y-6">
        <!-- Header with Back Button -->
        <div class="flex items-center gap-4 mb-6">
            <button type="button" id="backButton"
                class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
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
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Tipe</label>
                                        @php
                                            $typeColor = 'bg-gray-500';
                                            $typeText = 'Tidak Diketahui';

                                            switch ($official_report['report_type']) {
                                                case 'DISPOSAL':
                                                    $typeColor = 'bg-[#ACC3EF]';
                                                    $typeText = 'DIHAPUSKAN';
                                                    break;
                                                case 'LOSS':
                                                    $typeColor = 'bg-[#EF4444]';
                                                    $typeText = 'HILANG';
                                                    break;
                                                case 'FOUND':
                                                    $typeColor = 'bg-[#659B09]';
                                                    $typeText = 'DITEMUKAN';
                                                    break;
                                                default:
                                                    $typeText = $official_report['report_type'];
                                            }
                                        @endphp
                                        <div class="{{ $typeColor }} py-0.5 px-3 rounded-md text-center inline-block">
                                            <p class="text-xs text-white font-medium">{{ $typeText }}</p>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                                        @php
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                            $statusText = 'Tidak Diketahui';

                                            switch ($official_report['status']) {
                                                case 'SUBMITTED':
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                    $statusText = 'Diajukan';
                                                    break;
                                                case 'APPROVED':
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                    $statusText = 'Disetujui';
                                                    break;
                                                case 'REJECTED':
                                                    $statusClass = 'bg-red-100 text-red-800';
                                                    $statusText = 'Ditolak';
                                                    break;
                                                case 'PENDING':
                                                    $statusClass = 'bg-blue-100 text-blue-800';
                                                    $statusText = 'Menunggu';
                                                    break;
                                                default:
                                                    $statusText = $official_report['status'];
                                            }
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Rilis</label>
                                        <p class="text-base text-gray-900">
                                            @if($official_report['release_date'] !== 'N/A' && $official_report['release_date'])
                                                {{ \Carbon\Carbon::parse($official_report['release_date'])->locale('id')->isoFormat('D MMMM YYYY') }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Dibuat Oleh</label>
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-[#213268] rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm font-medium">
                                                    {{ substr($official_report['created_by']['employee_name'] ?? '-', 0, 1) }}
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
                                        <p class="text-base text-gray-900">
                                            @if($official_report['created_at'])
                                                {{ \Carbon\Carbon::parse($official_report['created_at'])->locale('id')->isoFormat('D MMMM YYYY') }}
                                            @else
                                                -
                                            @endif
                                        </p>
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
                                                    @php
                                                        $assetImageUrl = '';
                                                        $backendUrl = config('app.backend_url', 'https://web-magangunbin2025.rsummi.co.id/api');

                                                        // First check for asset_image_path (preferred)
                                                        if (isset($item['asset']['asset_image_path']) && $item['asset']['asset_image_path']) {
                                                            $assetImageUrl = $backendUrl . '/public' . $item['asset']['asset_image_path'];
                                                        }
                                                        // Then check for asset_image
                                                        elseif (isset($item['asset']['asset_image']) && $item['asset']['asset_image']) {
                                                            if (str_starts_with($item['asset']['asset_image'], '/')) {
                                                                // Use backend URL for paths that start with /
                                                                $assetImageUrl = $backendUrl . '/public' . $item['asset']['asset_image'];
                                                            } else {
                                                                // For relative paths, prepend with backend URL
                                                                $assetImageUrl = $backendUrl . '/public/' . $item['asset']['asset_image'];
                                                            }
                                                        }
                                                        // Fallback to placeholder
                                                        else {
                                                            $assetImageUrl = asset('images/placeholder.png');
                                                        }
                                                    @endphp
                                                    <!-- Debug: Generated URL = {{ $assetImageUrl }} -->
                                                    <img src="{{ $assetImageUrl }}"
                                                         alt="Asset Image"
                                                         class="w-48 h-48 object-contain rounded-lg bg-gray-100 p-3"
                                                         onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}'; this.alt='Gambar tidak tersedia'; this.classList.add('object-contain', 'p-3');"
                                                         title="URL: {{ $assetImageUrl }}">
                                                </div>
                                                <div class="flex-grow">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <h3 class="font-semibold text-gray-900 mb-2 text-base">{{ $item['asset']['asset_name'] ?? '-' }}</h3>
                                                            <p class="text-sm text-gray-600 mb-3 font-medium">{{ $item['asset']['asset_code'] ?? '-' }}</p>
                                                            <div class="space-y-2">
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Merk:</span> {{ $item['asset']['brand_name'] ?? '-' }}
                                                                </p>
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Model:</span> {{ $item['asset']['model'] ?? '-' }}
                                                                </p>
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Serial:</span> {{ $item['asset']['serial_number'] ?? '-' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="space-y-2">
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Tanggal Pembelian:</span>
                                                                    @if($item['asset']['purchase_date'] && $item['asset']['purchase_date'] !== '-')
                                                                        @php
                                                                            try {
                                                                                // Handle DD-MM-YYYY format
                                                                                $purchaseDate = \Carbon\Carbon::createFromFormat('d-m-Y', $item['asset']['purchase_date']);
                                                                                echo $purchaseDate->locale('id')->isoFormat('D MMMM YYYY');
                                                                            } catch (\Exception $e) {
                                                                                // Fallback if format is different
                                                                                try {
                                                                                    $purchaseDate = \Carbon\Carbon::parse($item['asset']['purchase_date']);
                                                                                    echo $purchaseDate->locale('id')->isoFormat('D MMMM YYYY');
                                                                                } catch (\Exception $e2) {
                                                                                    echo $item['asset']['purchase_date'];
                                                                                }
                                                                            }
                                                                        @endphp
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </p>
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Harga Pembelian:</span>
                                                                    @if($item['asset']['purchase_cost'])
                                                                        Rp {{ number_format($item['asset']['purchase_cost'], 0, ',', '.') }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </p>
                                                            </div>
                                                            @if($item['item_notes'])
                                                                <div class="mt-3">
                                                                    <p class="text-sm font-medium text-gray-700 mb-1">Catatan Item:</p>
                                                                    <p class="text-sm text-gray-600">{{ $item['item_notes'] }}</p>
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
                                            @php
                                                $approval1StatusText = 'Tidak Diketahui';
                                                switch ($official_report['approval_1_status']) {
                                                    case 'APPROVED':
                                                        $approval1StatusText = 'DISETUJUI';
                                                        break;
                                                    case 'REJECTED':
                                                        $approval1StatusText = 'DITOLAK';
                                                        break;
                                                    case 'PENDING':
                                                        $approval1StatusText = 'MENUNGGU';
                                                        break;
                                                    default:
                                                        $approval1StatusText = $official_report['approval_1_status'];
                                                }
                                            @endphp
                                            <span class="text-xs px-2 py-1 rounded-full
                                                @if($official_report['approval_1_status'] == 'APPROVED') bg-green-100 text-green-800
                                                @elseif($official_report['approval_1_status'] == 'REJECTED') bg-red-100 text-red-800
                                                @elseif($official_report['approval_1_status'] == 'PENDING') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $approval1StatusText }}
                                            </span>
                                        </div>
                                        @if($official_report['approval_1_by']['employee_name'])
                                            <p class="text-sm text-gray-600 mt-1">{{ $official_report['approval_1_by']['employee_name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $official_report['approval_1_by']['employee_number'] }}</p>
                                        @endif
                                        @if($official_report['approval_1_date'] !== 'N/A' && $official_report['approval_1_date'])
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ \Carbon\Carbon::parse($official_report['approval_1_date'])->locale('id')->isoFormat('D MMMM YYYY') }}
                                            </p>
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
                                            @php
                                                $approval2StatusText = 'Tidak Diketahui';
                                                switch ($official_report['approval_2_status']) {
                                                    case 'APPROVED':
                                                        $approval2StatusText = 'DISETUJUI';
                                                        break;
                                                    case 'REJECTED':
                                                        $approval2StatusText = 'DITOLAK';
                                                        break;
                                                    case 'PENDING':
                                                        $approval2StatusText = 'MENUNGGU';
                                                        break;
                                                    default:
                                                        $approval2StatusText = $official_report['approval_2_status'];
                                                }
                                            @endphp
                                            <span class="text-xs px-2 py-1 rounded-full
                                                @if($official_report['approval_2_status'] == 'APPROVED') bg-green-100 text-green-800
                                                @elseif($official_report['approval_2_status'] == 'REJECTED') bg-red-100 text-red-800
                                                @elseif($official_report['approval_2_status'] == 'PENDING') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $approval2StatusText }}
                                            </span>
                                        </div>
                                        @if($official_report['approval_2_by']['employee_name'])
                                            <p class="text-sm text-gray-600 mt-1">{{ $official_report['approval_2_by']['employee_name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $official_report['approval_2_by']['employee_number'] }}</p>
                                        @endif
                                        @if($official_report['approval_2_date'] !== 'N/A' && $official_report['approval_2_date'])
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ \Carbon\Carbon::parse($official_report['approval_2_date'])->locale('id')->isoFormat('D MMMM YYYY') }}
                                            </p>
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
                                                @if($official_report['rejected_date'] !== 'N/A' && $official_report['rejected_date'])
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ \Carbon\Carbon::parse($official_report['rejected_date'])->locale('id')->isoFormat('D MMMM YYYY') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    @php
                        // Get current user data from session (set by AuthController)
                        $userEmployeeName = session('employee_name', '');
                        $userEmployeeNumber = session('employee_number', '');

                        // Also try to get from profile data if available
                        $profileData = session('profile_data', []);
                        if (empty($userEmployeeName) && isset($profileData['employee_name'])) {
                            $userEmployeeName = $profileData['employee_name'];
                        }
                        if (empty($userEmployeeNumber) && isset($profileData['employee_number'])) {
                            $userEmployeeNumber = $profileData['employee_number'];
                        }

                        $hasAlreadyApproved = false;

                        // Check if current user has already approved (by employee name)
                        if (!empty($userEmployeeName)) {
                            $approval1Name = $official_report['approval_1_by']['employee_name'] ?? '';
                            $approval2Name = $official_report['approval_2_by']['employee_name'] ?? '';

                            if ($approval1Name === $userEmployeeName || $approval2Name === $userEmployeeName) {
                            $hasAlreadyApproved = true;
                        }
                        }

                        // Check if current user has already approved (by employee number)
                        if (!$hasAlreadyApproved && !empty($userEmployeeNumber)) {
                            $approval1Number = $official_report['approval_1_by']['employee_number'] ?? '';
                            $approval2Number = $official_report['approval_2_by']['employee_number'] ?? '';

                            if ($approval1Number === $userEmployeeNumber || $approval2Number === $userEmployeeNumber) {
                            $hasAlreadyApproved = true;
                            }
                        }

                        // Check if report has been approved by anyone
                        $isApproved = ($official_report['approval_1_status'] == 'APPROVED' ||
                                      $official_report['approval_2_status'] == 'APPROVED');
                    @endphp

                    <!-- Basic Actions Card -->
                    @if(hasPermission('official-report:edit') && !$isApproved)
                        <div class="card bg-base-100 shadow-xl mb-6">
                            <div class="card-body p-4 md:p-7">
                                <h2 class="text-xl font-semibold text-[#213268] mb-4">Aksi Dasar</h2>
                                <div class="space-y-3">
                                    <a href="{{ route('official-report.edit', $official_report['official_report_id']) }}"
                                       class="w-full block px-4 py-2 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit Berita Acara
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php
                        // Separate conditions for approve and reject buttons
                        $canApprove = !$hasAlreadyApproved &&
                                     ($official_report['status'] == 'SUBMITTED') &&
                                     hasPermission('official-report:approve');

                        $canReject = hasPermission('official-report:reject') &&
                                    (($official_report['status'] == 'SUBMITTED') ||
                                     ($hasAlreadyApproved && $official_report['status'] == 'APPROVED'));
                    @endphp

                    @if($canApprove || $canReject)
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body p-4 md:p-7">
                                <h2 class="text-xl font-semibold text-[#213268] mb-4">Aksi Persetujuan</h2>
                                <div class="space-y-3">
                                    @if($canApprove)
                                        <button class="approve-report-btn w-full px-4 py-2 bg-green-100 text-green-800 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium"
                                                data-report-id="{{ $official_report['official_report_id'] }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Setujui Berita Acara
                                        </button>
                                    @endif

                                    @if($canReject)
                                        <button class="reject-report-btn w-full px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium"
                                                data-report-id="{{ $official_report['official_report_id'] }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            @if($hasAlreadyApproved && $official_report['status'] == 'APPROVED')
                                                Batalkan Persetujuan
                                            @else
                                                Tolak Berita Acara
                                            @endif
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
            </div>
        </div>
    @endif
                        </div>

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
                            <h2 id="rejectModalTitle" class="text-xl sm:text-2xl font-semibold text-[#203268]">TOLAK BERITA ACARA</h2>
                            <button class="close-modal p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6 text-[#757575]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <form id="rejectOfficialReportForm" action="" method="POST" data-no-loading>
                                @csrf
                                <div class="space-y-4 max-w-[400px] mx-auto">
                                    <!-- Hidden ID -->
                                    <input type="hidden" id="rejectReportId" name="report_id">

                                    <!-- Confirmation Message -->
                                    <div class="flex flex-col items-center">
                                        <svg class="mb-4 w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                        <p id="rejectModalMessage" class="text-base text-gray-600 text-center">Apakah Anda yakin ingin menolak berita acara ini?</p>
                                    </div>

                                    <div class="flex gap-3">
                                        <button type="button"
                                            class="close-modal w-1/2 h-[45px] bg-gray-200 text-gray-800 rounded-lg text-base hover:bg-gray-300 transform active:scale-[0.98] transition-all duration-200">
                                            Batal
                                        </button>
                                        <button type="submit" id="rejectModalSubmitBtn"
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
                // Back button handler
                document.getElementById('backButton').addEventListener('click', function (e) {
                    window.location.href = '{{ route("official-report.index") }}';
                });

                // Modal elements
                const approveOfficialReportModal = document.getElementById('approveOfficialReportModal');
                const rejectOfficialReportModal = document.getElementById('rejectOfficialReportModal');
                const closeButtons = document.querySelectorAll('.close-modal');

                // Form elements
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

                        // Check if this is a cancel approval action
                        const buttonText = button.textContent.trim();
                        const isCancelApproval = buttonText.includes('Batalkan Persetujuan');

                        // Update modal content based on context
                        const modalTitle = document.getElementById('rejectModalTitle');
                        const modalMessage = document.getElementById('rejectModalMessage');
                        const modalSubmitBtn = document.getElementById('rejectModalSubmitBtn');

                        if (isCancelApproval) {
                            modalTitle.textContent = 'BATALKAN PERSETUJUAN';
                            modalMessage.textContent = 'Apakah Anda yakin ingin membatalkan persetujuan yang telah Anda berikan untuk berita acara ini?';
                            modalSubmitBtn.textContent = 'Batalkan';
                        } else {
                            modalTitle.textContent = 'TOLAK BERITA ACARA';
                            modalMessage.textContent = 'Apakah Anda yakin ingin menolak berita acara ini?';
                            modalSubmitBtn.textContent = 'Tolak';
                        }

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
                [approveOfficialReportModal, rejectOfficialReportModal].forEach(modal => {
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
                        [approveOfficialReportModal, rejectOfficialReportModal].forEach(modal => {
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

                            // Check if this is the reject form - use JSON for reject
                            if (this.id === 'rejectOfficialReportForm') {
                                const reportId = document.getElementById('rejectReportId').value;

                                const data = {
                                    report_id: reportId
                                };

                                fetch(this.action, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                                    },
                                    body: JSON.stringify(data)
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
                                        // Parse error response with comprehensive error handling
                                        const errorData = data.errors || [];
                                        let errorMessage = data.message || 'Terjadi kesalahan saat memproses permintaan';

                                        // Handle different error response formats - display directly without lists
                                        if (typeof errorData === 'string') {
                                            errorMessage = errorData;
                                        } else if (Array.isArray(errorData)) {
                                            if (errorData.length > 0) {
                                                if (errorData[0].message) {
                                                    errorMessage = errorData[0].message;
                                                } else if (typeof errorData[0] === 'string') {
                                                    errorMessage = errorData[0];
                                                }
                                            }
                                        } else if (typeof errorData === 'object' && Object.keys(errorData).length > 0) {
                                            const firstError = Object.values(errorData)[0];
                                            if (Array.isArray(firstError)) {
                                                errorMessage = firstError[0];
                                            } else if (typeof firstError === 'string') {
                                                errorMessage = firstError;
                                            }
                                        }

                                        showToast(errorMessage, 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    submitBtn.disabled = false;
                                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                    submitBtn.innerHTML = originalText;

                                    let errorMessage = 'Terjadi kesalahan saat menghubungi server';

                                    // Provide more specific error messages based on error type
                                    if (error.message.includes('JSON')) {
                                        errorMessage = 'Server mengembalikan response yang tidak valid. Silakan coba lagi atau hubungi administrator.';
                                    } else if (error.message.includes('HTTP')) {
                                        errorMessage = `Kesalahan server: ${error.message}. Silakan coba lagi.`;
                                    } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
                                        errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                                    }

                                    showToast(errorMessage, 'error');
                                });
                            } else {
                                // Use FormData for other forms
                                const formData = new FormData(this);

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
                                    // Parse error response with comprehensive error handling
                                    const errorData = data.errors || [];
                                    let errorMessage = data.message || 'Terjadi kesalahan saat memproses permintaan';

                                    // Handle different error response formats - display directly without lists
                                    if (typeof errorData === 'string') {
                                        errorMessage = errorData;
                                    } else if (Array.isArray(errorData)) {
                                        if (errorData.length > 0) {
                                            if (errorData[0].message) {
                                                errorMessage = errorData[0].message;
                                            } else if (typeof errorData[0] === 'string') {
                                                errorMessage = errorData[0];
                                            }
                                        }
                                    } else if (typeof errorData === 'object' && Object.keys(errorData).length > 0) {
                                        const firstError = Object.values(errorData)[0];
                                        if (Array.isArray(firstError)) {
                                            errorMessage = firstError[0];
                                        } else if (typeof firstError === 'string') {
                                            errorMessage = firstError;
                                        }
                                    }

                                    showToast(errorMessage, 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;

                                let errorMessage = 'Terjadi kesalahan saat menghubungi server';

                                // Provide more specific error messages based on error type
                                if (error.message.includes('JSON')) {
                                    errorMessage = 'Server mengembalikan response yang tidak valid. Silakan coba lagi atau hubungi administrator.';
                                } else if (error.message.includes('HTTP')) {
                                    errorMessage = `Kesalahan server: ${error.message}. Silakan coba lagi.`;
                                } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
                                    errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                                }

                                showToast(errorMessage, 'error');
                            });
                            }
                        }
                    });
                }

                // Apply form handlers
                handleFormSubmission(approveOfficialReportForm, 'Berita acara berhasil disetujui');

                // Special handling for reject form to provide dynamic success message
                if (rejectOfficialReportForm) {
                    rejectOfficialReportForm.addEventListener('submit', function (event) {
                        event.preventDefault();

                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerHTML;
                        const isCancelApproval = submitBtn.textContent.includes('Batalkan');
                        const successMessage = isCancelApproval ?
                            'Persetujuan berhasil dibatalkan' :
                            'Berita acara berhasil ditolak';

                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                            submitBtn.innerHTML = `
                                <div class="flex items-center justify-center">
                                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                                    <span>Memproses...</span>
                                </div>
                            `;

                            const reportId = document.getElementById('rejectReportId').value;
                            const data = { report_id: reportId };

                            fetch(this.action, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                                },
                                body: JSON.stringify(data)
                            })
                            .then(response => response.json())
                            .then(data => {
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;

                                if (data.success) {
                                    const modal = this.closest('[id$="Modal"]');
                                    closeModal(modal, modal.querySelector('[id$="ModalContent"]'));
                                    clearModalForms(modal);
                                    showToast(data.message || successMessage, 'success');

                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                } else {
                                    // Parse error response
                                    const errorData = data.errors || [];
                                    let errorMessage = data.message || 'Terjadi kesalahan saat memproses permintaan';

                                    if (typeof errorData === 'string') {
                                        errorMessage = errorData;
                                    } else if (Array.isArray(errorData) && errorData.length > 0) {
                                        if (errorData[0].message) {
                                            errorMessage = errorData[0].message;
                                        } else if (typeof errorData[0] === 'string') {
                                            errorMessage = errorData[0];
                                        }
                                    } else if (typeof errorData === 'object' && Object.keys(errorData).length > 0) {
                                        const firstError = Object.values(errorData)[0];
                                        if (Array.isArray(firstError)) {
                                            errorMessage = firstError[0];
                                        } else if (typeof firstError === 'string') {
                                            errorMessage = firstError;
                                        }
                                    }

                                    showToast(errorMessage, 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalText;

                                let errorMessage = 'Terjadi kesalahan saat menghubungi server';
                                if (error.message.includes('JSON')) {
                                    errorMessage = 'Server mengembalikan response yang tidak valid. Silakan coba lagi atau hubungi administrator.';
                                } else if (error.message.includes('HTTP')) {
                                    errorMessage = `Kesalahan server: ${error.message}. Silakan coba lagi.`;
                                } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
                                    errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                                }

                                showToast(errorMessage, 'error');
                            });
                        }
                    });
                }

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

                        /* Error field styling */
                        .border-red-500 {
                            border-color: #f56565 !important;
                            box-shadow: 0 0 0 1px #f56565 !important;
                        }
                    </style>
                `);

                // Function to highlight field errors (kept for future use)
                function highlightFieldError(fieldName, errorMessage) {
                    const field = document.getElementById(fieldName);
                    if (!field) return;

                    field.classList.add('border-red-500');

                    const errorElement = field.closest('.space-y-2')?.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = errorMessage;
                        errorElement.classList.remove('hidden');
                    }
                }

                // Function to clear field errors
                function clearFieldErrors() {
                    document.querySelectorAll('.border-red-500').forEach(field => {
                        field.classList.remove('border-red-500');
                    });
                    document.querySelectorAll('.error-message:not(.hidden)').forEach(errorElement => {
                        errorElement.classList.add('hidden');
                    });
                }
            });
        </script>
    @endpush
@endsection
