@extends('authenticator::layout')

@section('title', 'Set Security PIN — ' . config('app.name', 'Authenticator'))

@section('content')
<div class="auth-card p-4 p-sm-5 js-auth-container" data-auth-source="page">
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark mb-1">Set Security PIN</h4>
        <p class="text-muted small">Set a 4 to 6 digit PIN to protect your session</p>
    </div>

    @include('authenticator::partials.alerts')

    @include('authenticator::forms.set-pin-form')
</div>
@endsection
