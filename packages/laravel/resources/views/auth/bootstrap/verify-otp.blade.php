@extends('authenticator::layout')

@section('title', 'Verify OTP — ' . config('app.name', 'Authenticator'))

@section('content')
<div class="auth-card p-4 p-sm-5 js-auth-container" data-auth-source="page">
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark mb-1">Verification Code</h4>
        <p class="text-muted small">Enter the 6-digit code sent to your contact</p>
    </div>

    @include('authenticator::partials.alerts')

    @include('authenticator::forms.verify-otp-form')

    <div class="text-center mt-3 pt-3 border-top">
        <a href="{{ route('authenticator.web.sign-in') }}" class="small text-muted text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Return to Sign In
        </a>
    </div>
</div>
@endsection
