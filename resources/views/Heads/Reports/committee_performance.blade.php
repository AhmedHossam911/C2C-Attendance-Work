@extends('Common.Layouts.app')

@section('content')
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('reports.index') }}" class="hover:text-brand-blue transition-colors font-medium">Reports</a>
            <i class="bi bi-chevron-right text-xs opacity-50"></i>
            <span class="text-slate-800 dark:text-slate-200">Committee Performance</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Committee Performance 
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">
            Comparing attendance and task completion rates across committees.
        </p>
    </div>

    @if ($performance->isEmpty())
        <div
            class="flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50">
            <div
                class="inline-flex h-20 w-20 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 items-center justify-center mb-6">
                <i class="bi bi-bar-chart text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100">No Data Available</h3>
            <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-2">
                Not enough data to calculate performance metrics yet.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($performance as $committee)
                <x-card
                    class="h-full flex flex-col hover:shadow-lg transition-shadow duration-300 border-t-4 border-t-slate-200 dark:border-t-slate-700">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 line-clamp-1"
                                title="{{ $committee['name'] }}">
                                {{ $committee['name'] }}
                            </h3>
                            <p class="text-xs text-slate-500 font-medium">
                                {{ $committee['members'] }} Members
                            </p>
                        </div>
                        <div
                            class="h-10 w-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>

                    <div class="space-y-6 flex-1">
                        {{-- Attendance Rate --}}
                        <div>
                            <div class="flex justify-between text-xs font-bold uppercase tracking-wider mb-2">
                                <span class="text-brand-blue">Attendance</span>
                                <span
                                    class="text-slate-700 dark:text-slate-300">{{ number_format($committee['attendance_rate'], 1) }}%</span>
                            </div>
                            <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-brand-blue rounded-full transition-all duration-1000 ease-out"
                                    style="width: {{ $committee['attendance_rate'] }}%"></div>
                            </div>
                        </div>

                        {{-- Task Completion Rate --}}
                        <div>
                            <div class="flex justify-between text-xs font-bold uppercase tracking-wider mb-2">
                                <span class="text-brand-teal">Tasks</span>
                                <span
                                    class="text-slate-700 dark:text-slate-300">{{ number_format($committee['task_rate'], 1) }}%</span>
                            </div>
                            <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-brand-teal rounded-full transition-all duration-1000 ease-out"
                                    style="width: {{ $committee['task_rate'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
@endsection
