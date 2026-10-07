<x-app-layout>
    <x-slot name="header">
        <div class="feature-page-header">
            <div>
                <p class="feature-kicker"><span></span>{{ __('Intelligence and visibility') }}</p>
                <h2>{{ __('Analytics & Insights') }}</h2>
                <p>{{ __('Monitor research activity, approval performance, user engagement, and system trends.') }}</p>
            </div>
            <div class="feature-header-actions"><a href="#analytics-chart" class="feature-button feature-button-primary">{{ __('Export report') }}</a><a href="{{ route('analytics.index', ['period' => $analytics['period']]) }}" class="feature-button feature-button-secondary">↻ {{ __('Refresh data') }}</a></div>
        </div>
    </x-slot>

    @php
        $chartLabels = array_slice($analytics['trend']['labels'], -12);
        $chartValues = array_slice($analytics['trend']['submissions'], -12);
        $chartMax = max(max($chartValues ?: [0]), 1);
        $chartPoints = collect($chartValues)->map(function (int $value, int $index) use ($chartMax, $chartValues): string {
            $x = count($chartValues) > 1 ? ($index / (count($chartValues) - 1)) * 100 : 50;
            $y = 88 - (($value / $chartMax) * 68);
            return number_format($x, 2).','.number_format($y, 2);
        })->implode(' ');
    @endphp

    <div class="feature-page-shell">
        <div class="analytics-toolbar"><div><span class="feature-toolbar-label">{{ __('Reporting period') }}</span><strong>{{ $analytics['periodLabel'] }}</strong></div><form method="GET" class="analytics-periods">@foreach ($analytics['periodOptions'] as $option)<button type="submit" name="period" value="{{ $option['key'] }}" class="{{ $analytics['period'] === $option['key'] ? 'is-selected' : '' }}">{{ $option['label'] }}</button>@endforeach</form></div>

        <section class="feature-kpi-grid analytics-kpi-grid" aria-label="{{ __('Analytics metrics') }}">
            @foreach ([['Research submissions', $metrics['total'], __('Total records in scope'), 'feature-kpi-indigo', '◫'], ['Approval rate', $analytics['approvalRate'].'%', __('Approved or archived'), 'feature-kpi-green', '↗'], ['Average review time', '—', __('Needs review history data'), 'feature-kpi-purple', '◷'], ['Active users', $analytics['users']['active'], __('Verified accounts'), 'feature-kpi-cyan', '◎']] as [$label, $value, $description, $tone, $icon])
                <article class="feature-kpi-card {{ $tone }}"><div class="feature-kpi-icon">{{ $icon }}</div><p>{{ __($label) }}</p><strong>{{ $value }}</strong><small>{{ $description }}</small></article>
            @endforeach
        </section>

        <section id="analytics-chart" class="analytics-main-panel">
            <div class="feature-section-heading"><div><p class="feature-kicker"><span></span>{{ __('Research activity') }}</p><h3>{{ __('Submission momentum over time') }}</h3><p>{{ __('Compare research submissions across the selected reporting period.') }}</p></div><span class="analytics-total"><strong>{{ array_sum($chartValues) }}</strong> {{ __('submissions') }}</span></div>
            <div class="analytics-chart-large"><div class="analytics-chart-y"><span>{{ $chartMax }}</span><span>{{ (int) ceil($chartMax / 2) }}</span><span>0</span></div><svg viewBox="0 0 100 100" preserveAspectRatio="none" role="img" aria-label="{{ __('Research submissions over time') }}"><defs><linearGradient id="analytics-fill" x1="0" x2="0" y1="0" y2="1"><stop offset="0" stop-color="#6366f1" stop-opacity=".24"/><stop offset="1" stop-color="#6366f1" stop-opacity="0"/></linearGradient></defs><path class="analytics-grid-line" d="M0 20H100M0 54H100M0 88H100"/><polygon points="0,88 {{ $chartPoints }} 100,88" fill="url(#analytics-fill)"/><polyline points="{{ $chartPoints }}" class="analytics-line" vector-effect="non-scaling-stroke"/></svg><div class="analytics-chart-x">@foreach ($chartLabels as $label)<span>{{ $label }}</span>@endforeach</div></div>
        </section>

        <div class="analytics-insight-grid">
            <section class="analytics-breakdown-panel"><div class="feature-section-heading"><div><p class="feature-kicker"><span></span>{{ __('Approval performance') }}</p><h3>{{ __('Decision health') }}</h3></div></div><div class="approval-ring" style="--approval: {{ $analytics['approvalRate'] }}%"><strong>{{ $analytics['approvalRate'] }}<small>%</small></strong><span>{{ __('approved') }}</span></div><div class="approval-legend">@foreach ([['Approved', $analytics['workflow']['approved'], 'green'], ['Pending', $analytics['workflow']['pending'], 'amber'], ['Under review', $analytics['workflow']['review'], 'purple'], ['Revision required', $analytics['workflow']['revision'], 'rose']] as [$label, $value, $color])<div><span><i class="legend-dot legend-dot-{{ $color }}"></i>{{ __($label) }}</span><strong>{{ $value }}</strong></div>@endforeach</div></section>
            <section class="analytics-breakdown-panel"><div class="feature-section-heading"><div><p class="feature-kicker"><span></span>{{ __('Workflow efficiency') }}</p><h3>{{ __('Stage visibility') }}</h3></div></div><div class="efficiency-list">@foreach ([['Submission', $analytics['documents']['submitted'], 'cyan'], ['Review', $analytics['workflow']['review'], 'purple'], ['Revision', $analytics['workflow']['revision'], 'amber'], ['Approval', $analytics['workflow']['approved'], 'green']] as [$label, $value, $color])<div><div><span>{{ __($label) }}</span><strong>{{ $value }} {{ __('records') }}</strong></div><span class="efficiency-track efficiency-track-{{ $color }}"><i style="width: {{ $metrics['total'] > 0 ? min(100, ($value / $metrics['total']) * 100) : 0 }}%"></i></span></div>@endforeach</div></section>
        </div>

        <section class="analytics-insights-strip"><div><span class="insight-icon insight-icon-indigo">↗</span><div><strong>{{ __('Research activity') }}</strong><p>{{ $analytics['documents']['recent'] }} {{ __('documents were updated in the selected period.') }}</p></div></div><div><span class="insight-icon insight-icon-amber">!</span><div><strong>{{ __('Approval activity') }}</strong><p>{{ $analytics['workflow']['pending'] + $analytics['workflow']['review'] }} {{ __('submissions are currently awaiting review or a decision.') }}</p></div></div><div><span class="insight-icon insight-icon-cyan">◎</span><div><strong>{{ __('User engagement') }}</strong><p>{{ array_sum($analytics['trend']['activity']) }} {{ __('logged actions were recorded in this period.') }}</p></div></div></section>
    </div>
</x-app-layout>
