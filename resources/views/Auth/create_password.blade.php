<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Password - Asset Monitoring</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600&family=Inter:wght@400;500&display=swap" rel="stylesheet">
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
        <div class="flex-1 flex items-center justify-center p-4 md:ml-20">
            <div class="bg-white p-6 md:p-10 rounded-[30px] shadow-2xl w-full max-w-[450px]">
                <div class="flex flex-col gap-[9px]">
                    <h2 class="text-[28px] font-['Inter'] font-semibold leading-[34px] tracking-[-0.25px] text-[#213268]">
                        Create Your New Password
                    </h2>

                    <form action="{{ route('password.store') }}" method="POST" class="mt-6">
                        @csrf

                        @if ($errors->any())
                        <div class="bg-red-50 text-red-500 p-4 rounded-lg mb-6">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="p-6 border border-[#D9D9D9] rounded-lg">
                            <div class="space-y-2 mb-6">
                                <label class="block text-[#213268] font-['Inter'] text-base leading-[140%]">New Password</label>
                                <input
                                    type="password"
                                    name="password"
                                    placeholder="Insert Your New Password"
                                    class="w-full p-[12px_16px] border border-[#D9D9D9] rounded-lg font-['Inter'] text-sm text-[#303030] focus:outline-none placeholder:text-[#B3B3B3]"
                                    required
                                >
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[#213268] font-['Inter'] text-base leading-[140%]">Confirm Password</label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    placeholder="Confirm Your Password"
                                    class="w-full p-[12px_16px] border border-[#D9D9D9] rounded-lg font-['Inter'] text-sm text-[#303030] focus:outline-none placeholder:text-[#B3B3B3]"
                                    required
                                >
                            </div>

                            <button type="submit" class="w-full mt-6 py-3 bg-[#213268] text-white text-sm rounded-lg font-['Inter'] border border-[#ACC3EF]">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="hidden md:flex flex-1 flex-col items-center justify-center px-4 md:px-20">
            <div class="w-full max-w-[500px]">
                <img src="images/my_password.svg" alt="Create Password Illustration" class="w-full">
            </div>
        </div>
    </div>
</body>
</html>
