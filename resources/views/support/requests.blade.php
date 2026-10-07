<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-indigo-600">{{ __('Research Intelligence / 2026') }}</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">{{ __('My support requests') }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ __('Only requests submitted from your account are shown here.') }}</p>
    </x-slot>

    <div class="app-container space-y-8 py-8">
        @if (session('ticket-number'))
            <p class="rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800" role="status">{{ __('Request submitted. Ticket') }} #{{ session('ticket-number') }}</p>
        @endif
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5"><h2 class="text-lg font-bold text-slate-950">{{ __('Support tickets') }}</h2></div>
            @if ($tickets->isEmpty())
                <div class="p-8 text-center"><h3 class="font-semibold text-slate-900">{{ __('No support requests yet') }}</h3><p class="mt-2 text-sm text-slate-600">{{ __('Submitted requests will appear here.') }}</p><a href="{{ route('support.index') }}#contact-support" class="mt-4 inline-flex font-semibold text-indigo-700">{{ __('Contact support') }} →</a></div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[42rem] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3">{{ __('Ticket') }}</th><th class="px-5 py-3">{{ __('Subject') }}</th><th class="px-5 py-3">{{ __('Category') }}</th><th class="px-5 py-3">{{ __('Priority') }}</th><th class="px-5 py-3">{{ __('Status') }}</th><th class="px-5 py-3">{{ __('Submitted') }}</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($tickets as $ticket)
                                <tr><td class="px-5 py-4 font-semibold text-slate-800">{{ $ticket->ticket_number }}</td><td class="px-5 py-4 text-slate-700">{{ $ticket->subject }}</td><td class="px-5 py-4 text-slate-600">{{ $ticket->category }}</td><td class="px-5 py-4 text-slate-600">{{ $ticket->priority }}</td><td class="px-5 py-4"><span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">{{ $ticket->status }}</span></td><td class="px-5 py-4 text-slate-500">{{ $ticket->created_at->format('M j, Y') }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-5">{{ $tickets->links() }}</div>
            @endif
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5"><h2 class="text-lg font-bold text-slate-950">{{ __('Issue reports') }}</h2></div>
            @if ($issues->isEmpty())
                <div class="p-8 text-center"><h3 class="font-semibold text-slate-900">{{ __('No issue reports yet') }}</h3><p class="mt-2 text-sm text-slate-600">{{ __('Reported issues will appear here.') }}</p></div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($issues as $issue)
                        <li class="flex flex-col gap-2 p-5 sm:flex-row sm:items-center sm:justify-between"><div><p class="font-semibold text-slate-900">{{ $issue->category }} · {{ $issue->severity }}</p><p class="mt-1 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($issue->description, 180) }}</p><p class="mt-1 text-xs text-slate-500">{{ $issue->created_at->format('M j, Y') }}</p></div><span class="w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $issue->status }}</span></li>
                    @endforeach
                </ul>
            @endif
        </section>
        <a href="{{ route('support.index') }}" class="inline-flex font-semibold text-indigo-700 hover:text-indigo-900">← {{ __('Back to Help Center') }}</a>
    </div>
</x-app-layout>
