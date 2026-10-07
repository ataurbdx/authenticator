<div class="auth-form-wrapper space-y-6">
    <!-- Feedback Notification -->
    <div class="auth-gateway-feedback hidden p-4 rounded-xl text-sm transition-all flex items-center gap-3">
        <div class="auth-feedback-icon text-base shrink-0"></div>
        <div class="auth-feedback-text text-xs sm:text-sm font-medium"></div>
    </div>

    <!-- Initial Action Buttons (Email / Phone / Username - Find with ...) -->
    <div class="auth-methods-selection space-y-3" data-current-type="email">
        <button type="button" data-method="email"
            class="auth-method-btn group w-full py-3 px-4 bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700 rounded-xl text-white text-sm font-medium transition-all duration-200 flex items-center justify-center gap-3 shadow-sm hover:shadow-md hover:scale-[1.01] active:scale-[0.99] cursor-pointer">
            <i class="fa-regular fa-envelope text-emerald-400 text-base group-hover:scale-110 transition-transform"></i>
            <span class="text-slate-200 group-hover:text-white">Email</span>
        </button>

        <button type="button" data-method="phone"
            class="auth-method-btn group w-full py-3 px-4 bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700 rounded-xl text-white text-sm font-medium transition-all duration-200 flex items-center justify-center gap-3 shadow-sm hover:shadow-md hover:scale-[1.01] active:scale-[0.99] cursor-pointer">
            <i class="fa-solid fa-phone text-teal-400 text-base group-hover:scale-110 transition-transform"></i>
            <span class="text-slate-200 group-hover:text-white">Phone</span>
        </button>

        <button type="button" data-method="username"
            class="auth-method-btn group w-full py-3 px-4 bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700 rounded-xl text-white text-sm font-medium transition-all duration-200 flex items-center justify-center gap-3 shadow-sm hover:shadow-md hover:scale-[1.01] active:scale-[0.99] cursor-pointer">
            <i class="fa-regular fa-user text-sky-400 text-base group-hover:scale-110 transition-transform"></i>
            <span class="text-slate-200 group-hover:text-white">Username</span>
        </button>
    </div>

    <!-- Interactive Input Form (Shown when Email, Phone or Username is selected) -->
    <div class="auth-form-container hidden space-y-4">
        <div class="flex items-center justify-between text-xs mb-1">
            <span class="auth-input-title font-semibold uppercase tracking-wider text-slate-300">
                Email Address
            </span>
            <button type="button"
                class="auth-change-method-btn text-slate-400 hover:text-emerald-400 flex items-center gap-1 transition-colors cursor-pointer bg-transparent border-none">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Change method</span>
            </button>
        </div>

        <form class="auth-gateway-form space-y-4" 
              data-action-url="{{ route('authenticator.check-identifier') }}"
              data-signin-url="{{ route('authenticator.sign-in') }}"
              data-signup-url="{{ route('authenticator.sign-up') }}">
            @csrf
            <div class="flex gap-2">
                <div class="auth-country-code-wrap hidden relative w-1/3 sm:w-1/4 transition-all">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-globe text-sm"></i>
                    </div>
                    <input type="text" name="code" value="+880" placeholder="+880"
                        class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-3 pl-8 pr-2.5 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all text-center font-medium">
                </div>
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                        <i class="auth-input-icon fa-regular fa-envelope"></i>
                    </div>
                    <input type="text" class="auth-identifier-input w-full bg-slate-950/60 border border-slate-800 rounded-xl py-3 pl-11 pr-4 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium" 
                        name="identifier" 
                        required 
                        autocomplete="off"
                        placeholder="Enter your email address">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-emerald-700/20 hover:shadow-emerald-700/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                <span class="btn-text">Find Account & Continue</span>
                <i class="btn-icon fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>
    </div>
</div>
