<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>403 - Akses Dilarang</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#213268',
                        secondary: '#56C5F1',
                        light: '#ECECEC'
                    }
                }
            }
        }
    </script>
    <style>
        body,
        p,
        span,
        div,
        button,
        input,
        select,
        textarea,
        a {
            font-family: 'Poppins', sans-serif;
            font-size: max(16px, 0.75rem);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        .animate-float-delay {
            animation: float 4s ease-in-out 0.5s infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .blob {
            border-radius: 70% 30% 30% 70% / 60% 40% 60% 40%;
            animation: blobAnimation 8s infinite ease;
            box-shadow: 0 10px 50px -10px rgba(33, 50, 104, 0.3);
        }

        @keyframes blobAnimation {

            0%,
            100% {
                border-radius: 70% 30% 30% 70% / 60% 40% 60% 40%;
            }

            25% {
                border-radius: 30% 70% 70% 30% / 40% 60% 40% 60%;
            }

            50% {
                border-radius: 50% 50% 30% 70% / 30% 30% 70% 70%;
            }

            75% {
                border-radius: 70% 30% 50% 50% / 60% 70% 30% 30%;
            }
        }
    </style>
</head>

<body class="bg-[#ECECEC]">
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="w-full max-w-4xl relative">
            <!-- Background Blobs -->
            <div class="absolute -top-20 -left-20 w-64 h-64 bg-[#FF5F5F]/10 blob -z-10"></div>
            <div class="absolute -bottom-16 -right-16 w-48 h-48 bg-[#213268]/10 blob -z-10 animate-float-delay"></div>

            <!-- Main Content Card -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-2xl">
                <div class="flex flex-col md:flex-row">
                    <!-- Left Side (Illustration) -->
                    <div class="w-full md:w-2/5 bg-[#213268] flex items-center justify-center py-10 px-6">
                        <div class="animate-float">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" class="w-full max-w-xs mx-auto">
                                <path fill="#1a2854"
                                    d="M412.9,155.8c-8.3-23.3-21.5-44.4-38.3-61.2c-16.8-16.8-37.9-30-61.2-38.3C289.9,47.9,264.5,43,238.5,43
                                    s-51.4,4.9-74.9,13.2c-23.3,8.3-44.4,21.5-61.2,38.3c-16.8,16.8-30,37.9-38.3,61.2C55.9,179.3,51,204.7,51,230.7
                                    s4.9,51.4,13.2,74.9c8.3,23.3,21.5,44.4,38.3,61.2c16.8,16.8,37.9,30,61.2,38.3c23.5,8.3,48.9,13.2,74.9,13.2s51.4-4.9,74.9-13.2
                                    c23.3-8.3,44.4-21.5,61.2-38.3c16.8-16.8,30-37.9,38.3-61.2c8.3-23.5,13.2-48.9,13.2-74.9S421.2,179.3,412.9,155.8z" />
                                <path fill="#FFFFFF"
                                    d="M391.1,169.5c-7.1-19.9-18.4-37.9-32.7-52.2c-14.3-14.3-32.3-25.6-52.2-32.7c-20.1-7.1-41.7-11-63.8-11
                                    s-43.7,3.9-63.8,11c-19.9,7.1-37.9,18.4-52.2,32.7c-14.3,14.3-25.6,32.3-32.7,52.2c-7.1,20.1-11,41.7-11,63.8s3.9,43.7,11,63.8
                                    c7.1,19.9,18.4,37.9,32.7,52.2c14.3,14.3,32.3,25.6,52.2,32.7c20.1,7.1,41.7,11,63.8,11s43.7-3.9,63.8-11
                                    c19.9-7.1,37.9-18.4,52.2-32.7c14.3-14.3,25.6-32.3,32.7-52.2c7.1-20.1,11-41.7,11-63.8S398.2,189.6,391.1,169.5z" />
                                <text x="140" y="260" font-family="Poppins" font-size="120" font-weight="bold"
                                    fill="#FF5F5F">403</text>

                                <!-- Lock icon in circle -->
                                <circle cx="250" cy="350" r="40" fill="#FF5F5F" opacity="0.2" />
                                <path transform="translate(230, 330)" fill="#FF5F5F" d="M38 12H22V8c0-4.4-3.6-8-8-8S6 3.6 6 8v4H-10c-1.1 0-2 .9-2 2v28c0 1.1.9 2 2 2h48c1.1 0 2-.9 2-2V14c0-1.1-.9-2-2-2zm-20-4c0-2.2 1.8-4 4-4s4 1.8 4 4v4h-8V8zM38 40H-10V14h48v26z"/>
                                <path transform="translate(230, 330)" fill="#FF5F5F" d="M14 30c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Right Side (Content) -->
                    <div class="w-full md:w-3/5 p-8 md:p-12 flex flex-col justify-center">
                        <div class="mb-2 inline-block">
                            <span class="px-3 py-1 bg-[#FF5F5F]/20 text-[#FF5F5F] rounded-full font-medium">Error
                                403</span>
                        </div>

                        <h1 class="text-4xl font-bold text-[#213268] mb-4">Akses Dilarang</h1>

                        <p class="text-gray-600 mb-8">
                            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Jika Anda yakin seharusnya memiliki akses, silakan hubungi administrator sistem.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="javascript:history.back()"
                                class="px-6 py-3 bg-[#213268] text-white rounded-lg font-medium transition-all duration-300 hover:bg-[#162449] flex items-center justify-center shadow-md hover:shadow-lg transform hover:-translate-y-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-10 text-center text-gray-600">
            <p>© {{ date('Y') }} Asset Monitoring. Hak Cipta Dilindungi.</p>
        </div>
    </div>
</body>

</html>
