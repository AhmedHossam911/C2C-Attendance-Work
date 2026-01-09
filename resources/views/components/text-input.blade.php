@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'w-full px-4 py-2.5 rounded-xl border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:bg-slate-50 dark:focus:bg-slate-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed placeholder-slate-500 dark:placeholder-slate-400',
]) !!}>
