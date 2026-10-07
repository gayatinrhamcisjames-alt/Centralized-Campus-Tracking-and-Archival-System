<x-app-layout>
    <x-slot name="header">
        <div class="research-documents-header flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl leading-tight">{{ request()->routeIs('theses.archive') ? __('Archive Search') : __('Research Projects') }}</h2>
            <div class="flex items-center gap-3">
                <a href="{{ request()->routeIs('theses.archive') ? route('theses.index') : route('theses.archive') }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">{{ request()->routeIs('theses.archive') ? __('Active projects') : __('Archive search') }}</a>
                @can('create', \App\Models\Thesis::class)
                    <a href="{{ route('theses.create') }}" class="inline-flex min-h-10 min-w-28 items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ __('Add thesis') }}</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="research-documents-page py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('status') }}</div>
            @endif
            <form method="GET" class="grid gap-4 rounded-lg bg-white p-6 shadow-sm sm:grid-cols-2 lg:grid-cols-4 dark:bg-gray-800">
                <x-text-input name="search" type="search" placeholder="Search title, abstract, keywords, student, adviser" :value="request('search')" />
                <select name="status" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="">All statuses</option>
                    @foreach (\App\Models\Thesis::statuses() as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <select name="program" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="">All programs</option>
                    @foreach ($filterOptions['programs'] as $program)
                        <option value="{{ $program }}" @selected(request('program') === $program)>{{ $program }}</option>
                    @endforeach
                </select>
                <select name="academic_year" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="">All academic years</option>
                    @foreach ($filterOptions['academicYears'] as $academicYear)
                        <option value="{{ $academicYear }}" @selected(request('academic_year') === $academicYear)>{{ $academicYear }}</option>
                    @endforeach
                </select>
                <select name="student_id" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="">All students</option>
                    @foreach ($filterOptions['students'] as $student)
                        <option value="{{ $student->id }}" @selected((string) request('student_id') === (string) $student->id)>{{ $student->name }}</option>
                    @endforeach
                </select>
                <select name="adviser_id" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="">All advisers</option>
                    @foreach ($filterOptions['advisers'] as $adviser)
                        <option value="{{ $adviser->id }}" @selected((string) request('adviser_id') === (string) $adviser->id)>{{ $adviser->name }}</option>
                    @endforeach
                </select>
                <div class="flex items-center gap-3 lg:col-span-2"><x-primary-button class="min-w-24">{{ __('Search') }}</x-primary-button><a href="{{ request()->routeIs('theses.archive') ? route('theses.archive') : route('theses.index') }}" class="inline-flex min-h-10 min-w-20 items-center justify-center rounded-md border border-gray-300 px-4 text-xs font-semibold uppercase tracking-widest text-gray-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">{{ __('Clear') }}</a></div>
            </form>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="overflow-x-auto p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead><tr class="text-left text-xs uppercase tracking-wider text-gray-500"><th class="pb-3">Title</th><th class="pb-3">Student</th><th class="pb-3">Program</th><th class="pb-3">Status</th><th class="pb-3"></th></tr></thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($theses as $thesis)
                                <tr><td class="py-4 pr-4 font-medium">{{ $thesis->title }}</td><td class="py-4 pr-4">{{ $thesis->student->name }}</td><td class="max-w-48 truncate py-4 pr-4" title="{{ $thesis->program }}">{{ $thesis->program }}</td><td class="py-4 pr-4"><span class="inline-flex min-h-7 items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">{{ $thesis->status }}</span></td><td class="py-4 text-right"><div class="flex justify-end gap-2"><a class="research-documents-action inline-flex min-h-9 min-w-16 items-center justify-center rounded-md border px-3 text-xs font-bold uppercase tracking-wide" href="{{ route('theses.show', $thesis) }}">{{ __('View') }}</a>@can('archive', $thesis) @if ($thesis->is_archived)<form method="POST" action="{{ route('theses.restore', $thesis) }}">@csrf<button class="research-documents-action inline-flex min-h-9 items-center justify-center rounded-md border px-3 text-xs font-bold uppercase tracking-wide" type="submit">{{ __('Restore') }}</button></form>@else<form method="POST" action="{{ route('theses.archive.store', $thesis) }}">@csrf<button class="research-documents-action inline-flex min-h-9 items-center justify-center rounded-md border px-3 text-xs font-bold uppercase tracking-wide" type="submit">{{ __('Archive') }}</button></form>@endif @endcan</div></td></tr>
                            @empty
                                <tr><td colspan="5" class="py-8 text-center text-gray-500">{{ __('No thesis records yet.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-6">{{ $theses->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>