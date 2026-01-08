@props(['align' => 'left'])

@php
    $alignClass = match ($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
@endphp

<th
    {{ $attributes->merge(['class' => "px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider $alignClass"]) }}>
    {{ $slot }}
</th>
