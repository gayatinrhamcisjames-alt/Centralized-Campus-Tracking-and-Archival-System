@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-semibold leading-5 text-teal-700 bg-teal-50 focus:outline-none focus:ring-2 focus:ring-teal-600/20 transition duration-150 ease-in-out dark:bg-teal-900/30 dark:text-teal-300'
            : 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium leading-5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-600/20 transition duration-150 ease-in-out dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} @if ($active ?? false) aria-current="page" @endif>
    {{ $slot }}
</a>
