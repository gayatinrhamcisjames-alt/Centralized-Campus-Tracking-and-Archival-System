<?php

use App\Models\ActivityLog;
use App\Models\Thesis;
use App\Models\ThesisReview;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('allows a student to create a thesis draft', function () {
    $student = User::factory()->create(['role' => User::STUDENT]);

    $response = $this->actingAs($student)->post(route('theses.store'), [
        'title' => 'A Centralized Thesis Archive',
        'abstract' => 'A useful abstract.',
        'keywords' => 'thesis, archive',
        'program' => 'BS Computer Science',
        'academic_year' => '2025-2026',
    ]);

    $thesis = Thesis::first();

    $response->assertRedirect(route('theses.show', $thesis, absolute: false));
    expect($thesis)->not->toBeNull()
        ->and($thesis->student_id)->toBe($student->id)
        ->and($thesis->status)->toBe(Thesis::DRAFT);
});

it('only lists a students own thesis records', function () {
    $student = User::factory()->create(['role' => User::STUDENT]);
    $otherStudent = User::factory()->create(['role' => User::STUDENT]);
    $ownThesis = Thesis::factory()->create(['student_id' => $student->id]);
    $otherThesis = Thesis::factory()->create(['student_id' => $otherStudent->id]);

    $response = $this->actingAs($student)->get(route('theses.index'));

    $response->assertOk()
        ->assertSee($ownThesis->title)
        ->assertDontSee($otherThesis->title);
});

it('allows staff to review a thesis and archive its pdf', function () {
    Storage::fake();
    $staff = User::factory()->create(['role' => User::STAFF]);
    $thesis = Thesis::factory()->create(['status' => Thesis::SUBMITTED]);

    $response = $this->actingAs($staff)->put(route('theses.update', $thesis), [
        'title' => $thesis->title,
        'abstract' => $thesis->abstract,
        'keywords' => $thesis->keywords,
        'program' => $thesis->program,
        'academic_year' => $thesis->academic_year,
        'status' => Thesis::APPROVED,
    ]);

    $response->assertRedirect(route('theses.show', $thesis, absolute: false));
    expect($thesis->fresh()->status)->toBe(Thesis::APPROVED)
        ->and($thesis->fresh()->approved_at)->not->toBeNull();
});

it('prevents a student from viewing another students thesis', function () {
    $student = User::factory()->create(['role' => User::STUDENT]);
    $thesis = Thesis::factory()->create();

    $this->actingAs($student)->get(route('theses.show', $thesis))->assertForbidden();
});

it('filters thesis records and records workflow changes', function () {
    $staff = User::factory()->create(['role' => User::STAFF]);
    $matchingThesis = Thesis::factory()->create(['title' => 'Campus Data Archive']);
    $otherThesis = Thesis::factory()->create(['title' => 'Unrelated Research']);

    $this->actingAs($staff)
        ->get(route('theses.index', ['search' => 'Campus Data']))
        ->assertOk()
        ->assertSee($matchingThesis->title)
        ->assertDontSee($otherThesis->title);

    $this->actingAs($staff)->put(route('theses.update', $matchingThesis), [
        'title' => $matchingThesis->title,
        'abstract' => $matchingThesis->abstract,
        'keywords' => $matchingThesis->keywords,
        'program' => $matchingThesis->program,
        'department' => $matchingThesis->department,
        'academic_year' => $matchingThesis->academic_year,
        'school_year' => $matchingThesis->school_year,
        'status' => Thesis::APPROVED,
    ]);

    expect($matchingThesis->statusHistory()->latest()->value('to_status'))->toBe(Thesis::APPROVED);
});

it('supports combined advanced search filters', function () {
    $staff = User::factory()->create(['role' => User::STAFF]);
    $student = User::factory()->create(['role' => User::STUDENT, 'name' => 'Alex Researcher']);
    $adviser = User::factory()->create(['role' => User::FACULTY_ADVISER, 'name' => 'Dr. Ada Adviser']);
    $matchingThesis = Thesis::factory()->create([
        'title' => 'Secure Campus Repository',
        'abstract' => 'A study about similarity detection and archival search.',
        'keywords' => 'security, repository, similarity',
        'academic_year' => '2026-2027',
        'program' => 'BS Information Technology',
        'student_id' => $student->id,
        'adviser_id' => $adviser->id,
        'status' => Thesis::UNDER_REVIEW,
    ]);
    $otherThesis = Thesis::factory()->create([
        'title' => 'Legacy Campus Records',
        'abstract' => 'A study about similarity detection and archival search.',
        'academic_year' => '2025-2026',
        'program' => 'BS Information Technology',
        'status' => Thesis::UNDER_REVIEW,
    ]);

    $this->actingAs($staff)
        ->get(route('theses.index', [
            'search' => 'similarity detection',
            'academic_year' => '2026-2027',
            'program' => 'BS Information Technology',
            'student_id' => $student->id,
            'adviser_id' => $adviser->id,
            'status' => Thesis::UNDER_REVIEW,
        ]))
        ->assertOk()
        ->assertSee($matchingThesis->title)
        ->assertDontSee($otherThesis->title);
});

