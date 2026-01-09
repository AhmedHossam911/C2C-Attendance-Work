@props(['active' => false])

@php
    $tag = $attributes->has('href') ? 'a' : 'button';
    $defaultClasses =
        'inline-flex items-center px-5 py-2.5 bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl font-bold text-sm text-slate-800 dark:text-slate-100 tracking-wide shadow-sm hover:bg-slate-300 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:focus:ring-offset-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all cursor-pointer';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $defaultClasses]) }}>
    {{ $slot }}
    </{{ $tag }}>
