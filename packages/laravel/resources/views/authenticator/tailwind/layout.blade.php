<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ config('authenticator.ui.theme', 'dark') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Authenticator'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN standalone script for instant zero-build rendering) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        theme: {
                            primary: '#2563eb',
                            'primary-hover': '#1d4ed8',
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .scrollbar-thin::-webkit-scrollbar {
            width: 4px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.4);
            border-radius: 4px;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 dark:bg-[#060814] text-slate-800 dark:text-slate-100 flex flex-col justify-center py-6 sm:py-10 transition-colors duration-300">

    <div class="container mx-auto max-w-5xl px-4">
        <div class="flex items-center justify-center w-full">
            @yield('content')
        </div>
    </div>

    <!-- Authenticator JS API Bridge -->
    <script src="{{ asset('vendor/authenticator/js/authenticator.js') }}"></script>
    @stack('scripts')
</body>
</html>
