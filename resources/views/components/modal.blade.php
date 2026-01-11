@props(['name', 'title' => '', 'maxWidth' => '2xl'])

@php
    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth];
@endphp

<div x-data="{ show: false, name: '{{ $name }}' }" x-show="show" x-on:open-modal.window="if ($event.detail === name) show = true"
    x-on:close-modal.window="if ($event.detail === name) show = false" x-on:keydown.escape.window="show = false"
    style="display: none;" class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0" x-cloak>

    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-slate-900/75 backdrop-blur-sm"></div>
    </div>

    <div x-show="show"
        class="mb-6 bg-slate-50 dark:bg-[#1e293b] rounded-2xl overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto {{ $maxWidth }}"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

        @if ($title)
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                    {{ $title }}
                </h3>
                <button @click="show = false"
                    class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 transition-colors">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        <div class="px-6 py-4">
            {{ $slot }}
        </div>
    </div>
</div>
