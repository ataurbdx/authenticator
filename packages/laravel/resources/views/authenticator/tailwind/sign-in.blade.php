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

    @include('authenticator.partials.alerts')

    <!-- Modular Sign In Form -->
    @include('authenticator.forms.sign-in-form')

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
