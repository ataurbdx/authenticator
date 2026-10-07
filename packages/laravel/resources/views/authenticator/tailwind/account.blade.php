@extends('authenticator.layout')

@section('title', 'Account — Authenticator')

@section('content')
    <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-black/50">
        <!-- Reusable Tab Navigation -->
        @include('authenticator.nav-tabs', ['activeTab' => 'account'])

        <div class="space-y-6">
            <!-- Header -->
            <div class="text-center sm:text-left">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Find Your Account</h1>
                <p class="text-sm text-slate-400 mt-1.5">Choose how you'd like to get started with your account</p>
            </div>

            @include('authenticator.partials.alerts')

            <!-- Modular Account Gateway Form -->
            @include('authenticator.forms.account-form')

            <!-- Divider: Social Providers -->
            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-slate-800"></div>
                <span class="flex-shrink mx-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Or continue with
                </span>
                <div class="flex-grow border-t border-slate-800"></div>
            </div>

            <!-- Social Buttons -->
            <div class="social-login-section">
                @include('authenticator.social-btn')
            </div>
        </div>
    </div>
@endsection
