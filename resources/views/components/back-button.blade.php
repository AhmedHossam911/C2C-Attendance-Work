@props(['href'])

<a href="{{ $href }}"
    {{ $attributes->merge(['class' => 'h-10 w-10 rounded-xl bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors']) }}>
    <i class="bi bi-arrow-left text-lg"></i>
</a>
