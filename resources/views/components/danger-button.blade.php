@props(['active' => false])

@php
    $tag = $attributes->has('href') ? 'a' : 'button';
    $defaultClasses =
        'inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 border border-transparent rounded-xl font-bold text-sm text-white tracking-wide shadow-md shadow-red-600/25 active:scale-95 transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $defaultClasses]) }}>
    {{ $slot }}
    </{{ $tag }}>
