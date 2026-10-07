@extends('authenticator.layout')

@section('title', 'Two-Factor Challenge — Authenticator')

@section('content')
<div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-black/50">
    <div class="mb-6 text-center sm:text-left">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Two-Factor Authentication</h1>
        <p class="text-sm text-slate-400 mt-1.5">Please confirm access to your account by entering the authentication code provided by your authenticator application.</p>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('authenticator.2fa.verify') ?? '#' }}" class="space-y-5 auth-standard-form">
        @csrf
        <div>
            <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                Authentication Code
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i class="fa-solid fa-key text-sm"></i>
                </div>
                <input type="text" id="code" name="code" required autofocus placeholder="000 000"
                       class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-3.5 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium">
            </div>
        </div>

        <button type="submit" 
                class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-700/20 hover:shadow-emerald-700/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
            <span class="btn-text">Confirm Code</span>
            <i class="btn-icon fa-solid fa-arrow-right text-xs"></i>
        </button>
    </form>
</div>
@endsection
