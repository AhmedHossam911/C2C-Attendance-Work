@props(['header' => null, 'footer' => null, 'embedded' => false])

<div
    {{ $attributes->merge(['class' => 'bg-white dark:bg-[#1e293b] rounded-[1.5rem] shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden']) }}>
    @if ($header)
        <div
            class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
            {{ $header }}
        </div>
    @endif

    <div class="{{ $embedded ? 'p-0' : 'p-6' }}">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-t border-slate-100 dark:border-slate-800">
            {{ $footer }}
        </div>
    @endif
</div>
