<aside class="app-sidebar" :class="{ 'is-open': sidebarOpen }" aria-label="{{ __('Primary navigation') }}">
    <div class="app-sidebar-inner">
        <div class="app-sidebar-brand">
            <a href="{{ route('dashboard') }}" class="app-brand" aria-label="{{ config('app.name', 'CCTAS') }}">
                <span class="app-logo-mark"><x-application-logo class="h-6 w-6 fill-current" /></span>
                <span class="app-brand-copy"><span class="text-sm font-bold tracking-tight text-white">{{ config('app.name', 'CCTAS') }}</span><span class="app-brand-subtitle">{{ __('Centralized Campus Tracking and Archival System') }}</span></span>
            </a>
            <button type="button" @click="sidebarOpen = false" class="app-sidebar-close" aria-label="{{ __('Close navigation') }}">&times;</button>
        </div>
        <p class="app-nav-label">{{ __('Main menu') }}</p>
        <div class="app-sidebar-links">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')"><span class="app-nav-icon">⌂</span>{{ __('Dashboard') }}</x-nav-link>
            <x-nav-link :href="route('theses.index')" :active="request()->routeIs('theses.*')"><span class="app-nav-icon">◫</span>{{ __('Research & Documents') }}</x-nav-link>
            <x-nav-link :href="route('workflow.index')" :active="request()->routeIs('workflow.*')"><span class="app-nav-icon">⌁</span>{{ __('Workflow & Approvals') }}<span class="app-nav-badge">{{ __('Live') }}</span></x-nav-link>
            <x-nav-link :href="route('analytics.index')" :active="request()->routeIs('analytics.*')"><span class="app-nav-icon">↗</span>{{ __('Analytics') }}</x-nav-link>
            @if (Auth::user()->role === \App\Models\User::ADMINISTRATOR)
                <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')"><span class="app-nav-icon">◎</span>{{ __('User Management') }}</x-nav-link>
            @endif
        </div>
        <p class="app-nav-label app-nav-label-spaced">{{ __('System') }}</p>
        <div class="app-sidebar-links">
            <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')"><span class="app-nav-icon">◉</span>{{ __('Settings & Profile') }}</x-nav-link>
            <x-nav-link :href="route('about.index')" :active="request()->routeIs('about.*')"><span class="app-nav-icon">◎</span>{{ __('About Center') }}</x-nav-link>
            <x-nav-link :href="route('support.index')" :active="request()->routeIs('support.*')"><span class="app-nav-icon">?</span>{{ __('Help & Support') }}</x-nav-link>
            @if (Auth::user()->role === \App\Models\User::ADMINISTRATOR)
                <x-nav-link :href="route('support.admin.index')" :active="request()->routeIs('support.admin.*')"><span class="app-nav-icon">▤</span>{{ __('Help Center Admin') }}</x-nav-link>
            @endif
        </div>
        <div class="app-sidebar-footer">
            <div class="app-sidebar-user"><span class="app-sidebar-avatar">@if (Auth::user()->profile_photo_path)<img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="" class="h-full w-full rounded-full object-cover">@else{{ str(Auth::user()->name)->substr(0, 1)->upper() }}@endif</span><span class="min-w-0"><span class="block truncate text-sm font-semibold text-white">{{ Auth::user()->name }}</span><span class="block truncate text-xs text-slate-400">{{ Auth::user()->email }}</span></span></div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="app-sidebar-logout"><span>↪</span>{{ __('Log out') }}</button>
            </form>
        </div>
    </div>
</aside>
<div class="app-sidebar-backdrop" :class="{ 'is-visible': sidebarOpen }" @click="sidebarOpen = false"></div>
