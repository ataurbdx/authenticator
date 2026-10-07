@extends('authenticator.layout')

@section('title', 'Verification — Authenticator')

@section('content')
<div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-black/50">
    <div class="mb-6 text-center sm:text-left">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Verification Code</h1>
        <p class="text-sm text-slate-400 mt-1.5">We've sent a code to your {{ session('verify_type', 'email/phone') }}. Please enter it below.</p>
    </div>

    @include('authenticator.partials.alerts')

    <!-- Modular OTP Form -->
    @include('authenticator.forms.verify-otp-form')

    <div class="mt-8 pt-4 border-t border-slate-800/80 text-center">
        <a href="{{ route('authenticator.sign-in') }}" class="text-sm font-semibold text-slate-400 hover:text-white transition-colors flex items-center justify-center gap-2">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Back to Sign In</span>
        </a>
    </div>
</div>
@endsection
