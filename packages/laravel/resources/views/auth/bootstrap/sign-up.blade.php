@extends('authenticator::layout')

@section('title', 'Sign Up — ' . config('app.name', 'Authenticator'))

@section('content')
<div class="auth-card p-4 p-sm-5 js-auth-container" data-auth-source="page">
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark mb-1">Create Account</h4>
        <p class="text-muted small">Fill in your details to register</p>
    </div>

    @include('authenticator::partials.alerts')

    @include('authenticator::forms.sign-up-form')

    @include('authenticator::partials.social-buttons')

    <div class="text-center mt-3 pt-3 border-top">
        <span class="small text-muted">Already have an account?</span>
        <a href="{{ route('authenticator.web.sign-in') }}" class="small fw-bold text-primary text-decoration-none ms-1">
            Sign In
        </a>
    </div>
</div>
@endsection
