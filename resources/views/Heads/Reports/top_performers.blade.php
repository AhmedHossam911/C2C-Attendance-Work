@extends('Common.Layouts.app')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('reports.index') }}" class="hover:text-brand-blue transition-colors font-medium">Reports</a>
                <i class="bi bi-chevron-right text-xs opacity-50"></i>
                <span class="text-slate-800 dark:text-slate-200">Top Performers</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Top Performers</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Members ranked by total engagement score.</p>
        </div>

        <x-card class="p-1 md:p-2 bg-white dark:bg-slate-800 border-0 shadow-sm">
            <form action="{{ route('reports.top_performers') }}" method="GET" class="flex items-center gap-2">
                <div class="relative">
                    <select name="committee_id" onchange="this.form.submit()"
                        class="px-4 py-2 text-sm border-slate-200 dark:border-slate-700 rounded-lg focus:ring-brand-blue focus:border-brand-blue dark:bg-slate-900 dark:text-slate-300 shadow-sm cursor-pointer hover:border-slate-300 transition-colors">
                        <option value="">All Committees</option>
                        @foreach ($committees as $committee)
                            <option value="{{ $committee->id }}"
                                {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                                {{ $committee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </x-card>
    </div>

    {{-- How Scoring Works --}}
    <div
        class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-teal-50 dark:from-slate-800 dark:to-slate-800 rounded-xl border border-blue-100 dark:border-slate-700">
        <div class="flex items-start gap-3">
            <div
                class="shrink-0 h-10 w-10 rounded-full bg-white dark:bg-slate-700 flex items-center justify-center shadow-sm">
                <i class="bi bi-info-circle text-lg" style="color: #949bb0;"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 dark:text-slate-100 mb-1">How is the score calculated?</h4>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    <span class="font-semibold" style="color: #949bb0;">Score</span> =
                    <span class="font-semibold" style="color: #949bb0;">Sessions Attended</span> +
                    <span class="font-semibold" style="color: #949bb0;">Tasks Submitted</span>.
                    Members are ranked by total score, then alphabetically by name.
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        @if ($performers->isNotEmpty())
            {{-- Podium (Top 3 on Page 1 only) --}}
            @if (request('page', 1) == 1)
                <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 items-stretch mb-8">

                    {{-- 2nd Place --}}
                    @if (isset($performers[1]))
                        <div
                            class="order-2 sm:order-1 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="h-10 w-10 rounded-full bg-gradient-to-br from-slate-300 to-slate-400 flex items-center justify-center text-lg font-bold text-white shadow">
                                    {{ $performers[1]->rank }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-slate-800 dark:text-white truncate">{{ $performers[1]->name }}
                                    </h3>
                                    <p class="text-xs text-slate-400 uppercase tracking-wide">Silver</p>
                                </div>
                            </div>
                            <div class="text-center py-4 mb-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                                <span
                                    class="block text-4xl font-black text-slate-800 dark:text-white">{{ $performers[1]->total_score }}</span>
                                <span class="text-xs text-slate-500 uppercase tracking-wider">Total Score</span>
                            </div>
                            <div class="flex justify-around text-center">
                                <div>
                                    <span
                                        class="block text-lg font-bold text-brand-blue">{{ $performers[1]->attendance_records_count }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase">Sessions</span>
                                </div>
                                <div class="w-px bg-slate-200 dark:bg-slate-600"></div>
                                <div>
                                    <span
                                        class="block text-lg font-bold text-brand-teal">{{ $performers[1]->task_submissions_count }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase">Tasks</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 1st Place --}}
                    @if (isset($performers[0]))
                        <div
                            class="order-1 sm:order-2 bg-gradient-to-br from-amber-50 to-white dark:from-slate-800 dark:to-slate-800 rounded-2xl p-6 border-2 border-amber-300 dark:border-amber-600 shadow-lg hover:shadow-xl transition-shadow relative overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-amber-200/30 to-transparent rounded-bl-full">
                            </div>
                            <div class="flex items-center gap-3 mb-4 relative">
                                <div
                                    class="h-12 w-12 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-xl font-bold text-white shadow-lg">
                                    <i class="bi bi-trophy-fill"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-lg text-slate-900 dark:text-white truncate">
                                        {{ $performers[0]->name }}</h3>
                                    <p class="text-xs text-amber-600 dark:text-amber-400 font-bold uppercase tracking-wide">
                                        Champion</p>
                                </div>
                            </div>
                            <div
                                class="text-center py-5 mb-4 bg-gradient-to-r from-amber-100 to-amber-50 dark:from-amber-900/30 dark:to-slate-700/50 rounded-xl border border-amber-200 dark:border-amber-800">
                                <span
                                    class="block text-5xl font-black text-amber-700 dark:text-amber-400">{{ $performers[0]->total_score }}</span>
                                <span
                                    class="text-xs text-amber-600 dark:text-amber-500 uppercase tracking-wider font-semibold">Total
                                    Score</span>
                            </div>
                            <div class="flex justify-around text-center">
                                <div>
                                    <span
                                        class="block text-xl font-bold text-brand-blue">{{ $performers[0]->attendance_records_count }}</span>
                                    <span class="text-[10px] text-slate-500 uppercase">Sessions</span>
                                </div>
                                <div class="w-px bg-amber-200 dark:bg-slate-600"></div>
                                <div>
                                    <span
                                        class="block text-xl font-bold text-brand-teal">{{ $performers[0]->task_submissions_count }}</span>
                                    <span class="text-[10px] text-slate-500 uppercase">Tasks</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 3rd Place --}}
                    @if (isset($performers[2]))
                        <div
                            class="order-3 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="h-10 w-10 rounded-full bg-gradient-to-br from-amber-600 to-amber-800 flex items-center justify-center text-lg font-bold text-white shadow">
                                    {{ $performers[2]->rank }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-slate-800 dark:text-white truncate">
                                        {{ $performers[2]->name }}</h3>
                                    <p class="text-xs text-slate-400 uppercase tracking-wide">Bronze</p>
                                </div>
                            </div>
                            <div class="text-center py-4 mb-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                                <span
                                    class="block text-4xl font-black text-slate-800 dark:text-white">{{ $performers[2]->total_score }}</span>
                                <span class="text-xs text-slate-500 uppercase tracking-wider">Total Score</span>
                            </div>
                            <div class="flex justify-around text-center">
                                <div>
                                    <span
                                        class="block text-lg font-bold text-brand-blue">{{ $performers[2]->attendance_records_count }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase">Sessions</span>
                                </div>
                                <div class="w-px bg-slate-200 dark:bg-slate-600"></div>
                                <div>
                                    <span
                                        class="block text-lg font-bold text-brand-teal">{{ $performers[2]->task_submissions_count }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase">Tasks</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Full List --}}
            <div class="lg:col-span-3">
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-xs uppercase font-bold text-slate-500 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-4 py-4 w-12 text-center">Rank</th>
                                <th class="px-4 py-4">Member</th>
                                <th class="px-4 py-4 text-center">
                                    <span class="hidden sm:inline">Sessions Attended</span>
                                    <span class="sm:hidden">Sessions</span>
                                </th>
                                <th class="px-4 py-4 text-center">
                                    <span class="hidden sm:inline">Tasks Submitted</span>
                                    <span class="sm:hidden">Tasks</span>
                                </th>
                                <th class="px-4 py-4 text-center font-black">Score</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @php
                                $startIndex = (request('page', 1) - 1) * $performers->perPage();
                                $loopItems =
                                    request('page', 1) == 1 && $performers->count() >= 3
                                        ? $performers->skip(3)
                                        : $performers;
                            @endphp

                            @foreach ($loopItems as $index => $user)
                                <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="px-4 py-4 text-center font-bold text-slate-400">
                                        {{ $user->rank }}
                                    </td>
                                    <td
                                        class="px-4 py-4 font-bold text-slate-800 dark:text-slate-200 flex items-center gap-3">
                                        <div
                                            class="h-8 w-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-500 shrink-0">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <span class="truncate">{{ $user->name }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-1 font-bold text-brand-blue bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded-md">
                                            {{ $user->attendance_records_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-1 font-bold text-brand-teal bg-teal-50 dark:bg-teal-900/30 px-2 py-1 rounded-md">
                                            {{ $user->task_submissions_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span
                                            class="inline-flex items-center justify-center font-black text-slate-800 dark:text-white bg-slate-100 dark:bg-slate-600 px-3 py-1 rounded-lg min-w-[3rem]">
                                            {{ $user->total_score }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    @if ($performers->hasPages())
                        <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                            {{ $performers->links('components.pagination') }}
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div
                class="col-span-3 text-center py-16 text-slate-500 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl">
                <i class="bi bi-trophy text-4xl mb-4 block opacity-50"></i>
                <h3 class="font-bold text-lg text-slate-700 dark:text-slate-300">No Data Yet</h3>
                <p>Performance data will appear here once activities start.</p>
            </div>
        @endif
    </div>
@endsection
