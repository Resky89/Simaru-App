<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Pemantauan Aset</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #ffffff;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .blur-background {
            position: absolute;
            filter: blur(34.35px);
            z-index: -1;
            transition: all 0.5s ease;
        }

        .form-input {
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #213268;
            box-shadow: 0 0 0 3px rgba(33, 50, 104, 0.15);
        }

        .btn-primary {
            background-color: #213268;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #182552;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(33, 50, 104, 0.3);
        }

        .btn-primary:active {
            transform: translateY(1px);
            box-shadow: 0 2px 6px rgba(33, 50, 104, 0.2);
        }

        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(1deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        .custom-checkbox {
            width: 18px;
            height: 18px;
            border: 2px solid #213268;
            border-radius: 3px;
            position: relative;
            cursor: pointer;
            transition: background 0.2s;
        }

        .custom-checkbox:checked {
            background-color: #213268;
        }

        .custom-checkbox:checked:after {
            content: '';
            position: absolute;
            left: 5px;
            top: 1px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .masuk-text {
            color: #1B8ADB;
        }

        .subtitle {
            color: #1B8ADB;
        }

        .form-input-icon {
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .left-panel {
            background: #ffffff;
        }

        @media (max-width: 1023px) {
            .login-container {
                background: transparent;
                box-shadow: none;
            }
        }
    </style>
</head>

<body class="w-full min-h-screen bg-white">
    <!-- Background Blurs - Restored from original version -->
    <div class="fixed blur-background w-[300px] h-[300px] rounded-full -left-[100px] -top-[50px] bg-[#ACC3EF] opacity-70"></div>
    <div class="fixed blur-background w-[200px] h-[200px] rounded-full right-[40%] top-[100px] bg-[#7CE1FF] opacity-60 hidden md:block"></div>
    <div class="fixed blur-background w-[250px] h-[250px] rounded-full -right-[50px] -top-[50px] bg-[#25B1FF] opacity-70"></div>
    <div class="fixed blur-background w-[250px] h-[250px] rounded-full -left-[50px] -bottom-[50px] bg-[#25B1FF] opacity-70"></div>
    <div class="fixed blur-background w-[350px] h-[350px] rounded-full -right-[100px] -bottom-[100px] bg-[#213268] opacity-80"></div>

    <div class="min-h-screen w-full flex items-center justify-center p-4 relative">
        <div class="container max-w-6xl mx-auto">
            <div class="flex flex-col lg:flex-row rounded-3xl overflow-hidden shadow-2xl lg:login-container">
                <!-- Left side with illustration - Hidden on mobile -->
                <div class="w-full lg:w-7/12 left-panel p-6 flex-col relative hidden lg:flex">
                    <!-- Logo -->
                    <div class="mb-3">
                        <img src="images/Logo_RS_UMMI.png" alt="RS UMMI Logo" class="h-14 md:h-16">
    </div>

                    <!-- Content -->
                    <div class="mt-2 flex flex-col h-full">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-[#213268] mb-1">PEMANTAUAN ASET</h1>
                            <p class="subtitle text-base md:text-lg font-light mb-4">Pantau Setiap Aset, Kapan Saja, Dimana Saja</p>
    </div>

                        <div class="flex-grow flex items-center justify-center my-2">
                            <img src="images/pie_graph.svg" alt="Asset Monitoring Illustration" class="max-w-full max-h-[280px] floating">
            </div>
        </div>
                </div>

                <!-- Right side with login form -->
                <div class="w-full lg:w-5/12 bg-white p-6 flex flex-col justify-center rounded-3xl lg:rounded-none shadow-lg lg:shadow-none">
                    <!-- Mobile only logo -->
                    <div class="flex justify-center mb-5 lg:hidden">
                        <img src="images/Logo_RS_UMMI.png" alt="RS UMMI Logo" class="h-16">
                    </div>

                    <div class="mx-auto w-full max-w-md">
                        <div class="text-center mb-4">
                            <h2 class="text-xl md:text-2xl font-bold text-[#213268]">Selamat Datang Admin,</h2>
                            <p class="text-lg md:text-xl font-semibold">Silakan <span class="masuk-text">Masuk</span></p>
                        </div>

                        <form action="{{ route('auth.login') }}" method="POST" id="loginForm">
                    @csrf

                    @if ($errors->any())
                                <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-4 border-l-4 border-red-500 animate-pulse text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                            <!-- Employee Number -->
                            <div class="mb-4">
                                <label for="employee_number" class="block text-[#213268] font-medium mb-1 text-sm">Kode Karyawan</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" id="employee_number" name="employee_number" value="{{ old('employee_number') }}"
                            placeholder="Masukkan Kode Karyawan"
                                        class="form-input form-input-icon w-full h-10 pl-12 pr-4 rounded-xl border border-gray-300 focus:outline-none" required>
                                </div>
                    </div>

                            <!-- Password -->
                            <div class="mb-4">
                                <label for="password" class="block text-[#213268] font-medium mb-1 text-sm">Password</label>
                        <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <input type="password" id="password" name="password"
                                        placeholder="Masukkan Password"
                                        class="form-input form-input-icon w-full h-10 pl-12 pr-10 rounded-xl border border-gray-300 focus:outline-none" required>
                                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700">
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

                            <!-- Remember Me -->
                            <div class="flex items-center mb-5">
                                <input type="checkbox" id="remember" name="remember" class="custom-checkbox appearance-none">
                                <label for="remember" class="ml-2 text-gray-700 text-sm cursor-pointer">Ingat saya</label>
                    </div>

                            <!-- Login Button -->
                            <button type="submit" id="loginButton" class="btn-primary w-full h-11 rounded-xl text-white font-medium text-base shadow-md">
                        Masuk
                    </button>
                </form>

                        <!-- Footer -->
                        <div class="text-center mt-5 text-xs text-gray-500">
                            © {{ date('Y') }} RS UMMI - Sistem Pemantauan Aset
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
                        const togglePassword = document.getElementById('togglePassword');
                        const password = document.getElementById('password');
                        const eyeIcon = document.getElementById('eyeIcon');
                        const eyeOffIcon = document.getElementById('eyeOffIcon');

                        togglePassword.addEventListener('click', function() {
                            if (password.type === 'password') {
                                password.type = 'text';
                                eyeIcon.classList.add('hidden');
                                eyeOffIcon.classList.remove('hidden');
                            } else {
                                password.type = 'password';
                                eyeIcon.classList.remove('hidden');
                                eyeOffIcon.classList.add('hidden');
                            }
                password.focus();
            });

            // Show loading state on form submission
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');

            loginForm.addEventListener('submit', function() {
                if (loginForm.checkValidity()) {
                    loginButton.innerHTML = `
                        <div class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </div>
                    `;
                    loginButton.disabled = true;
                }
                        });
                    });
                </script>
</body>

</html>
