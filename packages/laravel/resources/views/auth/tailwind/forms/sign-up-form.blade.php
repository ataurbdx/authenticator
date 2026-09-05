<!-- Sign Up Form Component (Tailwind) -->
<form class="js-auth-form js-sign-up-form space-y-3" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/sign-up') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf

    <!-- Verified Identifier Banner (Populated if arriving from Step 1) -->
    <div class="js-preview-banner hidden p-2.5 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 font-bold text-xs shrink-0">
                <i class="fa-solid fa-check"></i>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-wider font-bold text-emerald-700 dark:text-emerald-400">Verified ID</p>
                <p class="js-identifier-preview text-xs font-bold text-slate-800 dark:text-slate-100"></p>
            </div>
        </div>
        <button type="button" class="js-back-to-account-btn text-[11px] font-bold text-emerald-600 hover:underline bg-transparent border-none cursor-pointer">
            Change
        </button>
    </div>

    <!-- Name Fields (Grid) -->
    <div class="grid grid-cols-2 gap-2">
        <div class="form-field-wrap">
            <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">First Name</label>
            <input type="text" name="first_name" placeholder="John" autocomplete="given-name" required
                class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all font-medium">
            <div class="error-message hidden mt-1"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
        </div>
        <div class="form-field-wrap">
            <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Last Name</label>
            <input type="text" name="last_name" placeholder="Doe" autocomplete="family-name"
                class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all font-medium">
            <div class="error-message hidden mt-1"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
        </div>
    </div>

    <!-- Username & Email (Grid) -->
    <div class="grid grid-cols-2 gap-2">
        <div class="form-field-wrap">
            <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Username</label>
            <input type="text" name="username" placeholder="johndoe" autocomplete="username"
                class="js-input-username w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all font-medium">
            <div class="error-message hidden mt-1"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
        </div>
        <div class="form-field-wrap">
            <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center justify-between">
                <span>Email</span>
                <span class="js-email-lock hidden text-emerald-500"><i class="fa-solid fa-lock text-[9px]"></i></span>
            </label>
            <input type="email" name="email" placeholder="john@example.com" autocomplete="email" required
                class="js-input-email w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all font-medium">
            <div class="error-message hidden mt-1"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
        </div>
    </div>

    <!-- Phone Field -->
    <div class="form-field-wrap">
        <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center justify-between">
            <span>Phone Number (Optional)</span>
            <span class="js-phone-lock hidden text-emerald-500"><i class="fa-solid fa-lock text-[9px]"></i></span>
        </label>
        <div class="flex gap-2">
            <div class="flex items-center px-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 select-none">
                +880
            </div>
            <input type="tel" name="phone" placeholder="1XXXXXXXXX" autocomplete="tel"
                class="js-input-phone flex-1 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all font-medium">
        </div>
        <div class="error-message hidden mt-1"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
    </div>

    <!-- Password Fields (Grid) -->
    <div class="grid grid-cols-2 gap-2">
        <div class="form-field-wrap">
            <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Password</label>
            <div class="relative">
                <input type="password" name="password" placeholder="••••••••" autocomplete="new-password" required
                    class="js-input-password w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 pr-8 text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all font-medium">
                <button type="button" class="js-password-toggle-btn absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 bg-transparent border-none cursor-pointer">
                    <i class="fa-regular fa-eye text-xs"></i>
                </button>
            </div>
            <div class="error-message hidden mt-1"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
        </div>
        <div class="form-field-wrap">
            <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Confirm</label>
            <input type="password" name="password_confirmation" placeholder="••••••••" autocomplete="new-password" required
                class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all font-medium">
        </div>
    </div>

    <!-- Terms Checkbox -->
    <div class="flex items-center">
        <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" name="terms" value="1" required
                class="rounded border-slate-300 text-theme-primary focus:ring-theme-primary h-3.5 w-3.5">
            <span class="text-[11px] text-slate-600 dark:text-slate-400 font-medium">
                I agree to the <a href="#" class="text-theme-primary hover:underline">Terms & Privacy Policy</a>
            </span>
        </label>
    </div>

    <!-- Submit Button -->
    <div class="pt-1">
        <button type="submit" 
            class="js-submit-btn w-full py-2.5 px-4 bg-theme-primary hover:bg-theme-primary-hover text-white text-xs font-extrabold uppercase tracking-wider rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 border-none cursor-pointer">
            <span>Create Account</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </button>
    </div>
</form>
