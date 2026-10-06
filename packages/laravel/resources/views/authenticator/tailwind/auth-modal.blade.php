<!-- Auth Modal (Tailwind) -->
<div class="js-auth-modal fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden"
     role="dialog" aria-modal="true">
    
    <!-- Modal Dialog Box -->
    <div class="relative w-full max-w-md bg-white dark:bg-[#0b132b] rounded-[28px] overflow-hidden border border-slate-200/80 dark:border-white/10 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.25)] p-5 sm:p-6 js-auth-container flex flex-col justify-between max-h-[90vh] overflow-y-auto"
         data-auth-source="modal"
         data-initial-tab="{{ $initialTab ?? 'login' }}">
        
        <!-- Modal Top Bar (Tabs Navigation + Close Button) -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/10 pb-2.5 mb-3 gap-2">
            <div class="flex items-center flex-1 js-modal-tabs-nav">
                <button type="button" data-tab="account"
                    class="js-tab-btn flex-1 pb-2 text-xs font-black uppercase tracking-wider transition-colors cursor-pointer text-center bg-transparent border-none -mb-[1px] {{ ($initialTab ?? 'login') === 'account' ? 'text-theme-primary border-b-2 border-theme-primary' : 'text-slate-400 border-b-2 border-transparent hover:text-slate-700 dark:hover:text-slate-200' }}">
                    Account
                </button>
                <button type="button" data-tab="login"
                    class="js-tab-btn flex-1 pb-2 text-xs font-black uppercase tracking-wider transition-colors cursor-pointer text-center bg-transparent border-none -mb-[1px] {{ ($initialTab ?? 'login') === 'login' ? 'text-theme-primary border-b-2 border-theme-primary' : 'text-slate-400 border-b-2 border-transparent hover:text-slate-700 dark:hover:text-slate-200' }}">
                    Sign In
                </button>
                <button type="button" data-tab="register"
                    class="js-tab-btn flex-1 pb-2 text-xs font-black uppercase tracking-wider transition-colors cursor-pointer text-center bg-transparent border-none -mb-[1px] {{ ($initialTab ?? 'login') === 'register' ? 'text-theme-primary border-b-2 border-theme-primary' : 'text-slate-400 border-b-2 border-transparent hover:text-slate-700 dark:hover:text-slate-200' }}">
                    Sign Up
                </button>
            </div>

            <!-- Close Button -->
            <button type="button" class="js-close-auth-modal w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center cursor-pointer border-none outline-none shrink-0 transition-colors" aria-label="Close modal">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <!-- Dynamic Alerts Container -->
        @include('authenticator::partials.alerts')

        <!-- Panels Container -->
        <div class="js-modal-panels-wrap">
            <!-- 1. Account Panel (Step 1) -->
            <div class="js-panel js-panel-account {{ ($initialTab ?? 'login') === 'account' ? '' : 'hidden' }}" data-panel="account">
                @include('authenticator::forms.account-form')
            </div>

            <!-- 2. Sign In Panel -->
            <div class="js-panel js-panel-login {{ ($initialTab ?? 'login') === 'login' ? '' : 'hidden' }}" data-panel="login">
                @include('authenticator::forms.sign-in-form')
            </div>

            <!-- 3. Sign Up Panel -->
            <div class="js-panel js-panel-register {{ ($initialTab ?? 'login') === 'register' ? '' : 'hidden' }}" data-panel="register">
                @include('authenticator::forms.sign-up-form')
            </div>
        </div>

        <!-- Social Connect Footer -->
        <div class="mt-3">
            @include('authenticator::partials.social-buttons')
        </div>

    </div>
</div>
