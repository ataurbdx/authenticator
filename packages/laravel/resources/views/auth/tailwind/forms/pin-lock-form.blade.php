<!-- PIN Lock Screen Form Component (Tailwind) -->
<form class="js-auth-form js-pin-lock-form space-y-4" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/pin/verify') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf

    <div class="form-field-wrap text-center">
        <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
            Enter Security PIN to Unlock
        </label>
        <input type="password" name="pin" maxlength="6" inputmode="numeric" placeholder="••••" required autofocus
            class="js-input-pin w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 text-center text-2xl tracking-widest font-black text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all">
        <div class="error-message hidden mt-1"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
    </div>

    <div class="pt-1">
        <button type="submit" 
            class="js-submit-btn w-full py-2.5 px-4 bg-theme-primary hover:bg-theme-primary-hover text-white text-xs font-extrabold uppercase tracking-wider rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 border-none cursor-pointer">
            <span>Unlock Session</span>
            <i class="fa-solid fa-lock-open text-xs"></i>
        </button>
    </div>
</form>
