@extends('authenticator.layout')

@section('title', 'Set Security PIN — Authenticator')

@section('content')
<div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-black/50">
    <div class="mb-6 text-center sm:text-left">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Security PIN</h1>
        <p class="text-sm text-slate-400 mt-1.5">Set a 4 to 6 digit security PIN to protect sensitive account operations.</p>
    </div>

    @include('authenticator::partials.alerts')

    @include('authenticator::forms.set-pin-form')
</div>
@endsection
