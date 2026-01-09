@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('reports.index') }}" class="hover:text-brand-blue transition-colors font-medium">Reports</a>
                <i class="bi bi-chevron-right text-xs opacity-50"></i>
                <span class="text-slate-800 dark:text-slate-200">Ghost Members</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Ghost Members</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-xl">
                Identifying members with 0% attendance and 0 task submissions.
            </p>
        </div>

        {{-- Filters: Search + Committee --}}
        <div class="w-full md:w-auto flex flex-col md:flex-row gap-4">
            <form action="{{ route('reports.ghost_members') }}" method="GET" class="contents">
                {{-- Search Input --}}
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ghosts..."
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:border-brand-blue focus:ring-brand-blue shadow-sm py-2.5 pl-10 pr-4 text-sm"
                        onkeydown="if(event.key === 'Enter') this.form.submit()">
                </div>

                {{-- Committee Select --}}
                <div class="relative w-full md:w-64">
                    <select name="committee_id" onchange="this.form.submit()"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:border-brand-blue focus:ring-brand-blue shadow-sm py-2.5 pl-4 pr-10 appearance-none cursor-pointer text-sm font-medium">
                        <option value="">All Committees</option>
                        @foreach ($committees as $committee)
                            <option value="{{ $committee->id }}"
                                {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                                {{ $committee->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                        <i class="bi bi-chevron-down text-xs"></i>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($ghosts->isEmpty())
        <x-card
            class="flex flex-col items-center justify-center py-16 text-center border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
            <div
                class="inline-flex h-20 w-20 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 items-center justify-center mb-6 shadow-sm">
                <i class="bi bi-check-lg text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100">Clean Sheet!</h3>
            <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-2">
                No ghost members found in this selection. Everyone is active!
            </p>
        </x-card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($ghosts as $user)
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow group relative overflow-hidden flex flex-col justify-between h-full">
                    {{-- Ghost Indicator --}}
                    <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="bi bi-ghost text-6xl text-slate-800 dark:text-slate-100"></i>
                    </div>

                    <div>
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3 relative z-10">
                                <div
                                    class="h-12 w-12 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-lg font-bold text-slate-500 shadow-inner">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4
                                        class="font-bold text-slate-800 dark:text-slate-100 line-clamp-1 text-base group-hover:text-brand-blue transition-colors">
                                        {{ $user->name }}
                                    </h4>
                                    <p class="text-xs text-slate-500 font-mono truncate max-w-[140px]"
                                        title="{{ $user->email }}">
                                        {{ $user->email }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Committees Badges --}}
                        <div class="mb-4 relative z-10 min-h-[44px]">
                            <p class="text-[10px] uppercase font-bold text-slate-400 mb-2 tracking-wider">Committees</p>
                            @if ($user->committees->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($user->committees as $committee)
                                        <span
                                            class="inline-block px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                                            {{ $committee->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-slate-400 italic text-xs">Unassigned</span>
                            @endif
                        </div>

                        {{-- Stats / Info --}}
                        <div class="grid grid-cols-2 gap-2 mb-4 relative z-10">
                            <div
                                class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 text-center">
                                <div class="text-xs text-slate-500 mb-0.5">Joined</div>
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ $user->created_at->format('M d, Y') }}</div>
                            </div>
                            <div
                                class="p-2 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-800/20 text-center">
                                <div class="text-xs text-red-600/70 dark:text-red-400 mb-0.5">Idle For</div>
                                <div class="text-sm font-bold text-red-600 dark:text-red-400">
                                    {{ $user->created_at->diffForHumans(null, true) }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Action --}}
                    <div class="mt-auto relative z-10">
                        <a href="{{ route('reports.member', ['search' => $user->id]) }}"
                            class="flex items-center justify-center w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold text-sm hover:bg-brand-blue hover:text-white dark:hover:bg-brand-blue transition-all group-hover:shadow-md">
                            View Profile <i
                                class="bi bi-arrow-right ml-2 opacity-0 group-hover:opacity-100 -translate-x-2 group-hover:translate-x-0 transition-all"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Custom Internal Pagination for Ghosts --}}
        @if ($ghosts->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/10">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    {{-- Info --}}
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        Showing <span
                            class="font-bold text-slate-800 dark:text-slate-200">{{ $ghosts->firstItem() ?? 0 }}</span>
                        to <span class="font-bold text-slate-800 dark:text-slate-200">{{ $ghosts->lastItem() ?? 0 }}</span>
                        of <span class="font-bold text-slate-800 dark:text-slate-200">{{ $ghosts->total() }}</span>
                        ghosts
                    </div>

                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                        {{-- Previous Page Link --}}
                        @if ($ghosts->onFirstPage())
                            <span
                                class="relative inline-flex items-center rounded-l-xl px-3 py-2 text-slate-300 ring-1 ring-inset ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 focus:z-20 focus:outline-offset-0 cursor-not-allowed">
                                <span class="sr-only">Previous</span>
                                <i class="bi bi-chevron-left text-xs"></i>
                            </span>
                        @else
                            <a href="{{ $ghosts->previousPageUrl() }}"
                                class="relative inline-flex items-center rounded-l-xl px-3 py-2 text-slate-500 ring-1 ring-inset ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 focus:z-20 focus:outline-offset-0 transition-colors">
                                <span class="sr-only">Previous</span>
                                <i class="bi bi-chevron-left text-xs"></i>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($ghosts->getUrlRange(max(1, $ghosts->currentPage() - 2), min($ghosts->lastPage(), $ghosts->currentPage() + 2)) as $page => $url)
                            @if ($page == $ghosts->currentPage())
                                <span aria-current="page"
                                    class="relative z-10 inline-flex items-center bg-brand-blue px-4 py-2 text-sm font-semibold text-white focus:visible:outline focus:visible:outline-2 focus:visible:outline-offset-2 focus:visible:outline-brand-blue">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 ring-1 ring-inset ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 focus:z-20 focus:outline-offset-0 transition-colors">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($ghosts->hasMorePages())
                            <a href="{{ $ghosts->nextPageUrl() }}"
                                class="relative inline-flex items-center rounded-r-xl px-3 py-2 text-slate-500 ring-1 ring-inset ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 focus:z-20 focus:outline-offset-0 transition-colors">
                                <span class="sr-only">Next</span>
                                <i class="bi bi-chevron-right text-xs"></i>
                            </a>
                        @else
                            <span
                                class="relative inline-flex items-center rounded-r-xl px-3 py-2 text-slate-300 ring-1 ring-inset ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 focus:z-20 focus:outline-offset-0 cursor-not-allowed">
                                <span class="sr-only">Next</span>
                                <i class="bi bi-chevron-right text-xs"></i>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
        @endif
    @endif
@endsection
