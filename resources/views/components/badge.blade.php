@props([
    'type' => null, // 'basic', 'extra' for task types
    'color' => null, // 'blue', 'purple', 'green', 'amber', 'red', 'slate'
    'size' => 'sm', // 'xs', 'sm', 'md'
    'pill' => true, // rounded-full or rounded
])

@php
    // Auto-detect color from type
    if ($type && !$color) {
        $color = match ($type) {
            'basic' => 'blue',
            'extra' => 'purple',
            default => 'slate',
        };
    }

    $colorClasses = match ($color ?? 'slate') {
        'blue'
            => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800/50',
        'purple'
            => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 border-purple-200 dark:border-purple-800/50',
        'green'
            => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800/50',
        'amber'
            => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800/50',
        'red' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-800/50',
        'teal'
            => 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400 border-teal-200 dark:border-teal-800/50',
        default
            => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700',
    };

    $sizeClasses = match ($size) {
        'xs' => 'px-1.5 py-0.5 text-[10px]',
        'sm' => 'px-2.5 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
        default => 'px-2.5 py-1 text-xs',
    };

    $roundedClass = $pill ? 'rounded-full' : 'rounded-lg';
@endphp

<span
    {{ $attributes->merge(['class' => "$colorClasses $sizeClasses $roundedClass font-bold uppercase tracking-wider border inline-flex items-center gap-1"]) }}>
    {{ $type ? ucfirst($type) : $slot }}
</span>
