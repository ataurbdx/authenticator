<form id="submit-form" action="{{ $action ?? route('authenticator.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ isset($data) && $data ? Crypt::encryptString($data->id) : (auth()->check() ? Crypt::encryptString(auth()->id()) : '') }}">

    @php
        $user = auth()->user();
        $displayName = $user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        $initials = strtoupper(substr($displayName ?: ($user->username ?? 'U'), 0, 2));
    @endphp

    <div class="space-y-4">
        <!-- Avatar Section -->
        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl">
            <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-2.5 mb-3.5">
                <i class="fa-regular fa-image text-emerald-400 text-xs"></i> Profile Photo (Avatar)
            </h4>

            <div class="grid grid-cols-[80px_1fr] sm:grid-cols-[96px_1fr] items-center gap-4">
                <!-- Avatar Preview -->
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden border border-slate-800 shadow-xs relative bg-slate-950 flex items-center justify-center shrink-0">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <span class="text-2xl font-black text-emerald-400 font-['Outfit']">{{ $initials }}</span>
                    @endif
                </div>

                <!-- Avatar Upload Input -->
                <div class="min-w-0 space-y-1.5 form-wrap">
                    <label for="avatar" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Upload New Photo
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/*"
                           class="w-full text-xs text-slate-400 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-500/10 file:text-emerald-400 hover:file:bg-emerald-500/20 file:cursor-pointer bg-slate-950 border border-slate-800 rounded-xl p-1.5 cursor-pointer">
                    <p class="text-[11px] text-slate-400">Square JPG, PNG or WEBP (Max 2MB)</p>
                    <div class="error-message hidden text-[11px] text-rose-500 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> <span class="error-text"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Names & Bio -->
        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2 border-b border-slate-800 pb-2.5">
                <i class="fa-solid fa-user-pen text-emerald-400 text-xs"></i> Personal Information
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="form-wrap space-y-1">
                    <label for="first_name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">First Name</label>
                    <input type="text" id="first_name" name="first_name"
                           value="{{ old('first_name', $user->first_name ?? '') }}"
                           placeholder="Enter first name"
                           class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 text-xs sm:text-sm text-white px-3.5 py-2.5 rounded-xl outline-none transition-all font-medium">
                    <div class="error-message hidden mt-1 text-[11px] text-rose-500 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> <span class="error-text"></span>
                    </div>
                </div>

                <div class="form-wrap space-y-1">
                    <label for="last_name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Last Name</label>
                    <input type="text" id="last_name" name="last_name"
                           value="{{ old('last_name', $user->last_name ?? '') }}"
                           placeholder="Enter last name"
                           class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 text-xs sm:text-sm text-white px-3.5 py-2.5 rounded-xl outline-none transition-all font-medium">
                    <div class="error-message hidden mt-1 text-[11px] text-rose-500 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> <span class="error-text"></span>
                    </div>
                </div>

                <div class="form-wrap sm:col-span-2 space-y-1">
                    <label for="about" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">About / Bio</label>
                    <textarea id="about" name="about" rows="3"
                              placeholder="Write a brief bio about yourself..."
                              class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 text-xs sm:text-sm text-white px-3.5 py-2.5 rounded-xl outline-none transition-all font-medium">{{ old('about', $user->about ?? '') }}</textarea>
                    <div class="error-message hidden mt-1 text-[11px] text-rose-500 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> <span class="error-text"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
