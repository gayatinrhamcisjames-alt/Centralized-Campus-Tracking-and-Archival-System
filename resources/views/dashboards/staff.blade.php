<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Staff Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>{{ __('Welcome :role!', ['role' => auth()->user()->role]) }}</p>
                    <p class="mt-2">{{ __('Role: :role', ['role' => auth()->user()->role]) }}</p>
                </div>
            </div>
            <x-role-workspace
                eyebrow="{{ __('Operations desk') }}"
                title="{{ __('Review the queue and keep records current.') }}"
                description="{{ __('Search active submissions, inspect project details, and move completed research into the archive when it is ready.') }}"
                :actions="[
                    ['label' => __('Open project queue'), 'url' => route('theses.index'), 'primary' => true],
                    ['label' => __('Archive search'), 'url' => route('theses.archive')],
                ]"
            />
            <x-weather-dashboard :weather="$weather" />
            <x-dashboard-analytics :analytics="$analytics" :metrics="$metrics" />
            <x-dashboard-theses :theses="$theses" />
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @csrf
        <x-primary-button>{{ __('Logout') }}</x-primary-button>
    </form>
</x-app-layout>