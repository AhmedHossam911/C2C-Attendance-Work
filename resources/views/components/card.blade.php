@props(['header' => null, 'footer' => null, 'embedded' => false])

<div {{ $attributes->merge(['class' => 'glass-card overflow-hidden']) }}>
    @if ($header)
        <div
            class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/50 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-800/50">
            {{ $header }}
        </div>
    @endif

    <div class="{{ $embedded ? 'p-0' : 'p-6' }}">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="bg-slate-50 dark:bg-slate-900/30 px-6 py-4 border-t border-slate-100 dark:border-slate-700/50">
            {{ $footer }}
        </div>
    @endif
</div>
