<x-guest-layout>
    <div x-data="authForm" class="auth-form-content">
        <p class="auth-form-eyebrow">{{ __('Get started') }}</p>
        <h1>{{ __('Create your account') }}</h1>
        <p class="auth-form-subtitle">{{ __('Set up your secure research workspace in a few steps.') }}</p>
    <form method="POST" action="{{ route('register') }}" @submit="submit">
        @csrf

        <div class="auth-field">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="auth-field">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="auth-field">
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role" class="auth-select" required>
                <option value="">{{ __('Select your role') }}</option>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected(old('role', \App\Models\User::STUDENT) === $role)>{{ $role }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="auth-field">
            <x-input-label for="workplace" :value="__('Workplace / Location')" />
            <x-text-input id="workplace" class="block mt-1 w-full" type="text" name="workplace" :value="old('workplace')" autocomplete="organization" placeholder="e.g. Main Campus" />
            <x-input-error :messages="$errors->get('workplace')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="auth-field">
            <x-input-label for="password" :value="__('Password')" />
            <div class="auth-input-wrap"><span class="auth-input-icon">*</span><x-text-input id="password" x-model="password" x-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="Create a strong password" /><button type="button" class="auth-password-toggle" @click="showPassword = ! showPassword" :aria-label="showPassword ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'"><span x-text="showPassword ? '🙈' : '🙉'"></span></button></div>
            <div class="auth-strength"><div class="auth-strength-track"><span :class="strengthClass" :style="`width: ${strengthPercent}%`"></span></div><span x-text="strengthLabel"></span></div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="auth-field">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="auth-input-wrap"><span class="auth-input-icon">*</span><x-text-input id="password_confirmation" x-bind:type="showConfirmation ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" /><button type="button" class="auth-password-toggle" @click="showConfirmation = ! showConfirmation" :aria-label="showConfirmation ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'"><span x-text="showConfirmation ? '🙈' : '🙉'"></span></button></div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <label class="auth-check-label auth-terms"><input type="checkbox" required><span>{{ __('I agree to the account terms and privacy policy.') }}</span></label>
        <button type="submit" class="auth-submit" :disabled="submitting"><span x-show="!submitting">{{ __('Create account') }}</span><span x-cloak x-show="submitting" class="auth-loading"><span class="auth-spinner"></span>{{ __('Creating account...') }}</span><span class="auth-button-arrow">&#8594;</span></button>
        <p class="auth-switch">{{ __('Already have an account?') }} <a class="auth-inline-link" href="{{ route('login') }}">{{ __('Sign in') }}</a></p>
    </form>
    </div>
</x-guest-layout>
