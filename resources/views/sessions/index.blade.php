@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Attendance Sessions</h2>
        <a href="{{ route('sessions.create') }}"
            class="px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white text-sm font-semibold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-95">
            Create Session
        </a>
    </div>

    <!-- Filter Card -->
    <x-card class="mb-6">
        <form action="{{ route('sessions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <select name="committee_id"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white">
                    <option value="">All Committees</option>
                    @foreach ($committees as $committee)
                        <option value="{{ $committee->id }}"
                            {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                            {{ $committee->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div>
                <input type="date" name="date_from"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                    placeholder="From Date" value="{{ request('date_from') }}">
            </div>
            <div class="flex gap-2">
                <input type="date" name="date_to"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                    placeholder="To Date" value="{{ request('date_to') }}">
                <button type="submit"
                    class="px-3 py-2 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-xl transition-colors">
                    <i class="bi bi-funnel-fill"></i>
                </button>
                <a href="{{ route('sessions.index') }}"
                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl transition-colors flex items-center justify-center">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>
    </x-card>

    <div class="space-y-6">
        <!-- Desktop Table -->
        <div class="hidden md:block">
            <x-card class="p-0" :embedded="true">
                <x-table :headers="['Title', 'Committee', 'Status', 'Threshold', 'Attendance', 'Created By', 'Date', 'Actions']">
                    @forelse ($sessions as $session)
                        <x-table.tr>
                            <x-table.td>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $session->title }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span
                                    class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-xs font-medium text-slate-600 dark:text-slate-400">
                                    {{ $session->committee->name ?? 'General' }}
                                </span>
                            </x-table.td>
                            <x-table.td>
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-bold {{ $session->status === 'open' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </x-table.td>
                            <x-table.td class="text-slate-500">{{ $session->late_threshold_minutes }}m</x-table.td>
                            <x-table.td>
                                <div class="flex items-center gap-1 font-medium text-slate-700 dark:text-slate-300">
                                    <i class="bi bi-people-fill text-slate-400"></i> {{ $session->records_count }}
                                </div>
                            </x-table.td>
                            <x-table.td class="text-slate-500">{{ $session->creator->name }}</x-table.td>
                            <x-table.td
                                class="text-slate-500 text-xs">{{ $session->created_at->format('M d, h:i A') }}</x-table.td>
                            <x-table.td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('sessions.show', $session) }}"
                                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                                        title="View">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('sessions.export', $session) }}"
                                        class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                                        title="Export">
                                        <i class="bi bi-file-earmark-excel-fill"></i>
                                    </a>
                                    <form action="{{ route('sessions.toggle', $session) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="p-2 rounded-lg transition-colors {{ $session->status === 'open' ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }}"
                                            title="{{ $session->status === 'open' ? 'Close Session' : 'Open Session' }}">
                                            <i
                                                class="bi bi-{{ $session->status === 'open' ? 'pause-fill' : 'play-fill' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="8" class="text-center py-8 text-slate-500">
                                No sessions found.
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-4">
            @forelse ($sessions as $session)
                <x-card class="relative overflow-hidden">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span
                                class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-xs font-medium text-slate-600 dark:text-slate-400 mb-2 inline-block">
                                {{ $session->committee->name ?? 'General' }}
                            </span>
                            <h3 class="font-bold text-slate-800 dark:text-white text-lg">{{ $session->title }}</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $session->created_at->format('M d, h:i A') }} • {{ $session->creator->name }}
                            </p>
                        </div>
                        <span
                            class="px-2 py-1 rounded-full text-xs font-bold {{ $session->status === 'open' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                            {{ ucfirst($session->status) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4 mb-4 text-sm text-slate-600 dark:text-slate-400">
                        <div class="flex items-center gap-1">
                            <i class="bi bi-people-fill text-slate-400"></i> {{ $session->records_count }}
                        </div>
                        <div class="flex items-center gap-1">
                            <i class="bi bi-clock-history text-slate-400"></i> {{ $session->late_threshold_minutes }}m
                            threshold
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <a href="{{ route('sessions.show', $session) }}"
                            class="flex items-center justify-center px-3 py-2 bg-blue-50 text-blue-600 rounded-xl font-medium text-sm hover:bg-blue-100">
                            Details
                        </a>
                        <a href="{{ route('sessions.export', $session) }}"
                            class="flex items-center justify-center px-3 py-2 bg-green-50 text-green-600 rounded-xl font-medium text-sm hover:bg-green-100">
                            Export
                        </a>
                        <form action="{{ route('sessions.toggle', $session) }}" method="POST" class="col-span-1">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center px-3 py-2 rounded-xl font-medium text-sm transition-colors {{ $session->status === 'open' ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }}">
                                {{ $session->status === 'open' ? 'Close' : 'Open' }}
                            </button>
                        </form>
                    </div>
                </x-card>
            @empty
                <x-card class="text-center py-8 text-slate-500">
                    No sessions found.
                </x-card>
            @endforelse
        </div>

        @if ($sessions->hasPages())
            <div class="mt-4">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
@endsection
