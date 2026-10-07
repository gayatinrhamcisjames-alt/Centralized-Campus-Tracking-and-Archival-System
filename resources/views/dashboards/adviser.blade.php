<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Faculty Adviser Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>{{ __('Welcome Faculty Adviser!') }}</p>
                    <p class="mt-2">{{ __('Role: Faculty Adviser') }}</p>
                </div>
            </div>
            <x-role-workspace
                eyebrow="{{ __('Adviser workspace') }}"
                title="{{ __('Give each project a clear next step.') }}"
                description="{{ __('Open assigned research, review the latest document version, and leave feedback that helps students move forward.') }}"
                :actions="[
                    ['label' => __('View assigned research'), 'url' => route('theses.index'), 'primary' => true],
                    ['label' => __('Open archive'), 'url' => route('theses.archive')],
                ]"
            />
            <x-weather-dashboard :weather="$weather" />
            <x-dashboard-analytics :analytics="$analytics" :metrics="$metrics" />
            <x-dashboard-theses :theses="$theses" />
        </div>
    </div>
</x-app-layout>
