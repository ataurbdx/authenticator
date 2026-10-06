<!-- Reset Password Form Component (Tailwind) -->
<form class="js-auth-form js-reset-password-form space-y-3.5" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/reset-password') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf
    <input type="hidden" name="identifier" class="js-reset-identifier" value="{{ request('identifier', '') }}">

    <!-- 6-digit Code -->
    <div class="form-field-wrap">
        <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
            <i class="fa-solid fa-key text-theme-primary text-xs"></i> 6-Digit Verification Code
        </label>
        <input type="text" name="code" placeholder="123456" maxlength="6" inputmode="numeric" required
            class="js-input-code w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-center text-base tracking-widest font-black text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all">
        <div class="error-message hidden mt-1"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
    </div>

    <!-- New Password -->
    <div class="form-field-wrap">
        <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
            <i class="fa-solid fa-lock text-theme-primary text-xs"></i> New Password
        </label>
        <div class="relative">
            <input type="password" name="password" placeholder="••••••••" autocomplete="new-password" required
                class="js-input-password w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 pr-10 text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all font-medium">
            <button type="button" class="js-password-toggle-btn absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 bg-transparent border-none cursor-pointer">
                <i class="fa-regular fa-eye text-xs"></i>
            </button>
        </div>
        <div class="error-message hidden mt-1"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
    </div>

    <!-- Confirm Password -->
    <div class="form-field-wrap">
        <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Confirm New Password</label>
        <input type="password" name="password_confirmation" placeholder="••••••••" autocomplete="new-password" required
            class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all font-medium">
    </div>

    <div class="pt-1">
        <button type="submit" 
            class="js-submit-btn w-full py-2.5 px-4 bg-theme-primary hover:bg-theme-primary-hover text-white text-xs font-extrabold uppercase tracking-wider rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 border-none cursor-pointer">
            <span>Update Password</span>
            <i class="fa-solid fa-check text-xs"></i>
        </button>
    </div>
</form>
