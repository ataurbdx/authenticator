@extends('authenticator::layout')

@section('title', 'Screen Locked — ' . config('app.name', 'Authenticator'))

@section('content')
<div class="fixed inset-0 z-50 bg-[#070624]/90 backdrop-blur-md flex items-center justify-center p-4">
    <div class="w-full max-w-sm bg-white dark:bg-[#0b132b] rounded-[28px] overflow-hidden border border-slate-200/80 dark:border-white/10 shadow-2xl p-6 sm:p-8 js-auth-container text-center"
         data-auth-source="page">
        
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500 text-3xl">
            <i class="fa-solid fa-lock"></i>
        </div>

        <h4 class="text-xl font-black text-slate-900 dark:text-white">Session Locked</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-5">
            {{ auth()->user()->name ?? 'User' }}, please enter your PIN to continue
        </p>

        @include('authenticator::partials.alerts')

        @include('authenticator::forms.pin-lock-form')
    </div>
</div>
@endsection
