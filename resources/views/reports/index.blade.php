@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Committee Attendance Reports</h2>
        <a href="{{ route('reports.export.committees') }}"
            class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/20 transition-all active:scale-95 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export to Excel
        </a>
    </div>

    <div class="space-y-4">
        @foreach ($committees as $committee)
            <x-card class="overflow-hidden" :embedded="true">
                <details class="group">
                    <summary
                        class="flex items-center justify-between p-6 cursor-pointer select-none bg-white dark:bg-[#1e293b] hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <h4 class="font-bold text-lg text-slate-800 dark:text-white">{{ $committee->name }}</h4>
                            <span
                                class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20 dark:text-brand-blue-light">
                                {{ $committee->users->count() }} Members
                            </span>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <span
                                class="inline-flex justify-center items-center w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 transition-transform group-open:rotate-180">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </summary>

                    <div class="border-t border-slate-100 dark:border-slate-800">
                        <x-table :headers="['Member', 'Total Sessions', 'Present', 'Late', 'Attendance %']">
                            @foreach ($committee->users as $user)
                                @php
                                    $totalRecords = $user->attendanceRecords->count();
                                    $present = $user->attendanceRecords->where('status', 'present')->count();
                                    $late = $user->attendanceRecords->where('status', 'late')->count();
                                @endphp
                                <x-table.tr>
                                    <x-table.td>
                                        <span
                                            class="font-bold text-slate-700 dark:text-slate-200">{{ $user->name }}</span>
                                    </x-table.td>
                                    <x-table.td>{{ $totalRecords }}</x-table.td>
                                    <x-table.td>
                                        <span class="text-green-600 font-medium">{{ $present }}</span>
                                    </x-table.td>
                                    <x-table.td>
                                        <span class="text-amber-600 font-medium">{{ $late }}</span>
                                    </x-table.td>
                                    <x-table.td>
                                        <span class="text-slate-400">N/A</span>
                                    </x-table.td>
                                </x-table.tr>
                            @endforeach
                        </x-table>
                    </div>
                </details>
            </x-card>
        @endforeach
    </div>
@endsection
