<x-app-layout>
    <x-slot name="header">
        <h2 class="student-dashboard-title font-semibold text-xl leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>{{ __('Welcome Student!') }}</p>
                    <p class="mt-2">{{ __('Role: Student') }}</p>
                </div>
            </div>
            <x-role-workspace
                eyebrow="{{ __('Student workspace') }}"
                title="{{ __('Turn your research plan into progress.') }}"
                description="{{ __('Submit a proposal, follow its status, and upload the next document version when feedback asks for a revision.') }}"
                :actions="[
                    ['label' => __('Submit a proposal'), 'url' => route('theses.create'), 'primary' => true],
                    ['label' => __('View my research'), 'url' => route('theses.index')],
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