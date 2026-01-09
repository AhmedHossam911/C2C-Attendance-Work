@props(['header' => null, 'footer' => null, 'embedded' => false])

<div
    {{ $attributes->merge(['class' => 'bg-slate-200 dark:bg-slate-800 rounded-[1.5rem] shadow-sm border border-slate-300 dark:border-slate-700 overflow-hidden']) }}>
    @if ($header)
        <div
            class="px-6 py-5 border-b border-slate-300 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-400 dark:bg-slate-800/80">
            {{ $header }}
        </div>
    @endif

    <div class="{{ $embedded ? 'p-0' : 'p-6' }}">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="bg-slate-400 dark:bg-slate-900/50 px-6 py-4 border-t border-slate-300 dark:border-slate-700">
            {{ $footer }}
        </div>
    @endif
</div>
