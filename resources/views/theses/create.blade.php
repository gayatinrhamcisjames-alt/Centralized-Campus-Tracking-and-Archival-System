<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Add Thesis Record') }}</h2></x-slot>
    <div class="py-12"><div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8"><div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800"><form method="POST" action="{{ route('theses.store') }}" enctype="multipart/form-data">@include('theses._form', ['submitLabel' => __('Create thesis')])</form></div></div></div>
</x-app-layout>