<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <!-- Styles -->
        <style>
            /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */
            html{line-height:1.15;-webkit-text-size-adjust:100%}
            body{margin:0}
            body{font-family:'Nunito',sans-serif}
            .antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
            .relative{position:relative}
            .flex{display:flex}
            .items-center{align-items:center}
            .justify-center{justify-content:center}
            .min-h-screen{min-height:100vh}
        </style>
    </head>
    <body class="antialiased">
        <div class="relative flex items-center justify-center min-h-screen">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-center">
                    <h1 class="text-4xl font-bold">Welcome to Laravel</h1>
                </div>
            </div>
        </div>
    </body>
</html>
