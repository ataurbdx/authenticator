<form method="POST" action="{{ route('authenticator.otp.confirm') ?? '#' }}" class="space-y-6 auth-standard-form">
    @csrf

    <div class="auth-otp-container">
        <div class="flex justify-center gap-2 sm:gap-3">
            @for ($i = 0; $i < 6; $i++)
                <input 
                    type="text" 
                    name="code[]"
                    maxlength="1" 
                    class="w-12 h-12 sm:w-14 sm:h-14 text-center text-xl font-bold bg-slate-950/60 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all otp-input"
                    autocomplete="off"
                >
            @endfor
        </div>
        <input type="hidden" name="verification_code" class="otp-hidden-input">
    </div>

    <button 
        type="submit" 
        id="verify-btn"
        class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-700/20 hover:shadow-emerald-700/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer"
    >
        <span class="btn-text">Verify</span>
        <i class="btn-icon fa-solid fa-shield-check text-xs"></i>
    </button>
</form>

<div class="text-center mt-4">
    <p class="text-sm text-slate-400 mb-2">Didn't receive the code?</p>
    <form method="POST" action="{{ route('authenticator.otp.send') ?? '#' }}">
        @csrf
        <button type="submit" class="font-semibold text-emerald-400 hover:text-emerald-300 transition-colors bg-transparent border-none cursor-pointer">
            Resend Code
        </button>
    </form>
</div>
