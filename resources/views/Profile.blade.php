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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
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

                    <!-- Display error message if exists -->
                    @if(isset($error))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ $error }}
                        </div>
                    @endif

                    <div class="space-y-4">
                        <!-- Employee Name -->
                        <div>
                            <label for="employee_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan</label>
                            <input type="text" id="employee_name"
                                value="{{ isset($profileData['employee_name']) ? $profileData['employee_name'] : ($accessTokenPayload['employee_name'] ?? 'Tidak tersedia') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed"
                                disabled>
                        </div>

                        <!-- Employee Number -->
                        <div>
                            <label for="employee_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor
                                Karyawan</label>
                            <input type="text" id="employee_number"
                                value="{{ isset($profileData['employee_number']) ? $profileData['employee_number'] : ($accessTokenPayload['employee_number'] ?? 'Tidak tersedia') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed"
                                disabled>
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                            @if(isset($profileData['roles']) && is_array($profileData['roles']) && count($profileData['roles']) > 0)
                                <div class="space-y-2">
                                    @foreach($profileData['roles'] as $role)
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                {{ $role['role_name'] ?? 'Unknown' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <input type="text" id="role"
                                    value="{{ is_array($accessTokenPayload['role_names'] ?? null) ? implode(', ', $accessTokenPayload['role_names']) : ($accessTokenPayload['role_names'] ?? 'Tidak tersedia') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50 cursor-not-allowed"
                                    disabled>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
