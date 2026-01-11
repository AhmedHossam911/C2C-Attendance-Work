@props(['align' => 'left', 'tooltip' => false])

@php
    $alignClass = match ($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
@endphp

<td
    {{ $attributes->merge(['class' => "px-6 py-5 text-sm text-slate-700 dark:text-slate-300 $alignClass border-b border-slate-100 dark:border-slate-800/50 align-top leading-relaxed break-words hyphens-auto"]) }}>
    @if ($tooltip)
        <div class="relative group truncate max-w-[200px] md:max-w-[300px]">
            <span
                class="cursor-help border-b border-dotted border-slate-400 dark:border-slate-500">{{ $slot }}</span>
            <div
                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-max max-w-xs p-2 bg-slate-800 dark:bg-slate-700 text-white text-xs rounded-lg shadow-xl z-50 pointer-events-none whitespace-normal leading-normal">
                {{ strip_tags((string) $slot) }}
                <!-- Arrow -->
                <div
                    class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-slate-800 dark:border-t-slate-700">
                </div>
            </div>
        </div>
    @else
        {{ $slot }}
    @endif
</td>
