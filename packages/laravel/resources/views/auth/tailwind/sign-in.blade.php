@extends('authenticator::layout')

@section('title', 'Sign In — ' . config('app.name', 'Authenticator'))

@section('content')
<!-- Auth Page Card (Tailwind) -->
<div class="relative w-full max-w-4xl lg:max-w-[900px] bg-white dark:bg-[#0b132b] rounded-[32px] overflow-hidden border border-slate-200/80 dark:border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.12)] grid grid-cols-1 lg:grid-cols-2 max-h-[90vh] h-[620px] js-auth-container"
     data-auth-source="page">
    
    <!-- Left Banner Side -->
    <div class="hidden lg:flex relative overflow-hidden items-center justify-center bg-[#070624] w-full h-full min-h-0 p-8 flex-col text-center">
        <div class="relative z-10 space-y-4">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-theme-primary/20 border border-theme-primary/40 flex items-center justify-center text-theme-primary text-3xl">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h3 class="text-2xl font-black text-white tracking-tight">{{ config('app.name', 'Authenticator') }}</h3>
            <p class="text-xs text-slate-300 max-w-xs mx-auto leading-relaxed">
                Universal, highly secure authentication platform supporting Multi-Identifier, OTP, 2FA, and Security PIN.
            </p>
        </div>
        <!-- Decorative Glow -->
        <div class="absolute -top-24 -left-24 w-72 h-72 bg-theme-primary/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Right Form Side -->
    <div class="p-6 sm:p-8 flex flex-col justify-between bg-white dark:bg-[#0b132b] relative h-full min-h-0 overflow-y-auto">
        <div>
            <!-- Header -->
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 dark:border-white/10">
                <div>
                    <h4 class="text-lg font-black text-slate-900 dark:text-white">Sign In</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Enter your credentials to access your account</p>
                </div>
                <a href="{{ route('authenticator.web.account') }}" class="text-xs font-bold text-theme-primary hover:underline no-underline">
                    Quick Check <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Alerts -->
            @include('authenticator::partials.alerts')

            <!-- Form -->
            @include('authenticator::forms.sign-in-form')
        </div>

        <!-- Social + Footer -->
        <div class="mt-4">
            @include('authenticator::partials.social-buttons')

            <div class="text-center pt-3 text-xs text-slate-500 dark:text-slate-400">
                Don't have an account? 
                <a href="{{ route('authenticator.web.sign-up') }}" class="font-bold text-theme-primary hover:underline no-underline">
                    Sign Up
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
