@extends('authenticator.layout')

@section('title', 'Sign Up — Authenticator')

@section('content')
<div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-black/50">
    <!-- Common Tabs Navigation -->
    @include('authenticator.nav-tabs', ['activeTab' => 'sign-up'])

    <div class="mb-8 text-center sm:text-left">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Create an Account</h1>
        <p class="text-sm text-slate-400 mt-1.5">Join Authenticator to get started</p>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
            <div class="font-semibold flex items-center gap-2 mb-1">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Please correct the errors below</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('authenticator.sign-up.submit') }}" class="space-y-4 auth-standard-form">
        @csrf

        <!-- Name (First Name & Last Name) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    First Name
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-regular fa-user text-sm"></i>
                    </div>
                    <input 
                        type="text" 
                        id="first_name" 
                        name="first_name" 
                        value="{{ old('first_name') }}" 
                        required 
                        autofocus
                        placeholder="John" 
                        class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-3.5 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                    >
                </div>
            </div>
            <div>
                <label for="last_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Last Name
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-regular fa-user text-sm"></i>
                    </div>
                    <input 
                        type="text" 
                        id="last_name" 
                        name="last_name" 
                        value="{{ old('last_name') }}" 
                        placeholder="Doe" 
                        class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-3.5 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                    >
                </div>
            </div>
        </div>

        <!-- Username -->
        <div>
            <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                Username
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i class="fa-solid fa-at text-sm"></i>
                </div>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="{{ old('username', request('username')) }}" 
                    required 
                    placeholder="johndoe123" 
                    class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-3.5 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                >
            </div>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                Email Address
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i class="fa-regular fa-envelope text-sm"></i>
                </div>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', request('email')) }}" 
                    placeholder="user@example.com" 
                    class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-3.5 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                >
            </div>
        </div>

        <!-- Phone (Optional/Supported) -->
        <div>
            <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                Phone Number
            </label>
            <div class="flex gap-2">
                <div class="relative w-1/3 sm:w-1/4">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-globe text-sm"></i>
                    </div>
                    <input 
                        type="text" 
                        name="code" 
                        value="{{ old('code', request('code', '+880')) }}" 
                        placeholder="+880" 
                        class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-8 pr-2.5 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all text-center"
                    >
                </div>
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-phone text-sm"></i>
                    </div>
                    <input 
                        type="text" 
                        id="number" 
                        name="number" 
                        value="{{ old('number', request('number')) }}" 
                        placeholder="1700000000" 
                        class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-3.5 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                    >
                </div>
            </div>
        </div>

        <!-- Password & Confirmation -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Password
                </label>
                <div class="relative auth-password-group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </div>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="••••••••" 
                        class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-10 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                    >
                    <button 
                        type="button" 
                        class="auth-password-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300 transition-colors bg-transparent border-none cursor-pointer"
                    >
                        <i class="fa-regular fa-eye text-xs"></i>
                    </button>
                </div>
            </div>
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Confirm Password
                </label>
                <div class="relative auth-password-group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-shield text-sm"></i>
                    </div>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required 
                        placeholder="••••••••" 
                        class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-10 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                    >
                    <button 
                        type="button" 
                        class="auth-password-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300 transition-colors bg-transparent border-none cursor-pointer"
                    >
                        <i class="fa-regular fa-eye text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full mt-3 py-3.5 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-700/20 hover:shadow-emerald-700/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer"
        >
            <span class="btn-text">Create Account</span>
            <i class="btn-icon fa-solid fa-user-plus text-xs"></i>
        </button>
    </form>

    <!-- Divider: Social Registration -->
    <div class="relative flex py-5 items-center">
        <div class="flex-grow border-t border-slate-800"></div>
        <span class="flex-shrink mx-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Or sign up with
        </span>
        <div class="flex-grow border-t border-slate-800"></div>
    </div>

    <!-- Social Providers -->
    @include('authenticator.social-btn')

    <!-- Switch to Sign In -->
    <div class="mt-8 pt-6 border-t border-slate-800/80 text-center">
        <p class="text-xs text-slate-400">
            Already have an account? 
            <a href="{{ route('authenticator.sign-in') }}" class="font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                Sign In instead
            </a>
        </p>
    </div>
</div>
@endsection
