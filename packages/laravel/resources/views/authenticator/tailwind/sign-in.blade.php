@extends('authenticator.layout')

@section('title', 'Sign In — Authenticator')

@section('content')
<div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-black/50">
    <!-- Common Tabs Navigation -->
    @include('authenticator.nav-tabs', ['activeTab' => 'sign-in'])

    <div class="mb-6 text-center sm:text-left">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Welcome Back</h1>
        <p class="text-sm text-slate-400 mt-1.5">Sign in to your account to continue</p>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
            <div class="font-semibold flex items-center gap-2 mb-1">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Please check your input</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $initialType = old('type', $type ?? 'email');
        $initialIdentifier = old('identifier', $identifier ?? '');
        $hasPrefilledIdentifier = !empty($initialIdentifier) || (isset($errors) && $errors->any());
    @endphp

    <!-- Method Selection Buttons (Continue with ...) -->
    <div class="auth-form-wrapper">
        <div class="auth-methods-selection space-y-3 {{ $hasPrefilledIdentifier ? 'hidden' : '' }}">
        <button type="button" 
                data-method="email" 
                class="auth-method-btn group w-full py-3 px-4 bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700 rounded-xl text-white text-sm font-medium transition-all duration-200 flex items-center justify-center gap-3 shadow-sm hover:shadow-md hover:scale-[1.01] active:scale-[0.99] cursor-pointer">
            <i class="fa-regular fa-envelope text-emerald-400 text-base group-hover:scale-110 transition-transform"></i>
            <span class="text-slate-200 group-hover:text-white">Continue with Email</span>
        </button>

        <button type="button" 
                data-method="phone" 
                class="auth-method-btn group w-full py-3 px-4 bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700 rounded-xl text-white text-sm font-medium transition-all duration-200 flex items-center justify-center gap-3 shadow-sm hover:shadow-md hover:scale-[1.01] active:scale-[0.99] cursor-pointer">
            <i class="fa-solid fa-phone text-teal-400 text-base group-hover:scale-110 transition-transform"></i>
            <span class="text-slate-200 group-hover:text-white">Continue with Phone</span>
        </button>

        <button type="button" 
                data-method="username" 
                class="auth-method-btn group w-full py-3 px-4 bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700 rounded-xl text-white text-sm font-medium transition-all duration-200 flex items-center justify-center gap-3 shadow-sm hover:shadow-md hover:scale-[1.01] active:scale-[0.99] cursor-pointer">
            <i class="fa-regular fa-user text-sky-400 text-base group-hover:scale-110 transition-transform"></i>
            <span class="text-slate-200 group-hover:text-white">Continue with Username</span>
        </button>
        </div>

        <!-- Sign In Form (Shown when method selected or prefilled) -->
        <div class="auth-form-container {{ $hasPrefilledIdentifier ? '' : 'hidden' }} space-y-4">
        <div class="flex items-center justify-between text-xs mb-1">
            <span class="auth-input-title font-semibold uppercase tracking-wider text-slate-300">
                {{ $initialType === 'phone' ? 'Phone Number' : ($initialType === 'username' ? 'Username' : 'Email Address') }}
            </span>
            <button type="button" class="auth-change-method-btn text-slate-400 hover:text-emerald-400 flex items-center gap-1 transition-colors cursor-pointer bg-transparent border-none">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Change method</span>
            </button>
        </div>

        <form method="POST" action="{{ route('authenticator.sign-in.submit') }}" class="space-y-4 auth-standard-form">
            @csrf
            <input type="hidden" name="type" class="auth-type-input" value="{{ $initialType }}">

            <!-- Identifier Field -->
            <div>
                <div class="flex gap-2">
                    <div class="auth-country-code-wrap {{ $initialType === 'phone' ? '' : 'hidden' }} relative w-1/3 sm:w-1/4 transition-all">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-globe text-sm"></i>
                        </div>
                        <input 
                            type="text" 
                            name="code" 
                            value="{{ old('code', request('code', '+880')) }}" 
                            placeholder="+880" 
                            class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-3 pl-8 pr-2 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
                        >
                    </div>
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <i class="auth-input-icon {{ $initialType === 'phone' ? 'fa-solid fa-phone' : ($initialType === 'username' ? 'fa-regular fa-user' : 'fa-regular fa-envelope') }}"></i>
                        </div>
                        <input 
                            type="{{ $initialType === 'phone' ? 'tel' : ($initialType === 'email' ? 'email' : 'text') }}" 
                            class="auth-identifier-input w-full bg-slate-950/60 border border-slate-800 rounded-xl py-3 pl-11 pr-4 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
                            name="identifier" 
                            value="{{ $initialIdentifier }}" 
                            required 
                            autocomplete="username"
                            placeholder="user@example.com" 
                        >
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="signin-password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Password
                    </label>
                    @if (Route::has('authenticator.forgot-password'))
                        <a href="{{ route('authenticator.forgot-password') }}" class="text-xs text-emerald-400 hover:text-emerald-300 transition-colors">
                            Forgot password?
                        </a>
                    @endif
                </div>
                <div class="relative auth-password-group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <input 
                        type="password" 
                        name="password" 
                        required 
                        {{ $hasPrefilledIdentifier ? 'autofocus' : '' }}
                        placeholder="••••••••" 
                        class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-3 pl-11 pr-12 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
                    >
                    <button 
                        type="button" 
                        class="auth-password-toggle absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-slate-300 transition-colors bg-transparent border-none cursor-pointer"
                    >
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        id="remember" 
                        class="rounded border-slate-700 bg-slate-950/60 text-emerald-600 focus:ring-emerald-500/30"
                    >
                    <span class="text-xs text-slate-400">Remember me on this device</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-emerald-700/20 hover:shadow-emerald-700/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <span class="btn-text">Sign In</span>
                <i class="btn-icon fa-solid fa-arrow-right-to-bracket text-xs"></i>
            </button>
        </form>
        </div>
    </div>

    <!-- Divider: Social Login -->
    <div class="relative flex py-5 items-center">
        <div class="flex-grow border-t border-slate-800"></div>
        <span class="flex-shrink mx-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Or continue with
        </span>
        <div class="flex-grow border-t border-slate-800"></div>
    </div>

    <!-- Social Providers -->
    @include('authenticator.social-btn')

    <!-- Switch to Sign Up -->
    <div class="mt-8 pt-6 border-t border-slate-800/80 text-center">
        <p class="text-xs text-slate-400">
            Don't have an account? 
            <a href="{{ route('authenticator.sign-up') }}" class="font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                Create an account
            </a>
        </p>
    </div>
</div>
@endsection
