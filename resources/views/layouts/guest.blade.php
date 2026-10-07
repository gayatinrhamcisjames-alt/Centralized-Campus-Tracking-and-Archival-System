<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CCTAS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|manrope:500,600,700,800|space-mono:400,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="auth-body font-sans antialiased">
        <main class="auth-page">
            <div class="auth-background" aria-hidden="true">
                <span class="auth-orb auth-orb-one"></span>
                <span class="auth-orb auth-orb-two"></span>
                <span class="auth-orb auth-orb-three"></span>
                <span class="auth-grid"></span>
                <span class="auth-particle auth-particle-one"></span>
                <span class="auth-particle auth-particle-two"></span>
                <span class="auth-particle auth-particle-three"></span>
            </div>
            <div class="auth-shell {{ request()->routeIs('register') ? 'auth-shell-register' : 'auth-shell-login' }}">
                <aside class="auth-visual">
                    <a href="/" class="auth-brand" aria-label="{{ config('app.name', 'CCTAS') }} home">
                        <span class="auth-brand-mark"><x-application-logo /></span>
                        <span>{{ config('app.name', 'CCTAS') }}</span>
                    </a>
                    <div class="auth-visual-copy">
                        <p class="auth-kicker">{{ __('Centralized Campus Tracking and Archival System') }}</p>
                        <h1>{{ __('One workspace for every milestone.') }}</h1>
                        <p>{{ __('Coordinate proposals, reviews, documents, and progress with clarity from a single secure workspace.') }}</p>
                    </div>
                    <div class="auth-visual-art"><span></span><span></span><span></span><span></span></div>
                    <p class="auth-visual-footer">{{ __('Built for focused academic collaboration.') }}</p>
                </aside>
                <section class="auth-form-panel" aria-label="{{ request()->routeIs('register') ? __('Create an account') : __('Sign in') }}">
                    <div class="auth-mobile-brand">
                        <span class="auth-brand-mark"><x-application-logo /></span>
                        <span>{{ config('app.name', 'CCTAS') }}</span>
                    </div>
                    {{ $slot }}
                </section>
            </div>
        </main>
    </body>
</html>
