<!-- Two-Factor Authentication Form Component (Tailwind) -->
<form class="js-auth-form js-two-factor-form space-y-4" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/2fa/verify-challenge') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf
    <input type="hidden" name="user_id" value="{{ session('auth_2fa_user_id', request('user_id')) }}">

    <div class="form-field-wrap text-center">
        <label class="js-2fa-label block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
            6-Digit Security Code
        </label>
        <input type="text" name="code" placeholder="000 000" maxlength="10" inputmode="numeric" required autofocus
            class="js-input-2fa-code w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 text-center text-xl font-black tracking-widest text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all">
        <div class="error-message hidden mt-1"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
    </div>

    <div class="pt-1">
        <button type="submit" 
            class="js-submit-btn w-full py-2.5 px-4 bg-theme-primary hover:bg-theme-primary-hover text-white text-xs font-extrabold uppercase tracking-wider rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 border-none cursor-pointer">
            <span>Verify Identity</span>
            <i class="fa-solid fa-shield-check text-xs"></i>
        </button>
    </div>

    <div class="text-center pt-1">
        <button type="button" class="js-toggle-recovery-btn text-xs font-bold text-theme-primary hover:underline bg-transparent border-none cursor-pointer" data-is-recovery="0">
            Use emergency recovery code
        </button>
    </div>
</form>
