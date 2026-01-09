@extends('layouts.app')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('reports.index') }}" class="hover:text-brand-blue transition-colors font-medium">Reports</a>
                <i class="bi bi-chevron-right text-xs opacity-50"></i>
                <span class="text-slate-800 dark:text-slate-200">Top Performers</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Top Performers
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Our most dedicated and active members.</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        @if ($performers->isNotEmpty())
            {{-- Podium (Top 3 on Page 1 only) --}}
            @if (request('page', 1) == 1)
                <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-8 items-end mb-8 mt-4 md:mt-8">

                    {{-- 2nd Place --}}
                    @if (isset($performers[1]))
                        <x-card
                            class="relative order-2 sm:order-1 mt-8 sm:mt-0 border-t-4 border-t-slate-400 flex flex-col items-center text-center p-6 bg-gradient-to-b from-white to-slate-50 dark:from-slate-800 dark:to-slate-800/50">
                            <div class="absolute -top-5">
                                <div
                                    class="h-10 w-10 rounded-full bg-slate-300 border-4 border-white dark:border-slate-800 flex items-center justify-center text-lg font-bold text-slate-600 shadow-lg">
                                    2
                                </div>
                            </div>
                            <div class="mt-4 mb-2">
                                <div
                                    class="h-14 w-14 mx-auto rounded-full bg-slate-200/50 flex items-center justify-center text-xl font-bold text-slate-500 mb-3 shadow-inner">
                                    {{ substr($performers[1]->name, 0, 1) }}
                                </div>
                                <h3 class="font-bold text-slate-800 dark:text-slate-100 truncate w-full px-2">
                                    {{ $performers[1]->name }}</h3>
                                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Silver Medalist</p>
                            </div>
                            <div
                                class="flex gap-3 text-xs mt-3 w-full justify-center bg-white dark:bg-slate-700/50 py-2 rounded-lg border border-slate-100 dark:border-slate-700">
                                <div class="text-center">
                                    <span
                                        class="block font-bold text-brand-blue text-base">{{ $performers[1]->attendance_records_count }}</span>
                                    <span class="text-[9px] text-slate-400 uppercase">Visits</span>
                                </div>
                                <div class="w-px bg-slate-200 dark:bg-slate-600"></div>
                                <div class="text-center">
                                    <span
                                        class="block font-bold text-brand-teal text-base">{{ $performers[1]->task_submissions_count }}</span>
                                    <span class="text-[9px] text-slate-400 uppercase">Tasks</span>
                                </div>
                            </div>
                        </x-card>
                    @endif

                    {{-- 1st Place --}}
                    @if (isset($performers[0]))
                        <x-card
                            class="relative order-1 sm:order-2 border-t-4 border-t-brand-gold flex flex-col items-center text-center p-8 transform sm:-translate-y-4 z-10 shadow-xl bg-gradient-to-b from-white to-amber-50/30 dark:from-slate-800 dark:to-amber-900/10">
                            <div class="absolute -top-7">
                                <div
                                    class="h-14 w-14 rounded-full bg-brand-gold border-4 border-white dark:border-slate-800 flex items-center justify-center text-2xl font-bold text-white shadow-xl">
                                    1
                                </div>
                            </div>
                            <div class="mt-6 mb-4">
                                <div
                                    class="h-24 w-24 mx-auto rounded-full bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-900 dark:to-amber-800 flex items-center justify-center text-4xl font-bold text-amber-700 dark:text-amber-400 mb-4 border-4 border-white dark:border-slate-700 shadow-lg">
                                    {{ substr($performers[0]->name, 0, 1) }}
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white truncate w-full px-2">
                                    {{ $performers[0]->name }}</h3>
                                <p class="text-xs text-brand-gold font-bold uppercase tracking-[0.2em] mt-1">Champion</p>
                            </div>
                            <div
                                class="grid grid-cols-2 gap-4 w-full bg-slate-50 dark:bg-slate-700/50 p-3 rounded-xl border border-dashed border-amber-200 dark:border-amber-900">
                                <div class="text-center">
                                    <span
                                        class="block font-black text-2xl text-brand-blue">{{ $performers[0]->attendance_records_count }}</span>
                                    <span
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Attended</span>
                                </div>
                                <div class="text-center border-l border-slate-200 dark:border-slate-600">
                                    <span
                                        class="block font-black text-2xl text-brand-teal">{{ $performers[0]->task_submissions_count }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Tasks</span>
                                </div>
                            </div>
                        </x-card>
                    @endif

                    {{-- 3rd Place --}}
                    @if (isset($performers[2]))
                        <x-card
                            class="relative order-3 mt-8 sm:mt-0 border-t-4 border-t-amber-700 flex flex-col items-center text-center p-6 bg-gradient-to-b from-white to-slate-50 dark:from-slate-800 dark:to-slate-800/50">
                            <div class="absolute -top-5">
                                <div
                                    class="h-10 w-10 rounded-full bg-amber-800 border-4 border-white dark:border-slate-800 flex items-center justify-center text-lg font-bold text-white shadow-lg">
                                    3
                                </div>
                            </div>
                            <div class="mt-4 mb-2">
                                <div
                                    class="h-14 w-14 mx-auto rounded-full bg-amber-100/50 flex items-center justify-center text-xl font-bold text-amber-900/60 mb-3 shadow-inner">
                                    {{ substr($performers[2]->name, 0, 1) }}
                                </div>
                                <h3 class="font-bold text-slate-800 dark:text-slate-100 truncate w-full px-2">
                                    {{ $performers[2]->name }}</h3>
                                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Bronze Medalist</p>
                            </div>
                            <div
                                class="flex gap-3 text-xs mt-3 w-full justify-center bg-white dark:bg-slate-700/50 py-2 rounded-lg border border-slate-100 dark:border-slate-700">
                                <div class="text-center">
                                    <span
                                        class="block font-bold text-brand-blue text-base">{{ $performers[2]->attendance_records_count }}</span>
                                    <span class="text-[9px] text-slate-400 uppercase">Visits</span>
                                </div>
                                <div class="w-px bg-slate-200 dark:bg-slate-600"></div>
                                <div class="text-center">
                                    <span
                                        class="block font-bold text-brand-teal text-base">{{ $performers[2]->task_submissions_count }}</span>
                                    <span class="text-[9px] text-slate-400 uppercase">Tasks</span>
                                </div>
                            </div>
                        </x-card>
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
                                <th class="px-4 py-4 w-12 text-center">#</th>
                                <th class="px-4 py-4">User</th>
                                <th class="px-4 py-4 text-center">Attendance</th>
                                <th class="px-4 py-4 text-center">Tasks</th>
                                <th class="px-4 py-4 text-right"></th>
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
                                        @if (request('page', 1) == 1)
                                            {{ $index + 4 }}
                                        @else
                                            {{ $startIndex + $index + 1 }}
                                        @endif
                                    </td>

                                    <td
                                        class="px-4 py-4 font-bold text-slate-800 dark:text-slate-200 flex items-center gap-3">
                                        <div
                                            class="h-8 w-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-500">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <span>{{ $user->name }}</span>
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
                                    <td class="px-4 py-4 text-right">
                                        <a href="{{ route('reports.member', ['search' => $user->id]) }}"
                                            class="text-slate-400 hover:text-brand-blue transition-colors"><i
                                                class="bi bi-eye-fill"></i></a>
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
