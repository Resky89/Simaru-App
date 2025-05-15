<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Pemantauan Aset</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .blur-background {
            position: absolute;
            filter: blur(34.35px);
            z-index: -1;
        }
    </style>
</head>

<body class="relative w-screen h-screen overflow-hidden bg-white">
    <!-- Background Blurs Circles -->
    <div class="absolute blur-background w-[300px] h-[300px] rounded-full left-[-100px] top-[-50px] bg-[#ACC3EF]"></div>
    <div class="absolute blur-background w-[200px] h-[200px] rounded-full right-[500px] top-[100px] bg-[#7CE1FF]"></div>
    <div class="absolute blur-background w-[250px] h-[250px] rounded-full right-[-50px] top-[-50px] bg-[#25B1FF]"></div>
    <div class="absolute blur-background w-[250px] h-[250px] rounded-full left-[-50px] bottom-[-50px] bg-[#25B1FF]">
    </div>
    <div class="absolute blur-background w-[350px] h-[350px] rounded-full right-[-100px] bottom-[-100px] bg-[#213268]">
    </div>

    <!-- Logo -->
    <div class="absolute left-1/2 transform -translate-x-1/2 md:left-8 md:transform-none top-8">
        <img src="images/Logo_RS_UMMI.png" alt="Logo RS UMMI" class="w-48">
    </div>

    <!-- Main Content -->
    <div class="flex flex-col md:flex-row h-screen">
        <!-- Left Section -->
        <div class="hidden md:flex flex-1 flex-col items-center justify-center px-4 md:px-20">
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-['Poppins'] font-semibold text-[#213268] mb-2">
                    PEMANTAUAN ASET
                </h1>
                <p class="text-lg md:text-xl font-['Poppins'] text-[#1B8ADB]">
                    Pantau Setiap Aset, Kapan Saja, Dimana Saja
                </p>
            </div>
            <div class="w-full max-w-[500px]">
                <img src="images/pie_graph.svg" alt="Asset Monitoring Illustration" class="w-full">
            </div>
        </div>

        <!-- Right Section -->
        <div class="flex-1 flex items-center justify-center p-4">
            <div class="bg-white p-6 md:p-10 rounded-[30px] shadow-2xl w-full max-w-[450px]">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-['Poppins'] font-semibold text-[#213268]">
                        Selamat Datang Admin,
                    </h2>
                    <p class="text-3xl font-['Poppins'] font-semibold">
                        Silakan <span class="text-[#1B8ADB]">Masuk</span>
                    </p>
                </div>

                <form action="{{ route('auth.login') }}" method="POST" class="space-y-6">
                    @csrf

                    @if ($errors->any())
                        <div class="bg-red-50 text-red-500 p-4 rounded-lg mb-6">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label class="block text-[#213268] font-['Poppins']">Kode Karyawan</label>
                        <input type="text" name="employee_number" value="{{ old('employee_number') }}"
                            placeholder="Masukkan Kode Karyawan"
                            class="w-full p-3 border border-gray-200 rounded-lg font-['Poppins'] text-gray-600 focus:outline-none focus:border-[#1B8ADB]"
                            required>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[#213268] font-['Poppins']">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="Masukkan Password"
                                class="w-full p-3 border border-gray-200 rounded-lg font-['Poppins'] text-gray-600 focus:outline-none focus:border-[#1B8ADB]"
                                required>
                            <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <!-- Eye icon (password hidden) -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" id="eyeIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <!-- Eye-off icon (password visible) -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" id="eyeOffIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Tambahkan checkbox Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="mr-2">
                        <label for="remember" class="text-[#213268] font-['Poppins'] text-sm">Ingat saya</label>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-[#213268] text-white rounded-lg font-['Poppins'] hover:bg-[#1a2857] transition-colors">
                        Masuk
                    </button>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const togglePassword = document.getElementById('togglePassword');
                        const password = document.getElementById('password');
                        const eyeIcon = document.getElementById('eyeIcon');
                        const eyeOffIcon = document.getElementById('eyeOffIcon');

                        togglePassword.addEventListener('click', function() {
                            // Toggle the password visibility
                            if (password.type === 'password') {
                                password.type = 'text';
                                eyeIcon.classList.add('hidden');
                                eyeOffIcon.classList.remove('hidden');
                            } else {
                                password.type = 'password';
                                eyeIcon.classList.remove('hidden');
                                eyeOffIcon.classList.add('hidden');
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</body>

</html>