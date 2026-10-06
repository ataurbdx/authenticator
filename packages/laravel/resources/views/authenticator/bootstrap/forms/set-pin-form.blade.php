<!-- Set PIN Form Component (Bootstrap 5) -->
<form class="js-auth-form js-set-pin-form" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/pin/set') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf

    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">
            <i class="bi bi-key text-primary me-1"></i> 4 to 6 Digit Security PIN
        </label>
        <input type="password" name="pin" class="form-control text-center fs-3 fw-bold tracking-widest js-input-pin" 
               maxlength="6" inputmode="numeric" placeholder="••••" required>
        <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-dark">Confirm Security PIN</label>
        <input type="password" name="pin_confirmation" class="form-control text-center fs-3 fw-bold tracking-widest" 
               maxlength="6" inputmode="numeric" placeholder="••••" required>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold js-submit-btn">
        <span>Save Security PIN</span>
        <i class="bi bi-lock ms-1"></i>
    </button>
</form>
