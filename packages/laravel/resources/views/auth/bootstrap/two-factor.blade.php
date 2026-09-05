@extends('authenticator::layout')

@section('title', 'Two-Factor Authentication — ' . config('app.name', 'Authenticator'))

@section('content')
<div class="auth-card p-4 p-sm-5 js-auth-container" data-auth-source="page">
    <div class="text-center mb-4">
        <div class="mb-2">
            <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2 rounded-pill">
                <i class="bi bi-shield-lock-fill me-1"></i> {{ config('app.name', 'Authenticator') }}
            </span>
        </div>
        <h4 class="fw-bold text-dark mt-2 mb-1">Two-Factor Authentication</h4>
        <p class="text-muted small">Open your Authenticator app and enter the 6-digit code</p>
    </div>

    @include('authenticator::partials.alerts')

    @include('authenticator::forms.two-factor-form')

    <div class="text-center mt-4 border-top pt-3">
        <a href="{{ route('authenticator.web.sign-in') }}" class="small text-muted text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Return to Sign In
        </a>
    </div>
</div>
@endsection
