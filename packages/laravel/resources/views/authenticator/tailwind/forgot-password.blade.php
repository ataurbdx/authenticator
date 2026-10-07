@extends('authenticator.layout')

@section('title', 'Forgot Password — Authenticator')

@section('content')
<div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-black/50">
    <div class="mb-6 text-center sm:text-left">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Forgot Password?</h1>
        <p class="text-sm text-slate-400 mt-1.5">No worries, we'll send you reset instructions.</p>
    </div>

    @include('authenticator.partials.alerts')

    <!-- Modular Forgot Password Form -->
    @include('authenticator.forms.forgot-password-form')

    <div class="mt-8 text-center">
        <a href="{{ route('authenticator.sign-in') }}" class="text-sm font-semibold text-slate-400 hover:text-white transition-colors flex items-center justify-center gap-2">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Back to Sign In</span>
        </a>
    </div>
</div>
@endsection
