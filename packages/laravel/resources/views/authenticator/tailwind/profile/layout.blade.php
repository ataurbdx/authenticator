<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'User Portal — ' . config('app.name', 'Authenticator'))</title>

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
<body class="bg-slate-950 font-sans antialiased text-slate-100 selection:bg-emerald-500 selection:text-white relative min-h-screen flex flex-col overflow-x-hidden">

    <!-- Background Glows -->
    <div class="fixed inset-0 bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 pointer-events-none -z-10"></div>
    <div class="fixed top-0 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-0 right-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <!-- Top Navigation Bar -->
    <header class="bg-slate-900/80 backdrop-blur-xl border-b border-slate-800 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <!-- Brand / Logo -->
            <a href="{{ route('authenticator.dashboard') }}" class="inline-flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white text-base shadow-md shadow-emerald-600/30 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <span class="text-base sm:text-lg font-black tracking-tight text-white font-['Outfit']">
                    Authenticator
                </span>
            </a>

            <!-- Nav Links -->
            <nav class="flex items-center gap-1.5 sm:gap-2">
                <a href="{{ route('authenticator.dashboard') }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('authenticator.dashboard') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-gauge mr-1"></i> Dashboard
                </a>

                <a href="{{ route('authenticator.profile') }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('authenticator.profile*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <i class="fa-regular fa-user mr-1"></i> Profile
                </a>

                <!-- Sign Out -->
                <form action="{{ route('authenticator.sign-out') }}" method="POST" class="inline ml-1 sm:ml-2">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 hover:text-rose-300 text-xs font-bold transition-all cursor-pointer">
                        <i class="fa-solid fa-arrow-right-from-bracket text-[11px]"></i>
                        <span class="hidden sm:inline">Sign Out</span>
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex-1 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-900 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Authenticator. All rights reserved.
    </footer>

    <!-- Core Authenticator JS -->
    <script src="{{ asset('vendor/authenticator/js/authenticator.js') }}"></script>

    @stack('scripts')
</body>
</html>
