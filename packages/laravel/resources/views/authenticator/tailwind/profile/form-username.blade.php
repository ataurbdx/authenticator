<form id="submit-form" action="{{ $action ?? route('authenticator.profile.username') }}" method="POST">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ isset($data) && $data ? Crypt::encryptString($data->id) : (auth()->check() ? Crypt::encryptString(auth()->id()) : '') }}">

    <div class="p-4 sm:p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
        @if(!empty(auth()->user()->username))
        <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800/80 text-xs flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Current Username</span>
            <span class="font-bold text-white">&#64;{{ auth()->user()->username }}</span>
        </div>
        @endif

        <div class="form-wrap space-y-1.5">
            <label for="username" class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-at text-emerald-400 text-xs"></i> New Username <span class="text-rose-500">*</span>
            </label>
            <input type="text" id="username" name="username"
                value="{{ old('username', auth()->user()->username ?? '') }}"
                placeholder="Enter unique username"
                autocomplete="username"
                required
                class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 text-xs sm:text-sm text-white px-3.5 py-2.5 rounded-xl outline-none transition-all font-medium">
            <p class="text-[11px] text-slate-400">Username can only contain letters, numbers, hyphens, and underscores.</p>
            <div class="error-message hidden mt-1 text-[11px] text-rose-500 font-semibold flex items-center gap-1">
                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> <span class="error-text"></span>
            </div>
        </div>
    </div>
</form>
