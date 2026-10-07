<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Administrator Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="admin-command-bar">
                <div class="admin-command-copy">
                    <p class="workspace-eyebrow"><span></span>{{ __('Administrator console') }}</p>
                    <span class="sr-only">{{ __('Welcome Administrator!') }}</span>
                    <span class="sr-only">{{ __('Role: Administrator') }}</span>
                    <h3>{{ __('Good to see you,') }} {{ Auth::user()->name }}.</h3>
                    <p>{{ __('Monitor the research operation, approvals, and people in one focused view.') }}</p>
                </div>
                <div class="admin-command-meta"><span class="admin-command-date">{{ now()->format('D, M j') }}</span><span class="admin-command-status"><i></i>{{ __('System healthy') }}</span></div>
                <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                        <button
                            type="button"
                            @click="open = !open"
                            :aria-expanded="open.toString()"
                            aria-controls="administrator-actions-menu"
                            class="admin-menu-trigger"
                        >
                            {{ __('Admin menu') }}
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="administrator-actions-menu" x-cloak x-show="open" x-transition.origin.top.right class="admin-menu-panel">
                            <a href="{{ route('profile.edit') }}" class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-gray-200 dark:hover:bg-gray-700">{{ __('Profile') }}</a>
                            <a href="{{ route('users.index') }}" class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-gray-200 dark:hover:bg-gray-700">{{ __('Manage users') }}</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block min-h-10 w-full rounded-md px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-red-300 dark:hover:bg-red-900/30">{{ __('Log Out') }}</button>
                            </form>
                        </div>
                </div>
            </div>
            <x-role-workspace
                eyebrow="{{ __('System control') }}"
                title="{{ __('Keep the research operation moving.') }}"
                description="{{ __('Review activity, manage accounts, and keep every capstone record discoverable from one controlled workspace.') }}"
                :actions="[
                    ['label' => __('Manage users'), 'url' => route('users.index'), 'primary' => true],
                    ['label' => __('Search archive'), 'url' => route('theses.archive')],
                    ['label' => __('All projects'), 'url' => route('theses.index')],
                ]"
            />
            <x-weather-dashboard :weather="$weather" :show-integration-details="true" />
            <x-dashboard-analytics :analytics="$analytics" :metrics="$metrics" />
            <x-dashboard-theses :theses="$theses" />

            <section x-data="{ open: false }" class="directory-panel">
                <div class="directory-heading">
                    <div>
                        <p class="workspace-eyebrow"><span></span>{{ __('People and access') }}</p>
                        <h3>{{ __('Workplace directory') }}</h3>
                        <p>{{ __('Administrator-only view of staff and student workplace assignments.') }}</p>
                    </div>
                    <button
                        type="button"
                        @click="open = !open"
                        :aria-expanded="open.toString()"
                        aria-controls="workplace-directory-content"
                        class="directory-toggle"
                    >
                        <span x-text="open ? '{{ __('Hide directory') }}' : '{{ __('Show directory') }}'"></span>
                        <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div id="workplace-directory-content" x-cloak x-show="open" x-transition.opacity.duration.200ms class="directory-content">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                <th class="pb-3 pr-4">{{ __('Name') }}</th>
                                <th class="pb-3 pr-4">{{ __('Role') }}</th>
                                <th class="pb-3 pr-4">{{ __('Email') }}</th>
                                <th class="pb-3">{{ __('Workplace / Location') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($workplaceUsers as $workplaceUser)
                                <tr>
                                    <td class="py-4 pr-4 font-medium text-gray-900 dark:text-gray-100">{{ $workplaceUser->name }}</td>
                                    <td class="py-4 pr-4 text-sm text-gray-600 dark:text-gray-300">{{ $workplaceUser->role }}</td>
                                    <td class="py-4 pr-4 text-sm text-gray-600 dark:text-gray-300">{{ $workplaceUser->email }}</td>
                                    <td class="py-4 text-sm text-gray-600 dark:text-gray-300">{{ $workplaceUser->workplace ?: __('Not assigned') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No workplace assignments yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

</x-app-layout>