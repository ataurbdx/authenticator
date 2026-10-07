@extends('authenticator.layout')

@section('title', 'Two-Factor Challenge — Authenticator')

@section('content')
<div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-black/50">
    <div class="mb-6 text-center sm:text-left">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Two-Factor Authentication</h1>
        <p class="text-sm text-slate-400 mt-1.5">Please confirm access to your account by entering the authentication code provided by your authenticator application.</p>
    </div>

    @include('authenticator::partials.alerts')

    @include('authenticator::forms.two-factor-form')
</div>
@endsection
