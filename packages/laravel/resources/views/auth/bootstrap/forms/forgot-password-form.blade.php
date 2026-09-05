<!-- Forgot Password Form Component (Bootstrap 5) -->
<form class="js-auth-form js-forgot-password-form" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/otp/send') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf
    <input type="hidden" name="action" value="reset_password">

    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">
            <i class="bi bi-envelope text-primary me-1"></i> Email Address or Phone
        </label>
        <input type="text" name="contact" class="form-control js-input-contact" placeholder="Enter your registered email or phone" autocomplete="username" required>
        <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold js-submit-btn">
        <span>Send Recovery Code</span>
        <i class="bi bi-arrow-right ms-1"></i>
    </button>
</form>
