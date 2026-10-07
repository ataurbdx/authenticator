<form id="submit-form" action="{{ $action ?? route('authenticator.profile.password') }}" method="POST">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ isset($data) && $data ? Crypt::encryptString($data->id) : (auth()->check() ? Crypt::encryptString(auth()->id()) : '') }}">

    <div class="p-4 sm:p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
        <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-2.5 mb-2">
            <i class="fa-solid fa-key text-emerald-400 text-xs"></i> Update Password
        </h4>

        <div class="space-y-3">
            @if(auth()->user()->password)
            <div class="form-wrap space-y-1">
                <label for="current_password" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Current Password <span class="text-rose-500">*</span></label>
                <input type="password" id="current_password" name="current_password" required
                       class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 text-xs sm:text-sm text-white px-3.5 py-2.5 rounded-xl outline-none transition-all font-medium">
                <div class="error-message hidden mt-1 text-[11px] text-rose-500 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i> <span class="error-text"></span>
                </div>
            </div>
            @endif

            <div class="form-wrap space-y-1">
                <label for="password" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">New Password <span class="text-rose-500">*</span></label>
                <input type="password" id="password" name="password" required minlength="8"
                       class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 text-xs sm:text-sm text-white px-3.5 py-2.5 rounded-xl outline-none transition-all font-medium">
                <div class="error-message hidden mt-1 text-[11px] text-rose-500 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i> <span class="error-text"></span>
                </div>
            </div>

            <div class="form-wrap space-y-1">
                <label for="password_confirmation" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Confirm New Password <span class="text-rose-500">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                       class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 text-xs sm:text-sm text-white px-3.5 py-2.5 rounded-xl outline-none transition-all font-medium">
                <div class="error-message hidden mt-1 text-[11px] text-rose-500 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i> <span class="error-text"></span>
                </div>
            </div>
        </div>
    </div>
</form>
