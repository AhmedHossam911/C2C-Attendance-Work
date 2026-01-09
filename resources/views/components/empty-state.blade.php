@props([
    'icon' => 'bi-inbox',
    'title' => null,
    'message' => 'No items found',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-12 text-center']) }}>
    <div class="h-16 w-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
        <i class="bi {{ $icon }} text-3xl text-slate-400 dark:text-slate-500"></i>
    </div>
    @if ($title)
        <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-1">{{ $title }}</h4>
    @endif
    <p class="text-slate-500 dark:text-slate-400 text-sm max-w-xs">{{ $message }}</p>
    @if ($slot->isNotEmpty())
        <div class="mt-4">
            {{ $slot }}
        </div>
    @endif
</div>
