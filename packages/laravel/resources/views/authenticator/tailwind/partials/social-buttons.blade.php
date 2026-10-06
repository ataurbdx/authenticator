@if(config('authenticator.modules.social', true))
    @php
        $dynamicProviders = collect();
        try {
            if (class_exists(\Ataurbdx\Authenticator\Modules\Socialite\Models\SocialProvider::class)) {
                $table = config('authenticator.tables.social_providers', 'social_providers');
                if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                    $dynamicProviders = \Ataurbdx\Authenticator\Modules\Socialite\Models\SocialProvider::where('is_active', true)->get();
                }
            }
        } catch (\Throwable $e) {
            $dynamicProviders = collect();
        }
    @endphp

    <div class="shrink-0 pt-3 border-t border-slate-100 dark:border-white/5 bg-transparent">
        <div class="relative flex py-1.5 items-center mb-2.5">
            <div class="flex-grow border-t border-slate-200/80 dark:border-white/10"></div>
            <span class="flex-shrink mx-2 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Or continue with</span>
            <div class="flex-grow border-t border-slate-200/80 dark:border-white/10"></div>
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            @if($dynamicProviders->isNotEmpty())
                @foreach($dynamicProviders as $provider)
                    <a href="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/social/' . $provider->provider) }}"
                       class="flex items-center justify-center gap-2 py-2.5 px-3 bg-slate-50 dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200/80 dark:border-white/10 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 transition-colors no-underline">
                        @if($provider->icon)
                            <i class="{{ $provider->icon }} text-xs"></i>
                        @else
                            <i class="fa-brands fa-{{ $provider->provider }} text-xs"></i>
                        @endif
                        <span>{{ $provider->name }}</span>
                    </a>
                @endforeach
            @else
                <!-- Static Default Socialite Providers (Fallback) -->
                <a href="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/social/google') }}"
                   class="flex items-center justify-center gap-2 py-2.5 px-3 bg-slate-50 dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200/80 dark:border-white/10 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 transition-colors no-underline">
                    <i class="fa-brands fa-google text-red-500 text-xs"></i>
                    <span>Google</span>
                </a>
                <a href="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/social/github') }}"
                   class="flex items-center justify-center gap-2 py-2.5 px-3 bg-slate-50 dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200/80 dark:border-white/10 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 transition-colors no-underline">
                    <i class="fa-brands fa-github text-slate-900 dark:text-white text-xs"></i>
                    <span>GitHub</span>
                </a>
            @endif
        </div>
    </div>
@endif
