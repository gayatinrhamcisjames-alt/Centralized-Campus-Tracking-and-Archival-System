<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueReportRequest;
use App\Http\Requests\StoreSupportTicketRequest;
use App\Models\HelpArticle;
use App\Models\HelpArticleFeedback;
use App\Models\HelpCategory;
use App\Models\HelpFaq;
use App\Models\IssueReport;
use App\Models\SupportTicket;
use App\Models\Thesis;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HelpCenterController extends Controller
{
    public function suggestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:120'],
        ]);
        $role = $request->user()->role;
        $search = $validated['q'];

        $articles = HelpArticle::query()
            ->with('category:id,name')
            ->where('is_published', true)
            ->where(function (Builder $query) use ($role): void {
                $query->whereNull('role_scope')->orWhereJsonContains('role_scope', $role);
            })
            ->where(function (Builder $query) use ($search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->orderByDesc('views')
            ->limit(6)
            ->get();

        $articleSuggestions = $articles->map(fn (HelpArticle $article): array => [
            'title' => $article->title,
            'excerpt' => $article->excerpt,
            'category' => $article->category->name,
            'reading_time' => $article->reading_time,
            'url' => route('support.articles.show', $article),
        ]);
        $faqSuggestions = HelpFaq::query()
            ->where('is_published', true)
            ->where(fn (Builder $query) => $query->where('question', 'like', "%{$search}%")->orWhere('answer', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->limit(max(0, 6 - $articleSuggestions->count()))
            ->get()
            ->map(fn (HelpFaq $faq): array => [
                'title' => $faq->question,
                'excerpt' => $faq->answer,
                'category' => __('Frequently asked'),
                'reading_time' => 1,
                'url' => route('support.index', ['q' => $search]).'#frequently-asked',
            ]);

        return response()->json($articleSuggestions->concat($faqSuggestions)->take(6)->values());
    }

    public function index(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $role = $request->user()->role;
        $myResearch = Thesis::query();

        if ($role === User::STUDENT) {
            $myResearch->where('student_id', $request->user()->id);
        } elseif ($role === User::FACULTY_ADVISER) {
            $myResearch->where('adviser_id', $request->user()->id);
        } elseif ($role === User::PANEL_MEMBER) {
            $myResearch->whereHas('panelMembers', fn (Builder $query) => $query->whereKey($request->user()->id));
        } else {
            $myResearch = null;
        }

        $articles = HelpArticle::query()
            ->with('category:id,name,slug')
            ->where('is_published', true)
            ->where(function (Builder $query) use ($role): void {
                $query->whereNull('role_scope')->orWhereJsonContains('role_scope', $role);
            });

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $articles->where(function (Builder $query) use ($search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $categorySlug = $request->string('category')->toString();
            $articles->whereHas('category', fn (Builder $query) => $query->where('slug', $categorySlug));
        }
        $faqQuery = HelpFaq::query()->where('is_published', true);

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $faqQuery->where(fn (Builder $query) => $query->where('question', 'like', "%{$search}%")->orWhere('answer', 'like', "%{$search}%"));
        }
        $searchResultCount = (clone $articles)->count() + (clone $faqQuery)->count();

        return view('support.index', [
            'categories' => HelpCategory::query()
                ->withCount(['articles' => function (Builder $query) use ($role): void {
                    $query->where('is_published', true)
                        ->where(fn (Builder $scope) => $scope->whereNull('role_scope')->orWhereJsonContains('role_scope', $role));
                }])
                ->orderBy('sort_order')
                ->get(),
            'articles' => $articles->orderByDesc('published_at')->orderBy('title')->paginate(12)->withQueryString(),
            'popularArticles' => HelpArticle::query()
                ->with('category:id,name,slug')
                ->where('is_published', true)
                ->where(function (Builder $query) use ($role): void {
                    $query->whereNull('role_scope')->orWhereJsonContains('role_scope', $role);
                })
                ->orderByDesc('views')
                ->limit(5)
                ->get(),
            'recommendedArticles' => HelpArticle::query()
                ->with('category:id,name,slug')
                ->where('is_published', true)
                ->where(function (Builder $query) use ($role): void {
                    $query->whereNull('role_scope')->orWhereJsonContains('role_scope', $role);
                })
                ->where(function (Builder $query) use ($role): void {
                    $query->whereJsonContains('role_scope', $role)
                        ->orWhere('help_category_id', HelpCategory::query()->where('slug', 'getting-started')->select('id'));
                })
                ->orderByDesc('views')
                ->limit(3)
                ->get(),
            'myResearchCount' => $myResearch?->count(),
            'searchQuery' => $request->string('q')->toString(),
            'faqs' => $faqQuery->orderBy('sort_order')->get(),
            'searchResultCount' => $searchResultCount,
            'selectedCategory' => $request->string('category')->toString(),
            'openTickets' => SupportTicket::query()
                ->whereBelongsTo($request->user())
                ->whereIn('status', ['Open', 'In Progress'])
                ->count(),
        ]);
    }

    public function show(Request $request, HelpArticle $article): View
    {
        $this->ensureArticleIsAvailableTo($request, $article);

        $article->increment('views');
        $article->load(['category:id,name,slug', 'author:id,name']);

        $relatedArticles = HelpArticle::query()
            ->with('category:id,name,slug')
            ->where('is_published', true)
            ->where('help_category_id', $article->help_category_id)
            ->where(function (Builder $query) use ($request): void {
                $query->whereNull('role_scope')->orWhereJsonContains('role_scope', $request->user()->role);
            })
            ->where('id', '<>', $article->id)
            ->limit(4)
            ->get();

        return view('support.article', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'feedback' => $article->feedback()->whereBelongsTo($request->user())->first(),
        ]);
    }

    public function feedback(Request $request, HelpArticle $article): RedirectResponse
    {
        $this->ensureArticleIsAvailableTo($request, $article);

        $validated = $request->validate([
            'helpful' => ['required', 'boolean'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        HelpArticleFeedback::query()->updateOrCreate(
            ['help_article_id' => $article->id, 'user_id' => $request->user()->id],
            ['helpful' => $validated['helpful'], 'comment' => $validated['comment'] ?? null],
        );

        return back()->with('feedback-saved', true);
    }

    public function ticket(StoreSupportTicketRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['attachment']);
        $ticket = SupportTicket::query()->create([
            ...$validated,
            'user_id' => $request->user()->id,
            'attachment_path' => $request->file('attachment')?->store('support-tickets', 'local'),
        ]);
        $ticket->update(['ticket_number' => sprintf('RI-%s-%04d', now()->format('Y'), $ticket->id)]);

        return redirect()->to(route('support.index').'#my-requests')
            ->with('ticket-number', $ticket->ticket_number);
    }

    public function issue(StoreIssueReportRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['screenshot']);
        IssueReport::query()->create([
            ...$validated,
            'user_id' => $request->user()->id,
            'screenshot_path' => $request->file('screenshot')?->store('issue-reports', 'local'),
        ]);

        return redirect()->to(route('support.index').'#report-issue')
            ->with('issue-reported', true);
    }

    public function requests(Request $request): View
    {
        $tickets = SupportTicket::query()
            ->whereBelongsTo($request->user())
            ->latest()
            ->paginate(15);
        $issues = IssueReport::query()
            ->whereBelongsTo($request->user())
            ->latest()
            ->limit(10)
            ->get();

        return view('support.requests', compact('tickets', 'issues'));
    }

    public function attachment(Request $request, string $type, int $id): StreamedResponse
    {
        abort_unless($request->user()->role === User::ADMINISTRATOR, 403);

        $path = match ($type) {
            'ticket' => SupportTicket::query()->findOrFail($id)->attachment_path,
            'issue' => IssueReport::query()->findOrFail($id)->screenshot_path,
            default => abort(404),
        };

        abort_if(! $path || ! Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }

    private function ensureArticleIsAvailableTo(Request $request, HelpArticle $article): void
    {
        abort_unless($article->is_published, 404);

        $roleScope = $article->role_scope;
        abort_if($roleScope !== null && ! in_array($request->user()->role, $roleScope, true), 404);
    }
}
