<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-indigo-600">{{ __('Research Intelligence / 2026') }}</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">{{ __('Help Center administration') }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ __('Manage published guides and triage incoming support requests.') }}</p>
    </x-slot>

    <div class="app-container space-y-8 py-8">
        @if (session('status'))
            <p class="rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800" role="status">{{ session('status') }}</p>
        @endif
        @if (session('error'))
            <p class="rounded-xl bg-rose-50 p-4 text-sm font-semibold text-rose-800" role="alert">{{ session('error') }}</p>
        @endif
        @if ($errors->any())
            <div class="rounded-xl bg-rose-50 p-4 text-sm text-rose-800" role="alert">
                <p class="font-semibold">{{ __('Please review the highlighted form values and try again.') }}</p>
                <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ([['Total guides', $metrics['articles']], ['Published guides', $metrics['published']], ['Guide views', $metrics['views']], ['Helpful feedback', $metrics['helpful'].'%'], ['Open tickets', $metrics['open_tickets']], ['Open issues', $metrics['open_issues']]] as [$label, $value])
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">{{ __($label) }}</p><p class="mt-2 text-3xl font-bold text-slate-950">{{ $value }}</p></div>
            @endforeach
        </section>

        <section class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <div>
                <h2 class="text-xl font-bold text-slate-950">{{ __('Guide categories') }}</h2>
                <p class="mt-1 text-sm text-slate-600">{{ __('Organize the published knowledge base into clear topics.') }}</p>
            </div>
            <form method="POST" action="{{ route('support.admin.categories.store') }}" class="grid gap-4 rounded-xl bg-slate-50 p-4 sm:grid-cols-2 xl:grid-cols-5">
                @csrf
                <div><label for="category-name-new" class="mb-1.5 block text-sm font-medium">{{ __('Name') }}</label><input id="category-name-new" name="name" required maxlength="120" class="w-full rounded-xl border-slate-300 text-sm"></div>
                <div><label for="category-slug-new" class="mb-1.5 block text-sm font-medium">{{ __('URL slug (optional)') }}</label><input id="category-slug-new" name="slug" maxlength="140" class="w-full rounded-xl border-slate-300 text-sm"></div>
                <div><label for="category-icon-new" class="mb-1.5 block text-sm font-medium">{{ __('Icon key') }}</label><input id="category-icon-new" name="icon" value="book-open" required maxlength="32" class="w-full rounded-xl border-slate-300 text-sm"></div>
                <div><label for="category-order-new" class="mb-1.5 block text-sm font-medium">{{ __('Display order') }}</label><input id="category-order-new" type="number" name="sort_order" value="10" min="0" max="65535" required class="w-full rounded-xl border-slate-300 text-sm"></div>
                <div class="xl:row-span-2"><label for="category-description-new" class="mb-1.5 block text-sm font-medium">{{ __('Description') }}</label><textarea id="category-description-new" name="description" rows="4" required maxlength="500" class="w-full rounded-xl border-slate-300 text-sm"></textarea><button type="submit" class="mt-3 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white">{{ __('Add category') }}</button></div>
            </form>
            <div class="grid gap-3 md:grid-cols-2">
                @foreach ($categories as $category)
                    <form method="POST" action="{{ route('support.admin.categories.update', $category) }}" class="grid gap-3 rounded-xl border border-slate-200 p-4 sm:grid-cols-2">
                        @csrf
                        @method('PUT')
                        <div><label for="category-name-{{ $category->id }}" class="mb-1.5 block text-xs font-medium">{{ __('Name') }}</label><input id="category-name-{{ $category->id }}" name="name" value="{{ $category->name }}" required maxlength="120" class="w-full rounded-lg border-slate-300 text-sm"></div>
                        <div><label for="category-slug-{{ $category->id }}" class="mb-1.5 block text-xs font-medium">{{ __('Slug') }}</label><input id="category-slug-{{ $category->id }}" name="slug" value="{{ $category->slug }}" required maxlength="140" class="w-full rounded-lg border-slate-300 text-sm"></div>
                        <div><label for="category-icon-{{ $category->id }}" class="mb-1.5 block text-xs font-medium">{{ __('Icon key') }}</label><input id="category-icon-{{ $category->id }}" name="icon" value="{{ $category->icon }}" required maxlength="32" class="w-full rounded-lg border-slate-300 text-sm"></div>
                        <div><label for="category-order-{{ $category->id }}" class="mb-1.5 block text-xs font-medium">{{ __('Display order') }}</label><input id="category-order-{{ $category->id }}" type="number" name="sort_order" value="{{ $category->sort_order }}" min="0" max="65535" required class="w-full rounded-lg border-slate-300 text-sm"></div>
                        <div class="sm:col-span-2"><label for="category-description-{{ $category->id }}" class="mb-1.5 block text-xs font-medium">{{ __('Description') }}</label><textarea id="category-description-{{ $category->id }}" name="description" rows="2" required maxlength="500" class="w-full rounded-lg border-slate-300 text-sm">{{ $category->description }}</textarea></div>
                        <div class="flex flex-wrap items-center justify-between gap-3 sm:col-span-2"><span class="text-xs text-slate-500">{{ $category->articles_count }} {{ __('guides') }}</span><div class="flex items-center gap-4"><button type="submit" class="text-sm font-semibold text-indigo-700">{{ __('Save category') }}</button><button type="submit" form="delete-category-{{ $category->id }}" class="text-sm font-semibold text-rose-700">{{ __('Delete') }}</button></div></div>
                    </form>
                    <form id="delete-category-{{ $category->id }}" method="POST" action="{{ route('support.admin.categories.destroy', $category) }}" onsubmit="return confirm('{{ __('Delete this empty category?') }}')">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <h2 class="text-xl font-bold text-slate-950">{{ __('Create a guide') }}</h2>
            <form method="POST" action="{{ route('support.admin.articles.store') }}" class="mt-5 grid gap-4 sm:grid-cols-2">
                @csrf
                <div><label for="new-category" class="mb-1.5 block text-sm font-medium">{{ __('Category') }}</label><select id="new-category" name="help_category_id" required class="w-full rounded-xl border-slate-300 text-sm">@foreach ($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div>
                <div><label for="new-title" class="mb-1.5 block text-sm font-medium">{{ __('Title') }}</label><input id="new-title" name="title" required maxlength="180" class="w-full rounded-xl border-slate-300 text-sm"></div>
                <div><label for="new-slug" class="mb-1.5 block text-sm font-medium">{{ __('URL slug (optional)') }}</label><input id="new-slug" name="slug" maxlength="180" class="w-full rounded-xl border-slate-300 text-sm"></div>
                <div><label for="new-excerpt" class="mb-1.5 block text-sm font-medium">{{ __('Short summary') }}</label><input id="new-excerpt" name="excerpt" required maxlength="500" class="w-full rounded-xl border-slate-300 text-sm"></div>
                <div class="sm:col-span-2"><label for="new-content" class="mb-1.5 block text-sm font-medium">{{ __('Guide content') }}</label><textarea id="new-content" name="content" rows="8" required maxlength="30000" class="w-full rounded-xl border-slate-300 text-sm"></textarea></div>
                <fieldset class="sm:col-span-2"><legend class="mb-2 text-sm font-medium">{{ __('Limit to roles (leave unchecked for all roles)') }}</legend><div class="flex flex-wrap gap-3">@foreach (\App\Models\User::roles() as $role)<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="role_scope[]" value="{{ $role }}" class="rounded border-slate-300 text-indigo-600">{{ $role }}</label>@endforeach</div></fieldset>
                <label class="inline-flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="is_published" value="1" class="rounded border-slate-300 text-indigo-600">{{ __('Publish immediately') }}</label>
                <div><button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-700">{{ __('Save guide') }}</button></div>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5"><h2 class="text-xl font-bold text-slate-950">{{ __('Guides') }}</h2></div>
            <div class="divide-y divide-slate-100">
                @foreach ($articles as $article)
                    <details class="p-5">
                        <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-3 font-semibold text-slate-900"><span>{{ $article->title }} <span class="ml-2 text-xs font-medium text-slate-500">{{ $article->category->name }} · {{ $article->is_published ? __('Published') : __('Draft') }}</span></span><span class="text-xs text-slate-500">{{ $article->views }} {{ __('views') }}</span></summary>
                        <form method="POST" action="{{ route('support.admin.articles.update', $article) }}" class="mt-5 grid gap-4 rounded-xl bg-slate-50 p-4 sm:grid-cols-2">
                            @csrf
                            @method('PUT')
                            <div><label for="category-{{ $article->id }}" class="mb-1.5 block text-sm font-medium">{{ __('Category') }}</label><select id="category-{{ $article->id }}" name="help_category_id" class="w-full rounded-xl border-slate-300 text-sm">@foreach ($categories as $category)<option value="{{ $category->id }}" @selected($article->help_category_id === $category->id)>{{ $category->name }}</option>@endforeach</select></div>
                            <div><label for="title-{{ $article->id }}" class="mb-1.5 block text-sm font-medium">{{ __('Title') }}</label><input id="title-{{ $article->id }}" name="title" value="{{ $article->title }}" required maxlength="180" class="w-full rounded-xl border-slate-300 text-sm"></div>
                            <div><label for="slug-{{ $article->id }}" class="mb-1.5 block text-sm font-medium">{{ __('URL slug') }}</label><input id="slug-{{ $article->id }}" name="slug" value="{{ $article->slug }}" required maxlength="180" class="w-full rounded-xl border-slate-300 text-sm"></div>
                            <div><label for="excerpt-{{ $article->id }}" class="mb-1.5 block text-sm font-medium">{{ __('Short summary') }}</label><input id="excerpt-{{ $article->id }}" name="excerpt" value="{{ $article->excerpt }}" required maxlength="500" class="w-full rounded-xl border-slate-300 text-sm"></div>
                            <div class="sm:col-span-2"><label for="content-{{ $article->id }}" class="mb-1.5 block text-sm font-medium">{{ __('Guide content') }}</label><textarea id="content-{{ $article->id }}" name="content" rows="7" required maxlength="30000" class="w-full rounded-xl border-slate-300 text-sm">{{ $article->content }}</textarea></div>
                            <fieldset class="sm:col-span-2"><legend class="mb-2 text-sm font-medium">{{ __('Visible to roles') }}</legend><div class="flex flex-wrap gap-3">@foreach (\App\Models\User::roles() as $role)<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="role_scope[]" value="{{ $role }}" @checked(in_array($role, $article->role_scope ?? [], true)) class="rounded border-slate-300 text-indigo-600">{{ $role }}</label>@endforeach</div><p class="mt-1 text-xs text-slate-500">{{ __('Leave all unchecked to show this guide to every role.') }}</p></fieldset>
                            <label class="inline-flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="is_published" value="1" @checked($article->is_published) class="rounded border-slate-300 text-indigo-600">{{ __('Published') }}</label>
                            <div class="flex flex-wrap gap-3"><button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white">{{ __('Save changes') }}</button></div>
                        </form>
                        <form method="POST" action="{{ route('support.admin.articles.destroy', $article) }}" class="mt-3" onsubmit="return confirm('{{ __('Delete this guide?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-semibold text-rose-700 hover:text-rose-900">{{ __('Delete guide') }}</button>
                        </form>
                    </details>
                @endforeach
            </div>
            <div class="p-5">{{ $articles->links() }}</div>
        </section>

        <section class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <div>
                <h2 class="text-xl font-bold text-slate-950">{{ __('Frequently asked questions') }}</h2>
                <p class="mt-1 text-sm text-slate-600">{{ __('Create, edit, reorder, publish, or remove the Help Center FAQ entries.') }}</p>
            </div>
            <form method="POST" action="{{ route('support.admin.faqs.store') }}" class="grid gap-4 rounded-xl bg-slate-50 p-4 sm:grid-cols-2">
                @csrf
                <div><label for="faq-question-new" class="mb-1.5 block text-sm font-medium">{{ __('Question') }}</label><input id="faq-question-new" name="question" required maxlength="255" class="w-full rounded-xl border-slate-300 text-sm"></div>
                <div><label for="faq-sort-new" class="mb-1.5 block text-sm font-medium">{{ __('Display order') }}</label><input id="faq-sort-new" type="number" name="sort_order" min="0" max="65535" value="10" required class="w-full rounded-xl border-slate-300 text-sm"></div>
                <div class="sm:col-span-2"><label for="faq-answer-new" class="mb-1.5 block text-sm font-medium">{{ __('Answer') }}</label><textarea id="faq-answer-new" name="answer" rows="4" maxlength="5000" required class="w-full rounded-xl border-slate-300 text-sm"></textarea></div>
                <label class="inline-flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="is_published" value="1" class="rounded border-slate-300 text-indigo-600">{{ __('Publish immediately') }}</label>
                <div><button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white">{{ __('Add FAQ') }}</button></div>
            </form>
            <div class="divide-y divide-slate-100">
                @foreach ($faqs as $faq)
                    <details class="py-4">
                        <summary class="cursor-pointer font-semibold text-slate-900">{{ $faq->question }} <span class="ml-2 text-xs font-medium text-slate-500">{{ $faq->is_published ? __('Published') : __('Draft') }} · {{ __('Order') }} {{ $faq->sort_order }}</span></summary>
                        <form method="POST" action="{{ route('support.admin.faqs.update', $faq) }}" class="mt-4 grid gap-4 rounded-xl bg-slate-50 p-4 sm:grid-cols-2">
                            @csrf
                            @method('PUT')
                            <div><label for="faq-question-{{ $faq->id }}" class="mb-1.5 block text-sm font-medium">{{ __('Question') }}</label><input id="faq-question-{{ $faq->id }}" name="question" value="{{ $faq->question }}" required maxlength="255" class="w-full rounded-xl border-slate-300 text-sm"></div>
                            <div><label for="faq-sort-{{ $faq->id }}" class="mb-1.5 block text-sm font-medium">{{ __('Display order') }}</label><input id="faq-sort-{{ $faq->id }}" type="number" name="sort_order" min="0" max="65535" value="{{ $faq->sort_order }}" required class="w-full rounded-xl border-slate-300 text-sm"></div>
                            <div class="sm:col-span-2"><label for="faq-answer-{{ $faq->id }}" class="mb-1.5 block text-sm font-medium">{{ __('Answer') }}</label><textarea id="faq-answer-{{ $faq->id }}" name="answer" rows="4" maxlength="5000" required class="w-full rounded-xl border-slate-300 text-sm">{{ $faq->answer }}</textarea></div>
                            <label class="inline-flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="is_published" value="1" @checked($faq->is_published) class="rounded border-slate-300 text-indigo-600">{{ __('Published') }}</label>
                            <div class="flex gap-4"><button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white">{{ __('Save FAQ') }}</button></div>
                        </form>
                        <form method="POST" action="{{ route('support.admin.faqs.destroy', $faq) }}" class="mt-3" onsubmit="return confirm('{{ __('Delete this FAQ?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-semibold text-rose-700">{{ __('Delete FAQ') }}</button>
                        </form>
                    </details>
                @endforeach
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-5"><h2 class="text-lg font-bold text-slate-950">{{ __('Recent support tickets') }}</h2></div>
                <div class="divide-y divide-slate-100">
                    @forelse ($tickets as $ticket)
                        <article class="p-5"><div class="flex flex-wrap justify-between gap-3"><div><p class="font-semibold text-slate-900">{{ $ticket->ticket_number }} · {{ $ticket->subject }}</p><p class="mt-1 text-xs text-slate-500">{{ $ticket->user->name }} · {{ $ticket->priority }} · {{ $ticket->category }}</p></div>@if ($ticket->attachment_path)<a href="{{ route('support.admin.attachments.show', ['type' => 'ticket', 'id' => $ticket->id]) }}" class="text-sm font-semibold text-indigo-700">{{ __('Download attachment') }}</a>@endif</div><p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $ticket->description }}</p><form method="POST" action="{{ route('support.admin.tickets.update', $ticket) }}" class="mt-3 flex gap-2">@csrf @method('PATCH')<label class="sr-only" for="ticket-status-{{ $ticket->id }}">{{ __('Ticket status') }}</label><select id="ticket-status-{{ $ticket->id }}" name="status" class="rounded-lg border-slate-300 text-sm">@foreach (['Open', 'In Progress', 'Resolved', 'Closed'] as $status)<option @selected($ticket->status === $status)>{{ $status }}</option>@endforeach</select><button class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white">{{ __('Update') }}</button></form></article>
                    @empty
                        <p class="p-5 text-sm text-slate-500">{{ __('No support tickets have been submitted.') }}</p>
                    @endforelse
                </div>
            </div>
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-5"><h2 class="text-lg font-bold text-slate-950">{{ __('Recent issue reports') }}</h2></div>
                <div class="divide-y divide-slate-100">
                    @forelse ($issues as $issue)
                        <article class="p-5"><div class="flex flex-wrap justify-between gap-3"><div><p class="font-semibold text-slate-900">{{ $issue->category }} · {{ $issue->severity }}</p><p class="mt-1 text-xs text-slate-500">{{ $issue->user->name }} · {{ $issue->created_at->format('M j, Y') }}</p></div>@if ($issue->screenshot_path)<a href="{{ route('support.admin.attachments.show', ['type' => 'issue', 'id' => $issue->id]) }}" class="text-sm font-semibold text-indigo-700">{{ __('View screenshot') }}</a>@endif</div><p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $issue->description }}</p>@if ($issue->reproduction_steps)<p class="mt-2 whitespace-pre-line text-xs text-slate-500"><strong>{{ __('Reproduction steps:') }}</strong> {{ $issue->reproduction_steps }}</p>@endif<form method="POST" action="{{ route('support.admin.issues.update', $issue) }}" class="mt-3 flex gap-2">@csrf @method('PATCH')<label class="sr-only" for="issue-status-{{ $issue->id }}">{{ __('Issue status') }}</label><select id="issue-status-{{ $issue->id }}" name="status" class="rounded-lg border-slate-300 text-sm">@foreach (['Open', 'In Progress', 'Resolved', 'Closed'] as $status)<option @selected($issue->status === $status)>{{ $status }}</option>@endforeach</select><button class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white">{{ __('Update') }}</button></form></article>
                    @empty
                        <p class="p-5 text-sm text-slate-500">{{ __('No issue reports have been submitted.') }}</p>
                    @endforelse
                </div>
            </div>
        </section>
        <a href="{{ route('support.index') }}" class="inline-flex font-semibold text-indigo-700 hover:text-indigo-900">← {{ __('Back to Help Center') }}</a>
    </div>
</x-app-layout>
