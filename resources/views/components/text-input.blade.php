@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:border-c2c-blue-500 focus:ring-4 focus:ring-c2c-blue-500/10 focus:bg-slate-50 dark:focus:bg-slate-800 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed placeholder-slate-400 dark:placeholder-slate-500 shadow-sm',
]) !!}>
