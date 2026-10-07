@extends('authenticator.profile.layout')

@section('title', 'My Profile — Authenticator')

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
    $bothVerified = $isEmailVerified && $isPhoneVerified;
@endphp

<div class="space-y-6">

    <!-- Top Navigation Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-regular fa-user text-emerald-400"></i> My Profile
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">Manage your personal information, security credentials, and verifications</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('authenticator.dashboard') }}"
               class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-xs font-bold transition-all shadow-sm">
                <i class="fa-solid fa-gauge text-teal-400 text-xs"></i>
                <span>Dashboard</span>
            </a>
            <button type="button" onclick="openProfileModal('edit-profile')"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:brightness-110 text-white text-xs font-bold shadow-md shadow-emerald-700/20 transition-all cursor-pointer border-none shrink-0">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
                <span>Edit Profile</span>
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('status') || session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-semibold flex items-center gap-2.5 shadow-sm">
            <i class="fa-solid fa-circle-check text-sm text-emerald-400"></i>
            <span>{{ session('status') ?? session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs font-semibold space-y-1 shadow-sm">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-sm text-rose-400"></i>
                <span>Action could not be completed:</span>
            </div>
            <ul class="list-disc list-inside text-[11px] text-rose-400 pl-4 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- VERIFICATION SECTION (SEPARATE EMAIL & PHONE STATUS) -->
    <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl relative overflow-hidden">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800/80 mb-4">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-emerald-400 text-sm"></i> Verification Status
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Email Verification Box -->
            <div class="p-4 rounded-2xl border {{ $isEmailVerified ? 'border-emerald-500/20 bg-emerald-500/5' : 'border-amber-500/20 bg-amber-500/5' }} flex items-center justify-between gap-3">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-11 h-11 rounded-2xl {{ $isEmailVerified ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }} flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid {{ $isEmailVerified ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-white truncate">Email Address</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                            {{ $user->email ?? 'No email added' }}
                        </p>
                        <span class="inline-block text-[10px] font-extrabold {{ $isEmailVerified ? 'text-emerald-400' : 'text-amber-400' }} mt-0.5">
                            {{ $isEmailVerified ? 'Verified' : 'Pending Verification' }}
                        </span>
                    </div>
                </div>

                @if(!$isEmailVerified && $user->email)
                    <a href="{{ route('authenticator.otp.verify') }}?type=email&email={{ urlencode($user->email) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/30 text-amber-300 text-xs font-bold transition-all shrink-0">
                        <i class="fa-solid fa-shield-halved text-[10px]"></i> Verify
                    </a>
                @endif
            </div>

            <!-- Phone Verification Box -->
            <div class="p-4 rounded-2xl border {{ $isPhoneVerified ? 'border-emerald-500/20 bg-emerald-500/5' : 'border-amber-500/20 bg-amber-500/5' }} flex items-center justify-between gap-3">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-11 h-11 rounded-2xl {{ $isPhoneVerified ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }} flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid {{ $isPhoneVerified ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-white truncate">Phone Number</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                            {{ $user->phone ?? 'No phone added' }}
                        </p>
                        <span class="inline-block text-[10px] font-extrabold {{ $isPhoneVerified ? 'text-emerald-400' : 'text-amber-400' }} mt-0.5">
                            {{ $isPhoneVerified ? 'Verified' : 'Pending Verification' }}
                        </span>
                    </div>
                </div>

                @if(!$isPhoneVerified && $user->phone)
                    <a href="{{ route('authenticator.otp.verify') }}?type=phone&number={{ urlencode($user->phone) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/30 text-amber-300 text-xs font-bold transition-all shrink-0">
                        <i class="fa-solid fa-shield-halved text-[10px]"></i> Verify
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- PERSONAL INFORMATION CARD (EXACT MATCHING DESIGN) -->
    <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800/80">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i class="fa-regular fa-id-card text-emerald-400 text-sm"></i> Personal Information
            </h3>
            <span class="text-[11px] text-slate-400">Member since {{ optional($user->created_at)->format('M d, Y') ?? 'Recent' }}</span>
        </div>

        <div class="flex flex-col md:flex-row items-start gap-6">
            <!-- Profile Photo Box -->
            <div class="flex flex-col items-center text-center shrink-0 mx-auto md:mx-0">
                <div class="relative w-24 h-24 rounded-3xl overflow-hidden border-2 border-emerald-500/30 p-1 bg-gradient-to-tr from-emerald-500/10 to-teal-500/10 shadow-sm flex items-center justify-center">
                    <div class="w-full h-full rounded-2xl overflow-hidden bg-slate-950 flex items-center justify-center text-emerald-400 text-2xl font-black">
                        @if ($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $displayName }}" class="w-full h-full object-cover">
                        @else
                            <span>{{ $initials }}</span>
                        @endif
                    </div>
                    @if($isEmailVerified || $isPhoneVerified)
                        <span class="absolute bottom-1 right-1 w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] border-2 border-slate-900 shadow-xs" title="Verified Member">
                            <i class="fa-solid fa-check"></i>
                        </span>
                    @endif
                </div>
                <span class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-wider">Profile Photo</span>
            </div>

            <!-- Details Grid -->
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                <!-- Full Name -->
                <div class="p-3.5 bg-slate-950/60 rounded-2xl border border-slate-800/80">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Full Name</span>
                    <p class="text-xs font-bold text-white">{{ $displayName }}</p>
                </div>

                <!-- Username (Separate Form Trigger) -->
                <div class="p-3.5 bg-slate-950/60 rounded-2xl border border-slate-800/80">
                    <div class="flex items-center justify-between mb-1 min-h-[20px]">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Username</span>
                        <button type="button" onclick="openProfileModal('username')"
                            class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition-colors cursor-pointer bg-transparent border-none">
                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                            <span>Change</span>
                        </button>
                    </div>
                    <p class="text-xs font-bold text-white">&#64;{{ $user->username ?: '—' }}</p>
                </div>

                <!-- Email Address (Separate Form Trigger) -->
                <div class="p-3.5 bg-slate-950/60 rounded-2xl border border-slate-800/80">
                    <div class="flex items-center justify-between mb-1 min-h-[20px] gap-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email Address</span>
                        <button type="button" onclick="openProfileModal('email')"
                            class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition-colors cursor-pointer bg-transparent border-none">
                            <i class="fa-solid {{ $user->email ? 'fa-pen-to-square' : 'fa-plus' }} text-[10px]"></i>
                            <span>{{ $user->email ? 'Change' : 'Add' }}</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-1.5 min-w-0">
                        <p class="text-xs font-bold text-white truncate" title="{{ $user->email ?: '—' }}">{{ $user->email ?: '—' }}</p>
                        @if($user->email)
                            @if($isEmailVerified)
                                <span class="inline-flex items-center text-emerald-400 shrink-0" title="Email Verified">
                                    <i class="fa-solid fa-circle-check text-xs"></i>
                                </span>
                            @else
                                <a href="{{ route('authenticator.otp.verify') }}?type=email&email={{ urlencode($user->email) }}" 
                                   class="inline-flex items-center text-amber-400 hover:text-amber-300 transition-colors shrink-0 cursor-pointer" 
                                   title="Email is unverified. Click to verify">
                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Phone Number (Separate Form Trigger) -->
                <div class="p-3.5 bg-slate-950/60 rounded-2xl border border-slate-800/80">
                    <div class="flex items-center justify-between mb-1 min-h-[20px] gap-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Phone Number</span>
                        <button type="button" onclick="openProfileModal('phone')"
                            class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition-colors cursor-pointer bg-transparent border-none">
                            <i class="fa-solid {{ $user->phone ? 'fa-pen-to-square' : 'fa-plus' }} text-[10px]"></i>
                            <span>{{ $user->phone ? 'Change' : 'Add' }}</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-1.5 min-w-0">
                        <p class="text-xs font-bold text-white">{{ $user->phone ?: '—' }}</p>
                        @if($user->phone)
                            @if($isPhoneVerified)
                                <span class="inline-flex items-center text-emerald-400 shrink-0" title="Phone Verified">
                                    <i class="fa-solid fa-circle-check text-xs"></i>
                                </span>
                            @else
                                <a href="{{ route('authenticator.otp.verify') }}?type=phone&number={{ urlencode($user->phone) }}" 
                                   class="inline-flex items-center text-amber-400 hover:text-amber-300 transition-colors shrink-0 cursor-pointer" 
                                   title="Phone is unverified. Click to verify">
                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- About / Bio -->
                <div class="sm:col-span-2 p-3.5 bg-slate-950/60 rounded-2xl border border-slate-800/80">
                    <div class="flex items-center justify-between mb-1 min-h-[20px]">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">About / Bio</span>
                        <button type="button" onclick="openProfileModal('edit-profile')"
                            class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition-colors cursor-pointer bg-transparent border-none">
                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                            <span>Edit</span>
                        </button>
                    </div>
                    <div class="text-xs text-slate-300 leading-relaxed font-medium">
                        {{ $user->about ?: 'No bio provided yet.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ACCOUNT SECURITY & PASSWORD -->
    <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-lock"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-white">Account Password & Security</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Ensure your account is using a strong password for optimal security</p>
            </div>
        </div>

        <button type="button" onclick="openProfileModal('password')"
            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:brightness-110 text-white text-xs font-bold shadow-md shadow-emerald-700/20 transition-all cursor-pointer border-none shrink-0">
            <i class="fa-solid fa-key text-xs"></i>
            <span>{{ $user->password ? 'Change Password' : 'Set Password' }}</span>
        </button>
    </div>

    <!-- CONNECTED SOCIAL ACCOUNTS (ASSET-SHEBA PATTERN) -->
    <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800/80">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-share-nodes"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider">Connected Social Accounts</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Link your social accounts for fast 1-click login and seamless access</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 pt-1">
            @php
                $activeProviders = collect();
                try {
                    if (class_exists(\Ataurbdx\Authenticator\Modules\Socialite\Models\SocialProvider::class)) {
                        $activeProviders = \Ataurbdx\Authenticator\Modules\Socialite\Models\SocialProvider::where('is_active', true)->get();
                    }
                } catch (\Throwable $e) {
                    $activeProviders = collect();
                }
            @endphp

            @forelse($activeProviders as $provider)
                @php
                    $slug = strtolower($provider->provider);
                    $isLinked = $user->hasSocial($slug);
                    $linkData = $isLinked ? $user->getSocial($slug) : null;
                    $iconClass = $provider->icon ?: match($slug) {
                        'google' => 'fa-brands fa-google text-rose-500',
                        'facebook' => 'fa-brands fa-facebook text-blue-500',
                        'github' => 'fa-brands fa-github text-slate-200',
                        'apple' => 'fa-brands fa-apple text-white',
                        default => 'fa-solid fa-globe text-emerald-400',
                    };
                @endphp
                <div class="p-3.5 bg-slate-950/60 rounded-2xl border {{ $isLinked ? 'border-emerald-500/20 bg-emerald-500/5' : 'border-slate-800/80' }} flex flex-col justify-between gap-3">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-sm shrink-0">
                                <i class="{{ $iconClass }}"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-white block truncate">{{ $provider->name }}</span>
                                <span class="text-[10px] text-slate-400 truncate block">
                                    {{ $isLinked ? ($linkData->email ?? 'Connected') : 'Not linked' }}
                                </span>
                            </div>
                        </div>

                        @if($isLinked)
                            <span class="inline-flex items-center text-emerald-400 text-xs shrink-0" title="Connected">
                                <i class="fa-solid fa-circle-check"></i>
                            </span>
                        @endif
                    </div>

                    <div class="pt-1 border-t border-slate-800/60 flex items-center justify-end">
                        @if($isLinked)
                            <button type="button" onclick="disconnectSocialAccount('{{ $slug }}')"
                                class="text-[11px] font-bold text-rose-400 hover:text-rose-300 transition-colors cursor-pointer bg-transparent border-none flex items-center gap-1">
                                <i class="fa-solid fa-link-slash text-[10px]"></i> Disconnect
                            </button>
                        @else
                            <button type="button" onclick="openSocialPopup('{{ $slug }}', event, '{{ route('authenticator.social.connect', $slug) }}')"
                                class="text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition-colors cursor-pointer bg-transparent border-none flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[10px]"></i> Connect
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-4 text-center text-xs text-slate-500">
                    No active social providers configured.
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- Universal Dynamic Modal for Profile Actions -->
<div id="profile-action-modal"
    class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6 opacity-0 pointer-events-none transition-all duration-300 hidden select-none">
    <!-- Backdrop with blur -->
    <div id="profile-action-backdrop" onclick="closeProfileModal()"
        class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity duration-300"></div>

    <!-- Modal Dialog Window -->
    <div id="profile-action-dialog"
        class="relative w-full max-w-xl bg-slate-900 rounded-3xl overflow-hidden border border-slate-800 shadow-2xl z-10 flex flex-col max-h-[90vh] scale-95 opacity-0 transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between gap-2.5 shrink-0 bg-slate-900">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0">
                    <i id="profile-modal-icon" class="fa-solid fa-pen-to-square text-xs"></i>
                </div>
                <h2 id="profile-modal-title" class="text-base font-bold text-white tracking-tight truncate">
                    Edit Information
                </h2>
            </div>

            <!-- Close Modal Button -->
            <button type="button" onclick="closeProfileModal()"
                class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer shrink-0 border border-slate-700">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Modal Body (Swappable Forms) -->
        <div class="p-4 sm:p-6 overflow-y-auto flex-1 bg-slate-950/40">
            <!-- Form 1: Username -->
            <div id="form-container-username" class="modal-form-panel hidden">
                @include('authenticator.profile.form-username', ['action' => route('authenticator.profile.username')])
            </div>

            <!-- Form 2: Email -->
            <div id="form-container-email" class="modal-form-panel hidden">
                @include('authenticator.profile.form-email', ['action' => route('authenticator.profile.email')])
            </div>

            <!-- Form 3: Phone -->
            <div id="form-container-phone" class="modal-form-panel hidden">
                @include('authenticator.profile.form-phone', ['action' => route('authenticator.profile.phone')])
            </div>

            <!-- Form 4: Edit Profile (Name, Photo, Bio) -->
            <div id="form-container-edit-profile" class="modal-form-panel hidden">
                @include('authenticator.profile.form', ['action' => route('authenticator.profile.update')])
            </div>

            <!-- Form 5: Password -->
            <div id="form-container-password" class="modal-form-panel hidden">
                @include('authenticator.profile.form-password', ['action' => route('authenticator.profile.password')])
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-3.5 bg-slate-900 border-t border-slate-800 flex items-center justify-between gap-3 text-xs shrink-0">
            <button type="button" onclick="closeProfileModal()"
                class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition-all cursor-pointer border border-slate-700">
                Cancel
            </button>
            <button type="button" onclick="submitActiveModalForm()" id="profile-modal-submit-btn"
                class="text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:brightness-110 px-5 py-2 rounded-xl transition-all shadow-md shadow-emerald-700/20 cursor-pointer border-none flex items-center gap-1.5">
                <i class="fa-solid fa-check text-[11px]"></i>
                <span id="profile-modal-submit-text">Save Changes</span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let activeModalFormType = null;

    function openProfileModal(type) {
        activeModalFormType = type;
        const modal = document.getElementById('profile-action-modal');
        const dialog = document.getElementById('profile-action-dialog');
        const titleEl = document.getElementById('profile-modal-title');
        const iconEl = document.getElementById('profile-modal-icon');

        // Hide all form panels
        document.querySelectorAll('.modal-form-panel').forEach(panel => panel.classList.add('hidden'));

        // Config per type
        const configs = {
            'username': {
                title: 'Change Username',
                icon: 'fa-at',
                panelId: 'form-container-username'
            },
            'email': {
                title: 'Change Email Address',
                icon: 'fa-envelope',
                panelId: 'form-container-email'
            },
            'phone': {
                title: 'Change Phone Number',
                icon: 'fa-phone',
                panelId: 'form-container-phone'
            },
            'edit-profile': {
                title: 'Edit Profile Information',
                icon: 'fa-user-pen',
                panelId: 'form-container-edit-profile'
            },
            'password': {
                title: 'Change Account Password',
                icon: 'fa-key',
                panelId: 'form-container-password'
            }
        };

        const config = configs[type] || configs['edit-profile'];
        titleEl.textContent = config.title;
        iconEl.className = 'fa-solid ' + config.icon + ' text-xs';

        const activePanel = document.getElementById(config.panelId);
        if (activePanel) {
            activePanel.classList.remove('hidden');
        }

        // Open animation
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            dialog.classList.remove('scale-95', 'opacity-0');
            dialog.classList.add('scale-100', 'opacity-100');
        }, 10);
        document.body.classList.add('overflow-hidden');
    }

    function closeProfileModal() {
        const modal = document.getElementById('profile-action-modal');
        const dialog = document.getElementById('profile-action-dialog');

        modal.classList.remove('opacity-100', 'pointer-events-auto');
        modal.classList.add('opacity-0', 'pointer-events-none');
        dialog.classList.remove('scale-100', 'opacity-100');
        dialog.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 300);
    }

    function submitActiveModalForm() {
        if (!activeModalFormType) return;
        const panelMap = {
            'username': 'form-container-username',
            'email': 'form-container-email',
            'phone': 'form-container-phone',
            'edit-profile': 'form-container-edit-profile',
            'password': 'form-container-password'
        };

        const panelId = panelMap[activeModalFormType];
        if (panelId) {
            const container = document.getElementById(panelId);
            const form = container ? container.querySelector('form') : null;
            if (form) {
                const submitBtn = document.getElementById('profile-modal-submit-btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Saving...';
                form.submit();
            }
        }
    }

    function disconnectSocialAccount(provider) {
        if (!confirm('Are you sure you want to disconnect your ' + provider.toUpperCase() + ' account?')) {
            return;
        }

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const url = '{{ route("authenticator.social.disconnect", ["provider" => "__PROVIDER__"]) }}'.replace('__PROVIDER__', provider);

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Could not disconnect account.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while disconnecting the account.');
        });
    }
</script>
@endpush
@endsection
