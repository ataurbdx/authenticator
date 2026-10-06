<!-- Sign Up Form Component (Bootstrap 5) -->
<form class="js-auth-form js-sign-up-form" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/sign-up') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf

    <!-- Verified Identifier Banner (Shown if arriving from Step 1) -->
    <div class="js-preview-banner d-none p-3 rounded-3 bg-success-subtle border border-success-subtle mb-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i class="bi bi-check-lg small"></i>
            </div>
            <div>
                <div class="fw-bold small text-success-emphasis text-uppercase" style="font-size: 10px;">Verified ID</div>
                <div class="js-identifier-preview small text-dark fw-semibold"></div>
            </div>
        </div>
        <button type="button" class="btn btn-link btn-sm text-success p-0 text-decoration-none fw-bold js-back-to-account-btn">
            Change
        </button>
    </div>

    <!-- Name Row -->
    <div class="row g-2 mb-2">
        <div class="col-6">
            <label class="form-label small fw-semibold text-dark mb-1">First Name</label>
            <input type="text" name="first_name" class="form-control form-control-sm" placeholder="John" autocomplete="given-name" required>
            <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
        </div>
        <div class="col-6">
            <label class="form-label small fw-semibold text-dark mb-1">Last Name</label>
            <input type="text" name="last_name" class="form-control form-control-sm" placeholder="Doe" autocomplete="family-name">
            <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
        </div>
    </div>

    <!-- Username & Email Row -->
    <div class="row g-2 mb-2">
        <div class="col-6">
            <label class="form-label small fw-semibold text-dark mb-1">Username</label>
            <input type="text" name="username" class="form-control form-control-sm js-input-username" placeholder="johndoe" autocomplete="username">
            <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
        </div>
        <div class="col-6">
            <label class="form-label small fw-semibold text-dark mb-1 d-flex justify-content-between">
                <span>Email</span>
                <span class="js-email-lock d-none text-success"><i class="bi bi-lock-fill"></i></span>
            </label>
            <input type="email" name="email" class="form-control form-control-sm js-input-email" placeholder="john@example.com" autocomplete="email" required>
            <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
        </div>
    </div>

    <!-- Phone Field -->
    <div class="mb-2">
        <label class="form-label small fw-semibold text-dark mb-1 d-flex justify-content-between">
            <span>Phone Number (Optional)</span>
            <span class="js-phone-lock d-none text-success"><i class="bi bi-lock-fill"></i></span>
        </label>
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-light text-dark fw-bold small">+880</span>
            <input type="tel" name="phone" class="form-control js-input-phone" placeholder="1XXXXXXXXX" autocomplete="tel">
        </div>
        <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
    </div>

    <!-- Password Row -->
    <div class="row g-2 mb-2">
        <div class="col-6">
            <label class="form-label small fw-semibold text-dark mb-1">Password</label>
            <div class="input-group input-group-sm">
                <input type="password" name="password" class="form-control js-input-password" placeholder="••••••••" autocomplete="new-password" required>
                <button type="button" class="btn btn-outline-secondary js-password-toggle-btn border-start-0">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
        </div>
        <div class="col-6">
            <label class="form-label small fw-semibold text-dark mb-1">Confirm</label>
            <input type="password" name="password_confirmation" class="form-control form-control-sm" placeholder="••••••••" autocomplete="new-password" required>
        </div>
    </div>

    <!-- Terms Checkbox -->
    <div class="mb-3 form-check">
        <input type="checkbox" name="terms" value="1" class="form-check-input" id="termsAgree" required>
        <label class="form-check-label small text-muted" for="termsAgree" style="font-size: 11px;">
            I agree to the <a href="#" class="text-primary text-decoration-none">Terms & Privacy Policy</a>
        </label>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold js-submit-btn">
        <span>Create Account</span>
        <i class="bi bi-arrow-right ms-1"></i>
    </button>
</form>