it('stores versioned thesis documents', function () {
    Storage::fake();
    $staff = User::factory()->create(['role' => User::STAFF]);
    $thesis = Thesis::factory()->create();

    $response = $this->actingAs($staff)->post(route('theses.documents.store', $thesis), [
        'name' => 'Final manuscript',
        'document_type' => 'Manuscript',
        'file' => UploadedFile::fake()->create('manuscript.pdf', 100, 'application/pdf'),
    ]);

    $response->assertRedirect();
    expect($thesis->fresh()->documents()->first()->versions)->toHaveCount(1);
});

it('allows staff to add thesis milestones', function () {
    $staff = User::factory()->create(['role' => User::STAFF]);
    $thesis = Thesis::factory()->create();

    $response = $this->actingAs($staff)->post(route('theses.milestones.store', $thesis), [
        'name' => 'Proposal defense',
        'due_date' => '2026-10-01',
        'status' => 'Pending',
    ]);

    $response->assertRedirect();
    expect($thesis->fresh()->milestones)->toHaveCount(1);
});

it('archives and restores a thesis while keeping an audit trail', function () {
    $staff = User::factory()->create(['role' => User::STAFF]);
    $thesis = Thesis::factory()->create(['status' => Thesis::APPROVED]);

    $this->actingAs($staff)->post(route('theses.archive.store', $thesis))->assertRedirect();

    expect($thesis->fresh()->is_archived)->toBeTrue()
        ->and($thesis->fresh()->status)->toBe(Thesis::ARCHIVED)
        ->and(ActivityLog::where('action', 'thesis.archived')->where('thesis_id', $thesis->id)->exists())->toBeTrue();

    $this->actingAs($staff)->get(route('theses.index'))->assertDontSee($thesis->title);
    $this->actingAs($staff)->get(route('theses.archive'))->assertSee($thesis->title);

    $this->actingAs($staff)->post(route('theses.restore', $thesis))->assertRedirect();

    expect($thesis->fresh()->is_archived)->toBeFalse()
        ->and($thesis->fresh()->status)->toBe(Thesis::IN_DEVELOPMENT);
});

it('soft deletes a thesis and blocks students from creating records as another role', function () {
    $staff = User::factory()->create(['role' => User::STAFF]);
    $thesis = Thesis::factory()->create();

    $this->actingAs($staff)->delete(route('theses.destroy', $thesis))->assertRedirect();

    expect(Thesis::withTrashed()->find($thesis->id))->not->toBeNull()
        ->and(Thesis::find($thesis->id))->toBeNull();

    $this->actingAs($staff)->post(route('theses.store'), [
        'title' => 'Unauthorized submission',
        'program' => 'BS Information Technology',
        'academic_year' => '2025-2026',
    ])->assertForbidden();
});

it('allows a coordinator to assign a reviewer and an assigned adviser to review', function () {
    $coordinator = User::factory()->create(['role' => User::THESIS_COORDINATOR]);
    $adviser = User::factory()->create(['role' => User::FACULTY_ADVISER]);
    $thesis = Thesis::factory()->create(['adviser_id' => $adviser->id]);

    $this->actingAs($coordinator)
        ->post(route('theses.reviewers.store', $thesis), ['reviewer_id' => $adviser->id])
        ->assertRedirect();

    expect($thesis->fresh()->panelMembers()->whereKey($adviser->id)->exists())->toBeTrue();

    $this->actingAs($adviser)
        ->post(route('theses.reviews.store', $thesis), [
            'status' => Thesis::REVISION_REQUIRED,
            'remarks' => 'Please clarify the methodology.',
        ])->assertRedirect();

    expect(ThesisReview::where('thesis_id', $thesis->id)->where('reviewer_id', $adviser->id)->value('status'))
        ->toBe(Thesis::REVISION_REQUIRED)
        ->and($thesis->fresh()->status)->toBe(Thesis::REVISION_REQUIRED);
});
