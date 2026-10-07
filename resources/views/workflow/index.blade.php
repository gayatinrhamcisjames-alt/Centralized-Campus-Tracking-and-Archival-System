<x-app-layout>
    <x-slot name="header">
        <div class="feature-page-header">
            <div>
                <p class="feature-kicker"><span></span>{{ __('Control and transparency') }}</p>
                <h2>{{ __('Workflow & Approval') }}</h2>
                <p>{{ __('Track research submissions, reviews, revisions, and approvals in one centralized workspace.') }}</p>
            </div>
            <div class="feature-header-actions">
                <a href="{{ route('theses.create') }}" class="feature-button feature-button-primary"><span>＋</span>{{ __('New submission') }}</a>
                <a href="#workflow-history" class="feature-button feature-button-secondary">{{ __('View history') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="feature-page-shell" x-data="{ query: '', selected: null }">
        <section class="feature-kpi-grid" aria-label="{{ __('Workflow metrics') }}">
            @foreach ([['Total submissions', $metrics['total'], 'All research records', 'feature-kpi-indigo', '◫'], ['Pending review', $analytics['workflow']['pending'], 'Awaiting first review', 'feature-kpi-amber', '◌'], ['Under review', $analytics['workflow']['review'], 'Currently being reviewed', 'feature-kpi-purple', '⌁'], ['Approved', $analytics['workflow']['approved'], 'Ready to progress', 'feature-kpi-green', '✓'], ['Returned for revision', $analytics['workflow']['revision'], 'Needs researcher action', 'feature-kpi-rose', '↺']] as [$label, $value, $description, $tone, $icon])
                <article class="feature-kpi-card {{ $tone }}">
                    <div class="feature-kpi-icon">{{ $icon }}</div>
                    <p>{{ __($label) }}</p>
                    <strong>{{ $value }}</strong>
                    <small>{{ __($description) }}</small>
                </article>
            @endforeach
        </section>

        <section class="workflow-pipeline-panel">
            <div class="feature-section-heading"><div><p class="feature-kicker"><span></span>{{ __('Approval pipeline') }}</p><h3>{{ __('Every decision, clearly staged.') }}</h3><p>{{ __('See where research is moving and where attention is needed next.') }}</p></div><span class="feature-live-status"><i></i>{{ __('Live workflow') }}</span></div>
            <div class="workflow-pipeline">
                @foreach ([['Draft', $metrics['active'] - $analytics['workflow']['pending'] - $analytics['workflow']['review'] - $analytics['workflow']['revision'], 'Prepared'], ['Submitted', $analytics['workflow']['pending'], 'Awaiting review'], ['Under review', $analytics['workflow']['review'], 'In progress'], ['Revision', $analytics['workflow']['revision'], 'Researcher action'], ['Approved', $analytics['workflow']['approved'], 'Completed']] as [$stage, $count, $state])
                    <div class="workflow-stage"><div class="workflow-stage-marker"><span>{{ $loop->iteration }}</span></div><div class="workflow-stage-copy"><strong>{{ __($stage) }}</strong><b>{{ max(0, $count) }}</b><small>{{ __($state) }}</small></div></div>
                @endforeach
            </div>
        </section>

        <section class="feature-table-panel">
            <div class="feature-section-heading feature-table-heading"><div><p class="feature-kicker"><span></span>{{ __('Submission register') }}</p><h3>{{ __('Research submissions') }}</h3><p>{{ __('A focused queue for review, decisions, and follow-up.') }}</p></div><div class="feature-table-tools"><label class="feature-search"><span>⌕</span><input type="search" x-model="query" placeholder="{{ __('Search submissions...') }}" aria-label="{{ __('Search submissions') }}"></label><a href="{{ route('theses.index') }}" class="feature-button feature-button-secondary">{{ __('Open archive') }}</a></div></div>
            <div class="feature-table-wrap">
                <table class="feature-data-table">
                    <thead><tr><th>{{ __('Research title') }}</th><th>{{ __('Researcher') }}</th><th>{{ __('Reviewer') }}</th><th>{{ __('Status') }}</th><th>{{ __('Last updated') }}</th><th><span class="sr-only">{{ __('Actions') }}</span></th></tr></thead>
                    <tbody>
                        @forelse ($theses as $thesis)
                            <tr x-show="!query || '{{ strtolower($thesis->title.' '.$thesis->student->name.' '.$thesis->status) }}'.includes(query.toLowerCase())" x-transition.opacity>
                                <td><strong>{{ $thesis->title }}</strong><small>{{ $thesis->program }}</small></td>
                                <td>{{ $thesis->student->name }}</td>
                                <td>{{ $thesis->adviser?->name ?? __('Unassigned') }}</td>
                                <td><span class="feature-status feature-status-{{ str($thesis->status)->lower()->replace(' ', '-') }}">{{ $thesis->status }}</span></td>
                                <td><time>{{ $thesis->updated_at?->diffForHumans() ?? __('Recently') }}</time></td>
                                <td class="feature-table-action"><button type="button" class="feature-icon-button" @click="selected = selected === {{ $thesis->id }} ? null : {{ $thesis->id }}" aria-label="{{ __('View details') }}">→</button></td>
                            </tr>
                            <tr x-cloak x-show="selected === {{ $thesis->id }}" class="workflow-detail-row"><td colspan="6"><div class="workflow-detail-card"><div><p class="feature-kicker"><span></span>{{ __('Approval detail') }}</p><h4>{{ $thesis->title }}</h4><p>{{ __('Submitted by') }} {{ $thesis->student->name }} · {{ __('Adviser') }} {{ $thesis->adviser?->name ?? __('Unassigned') }}</p></div><div class="workflow-detail-actions"><a href="{{ route('theses.show', $thesis) }}" class="feature-button feature-button-primary">{{ __('View details') }}</a>@can('update', $thesis)<a href="{{ route('theses.edit', $thesis) }}" class="feature-button feature-button-secondary">{{ __('Add comment') }}</a>@endcan</div></div></td></tr>
                        @empty
                            <tr><td colspan="6" class="feature-empty-state">{{ __('No submissions are available for this workspace yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
