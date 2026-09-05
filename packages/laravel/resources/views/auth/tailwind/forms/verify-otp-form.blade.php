<!-- Verify OTP Form Component (Tailwind) -->
<form class="js-auth-form js-verify-otp-form space-y-4" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/otp/verify') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf
    <input type="hidden" name="contact" class="js-otp-contact" value="{{ request('contact', '') }}">
    <input type="hidden" name="code" class="js-otp-full-code">

    <div>
        <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2 text-center">
            Enter 6-Digit Verification Code
        </label>
        <div class="flex justify-center gap-2 js-otp-inputs-wrap">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code"
                    class="js-otp-digit w-11 h-12 text-center text-lg font-black bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all shadow-sm">
            @endfor
        </div>
        <div class="error-message hidden mt-2 text-center"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
    </div>

    <div class="pt-1">
        <button type="submit" 
            class="js-submit-btn w-full py-2.5 px-4 bg-theme-primary hover:bg-theme-primary-hover text-white text-xs font-extrabold uppercase tracking-wider rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 border-none cursor-pointer">
            <span>Verify & Continue</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </button>
    </div>

    <!-- Timer / Resend Section -->
    <div class="text-center pt-2">
        <p class="js-timer-wrap text-xs text-slate-500 dark:text-slate-400">
            Resend code in <span class="js-countdown font-bold text-theme-primary">60</span>s
        </p>
        <button type="button" class="js-resend-otp-btn hidden text-xs font-bold text-theme-primary hover:underline bg-transparent border-none cursor-pointer">
            Resend Verification Code
        </button>
    </div>
</form>
