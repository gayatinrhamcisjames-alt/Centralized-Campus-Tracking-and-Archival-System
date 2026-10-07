<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Thesis;
use App\Models\ThesisReview;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ThesisWorkflowController extends Controller
{
    public function archive(Thesis $thesis): RedirectResponse
    {
        Gate::authorize('archive', $thesis);

        $previousStatus = $thesis->status;
        $thesis->update(['status' => Thesis::ARCHIVED, 'is_archived' => true, 'archived_at' => now()]);
        $thesis->statusHistory()->create(['changed_by' => auth()->id(), 'from_status' => $previousStatus, 'to_status' => Thesis::ARCHIVED]);
        ActivityLog::record('thesis.archived', $thesis, ['from_status' => $previousStatus]);

        return back()->with('status', 'Thesis archived successfully.');
    }

    public function restore(Thesis $thesis): RedirectResponse
    {
        Gate::authorize('restore', $thesis);

        $thesis->update(['status' => Thesis::IN_DEVELOPMENT, 'is_archived' => false, 'archived_at' => null]);
        $thesis->statusHistory()->create(['changed_by' => auth()->id(), 'from_status' => Thesis::ARCHIVED, 'to_status' => Thesis::IN_DEVELOPMENT]);
        ActivityLog::record('thesis.restored', $thesis);

        return back()->with('status', 'Thesis restored successfully.');
    }

    public function assignReviewer(Request $request, Thesis $thesis): RedirectResponse
    {
        Gate::authorize('assignReviewer', $thesis);

        $validated = $request->validate([
            'reviewer_id' => ['required', 'integer', Rule::exists('users', 'id')->whereIn('role', [User::STAFF, User::FACULTY_ADVISER, User::PANEL_MEMBER])],
        ]);

        $thesis->panelMembers()->syncWithoutDetaching([$validated['reviewer_id'] => ['role' => 'Reviewer']]);
        ActivityLog::record('thesis.reviewer_assigned', $thesis, ['reviewer_id' => $validated['reviewer_id']]);

        return back()->with('status', 'Reviewer assigned successfully.');
    }

    public function review(Request $request, Thesis $thesis): RedirectResponse
    {
        Gate::authorize('review', $thesis);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['Approved', 'Revision Required', 'Pending'])],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        $review = ThesisReview::updateOrCreate(
            ['thesis_id' => $thesis->id, 'reviewer_id' => auth()->id()],
            ['status' => $validated['status'], 'remarks' => $validated['remarks'] ?? null, 'reviewed_at' => now()],
        );

        if ($validated['status'] !== 'Pending') {
            $previousStatus = $thesis->status;
            $thesis->update(['status' => $validated['status']]);
            if ($previousStatus !== $thesis->status) {
                $thesis->statusHistory()->create(['changed_by' => auth()->id(), 'from_status' => $previousStatus, 'to_status' => $thesis->status]);
            }
        }

        ActivityLog::record('thesis.review_submitted', $thesis, ['review_id' => $review->id, 'status' => $validated['status']]);

        return back()->with('status', 'Review submitted successfully.');
    }
}
