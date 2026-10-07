@php
    $activeTab = $activeTab ?? 'account';
    $isModal = $isModal ?? false;
@endphp

<div class="auth-tabs-wrapper mb-6">
    <div class="bg-slate-950/60 p-1.5 rounded-2xl border border-slate-800/80 flex items-center justify-between gap-1 shadow-inner">
        <!-- Account Tab -->
        @if ($isModal)
            <button type="button" data-auth-tab="account"
                class="auth-tab-btn flex-1 py-2 sm:py-2.5 px-3 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-1.5 cursor-pointer border-0 {{ $activeTab === 'account' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-700/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-compass text-[11px]"></i>
                <span>Account</span>
            </button>
        @else
            <a href="{{ route('authenticator.account') }}" data-auth-tab="account"
                class="auth-tab-btn flex-1 py-2 sm:py-2.5 px-3 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-1.5 no-underline {{ $activeTab === 'account' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-700/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-compass text-[11px]"></i>
                <span>Account</span>
            </a>
        @endif

        <!-- Sign In Tab -->
        @if ($isModal)
            <button type="button" data-auth-tab="sign-in"
                class="auth-tab-btn flex-1 py-2 sm:py-2.5 px-3 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-1.5 cursor-pointer border-0 {{ $activeTab === 'sign-in' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-700/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-arrow-right-to-bracket text-[11px]"></i>
                <span>Sign In</span>
            </button>
        @else
            <a href="{{ route('authenticator.sign-in') }}" data-auth-tab="sign-in"
                class="auth-tab-btn flex-1 py-2 sm:py-2.5 px-3 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-1.5 no-underline {{ $activeTab === 'sign-in' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-700/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-arrow-right-to-bracket text-[11px]"></i>
                <span>Sign In</span>
            </a>
        @endif

        <!-- Sign Up Tab -->
        @if ($isModal)
            <button type="button" data-auth-tab="sign-up"
                class="auth-tab-btn flex-1 py-2 sm:py-2.5 px-3 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-1.5 cursor-pointer border-0 {{ $activeTab === 'sign-up' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-700/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-user-plus text-[11px]"></i>
                <span>Sign Up</span>
            </button>
        @else
            <a href="{{ route('authenticator.sign-up') }}" data-auth-tab="sign-up"
                class="auth-tab-btn flex-1 py-2 sm:py-2.5 px-3 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-1.5 no-underline {{ $activeTab === 'sign-up' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-700/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-user-plus text-[11px]"></i>
                <span>Sign Up</span>
            </a>
        @endif
    </div>
</div>
