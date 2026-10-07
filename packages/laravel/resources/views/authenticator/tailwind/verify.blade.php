@extends('authenticator.layout')

@section('title', 'Verification — Authenticator')

@section('content')
<div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-black/50">
    <div class="mb-6 text-center sm:text-left">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Verification Code</h1>
        <p class="text-sm text-slate-400 mt-1.5">We've sent a code to your {{ session('verify_type', 'email/phone') }}. Please enter it below.</p>
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
                <span>Please check your code</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('authenticator.otp.confirm') ?? '#' }}" class="space-y-6 auth-standard-form">
        @csrf

        <div class="auth-otp-container">
            <div class="flex justify-center gap-2 sm:gap-3">
                @for ($i = 0; $i < 6; $i++)
                    <input 
                        type="text" 
                        name="code[]"
                        maxlength="1" 
                        class="w-12 h-12 sm:w-14 sm:h-14 text-center text-xl font-bold bg-slate-950/60 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all otp-input"
                        autocomplete="off"
                    >
                @endfor
            </div>
            <input type="hidden" name="verification_code" class="otp-hidden-input">
        </div>

        <button 
            type="submit" 
            id="verify-btn"
            class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-700/20 hover:shadow-emerald-700/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer"
        >
            <span class="btn-text">Verify</span>
            <i class="btn-icon fa-solid fa-shield-check text-xs"></i>
        </button>
    </form>

    <div class="mt-8 text-center space-y-4">
        <div>
            <p class="text-sm text-slate-400 mb-2">Didn't receive the code?</p>
            <form method="POST" action="{{ route('authenticator.otp.send') ?? '#' }}">
                @csrf
                <button type="submit" class="font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                    Resend Code
                </button>
            </form>
        </div>

        <div class="pt-4 border-t border-slate-800/80">
            <a href="{{ route('authenticator.sign-in') }}" class="text-sm font-semibold text-slate-400 hover:text-white transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Back to Sign In</span>
            </a>
        </div>
    </div>
</div>
@endsection
