@props([
    'name',
    'size' => 'md', // 'xs', 'sm', 'md', 'lg'
    'gradient' => true,
])

@php
    $initials = collect(explode(' ', $name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('');

    $sizeClasses = match ($size) {
        'xs' => 'h-5 w-5 text-[8px]',
        'sm' => 'h-6 w-6 text-[10px]',
        'md' => 'h-8 w-8 text-xs',
        'lg' => 'h-10 w-10 text-sm',
        default => 'h-8 w-8 text-xs',
    };

    $bgClass = $gradient
        ? 'bg-gradient-to-br from-indigo-500 to-purple-500 text-white'
        : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300';
@endphp

<div {{ $attributes->merge(['class' => "$sizeClasses $bgClass rounded-full flex items-center justify-center font-bold shrink-0"]) }}
    title="{{ $name }}">
    {{ $initials }}
</div>
