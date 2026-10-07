<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200">{{ __('Add User') }}</h2></x-slot>
    <div class="py-12"><div class="mx-auto max-w-3xl sm:px-6 lg:px-8"><section class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800"><form method="POST" action="{{ route('users.store') }}">@csrf @include('users._form', ['submitLabel' => __('Create user'), 'isCreate' => true])</form></section></div></div>
</x-app-layout>