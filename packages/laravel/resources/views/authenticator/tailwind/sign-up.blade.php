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

    @include('authenticator.partials.alerts')

    <!-- Modular Sign Up Form -->
    @include('authenticator.forms.sign-up-form')

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
