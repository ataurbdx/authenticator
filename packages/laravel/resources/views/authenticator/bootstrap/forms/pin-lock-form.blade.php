<!-- PIN Lock Screen Form Component (Bootstrap 5) -->
<form class="js-auth-form js-pin-lock-form" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/pin/verify') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf

    <div class="mb-4">
        <label class="form-label small fw-semibold text-dark mb-2">
            Enter Security PIN to Unlock
        </label>
        <input type="password" name="pin" class="form-control text-center fs-2 fw-bold tracking-widest js-input-pin" 
               maxlength="6" inputmode="numeric" placeholder="••••" required autofocus>
        <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold js-submit-btn">
        <span>Unlock Session</span>
        <i class="bi bi-unlock ms-1"></i>
    </button>
</form>
