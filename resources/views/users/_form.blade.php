<div class="space-y-8">
    <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ __('Account details') }}</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Enter the user information and assign their access role.') }}</p>
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name ?? '')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email ?? '')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected(old('role', $user->role ?? '') === $role)>{{ $role }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="workplace" :value="__('Workplace / Location')" />
            <x-text-input id="workplace" name="workplace" type="text" class="mt-1 block w-full" :value="old('workplace', $user->workplace ?? '')" autocomplete="organization" />
            <x-input-error :messages="$errors->get('workplace')" class="mt-2" />
        </div>
    </div>

    @if ($isCreate)
    <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ __('Password and security') }}</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Create a password for this user. Both fields are required.') }}</p>
        <div class="mt-5 grid gap-6 sm:grid-cols-2">
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" aria-describedby="password-help" />
                <p id="password-help" class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Use at least 8 characters.') }}</p>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm password')" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>
    </div>
    @endif

    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:items-center sm:justify-end dark:border-gray-700">
        <a href="{{ route('users.index') }}" class="inline-flex justify-center rounded-md px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">{{ __('Cancel') }}</a>
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
    </div>
</div>