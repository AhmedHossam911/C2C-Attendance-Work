@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span
                class="px-4 py-2 text-sm font-medium text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-not-allowed">
                <i class="bi bi-chevron-left mr-1"></i> Previous
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                class="px-4 py-2 text-sm font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-brand-blue transition-colors flex items-center shadow-sm">
                <i class="bi bi-chevron-left mr-1"></i> Previous
            </a>
        @endif

        {{-- Page Numbers (Simplified for mobile/compact) --}}
        <div class="hidden md:flex items-center gap-2">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-2 text-sm text-slate-500">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                class="w-9 h-9 flex items-center justify-center text-sm font-bold text-white bg-brand-blue rounded-xl shadow-md shadow-brand-blue/20">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="w-9 h-9 flex items-center justify-center text-sm font-bold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-brand-blue/30 transition-all">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Mobile Page Info --}}
        <div class="md:hidden text-xs text-slate-500 font-medium">
            Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                class="px-4 py-2 text-sm font-bold text-brand-blue bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-brand-blue hover:text-white transition-colors flex items-center shadow-sm group">
                Next <i class="bi bi-chevron-right ml-1 transition-transform group-hover:translate-x-1"></i>
            </a>
        @else
            <span
                class="px-4 py-2 text-sm font-medium text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-not-allowed">
                Next <i class="bi bi-chevron-right ml-1"></i>
            </span>
        @endif
    </nav>
@endif
