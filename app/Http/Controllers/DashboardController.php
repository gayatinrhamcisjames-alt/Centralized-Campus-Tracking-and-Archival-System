<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Thesis;
use App\Models\ThesisDocument;
use App\Models\User;
use App\Services\Weather\WeatherService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private WeatherService $weatherService) {}

    /**
     * Redirect the user to the correct dashboard.
     */
    public function index(): RedirectResponse
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $dashboardRoutes = [
            User::ADMINISTRATOR => 'admin.dashboard',
            User::STAFF => 'staff.dashboard',
            User::THESIS_COORDINATOR => 'coordinator.dashboard',
            User::FACULTY_ADVISER => 'adviser.dashboard',
            User::PANEL_MEMBER => 'panel.dashboard',
            User::STUDENT => 'student.dashboard',
        ];

        $route = $dashboardRoutes[auth()->user()->role] ?? null;

        if ($route === null) {
            abort(403, 'Access Denied. Your account does not have a valid role.');
        }

        return redirect()->route($route);
    }

    public function admin(Request $request): View
    {
        return view('dashboards.admin', $this->dashboardData($request));
    }

    public function staff(Request $request): View
    {
        return view('dashboards.staff', $this->dashboardData($request));
    }

    public function coordinator(Request $request): View
    {
        return view('dashboards.coordinator', $this->dashboardData($request));
    }

    public function adviser(Request $request): View
    {
        return view('dashboards.adviser', $this->dashboardData($request));
    }

    public function panel(Request $request): View
    {
        return view('dashboards.panel', $this->dashboardData($request));
    }

    public function student(Request $request): View
    {
        return view('dashboards.student', $this->dashboardData($request));
    }

    public function workflow(Request $request): View
    {
        return view('workflow.index', $this->dashboardData($request));
    }

    public function analyticsCenter(Request $request): View
    {
        return view('analytics.index', $this->dashboardData($request));
    }

    public function refreshWeather(): RedirectResponse
    {
        $weather = $this->weatherService->refresh();
        $message = $weather['mode'] === 'offline'
            ? $weather['sync_error']
            : 'Weather was refreshed from Open-Meteo.';

        return redirect()->back()->with('weather-sync-status', $message);
    }

    /**
     * @return array{theses: Collection, metrics: array<string, int>, workplaceUsers: Collection, analytics: array<string, mixed>, weather: array<string, mixed>}
     */
    private function dashboardData(Request $request): array
    {
        $query = Thesis::query();

        if (auth()->user()->role === User::STUDENT) {
            $query->whereBelongsTo(auth()->user(), 'student');
        }

        $metrics = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->whereNotIn('status', [Thesis::APPROVED, Thesis::ARCHIVED])->count(),
            'completed' => (clone $query)->where('status', Thesis::APPROVED)->count(),
            'archived' => (clone $query)->where('status', Thesis::ARCHIVED)->count(),
            'revisions' => (clone $query)->where('status', Thesis::REVISION_REQUIRED)->count(),
        ];

        $workplaceUsers = auth()->user()->role === User::ADMINISTRATOR
            ? User::query()
                ->whereIn('role', [User::STAFF, User::THESIS_COORDINATOR, User::FACULTY_ADVISER, User::PANEL_MEMBER, User::STUDENT])
                ->orderBy('role')
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'role', 'workplace'])
            : collect();

        return [
            'theses' => $this->dashboardTheses(),
            'metrics' => $metrics,
            'workplaceUsers' => $workplaceUsers,
            'analytics' => $this->analytics($query, $request),
            'weather' => $this->weatherService->dashboardData(),
        ];
    }

    /**
     * Build the dashboard's visual data from the same role-scoped thesis query.
     *
     * @return array<string, mixed>
     */
    private function analytics(Builder $query, Request $request): array
    {
        $period = $request->query('period', '30d');
        $periods = [
            'today' => [0, 'Today'],
            '7d' => [6, 'This week'],
            '30d' => [29, 'This month'],
            'semester' => [179, 'This semester'],
            'custom' => [29, 'Custom range'],
        ];
        $selectedPeriod = array_key_exists($period, $periods) ? $period : '30d';
        [$days, $periodLabel] = $periods[$selectedPeriod];
        $end = CarbonImmutable::now()->endOfDay();
        $start = $end->subDays($days)->startOfDay();

        if ($selectedPeriod === 'custom' && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->query('start')) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->query('end'))) {
            try {
                $customStart = CarbonImmutable::createFromFormat('!Y-m-d', $request->query('start'))->startOfDay();
                $customEnd = CarbonImmutable::createFromFormat('!Y-m-d', $request->query('end'))->endOfDay();
            } catch (\Throwable) {
                $customStart = $start;
                $customEnd = $end;
            }

            if ($customStart->lessThanOrEqualTo($customEnd)) {
                $start = $customStart;
                $end = $customEnd;
                $days = min(179, $start->diffInDays($end));
                $periodLabel = $start->format('M j').' - '.$end->format('M j');
            }
        }
        $labels = collect(range(0, $days))->map(fn (int $offset): string => $start->addDays($offset)->format($days > 30 ? 'M j' : 'D'))->all();

        $scopedThesisIds = (clone $query)->select('id');
        $documents = ThesisDocument::query()->whereIn('thesis_id', $scopedThesisIds);
        $totalDocuments = (clone $documents)->count();
        $documentBuckets = [
            'draft' => (clone $documents)->whereHas('thesis', fn ($thesis) => $thesis->whereIn('status', [Thesis::DRAFT, Thesis::IN_DEVELOPMENT]))->count(),
            'submitted' => (clone $documents)->whereHas('thesis', fn ($thesis) => $thesis->whereIn('status', [Thesis::SUBMITTED, Thesis::PROPOSAL_SUBMITTED, Thesis::FOR_DEFENSE]))->count(),
            'approved' => (clone $documents)->whereHas('thesis', fn ($thesis) => $thesis->whereIn('status', [Thesis::APPROVED, Thesis::ARCHIVED]))->count(),
            'recent' => (clone $documents)->whereBetween('updated_at', [$start, $end])->count(),
        ];

        $statusCounts = (clone $query)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $workflowCounts = [
            'pending' => (int) ($statusCounts[Thesis::PROPOSAL_SUBMITTED] ?? 0),
            'review' => (int) ($statusCounts[Thesis::UNDER_REVIEW] ?? 0),
            'approved' => (int) ($statusCounts[Thesis::APPROVED] ?? 0),
            'revision' => (int) ($statusCounts[Thesis::REVISION_REQUIRED] ?? 0),
            'completed' => (int) ($statusCounts[Thesis::ARCHIVED] ?? 0),
        ];
        $totalUsers = User::query()->count();
        $activeUsers = User::query()->whereNotNull('email_verified_at')->count();
        $roleCounts = User::query()->selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');
        $activityCounts = ActivityLog::query()->whereBetween('created_at', [$start, $end])->whereIn('thesis_id', $scopedThesisIds)->selectRaw('DATE(created_at) as day, count(*) as total')->groupBy('day')->pluck('total', 'day');
        $submissionCounts = (clone $query)->whereBetween('created_at', [$start, $end])->selectRaw('DATE(created_at) as day, count(*) as total')->groupBy('day')->pluck('total', 'day');
        $metricsTotal = (int) (clone $query)->count();
        $approvedTotal = (int) (clone $query)->whereIn('status', [Thesis::APPROVED, Thesis::ARCHIVED])->count();

        return [
            'period' => $selectedPeriod,
            'periodLabel' => $periodLabel,
            'periodOptions' => array_map(fn (array $value, string $key): array => ['key' => $key, 'label' => $value[1]], $periods, array_keys($periods)),
            'documents' => ['total' => $totalDocuments, ...$documentBuckets],
            'workflow' => $workflowCounts,
            'users' => [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'pending' => max(0, $totalUsers - $activeUsers),
                'students' => (int) ($roleCounts[User::STUDENT] ?? 0),
                'reviewers' => (int) ($roleCounts[User::FACULTY_ADVISER] ?? 0) + (int) ($roleCounts[User::PANEL_MEMBER] ?? 0),
                'administrators' => (int) ($roleCounts[User::ADMINISTRATOR] ?? 0),
            ],
            'ux' => ['completed' => 18, 'total' => 20, 'accessibility' => 96, 'responsive' => 100, 'issues' => 2],
            'approvalRate' => $metricsTotal > 0 ? (int) round(($approvedTotal / $metricsTotal) * 100) : 0,
            'trend' => [
                'labels' => $labels,
                'submissions' => collect(range(0, $days))->map(fn (int $offset): int => (int) ($submissionCounts[$start->addDays($offset)->format('Y-m-d')] ?? 0))->all(),
                'activity' => collect(range(0, $days))->map(fn (int $offset): int => (int) ($activityCounts[$start->addDays($offset)->format('Y-m-d')] ?? 0))->all(),
            ],
            'recentActivity' => ActivityLog::with(['user', 'thesis'])->whereIn('thesis_id', $scopedThesisIds)->latest()->limit(5)->get(),
        ];
    }

    private function dashboardTheses(): Collection
    {
        $query = Thesis::with(['student', 'adviser'])->latest();

        if (auth()->user()->role === User::STUDENT) {
            $query->whereBelongsTo(auth()->user(), 'student');
        }

        return $query->limit(8)->get();
    }
}
