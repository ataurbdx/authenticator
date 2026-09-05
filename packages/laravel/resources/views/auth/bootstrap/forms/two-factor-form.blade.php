<!-- Two-Factor Authentication Form Component (Bootstrap 5) -->
<form class="js-auth-form js-two-factor-form" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/2fa/verify-challenge') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf
    <input type="hidden" name="user_id" value="{{ session('auth_2fa_user_id', request('user_id')) }}">

    <div class="mb-4 text-center">
        <label class="js-2fa-label form-label small fw-semibold text-dark mb-2">6-Digit Security Code</label>
        <input type="text" class="form-control text-center fs-3 fw-bold tracking-widest js-input-2fa-code" name="code" 
               maxlength="10" placeholder="000 000" inputmode="numeric" required autofocus>
        <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold js-submit-btn mb-3">
        <span>Verify Identity</span>
        <i class="bi bi-shield-check ms-1"></i>
    </button>

    <div class="text-center">
        <button type="button" class="btn btn-link p-0 small text-primary text-decoration-none js-toggle-recovery-btn" data-is-recovery="0">
            Use emergency recovery code
        </button>
    </div>
</form>
