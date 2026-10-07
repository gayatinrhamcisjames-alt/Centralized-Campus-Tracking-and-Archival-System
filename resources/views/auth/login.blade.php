<x-guest-layout>
    <div x-data="authForm" class="auth-form-content">
        <x-auth-session-status class="auth-status" :status="session('status')" />
        <p class="auth-form-eyebrow">{{ __('Welcome back') }}</p>
        <h1>{{ __('Sign in to your workspace') }}</h1>
        <p class="auth-form-subtitle">{{ __('Sign in to continue to your account.') }}</p>

    <form method="POST" action="{{ route('login') }}" @submit="submit">
        @csrf

        <div class="auth-field">
            <x-input-label for="email" :value="__('Email')" />
            <div class="auth-input-wrap"><span class="auth-input-icon">@</span><x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" /></div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="auth-field">
            <x-input-label for="password" :value="__('Password')" />
            <div class="auth-input-wrap"><span class="auth-input-icon">*</span><x-text-input id="password" x-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="Enter your password" /><button type="button" class="auth-password-toggle" @click="showPassword = ! showPassword" :aria-label="showPassword ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'"><span x-text="showPassword ? '🙈' : '🙉'"></span></button></div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="auth-field">
            <x-input-label for="role" :value="__('Role')" class="auth-role-label" />
            <select id="role" name="role" class="auth-select auth-role-select" required>
                <option value="">{{ __('Select your role') }}</option>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected(old('role') === $role)>{{ $role }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="auth-options">
            <label for="remember_me" class="auth-check-label"><input id="remember_me" type="checkbox" name="remember"><span>{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="auth-inline-link" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
            @endif
        </div>

        <button type="submit" class="auth-submit" :disabled="submitting"><span x-show="!submitting">{{ __('Sign in') }}</span><span x-cloak x-show="submitting" class="auth-loading"><span class="auth-spinner"></span>{{ __('Signing in...') }}</span><span class="auth-button-arrow">&#8594;</span></button>
        <p class="auth-switch">{{ __('New to the workspace?') }} <a class="auth-inline-link" href="{{ route('register') }}">{{ __('Create an account') }}</a></p>
    </form>
    </div>
</x-guest-layout>
