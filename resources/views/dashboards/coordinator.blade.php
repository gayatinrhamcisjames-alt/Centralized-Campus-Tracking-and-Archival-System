<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Thesis Coordinator Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>{{ __('Welcome Thesis Coordinator!') }}</p>
                    <p class="mt-2">{{ __('Role: Thesis Coordinator') }}</p>
                </div>
            </div>
            <x-role-workspace
                eyebrow="{{ __('Workflow control') }}"
                title="{{ __('Coordinate every review milestone.') }}"
                description="{{ __('Find submissions quickly, assign reviewers, and keep status changes visible to the students and faculty involved.') }}"
                :actions="[
                    ['label' => __('Browse submissions'), 'url' => route('theses.index'), 'primary' => true],
                    ['label' => __('Search archive'), 'url' => route('theses.archive')],
                ]"
            />
            <x-weather-dashboard :weather="$weather" />
            <x-dashboard-analytics :analytics="$analytics" :metrics="$metrics" />
            <x-dashboard-theses :theses="$theses" />
        </div>
    </div>
</x-app-layout>
