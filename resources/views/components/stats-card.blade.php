@props([
    'title',
    'value',
    'icon',
    'color' => 'blue', // blue, purple, green, orange, etc.
    'subValue' => null,
    'subLabel' => null,
])

@php
    $colors = [
        'blue' => [
            'bg' => 'bg-blue-500/10',
            'text' => 'text-blue-600 dark:text-blue-400',
            'label' => 'text-slate-500 dark:text-teal-300',
        ],
        'purple' => [
            'bg' => 'bg-purple-500/10',
            'text' => 'text-purple-600 dark:text-purple-400',
            'label' => 'text-slate-500 dark:text-teal-300',
        ],
        'green' => [
            'bg' => 'bg-green-500/10',
            'text' => 'text-green-600 dark:text-green-400',
            'label' => 'text-slate-500 dark:text-teal-300',
        ],
        'orange' => [
            'bg' => 'bg-orange-500/10',
            'text' => 'text-orange-600 dark:text-orange-400',
            'label' => 'text-slate-500 dark:text-teal-300',
        ],
        'brand-teal' => [
            'bg' => 'bg-teal-500/10',
            'text' => 'text-brand-teal dark:text-teal-400',
            'label' => 'text-slate-500 dark:text-teal-300',
        ],
        'brand-gold' => [
            'bg' => 'bg-yellow-500/10',
            'text' => 'text-brand-gold dark:text-yellow-400',
            'label' => 'text-slate-500 dark:text-teal-300',
        ],
        'slate' => [
            'bg' => 'bg-slate-300/10',
            'text' => 'text-slate-600 dark:text-slate-300',
            'label' => 'text-slate-500 dark:text-teal-300',
        ],
    ];

    $theme = $colors[$color] ?? $colors['blue'];
@endphp

<div
    class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center gap-4 group hover:-translate-y-1 hover:shadow-md transition-all duration-300">
    <div
        class="h-12 w-12 rounded-2xl {{ $theme['bg'] }} {{ $theme['text'] }} flex items-center justify-center text-xl shrink-0 group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
        <i class="{{ $icon }}"></i>
    </div>
    <div>
        <p class="{{ $theme['label'] }} text-xs font-bold uppercase tracking-wider mb-1">{{ $title }}</p>
        <h3 class="text-3xl font-bold text-slate-900 dark:text-white">{{ $value }}</h3>
        @if ($subValue)
            <p class="text-xs text-slate-400 mt-1">{{ $subValue }} {{ $subLabel }}</p>
        @endif
    </div>
</div>
