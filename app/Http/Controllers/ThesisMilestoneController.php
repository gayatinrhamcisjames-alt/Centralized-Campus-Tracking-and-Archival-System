<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\ThesisMilestone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ThesisMilestoneController extends Controller
{
    public function store(Request $request, Thesis $thesis): RedirectResponse
    {
        Gate::authorize('update', $thesis);
        $thesis->milestones()->create($this->validatedData($request));

        return back()->with('status', 'Milestone added successfully.');
    }

    public function update(Request $request, Thesis $thesis, ThesisMilestone $milestone): RedirectResponse
    {
        Gate::authorize('update', $thesis);
        abort_unless($milestone->thesis_id === $thesis->id, 404);
        $milestone->update($this->validatedData($request));

        return back()->with('status', 'Milestone updated successfully.');
    }

    public function destroy(Thesis $thesis, ThesisMilestone $milestone): RedirectResponse
    {
        Gate::authorize('update', $thesis);
        abort_unless($milestone->thesis_id === $thesis->id, 404);
        $milestone->delete();

        return back()->with('status', 'Milestone deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'due_date' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'status' => ['required', 'in:Pending,In Progress,Completed'],
        ]);
    }
}
