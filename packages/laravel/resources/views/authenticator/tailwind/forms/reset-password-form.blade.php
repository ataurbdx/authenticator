<form method="POST" action="{{ route('authenticator.reset-password.submit') ?? '#' }}" class="space-y-4 auth-standard-form">
    @csrf

    <input type="hidden" name="token" value="{{ $token ?? '' }}">
    <input type="hidden" name="identifier" value="{{ request('identifier') ?? old('identifier') }}">

    <div>
        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
            New Password
        </label>
        <div class="relative auth-password-group">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                <i class="fa-solid fa-lock text-sm"></i>
            </div>
            <input 
                type="password" 
                name="password" 
                required 
                placeholder="••••••••" 
                class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-10 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
            >
            <button 
                type="button" 
                class="auth-password-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300 transition-colors bg-transparent border-none cursor-pointer"
            >
                <i class="fa-regular fa-eye text-xs"></i>
            </button>
        </div>
    </div>

    <div>
        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
            Confirm Password
        </label>
        <div class="relative auth-password-group">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                <i class="fa-solid fa-shield text-sm"></i>
            </div>
            <input 
                type="password" 
                name="password_confirmation" 
                required 
                placeholder="••••••••" 
                class="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2.5 pl-10 pr-10 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
            >
            <button 
                type="button" 
                class="auth-password-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300 transition-colors bg-transparent border-none cursor-pointer"
            >
                <i class="fa-regular fa-eye text-xs"></i>
            </button>
        </div>
    </div>

    <button 
        type="submit" 
        class="w-full mt-2 py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-700/20 hover:shadow-emerald-700/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer"
    >
        <span class="btn-text">Update Password</span>
        <i class="btn-icon fa-solid fa-check text-xs"></i>
    </button>
</form>
