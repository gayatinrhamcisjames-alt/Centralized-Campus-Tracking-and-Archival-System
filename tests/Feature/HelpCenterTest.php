<?php

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use App\Models\HelpFaq;
use App\Models\IssueReport;
use App\Models\SupportTicket;
use App\Models\User;
use Database\Seeders\HelpCenterSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('signed in users can search role-appropriate help guides', function () {
    $user = User::factory()->create(['role' => User::STUDENT]);
    $this->seed(HelpCenterSeeder::class);

    expect(HelpArticle::query()->count())->toBe(45)
        ->and(HelpFaq::query()->where('is_published', true)->count())->toBe(6);

    $this->actingAs($user)
        ->get(route('support.index', ['q' => 'approval']))
        ->assertOk()
        ->assertSee('Research Intelligence')
        ->assertSee('How approval works')
        ->assertSee('Knowledge base');
});

test('visitors are redirected to sign in before viewing support', function () {
    $this->get(route('support.index'))
        ->assertRedirect(route('login'));
});

test('users cannot view guides restricted to another role', function () {
    $student = User::factory()->create(['role' => User::STUDENT]);
    $category = HelpCategory::query()->create([
        'name' => 'Restricted guide',
        'slug' => 'restricted-guide',
        'description' => 'Administrator guidance.',
        'icon' => 'shield',
    ]);
    $article = HelpArticle::query()->create([
        'help_category_id' => $category->id,
        'title' => 'Administrator-only guide',
        'slug' => 'administrator-only-guide',
        'excerpt' => 'Private guide summary.',
        'content' => 'Restricted instructions.',
        'role_scope' => [User::ADMINISTRATOR],
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->actingAs($student)
        ->get(route('support.articles.show', $article))
        ->assertNotFound();

    $this->post(route('support.articles.feedback', $article), [
        'helpful' => '1',
    ])->assertNotFound();

    expect($article->feedback()->count())->toBe(0);
});

test('search suggestions exclude guides outside the signed in role', function () {
    $student = User::factory()->create(['role' => User::STUDENT]);
    $category = HelpCategory::query()->create([
        'name' => 'Restricted operations',
        'slug' => 'restricted-operations',
        'description' => 'Private administrator instructions.',
    ]);
    HelpArticle::query()->create([
        'help_category_id' => $category->id,
        'title' => 'Confidential access recertification',
        'slug' => 'confidential-access-recertification',
        'excerpt' => 'Administrator access review steps.',
        'content' => 'Review the access list.',
        'role_scope' => [User::ADMINISTRATOR],
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->actingAs($student)
        ->getJson(route('support.search', ['q' => 'recertification']))
        ->assertOk()
        ->assertJsonMissing(['title' => 'Confidential access recertification']);
});

test('users can leave feedback on published guides', function () {
    $user = User::factory()->create();
    $this->seed(HelpCenterSeeder::class);
    $article = HelpArticle::query()->where('slug', 'creating-your-research-record')->firstOrFail();

    $this->actingAs($user)
        ->post(route('support.articles.feedback', $article), [
            'helpful' => '1',
            'comment' => 'The steps were clear.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('help_article_feedback', [
        'help_article_id' => $article->id,
        'user_id' => $user->id,
        'helpful' => true,
        'comment' => 'The steps were clear.',
    ]);
});

test('guide content escapes administrator-authored HTML', function () {
    $user = User::factory()->create();
    $category = HelpCategory::query()->create([
        'name' => 'Safe content',
        'slug' => 'safe-content',
        'description' => 'Escaped content test.',
    ]);
    $article = HelpArticle::query()->create([
        'help_category_id' => $category->id,
        'title' => 'Guide with markup',
        'slug' => 'guide-with-markup',
        'excerpt' => 'A summary.',
        'content' => "<script>alert('xss')</script>",
        'is_published' => true,
    ]);

    $this->actingAs($user)
        ->get(route('support.articles.show', $article))
        ->assertOk()
        ->assertSee('&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;', false)
        ->assertDontSee("<script>alert('xss')</script>", false);
});

test('users can submit support tickets with private attachments', function () {
    Storage::fake('local');
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('support.tickets.store'), [
            'research_reference' => 'TH-2026-10',
            'category' => 'Documents',
            'subject' => 'I cannot upload my revised manuscript',
            'description' => 'The upload fails after selecting a supported PDF file.',
            'priority' => 'High',
            'attachment' => UploadedFile::fake()->create('error.pdf', 24, 'application/pdf'),
        ])
        ->assertRedirect(route('support.index').'#my-requests')
        ->assertSessionHas('ticket-number', 'RI-'.now()->format('Y').'-0001');

    $ticket = SupportTicket::query()->whereBelongsTo($user)->firstOrFail();

    $this->assertModelExists($ticket);
    expect($ticket->ticket_number)->toBe('RI-'.now()->format('Y').'-0001')
        ->and($ticket->status)->toBe('Open');
    Storage::disk('local')->assertExists($ticket->attachment_path);
});

test('support ticket input is validated before creating a request', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('support.index'))
        ->post(route('support.tickets.store'), [
            'category' => 'Unknown',
            'subject' => '',
            'description' => 'short',
            'priority' => 'Critical',
        ])
        ->assertSessionHasErrorsIn('supportTicket', ['category', 'subject', 'description', 'priority']);

    expect(SupportTicket::query()->count())->toBe(0);
});

test('users can submit issue reports with private screenshots', function () {
    Storage::fake('local');
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('support.issues.store'), [
            'category' => 'Workflow',
            'page_url' => 'https://cctas.test/workflow',
            'description' => 'The review action disappears after the workflow record opens.',
            'reproduction_steps' => 'Open a review record, wait for it to load, then inspect the available actions.',
            'browser' => 'Chrome',
            'device' => 'Desktop',
            'severity' => 'Moderate',
            'screenshot' => UploadedFile::fake()->image('workflow.png'),
        ])
        ->assertRedirect(route('support.index').'#report-issue')
        ->assertSessionHas('issue-reported', true);

    $issue = IssueReport::query()->whereBelongsTo($user)->firstOrFail();

    $this->assertModelExists($issue);
    Storage::disk('local')->assertExists($issue->screenshot_path);
    expect($issue->status)->toBe('Open');
});

