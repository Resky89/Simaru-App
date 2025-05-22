@extends('Layout.app')

@section('title', 'Detail Pemesanan')

@section('content')
@include('Layout.loading')
<div class="h-full space-y-4 md:space-y-6">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center">
                        <a href="{{ route('procurement.purchase-order') }}" class="mr-4 p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DETAIL PEMESANAN</h1>
                </div>

                    @if(isset($purchaseOrder) && !empty($purchaseOrder))
                    @if(hasPermission('purchase-order:export'))
                    <a href="{{ route('procurement.purchase-order.detail.export-pdf', ['id' => $purchaseOrder['purchase_order_id']]) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       id="exportPdfBtn"
                       class="flex items-center gap-2 px-4 py-3 border-2 border-[#213268] rounded-lg text-[#213268] hover:bg-[#213268] hover:text-white transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export PDF
                    </a>
                    @endif
                    @endif
                </div>

                @if(isset($purchaseOrder) && !empty($purchaseOrder))
                <!-- PO Details -->
                <div class="grid grid-cols-1 gap-5">
                    <!-- PO ID -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                        <p class="w-40 text-[#666666] font-medium">Nomor Pemesanan</p>
                        <p class="text-[#666666]"><span class="sm:hidden">: </span>{{ $purchaseOrder['purchase_order_code'] }}</p>
                    </div>

                    <!-- Comparison ID -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                        <p class="w-40 text-[#666666] font-medium">Nomor Penawaran</p>
                        <p class="text-[#666666]"><span class="sm:hidden">: </span>{{ $purchaseOrder['comparison_code'] ?? 'N/A' }}</p>
                    </div>

                    <!-- Vendor -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                        <p class="w-40 text-[#666666] font-medium">Vendor</p>
                        <p class="text-[#666666]"><span class="sm:hidden">: </span>{{ $purchaseOrder['vendor']['vendor_name'] ?? 'N/A' }}</p>
                    </div>

                    <!-- PIC -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                        <p class="w-40 text-[#666666] font-medium">Penanggung Jawab</p>
                        <p class="text-[#666666]"><span class="sm:hidden">: </span>{{ $purchaseOrder['vendor']['contact_person'] ?? 'N/A' }}</p>
                    </div>

                    <!-- PIC Contact -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                        <p class="w-40 text-[#666666] font-medium">Kontak Penanggung Jawab</p>
                        <p class="text-[#666666]"><span class="sm:hidden">: </span>{{ $purchaseOrder['vendor']['phone_number'] ?? 'N/A' }}</p>
                    </div>

                    <!-- PO Date -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                        <p class="w-40 text-[#666666] font-medium">Tanggal Pemesanan</p>
                        <p class="text-[#666666]"><span class="sm:hidden">: </span>
                            @if(isset($purchaseOrder['created_at']))
                                @php
                                    $date = \Carbon\Carbon::parse($purchaseOrder['created_at']);
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
                </div>

                <!-- Item List -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-[#666666]">DAFTAR ASET</h2>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-left">NAMA ASET</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-center">JUMLAH</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-right">HARGA SATUAN</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-right">TOTAL</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-sm text-center">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTotal = 0; @endphp

                                @if(isset($purchaseOrder['items']) && is_array($purchaseOrder['items']) && count($purchaseOrder['items']) > 0)
                                    @foreach($purchaseOrder['items'] as $item)
                                        @php
                                            $grandTotal += (float)($item['total_price'] ?? 0);
                                        @endphp
                                        <tr class="border-t border-[#EEF1F4]">
                                            <td class="p-3 text-sm text-[#666666]">{{ $item['procurement_item_name'] ?? 'N/A' }}</td>
                                            <td class="p-3 text-sm text-center text-[#666666]">{{ $item['quantity'] ?? 'N/A' }}</td>
                                            <td class="p-3 text-sm text-right text-[#666666]">
                                                <div class="text-sm font-medium">{{ isset($item['unit_price']) ? number_format((float)$item['unit_price'], 0, ',', '.') : 'N/A' }}</div>
                                            </td>
                                            <td class="p-3 text-sm text-right text-[#666666]">
                                                <div class="text-sm font-medium">{{ isset($item['total_price']) ? number_format((float)$item['total_price'], 0, ',', '.') : 'N/A' }}</div>
                                            </td>
                                            <td class="p-3 text-sm text-center">
                                                <span class="px-2 py-1 rounded-full text-sm {{ isset($purchaseOrder['completed_at']) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-500' }}">
                                                    {{ isset($purchaseOrder['completed_at']) ? 'Diterima' : 'Belum Diterima' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="p-3 text-center text-gray-500">Tidak ada item yang ditemukan untuk purchase order ini.</td>
                                    </tr>
                                @endif

                                <!-- Grand Total -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td colspan="3" class="p-3 text-sm font-medium text-right text-[#666666]">Total Keseluruhan</td>
                                    <td class="p-3 text-sm text-right text-[#666666]">
                                        <div class="text-sm font-medium">{{ number_format($grandTotal, 0, ',', '.') }}</div>
                                    </td>
                                    <td></td>
                                </tr>

                                <!-- Payment Terms Row -->
                                <tr class="border-t border-[#EEF1F4] bg-[#E9ECF6]">
                                    <td class="p-3 text-sm font-medium text-left text-[#213268]">Syarat Pembayaran</td>
                                    <td colspan="4" class="p-3 text-sm text-[#666666]">
                                        {{ $purchaseOrder['payment_terms'] ?? 'Tidak ada data' }}
                                    </td>
                                </tr>

                                <!-- Delivery Terms Row -->
                                <tr class="border-t border-[#EEF1F4] bg-[#E9ECF6]">
                                    <td class="p-3 text-sm font-medium text-left text-[#213268]">Syarat Pengiriman</td>
                                    <td colspan="4" class="p-3 text-sm text-[#666666]">
                                        {{ $purchaseOrder['delivery_terms'] ?? 'Tidak ada data' }}
                                    </td>
                                </tr>

                                <!-- Notes Row -->
                                <tr class="border-t border-[#EEF1F4] bg-[#E9ECF6]">
                                    <td class="p-3 text-sm font-medium text-left text-[#213268]">Catatan</td>
                                    <td colspan="4" class="p-3 text-sm text-[#666666]">
                                        {{ $purchaseOrder['notes'] ?? 'Tidak ada data' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <!-- Not Found State -->
                <div class="flex flex-col items-center justify-center py-8">
                    <svg class="w-16 h-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Pemesanan Tidak Ditemukan</h2>
                    <p class="text-gray-600 mb-8">{{ $error ?? 'Data pemesanan yang diminta tidak dapat ditemukan atau telah dihapus.' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check permissions and hide elements if needed
        @if(!hasPermission('purchase-order:export'))
        // Hide export PDF button if user doesn't have permission
        const exportButtons = document.querySelectorAll('#exportPdfBtn');
        exportButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
            }
        });
        @endif
    });
</script>
@endpush
