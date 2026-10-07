@extends('authenticator.layout')

@section('title', 'Reset Password — Authenticator')

@section('content')
<div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-black/50">
    <div class="mb-6 text-center sm:text-left">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Set New Password</h1>
        <p class="text-sm text-slate-400 mt-1.5">Must be at least 8 characters long.</p>
    </div>

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

    <form method="POST" action="{{ route('authenticator.reset-password.submit') ?? '#' }}" class="space-y-4 auth-standard-form">
        @csrf

        <input type="hidden" name="token" value="{{ $token ?? '' }}">
        <input type="hidden" name="identifier" value="{{ request('identifier') ?? old('identifier') }}">

        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                New Password
            </label>
            <div class="relative auth-password-group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i class="fa-solid fa-lock text-sm"></i>
                </div>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••" 
                    class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-10 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
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
                    name="password_confirmation" 
                    required 
                    placeholder="••••••••" 
                    class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-10 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
                >
                <button 
                    type="button" 
                    class="auth-password-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300 transition-colors bg-transparent border-none cursor-pointer"
                >
                    <i class="fa-regular fa-eye text-xs"></i>
                </button>
            </div>
        </div>

        <button 
            type="submit" 
            class="w-full mt-2 py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-700/20 hover:shadow-emerald-700/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer"
        >
            <span class="btn-text">Update Password</span>
            <i class="btn-icon fa-solid fa-check text-xs"></i>
        </button>
    </form>

    <div class="mt-8 text-center pt-4 border-t border-slate-800/80">
        <a href="{{ route('authenticator.sign-in') }}" class="text-sm font-semibold text-slate-400 hover:text-white transition-colors flex items-center justify-center gap-2">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Back to Sign In</span>
        </a>
    </div>
</div>
@endsection
