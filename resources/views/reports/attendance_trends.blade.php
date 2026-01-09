@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('reports.index') }}" class="hover:text-brand-blue transition-colors font-medium">Reports</a>
            <i class="bi bi-chevron-right text-xs opacity-50"></i>
            <span class="text-slate-800 dark:text-slate-200">Attendance Trends</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Attendance Trends 
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">
            Tracking the last 10 sessions to identify attendance patterns.
        </p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">
        <h3 class="font-bold text-xl text-slate-800 dark:text-slate-100 mb-8 flex items-center gap-3">
            <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-500">
                <i class="bi bi-graph-up"></i>
            </div>
            Last 10 Sessions Attendance
        </h3>

        @if ($trends->isEmpty())
            <div class="text-center py-20 border-2 border-dashed border-slate-100 dark:border-slate-700 rounded-2xl">
                <div
                    class="inline-flex h-16 w-16 rounded-full bg-slate-50 dark:bg-slate-800 text-slate-300 items-center justify-center mb-4">
                    <i class="bi bi-bar-chart text-3xl"></i>
                </div>
                <p class="text-slate-500 font-medium">Not enough data to generate trends.</p>
            </div>
        @else
            <div
                class="relative h-64 md:h-80 w-full flex items-end justify-between gap-2 md:gap-6 border-b border-slate-200 dark:border-slate-600 pb-2">

                {{-- Y-Axis Labels (100% and 50%) --}}
                <div class="absolute w-full h-full pointer-events-none">
                    <div class="border-t border-dashed border-slate-100 dark:border-slate-700 w-full absolute top-0"></div>
                    <div class="border-t border-dashed border-slate-100 dark:border-slate-700 w-full absolute top-1/2">
                    </div>
                </div>
                <div class="absolute -left-8 top-0 text-[10px] text-slate-400">100%</div>
                <div class="absolute -left-8 top-1/2 -translate-y-1/2 text-[10px] text-slate-400">50%</div>
                <div class="absolute -left-6 bottom-0 text-[10px] text-slate-400">0%</div>

                @foreach ($trends as $data)
                    <div class="group relative flex-1 h-full flex flex-col justify-end items-center">
                        {{-- Tooltip --}}
                        <div
                            class="absolute bottom-full mb-3 opacity-0 group-hover:opacity-100 transition-all duration-300 z-10 w-48 -left-1/2 ml-7 pointer-events-none">
                            <div class="bg-slate-900 text-white text-xs rounded-xl py-2 px-3 shadow-xl">
                                <p class="font-bold truncate">{{ $data['label'] }}</p>
                                <div class="mt-2 pt-2 border-t border-slate-700 flex justify-between">
                                    <span>Rate:</span>
                                    <span class="font-bold text-green-400">{{ $data['rate'] }}%</span>
                                </div>
                            </div>
                            <div class="w-3 h-3 bg-slate-900 transform rotate-45 mx-auto -mt-1.5"></div>
                        </div>

                        {{-- Bar --}}
                        <div class="w-full max-w-[40px] bg-indigo-500 rounded-t-lg transition-all duration-500 hover:bg-indigo-400 cursor-pointer relative group-hover:shadow-[0_0_20px_rgba(99,102,241,0.5)]"
                            style="height: {{ $data['rate'] }}%">
                            <div
                                class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs font-bold text-slate-600 dark:text-slate-300 opacity-0 group-hover:opacity-100 transition-opacity">
                                {{ $data['rate'] }}%
                            </div>
                        </div>

                        {{-- Date Label --}}
                        <p
                            class="text-[10px] text-slate-600 dark:text-slate-400 mt-3 rotate-0 hidden md:block text-center truncate w-full px-1">
                            {{-- Controller sends pre-formatted label, we can parse it or just use a part of it --}}
                            {{-- Using a simple substr logic or just showing index if needed, but the loop variable data has full label --}}
                            {{-- Let's assume we want short date. The controller string is "Title (Date)" --}}
                            {{ Str::afterLast($data['label'], '(') ? Str::beforeLast(Str::afterLast($data['label'], '('), ')') : '' }}
                        </p>
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-slate-400 mt-6 text-center md:hidden">Tap bars to see details</p>
        @endif
    </div>
@endsection
