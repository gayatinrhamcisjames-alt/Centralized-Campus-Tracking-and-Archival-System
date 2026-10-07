<section class="profile-edit-card">
    <form
        method="POST"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="profile-form"
    >
        @csrf
        @method('PATCH')

        {{-- Profile Photo --}}
        <div class="profile-photo-section">
            <div class="profile-photo-preview-wrap">
                <div
                    class="profile-photo-preview"
                    id="profile-photo-preview"
                    @if($user->profile_photo_path)
                        style="background-image: url('{{ asset('storage/' . $user->profile_photo_path) }}');"
                    @endif
                    :style="avatarPreview ? `background-image:url('${avatarPreview}')` : 'none'"
                >
                    <span id="profile-photo-initial" x-show="!avatarPreview">
                        {{ str($user->name)->substr(0, 1)->upper() }}
                    </span>
                </div>

                <span class="profile-photo-status">
                    <i></i>
                    {{ __('Active') }}
                </span>
            </div>

            <div class="profile-photo-content">
                <div class="profile-photo-heading">
                    <div>
                        <span class="settings-field-eyebrow">
                            {{ __('Profile photo') }}
                        </span>

                        <h4>{{ __('Make your profile recognizable.') }}</h4>

                        <p>
                            {{ __('Upload a clear photo so your collaborators can easily identify you across the workspace.') }}
                        </p>
                    </div>
                </div>

                <div class="profile-photo-actions">
                    <label
                        for="profile-photo"
                        class="feature-button feature-button-primary profile-upload-button"
                    >
                        <span>↑</span>
                        {{ $user->profile_photo_path ? __('Change photo') : __('Upload photo') }}
                    </label>

                    @if($user->profile_photo_path)
                        <button
                            type="button"
                            class="feature-button feature-button-secondary"
                            id="remove-profile-photo"
                            @click="removeProfilePhoto()"
                        >
                            {{ __('Remove') }}
                        </button>
                    @endif
                </div>

                <p class="profile-photo-hint">
                    {{ __('JPG, JPEG, PNG or WEBP · Maximum 2 MB') }}
                </p>

                <input
                    type="file"
                    name="profile_photo"
                    id="profile-photo"
                    accept="image/jpeg,image/png,image/webp"
                    class="sr-only"
                    x-ref="avatarInput"
                    @change="previewAvatar($event)"
                >

                <input
                    type="hidden"
                    name="remove_profile_photo"
                    id="remove-profile-photo-input"
                    value="0"
                >

                @error('profile_photo')
                    <p class="profile-field-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="profile-form-divider"></div>

        {{-- Name --}}
        <div class="profile-form-grid">

            <div class="settings-form-field">
                <label for="name">
                    {{ __('Full name') }}
                </label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $user->name) }}"
                    required
                    autocomplete="name"
                    class="settings-input"
                    placeholder="{{ __('Your full name') }}"
                >

                @error('name')
                    <p class="profile-field-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="settings-form-field">
                <label for="email">
                    {{ __('Email address') }}
                </label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    autocomplete="username"
                    class="settings-input"
                    placeholder="{{ __('you@example.com') }}"
                >

                @error('email')
                    <p class="profile-field-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Assigned role --}}
            <div class="settings-form-field">
                <label for="role">
                    {{ __('Assigned role') }}
                </label>

                <input
                    id="role"
                    type="text"
                    value="{{ $user->role }}"
                    class="settings-input"
                    readonly
                    aria-describedby="role-help"
                >

                <p id="role-help" class="text-sm text-slate-500">{{ __('Your assigned role is managed by a CCTAS administrator.') }}</p>
            </div>

            {{-- Workplace --}}
            <div class="settings-form-field">
                <label for="workplace">
                    {{ __('Workplace / Department') }}
                </label>

                <input
                    id="workplace"
                    name="workplace"
                    type="text"
                    value="{{ old('workplace', $user->workplace) }}"
                    class="settings-input"
                    placeholder="{{ __('Research & Development Department') }}"
                >

                @error('workplace')
                    <p class="profile-field-error">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Save area --}}
        <div class="profile-form-footer">

            <div class="profile-save-message">
                @if (session('status') === 'profile-updated')
                    <span class="profile-success-icon">✓</span>

                    <span>
                        {{ __('Profile updated successfully.') }}
                    </span>
                @else
                    <span class="profile-save-icon">✓</span>

                    <span>
                        {{ __('Your information is stored securely.') }}
                    </span>
                @endif
            </div>

            <button
                type="submit"
                class="feature-button feature-button-primary profile-save-button"
            >
                <span>{{ __('Save changes') }}</span>
                <span>→</span>
            </button>

        </div>
    </form>
</section>
