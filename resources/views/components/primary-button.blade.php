<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-10 items-center justify-center whitespace-nowrap rounded-lg border border-transparent bg-teal-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition duration-150 ease-in-out hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600/30 focus:ring-offset-2 active:bg-teal-900 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-teal-500 dark:text-slate-950 dark:hover:bg-teal-400']) }}>
    {{ $slot }}
</button>
