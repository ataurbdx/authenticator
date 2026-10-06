<!-- Verify OTP Form Component (Bootstrap 5) -->
<form class="js-auth-form js-verify-otp-form" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/otp/verify') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf
    <input type="hidden" name="contact" class="js-otp-contact" value="{{ request('contact', '') }}">
    <input type="hidden" name="code" class="js-otp-full-code">

    <div class="mb-4 text-center">
        <label class="form-label small fw-bold text-uppercase text-muted mb-3" style="font-size: 11px;">
            Enter 6-Digit Verification Code
        </label>
        <div class="d-flex justify-content-center gap-2 js-otp-inputs-wrap">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code"
                    class="form-control text-center fs-4 fw-bold js-otp-digit" style="width: 46px; height: 52px;">
            @endfor
        </div>
        <div class="invalid-feedback error-message d-none mt-2"><span class="error-text"></span></div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold js-submit-btn mb-3">
        <span>Verify & Continue</span>
        <i class="bi bi-arrow-right ms-1"></i>
    </button>

    <div class="text-center">
        <div class="js-timer-wrap small text-muted">
            Resend code in <span class="js-countdown fw-bold text-primary">60</span>s
        </div>
        <button type="button" class="btn btn-link p-0 small text-primary text-decoration-none js-resend-otp-btn d-none">
            Resend Verification Code
        </button>
    </div>
</form>
