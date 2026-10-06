<!-- Step 1: Identifier Detection Form (Bootstrap 5) -->
<form class="js-auth-form js-account-form" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/check-identifier') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf

    <!-- Identifier Type Selection Tabs (Email / Username / Phone) -->
    <div class="mb-3">
        <label class="form-label small fw-bold text-uppercase text-muted" style="font-size: 11px;">
            <i class="bi bi-list-ul text-primary me-1"></i> Select Sign-In Method
        </label>
        <div class="btn-group w-100 p-1 bg-light rounded-pill border js-type-toggle-group" role="group">
            <button type="button" data-target="email" class="btn btn-sm rounded-pill fw-bold js-identifier-tab-btn btn-white bg-white text-primary shadow-sm is-active">
                <i class="bi bi-envelope me-1"></i> Email
            </button>
            <button type="button" data-target="username" class="btn btn-sm rounded-pill fw-bold js-identifier-tab-btn text-muted">
                <i class="bi bi-person me-1"></i> Username
            </button>
            <button type="button" data-target="phone" class="btn btn-sm rounded-pill fw-bold js-identifier-tab-btn text-muted">
                <i class="bi bi-telephone me-1"></i> Phone
            </button>
        </div>
        <input type="hidden" class="js-active-identifier-type" name="type" value="email">
    </div>

    <!-- 1. Dedicated Email Field -->
    <div class="mb-3 js-field-panel js-field-email">
        <label class="form-label small fw-semibold text-dark">
            <i class="bi bi-envelope text-primary me-1"></i> Email Address
        </label>
        <input type="email" name="email" class="form-control js-input-email" placeholder="example@domain.com" autocomplete="email">
        <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
    </div>

    <!-- 2. Dedicated Username Field -->
    <div class="mb-3 js-field-panel js-field-username d-none">
        <label class="form-label small fw-semibold text-dark">
            <i class="bi bi-person text-primary me-1"></i> Username
        </label>
        <input type="text" name="username" class="form-control js-input-username" placeholder="your_username" autocomplete="username">
        <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
    </div>

    <!-- 3. Dedicated Phone Field -->
    <div class="mb-3 js-field-panel js-field-phone d-none">
        <label class="form-label small fw-semibold text-dark">
            <i class="bi bi-telephone text-primary me-1"></i> Mobile Number
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light text-dark fw-bold small">+880</span>
            <input type="tel" name="phone" class="form-control js-input-phone" placeholder="1XXXXXXXXX" autocomplete="tel">
        </div>
        <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold js-submit-btn">
        <span>Continue</span>
        <i class="bi bi-arrow-right ms-1"></i>
    </button>
</form>
