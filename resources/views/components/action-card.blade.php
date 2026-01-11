@props(['href', 'icon', 'title', 'description', 'color' => 'blue', 'rotate' => false])

@php
    $colors = [
        'blue' => 'bg-blue-500/10 text-blue-600',
        'orange' => 'bg-orange-500/10 text-orange-600',
        'teal' => 'bg-brand-teal/10 text-brand-teal',
        'indigo' => 'bg-indigo-500/10 text-indigo-600',
        'amber' => 'bg-amber-500/10 text-amber-600',
        'purple' => 'bg-purple-500/10 text-purple-600',
        'brand-blue' => 'bg-brand-blue/10 text-brand-blue',
    ];

    $bgClass = 'bg-white dark:bg-slate-800';
    // Logic for alternate background colors if needed, but sticking to standard for now.
    // Some cards in dashboard had bg-slate-300 for members/committees, handled via classes or maybe a prop?
    // The original code had:
    // Admin: bg-white
    // Committee Head: Tasks (white), Reports (slate-300), Reviews (slate-300)
    // Member: My Tasks (slate-300), My Sessions (slate-300)

    // To keep it simple, I'll default to white/dark, but allow overriding class.
@endphp

<a href="{{ $href }}"
    {{ $attributes->merge(['class' => 'group relative overflow-hidden bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-100 dark:border-slate-700 shadow-sm transition-all hover:shadow-md hover:-translate-y-1']) }}>
    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
        <i class="{{ $icon }} text-8xl transform {{ $rotate ? '-rotate-12' : 'rotate-12' }}"></i>
    </div>
    <div class="relative z-10 flex items-center gap-4">
        <div
            class="h-14 w-14 rounded-2xl {{ $colors[$color] ?? $colors['blue'] }} flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <i class="{{ $icon }}"></i>
        </div>
        <div>
            <h4 class="text-lg font-bold text-slate-900 dark:text-white">{{ $title }}</h4>
            <p class="text-sm text-slate-500 dark:text-teal-300">{{ $description }}</p>
        </div>
    </div>
</a>
