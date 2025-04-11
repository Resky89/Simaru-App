<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Asset Monitoring</title>
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
    <div class="absolute blur-background w-[250px] h-[250px] rounded-full left-[-50px] bottom-[-50px] bg-[#25B1FF]"></div>
    <div class="absolute blur-background w-[350px] h-[350px] rounded-full right-[-100px] bottom-[-100px] bg-[#213268]"></div>

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
                    ASSETS MONITORING
                </h1>
                <p class="text-lg md:text-xl font-['Poppins'] text-[#1B8ADB]">
                    Track Every Asset, Anytime, Anywhere
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
                        Welcome Admin,
                    </h2>
                    <p class="text-3xl font-['Poppins'] font-semibold">
                        Please <span class="text-[#1B8ADB]">Login</span>
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
                        <label class="block text-[#213268] font-['Poppins']">Email</label>
                        <input
                            type="text"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Insert Your Email"
                            class="w-full p-3 border border-gray-200 rounded-lg font-['Poppins'] text-gray-600 focus:outline-none focus:border-[#1B8ADB]"
                            required
                        >
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[#213268] font-['Poppins']">Password</label>
                        <input
                            type="password"
                            name="password"
                            placeholder="Insert Your Password"
                            class="w-full p-3 border border-gray-200 rounded-lg font-['Poppins'] text-gray-600 focus:outline-none focus:border-[#1B8ADB]"
                            required
                        >
                    </div>

                    <!-- Tambahkan checkbox Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="mr-2">
                        <label for="remember" class="text-[#213268] font-['Poppins'] text-sm">Ingat saya</label>
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#213268] text-white rounded-lg font-['Poppins'] hover:bg-[#1a2857] transition-colors">
                        Log in
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
