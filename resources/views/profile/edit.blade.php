```blade
<x-app-layout>

    <x-slot name="header">
        <div class="feature-page-header">
            <div>
                <p class="feature-kicker">
                    <span></span>
                    {{ __('Your workspace identity') }}
                </p>

                <h2>{{ __('Profile & Settings') }}</h2>

                <p>
                    {{ __('Manage your account, security, preferences, and privacy from one calm control center.') }}
                </p>
            </div>

            <span class="settings-save-state">
                <i></i>
                {{ __('Account secured') }}
            </span>
        </div>
    </x-slot>


    {{-- =========================================================
         MAIN SETTINGS CONTAINER
    ========================================================== --}}
    <div
        class="settings-page-shell"
        x-data="profileSettings()"
        x-init="init()"
    >

        {{-- =====================================================
             PROFILE HERO CARD
        ====================================================== --}}
        <section class="profile-identity-card">

            <div
                class="profile-identity-glow"
                aria-hidden="true"
            ></div>


            {{-- Avatar --}}
            <div class="profile-avatar-wrap">

                <div
                    class="profile-avatar-large"
                    :class="{ 'has-preview': avatarPreview }"
                    :style="avatarPreview ? `background-image:url('${avatarPreview}')` : ''"
                >
                    <template x-if="!avatarPreview">
                        <span>
                            {{ str($user->name)->substr(0, 1)->upper() }}
                        </span>
                    </template>
                </div>

                <span
                    class="profile-online-dot"
                    aria-label="{{ __('Active now') }}"
                ></span>

            </div>


            {{-- Profile Information --}}
            <div class="profile-identity-copy">

                <p class="feature-kicker">
                    <span></span>
                    {{ __('Active member') }}
                </p>

                <h3>
                    {{ $user->name }}
                </h3>

                <div class="profile-meta-row">

                    <span class="profile-role-badge">
                        {{ $user->role }}
                    </span>

                    <span>
                        {{ $user->email }}
                    </span>

                </div>

                <p>
                    {{ $user->workplace ?: __('Research & Development Department') }}
                </p>

                <small>
                    {{ __('Member since') }}
                    {{ $user->created_at?->format('F Y') ?? now()->format('F Y') }}
                    ·
                    {{ __('Research operations workspace') }}
                </small>

            </div>


            {{-- Edit Button --}}
            <button
                type="button"
                class="feature-button feature-button-primary profile-edit-button"
                @click="openProfileEditor()"
            >
                <span>{{ __('Edit profile') }}</span>
                <span class="edit-arrow">→</span>
            </button>

        </section>



        {{-- =====================================================
             SETTINGS LAYOUT
        ====================================================== --}}
        <div class="settings-layout">


            {{-- =================================================
                 SIDEBAR
            ================================================== --}}
            <aside
                class="settings-navigation"
                aria-label="{{ __('Settings navigation') }}"
            >

                <p class="settings-nav-label">
                    {{ __('Account center') }}
                </p>


                @foreach ([
                    ['profile', '♙', 'Profile', 'Personal information'],
                    ['security', '⌾', 'Security', 'Password and access'],
                    ['notifications', '◌', 'Notifications', 'Stay informed'],
                    ['appearance', '◒', 'Appearance', 'Make it yours'],
                    ['language', '文', 'Language & Region', 'Localization'],
                    ['accessibility', '⊙', 'Accessibility', 'Comfort and access'],
                    ['connected', '↗', 'Connected Accounts', 'Your integrations'],
                    ['privacy', '◈', 'Privacy', 'Data visibility'],
                    ['danger', '!', 'Danger Zone', 'Irreversible actions']
                ] as [$key, $icon, $title, $description])

                    <button
                        type="button"
                        class="settings-nav-item"
                        :class="{ 'is-active': activePanel === '{{ $key }}' }"
                        @click="changePanel('{{ $key }}')"
                    >

                        <span class="settings-nav-icon">
                            {{ $icon }}
                        </span>

                        <span>
                            <strong>
                                {{ __($title) }}
                            </strong>

                            <small>
                                {{ __($description) }}
                            </small>
                        </span>

                    </button>

                @endforeach

            </aside>



            {{-- =================================================
                 MAIN CONTENT
            ================================================== --}}
            <main class="settings-content">


                {{-- =================================================
                     PROFILE
                ================================================== --}}
                <section
                    x-cloak
                    x-show="activePanel === 'profile'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="settings-panel"
                >

                    <div class="settings-panel-heading">

                        <div>

                            <p class="feature-kicker">
                                <span></span>
                                {{ __('Profile information') }}
                            </p>

                            <h3>
                                {{ __('Keep your identity current.') }}
                            </h3>

                            <p>
                                {{ __('This information helps your research team know who they are collaborating with.') }}
                            </p>

                        </div>

                        <span class="settings-panel-number">
                            01
                        </span>

                    </div>


                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Full name') }}</p>
                            <p class="mt-2 break-words text-base font-semibold text-slate-900">{{ $user->name }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Email address') }}</p>
                            <p class="mt-2 break-words text-base font-semibold text-slate-900">{{ $user->email }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Assigned role') }}</p>
                            <p class="mt-2 break-words text-base font-semibold text-slate-900">{{ $user->role }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('Workplace / Department') }}</p>
                            <p class="mt-2 break-words text-base font-semibold text-slate-900">{{ $user->workplace ?: __('Not specified') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col justify-between gap-4 rounded-2xl bg-slate-50 p-5 sm:flex-row sm:items-center">
                        <p class="text-sm leading-6 text-slate-600">{{ __('Keep your contact details current. Your assigned role is managed by a CCTAS administrator.') }}</p>
                        <button type="button" class="feature-button feature-button-primary shrink-0" @click="openProfileEditor()">
                            {{ __('Edit profile') }}
                        </button>
                    </div>

                </section>



                {{-- =================================================
                     SECURITY
                ================================================== --}}
                <section
                    x-cloak
                    x-show="activePanel === 'security'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="settings-panel"
                >

                    <div class="settings-panel-heading">

                        <div>

                            <p class="feature-kicker">
                                <span></span>
                                {{ __('Security center') }}
                            </p>

                            <h3>
                                {{ __('Protect your research workspace.') }}
                            </h3>

                            <p>
                                {{ __('Use a strong password and review account access regularly.') }}
                            </p>

                        </div>

                        <span class="settings-panel-number">
                            02
                        </span>

                    </div>


                    <div class="security-status-card">

                        <span class="security-status-icon">
                            ✓
                        </span>

                        <div>
                            <strong>
                                {{ __('Your account is protected') }}
                            </strong>

                            <p>
                                {{ __('Password authentication is active. Two-factor authentication can be connected when enabled by your workspace.') }}
                            </p>
                        </div>

                        <span class="security-status-badge">
                            {{ __('Healthy') }}
                        </span>

                    </div>


                    @include('profile.partials.update-password-form')


                    <div class="settings-info-grid">

                        <div>

                            <span class="settings-info-icon">
                                ◷
                            </span>

                            <div>

                                <strong>
                                    {{ __('Last password change') }}
                                </strong>

                                <small>
                                    {{ __('No recent password change recorded') }}
                                </small>

                            </div>

                        </div>


                        <div>

                            <span class="settings-info-icon settings-info-icon-purple">
                                ⌁
                            </span>

                            <div>

                                <strong>
                                    {{ __('Active session') }}
                                </strong>

                                <small>
                                    {{ __('This browser · Current device') }}
                                </small>

                            </div>

                        </div>

                    </div>

                </section>



                {{-- =================================================
                     NOTIFICATIONS
                ================================================== --}}
                <section
                    x-cloak
                    x-show="activePanel === 'notifications'"
                    x-transition
                    class="settings-panel"
                >

                    <div class="settings-panel-heading">

                        <div>

                            <p class="feature-kicker">
                                <span></span>
                                {{ __('Notification preferences') }}
                            </p>

                            <h3>
                                {{ __('Choose what reaches you.') }}
                            </h3>

                            <p>
                                {{ __('Keep important research moments visible without adding noise.') }}
                            </p>

                        </div>

                        <span class="settings-panel-number">
                            03
                        </span>

                    </div>


                    <div class="settings-toggle-group">

                        @foreach ([
                            ['Research & Documents', 'New submissions, document updates, and review comments'],
                            ['Workflow & Approvals', 'Approval requests, revisions, and status changes'],
                            ['System & Security', 'Security alerts, announcements, and maintenance']
                        ] as [$title, $description])

                            <div class="settings-toggle-row">

                                <div>

                                    <strong>
                                        {{ __($title) }}
                                    </strong>

                                    <small>
                                        {{ __($description) }}
                                    </small>

                                </div>

                                <label class="premium-toggle">

                                    <input
                                        type="checkbox"
                                        checked
                                    >

                                    <span></span>

                                </label>

                            </div>

                        @endforeach

                    </div>


                    <div class="settings-channel-strip">

                        <span>
                            {{ __('Delivery channels') }}
                        </span>

                        <button
                            type="button"
                            class="settings-channel is-selected"
                        >
                            {{ __('Email') }}
                        </button>

                        <button
                            type="button"
                            class="settings-channel is-selected"
                        >
                            {{ __('In-app') }}
                        </button>

                        <button
                            type="button"
                            class="settings-channel"
                        >
                            {{ __('Push') }}
                        </button>

                    </div>

                </section>



                {{-- =================================================
                     APPEARANCE
                ================================================== --}}
                <section
                    x-cloak
                    x-show="activePanel === 'appearance'"
                    x-transition
                    class="settings-panel"
                >

                    <div class="settings-panel-heading">

                        <div>

                            <p class="feature-kicker">
                                <span></span>
                                {{ __('Appearance') }}
                            </p>

                            <h3>
                                {{ __('A workspace that feels like yours.') }}
                            </h3>

                            <p>
                                {{ __('Choose the visual rhythm that helps you focus.') }}
                            </p>

                        </div>

                        <span class="settings-panel-number">
                            04
                        </span>

                    </div>


                    <div class="settings-choice-section">

                        <span class="settings-choice-label">
                            {{ __('Theme') }}
                        </span>


                        <div class="settings-choice-grid">

                            <button
                                type="button"
                                class="settings-choice-card is-selected"
                            >
                                <span>☼</span>
                                <strong>{{ __('Light') }}</strong>
                                <small>{{ __('Bright and focused') }}</small>
                            </button>

                            <button
                                type="button"
                                class="settings-choice-card"
                            >
                                <span>◐</span>
                                <strong>{{ __('Dark') }}</strong>
                                <small>{{ __('Quiet for late work') }}</small>
                            </button>

                            <button
                                type="button"
                                class="settings-choice-card"
                            >
                                <span>◌</span>
                                <strong>{{ __('System') }}</strong>
                                <small>{{ __('Follow device') }}</small>
                            </button>

                        </div>

                    </div>


                    <div class="settings-choice-section">

                        <span class="settings-choice-label">
                            {{ __('Accent color') }}
                        </span>

                        <div class="accent-swatches">

                            <button
                                type="button"
                                class="accent-swatch accent-swatch-indigo is-selected"
                                aria-label="{{ __('Indigo accent') }}"
                            ></button>

                            <button
                                type="button"
                                class="accent-swatch accent-swatch-cyan"
                                aria-label="{{ __('Cyan accent') }}"
                            ></button>

                            <button
                                type="button"
                                class="accent-swatch accent-swatch-emerald"
                                aria-label="{{ __('Emerald accent') }}"
                            ></button>

                            <button
                                type="button"
                                class="accent-swatch accent-swatch-rose"
                                aria-label="{{ __('Rose accent') }}"
                            ></button>

                        </div>

                    </div>

                </section>



                {{-- =================================================
                     LANGUAGE / ACCESSIBILITY
                ================================================== --}}
                <section
                    x-cloak
                    x-show="activePanel === 'language' || activePanel === 'accessibility'"
                    x-transition
                    class="settings-panel"
                >

                    <div class="settings-panel-heading">

                        <div>

                            <p class="feature-kicker">
                                <span></span>
                                {{ __('Workspace preferences') }}
                            </p>

                            <h3>
                                {{ __('Make every interaction comfortable.') }}
                            </h3>

                            <p>
                                {{ __('These controls are ready for your personal workflow and accessibility needs.') }}
                            </p>

                        </div>

                        <span class="settings-panel-number">
                            05
                        </span>

                    </div>


                    <div class="settings-preference-list">

                        <div>

                            <strong>
                                {{ __('Language & Region') }}
                            </strong>

                            <small>
                                {{ __('English · Asia/Manila · MMM D, YYYY') }}
                            </small>

                            <button
                                type="button"
                                class="feature-button feature-button-secondary"
                            >
                                {{ __('Configure') }}
                            </button>

                        </div>


                        <div>

                            <strong>
                                {{ __('Reduced motion') }}
                            </strong>

                            <small>
                                {{ __('Use fewer transitions and movement effects.') }}
                            </small>

                            <label class="premium-toggle">

                                <input type="checkbox">

                                <span></span>

                            </label>

                        </div>


                        <div>

                            <strong>
                                {{ __('Larger text') }}
                            </strong>

                            <small>
                                {{ __('Increase reading comfort across the workspace.') }}
                            </small>

                            <label class="premium-toggle">

                                <input type="checkbox">

                                <span></span>

                            </label>

                        </div>


                        <div>

                            <strong>
                                {{ __('High contrast') }}
                            </strong>

                            <small>
                                {{ __('Increase separation between surfaces and text.') }}
                            </small>

                            <label class="premium-toggle">

                                <input type="checkbox">

                                <span></span>

                            </label>

                        </div>


                        <div>

                            <strong>
                                {{ __('Keyboard navigation') }}
                            </strong>

                            <small>
                                {{ __('Keep every core action available without a mouse.') }}
                            </small>

                            <label class="premium-toggle">

                                <input
                                    type="checkbox"
                                    checked
                                >

                                <span></span>

                            </label>

                        </div>

                    </div>

                </section>



                {{-- =================================================
                     CONNECTED ACCOUNTS
                ================================================== --}}
                <section
                    x-cloak
                    x-show="activePanel === 'connected'"
                    x-transition
                    class="settings-panel"
                >

                    <div class="settings-panel-heading">

                        <div>

                            <p class="feature-kicker">
                                <span></span>
                                {{ __('Connected accounts') }}
                            </p>

                            <h3>
                                {{ __('Bring your tools together.') }}
                            </h3>

                            <p>
                                {{ __('Manage integrations without losing control of your account.') }}
                            </p>

                        </div>

                        <span class="settings-panel-number">
                            06
                        </span>

                    </div>


                    <div class="connected-account-list">

                        <div>

                            <span class="connected-account-icon">
                                G
                            </span>

                            <div>
                                <strong>Google Workspace</strong>
                                <small>{{ __('Not connected') }}</small>
                            </div>

                            <button
                                type="button"
                                class="feature-button feature-button-secondary"
                            >
                                {{ __('Connect') }}
                            </button>

                        </div>


                        <div>

                            <span class="connected-account-icon connected-account-icon-github">
                                ⌘
                            </span>

                            <div>
                                <strong>GitHub</strong>
                                <small>{{ __('Not connected') }}</small>
                            </div>

                            <button
                                type="button"
                                class="feature-button feature-button-secondary"
                            >
                                {{ __('Connect') }}
                            </button>

                        </div>

                    </div>

                </section>



                {{-- =================================================
                     PRIVACY
                ================================================== --}}
                <section
                    x-cloak
                    x-show="activePanel === 'privacy'"
                    x-transition
                    class="settings-panel"
                >

                    <div class="settings-panel-heading">

                        <div>

                            <p class="feature-kicker">
                                <span></span>
                                {{ __('Privacy controls') }}
                            </p>

                            <h3>
                                {{ __('Your work stays in your hands.') }}
                            </h3>

                            <p>
                                {{ __('Control how your profile and research activity are visible to collaborators.') }}
                            </p>

                        </div>

                        <span class="settings-panel-number">
                            07
                        </span>

                    </div>


                    <div class="settings-toggle-group">

                        <div class="settings-toggle-row">

                            <div>
                                <strong>
                                    {{ __('Profile visibility') }}
                                </strong>

                                <small>
                                    {{ __('Allow your name and role to appear to workspace members.') }}
                                </small>
                            </div>

                            <label class="premium-toggle">

                                <input
                                    type="checkbox"
                                    checked
                                >

                                <span></span>

                            </label>

                        </div>


                        <div class="settings-toggle-row">

                            <div>
                                <strong>
                                    {{ __('Activity visibility') }}
                                </strong>

                                <small>
                                    {{ __('Show your recent workspace actions to your research team.') }}
                                </small>
                            </div>

                            <label class="premium-toggle">

                                <input
                                    type="checkbox"
                                    checked
                                >

                                <span></span>

                            </label>

                        </div>

                    </div>


                    <a
                        href="#"
                        class="settings-data-action"
                    >

                        <span>⇩</span>

                        <div>
                            <strong>{{ __('Download my data') }}</strong>
                            <small>{{ __('Request a copy of your account information.') }}</small>
                        </div>

                        <b>→</b>

                    </a>

                </section>



                {{-- =================================================
                     DANGER ZONE
                ================================================== --}}
                <section
                    x-cloak
                    x-show="activePanel === 'danger'"
                    x-transition
                    class="settings-panel settings-danger-panel"
                >

                    <div class="settings-panel-heading">

                        <div>

                            <p class="feature-kicker">
                                <span></span>
                                {{ __('Danger zone') }}
                            </p>

                            <h3>
                                {{ __('Irreversible account actions.') }}
                            </h3>

                            <p>
                                {{ __('These actions affect your access and cannot be undone casually.') }}
                            </p>

                        </div>

                        <span class="settings-panel-number">
                            08
                        </span>

                    </div>


                    @include('profile.partials.delete-user-form')

                </section>



                {{-- =================================================
                     ACCOUNT ACTIVITY
                ================================================== --}}
                <section class="account-activity-panel">

                    <div class="settings-panel-heading">

                        <div>

                            <p class="feature-kicker">
                                <span></span>
                                {{ __('Account activity') }}
                            </p>

                            <h3>
                                {{ __('A clear record of your workspace.') }}
                            </h3>

                        </div>


                        <button
                            type="button"
                            class="feature-button feature-button-secondary"
                            @click="showAllActivity = !showAllActivity"
                            x-text="showAllActivity
                                ? '{{ __('Show less') }}'
                                : '{{ __('View all activity') }}'"
                        ></button>

                    </div>


                    <div class="account-timeline">

                        <div>

                            <span class="timeline-icon timeline-icon-green">
                                ✓
                            </span>

                            <div>
                                <strong>
                                    {{ __('Signed in to the workspace') }}
                                </strong>

                                <small>
                                    {{ __('Current browser · Today') }}
                                </small>
                            </div>

                        </div>


                        <div>

                            <span class="timeline-icon timeline-icon-indigo">
                                ◉
                            </span>

                            <div>
                                <strong>
                                    {{ __('Profile reviewed') }}
                                </strong>

                                <small>
                                    {{ __('Account settings · Recently') }}
                                </small>
                            </div>

                        </div>


                        <div
                            x-show="showAllActivity"
                            x-transition
                        >

                            <span class="timeline-icon timeline-icon-purple">
                                ⌁
                            </span>

                            <div>
                                <strong>
                                    {{ __('Research workspace opened') }}
                                </strong>

                                <small>
                                    {{ __('CCTAS platform · This month') }}
                                </small>
                            </div>

                        </div>

                    </div>

                </section>

            </main>

        </div>



        {{-- =========================================================
             SMOOTH EDIT PROFILE OVERLAY
        ========================================================== --}}
        <div
            x-cloak
            x-show="profileEditorOpen"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="profile-editor-overlay"
            @keydown.escape.window="closeProfileEditor()"
        >

            {{-- Background --}}
            <div
                class="profile-editor-backdrop"
                @click="closeProfileEditor()"
            ></div>


            {{-- Editor --}}
            <div
                class="profile-editor-modal"
                x-show="profileEditorOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                @click.stop
                role="dialog"
                aria-modal="true"
                aria-labelledby="edit-profile-title"
            >

                {{-- Modal Header --}}
                <div class="profile-editor-header">

                    <div>

                        <p class="feature-kicker">
                            <span></span>
                            {{ __('Personal information') }}
                        </p>

                        <h3 id="edit-profile-title">
                            {{ __('Edit your profile') }}
                        </h3>

                        <p>
                            {{ __('Update your information and keep your workspace profile current.') }}
                        </p>

                    </div>


                    <button
                        type="button"
                        class="profile-editor-close"
                        @click="closeProfileEditor()"
                        aria-label="{{ __('Close') }}"
                    >
                        ×
                    </button>

                </div>


                {{-- Avatar Preview --}}
                <div class="profile-editor-avatar-section">

                    <div
                        class="profile-editor-avatar"
                        :style="avatarPreview ? `background-image:url('${avatarPreview}')` : ''"
                    >

                        <template x-if="!avatarPreview">
                            <span>
                                {{ str($user->name)->substr(0, 1)->upper() }}
                            </span>
                        </template>

                    </div>


                    <div>

                        <strong>
                            {{ __('Profile photo') }}
                        </strong>

                        <p>
                            {{ __('Choose a clear JPG, PNG, or WebP image.') }}
                        </p>

                        <button
                            type="button"
                            class="profile-photo-button"
                            @click="$refs.avatarInput.click()"
                        >
                            {{ __('Change photo') }}
                        </button>

                    </div>

                </div>


                {{-- Profile update form --}}
                <div class="profile-editor-form">

                    @include('profile.partials.update-profile-information-form')

                </div>


                {{-- Footer --}}
                <div class="profile-editor-footer">

                    <button
                        type="button"
                        class="feature-button feature-button-secondary"
                        @click="closeProfileEditor()"
                    >
                        {{ __('Cancel') }}
                    </button>


                    <button
                        type="button"
                        class="feature-button feature-button-primary"
                        @click="submitProfileForm()"
                        :disabled="savingProfile"
                    >

                        <span
                            x-show="!savingProfile"
                            x-cloak
                        >
                            {{ __('Save changes') }}
                        </span>

                        <span
                            x-show="savingProfile"
                            x-cloak
                            class="profile-saving-state"
                        >
                            <span class="profile-spinner"></span>
                            {{ __('Saving...') }}
                        </span>

                    </button>

                </div>

            </div>

        </div>



        {{-- =========================================================
             SUCCESS MESSAGE
        ========================================================== --}}
        <div
            x-cloak
            x-show="showSuccess"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-3"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-3"
            class="profile-success-toast"
        >

            <span class="profile-success-icon">
                ✓
            </span>

            <div>

                <strong>
                    {{ __('Profile updated') }}
                </strong>

                <small>
                    {{ __('Your profile information has been saved successfully.') }}
                </small>

            </div>

            <button
                type="button"
                @click="showSuccess = false"
                aria-label="{{ __('Close') }}"
            >
                ×
            </button>

        </div>

    </div>



    {{-- =========================================================
         SMOOTH PROFILE SCRIPT
    ========================================================== --}}
    <script>

        function profileSettings() {

            return {

                activePanel: 'profile',

                profileEditorOpen: @js($errors->any()),

                avatarPreview: @js($user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : null),

                showAllActivity: false,

                savingProfile: false,

                showSuccess: @js(session('status') === 'profile-updated'),


                init() {

                    this.$nextTick(() => {

                        this.updateBodyScroll();

                    });

                    if (this.showSuccess) {

                        setTimeout(() => {

                            this.showSuccess = false;

                        }, 4500);

                    }

                },


                changePanel(panel) {

                    this.activePanel = panel;

                    if (panel !== 'profile') {

                        this.profileEditorOpen = false;

                    }

                    this.$nextTick(() => {

                        this.updateBodyScroll();

                    });

                },


                openProfileEditor() {

                    this.activePanel = 'profile';

                    this.profileEditorOpen = true;

                    this.updateBodyScroll();

                    this.$nextTick(() => {

                        const firstInput =
                            this.$root.querySelector(
                                '.profile-editor-modal input:not([type="hidden"]):not([type="file"])'
                            );

                        if (firstInput) {

                            setTimeout(() => {

                                firstInput.focus();

                            }, 250);

                        }

                    });

                },


                closeProfileEditor() {

                    if (this.savingProfile) {

                        return;

                    }

                    this.profileEditorOpen = false;

                    this.updateBodyScroll();

                },


                updateBodyScroll() {

                    if (this.profileEditorOpen) {

                        document.body.classList.add('profile-editor-open');

                    } else {

                        document.body.classList.remove('profile-editor-open');

                    }

                },


                previewAvatar(event) {

                    const file = event.target.files?.[0];

                    if (!file) {

                        return;

                    }


                    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {

                        alert(@js(__('Please choose a JPG, PNG, or WEBP image.')));
                        event.target.value = '';

                        return;
                    }

                    const maxSize = 2 * 1024 * 1024;

                    if (file.size > maxSize) {

                        alert(@js(__('Please choose an image smaller than 2 MB.')));

                        event.target.value = '';

                        return;

                    }


                    if (this.avatarPreview?.startsWith('blob:')) {

                        URL.revokeObjectURL(this.avatarPreview);

                    }


                    this.avatarPreview =
                        URL.createObjectURL(file);

                    const removeInput = this.$root.querySelector('#remove-profile-photo-input');

                    if (removeInput) {

                        removeInput.value = '0';

                    }

                },


                removeProfilePhoto() {

                    if (!confirm(@js(__('Remove your profile photo?')))) {

                        return;

                    }

                    this.avatarPreview = null;

                    const photoInput = this.$refs.avatarInput;
                    const removeInput = this.$root.querySelector('#remove-profile-photo-input');

                    photoInput.value = '';
                    removeInput.value = '1';


                },


                submitProfileForm() {

                    const form =
                        this.$root.querySelector('.profile-form');

                    if (!form) {

                        return;

                    }


                    this.savingProfile = true;


                    /*
                     * Submit the original Laravel form.
                     * This keeps your existing controller,
                     * validation and route working.
                     */

                    form.requestSubmit();

                }

            };

        }


        /*
         * Re-enable scrolling after leaving the page.
         */
        window.addEventListener('beforeunload', function () {

            document.body.classList.remove(
                'profile-editor-open'
            );

        });

    </script>



    {{-- =========================================================
         PROFILE EDITOR CSS
    ========================================================== --}}
    <style>

        [x-cloak] {
            display: none !important;
        }


        /* =====================================================
           BODY LOCK
        ====================================================== */

        body.profile-editor-open {
            overflow: hidden;
        }


        /* =====================================================
           EDIT BUTTON
        ====================================================== */

        .profile-edit-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition:
                transform .25s ease,
                box-shadow .25s ease,
                background .25s ease;
        }


        .profile-edit-button:hover {

            transform: translateY(-2px);

            box-shadow:
                0 12px 30px rgba(79, 70, 229, .20);

        }


        .edit-arrow {

            transition:
                transform .25s ease;

        }


        .profile-edit-button:hover .edit-arrow {

            transform: translateX(5px);

        }


        /* =====================================================
           AVATAR
        ====================================================== */

        .profile-avatar-large {

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            transition:
                background-image .25s ease,
                transform .25s ease;

        }


        .profile-avatar-large.has-preview {

            color: transparent;

        }


        .profile-avatar-upload {

            cursor: pointer;

            transition:
                transform .2s ease,
                box-shadow .2s ease;

        }


        .profile-avatar-upload:hover {

            transform: scale(1.1);

            box-shadow:
                0 8px 20px rgba(0, 0, 0, .15);

        }


        /* =====================================================
           PROFILE FORM
        ====================================================== */

        .profile-form-wrapper {

            animation:
                profileFormIn .35s ease both;

        }


        @keyframes profileFormIn {

            from {

                opacity: 0;

                transform:
                    translateY(8px);

            }

            to {

                opacity: 1;

                transform:
                    translateY(0);

            }

        }


        /* =====================================================
           OVERLAY
        ====================================================== */

        .profile-editor-overlay {

            position: fixed;

            inset: 0;

            z-index: 9999;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 24px;

        }


        .profile-editor-backdrop {

            position: absolute;

            inset: 0;

            background:
                rgba(15, 23, 42, .58);

            backdrop-filter:
                blur(8px);

            -webkit-backdrop-filter:
                blur(8px);

        }


        /* =====================================================
           MODAL
        ====================================================== */

        .profile-editor-modal {

            position: relative;

            width: min(760px, 100%);

            max-height: calc(100vh - 48px);

            overflow-y: auto;

            background:
                #ffffff;

            border:
                1px solid rgba(148, 163, 184, .20);

            border-radius:
                24px;

            box-shadow:
                0 30px 90px rgba(15, 23, 42, .25);

            scrollbar-width:
                thin;

        }


        .profile-editor-modal::-webkit-scrollbar {

            width: 7px;

        }


        .profile-editor-modal::-webkit-scrollbar-thumb {

            background:
                #cbd5e1;

            border-radius:
                20px;

        }


        /* =====================================================
           MODAL HEADER
        ====================================================== */

        .profile-editor-header {

            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            gap: 24px;

            padding: 28px 30px 22px;

            border-bottom:
                1px solid #eef2f7;

        }


        .profile-editor-header h3 {

            margin:
                5px 0 7px;

            font-size:
                25px;

            font-weight:
                750;

            color:
                #172033;

        }


        .profile-editor-header p:not(.feature-kicker) {

            margin:
                0;

            color:
                #64748b;

            line-height:
                1.6;

        }


        .profile-editor-close {

            width:
                38px;

            height:
                38px;

            flex:
                0 0 38px;

            border:
                0;

            border-radius:
                12px;

            background:
                #f1f5f9;

            color:
                #475569;

            font-size:
                25px;

            line-height:
                1;

            cursor:
                pointer;

            transition:
                all .2s ease;

        }


        .profile-editor-close:hover {

            background:
                #e2e8f0;

            color:
                #0f172a;

            transform:
                rotate(5deg);

        }


        /* =====================================================
           AVATAR EDITOR
        ====================================================== */

        .profile-editor-avatar-section {

            display: flex;

            align-items: center;

            gap: 18px;

            margin:
                24px 30px;

            padding:
                18px;

            border:
                1px solid #e8edf4;

            border-radius:
                18px;

            background:
                #f8fafc;

        }


        .profile-editor-avatar {

            width:
                76px;

            height:
                76px;

            flex:
                0 0 76px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                50%;

            background:
                linear-gradient(
                    135deg,
                    #6366f1,
                    #8b5cf6
                );

            background-size:
                cover;

            background-position:
                center;

            color:
                white;

            font-size:
                28px;

            font-weight:
                700;

            box-shadow:
                0 10px 25px rgba(79, 70, 229, .18);

        }


        .profile-editor-avatar-section strong {

            display:
                block;

            margin-bottom:
                4px;

            color:
                #172033;

        }


        .profile-editor-avatar-section p {

            margin:
                0 0 10px;

            font-size:
                13px;

            color:
                #64748b;

        }


        .profile-photo-button {

            border:
                0;

            padding:
                8px 13px;

            border-radius:
                9px;

            background:
                #eef2ff;

            color:
                #4f46e5;

            font-size:
                13px;

            font-weight:
                650;

            cursor:
                pointer;

            transition:
                all .2s ease;

        }


        .profile-photo-button:hover {

            background:
                #e0e7ff;

            transform:
                translateY(-1px);

        }


        /* =====================================================
           FORM AREA
        ====================================================== */

        .profile-editor-form {

            padding:
                0 30px 8px;

        }


        .profile-editor-form form {

            width:
                100%;

        }


        .profile-editor-form input,
        .profile-editor-form textarea,
        .profile-editor-form select {

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;

        }


        .profile-editor-form input:focus,
        .profile-editor-form textarea:focus,
        .profile-editor-form select:focus {

            outline:
                none;

            border-color:
                #6366f1;

            box-shadow:
                0 0 0 4px rgba(99, 102, 241, .10);

        }


        /* =====================================================
           FOOTER
        ====================================================== */

        .profile-editor-footer {

            display:
                flex;

            justify-content:
                flex-end;

            align-items:
                center;

            gap:
                12px;

            padding:
                20px 30px 26px;

            margin-top:
                12px;

            border-top:
                1px solid #eef2f7;

            background:
                rgba(248, 250, 252, .80);

        }


        .profile-editor-footer button {

            min-width:
                120px;

        }


        .profile-editor-footer button:disabled {

            opacity:
                .65;

            cursor:
                not-allowed;

            transform:
                none !important;

        }


        /* =====================================================
           SAVING
        ====================================================== */

        .profile-saving-state {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                9px;

        }


        .profile-spinner {

            width:
                15px;

            height:
                15px;

            border:
                2px solid rgba(255,255,255,.45);

            border-top-color:
                #ffffff;

            border-radius:
                50%;

            animation:
                profileSpin .7s linear infinite;

        }


        @keyframes profileSpin {

            to {
                transform:
                    rotate(360deg);
            }

        }


        /* =====================================================
           SUCCESS TOAST
        ====================================================== */

        .profile-success-toast {

            position:
                fixed;

            right:
                24px;

            bottom:
                24px;

            z-index:
                10000;

            display:
                flex;

            align-items:
                center;

            gap:
                13px;

            width:
                min(400px, calc(100vw - 32px));

            padding:
                15px 16px;

            background:
                #ffffff;

            border:
                1px solid #dcfce7;

            border-radius:
                16px;

            box-shadow:
                0 18px 45px rgba(15, 23, 42, .15);

        }


        .profile-success-icon {

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            width:
                36px;

            height:
                36px;

            flex:
                0 0 36px;

            border-radius:
                50%;

            background:
                #dcfce7;

            color:
                #16a34a;

            font-weight:
                800;

        }


        .profile-success-toast div {

            flex:
                1;

        }


        .profile-success-toast strong {

            display:
                block;

            color:
                #172033;

        }


        .profile-success-toast small {

            display:
                block;

            margin-top:
                2px;

            color:
                #64748b;

        }


        .profile-success-toast button {

            border:
                0;

            background:
                transparent;

            color:
                #94a3b8;

            font-size:
                20px;

            cursor:
                pointer;

        }


        /* =====================================================
           MOBILE
        ====================================================== */

        @media (max-width: 768px) {

            .profile-editor-overlay {

                align-items:
                    flex-end;

                padding:
                    0;

            }


            .profile-editor-modal {

                width:
                    100%;

                max-height:
                    92vh;

                border-radius:
                    24px 24px 0 0;

            }


            .profile-editor-header {

                padding:
                    22px 20px 18px;

            }


            .profile-editor-avatar-section {

                margin:
                    18px 20px;

            }


            .profile-editor-form {

                padding:
                    0 20px 8px;

            }


            .profile-editor-footer {

                padding:
                    16px 20px 20px;

            }


            .profile-editor-footer button {

                flex:
                    1;

            }


            .profile-success-toast {

                right:
                    16px;

                bottom:
                    16px;

            }

        }


        @media (max-width: 480px) {

            .profile-editor-avatar-section {

                align-items:
                    flex-start;

            }


            .profile-editor-avatar {

                width:
                    60px;

                height:
                    60px;

                flex-basis:
                    60px;

            }


            .profile-editor-header h3 {

                font-size:
                    21px;

            }

        }

    </style>

</x-app-layout>
```
