<!-- Step 1: Identifier Detection Form (Tailwind) -->
<form class="js-auth-form js-account-form space-y-4" 
      data-api-endpoint="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/check-identifier') }}" 
      method="POST" action="javascript:void(0);" novalidate>
    @csrf

    <!-- Identifier Type Selection Tabs (Email / Username / Phone) -->
    <div>
        <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2 flex items-center gap-1.5">
            <i class="fa-solid fa-list-ul text-theme-primary text-xs"></i> Select Sign-In Method
        </label>
        <div class="grid grid-cols-3 gap-1 bg-slate-100 dark:bg-slate-800/80 p-1 rounded-xl border border-slate-200/80 dark:border-white/5 js-type-toggle-group">
            <button type="button" data-target="email" class="js-identifier-tab-btn py-1.5 px-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer border-none bg-white dark:bg-slate-700 text-theme-primary shadow-sm is-active">
                <i class="fa-regular fa-envelope text-[11px]"></i> Email
            </button>
            <button type="button" data-target="username" class="js-identifier-tab-btn py-1.5 px-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer border-none bg-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                <i class="fa-regular fa-user text-[11px]"></i> Username
            </button>
            <button type="button" data-target="phone" class="js-identifier-tab-btn py-1.5 px-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer border-none bg-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                <i class="fa-solid fa-phone text-[11px]"></i> Phone
            </button>
        </div>
        <input type="hidden" class="js-active-identifier-type" name="type" value="email">
    </div>

    <!-- 1. Dedicated Email Field -->
    <div class="form-field-wrap js-field-panel js-field-email">
        <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
            <i class="fa-regular fa-envelope text-theme-primary text-xs"></i> Email Address
        </label>
        <input type="email" name="email" placeholder="example@domain.com" autocomplete="email"
            class="js-input-email w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all font-medium">
        <div class="error-message hidden mt-1"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
    </div>

    <!-- 2. Dedicated Username Field -->
    <div class="form-field-wrap js-field-panel js-field-username hidden">
        <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
            <i class="fa-regular fa-user text-theme-primary text-xs"></i> Username
        </label>
        <input type="text" name="username" placeholder="your_username" autocomplete="username"
            class="js-input-username w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all font-medium">
        <div class="error-message hidden mt-1"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
    </div>

    <!-- 3. Dedicated Phone Field -->
    <div class="form-field-wrap js-field-panel js-field-phone hidden">
        <label class="block text-[10px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
            <i class="fa-solid fa-phone text-theme-primary text-xs"></i> Mobile Number
        </label>
        <div class="flex gap-2">
            <div class="flex items-center px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 select-none">
                +880
            </div>
            <input type="tel" name="phone" placeholder="1XXXXXXXXX" autocomplete="tel"
                class="js-input-phone flex-1 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 dark:text-slate-100 outline-none focus:border-theme-primary focus:bg-white dark:focus:bg-slate-800 transition-all font-medium">
        </div>
        <div class="error-message hidden mt-1"><span class="error-text text-red-500 text-[11px] font-medium"></span></div>
    </div>

    <!-- Submit Button -->
    <div class="pt-1">
        <button type="submit" 
            class="js-submit-btn w-full py-2.5 px-4 bg-theme-primary hover:bg-theme-primary-hover text-white text-xs font-extrabold uppercase tracking-wider rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 border-none cursor-pointer">
            <span>Continue</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </button>
    </div>
</form>
