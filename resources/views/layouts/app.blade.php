<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CCTAS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|manrope:500,600,700,800|space-mono:400,700|plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="app-body font-sans antialiased">
        <div class="app-shell min-h-screen" x-data="{ sidebarOpen: false }">
            @include('layouts.navigation')

            <div class="app-main">
                <header class="app-topbar">
                    <div class="app-topbar-inner">
                        <button type="button" @click="sidebarOpen = ! sidebarOpen" class="app-mobile-toggle" aria-label="{{ __('Toggle sidebar') }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                        <div class="app-topbar-context"><span class="app-context-pulse"></span><span>{{ __('Campus research tracking and archival workspace') }}</span></div>
                        <div class="app-search" role="search" x-data="{ searchOpen: false }">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7" /><path stroke-linecap="round" d="m20 20-4-4" /></svg>
                            <input type="search" @focus="searchOpen = true" @keydown.escape="searchOpen = false" placeholder="{{ __('Search documents, users, projects...') }}" aria-label="{{ __('Search the workspace') }}">
                            <span class="app-search-shortcut">{{ __('Ctrl K') }}</span>
                            <div x-cloak x-show="searchOpen" x-transition.opacity class="app-search-panel">
                                <p>{{ __('Quick find') }}</p>
                                <a href="{{ route('theses.index') }}"><span class="search-result-icon">◫</span><span><strong>{{ __('Research documents') }}</strong><small>{{ __('Browse submissions and records') }}</small></span><span class="search-result-key">↵</span></a>
                                @if (Auth::user()->role === \App\Models\User::ADMINISTRATOR)
                                    <a href="{{ route('users.index') }}"><span class="search-result-icon">◎</span><span><strong>{{ __('People & access') }}</strong><small>{{ __('Manage users and permissions') }}</small></span><span class="search-result-key">↵</span></a>
                                @endif
                                <a href="{{ route('theses.archive') }}"><span class="search-result-icon">▤</span><span><strong>{{ __('Research archive') }}</strong><small>{{ __('Find completed research') }}</small></span><span class="search-result-key">↵</span></a>
                            </div>
                        </div>
                        <div class="app-topbar-actions">
                            <button type="button" class="app-icon-button" aria-label="{{ __('Notifications') }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></svg>
                                <span class="app-notification-dot"></span>
                            </button>
                            <div class="hidden h-6 w-px bg-slate-200 sm:block"></div>
                            <span class="hidden text-right sm:block"><span class="block text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</span><span class="block text-xs text-slate-500">{{ Auth::user()->role }}</span></span>
                            <span class="app-avatar">@if (Auth::user()->profile_photo_path)<img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="" class="h-full w-full rounded-full object-cover">@else{{ str(Auth::user()->name)->substr(0, 1)->upper() }}@endif</span>
                        </div>
                    </div>
                </header>

                @isset($header)
                    <header class="app-header">
                        <div class="app-container py-7">
                            <p class="app-header-kicker">{{ __('Research intelligence /') }} {{ now()->format('Y') }}</p>
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
