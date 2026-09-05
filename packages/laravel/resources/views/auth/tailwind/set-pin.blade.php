@extends('authenticator::layout')

@section('title', 'Set Security PIN — ' . config('app.name', 'Authenticator'))

@section('content')
<div class="w-full max-w-[440px] bg-white dark:bg-[#0b132b] rounded-[28px] overflow-hidden border border-slate-200/80 dark:border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.12)] p-6 sm:p-8 js-auth-container"
     data-auth-source="page">
    
    <div class="text-center mb-6">
        <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-theme-primary/10 border border-theme-primary/20 flex items-center justify-center text-theme-primary text-2xl">
            <i class="fa-solid fa-lock"></i>
        </div>
        <h4 class="text-xl font-black text-slate-900 dark:text-white">Security PIN</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Set a numeric PIN to lock and protect your session</p>
    </div>

    @include('authenticator::partials.alerts')

    @include('authenticator::forms.set-pin-form')
</div>
@endsection
