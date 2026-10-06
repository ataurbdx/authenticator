@extends('authenticator::layout')

@section('title', 'Account — ' . config('app.name', 'Authenticator'))

@section('content')
<div class="auth-card p-4 p-sm-5 js-auth-container" data-auth-source="page">
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark mb-1">Account Access</h4>
        <p class="text-muted small">Enter your email, username, or phone to begin</p>
    </div>

    @include('authenticator::partials.alerts')

    @include('authenticator::forms.account-form')

    @include('authenticator::partials.social-buttons')

    <div class="text-center mt-3 pt-3 border-top">
        <a href="{{ route('authenticator.web.sign-in') }}" class="small fw-bold text-primary text-decoration-none me-2">
            Direct Sign In
        </a>
        <span class="text-muted small">•</span>
        <a href="{{ route('authenticator.web.sign-up') }}" class="small fw-bold text-primary text-decoration-none ms-2">
            Create Account
        </a>
    </div>
</div>
@endsection
