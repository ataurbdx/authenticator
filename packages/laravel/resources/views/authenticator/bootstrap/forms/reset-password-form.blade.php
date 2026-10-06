<!-- Reset Password Form Component (Bootstrap 5) -->
<form class="js-auth-form js-reset-password-form" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/reset-password') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf
    <input type="hidden" name="identifier" class="js-reset-identifier" value="{{ request('identifier', '') }}">

    <!-- 6-digit Code -->
    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">
            <i class="bi bi-key text-primary me-1"></i> 6-Digit Verification Code
        </label>
        <input type="text" name="code" class="form-control text-center fs-4 fw-bold tracking-widest js-input-code" placeholder="123456" maxlength="6" inputmode="numeric" required>
        <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
    </div>

    <!-- New Password -->
    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">
            <i class="bi bi-lock text-primary me-1"></i> New Password
        </label>
        <div class="input-group">
            <input type="password" name="password" class="form-control js-input-password" placeholder="••••••••" autocomplete="new-password" required>
            <button type="button" class="btn btn-outline-secondary js-password-toggle-btn border-start-0">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
    </div>

    <!-- Confirm Password -->
    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">Confirm New Password</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" autocomplete="new-password" required>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold js-submit-btn">
        <span>Update Password</span>
        <i class="bi bi-check-lg ms-1"></i>
    </button>
</form>
