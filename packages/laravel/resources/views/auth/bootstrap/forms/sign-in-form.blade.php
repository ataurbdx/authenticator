<!-- Sign In Form Component (Bootstrap 5) -->
<form class="js-auth-form js-sign-in-form" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/sign-in') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf

    <!-- Preview Banner (Shown if arriving from Step 1 Identifier Check) -->
    <div class="js-preview-banner d-none p-3 rounded-3 bg-primary-subtle border border-primary-subtle mb-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i class="bi bi-person-check small"></i>
            </div>
            <div>
                <div class="js-user-name fw-bold small text-dark leading-tight">Welcome Back!</div>
                <div class="js-identifier-preview small text-muted" style="font-size: 11px;"></div>
            </div>
        </div>
        <button type="button" class="btn btn-link btn-sm text-primary p-0 text-decoration-none fw-bold js-back-to-account-btn">
            Change
        </button>
    </div>
    <!-- Hidden identifier field populated when arriving from preview banner -->
    <input type="hidden" name="identifier" class="js-prefilled-identifier">

    <!-- Direct Input Wrapper (Shown when visiting /sign-in directly) -->
    <div class="js-direct-wrap mb-3">
        <!-- Identifier Tabs Toggle (Email / Username / Phone) -->
        <div class="mb-2">
            <label class="form-label small fw-bold text-uppercase text-muted" style="font-size: 11px;">
                <i class="bi bi-list-ul text-primary me-1"></i> Sign In With
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

        <!-- 1. Dedicated Email Input -->
        <div class="js-field-panel js-field-email">
            <label class="form-label small fw-semibold text-dark">
                <i class="bi bi-envelope text-primary me-1"></i> Email Address
            </label>
            <input type="email" name="email" class="form-control js-input-email" placeholder="example@domain.com" autocomplete="email">
            <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
        </div>

        <!-- 2. Dedicated Username Input -->
        <div class="js-field-panel js-field-username d-none">
            <label class="form-label small fw-semibold text-dark">
                <i class="bi bi-person text-primary me-1"></i> Username
            </label>
            <input type="text" name="username" class="form-control js-input-username" placeholder="your_username" autocomplete="username">
            <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
        </div>

        <!-- 3. Dedicated Phone Input -->
        <div class="js-field-panel js-field-phone d-none">
            <label class="form-label small fw-semibold text-dark">
                <i class="bi bi-telephone text-primary me-1"></i> Mobile Number
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light text-dark fw-bold small">+880</span>
                <input type="tel" name="phone" class="form-control js-input-phone" placeholder="1XXXXXXXXX" autocomplete="tel">
            </div>
            <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
        </div>
    </div>

    <!-- Password Field -->
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label small fw-semibold text-dark mb-0">
                <i class="bi bi-lock text-primary me-1"></i> Password
            </label>
            <a href="{{ route('authenticator.web.reset-password') }}" class="small text-primary text-decoration-none" style="font-size: 11px;">
                Forgot password?
            </a>
        </div>
        <div class="input-group">
            <input type="password" name="password" class="form-control js-input-password" placeholder="Enter your password" autocomplete="current-password" required>
            <button type="button" class="btn btn-outline-secondary js-password-toggle-btn border-start-0" type="button">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        <div class="invalid-feedback error-message d-none"><span class="error-text"></span></div>
    </div>

    <!-- Remember Me -->
    <div class="mb-3 form-check">
        <input type="checkbox" name="remember" value="1" class="form-check-input" id="rememberDevice">
        <label class="form-check-label small text-muted" for="rememberDevice">Remember this device</label>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold js-submit-btn">
        <span>Sign In</span>
        <i class="bi bi-arrow-right ms-1"></i>
    </button>
</form>
