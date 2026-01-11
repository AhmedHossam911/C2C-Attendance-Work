@extends('Common.Layouts.app')

@section('content')
    <div class="mb-8 space-y-4 md:space-y-0 md:flex md:items-end md:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('reports.index') }}" class="hover:text-brand-blue transition-colors font-medium">Reports</a>
                <i class="bi bi-chevron-right text-xs opacity-50"></i>
                <span class="text-slate-800 dark:text-slate-200">Committee Attendance</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Committee Attendance
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Detailed attendance records per committee.</p>
        </div>

        {{-- Filters --}}
        <div class="w-full md:w-auto flex flex-col md:flex-row gap-4">
            <form action="{{ route('reports.committees') }}" method="GET" class="contents">
                {{-- Search Input --}}
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search members..."
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:border-brand-blue focus:ring-brand-blue shadow-sm py-2.5 pl-10 pr-4 text-sm"
                        onkeydown="if(event.key === 'Enter') this.form.submit()">
                </div>

                {{-- Committee Select --}}
                <div class="relative w-full md:w-64">
                    <select name="committee_id" id="committee_id" onchange="this.form.submit()"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:border-brand-blue focus:ring-brand-blue shadow-sm py-2.5 pl-4 pr-10 appearance-none cursor-pointer text-sm font-medium">
                        @foreach ($committeesList as $committee)
                            <option value="{{ $committee->id }}" {{ $selectedId == $committee->id ? 'selected' : '' }}>
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

    @if (!isset($selectedCommittee))
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center border border-slate-200 dark:border-slate-700 border-dashed">
            <div
                class="inline-flex h-16 w-16 rounded-full bg-slate-50 dark:bg-slate-900 text-slate-300 items-center justify-center mb-4">
                <i class="bi bi-people text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">No Committee Selected</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-2">Please select a committee to view attendance.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($members as $member)
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow group relative overflow-hidden">
                    {{-- Rate Background Indicator --}}
                    <div
                        class="absolute top-0 left-0 w-1 h-full 
                        {{ $member->attendance_rate >= 80 ? 'bg-emerald-500' : ($member->attendance_rate >= 50 ? 'bg-amber-500' : 'bg-red-500') }}">
                    </div>

                    <div class="flex items-start justify-between mb-4 pl-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-sm font-bold text-slate-600 dark:text-slate-300">
                                {{ substr($member->name, 0, 1) }}
                            </div>
                            <div>
                                <a href="{{ route('reports.member', ['search' => $member->id]) }}"
                                    class="font-bold text-slate-800 dark:text-slate-100 line-clamp-1 hover:text-brand-blue transition-colors">
                                    {{ $member->name }}
                                </a>
                                <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400">
                                    {{ str_replace('_', ' ', $member->role) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-3 gap-2 mb-4 pl-3">
                        <div
                            class="text-center p-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/20">
                            <div class="text-emerald-600 dark:text-emerald-400 font-bold text-lg leading-none">
                                {{ $member->present }}</div>
                            <div class="text-[10px] uppercase text-emerald-600/70 dark:text-emerald-500 font-bold mt-1">
                                Present</div>
                        </div>
                        <div
                            class="text-center p-2 rounded-lg bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/20">
                            <div class="text-amber-600 dark:text-amber-400 font-bold text-lg leading-none">
                                {{ $member->late }}</div>
                            <div class="text-[10px] uppercase text-amber-600/70 dark:text-amber-500 font-bold mt-1">Late
                            </div>
                        </div>
                        <div
                            class="text-center p-2 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-800/20">
                            <div class="text-red-600 dark:text-red-400 font-bold text-lg leading-none">
                                {{ $member->absent }}</div>
                            <div class="text-[10px] uppercase text-red-600/70 dark:text-red-500 font-bold mt-1">Absent</div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="pl-3 mb-4">
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-slate-500 font-medium">Attendance Rate</span>
                            <span
                                class="font-bold {{ $member->attendance_rate >= 80 ? 'text-emerald-600' : ($member->attendance_rate >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                                {{ $member->attendance_rate }}%
                            </span>
                        </div>
                        <div class="h-2 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $member->attendance_rate >= 80 ? 'bg-emerald-500' : ($member->attendance_rate >= 50 ? 'bg-amber-500' : 'bg-red-500') }}"
                                style="width: {{ $member->attendance_rate }}%"></div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-auto pl-3">
                        @if (auth()->user()->hasRole('top_management'))
                            <a href="{{ route('reports.member', ['search' => $member->id]) }}"
                                class="flex items-center justify-center w-full py-2.5 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-brand-blue hover:text-white dark:hover:bg-brand-blue text-xs font-bold text-slate-600 dark:text-slate-300 transition-all group-hover:shadow-sm uppercase tracking-wide">
                                View Profile
                            </a>
                        @else
                            <button disabled
                                class="flex items-center justify-center w-full py-2.5 rounded-xl bg-slate-50 dark:bg-slate-700/30 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wide cursor-not-allowed">
                                View Profile (Restricted)
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div
            class="mt-8 flex flex-col md:flex-row items-center justify-between gap-4 border-t border-slate-200 dark:border-slate-700 pt-6">
            {{-- Info --}}
            <div class="text-sm text-slate-500 dark:text-slate-400">
                Showing <span class="font-bold text-slate-800 dark:text-slate-200">{{ $members->firstItem() ?? 0 }}</span>
                to <span class="font-bold text-slate-800 dark:text-slate-200">{{ $members->lastItem() ?? 0 }}</span> of
                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $members->total() }}</span> members
            </div>

            {{-- Custom Pagination --}}
            @if ($members->hasPages())
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    {{-- Previous Page Link --}}
                    @if ($members->onFirstPage())
                        <span
                            class="relative inline-flex items-center rounded-l-xl px-3 py-2 text-slate-300 ring-1 ring-inset ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 focus:z-20 focus:outline-offset-0 cursor-not-allowed">
                            <span class="sr-only">Previous</span>
                            <i class="bi bi-chevron-left text-xs"></i>
                        </span>
                    @else
                        <a href="{{ $members->previousPageUrl() }}"
                            class="relative inline-flex items-center rounded-l-xl px-3 py-2 text-slate-500 ring-1 ring-inset ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 focus:z-20 focus:outline-offset-0 transition-colors">
                            <span class="sr-only">Previous</span>
                            <i class="bi bi-chevron-left text-xs"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($members->getUrlRange(max(1, $members->currentPage() - 2), min($members->lastPage(), $members->currentPage() + 2)) as $page => $url)
                        @if ($page == $members->currentPage())
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
                    @if ($members->hasMorePages())
                        <a href="{{ $members->nextPageUrl() }}"
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
            @endif
        </div>
    @endif
@endsection
