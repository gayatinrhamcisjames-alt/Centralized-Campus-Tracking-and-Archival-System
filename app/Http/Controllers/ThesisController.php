<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThesisRequest;
use App\Http\Requests\UpdateThesisRequest;
use App\Models\ActivityLog;
use App\Models\Thesis;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ThesisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Thesis::class);

        $query = Thesis::with(['student', 'adviser'])->where('is_archived', false);

        if (auth()->user()->role === User::STUDENT) {
            $query->whereBelongsTo(auth()->user(), 'student');
        }

        $this->applyFilters($query, $request);

        $theses = $query->paginate(10)->withQueryString();
        $filterOptions = $this->filterOptions();

        return view('theses.index', compact('theses', 'filterOptions'));
    }

    public function archive(Request $request): View
    {
        Gate::authorize('viewAny', Thesis::class);

        $query = Thesis::with(['student', 'adviser'])->where('is_archived', true)->latest();
        if (auth()->user()->role === User::STUDENT) {
            $query->whereBelongsTo(auth()->user(), 'student');
        }
        $this->applyFilters($query, $request);
        $theses = $query->paginate(10)->withQueryString();
        $filterOptions = $this->filterOptions();

        return view('theses.index', compact('theses', 'filterOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Thesis::class);

        $advisers = $this->advisers();

        return view('theses.create', compact('advisers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreThesisRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('file');
        $data['student_id'] = $request->user()->id;
        $data['status'] = Thesis::DRAFT;

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('theses');
        }

        $thesis = Thesis::create($data);

        $thesis->statusHistory()->create([
            'changed_by' => $request->user()->id,
            'to_status' => Thesis::DRAFT,
        ]);
        ActivityLog::record('thesis.created', $thesis);

        return redirect()->route('theses.show', $thesis)
            ->with('status', 'Thesis record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Thesis $thesis): View
    {
        Gate::authorize('view', $thesis);
        $thesis->load(['student', 'adviser', 'statusHistory.changedBy', 'documents.versions', 'milestones']);

        return view('theses.show', compact('thesis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thesis $thesis): View
    {
        Gate::authorize('update', $thesis);

        $advisers = $this->advisers();

        return view('theses.edit', compact('thesis', 'advisers'));
    }

    private function advisers()
    {
        return User::whereIn('role', [User::STAFF, User::FACULTY_ADVISER])
            ->orderBy('name')
            ->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateThesisRequest $request, Thesis $thesis): RedirectResponse
    {
        $previousStatus = $thesis->status;
        $data = $request->safe()->except(['file', 'status']);

        if ($request->filled('status')) {
            $data['status'] = $request->string('status')->toString();
            $data['submitted_at'] = $data['status'] === Thesis::SUBMITTED ? now() : $thesis->submitted_at;
            $data['approved_at'] = $data['status'] === Thesis::APPROVED ? now() : null;
            $data['archived_at'] = $data['status'] === Thesis::ARCHIVED ? now() : null;
        }

        if ($request->hasFile('file')) {
            if ($thesis->file_path) {
                Storage::delete($thesis->file_path);
            }

            $data['file_path'] = $request->file('file')->store('theses');
        }

        $thesis->update($data);

        if (array_key_exists('status', $data) && $data['status'] !== $previousStatus) {
            $thesis->statusHistory()->create([
                'changed_by' => $request->user()->id,
                'from_status' => $previousStatus,
                'to_status' => $data['status'],
            ]);
            ActivityLog::record('thesis.status_changed', $thesis, ['from' => $previousStatus, 'to' => $data['status']]);
        }

        return redirect()->route('theses.show', $thesis)
            ->with('status', 'Thesis record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Thesis $thesis): RedirectResponse
    {
        Gate::authorize('delete', $thesis);

        if ($thesis->file_path) {
            Storage::delete($thesis->file_path);
        }

        $thesis->delete();
        ActivityLog::record('thesis.deleted', $thesis);

        return redirect()->route('theses.index')
            ->with('status', 'Thesis record deleted successfully.');
    }

    /**
     * @param  Builder<Thesis>  $query
     */
    private function applyFilters(Builder $query, Request $request): void
    {
        $query->when($request->filled('search'), function ($query) use ($request): void {
            $search = $request->string('search')->toString();
            $query->where(function ($query) use ($search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('abstract', 'like', "%{$search}%")
                    ->orWhere('keywords', 'like', "%{$search}%")
                    ->orWhereHas('student', fn ($studentQuery) => $studentQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('adviser', fn ($adviserQuery) => $adviserQuery->where('name', 'like', "%{$search}%"));
            });
        });

        $query->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()));
        $query->when($request->filled('program'), fn ($query) => $query->where('program', $request->string('program')->toString()));
        $query->when($request->filled('academic_year'), fn ($query) => $query->where('academic_year', $request->string('academic_year')->toString()));
        $query->when($request->filled('student_id'), fn ($query) => $query->where('student_id', $request->integer('student_id')));
        $query->when($request->filled('adviser_id'), fn ($query) => $query->where('adviser_id', $request->integer('adviser_id')));
    }

    /**
     * @return array{programs: Collection, academicYears: Collection, students: Collection, advisers: Collection}
     */
    private function filterOptions(): array
    {
        return [
            'programs' => Thesis::query()->select('program')->distinct()->orderBy('program')->pluck('program'),
            'academicYears' => Thesis::query()->select('academic_year')->distinct()->orderByDesc('academic_year')->pluck('academic_year'),
            'students' => User::query()->where('role', User::STUDENT)->orderBy('name')->get(['id', 'name']),
            'advisers' => User::query()->whereIn('role', [User::STAFF, User::FACULTY_ADVISER])->orderBy('name')->get(['id', 'name']),
        ];
    }
}
