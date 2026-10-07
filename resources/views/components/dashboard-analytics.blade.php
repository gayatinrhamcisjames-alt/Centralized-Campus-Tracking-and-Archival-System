@props(['analytics', 'metrics'])

@php
    $trendLabels = array_slice($analytics['trend']['labels'], -12);
    $trendSubmissions = array_slice($analytics['trend']['submissions'], -12);
    $trendActivity = array_slice($analytics['trend']['activity'], -12);
    $trendMax = max(max($trendSubmissions ?: [0]), max($trendActivity ?: [0]), 1);
    $trendPoints = collect($trendSubmissions)->map(function (int $value, int $index) use ($trendMax, $trendSubmissions): string {
        $x = count($trendSubmissions) > 1 ? ($index / (count($trendSubmissions) - 1)) * 100 : 50;
        $y = 88 - (($value / $trendMax) * 68);

        return number_format($x, 2).','.number_format($y, 2);
    })->implode(' ');
@endphp

<section class="dashboard-overview" x-data="{ activePoint: null }">
    <div class="dashboard-section-heading">
        <div>
            <p class="workspace-eyebrow"><span></span>{{ __('Intelligence overview') }}</p>
            <h2>{{ __('Your research operation, at a glance.') }}</h2>
        </div>
        <span class="dashboard-period-note">{{ $analytics['periodLabel'] }} <span class="dashboard-live-dot"></span></span>
    </div>

    <section id="workflow" class="platform-purpose-panel">
        <div class="platform-purpose-copy">
            <p class="workspace-eyebrow"><span></span>{{ __('Why this workspace exists') }}</p>
            <h3>{{ __('Move research forward with one visible workflow.') }}</h3>
            <p>{{ __('CCTAS keeps every proposal, document, decision, and revision connected so teams can spend less time searching and more time making progress.') }}</p>
        </div>
        <div class="workflow-steps" aria-label="{{ __('Research workflow') }}">
            @foreach ([['01', 'Proposal', 'Shape the research question'], ['02', 'Review', 'Gather focused feedback'], ['03', 'Approval', 'Make the decision visible'], ['04', 'Archive', 'Preserve the final record']] as [$number, $title, $description])
                <div class="workflow-purpose-step"><span>{{ $number }}</span><div><strong>{{ __($title) }}</strong><small>{{ __($description) }}</small></div></div>
            @endforeach
        </div>
    </section>

    <div class="dashboard-overview-grid">
        <article class="overview-card overview-card-documents">
            <div class="overview-card-header"><span class="overview-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3.75h8.5L19 8.25v12H6zM14 3.75v4.5h5M9 12h7M9 15.5h5" /></svg></span><span class="overview-index">01 / 04</span></div>
            <p class="overview-card-label">{{ __('Research & documents') }}</p>
            <strong class="overview-card-value">{{ $analytics['documents']['total'] }}</strong>
            <p class="overview-card-copy">{{ __('Research documents in the workspace') }}</p>
            <div class="overview-mini-stat"><span>{{ __('Approved') }}</span><strong>{{ $analytics['documents']['approved'] }}</strong><div class="mini-progress"><span style="width: {{ $analytics['documents']['total'] > 0 ? ($analytics['documents']['approved'] / $analytics['documents']['total']) * 100 : 0 }}%"></span></div></div>
            <div class="overview-breakdown"><span><i class="dot dot-blue"></i>{{ __('Draft') }} <b>{{ $analytics['documents']['draft'] }}</b></span><span><i class="dot dot-cyan"></i>{{ __('Submitted') }} <b>{{ $analytics['documents']['submitted'] }}</b></span><span><i class="dot dot-green"></i>{{ __('Updated recently') }} <b>{{ $analytics['documents']['recent'] }}</b></span></div>
            <a href="{{ route('theses.index') }}" class="overview-link">{{ __('View documents') }} <span>&rarr;</span></a>
        </article>

        <article class="overview-card overview-card-workflow">
            <div class="overview-card-header"><span class="overview-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 6h14M5 12h9M5 18h14M16 9l3 3-3 3" /></svg></span><span class="overview-index">02 / 04</span></div>
            <p class="overview-card-label">{{ __('Workflow & approval') }}</p>
            <strong class="overview-card-value">{{ $analytics['approvalRate'] }}<small>%</small></strong>
            <p class="overview-card-copy">{{ __('Approval completion rate') }}</p>
            <div class="workflow-track"><span class="workflow-track-line"></span>@foreach ([['pending', 'P'], ['review', 'R'], ['approved', 'A'], ['completed', 'C']] as [$key, $letter])<span class="workflow-step {{ $analytics['workflow'][$key] > 0 ? 'is-active' : '' }}"><b>{{ $letter }}</b><small>{{ $analytics['workflow'][$key] }}</small></span>@endforeach</div>
            <div class="overview-breakdown"><span><i class="dot dot-purple"></i>{{ __('Pending review') }} <b>{{ $analytics['workflow']['pending'] + $analytics['workflow']['review'] }}</b></span><span><i class="dot dot-rose"></i>{{ __('Revision') }} <b>{{ $analytics['workflow']['revision'] }}</b></span></div>
            <a href="{{ route('theses.index') }}" class="overview-link">{{ __('View workflow') }} <span>&rarr;</span></a>
        </article>

        <article class="overview-card overview-card-users">
            <div class="overview-card-header"><span class="overview-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 19v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 17.5V19M10 10a3.25 3.25 0 1 0 0-6.5A3.25 3.25 0 0 0 10 10ZM16 11a3 3 0 0 1 2.5 1.3M15.5 4.2a3.25 3.25 0 0 1 0 6.1" /></svg></span><span class="overview-index">03 / 04</span></div>
            <p class="overview-card-label">{{ __('User roles') }}</p>
            <strong class="overview-card-value">{{ $analytics['users']['total'] }}</strong>
            <p class="overview-card-copy">{{ __('People connected to the workspace') }}</p>
            <div class="role-bars"><span style="width: {{ $analytics['users']['total'] > 0 ? ($analytics['users']['students'] / $analytics['users']['total']) * 100 : 0 }}%" title="{{ __('Researchers / Students') }}"></span><span style="width: {{ $analytics['users']['total'] > 0 ? ($analytics['users']['reviewers'] / $analytics['users']['total']) * 100 : 0 }}%" title="{{ __('Reviewers / Faculty') }}"></span><span style="width: {{ $analytics['users']['total'] > 0 ? ($analytics['users']['administrators'] / $analytics['users']['total']) * 100 : 0 }}%" title="{{ __('Administrators') }}"></span></div>
            <div class="overview-breakdown"><span><i class="dot dot-green"></i>{{ __('Active users') }} <b>{{ $analytics['users']['active'] }}</b></span><span><i class="dot dot-amber"></i>{{ __('Pending accounts') }} <b>{{ $analytics['users']['pending'] }}</b></span></div>
            @if (Auth::user()->role === \App\Models\User::ADMINISTRATOR)<a href="{{ route('users.index') }}" class="overview-link">{{ __('Manage users') }} <span>&rarr;</span></a>@else<a href="{{ route('profile.edit') }}" class="overview-link">{{ __('View profile') }} <span>&rarr;</span></a>@endif
        </article>

        <article class="overview-card overview-card-ux">
            <div class="overview-card-header"><span class="overview-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.5 14.6 9l5.9.7-4.4 4 1.2 5.8-5.3-3-5.3 3 1.2-5.8-4.4-4L9.4 9z" /></svg></span><span class="overview-index">04 / 04</span></div>
            <p class="overview-card-label">{{ __('UI / UX refinements') }}</p>
            <strong class="overview-card-value">{{ $analytics['ux']['completed'] }}<small>/{{ $analytics['ux']['total'] }}</small></strong>
            <p class="overview-card-copy">{{ __('Interface improvements shipped') }}</p>
            <div class="ux-progress"><span style="width: {{ ($analytics['ux']['completed'] / $analytics['ux']['total']) * 100 }}%"></span></div>
            <div class="ux-status-row"><span><b>{{ $analytics['ux']['accessibility'] }}%</b>{{ __('Accessible') }}</span><span><b>{{ $analytics['ux']['responsive'] }}%</b>{{ __('Responsive') }}</span><span><b>{{ $analytics['ux']['issues'] }}</b>{{ __('Open issues') }}</span></div>
            <a href="#analytics" class="overview-link">{{ __('View improvements') }} <span>&rarr;</span></a>
        </article>
    </div>

    <div class="dashboard-quick-stats">
        <div><span class="quick-stat-icon">◈</span><p>{{ __('Total theses') }}</p><strong>{{ $metrics['total'] }}</strong><small>{{ __('all records') }}</small></div>
        <div><span class="quick-stat-icon quick-stat-purple">⌁</span><p>{{ __('Pending approvals') }}</p><strong>{{ $analytics['workflow']['pending'] + $analytics['workflow']['review'] }}</strong><small>{{ __('need attention') }}</small></div>
        <div><span class="quick-stat-icon quick-stat-green">⊙</span><p>{{ __('Active users') }}</p><strong>{{ $analytics['users']['active'] }}</strong><small>{{ __('verified accounts') }}</small></div>
        <div><span class="quick-stat-icon quick-stat-amber">↗</span><p>{{ __('Completion rate') }}</p><strong>{{ $analytics['approvalRate'] }}<small>%</small></strong><small>{{ __('approved or archived') }}</small></div>
    </div>

    <section id="analytics" class="analytics-panel">
        <div class="analytics-heading"><div><p class="workspace-eyebrow"><span></span>{{ __('Analytics') }}</p><h3>{{ __('Research activity over time') }}</h3><p>{{ __('A live view of submissions and workspace activity for the selected period.') }}</p></div><form method="GET" class="analytics-periods" aria-label="{{ __('Analytics period') }}">@foreach ($analytics['periodOptions'] as $option)<button type="submit" name="period" value="{{ $option['key'] }}" class="{{ $analytics['period'] === $option['key'] ? 'is-selected' : '' }}">{{ $option['label'] }}</button>@endforeach @if ($analytics['period'] === 'custom')<label class="analytics-date"><span>{{ __('From') }}</span><input type="date" name="start" value="{{ request('start') }}"></label><label class="analytics-date"><span>{{ __('To') }}</span><input type="date" name="end" value="{{ request('end') }}"></label><button type="submit" class="analytics-apply">{{ __('Apply') }}</button>@endif</form></div>
        <div class="analytics-chart-layout">
            <div class="trend-chart" x-data="{ hovered: null }">
                <div class="trend-chart-legend"><span><i class="legend-line legend-line-primary"></i>{{ __('Submissions') }}</span><span><i class="legend-line legend-line-secondary"></i>{{ __('Activity') }}</span><span class="trend-total">{{ array_sum($trendSubmissions) }} {{ __('submissions') }}</span></div>
                <div class="trend-chart-canvas"><div class="chart-y-labels"><span>{{ $trendMax }}</span><span>{{ (int) ceil($trendMax / 2) }}</span><span>0</span></div><svg viewBox="0 0 100 100" preserveAspectRatio="none" role="img" aria-label="{{ __('Submission and activity trend chart') }}"><defs><linearGradient id="trend-fill" x1="0" x2="0" y1="0" y2="1"><stop offset="0" stop-color="#1c8c80" stop-opacity=".26"/><stop offset="1" stop-color="#1c8c80" stop-opacity="0"/></linearGradient></defs><path class="chart-grid-line" d="M0 20H100M0 54H100M0 88H100"/><polygon points="0,88 {{ $trendPoints }} 100,88" fill="url(#trend-fill)"/><polyline points="{{ $trendPoints }}" class="trend-primary" vector-effect="non-scaling-stroke"/></svg>@foreach ($trendSubmissions as $index => $value)<button type="button" class="chart-point" style="left: {{ count($trendSubmissions) > 1 ? ($index / (count($trendSubmissions) - 1)) * 100 : 50 }}%; bottom: {{ ($value / $trendMax) * 68 + 12 }}%;" @mouseenter="hovered = {{ $index }}" @focus="hovered = {{ $index }}" @mouseleave="hovered = null" @blur="hovered = null" aria-label="{{ $trendLabels[$index] }}: {{ $value }} {{ __('submissions') }}"><span x-show="hovered === {{ $index }}" x-cloak>{{ $value }}</span></button>@endforeach</div><div class="chart-x-labels">@foreach ($trendLabels as $label)<span>{{ $label }}</span>@endforeach</div>
            </div>
            <div class="analytics-side-stats"><div class="side-stat-block"><span>{{ __('Workflow processing') }}</span><strong>{{ $analytics['workflow']['completed'] + $analytics['workflow']['approved'] }} <small>{{ __('completed') }}</small></strong><div class="side-stat-bar"><span style="width: {{ $analytics['approvalRate'] }}%"></span></div></div><div class="side-stat-block"><span>{{ __('Document health') }}</span><strong>{{ $analytics['documents']['approved'] }} <small>{{ __('approved docs') }}</small></strong><div class="side-stat-bar side-stat-bar-cyan"><span style="width: {{ $analytics['documents']['total'] > 0 ? ($analytics['documents']['approved'] / $analytics['documents']['total']) * 100 : 0 }}%"></span></div></div><div class="side-stat-block"><span>{{ __('User activity') }}</span><strong>{{ array_sum($trendActivity) }} <small>{{ __('logged actions') }}</small></strong><div class="side-stat-bar side-stat-bar-amber"><span style="width: {{ min(100, array_sum($trendActivity) * 5) }}%"></span></div></div></div>
        </div>
    </section>

    <div class="dashboard-bottom-grid">
        <section class="activity-panel"><div class="dashboard-subheading"><div><p class="workspace-eyebrow"><span></span>{{ __('Live feed') }}</p><h3>{{ __('Recent activity') }}</h3></div><span class="activity-pulse">{{ __('LIVE') }}</span></div>@forelse ($analytics['recentActivity'] as $activity)<div class="activity-row"><span class="activity-avatar">{{ str($activity->user?->name ?? 'S')->substr(0, 1)->upper() }}</span><div><strong>{{ $activity->action }}</strong><p>{{ $activity->thesis?->title ?? __('Workspace update') }}</p></div><time>{{ $activity->created_at?->diffForHumans() }}</time></div>@empty<div class="activity-empty">{{ __('No recent activity has been recorded yet.') }}</div>@endforelse</section>
        <section id="support" class="quick-actions-panel support-panel"><div class="dashboard-subheading"><div><p class="workspace-eyebrow"><span></span>{{ __('Human support') }}</p><h3>{{ __('Need a hand?') }}</h3></div><span class="support-status"><i></i>{{ __('Online') }}</span></div><p class="support-copy">{{ __('Questions about a review, document version, or account access? Our support desk can help you find the next step.') }}</p><a class="support-contact" href="mailto:gayatinrhamcisjames@gmail.com"><span class="support-contact-icon">@</span><span><small>{{ __('Contact support') }}</small><strong>gayatinrhamcisjames@gmail.com</strong></span><span class="support-arrow">&rarr;</span></a><div class="quick-action-grid"><a href="{{ route('theses.create') }}"><span>＋</span><strong>{{ __('Add research') }}</strong><small>{{ __('Start a new record') }}</small></a><a href="{{ route('theses.index') }}"><span>⌁</span><strong>{{ __('Review submissions') }}</strong><small>{{ __('Open thesis archive') }}</small></a>@if (Auth::user()->role === \App\Models\User::ADMINISTRATOR)<a href="{{ route('users.index') }}"><span>◎</span><strong>{{ __('Manage users') }}</strong><small>{{ __('Roles and access') }}</small></a>@endif<a href="#analytics"><span>↗</span><strong>{{ __('View analytics') }}</strong><small>{{ __('Change the period') }}</small></a></div></section>
    </div>
</section>