test('users can only see their own support requests', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $ownerTicket = SupportTicket::query()->create([
        'user_id' => $owner->id,
        'ticket_number' => 'RI-2026-0010',
        'category' => 'Account',
        'subject' => 'My account request',
        'description' => 'Please help me update my account details.',
        'priority' => 'Normal',
    ]);
    SupportTicket::query()->create([
        'user_id' => $otherUser->id,
        'ticket_number' => 'RI-2026-0011',
        'category' => 'Account',
        'subject' => 'Another users private request',
        'description' => 'This request belongs to a different user.',
        'priority' => 'Normal',
    ]);

    $this->actingAs($owner)
        ->get(route('support.requests'))
        ->assertOk()
        ->assertSee($ownerTicket->ticket_number)
        ->assertSee($ownerTicket->subject)
        ->assertDontSee('Another users private request');
});

test('only administrators can manage help guides and support requests', function () {
    $student = User::factory()->create(['role' => User::STUDENT]);

    $this->actingAs($student)
        ->get(route('support.admin.index'))
        ->assertForbidden();
});

test('administrators can create and publish a guide', function () {
    $admin = User::factory()->create(['role' => User::ADMINISTRATOR]);
    $category = HelpCategory::query()->create([
        'name' => 'Admin guides',
        'slug' => 'admin-guides',
        'description' => 'Administrator documentation.',
        'icon' => 'users',
    ]);

    $this->actingAs($admin)
        ->get(route('support.admin.index'))
        ->assertOk()
        ->assertSee('Help Center administration')
        ->assertSee('Frequently asked questions');

    $this->actingAs($admin)
        ->post(route('support.admin.articles.store'), [
            'help_category_id' => $category->id,
            'title' => 'Publishing a guide',
            'excerpt' => 'A clear guide summary.',
            'content' => 'Review the content and publish the guide for its intended audience.',
            'is_published' => '1',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $article = HelpArticle::query()->where('slug', 'publishing-a-guide')->firstOrFail();
    $this->assertModelExists($article);
    expect($article->is_published)->toBeTrue()
        ->and($article->author_id)->toBe($admin->id);
});

test('administrators can manage published frequently asked questions', function () {
    $admin = User::factory()->create(['role' => User::ADMINISTRATOR]);
    $this->seed(HelpCenterSeeder::class);

    $faq = HelpFaq::query()->where('question', 'How do I submit a research document?')->firstOrFail();
    $this->actingAs($admin)
        ->put(route('support.admin.faqs.update', $faq), [
            'question' => 'How do I submit research?',
            'answer' => 'Open the record, add the required document, and submit it for review.',
            'sort_order' => 1,
            'is_published' => '1',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect($faq->refresh()->question)->toBe('How do I submit research?')
        ->and($faq->is_published)->toBeTrue();
});

test('administrators cannot delete a category while it contains guides', function () {
    $admin = User::factory()->create(['role' => User::ADMINISTRATOR]);
    $this->seed(HelpCenterSeeder::class);
    $category = HelpCategory::query()->where('slug', 'getting-started')->firstOrFail();

    $this->actingAs($admin)
        ->delete(route('support.admin.categories.destroy', $category))
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertModelExists($category);
});

test('administrators can create a guide category', function () {
    $admin = User::factory()->create(['role' => User::ADMINISTRATOR]);

    $this->actingAs($admin)
        ->post(route('support.admin.categories.store'), [
            'name' => 'Research ethics',
            'description' => 'Guides for ethical research processes.',
            'icon' => 'shield',
            'sort_order' => 9,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $category = HelpCategory::query()->where('slug', 'research-ethics')->firstOrFail();
    $this->assertModelExists($category);
});

test('only administrators can download private support attachments', function () {
    Storage::fake('local');
    $student = User::factory()->create(['role' => User::STUDENT]);
    $admin = User::factory()->create(['role' => User::ADMINISTRATOR]);
    $path = UploadedFile::fake()->create('request.pdf', 10, 'application/pdf')->store('support-tickets', 'local');
    $ticket = SupportTicket::query()->create([
        'user_id' => $student->id,
        'ticket_number' => 'RI-2026-0042',
        'category' => 'Documents',
        'subject' => 'Private attachment access test',
        'description' => 'A support request with a private document attachment.',
        'priority' => 'Normal',
        'attachment_path' => $path,
    ]);

    $this->actingAs($student)
        ->get(route('support.admin.attachments.show', ['type' => 'ticket', 'id' => $ticket->id]))
        ->assertForbidden();

    $this->actingAs($admin)
        ->get(route('support.admin.attachments.show', ['type' => 'ticket', 'id' => $ticket->id]))
        ->assertOk()
        ->assertDownload();
});
