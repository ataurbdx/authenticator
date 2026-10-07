<form method="POST" action="{{ route('authenticator.forgot-password.submit') ?? '#' }}" class="space-y-5 auth-standard-form">
    @csrf

    <div>
        <label for="identifier" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
            Email or Phone Number (With Code)
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                <i class="fa-regular fa-envelope text-sm"></i>
            </div>
            <input 
                type="text" 
                id="identifier" 
                name="identifier" 
                value="{{ old('identifier') }}" 
                required 
                autofocus
                placeholder="Enter your email or phone" 
                class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-3.5 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
            >
        </div>
    </div>

    <button 
        type="submit" 
        class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-700/20 hover:shadow-emerald-700/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer"
    >
        <span class="btn-text">Reset Password</span>
        <i class="btn-icon fa-solid fa-arrow-right-long text-xs"></i>
    </button>
</form>
