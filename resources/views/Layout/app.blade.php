<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Asset Monitoring')</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Font (Optional - Gunakan font yang modern) -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        main {
            min-height: calc(100vh - 4rem);
            padding-top: 2rem;
            background-color: #f8fafc;
        }
        .page-enter-active,
        .page-leave-active {
            transition: opacity 0.5s ease;
        }

        .page-enter-from,
        .page-leave-to {
            opacity: 0;
        }
    </style>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-[#ECECEC]">
    <div class="flex min-h-screen relative">
        <!-- Mobile Menu Button - Add this -->
        <button id="mobile-menu-button" class="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-lg bg-[#213268] text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Sidebar - Update this -->
        <div id="sidebar" class="transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:relative fixed inset-y-0 left-0 z-40">
            @include('Layout.sidebar')
        </div>

        <!-- Main Content -->
        <div class="flex-1 w-full flex flex-col">
            <!-- Navbar -->
            @include('Layout.navbar')

            <!-- Content -->
            <main class="flex-1 p-6 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');

        mobileMenuButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>
</body>
</html>
