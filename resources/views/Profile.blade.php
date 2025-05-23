@extends('Layout.app')

@section('title', 'Profil')

@section('content')
<div class="h-full">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Left Section - User Icon & Info -->
        <div class="md:col-span-3 bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <!-- Person Icon -->
            <div class="mb-6 mt-4">
                <div class="w-40 h-40 rounded-full bg-[#213268]/10 flex items-center justify-center text-[#213268]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>

            <!-- Last Login -->
            @php
                $accessTokenPayload = session('access_token_payload', []);
                setlocale(LC_TIME, 'id_ID');
                \Carbon\Carbon::setLocale('id');
                $lastLoginTime = isset($accessTokenPayload['iat']) ?
                    \Carbon\Carbon::createFromTimestamp($accessTokenPayload['iat'])->translatedFormat('d F Y H:i') :
                    'Tidak tersedia';
            @endphp
            <div class="text-center text-gray-500 text-sm">
                <p>Login terakhir:</p>
                <p class="font-medium">{{ $lastLoginTime }}</p>
            </div>
        </div>

        <!-- Right Section - Forms -->
        <div class="md:col-span-9 space-y-6">
            <!-- Profile Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-[#213268] mb-6">Informasi Profil</h3>

                <div class="space-y-4">
                    <!-- Employee Number -->
                    <div>
                        <label for="employee_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Karyawan</label>
                        <input type="text" id="employee_number" value="{{ $accessTokenPayload['employee_number'] ?? 'Tidak tersedia' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed" disabled>
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                        <input type="text" id="role"
                               value="{{ is_array($accessTokenPayload['roles'] ?? null) ? implode(', ', $accessTokenPayload['roles']) : ($accessTokenPayload['roles'] ?? 'Tidak tersedia') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
