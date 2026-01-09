@props(['active' => false])

@php
    $tag = $attributes->has('href') ? 'a' : 'button';
    $defaultClasses =
        'inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 border border-transparent rounded-xl font-bold text-sm text-white tracking-wide shadow-md shadow-blue-600/25 active:scale-95 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $defaultClasses]) }}>
    {{ $slot }}
    </{{ $tag }}>
