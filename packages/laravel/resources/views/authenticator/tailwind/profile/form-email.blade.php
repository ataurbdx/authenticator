<form id="submit-form" action="{{ $action ?? route('authenticator.profile.email') }}" method="POST">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ isset($data) && $data ? Crypt::encryptString($data->id) : (auth()->check() ? Crypt::encryptString(auth()->id()) : '') }}">

    <div class="p-4 sm:p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
        @if(!empty(auth()->user()->email))
        <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800/80 flex items-center justify-between text-xs">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Current Email Address</span>
                <span class="font-bold text-white">{{ auth()->user()->email }}</span>
            </div>
            @if(!empty(auth()->user()->email_verified_at))
                <span class="inline-flex items-center text-emerald-400 text-xs gap-1 font-bold" title="Verified">
                    <i class="fa-solid fa-circle-check"></i> Verified
                </span>
            @else
                <span class="inline-flex items-center text-amber-400 text-xs gap-1 font-bold" title="Unverified">
                    <i class="fa-solid fa-triangle-exclamation"></i> Unverified
                </span>
            @endif
        </div>
        @endif

        <div class="form-wrap space-y-1.5">
            <label for="email" class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-regular fa-envelope text-emerald-400 text-xs"></i> New Email Address <span class="text-rose-500">*</span>
            </label>
            <input type="email" id="email" name="email"
                value="{{ old('email', auth()->user()->email ?? '') }}"
                placeholder="name@example.com"
                autocomplete="email"
                required
                class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 text-xs sm:text-sm text-white px-3.5 py-2.5 rounded-xl outline-none transition-all font-medium">
            <p class="text-[11px] text-slate-400">A verification link or code will be sent to confirm this new address.</p>
            <div class="error-message hidden mt-1 text-[11px] text-rose-500 font-semibold flex items-center gap-1">
                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> <span class="error-text"></span>
            </div>
        </div>
    </div>
</form>
