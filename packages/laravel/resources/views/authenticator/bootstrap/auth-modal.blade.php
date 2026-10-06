<!-- Auth Modal (Bootstrap 5) -->
<div class="modal fade js-auth-modal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg js-auth-container p-3 p-sm-4"
             data-auth-source="modal"
             data-initial-tab="{{ $initialTab ?? 'login' }}">
            
            <!-- Modal Header (Tabs + Close Button) -->
            <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                <ul class="nav nav-tabs border-bottom-0 flex-grow-1 js-modal-tabs-nav" role="tablist">
                    <li class="nav-item flex-fill text-center">
                        <button type="button" data-tab="account" 
                            class="nav-link w-100 py-2 small fw-bold text-uppercase js-tab-btn border-0 {{ ($initialTab ?? 'login') === 'account' ? 'active text-primary border-bottom border-primary border-2' : 'text-muted' }}">
                            Account
                        </button>
                    </li>
                    <li class="nav-item flex-fill text-center">
                        <button type="button" data-tab="login" 
                            class="nav-link w-100 py-2 small fw-bold text-uppercase js-tab-btn border-0 {{ ($initialTab ?? 'login') === 'login' ? 'active text-primary border-bottom border-primary border-2' : 'text-muted' }}">
                            Sign In
                        </button>
                    </li>
                    <li class="nav-item flex-fill text-center">
                        <button type="button" data-tab="register" 
                            class="nav-link w-100 py-2 small fw-bold text-uppercase js-tab-btn border-0 {{ ($initialTab ?? 'login') === 'register' ? 'active text-primary border-bottom border-primary border-2' : 'text-muted' }}">
                            Sign Up
                        </button>
                    </li>
                </ul>
                <button type="button" class="btn-close js-close-auth-modal ms-2" aria-label="Close"></button>
            </div>

            <!-- Alerts Container -->
            @include('authenticator::partials.alerts')

            <!-- Panels Container -->
            <div class="js-modal-panels-wrap">
                <!-- 1. Account Panel (Step 1) -->
                <div class="js-panel js-panel-account {{ ($initialTab ?? 'login') === 'account' ? '' : 'd-none' }}" data-panel="account">
                    @include('authenticator::forms.account-form')
                </div>

                <!-- 2. Sign In Panel -->
                <div class="js-panel js-panel-login {{ ($initialTab ?? 'login') === 'login' ? '' : 'd-none' }}" data-panel="login">
                    @include('authenticator::forms.sign-in-form')
                </div>

                <!-- 3. Sign Up Panel -->
                <div class="js-panel js-panel-register {{ ($initialTab ?? 'login') === 'register' ? '' : 'd-none' }}" data-panel="register">
                    @include('authenticator::forms.sign-up-form')
                </div>
            </div>

            <!-- Social Footer -->
            <div class="mt-3">
                @include('authenticator::partials.social-buttons')
            </div>

        </div>
    </div>
</div>
