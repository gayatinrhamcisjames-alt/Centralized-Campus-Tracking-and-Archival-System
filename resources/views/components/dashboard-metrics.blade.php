@props(['metrics'])

<div class="dashboard-metrics">
    @foreach ([['total', 'Total theses', '01'], ['active', 'Active', '02'], ['completed', 'Completed', '03'], ['archived', 'Archived', '04'], ['revisions', 'Pending revisions', '05']] as [$key, $label, $index])
        <div class="metric-card">
            <div class="metric-topline"><span>{{ $index }}</span><span class="metric-arrow">&nearr;</span></div>
            <p>{{ __($label) }}</p>
            <strong>{{ $metrics[$key] }}</strong>
        </div>
    @endforeach
</div>