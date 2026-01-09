@props([
    'status' => 'pending', // 'pending', 'reviewed', 'overdue', 'late'
    'showIcon' => true,
])

@php
    $config = match ($status) {
        'reviewed' => [
            'color' =>
                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800/50',
            'icon' => 'bi-check-circle-fill',
            'label' => 'Reviewed',
        ],
        'overdue' => [
            'color' =>
                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-800/50',
            'icon' => 'bi-exclamation-circle-fill',
            'label' => 'Overdue',
        ],
        'late' => [
            'color' =>
                'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 border-red-100 dark:border-red-900/30',
            'icon' => 'bi-clock-fill',
            'label' => 'Late',
        ],
        default => [
            'color' =>
                'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800/50',
            'icon' => 'bi-hourglass-split',
            'label' => 'Pending',
        ],
    };
@endphp

<span
    {{ $attributes->merge(['class' => "{$config['color']} px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide border inline-flex items-center gap-1.5"]) }}>
    @if ($showIcon)
        <i class="bi {{ $config['icon'] }} text-[10px]"></i>
    @endif
    {{ $config['label'] }}
</span>
