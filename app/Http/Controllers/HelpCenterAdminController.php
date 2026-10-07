<?php

namespace App\Http\Controllers;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use App\Models\HelpFaq;
use App\Models\IssueReport;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HelpCenterAdminController extends Controller
{
    public function index(): View
    {
        return view('support.admin', [
            'categories' => HelpCategory::query()->withCount('articles')->orderBy('sort_order')->get(),
            'articles' => HelpArticle::query()->with('category:id,name')->latest()->paginate(15),
            'faqs' => HelpFaq::query()->orderBy('sort_order')->get(),
            'tickets' => SupportTicket::query()->with('user:id,name,email')->latest()->limit(25)->get(),
            'issues' => IssueReport::query()->with('user:id,name,email')->latest()->limit(25)->get(),
            'metrics' => [
                'articles' => HelpArticle::count(),
                'published' => HelpArticle::where('is_published', true)->count(),
                'views' => HelpArticle::sum('views'),
                'helpful' => (int) round(HelpArticle::join('help_article_feedback', 'help_articles.id', '=', 'help_article_feedback.help_article_id')->avg('help_article_feedback.helpful') * 100),
                'open_tickets' => SupportTicket::whereIn('status', ['Open', 'In Progress'])->count(),
                'open_issues' => IssueReport::whereIn('status', ['Open', 'In Progress'])->count(),
            ],
        ]);
    }

    public function storeArticle(Request $request): RedirectResponse
    {
        $request->merge([
            'slug' => Str::slug($request->input('slug') ?: $request->input('title', '')),
            'role_scope' => $request->input('role_scope', []),
        ]);
        $validated = $request->validate($this->articleRules());
        $validated['role_scope'] = $validated['role_scope'] ?: null;
        $validated['author_id'] = $request->user()->id;
        $validated['is_published'] = $request->boolean('is_published');
        $validated['published_at'] = $validated['is_published'] ? now() : null;
        $validated['reading_time'] = max(1, (int) ceil(str_word_count($validated['content']) / 180));

        HelpArticle::query()->create($validated);

        return back()->with('status', __('Guide created.'));
    }

    public function updateArticle(Request $request, HelpArticle $article): RedirectResponse
    {
        $request->merge([
            'slug' => Str::slug($request->input('slug') ?: $request->input('title', '')),
            'role_scope' => $request->input('role_scope', []),
        ]);
        $validated = $request->validate($this->articleRules($article));
        $validated['role_scope'] = $validated['role_scope'] ?: null;
        $validated['is_published'] = $request->boolean('is_published');
        $validated['published_at'] = $validated['is_published'] ? ($article->published_at ?? now()) : null;
        $validated['reading_time'] = max(1, (int) ceil(str_word_count($validated['content']) / 180));

        $article->update($validated);

        return back()->with('status', __('Guide updated.'));
    }

    public function destroyArticle(HelpArticle $article): RedirectResponse
    {
        $article->delete();

        return back()->with('status', __('Guide deleted.'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('name', ''))]);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('help_categories', 'name')],
            'slug' => ['required', 'string', 'max:140', Rule::unique('help_categories', 'slug')],
            'description' => ['required', 'string', 'max:500'],
            'icon' => ['required', 'string', 'max:32'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
        ]);

        HelpCategory::query()->create($validated);

        return back()->with('status', __('Category created.'));
    }

    public function updateCategory(Request $request, HelpCategory $category): RedirectResponse
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('name', ''))]);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('help_categories', 'name')->ignore($category)],
            'slug' => ['required', 'string', 'max:140', Rule::unique('help_categories', 'slug')->ignore($category)],
            'description' => ['required', 'string', 'max:500'],
            'icon' => ['required', 'string', 'max:32'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
        ]);

        $category->update($validated);

        return back()->with('status', __('Category updated.'));
    }

    public function destroyCategory(HelpCategory $category): RedirectResponse
    {
        if ($category->articles()->exists()) {
            return back()->with('error', __('Move or delete the category’s guides before deleting this category.'));
        }

        $category->delete();

        return back()->with('status', __('Category deleted.'));
    }

    public function storeFaq(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
        ]);
        HelpFaq::query()->create([
            ...$validated,
            'author_id' => $request->user()->id,
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('status', __('FAQ created.'));
    }

    public function updateFaq(Request $request, HelpFaq $faq): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
        ]);
        $faq->update([
            ...$validated,
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('status', __('FAQ updated.'));
    }

    public function destroyFaq(HelpFaq $faq): RedirectResponse
    {
        $faq->delete();

        return back()->with('status', __('FAQ deleted.'));
    }

    public function updateTicket(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Open', 'In Progress', 'Resolved', 'Closed'])],
        ]);
        $ticket->update($validated);

        return back()->with('status', __('Support request updated.'));
    }

    public function updateIssue(Request $request, IssueReport $issue): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Open', 'In Progress', 'Resolved', 'Closed'])],
        ]);
        $issue->update($validated);

        return back()->with('status', __('Issue report updated.'));
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    private function articleRules(?HelpArticle $article = null): array
    {
        return [
            'help_category_id' => ['required', 'integer', Rule::exists('help_categories', 'id')],
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180', Rule::unique('help_articles', 'slug')->ignore($article)],
            'excerpt' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string', 'max:30000'],
            'role_scope' => ['nullable', 'array'],
            'role_scope.*' => ['string', Rule::in(['Administrator', 'Staff', 'Thesis Coordinator', 'Faculty Adviser', 'Panel Member', 'Student'])],
        ];
    }
}
