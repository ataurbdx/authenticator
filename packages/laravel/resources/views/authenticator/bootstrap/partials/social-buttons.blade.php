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

    <!-- Divider -->
    <div class="d-flex align-items-center my-3">
        <hr class="flex-grow-1 text-muted my-0">
        <span class="px-2 small text-muted text-uppercase" style="font-size: 11px;">Or continue with</span>
        <hr class="flex-grow-1 text-muted my-0">
    </div>

    <!-- Social Providers Grid -->
    <div class="d-grid gap-2 mb-3">
        @if($dynamicProviders->isNotEmpty())
            @foreach($dynamicProviders as $provider)
                <a href="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/social/' . $provider->provider) }}" 
                   class="btn btn-outline-secondary py-2 small text-dark d-flex align-items-center justify-content-center gap-2">
                    @if($provider->icon)
                        <i class="{{ $provider->icon }}"></i>
                    @else
                        <i class="bi bi-{{ $provider->provider }}"></i>
                    @endif
                    <span>{{ $provider->name }}</span>
                </a>
            @endforeach
        @else
            <!-- Static Default Socialite Providers (Fallback) -->
            <a href="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/social/google') }}" 
               class="btn btn-outline-secondary py-2 small text-dark d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-google text-danger"></i>
                <span>Continue with Google</span>
            </a>
            <a href="{{ url(config('authenticator.routes.api_prefix', 'api/v1/auth') . '/social/github') }}" 
               class="btn btn-outline-secondary py-2 small text-dark d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-github"></i>
                <span>Continue with GitHub</span>
            </a>
        @endif
    </div>
@endif
