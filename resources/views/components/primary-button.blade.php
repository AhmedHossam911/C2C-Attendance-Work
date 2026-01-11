@props(['active' => false])

@php
    $tag = $attributes->has('href') ? 'a' : 'button';
    $defaultClasses =
        'inline-flex items-center px-5 py-2.5 bg-gradient-brand hover:opacity-90 border border-transparent rounded-xl font-bold text-sm text-white tracking-wide shadow-md shadow-c2c-blue-500/20 active:scale-95 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-c2c-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer hover-lift';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $defaultClasses]) }}>
    {{ $slot }}
    </{{ $tag }}>
