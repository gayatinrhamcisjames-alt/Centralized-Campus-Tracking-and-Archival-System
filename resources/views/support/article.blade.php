<x-app-layout>
    <x-slot name="header">
        <nav aria-label="{{ __('Breadcrumb') }}" class="mb-4 text-sm text-slate-500">
            <a href="{{ route('support.index') }}" class="hover:text-indigo-700">{{ __('Research Intelligence / 2026') }}</a>
            <span class="px-2" aria-hidden="true">/</span>
            <a href="{{ route('support.index', ['category' => $article->category->slug]) }}" class="hover:text-indigo-700">{{ $article->category->name }}</a>
        </nav>
        <h1 class="max-w-4xl text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">{{ $article->title }}</h1>
        <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">{{ $article->excerpt }}</p>
        <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-xs text-slate-500">
            <span>{{ __('By') }} {{ $article->author?->name ?? __('CCTAS Support Team') }}</span>
            <span>{{ __('Updated') }} {{ $article->updated_at->format('M j, Y') }}</span>
            <span>{{ $article->reading_time }} {{ __('min read') }}</span>
            <span>{{ $article->views }} {{ __('views') }}</span>
        </div>
    </x-slot>

    <div class="app-container grid gap-8 py-8 lg:grid-cols-[minmax(0,1fr)_18rem]">
        <article class="min-w-0 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-9">
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-700">{{ __('Overview') }}</p>
                <p class="mt-2 text-sm leading-7 text-slate-700">{{ $article->excerpt }}</p>
            </div>
            <nav aria-label="{{ __('On this page') }}" class="my-7 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="text-sm font-bold text-slate-900">{{ __('On this page') }}</h2>
                <ul class="mt-3 grid gap-2 text-sm text-indigo-700 sm:grid-cols-2">
                    <li><a href="#guide-overview" class="hover:underline">{{ __('Guide details') }}</a></li>
                    <li><a href="#guide-next-steps" class="hover:underline">{{ __('Next steps') }}</a></li>
                    <li><a href="#guide-feedback" class="hover:underline">{{ __('Was this helpful?') }}</a></li>
                </ul>
            </nav>
            <section id="guide-overview" class="scroll-mt-8">
                <h2 class="text-xl font-bold text-slate-950">{{ __('Guide details') }}</h2>
                <div class="mt-4 whitespace-pre-line text-[15px] leading-8 text-slate-700">{{ $article->content }}</div>
            </section>
            <section id="guide-next-steps" class="mt-8 rounded-2xl border border-slate-200 p-5">
                <h2 class="text-lg font-bold text-slate-950">{{ __('Still need help?') }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('Send the support desk the page or research reference and the step where you got stuck. Do not include passwords or access tokens.') }}</p>
                <a href="{{ route('support.index') }}#contact-support" class="mt-4 inline-flex rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">{{ __('Contact support') }} <span class="ml-2" aria-hidden="true">→</span></a>
            </section>
            <section id="guide-feedback" class="mt-8 rounded-2xl bg-slate-50 p-5">
                <h2 class="font-bold text-slate-950">{{ __('Was this guide helpful?') }}</h2>
                @if (session('feedback-saved'))
                    <p class="mt-3 text-sm font-semibold text-emerald-700" role="status">{{ __('Thank you. Your feedback has been recorded.') }}</p>
                @endif
                <form method="POST" action="{{ route('support.articles.feedback', $article) }}" class="mt-4">
                    @csrf
                    <label for="feedback-comment" class="mb-2 block text-sm text-slate-600">{{ __('Optional comment') }}</label>
                    <textarea id="feedback-comment" name="comment" rows="2" maxlength="1000" class="w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('What would make this guide clearer?') }}">{{ old('comment', $feedback?->comment) }}</textarea>
                    @error('comment')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    <div class="mt-3 flex flex-wrap gap-3">
                        <button type="submit" name="helpful" value="1" class="rounded-xl px-4 py-2.5 text-sm font-semibold {{ $feedback?->helpful ? 'bg-emerald-100 text-emerald-800' : 'border border-slate-300 bg-white text-slate-700 hover:border-emerald-300' }}" aria-label="{{ __('Yes, this was helpful') }}">👍 {{ __('Yes') }}</button>
                        <button type="submit" name="helpful" value="0" class="rounded-xl px-4 py-2.5 text-sm font-semibold {{ $feedback && ! $feedback->helpful ? 'bg-amber-100 text-amber-800' : 'border border-slate-300 bg-white text-slate-700 hover:border-amber-300' }}" aria-label="{{ __('No, this was not helpful') }}">👎 {{ __('Not really') }}</button>
                    </div>
                </form>
            </section>
        </article>

        <aside class="space-y-5">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-bold text-slate-950">{{ __('On this page') }}</h2>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li><a href="#guide-overview" class="hover:text-indigo-700">{{ __('Guide details') }}</a></li>
                    <li><a href="#guide-next-steps" class="hover:text-indigo-700">{{ __('Next steps') }}</a></li>
                    <li><a href="#guide-feedback" class="hover:text-indigo-700">{{ __('Was this helpful?') }}</a></li>
                </ul>
            </section>
            <section class="rounded-2xl bg-slate-950 p-5 text-white">
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-200">{{ __('Need more help?') }}</p>
                <h2 class="mt-2 text-lg font-bold">{{ __('Talk to the support desk.') }}</h2>
                <a href="{{ route('support.index') }}#contact-support" class="mt-4 inline-flex text-sm font-semibold text-white hover:text-indigo-200">{{ __('Contact Support') }} <span class="ml-2" aria-hidden="true">→</span></a>
            </section>
            @if ($relatedArticles->isNotEmpty())
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="font-bold text-slate-950">{{ __('Related guides') }}</h2>
                    <ul class="mt-3 space-y-3">
                        @foreach ($relatedArticles as $related)
                            <li><a href="{{ route('support.articles.show', $related) }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">{{ $related->title }}</a></li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </aside>
    </div>
</x-app-layout>
