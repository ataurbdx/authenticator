@extends('authenticator.layout')

@section('title', 'Set Security PIN — Authenticator')

@section('content')
<div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-black/50">
    <div class="mb-6 text-center sm:text-left">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Security PIN</h1>
        <p class="text-sm text-slate-400 mt-1.5">Set a 4 to 6 digit security PIN to protect sensitive account operations.</p>
    </div>

    <form method="POST" action="{{ route('authenticator.pin.set.submit') ?? '#' }}" class="space-y-5 auth-standard-form">
        @csrf
        <div>
            <label for="pin" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">New PIN</label>
            <input type="password" id="pin" name="pin" maxlength="6" required autofocus placeholder="••••"
                   class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 px-4 text-white text-center text-lg tracking-widest placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium">
        </div>
        <div>
            <label for="pin_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Confirm PIN</label>
            <input type="password" id="pin_confirmation" name="pin_confirmation" maxlength="6" required placeholder="••••"
                   class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 px-4 text-white text-center text-lg tracking-widest placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium">
        </div>

        <button type="submit" 
                class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-700/20 hover:shadow-emerald-700/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
            <span class="btn-text">Save PIN</span>
            <i class="btn-icon fa-solid fa-lock text-xs"></i>
        </button>
    </form>
</div>
@endsection
