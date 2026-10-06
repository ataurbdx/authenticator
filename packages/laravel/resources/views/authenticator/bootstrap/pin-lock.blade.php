@extends('authenticator::layout')

@section('title', 'Screen Locked — ' . config('app.name', 'Authenticator'))

@section('content')
<div class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex align-items-center justify-content-center p-3" style="z-index: 1060; backdrop-filter: blur(8px);">
    <div class="auth-card p-4 p-sm-5 text-center js-auth-container" data-auth-source="page">
        <div class="mb-3">
            <span class="p-3 bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                <i class="bi bi-lock-fill fs-2"></i>
            </span>
        </div>

        <h4 class="fw-bold text-dark mb-1">Session Locked</h4>
        <p class="text-muted small mb-4">
            {{ auth()->user()->name ?? 'User' }}, enter your PIN to continue
        </p>

        @include('authenticator::partials.alerts')

        @include('authenticator::forms.pin-lock-form')
    </div>
</div>
@endsection
