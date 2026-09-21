@props([
    'eyebrow' => 'Secure workspace',
    'title',
    'description' => null,
    'panelTitle' => 'Build with confidence.',
    'panelDescription' => 'Everything you need to manage your business, customers and growth from one focused workspace.',
    'backRoute' => 'homepage',
    'backLabel' => 'Back to website',
])

<div class="auth-page">
    <div class="auth-shell">
        <aside class="auth-visual" aria-label="Right platform introduction">
            <div class="auth-visual__glow auth-visual__glow--one"></div>
            <div class="auth-visual__glow auth-visual__glow--two"></div>
            <a href="{{ route('homepage') }}" class="auth-brand auth-brand--light">
                <img src="{{ asset('assets/img/site_logo.webp') }}" alt="Right" class="auth-brand__logo">
                <span class="auth-brand__label">{{ $eyebrow }}</span>
            </a>

            <div class="auth-visual__content">
                <span class="auth-kicker">RIGHT PLATFORM</span>
                <h2>{{ $panelTitle }}</h2>
                <p>{{ $panelDescription }}</p>
                <div class="auth-points" aria-label="Platform benefits">
                    <span><i class="fas fa-check"></i> Simple, secure access</span>
                    <span><i class="fas fa-check"></i> Built for momentum</span>
                </div>
            </div>

            <div class="auth-visual__footer">
                <span class="auth-visual__line"></span>
                <span>One place for what matters.</span>
            </div>
        </aside>

        <main class="auth-content">
            <div class="auth-content__inner">
                <div class="auth-mobile-brand">
                    <a href="{{ route('homepage') }}" class="auth-brand">
                        <img src="{{ asset('assets/img/site_logo.webp') }}" alt="Right" class="auth-brand__logo">
                    </a>
                    <a href="{{ route($backRoute) }}" class="auth-back-link">
                        <i class="fas fa-arrow-left"></i> {{ $backLabel }}
                    </a>
                </div>

                <div class="auth-heading">
                    <span class="auth-heading__eyebrow">{{ $eyebrow }}</span>
                    <h1>{{ $title }}</h1>
                    @if ($description)
                        <p>{{ $description }}</p>
                    @endif
                </div>

                <div class="auth-form-card">
                    {{ $slot }}
                </div>

                @if (isset($footer))
                    <div class="auth-footer">{{ $footer }}</div>
                @endif
            </div>
        </main>
    </div>
</div>
