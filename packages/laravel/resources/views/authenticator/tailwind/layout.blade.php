<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Authenticator'))</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind / Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Plus Jakarta Sans', 'Outfit', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                50: '#f0fdf4',
                                100: '#dcfce7',
                                500: '#15803d',
                                600: '#166534',
                                700: '#14532d',
                            }
                        }
                    }
                }
            }
        </script>
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-950 font-sans antialiased text-slate-100 selection:bg-emerald-500 selection:text-white relative overflow-x-hidden">
    <!-- Fixed Background Layers -->
    <div class="fixed inset-0 bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 pointer-events-none -z-10"></div>
    <div class="fixed top-0 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-0 right-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <div class="min-h-screen flex flex-col justify-center items-center py-8 sm:py-12">
        
        <div class="w-full sm:max-w-xl px-4 sm:px-0 mt-auto mb-auto">
            <div class="text-center mb-6">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white text-2xl shadow-lg shadow-emerald-600/30 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-white font-['Outfit']">
                        Authenticator
                    </span>
                </a>
            </div>

            <main class="w-full">
                @yield('content')
            </main>

            <footer class="mt-8 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} Authenticator. All rights reserved.
            </footer>
        </div>

    </div>

    <!-- Authenticator Common Script -->
    <script src="{{ asset('vendor/authenticator/js/authenticator.js') }}?v={{ time() }}"></script>
    @stack('scripts')
</body>
</html>
