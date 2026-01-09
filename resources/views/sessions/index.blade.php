@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Attendance Sessions</h2>
        @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr', 'committee_head', 'vice_head']))
            <x-primary-button href="{{ route('sessions.create') }}" class="flex items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                Create Session
            </x-primary-button>
        @endif
    </div>

    <!-- Filter Card -->
    <x-card class="mb-6">
        <x-slot name="header">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                <i class="bi bi-funnel-fill text-brand-blue"></i>
                Filter Options
            </h3>
        </x-slot>
        <form action="{{ route('sessions.index') }}" method="GET"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <x-input-label for="committee_id" value="Committee" />
                <x-select-input name="committee_id" id="committee_id" class="w-full mt-1">
                    <option value="">All Committees</option>
                    @foreach ($committees as $committee)
                        <option value="{{ $committee->id }}"
                            {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                            {{ $committee->name }}
                        </option>
                    @endforeach
                </x-select-input>
            </div>
            <div>
                <x-input-label for="status" value="Status" />
                <x-select-input name="status" id="status" class="w-full mt-1">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </x-select-input>
            </div>
            <div>
                <x-input-label for="date_from" value="From Date" />
                <x-text-input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                    class="w-full mt-1" />
            </div>
            <div>
                <x-input-label for="date_to" value="To Date" />
                <x-text-input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                    class="w-full mt-1" />
            </div>
            <div class="flex items-end gap-2">
                <x-primary-button type="submit" class="flex-1 justify-center py-2.5">
                    <i class="bi bi-search"></i>
                </x-primary-button>
                <x-secondary-button href="{{ route('sessions.index') }}" class="flex-1 justify-center py-2.5">
                    <i class="bi bi-x-circle"></i>
                </x-secondary-button>
            </div>
        </form>
    </x-card>

    <div class="space-y-6">
        <!-- Desktop Table -->
        <div class="hidden md:block">
            <x-card class="p-0 overflow-hidden" :embedded="true">
                <x-table :headers="array_merge(
                    ['Title', 'Committee', 'Status', 'Threshold'],
                    in_array(Auth::user()->role, ['top_management', 'board', 'hr', 'committee_head'])
                        ? ['Attendance']
                        : ['My Status'],
                    ['Created By', 'Date', 'Actions'],
                )">
                    @forelse ($sessions as $session)
                        @php
                            $myRecord = $session->records->first();
                        @endphp
                        <x-table.tr>
                            <x-table.td>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $session->title }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span
                                    class="px-2.5 py-1 rounded-lg bg-slate-400 dark:bg-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                    {{ $session->committee->name ?? 'General' }}
                                </span>
                            </x-table.td>
                            <x-table.td>
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-bold {{ $session->status === 'open' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800' : 'bg-slate-400 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </x-table.td>
                            <x-table.td
                                class="text-slate-500 font-medium">{{ $session->late_threshold_minutes }}m</x-table.td>

                            {{-- Attendance Column --}}
                            <x-table.td>
                                @php
                                    $canManage =
                                        in_array(Auth::user()->role, ['top_management']) ||
                                        (isset($authorizedCommitteeIds) &&
                                            in_array($session->committee_id, $authorizedCommitteeIds));
                                @endphp
                                @if ($canManage)
                                    <div class="flex items-center gap-1.5 font-bold text-slate-700 dark:text-slate-300">
                                        <i class="bi bi-people-fill text-brand-blue/60 dark:text-brand-blue/80"></i>
                                        {{ $session->records_count }}
                                    </div>
                                @else
                                    @if ($myRecord)
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-bold
                                            {{ $myRecord->status === 'present'
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                                : ($myRecord->status === 'late'
                                                    ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                                                    : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                                            {{ ucfirst($myRecord->status) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Not Recorded</span>
                                    @endif
                                @endif
                            </x-table.td>

                            <x-table.td>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="h-6 w-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-[10px] font-bold">
                                        {{ substr($session->creator->name, 0, 1) }}
                                    </div>
                                    <span
                                        class="text-slate-600 dark:text-slate-400 text-sm font-medium">{{ $session->creator->name }}</span>
                                </div>
                            </x-table.td>
                            <x-table.td class="text-slate-500 text-xs font-medium">
                                {{ $session->created_at->format('M d, h:i A') }}
                            </x-table.td>
                            <x-table.td>
                                <div class="flex items-center gap-2">
                                    {{-- Feedback Buttons Logic --}}
                                    @if ($session->status === 'closed')
                                        @if ($canManage && Auth::user()->role !== 'hr')
                                            <a href="{{ route('sessions.feedback-results', $session) }}"
                                                class="p-2 bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/40 transition-colors"
                                                title="View Feedback Results">
                                                <i class="bi bi-chat-right-text-fill"></i>
                                            </a>
                                        @elseif ($myRecord && in_array($myRecord->status, ['present', 'late']))
                                            @php
                                                $hasFeedback = $session->feedbacks->isNotEmpty();
                                            @endphp
                                            <a href="{{ route('sessions.feedback.create', $session) }}"
                                                class="p-2 {{ $hasFeedback ? 'bg-slate-100 text-slate-500 hover:text-purple-600 dark:bg-slate-800 dark:text-slate-400' : 'bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400 hover:bg-purple-100 dark:hover:bg-purple-900/40' }} rounded-lg transition-all relative group"
                                                title="{{ $hasFeedback ? 'View Feedback' : 'Give Feedback' }}">
                                                <i
                                                    class="bi {{ $hasFeedback ? 'bi-chat-quote' : 'bi-chat-quote-fill' }}"></i>
                                                @if (!$hasFeedback)
                                                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                                        <span
                                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                                                        <span
                                                            class="relative inline-flex rounded-full h-3 w-3 bg-purple-500"></span>
                                                    </span>
                                                @endif
                                            </a>
                                        @endif
                                    @endif

                                    @if (Auth::user()->role !== 'hr' || $canManage)
                                        <a href="{{ route('sessions.show', $session) }}"
                                            class="p-2 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors"
                                            title="View Details">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    @endif

                                    @if ($canManage)
                                        <a href="{{ route('sessions.export', $session) }}"
                                            class="p-2 bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors"
                                            title="Export Excel">
                                            <i class="bi bi-file-earmark-excel-fill"></i>
                                        </a>
                                        <form action="{{ route('sessions.toggle', $session) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="p-2 rounded-lg transition-colors {{ $session->status === 'open' ? 'bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:hover:bg-amber-900/40' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/40' }}"
                                                title="{{ $session->status === 'open' ? 'Close Session' : 'Open Session' }}">
                                                <i
                                                    class="bi bi-{{ $session->status === 'open' ? 'stop-fill' : 'play-fill' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="8" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <i class="bi bi-calendar-x text-4xl mb-3 opacity-50"></i>
                                    <p class="font-medium">No sessions found matching your filters.</p>
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-4">
            @forelse ($sessions as $session)
                @php
                    $myRecord = $session->records->first();
                @endphp
                <x-card
                    class="relative overflow-hidden border-l-4 {{ $session->status === 'open' ? 'border-l-green-500' : 'border-l-slate-300 dark:border-l-slate-600' }}">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span
                                class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-300 dark:bg-slate-800 text-slate-500 dark:text-slate-400 mb-2 inline-block">
                                {{ $session->committee->name ?? 'General' }}
                            </span>
                            <h3 class="font-bold text-slate-800 dark:text-white text-lg leading-tight">
                                {{ $session->title }}</h3>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                <i class="bi bi-calendar-event"></i>
                                {{ $session->created_at->format('M d, h:i A') }}
                            </p>
                        </div>
                        <span
                            class="px-2 py-1 rounded-full text-xs font-bold {{ $session->status === 'open' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-slate-300 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                            {{ ucfirst($session->status) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4 mb-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex flex-col">
                            @php
                                $canManage =
                                    in_array(Auth::user()->role, ['top_management', 'board']) ||
                                    (isset($authorizedCommitteeIds) &&
                                        in_array($session->committee_id, $authorizedCommitteeIds));
                            @endphp
                            @if ($canManage)
                                <span
                                    class="text-[10px] uppercase text-slate-400 font-bold tracking-wider">Attendance</span>
                                <div class="flex items-center gap-1.5 font-bold text-slate-700 dark:text-slate-300">
                                    <i class="bi bi-people-fill text-brand-blue"></i> {{ $session->records_count }}
                                </div>
                            @else
                                <span class="text-[10px] uppercase text-slate-400 font-bold tracking-wider">My
                                    Status</span>
                                @if ($myRecord)
                                    <div
                                        class="font-bold {{ $myRecord->status === 'present' ? 'text-green-600' : ($myRecord->status === 'late' ? 'text-amber-600' : 'text-red-500') }}">
                                        {{ ucfirst($myRecord->status) }}
                                    </div>
                                @else
                                    <div class="text-slate-400 font-medium text-xs">Not Recorded</div>
                                @endif
                            @endif
                        </div>
                        <div class="w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase text-slate-400 font-bold tracking-wider">Threshold</span>
                            <div class="flex items-center gap-1.5 font-bold text-slate-700 dark:text-slate-300">
                                <i class="bi bi-stopwatch text-amber-500"></i> {{ $session->late_threshold_minutes }}m
                            </div>
                        </div>
                        <div class="w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase text-slate-400 font-bold tracking-wider">Creator</span>
                            <div
                                class="flex items-center gap-1.5 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                {{ $session->creator->name }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        @if ($session->status === 'closed')
                            @if ($canManage && Auth::user()->role !== 'hr')
                                <a href="{{ route('sessions.feedback-results', $session) }}"
                                    class="flex items-center justify-center gap-2 px-3 py-2 bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400 rounded-xl font-bold text-sm hover:bg-purple-100 transition-colors">
                                    <i class="bi bi-chat-right-text-fill"></i> Feedback
                                </a>
                            @elseif ($myRecord && in_array($myRecord->status, ['present', 'late']))
                                @php
                                    $hasFeedback = $session->feedbacks->isNotEmpty();
                                @endphp
                                <a href="{{ route('sessions.feedback.create', $session) }}"
                                    class="flex items-center justify-center gap-2 px-3 py-2 {{ $hasFeedback ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' : 'bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400 hover:bg-purple-100' }} rounded-xl font-bold text-sm transition-colors relative overflow-hidden">
                                    @if (!$hasFeedback)
                                        <div class="absolute inset-0 bg-purple-500/10 animate-pulse"></div>
                                    @endif
                                    <i
                                        class="bi {{ $hasFeedback ? 'bi-chat-quote' : 'bi-chat-quote-fill transform scale-110' }}"></i>
                                    {{ $hasFeedback ? 'View Feedback' : 'Give Feedback' }}
                                </a>
                            @endif
                        @endif

                        @if (Auth::user()->role !== 'hr' || $canManage)
                            <a href="{{ route('sessions.show', $session) }}"
                                class="flex items-center justify-center gap-2 px-3 py-2 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-xl font-bold text-sm hover:bg-blue-100 transition-colors">
                                <i class="bi bi-eye"></i> View
                            </a>
                        @endif

                        @if ($canManage)
                            <a href="{{ route('sessions.export', $session) }}"
                                class="flex items-center justify-center gap-2 px-3 py-2 bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400 rounded-xl font-bold text-sm hover:bg-green-100 transition-colors">
                                <i class="bi bi-download"></i> Excel
                            </a>
                            <form action="{{ route('sessions.toggle', $session) }}" method="POST" class="col-span-1">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl font-bold text-sm transition-colors {{ $session->status === 'open' ? 'bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400' }}">
                                    <i class="bi bi-{{ $session->status === 'open' ? 'stop-fill' : 'play-fill' }}"></i>
                                    {{ $session->status === 'open' ? 'Close' : 'Open' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </x-card>
            @empty
                <x-card class="text-center py-12 text-slate-500">
                    <i class="bi bi-calendar-x text-4xl mb-3 opacity-50 inline-block"></i>
                    <p>No sessions found.</p>
                </x-card>
            @endforelse
        </div>

        @if ($sessions->hasPages())
            @php
                $sessions->appends(request()->query());
            @endphp
            <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-800 pt-4">
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    Showing <span class="font-bold">{{ $sessions->firstItem() }}</span> to <span
                        class="font-bold">{{ $sessions->lastItem() }}</span> of <span
                        class="font-bold">{{ $sessions->total() }}</span> sessions
                </div>
                <div class="flex gap-2">
                    <a href="{{ $sessions->previousPageUrl() }}"
                        class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-300 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-700 disabled:opacity-50 transition-colors {{ $sessions->onFirstPage() ? 'pointer-events-none opacity-50' : '' }}">
                        <i class="bi bi-chevron-left"></i>
                    </a>

                    @foreach ($sessions->getUrlRange(max(1, $sessions->currentPage() - 1), min($sessions->lastPage(), $sessions->currentPage() + 1)) as $page => $url)
                        <a href="{{ $url }}"
                            class="px-3 py-1.5 rounded-lg text-sm font-bold border {{ $page == $sessions->currentPage() ? 'bg-brand-blue border-brand-blue text-white' : 'bg-slate-300 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-300' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    <a href="{{ $sessions->nextPageUrl() }}"
                        class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-300 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-700 disabled:opacity-50 transition-colors {{ !$sessions->hasMorePages() ? 'pointer-events-none opacity-50' : '' }}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
