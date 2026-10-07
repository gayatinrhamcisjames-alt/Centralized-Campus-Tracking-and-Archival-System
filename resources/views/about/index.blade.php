<x-app-layout>
    <x-slot name="header">
        <div class="feature-page-header support-page-header">
            <div>
                <p class="feature-kicker"><span></span>{{ __('About our platform') }}</p>
                <h2>{{ __('About Center') }}</h2>
                <p>{{ __('Centralized Campus Tracking and Archival System (CCTAS) brings campus research records, review activity, and archival workflows together in one role-aware workspace.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="feature-page-shell about-page-shell">
        <section class="about-hero-panel">
            <div class="about-hero-copy">
                <p class="feature-kicker"><span></span>{{ __('Who we are') }}</p>
                <h3>{{ __('A clear, connected home for campus research records.') }}</h3>
                <p>
                    {{ __('CCTAS helps students, advisers, panel members, coordinators, staff, and administrators follow research work from submission and review through completion and archival. Records, milestones, documents, and decisions stay connected to the people responsible for the next step.') }}
                </p>
                <div class="about-hero-actions">
                    <a href="{{ route('dashboard') }}" class="feature-button feature-button-primary">{{ __('Open dashboard') }}</a>
                    <a href="{{ route('support.index') }}" class="feature-button feature-button-secondary">{{ __('Help & support') }}</a>
                </div>
            </div>
            <div class="about-hero-metrics">
                <div class="about-metric-card">
                    <strong>01</strong>
                    <span>{{ __('Centralized research records') }}</span>
                </div>
                <div class="about-metric-card">
                    <strong>02</strong>
                    <span>{{ __('Traceable review workflow') }}</span>
                </div>
                <div class="about-metric-card">
                    <strong>03</strong>
                    <span>{{ __('Organized digital archive') }}</span>
                </div>
            </div>
        </section>

        <section class="about-grid">
            <article class="about-panel">
                <p class="feature-kicker"><span></span>{{ __('Mission') }}</p>
                <h4>{{ __('Make research progress easier to follow.') }}</h4>
                <p>{{ __('Our mission is to provide one dependable place to submit research, record review decisions, track milestones, and retain completed work for future reference.') }}</p>
            </article>
            <article class="about-panel">
                <p class="feature-kicker"><span></span>{{ __('Vision') }}</p>
                <h4>{{ __('A connected process for every contributor.') }}</h4>
                <p>{{ __('Our vision is a campus research process where students and academic teams can understand a project’s status, its recorded feedback, and the next action appropriate to their role.') }}</p>
            </article>
            <article class="about-panel">
                <p class="feature-kicker"><span></span>{{ __('Values') }}</p>
                <h4>{{ __('Accuracy, accountability, and access by role.') }}</h4>
                <p>{{ __('CCTAS is organized around clear records, documented workflow activity, and user roles that help each participant reach the information and actions relevant to their work.') }}</p>
            </article>
        </section>

        <section class="about-story-panel">
            <div class="feature-section-heading">
                <div>
                    <p class="feature-kicker"><span></span>{{ __('What we deliver') }}</p>
                    <h3>{{ __('Tools that support each stage of campus research.') }}</h3>
                </div>
            </div>
            <div class="about-story-grid">
                <div>
                    <ul class="about-feature-list">
                        <li><span>✓</span>{{ __('Centralized thesis and research records') }}</li>
                        <li><span>✓</span>{{ __('Document submission, review, and version history') }}</li>
                        <li><span>✓</span>{{ __('Milestone and approval workflow tracking') }}</li>
                        <li><span>✓</span>{{ __('Role-based workspace access and user management') }}</li>
                    </ul>
                </div>
                <div>
                    <ul class="about-feature-list">
                        <li><span>✓</span>{{ __('Activity history for recorded research decisions') }}</li>
                        <li><span>✓</span>{{ __('Research status summaries and dashboard insights') }}</li>
                        <li><span>✓</span>{{ __('An archive for completed research records') }}</li>
                        <li><span>✓</span>{{ __('Account settings and help resources for users') }}</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="about-team-panel">
            <div class="feature-section-heading">
                <div>
                    <p class="feature-kicker"><span></span>{{ __('Platform focus') }}</p>
                    <h3>{{ __('A shared process with clear responsibilities.') }}</h3>
                </div>
            </div>
            <div class="about-pill-row">
                <span>{{ __('Research administration') }}</span>
                <span>{{ __('Collaboration') }}</span>
                <span>{{ __('Review workflow') }}</span>
                <span>{{ __('Document control') }}</span>
                <span>{{ __('Access management') }}</span>
                <span>{{ __('Research archive') }}</span>
            </div>
        </section>

        <section class="about-faq-panel">
            <div class="feature-section-heading">
                <div>
                    <p class="feature-kicker"><span></span>{{ __('Frequently asked') }}</p>
                    <h3>{{ __('How the platform supports your work.') }}</h3>
                </div>
            </div>

            <div class="support-faq-list" x-data="{ open: 0 }">
                @foreach([
                    ['What does CCTAS stand for?', 'CCTAS means Centralized Campus Tracking and Archival System. It organizes campus research records and supports their review, progress tracking, and archival.'],
                    ['Who can use the system?', 'The workspace supports students, faculty advisers, panel members, thesis coordinators, staff, and administrators. Available actions depend on the role assigned to each account.'],
                    ['What happens during a research review?', 'Research records move through the configured workflow. Reviewers can record feedback and decisions, while milestones and activity history help participants follow progress and next steps.'],
                    ['Can I change my assigned role in Profile Settings?', 'No. Profile Settings lets you update your personal account details, but assigned roles are managed by a CCTAS administrator to preserve appropriate access.'],
                    ['How do I find completed work?', 'Use the Research Archive in the workspace navigation to browse records that have been completed and archived. Access to records follows the permissions configured for your account.'],
                    ['Where can I get help using CCTAS?', 'Open Help & Support from the workspace navigation for guidance on common account, research, document, and workflow questions.'],
                ] as [$question, $answer])
                    <div class="support-faq-item">
                        <button type="button" @click="open = open === {{ $loop->index }} ? null : {{ $loop->index }}" :aria-expanded="(open === {{ $loop->index }}).toString()">
                            <span>{{ __($question) }}</span>
                            <b :class="{ 'is-open': open === {{ $loop->index }} }">+</b>
                        </button>
                        <div x-cloak x-show="open === {{ $loop->index }}" x-transition class="support-faq-answer">
                            <p>{{ __($answer) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
