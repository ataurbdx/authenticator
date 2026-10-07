<form id="submit-form" action="{{ $action ?? route('authenticator.profile.phone') }}" method="POST">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ isset($data) && $data ? Crypt::encryptString($data->id) : (auth()->check() ? Crypt::encryptString(auth()->id()) : '') }}">

    @php
        $user = auth()->user();
        $currentPhone = $user->phone ?? '';
        
        $phoneData = null;
        if (!empty($user->phone_data)) {
            $phoneData = is_array($user->phone_data) ? $user->phone_data : json_decode($user->phone_data, true);
        }

        $countryCode = $phoneData['code'] ?? '+880';
        $rawNumber = $phoneData['number'] ?? '';

        if (empty($rawNumber) && !empty($currentPhone)) {
            $digits = ltrim(preg_replace('/[^\d]/', '', $currentPhone), '0');
            if (str_starts_with($digits, '880')) {
                $countryCode = '+880';
                $rawNumber = substr($digits, 3);
            } else {
                $rawNumber = $digits;
            }
        }
    @endphp

    <div class="p-4 sm:p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
        @if(!empty($currentPhone))
        <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800/80 flex items-center justify-between text-xs">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Current Phone Number</span>
                <span class="font-bold text-white">{{ $currentPhone }}</span>
            </div>
            @if(!empty($user->phone_verified_at))
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
            <label for="number" class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-phone text-emerald-400 text-xs"></i> New Phone Number <span class="text-rose-500">*</span>
            </label>
            <div class="flex gap-2">
                <div class="relative w-1/3 sm:w-1/4">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-globe text-xs sm:text-sm"></i>
                    </div>
                    <input 
                        type="text" 
                        id="code"
                        name="code" 
                        value="{{ old('code', $countryCode) }}" 
                        placeholder="+880" 
                        class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 text-xs sm:text-sm text-white py-2.5 pl-7 sm:pl-8 pr-2 text-center rounded-xl outline-none transition-all font-medium h-11"
                    >
                </div>
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <i class="fa-solid fa-phone text-xs sm:text-sm"></i>
                    </div>
                    <input 
                        type="tel" 
                        id="number" 
                        name="number"
                        value="{{ old('number', $rawNumber) }}"
                        placeholder="17XXXXXXXX"
                        maxlength="10"
                        autocomplete="tel"
                        required
                        class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 text-xs sm:text-sm text-white pl-9 sm:pl-10 pr-3.5 py-2.5 rounded-xl outline-none transition-all font-medium h-11"
                    >
                </div>
            </div>
            <p class="text-[11px] text-slate-400">Enter your 10-digit mobile number (e.g. 17XXXXXXXX) to receive OTP verification.</p>
            <div class="error-message hidden mt-1 text-[11px] text-rose-500 font-semibold flex items-center gap-1">
                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> <span class="error-text"></span>
            </div>
        </div>
    </div>
</form>
