<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'CCTAS') }} | {{ __('Campus research tracking and archive') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|manrope:600,700,800|space-mono:400,700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="landing-body">
        <main class="landing-page">
            <nav class="landing-nav" aria-label="{{ __('Main navigation') }}">
                <a href="{{ url('/') }}" class="landing-brand">
                    <span class="landing-brand-mark"><x-application-logo class="h-6 w-6 fill-current" /></span>
                    <span><strong>{{ config('app.name', 'CCTAS') }}</strong><small>{{ __('Centralized Campus Tracking and Archival System') }}</small></span>
                </a>
                <div class="landing-nav-links">
                    @auth
                        <a href="{{ url('/dashboard') }}">{{ __('Open workspace') }} <span>&rarr;</span></a>
                    @else
                        <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="landing-nav-cta">{{ __('Create account') }}</a>
                        @endif
                    @endauth
                </div>
            </nav>

            <section class="landing-hero">
                <div class="landing-hero-copy">
                    <p class="landing-kicker"><span></span>{{ __('Capstone research intelligence platform') }}</p>
                    <h1>{{ __('Make every research milestone visible.') }}</h1>
                    <p class="landing-lede">{{ __('A shared workspace for students, advisers, panels, and coordinators to move ideas from proposal to publication with clarity.') }}</p>
                    <div class="landing-actions">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="landing-primary">{{ __('Enter workspace') }} <span>&rarr;</span></a>
                        @else
                            <a href="{{ route('login') }}" class="landing-primary">{{ __('Enter workspace') }} <span>&rarr;</span></a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="landing-secondary">{{ __('Get started') }}</a>
                            @endif
                        @endauth
                    </div>
                    <div class="landing-proof"><span class="landing-proof-dot"></span>{{ __('Structured for academic teams') }} <span class="landing-proof-divider"></span> {{ __('Built for momentum') }}</div>
                </div>
                <div class="landing-visual" aria-label="{{ __('Research workflow visualization') }}">
                    <div class="landing-orbit landing-orbit-one"></div>
                    <div class="landing-orbit landing-orbit-two"></div>
                    <div class="landing-node landing-node-main"><span class="landing-node-icon">01</span><strong>{{ __('Research') }}</strong><small>{{ __('A clear source of truth') }}</small></div>
                    <div class="landing-node landing-node-top"><span>02</span>{{ __('Review') }}</div>
                    <div class="landing-node landing-node-bottom"><span>03</span>{{ __('Progress') }}</div>
                    <div class="landing-visual-label">{{ __('SYSTEM / 01') }}<br><span>{{ __('Connected research flow') }}</span></div>
                </div>
            </section>

            <section class="landing-footer-grid">
                <div><span>01</span><strong>{{ __('One shared record') }}</strong><p>{{ __('Keep documents, status, and feedback connected.') }}</p></div>
                <div><span>02</span><strong>{{ __('Clear next steps') }}</strong><p>{{ __('Turn review cycles into visible momentum.') }}</p></div>
                <div><span>03</span><strong>{{ __('Academic confidence') }}</strong><p>{{ __('Present progress with structure and context.') }}</p></div>
            </section>
        </main>
    </body>
</html>
