@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Member Search</h2>
    </div>

    <!-- Search Card -->
    <x-card class="mb-6">
        <form action="{{ route('reports.member') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <input type="text" name="search"
                    class="w-full pl-4 pr-10 py-2.5 rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                    placeholder="Search by ID, Name, or Email" value="{{ request('search') }}">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white font-semibold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-95">
                    Search
                </button>
                <a href="{{ route('reports.export.members', ['search' => request('search')]) }}"
                    class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/20 transition-all active:scale-95 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Results
                </a>
            </div>
        </form>
    </x-card>

    @if (isset($members))
        <div class="space-y-4">
            @foreach ($members as $member)
                <x-card class="overflow-hidden" :embedded="true">
                    <details class="group">
                        <summary
                            class="flex items-center justify-between p-6 cursor-pointer select-none bg-white dark:bg-[#1e293b] hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
                                <span class="font-bold text-lg text-slate-800 dark:text-white">{{ $member->name }}</span>
                                <span class="hidden md:inline text-slate-300 dark:text-slate-600">|</span>
                                <span class="text-slate-500 dark:text-slate-400 text-sm">{{ $member->email }}</span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                    {{ ucfirst($member->role) }}
                                </span>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <span
                                    class="inline-flex justify-center items-center w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 transition-transform group-open:rotate-180">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </div>
                        </summary>

                        <div class="border-t border-slate-100 dark:border-slate-800">
                            <div class="bg-slate-50/50 dark:bg-slate-900/20 p-4">
                                <h5 class="font-bold text-slate-700 dark:text-slate-300 mb-3 px-2">Attendance History</h5>
                                <x-card class="p-0" :embedded="true">
                                    <x-table :headers="['Session', 'Date', 'Status']">
                                        @foreach ($member->attendanceRecords as $record)
                                            <x-table.tr>
                                                <x-table.td>{{ $record->session->title }}</x-table.td>
                                                <x-table.td>{{ $record->scanned_at->format('Y-m-d h:i A') }}</x-table.td>
                                                <x-table.td>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $record->status === 'present' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                                        {{ ucfirst($record->status) }}
                                                    </span>
                                                </x-table.td>
                                            </x-table.tr>
                                        @endforeach
                                    </x-table>
                                </x-card>
                            </div>
                        </div>
                    </details>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $members->links() }}
        </div>
    @endif
@endsection
