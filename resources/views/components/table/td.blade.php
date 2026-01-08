@props(['align' => 'left'])

@php
    $alignClass = match ($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
@endphp

<td
    {{ $attributes->merge(['class' => "px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300 $alignClass"]) }}>
    {{ $slot }}
</td>
