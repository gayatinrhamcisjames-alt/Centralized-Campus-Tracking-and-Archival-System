<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <span aria-hidden="true" class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-950 text-sm font-extrabold tracking-tight text-white shadow-sm">RI</span>
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-indigo-600">{{ __('Research Intelligence') }} <span class="ml-1 rounded-full bg-indigo-50 px-2 py-1 text-indigo-700">2026</span></p>
                </div>
                <h1 class="mt-3 font-sans text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">{{ __('Friendly guidance, when you need it.') }}</h1>
                <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">{{ __('Find answers, explore guides, or contact support for a clear next step.') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="#contact-support" class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{ __('Contact support') }}</a>
                <a href="#report-issue" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-indigo-300 hover:text-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{ __('Report an issue') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="app-container space-y-12 py-8 sm:py-10" x-data="helpCenterSearch()">
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
            <form action="{{ route('support.index') }}" method="GET" role="search">
                <label for="help-query" class="mb-3 block text-sm font-semibold text-slate-800">{{ __('Search the knowledge base') }}</label>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-xl text-slate-400" aria-hidden="true">⌕</span>
                        <input id="help-query" name="q" type="search" value="{{ $searchQuery }}" x-ref="search" x-model="query" @input.debounce.250ms="searchForGuides()" @keydown.escape="suggestionsOpen = false" @keydown.arrow-down.prevent="focusFirstSuggestion()" @focus="suggestionsOpen = true" autocomplete="off" placeholder="{{ __('Search for help, guides, or common questions...') }}" class="w-full rounded-2xl border border-slate-300 bg-slate-50 py-4 pl-12 pr-4 text-base text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500" aria-describedby="search-help" aria-controls="help-suggestions" :aria-expanded="suggestionsOpen.toString()">
                        <div id="help-suggestions" x-cloak x-show="suggestionsOpen && !query" @click.outside="suggestionsOpen = false" class="absolute left-0 right-0 top-full z-20 mt-2 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl">
                            <p class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Popular searches') }}</p>
                            @foreach (['How do I submit a research document?', 'How does approval work?', 'How can I request a revision?', 'How do I upload a PDF?', 'How do I change my profile?', 'How do administrators manage roles?'] as $suggestion)
                                <button type="submit" name="q" value="{{ $suggestion }}" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600">{{ $suggestion }}</button>
                            @endforeach
                        </div>
                        <div x-cloak x-show="suggestionsOpen && query.trim().length >= 2" @click.outside="suggestionsOpen = false" class="absolute left-0 right-0 top-full z-20 mt-2 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl" aria-live="polite">
                            <p x-show="searching" class="px-3 py-2 text-sm text-slate-500">{{ __('Searching guides...') }}</p>
                            <p x-show="searchError" x-text="searchError" class="px-3 py-2 text-sm text-rose-700"></p>
                            <p x-show="!searching && !searchError && suggestions.length === 0" class="px-3 py-2 text-sm text-slate-600">{{ __('No matching guides found. Submit to search the complete knowledge base.') }}</p>
                            <ul x-ref="suggestions" x-show="suggestions.length > 0" class="space-y-1">
                                <template x-for="suggestion in suggestions" :key="suggestion.url">
                                    <li><a :href="suggestion.url" class="block rounded-xl px-3 py-2 hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600"><span class="block text-sm font-semibold text-slate-900" x-text="suggestion.title"></span><span class="mt-1 block text-xs text-slate-500"><span x-text="suggestion.category"></span> · <span x-text="suggestion.reading_time"></span> {{ __('min read') }}</span><span class="mt-1 block text-xs leading-5 text-slate-600" x-text="suggestion.excerpt"></span></a></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    <button type="submit" class="rounded-2xl bg-indigo-600 px-6 py-4 text-sm font-bold text-white transition hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{ __('Search guides') }}</button>
                </div>
                <p id="search-help" class="mt-3 text-xs text-slate-500">{{ __('Search titles and guide content. Press Ctrl+K or ⌘+K to focus search.') }}</p>
            </form>
            <div class="mt-5 flex flex-wrap gap-2" aria-label="{{ __('Filter by category') }}">
                <a href="{{ route('support.index', array_filter(['q' => $searchQuery])) }}" class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $selectedCategory === '' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">{{ __('All topics') }}</a>
                @foreach ($categories as $category)
                    <a href="{{ route('support.index', array_filter(['q' => $searchQuery, 'category' => $category->slug])) }}" class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $selectedCategory === $category->slug ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">{{ $category->name }}</a>
                @endforeach
            </div>
        </section>

        @if ($searchQuery !== '')
            <p class="-mb-8 text-sm text-slate-600">{{ trans_choice(':count result found|:count results found', $searchResultCount, ['count' => $searchResultCount]) }} {{ __('for') }} <strong class="text-slate-900">“{{ $searchQuery }}”</strong></p>
        @endif

        <section aria-labelledby="explore-title">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.18em] text-indigo-600">{{ __('Explore the workspace') }}</p>
                    <h2 id="explore-title" class="mt-2 text-2xl font-bold tracking-tight text-slate-950">{{ __('Find the right place to start.') }}</h2>
                </div>
                <a href="#knowledge-base" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">{{ __('Browse all guides') }} <span aria-hidden="true">→</span></a>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($categories as $category)
                    <a href="{{ route('support.index', ['category' => $category->slug]) }}" class="group flex min-h-48 flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-lg text-indigo-700" aria-hidden="true">{{ ['getting-started' => '📚', 'research-documents' => '📄', 'workflow-approvals' => '🔄', 'users-roles' => '👥', 'analytics' => '📊', 'account-settings' => '⚙', 'security-privacy' => '🔐', 'troubleshooting' => '🛠'][$category->slug] ?? '▤' }}</span>
                        <h3 class="mt-4 font-semibold text-slate-950 group-hover:text-indigo-700">{{ $category->name }}</h3>
                        <p class="mt-2 flex-1 text-sm leading-6 text-slate-600">{{ $category->description }}</p>
                        <span class="mt-4 flex items-center justify-between text-xs font-semibold text-slate-500"><span>{{ trans_choice(':count article|:count articles', $category->articles_count, ['count' => $category->articles_count]) }}</span><span class="text-indigo-600" aria-hidden="true">→</span></span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <div class="rounded-3xl border border-indigo-100 bg-gradient-to-br from-indigo-50 via-white to-white p-6 sm:p-8">
                <p class="text-xs font-bold uppercase tracking-[.18em] text-indigo-600">{{ __('Welcome back') }}</p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">{{ auth()->user()->name }}</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ __('These guides are selected for your') }} {{ auth()->user()->role }} {{ __('workspace. Start with the topic closest to your next step.') }}</p>
                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                    @foreach ($recommendedArticles as $article)
                        <a href="{{ route('support.articles.show', $article) }}" class="rounded-xl border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-800 transition hover:border-indigo-300 hover:text-indigo-700">{{ $article->title }}<span class="mt-2 block text-xs font-normal text-slate-500">{{ $article->category->name }}</span></a>
                    @endforeach
                </div>
            </div>
            <div class="flex flex-col justify-between rounded-3xl bg-slate-950 p-6 text-white">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.18em] text-indigo-200">{{ __('Your workspace') }}</p>
                    @if ($myResearchCount !== null)
                        <p class="mt-3 text-3xl font-bold">{{ $myResearchCount }}</p>
                        <p class="mt-1 text-sm text-slate-300">{{ trans_choice('research record associated with your account|research records associated with your account', $myResearchCount, ['count' => $myResearchCount]) }}</p>
                    @else
                        <p class="mt-3 text-lg font-bold">{{ __('Role-aware guidance') }}</p>
                        <p class="mt-1 text-sm leading-6 text-slate-300">{{ __('Your guides and workspace actions follow your assigned CCTAS role.') }}</p>
                    @endif
                </div>
                <a href="{{ route('theses.index') }}" class="mt-5 inline-flex w-fit items-center gap-2 text-sm font-semibold text-white hover:text-indigo-200">{{ __('Open research workspace') }} <span aria-hidden="true">→</span></a>
            </div>
        </section>

        <section id="knowledge-base" aria-labelledby="guides-title">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.18em] text-indigo-600">{{ __('Knowledge base') }}</p>
                    <h2 id="guides-title" class="mt-2 text-2xl font-bold tracking-tight text-slate-950">{{ __('Guides for your research workspace.') }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ __('Browse practical instructions written for your assigned workspace role.') }}</p>
                </div>
                @if (auth()->user()->role === \App\Models\User::ADMINISTRATOR)
                    <a href="{{ route('support.admin.index') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:border-indigo-300 hover:text-indigo-700">{{ __('Manage Help Center') }}</a>
                @endif
            </div>

            @if ($articles->isEmpty() && ($searchQuery === '' || $faqs->isEmpty()))
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                    <h3 class="font-semibold text-slate-900">{{ __('No guides found') }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ __('Try another search term or browse the knowledge base categories.') }}</p>
                    <div class="mt-4 flex flex-wrap justify-center gap-3">
                        <a href="{{ route('support.index') }}" class="font-semibold text-indigo-700 hover:text-indigo-900">{{ __('Clear search') }}</a>
                        <a href="#contact-support" class="font-semibold text-indigo-700 hover:text-indigo-900">{{ __('Contact support') }}</a>
                        <a href="#report-issue" class="font-semibold text-indigo-700 hover:text-indigo-900">{{ __('Report an issue') }}</a>
                    </div>
                </div>
            @elseif ($articles->isEmpty())
                <p class="rounded-2xl border border-slate-200 bg-white p-5 text-sm text-slate-600">{{ __('There are no guide articles for this search yet. Matching frequently asked questions are shown below.') }}</p>
            @else
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    @foreach ($articles as $article)
                        <a href="{{ route('support.articles.show', $article) }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-indigo-200 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">{{ $article->category->name }}</span>
                                <span class="text-xs text-slate-500">{{ $article->reading_time }} {{ __('min read') }}</span>
                            </div>
                            <h3 class="mt-3 text-lg font-semibold text-slate-950 group-hover:text-indigo-700">{{ $article->title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $article->excerpt }}</p>
                            <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-indigo-700">{{ __('Read guide') }} <span aria-hidden="true">→</span></span>
                        </a>
                    @endforeach
                </div>
                <div class="mt-6">{{ $articles->links() }}</div>
            @endif
        </section>

        <section aria-labelledby="popular-title" class="rounded-3xl border border-slate-200 bg-slate-50 p-5 sm:p-7">
            <div class="mb-4">
                <p class="text-xs font-bold uppercase tracking-[.18em] text-indigo-600">{{ __('Frequently used') }}</p>
                <h2 id="popular-title" class="mt-2 text-xl font-bold text-slate-950">{{ __('Popular guides') }}</h2>
            </div>
            <div class="grid gap-2 md:grid-cols-2">
                @foreach ($popularArticles as $article)
                    <a href="{{ route('support.articles.show', $article) }}" class="flex items-center justify-between gap-4 rounded-xl bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:text-indigo-700">
                        <span>{{ $article->title }}</span><span class="shrink-0 text-xs text-slate-500">{{ $article->views }} {{ __('views') }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <section id="frequently-asked" aria-labelledby="faq-title">
            <div class="mb-5">
                <p class="text-xs font-bold uppercase tracking-[.18em] text-indigo-600">{{ __('Frequently asked') }}</p>
                <h2 id="faq-title" class="mt-2 text-2xl font-bold tracking-tight text-slate-950">{{ __('Answers to common questions.') }}</h2>
            </div>
            <div class="space-y-3" x-data="{ open: 0 }">
                @forelse ($faqs as $faq)
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <h3>
                            <button type="button" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left font-semibold text-slate-900 hover:text-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600" @click="open = open === {{ $loop->index + 1 }} ? 0 : {{ $loop->index + 1 }}" :aria-expanded="(open === {{ $loop->index + 1 }}).toString()" aria-controls="faq-answer-{{ $faq->id }}">
                                <span>{{ $faq->question }}</span><span aria-hidden="true" class="text-xl text-indigo-600" x-text="open === {{ $loop->index + 1 }} ? '−' : '+'"></span>
                            </button>
                        </h3>
                        <div id="faq-answer-{{ $faq->id }}" x-cloak x-show="open === {{ $loop->index + 1 }}" x-transition class="whitespace-pre-line px-5 pb-5 text-sm leading-7 text-slate-600">{{ $faq->answer }}</div>
                    </article>
                @empty
                    <p class="rounded-xl border border-slate-200 bg-white p-5 text-sm text-slate-600">{{ __('No published FAQs are available yet. Browse the guides or contact support for assistance.') }}</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="system-status-title">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.18em] text-emerald-700">{{ __('System status') }}</p>
                    <h2 id="system-status-title" class="mt-2 text-xl font-bold text-slate-950">{{ __('CCTAS workspace is responding') }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ __('This page loaded successfully at') }} {{ now()->format('M j, Y · g:i A') }}.</p>
                </div>
                <span class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>{{ __('Application online') }}</span>
            </div>
            <div class="mt-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('Workspace areas') }}</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach (['Authentication', 'Research management', 'Workflow', 'Analytics', 'Help Center'] as $service)
                        <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">{{ __($service) }}</span>
                    @endforeach
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-500">{{ __('Service-level uptime and response-time monitoring is not configured in this environment.') }}</p>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <div id="contact-support" class="scroll-mt-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <p class="text-xs font-bold uppercase tracking-[.18em] text-indigo-600">{{ __('Contact support') }}</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-950">{{ __('Need assistance?') }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('Send a message to the support desk. Include a research reference where it helps us understand the request.') }}</p>
                @if (session('ticket-number'))
                    <p class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm font-semibold text-emerald-800" role="status">{{ __('Support request submitted successfully.') }} {{ __('Ticket') }} #{{ session('ticket-number') }}</p>
                @endif
                <form method="POST" action="{{ route('support.tickets.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-4 sm:grid-cols-2">
                    @csrf
                    <div><label for="ticket-name" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Name') }}</label><input id="ticket-name" value="{{ auth()->user()->name }}" readonly class="w-full rounded-xl border-slate-300 bg-slate-50 text-sm text-slate-600"></div>
                    <div><label for="ticket-email" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Email') }}</label><input id="ticket-email" value="{{ auth()->user()->email }}" readonly class="w-full rounded-xl border-slate-300 bg-slate-50 text-sm text-slate-600"></div>
                    <div><label for="ticket-reference" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Research reference') }}</label><input id="ticket-reference" name="research_reference" value="{{ old('research_reference') }}" maxlength="80" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                    <div><label for="ticket-category" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Category') }}</label><select id="ticket-category" name="category" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@foreach (['Account', 'Research Submission', 'Documents', 'Workflow', 'Permissions', 'Analytics', 'Technical Issue', 'Other'] as $category)<option @selected(old('category') === $category)>{{ $category }}</option>@endforeach</select></div>
                    <div class="sm:col-span-2"><label for="ticket-subject" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Subject') }}</label><input id="ticket-subject" name="subject" value="{{ old('subject') }}" required maxlength="180" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@error('subject', 'supportTicket')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror</div>
                    <div><label for="ticket-priority" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Priority') }}</label><select id="ticket-priority" name="priority" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@foreach (['Low', 'Normal', 'High', 'Urgent'] as $priority)<option @selected(old('priority', 'Normal') === $priority)>{{ $priority }}</option>@endforeach</select></div>
                    <div><label for="ticket-attachment" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Attachment (optional)') }}</label><input id="ticket-attachment" type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full text-sm">@error('attachment')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror</div>
                    <div class="sm:col-span-2"><label for="ticket-description" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Description') }}</label><textarea id="ticket-description" name="description" rows="5" required minlength="20" maxlength="5000" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Describe what you need help with. Please do not include passwords or access tokens.') }}">{{ old('description') }}</textarea>@error('description', 'supportTicket')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror</div>
                    <div class="sm:col-span-2"><button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{ __('Send message') }} <span aria-hidden="true">→</span></button></div>
                </form>
                @error('category', 'supportTicket')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                @error('priority', 'supportTicket')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div id="report-issue" class="scroll-mt-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <p class="text-xs font-bold uppercase tracking-[.18em] text-indigo-600">{{ __('Report an issue') }}</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-950">{{ __('Tell us what went wrong.') }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('A clear description and reproduction steps help the support desk investigate.') }}</p>
                @if (session('issue-reported'))
                    <p class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm font-semibold text-emerald-800" role="status">{{ __('Issue reported successfully.') }}</p>
                @endif
                <form method="POST" action="{{ route('support.issues.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-4 sm:grid-cols-2">
                    @csrf
                    <div><label for="issue-category" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Issue category') }}</label><select id="issue-category" name="category" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@foreach (['Account', 'Research Submission', 'Documents', 'Workflow', 'Permissions', 'Analytics', 'Technical Issue', 'Other'] as $category)<option @selected(old('category') === $category)>{{ $category }}</option>@endforeach</select></div>
                    <div><label for="issue-severity" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Severity') }}</label><select id="issue-severity" name="severity" required class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@foreach (['Minor', 'Moderate', 'Major', 'Critical'] as $severity)<option @selected(old('severity', 'Minor') === $severity)>{{ $severity }}</option>@endforeach</select></div>
                    <div class="sm:col-span-2"><label for="issue-page" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Page URL (optional)') }}</label><input id="issue-page" name="page_url" type="url" value="{{ old('page_url', url()->current()) }}" maxlength="2048" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@error('page_url', 'issueReport')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror</div>
                    <div class="sm:col-span-2"><label for="issue-description" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Description') }}</label><textarea id="issue-description" name="description" rows="4" required minlength="20" maxlength="5000" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('What happened? What did you expect to happen?') }}">{{ old('description') }}</textarea>@error('description', 'issueReport')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror</div>
                    <div class="sm:col-span-2"><label for="issue-steps" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Steps to reproduce (optional)') }}</label><textarea id="issue-steps" name="reproduction_steps" rows="3" maxlength="5000" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('reproduction_steps') }}</textarea></div>
                    <div><label for="issue-browser" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Browser') }}</label><input id="issue-browser" name="browser" value="{{ old('browser') }}" maxlength="120" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                    <div><label for="issue-device" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Device') }}</label><input id="issue-device" name="device" value="{{ old('device') }}" maxlength="120" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                    <div class="sm:col-span-2"><label for="issue-screenshot" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Screenshot (optional)') }}</label><input id="issue-screenshot" type="file" name="screenshot" accept="image/jpeg,image/png,image/webp" class="w-full text-sm">@error('screenshot', 'issueReport')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror</div>
                    <div class="sm:col-span-2"><button type="submit" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-800 hover:border-indigo-300 hover:text-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{ __('Submit issue') }}</button></div>
                </form>
                @error('category', 'issueReport')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
        </section>

        <section id="my-requests" class="flex flex-col justify-between gap-4 rounded-3xl bg-slate-950 p-6 text-white sm:flex-row sm:items-center sm:p-8">
            <div><p class="text-sm font-semibold text-indigo-200">{{ __('Your support requests') }}</p><h2 class="mt-1 text-xl font-bold">{{ trans_choice(':count open request|:count open requests', $openTickets, ['count' => $openTickets]) }}</h2><p class="mt-2 text-sm text-slate-300">{{ __('Review ticket numbers and current request status in one place.') }}</p></div>
            <a href="{{ route('support.requests') }}" class="shrink-0 rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-950 hover:bg-indigo-50">{{ __('View my requests') }} <span aria-hidden="true">→</span></a>
        </section>

        <footer class="flex flex-col gap-3 border-t border-slate-200 pt-6 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <span>{{ __('Research Intelligence / 2026') }} · {{ __('Friendly guidance, when you need it.') }}</span>
            <span>{{ __('Help content for') }} {{ config('app.name', 'CCTAS') }}</span>
        </footer>
    </div>

    <script>
        function helpCenterSearch() {
            return {
                query: @js($searchQuery),
                suggestionsOpen: false,
                suggestions: [],
                searching: false,
                searchError: '',
                latestSearch: '',
                init() {
                    window.addEventListener('keydown', (event) => {
                        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
                            event.preventDefault();
                            this.$refs.search.focus();
                        }
                    });
                },
                async searchForGuides() {
                    this.suggestionsOpen = true;
                    this.searchError = '';
                    const search = this.query.trim();

                    this.latestSearch = search;

                    if (search.length < 2) {
                        this.suggestions = [];
                        this.searching = false;

                        return;
                    }

                    this.searching = true;

                    try {
                        const response = await fetch(`${@js(route('support.search'))}?q=${encodeURIComponent(search)}`, {
                            headers: { Accept: 'application/json' },
                            credentials: 'same-origin',
                        });

                        if (!response.ok) {
                            throw new Error('The Help Center search request failed.');
                        }

                        const suggestions = await response.json();

                        if (this.latestSearch === search) {
                            this.suggestions = suggestions;
                        }
                    } catch (error) {
                        console.error(error);
                        if (this.latestSearch === search) {
                            this.searchError = @js(__('Search is temporarily unavailable. Submit the form to search the full guide library.'));
                        }
                    } finally {
                        if (this.latestSearch === search) {
                            this.searching = false;
                        }
                    }
                },
                focusFirstSuggestion() {
                    const firstSuggestion = this.$refs.suggestions.querySelector('a');

                    if (firstSuggestion) {
                        firstSuggestion.focus();
                    }
                },
            };
        }
    </script>
</x-app-layout>
