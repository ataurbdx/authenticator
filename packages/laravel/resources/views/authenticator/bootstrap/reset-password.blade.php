@extends('authenticator::layout')

@section('title', 'Reset Password — ' . config('app.name', 'Authenticator'))

@section('content')
<div class="auth-card p-4 p-sm-5 js-auth-container" data-auth-source="page">
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark mb-1">Reset Password</h4>
        <p class="text-muted small">Enter your verification code and set a new password</p>
    </div>

    @include('authenticator::partials.alerts')

    @include('authenticator::forms.reset-password-form')

    <div class="text-center mt-3 pt-3 border-top">
        <a href="{{ route('authenticator.web.sign-in') }}" class="small text-muted text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Return to Sign In
        </a>
    </div>
</div>
@endsection
