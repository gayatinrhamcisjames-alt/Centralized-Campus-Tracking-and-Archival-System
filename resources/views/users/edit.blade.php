<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200">{{ __('Edit User') }}</h2></x-slot>
    <div class="py-12"><div class="mx-auto max-w-3xl sm:px-6 lg:px-8"><section class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800"><form method="POST" action="{{ route('users.update', $user) }}">@csrf @method('PUT') @include('users._form', ['submitLabel' => __('Save changes'), 'isCreate' => false])</form></section></div></div>
</x-app-layout>