@php
    $providers = collect();
    try {
        if (class_exists(\Ataurbdx\Authenticator\Modules\Socialite\Models\SocialProvider::class)) {
            $providers = \Ataurbdx\Authenticator\Modules\Socialite\Models\SocialProvider::where('is_active', true)->get();
        }
    } catch (\Throwable $e) {
        $providers = collect();
    }
@endphp

@if ($providers->isNotEmpty())
    <div class="space-y-3">
        @foreach ($providers as $provider)
            @php
                $providerSlug = strtolower($provider->provider);
                $redirectUrl = route('authenticator.api.social.redirect', $providerSlug);
                
                // Styling details
                $iconClass = $provider->icon ?: match($providerSlug) {
                    'google' => 'fa-brands fa-google text-rose-500',
                    'facebook' => 'fa-brands fa-facebook text-blue-500',
                    'github' => 'fa-brands fa-github text-slate-100',
                    'apple' => 'fa-brands fa-apple text-white',
                    default => 'fa-solid fa-globe text-emerald-400',
                };
            @endphp

            <a href="{{ $redirectUrl }}"
               class="group w-full py-3 px-4 bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700 rounded-xl text-white text-sm font-medium transition-all duration-200 flex items-center justify-center gap-3 no-underline shadow-sm hover:shadow-md hover:scale-[1.01] active:scale-[0.99]">
                <i class="{{ $iconClass }} text-base group-hover:scale-110 transition-transform"></i>
                <span class="text-slate-200 group-hover:text-white">Continue with {{ $provider->name }}</span>
            </a>
        @endforeach
    </div>
@else
    <!-- Fallback default buttons if database table is empty -->
    <div class="space-y-3">
        <a href="{{ url('api/v1/auth/social/google') }}"
           class="group w-full py-3 px-4 bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700 rounded-xl text-white text-sm font-medium transition-all duration-200 flex items-center justify-center gap-3 no-underline shadow-sm hover:shadow-md hover:scale-[1.01] active:scale-[0.99]">
            <i class="fa-brands fa-google text-rose-500 text-base group-hover:scale-110 transition-transform"></i>
            <span class="text-slate-200 group-hover:text-white">Continue with Google</span>
        </a>
        <a href="{{ url('api/v1/auth/social/facebook') }}"
           class="group w-full py-3 px-4 bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700 rounded-xl text-white text-sm font-medium transition-all duration-200 flex items-center justify-center gap-3 no-underline shadow-sm hover:shadow-md hover:scale-[1.01] active:scale-[0.99]">
            <i class="fa-brands fa-facebook text-blue-500 text-base group-hover:scale-110 transition-transform"></i>
            <span class="text-slate-200 group-hover:text-white">Continue with Facebook</span>
        </a>
    </div>
@endif
