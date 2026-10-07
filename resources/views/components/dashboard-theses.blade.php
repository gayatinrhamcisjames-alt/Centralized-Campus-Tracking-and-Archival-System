@props(['theses'])

<section x-data="{ open: false }" class="thesis-panel">
    <div class="thesis-panel-heading">
        <div>
            <p class="workspace-eyebrow"><span></span>{{ __('Recent activity') }}</p>
            <h3>{{ __('Thesis records') }}</h3>
            <p>{{ __('Track submissions, reviews, approvals, and archived research.') }}</p>
        </div>
        <div class="relative" @click.outside="open = false">
            <button
                type="button"
                @click="open = !open"
                :aria-expanded="open.toString()"
                aria-controls="thesis-actions-menu"
                class="thesis-actions-trigger"
            >
                {{ __('Actions') }}
                <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25-4.51a.75.75 0 01.02-1.06l4.25-4.51a.75.75 0 111.08 1.04L10 11.168l-3.71-3.938a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
            </button>
            <div id="thesis-actions-menu" x-cloak x-show="open" x-transition.origin.top.right class="thesis-actions-menu">
                <a href="{{ route('theses.create') }}" class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">{{ __('Add thesis') }}</a>
                <a href="{{ route('theses.index') }}" class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">{{ __('View all records') }}</a>
            </div>
        </div>
    </div>

    <div class="thesis-table-wrap">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    <th class="pb-3 pr-4">{{ __('Title') }}</th>
                    <th class="pb-3 pr-4">{{ __('Student') }}</th>
                    <th class="w-48 pb-3 pr-4">{{ __('Program') }}</th>
                    <th class="w-40 pb-3 pr-4">{{ __('Status') }}</th>
                    <th class="pb-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($theses as $thesis)
                    <tr>
                        <td class="max-w-xs py-4 pr-4 font-medium text-gray-900 dark:text-gray-100">{{ $thesis->title }}</td>
                        <td class="whitespace-nowrap py-4 pr-4 text-sm text-gray-600 dark:text-gray-300">{{ $thesis->student->name }}</td>
                        <td class="w-48 max-w-48 truncate py-4 pr-4 text-sm text-gray-600 dark:text-gray-300" title="{{ $thesis->program }}">{{ $thesis->program }}</td>
                        <td class="w-40 whitespace-nowrap py-4 pr-4 text-sm">
                            <span class="inline-flex min-h-7 items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">{{ $thesis->status }}</span>
                        </td>
                        <td class="whitespace-nowrap py-4 text-right text-sm">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('theses.show', $thesis) }}" class="inline-flex min-h-9 min-w-16 items-center justify-center rounded-md border border-indigo-200 px-3 text-xs font-semibold uppercase tracking-wide text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-indigo-400/40 dark:text-indigo-300 dark:hover:bg-indigo-900/30">{{ __('View') }}</a>
                                @can('update', $thesis)
                                    <a href="{{ route('theses.edit', $thesis) }}" class="inline-flex min-h-9 min-w-16 items-center justify-center rounded-md border border-gray-200 px-3 text-xs font-semibold uppercase tracking-wide text-gray-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">{{ __('Edit') }}</a>
                                @endcan
                                @can('delete', $thesis)
                                    <form method="POST" action="{{ route('theses.destroy', $thesis) }}" onsubmit="return confirm('{{ __('Delete this thesis record?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex min-h-9 min-w-16 items-center justify-center rounded-md border border-red-200 px-3 text-xs font-semibold uppercase tracking-wide text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-red-400/40 dark:text-red-300 dark:hover:bg-red-900/30">{{ __('Delete') }}</button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No thesis records yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-6">
            <a href="{{ route('theses.index') }}" class="text-sm text-indigo-600 underline hover:text-indigo-500">{{ __('View all thesis records') }}</a>
        </div>
    </div>
</section>