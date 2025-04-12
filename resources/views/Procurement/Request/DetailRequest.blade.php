@extends('Layout.app')

@section('title', 'Detail Request')

@section('content')
<div class="h-full space-y-4 md:space-y-6">
    <!-- Request Detail Section -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-4 md:p-7">
            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h1 class="text-2xl md:text-[32px] font-semibold text-[#213268]">DETAIL REQUEST</h1>
                </div>

                <!-- Request Details -->
                <div class="grid grid-cols-1 gap-5">
                    <!-- Request Number -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Request ID</p>
                        <p class="text-[#666666]">: <span id="requestNumber">{{ $procurement['procurement_code'] }}</span></p>
                    </div>

                    <!-- Request Name -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Request Title</p>
                        <p class="text-[#666666]">: <span id="requestName">{{ $procurement['title'] }}</span></p>
                    </div>

                    <!-- User Input -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Requester</p>
                        <p class="text-[#666666]">: <span id="userInput">{{ $procurement['requester']['first_name'] }} {{ $procurement['requester']['last_name'] }}</span></p>
                    </div>

                    <!-- Input Date -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Request Date</p>
                        <p class="text-[#666666]">: <span id="inputDate">{{ \Carbon\Carbon::parse($procurement['request_date'])->format('Y-m-d H:i:s') }}</span></p>
                    </div>

                    <!-- Status -->
                    <div class="flex items-start gap-2">
                        <p class="w-32 text-[#666666] font-medium">Status</p>
                        <p class="text-[#666666]">:
                            <span class="px-2 py-1 rounded-full text-xs
                                @if($procurement['status'] == 'Submitted') bg-blue-100 text-blue-800
                                @elseif($procurement['status'] == 'Approved') bg-green-100 text-green-800
                                @elseif($procurement['status'] == 'Rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $procurement['status'] }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Item List -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-[#666666]">Asset List</h2>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Asset Name</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Specification</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Qty</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Unit Price</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-left">Total</th>
                                    <th class="bg-[#213268] text-white p-3 font-bold text-xs text-center">Price Comparison</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($procurement['details'] as $detail)
                                <tr class="border-t border-[#EEF1F4]">
                                    <td class="p-3 text-xs text-[#666666]">{{ $detail['asset_name'] }}</td>
                                    <td class="p-3 text-xs text-[#666666]">{{ $detail['specifications'] }}</td>
                                    <td class="p-3 text-xs text-center text-[#666666]">{{ $detail['quantity'] }}</td>
                                    <td class="p-3 text-xs text-left text-[#666666]">{{ number_format($detail['estimated_unit_price'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-xs text-left text-[#666666]">{{ number_format($detail['estimated_total_price'], 0, ',', '.') }}</td>
                                    <td class="p-3 text-xs text-center">
                                        <span class="px-2 py-1 rounded-full text-xs
                                            @if($detail['status_price_comparison'] == 'Waiting') text-red-500
                                            @elseif($detail['status_price_comparison'] == 'Completed') text-green-500
                                            @else text-gray-500 @endif">
                                            {{ $detail['status_price_comparison'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach

                                <!-- Grand Total -->
                                <tr class="border-t border-[#EEF1F4]">
                                    <td colspan="4" class="p-3 text-xs font-medium text-right text-[#666666]">Grand Total</td>
                                    <td class="p-3 text-xs font-medium text-right text-[#666666]">{{ number_format($procurement['estimated_grand_total'], 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex gap-4 mt-8">
                    <a href="{{ route('procurement.request') }}" class="px-6 py-3 bg-[#333333] text-white rounded-lg text-base hover:bg-gray-800 transform active:scale-[0.98] transition-all duration-200">
                        CANCEL
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // If you need to load the data dynamically, you can do it here
        // For example, fetching the request details from an API

        // Example:
        fetch('/api/procurement/request/1')
            .then(response => response.json())
            .then(data => {
                document.getElementById('requestNumber').textContent = data.number;
                document.getElementById('requestName').textContent = data.name;
                document.getElementById('userInput').textContent = data.user;
                document.getElementById('inputDate').textContent = data.date;

                // Populate the items table
                // ...
            });
    });
</script>
@endpush
