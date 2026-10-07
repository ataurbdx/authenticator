@extends('authenticator.profile.layout')

@section('title', 'Dashboard — Authenticator')

@section('content')
@php
    $user = auth()->user();
    $displayName = $user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
    if (empty($displayName)) {
        $displayName = $user->username ?? 'User';
    }
    $initials = strtoupper(substr($displayName, 0, 2));
    $isEmailVerified = !empty($user->email_verified_at);
    $isPhoneVerified = !empty($user->phone_verified_at);
@endphp

<div class="space-y-6">

    <!-- Top Navigation Bar -->
    <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-4 sm:p-6 shadow-2xl shadow-black/50 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4 w-full sm:w-auto">
            <div class="relative w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white text-lg font-black shadow-lg shadow-emerald-600/30 shrink-0">
                <span>{{ $initials }}</span>
                <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-slate-900 rounded-full" title="Active"></span>
            </div>
            <div class="min-w-0">
                <h1 class="text-lg sm:text-xl font-extrabold text-white tracking-tight truncate flex items-center gap-2">
                    {{ $displayName }}
                </h1>
                <p class="text-xs text-slate-400 truncate">
                    &#64;{{ $user->username ?? 'user' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
            @if(Route::has('authenticator.profile'))
                <a href="{{ route('authenticator.profile') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700/80 border border-slate-700 text-slate-200 text-xs font-bold transition-all shadow-sm">
                    <i class="fa-regular fa-user text-emerald-400"></i>
                    <span>Profile</span>
                </a>
            @endif

            <form action="{{ route('authenticator.sign-out') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 hover:text-rose-300 text-xs font-bold transition-all cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Stats Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Account Status -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-5 shadow-xl flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Account Status</span>
                <span class="text-sm font-extrabold text-white">Active</span>
                <span class="block text-[10px] text-emerald-400 font-semibold mt-0.5">Ready to use</span>
            </div>
        </div>

        <!-- Security / 2FA -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-5 shadow-xl flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl {{ $user->two_factor ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-slate-800 text-slate-400 border-slate-700' }} border flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Security Level</span>
                <span class="text-sm font-extrabold text-white">
                    {{ $user->two_factor ? '2FA Enabled' : 'Standard' }}
                </span>
                <span class="block text-[10px] text-slate-400 font-medium mt-0.5">
                    {{ !empty($user->password) ? 'Password protected' : 'OTP access only' }}
                </span>
            </div>
        </div>

        <!-- Member Since -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-5 shadow-xl flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center text-lg shrink-0">
                <i class="fa-regular fa-calendar-check"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Member Since</span>
                <span class="text-sm font-extrabold text-white">
                    {{ optional($user->created_at)->format('M d, Y') ?? 'Recent' }}
                </span>
                <span class="block text-[10px] text-slate-400 font-medium mt-0.5">
                    {{ optional($user->created_at)->diffForHumans() ?? 'Just now' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Account Details & Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Overview Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 shadow-2xl shadow-black/50 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-regular fa-id-badge text-emerald-400"></i> Account Information
                </h3>
                <a href="{{ route('authenticator.profile') }}" class="text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition-colors">
                    Manage
                </a>
            </div>

            <div class="space-y-3 text-xs">
                <!-- Username -->
                <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800/80 flex items-center justify-between">
                    <span class="text-slate-400 font-medium">Username</span>
                    <span class="font-bold text-white">&#64;{{ $user->username ?? '—' }}</span>
                </div>

                <!-- Email Address with Verified or Warning Caution Icon -->
                <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800/80 flex items-center justify-between gap-3">
                    <span class="text-slate-400 font-medium">Email Address</span>
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="font-bold text-white truncate" title="{{ $user->email ?? '—' }}">
                            {{ $user->email ?? '—' }}
                        </span>
                        @if($user->email)
                            @if($isEmailVerified)
                                <i class="fa-solid fa-circle-check text-emerald-400 text-xs shrink-0" title="Verified"></i>
                            @else
                                <a href="{{ route('authenticator.profile') }}" 
                                   class="text-amber-400 hover:text-amber-300 transition-colors shrink-0 cursor-pointer inline-flex items-center" 
                                   title="Unverified Email">
                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Phone Number with Verified or Warning Caution Icon -->
                <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800/80 flex items-center justify-between gap-3">
                    <span class="text-slate-400 font-medium">Phone Number</span>
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="font-bold text-white truncate" title="{{ $user->phone ?? '—' }}">
                            {{ $user->phone ?? '—' }}
                        </span>
                        @if($user->phone)
                            @if($isPhoneVerified)
                                <i class="fa-solid fa-circle-check text-emerald-400 text-xs shrink-0" title="Verified"></i>
                            @else
                                <a href="{{ route('authenticator.profile') }}" 
                                   class="text-amber-400 hover:text-amber-300 transition-colors shrink-0 cursor-pointer inline-flex items-center" 
                                   title="Unverified Phone">
                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Shortcuts Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 shadow-2xl shadow-black/50 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-emerald-400"></i> Quick Actions
                </h3>
            </div>

            <div class="grid grid-cols-1 gap-3">
                @if(Route::has('authenticator.profile'))
                    <a href="{{ route('authenticator.profile') }}"
                       class="p-3.5 bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700 rounded-2xl flex items-center justify-between transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-user-pen"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">Profile</h4>
                                <p class="text-[11px] text-slate-400">Update name, contact info, and bio</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-500 text-xs group-hover:text-white transition-colors"></i>
                    </a>
                @endif

                <a href="{{ url('/') }}"
                   class="p-3.5 bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700 rounded-2xl flex items-center justify-between transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-sm group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-house"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white">Back to Home</h4>
                            <p class="text-[11px] text-slate-400">Visit public website homepage</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-500 text-xs group-hover:text-white transition-colors"></i>
                </a>

                <form action="{{ route('authenticator.sign-out') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full text-left p-3.5 bg-slate-950/60 hover:bg-rose-500/10 border border-slate-800 hover:border-rose-500/30 rounded-2xl flex items-center justify-between transition-all group cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-sm group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-rose-300">Sign Out</h4>
                                <p class="text-[11px] text-slate-400">Safely terminate your current session</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-500 text-xs group-hover:text-rose-400 transition-colors"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
