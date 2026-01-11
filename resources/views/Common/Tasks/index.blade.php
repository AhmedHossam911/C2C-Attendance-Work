@extends('Common.Layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight">Tasks</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Manage and track your committee assignments.</p>
        </div>
        @can('create', App\Models\Task::class)
            <a href="{{ route('tasks.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-brand-blue/90 shadow-md transition-all transform hover:-translate-y-0.5">
                <i class="bi bi-plus-lg text-lg"></i>
                <span>New Task</span>
            </a>
        @endcan
    </div>

    {{-- Top Filter Navigation --}}
    <div class="mb-8 overflow-x-auto">
        <div class="flex items-center gap-3 pb-2 min-w-max">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2">Committees:</span>
            @forelse ($committees as $committee)
                <a href="{{ route('tasks.index', ['committee_id' => $committee->id]) }}"
                    class="group flex items-center gap-2 px-4 py-2 rounded-full border transition-all duration-200
                {{ $selectedCommittee && $selectedCommittee->id == $committee->id
                    ? 'bg-brand-blue text-white border-brand-blue shadow-lg shadow-brand-blue/25'
                    : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:border-brand-blue/50 hover:text-brand-blue' }}">
                    <i
                        class="bi bi-collection{{ $selectedCommittee && $selectedCommittee->id == $committee->id ? '-fill' : '' }}"></i>
                    <span class="font-bold text-sm">{{ $committee->name }}</span>
                    @if ($committee->tasks_count > 0)
                        <span
                            class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-md
                        {{ $selectedCommittee && $selectedCommittee->id == $committee->id
                            ? 'bg-white/20 text-white'
                            : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }}">
                            {{ $committee->tasks_count }}
                        </span>
                    @endif
                </a>
            @empty
                <span class="text-slate-500 italic text-sm">No committees available.</span>
            @endforelse
        </div>
    </div>

    {{-- Main Content --}}
    <div>
        @if ($selectedCommittee)
            <div class="animate-fade-in-up">
                {{-- Header & Meta --}}
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-xl text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="bi bi-list-task text-brand-blue"></i>
                        {{ $selectedCommittee->name }}
                        <span class="text-slate-400 text-lg mx-2">/</span>
                        <span class="text-base font-medium text-slate-500">Active Tasks</span>
                    </h3>

                </div>

                {{-- Read-Only Indicator --}}
                @if (Auth::user()->role === 'top_management' ||
                        (Auth::user()->role === 'hr' && !Auth::user()->committees->contains($selectedCommittee->id)))
                    <div
                        class="mb-6 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl flex items-center gap-2 text-amber-800 dark:text-amber-400 text-sm font-medium">
                        <i class="bi bi-eye-fill"></i>
                        You are viewing this committee's tasks in read-only mode.
                    </div>
                @endif

                {{-- Determine View Type --}}
                @php
                    $isManagement = in_array(Auth::user()->role, ['top_management', 'board', 'committee_head']);

                    // HR is management (unified view) if they are viewing a committee they are NOT a member of
                    if (Auth::user()->role === 'hr') {
                        if ($selectedCommittee && !Auth::user()->committees->contains($selectedCommittee->id)) {
                            $isManagement = true;
                        }
                    }

                    $showSplitView = !$isManagement;
                @endphp

                {{-- Split View for Members (and HR for own committee) --}}
                @if ($showSplitView)
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {{-- Pending Tasks Card --}}
                        <div
                            class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col h-full">
                            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                                <div
                                    class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 flex items-center justify-center">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-lg">Pending Tasks</h4>
                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Action Required</p>
                                </div>
                            </div>

                            <div class="space-y-4 flex-1">
                                @php
                                    $pendingTasks = $tasks->filter(fn($t) => $t->submissions->isEmpty());
                                @endphp

                                @forelse($pendingTasks as $task)
                                    <div
                                        class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 hover:border-brand-blue/30 transition-colors group">
                                        <div class="flex justify-between items-start gap-3">
                                            <div>
                                                <h5
                                                    class="font-bold text-slate-800 dark:text-white mb-1 group-hover:text-brand-blue transition-colors">
                                                    {{ $task->title }}
                                                </h5>
                                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                                    <span
                                                        class="{{ $task->deadline->isPast() ? 'text-red-500 font-bold' : '' }}">
                                                        {{ $task->deadline->format('M d') }}
                                                    </span>
                                                    <span>&bull;</span>
                                                    <span
                                                        class="uppercase text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700">
                                                        {{ $task->type }}
                                                    </span>
                                                </div>
                                            </div>
                                            <a href="{{ route('tasks.show', $task) }}"
                                                class="shrink-0 h-8 w-8 rounded-lg bg-brand-blue text-white flex items-center justify-center shadow-lg shadow-brand-blue/20 hover:scale-105 transition-transform">
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-slate-400">
                                        <i class="bi bi-check2-circle text-4xl mb-2 opacity-50 block"></i>
                                        <p class="text-sm">All caught up! No pending tasks.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Submitted Tasks Card --}}
                        <div
                            class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col h-full">
                            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                                <div
                                    class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-lg">Submitted Tasks</h4>
                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">History & Status
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4 flex-1">
                                @php
                                    $submittedTasks = $tasks->filter(fn($t) => $t->submissions->isNotEmpty());
                                @endphp

                                @forelse($submittedTasks as $task)
                                    @php $submission = $task->submissions->first(); @endphp
                                    <div
                                        class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 hover:border-green-500/30 transition-colors group relative">
                                        <a href="{{ route('tasks.show', $task) }}" class="absolute inset-0 z-10"></a>
                                        <div class="flex justify-between items-start gap-3">
                                            <div>
                                                <h5
                                                    class="font-bold text-slate-800 dark:text-white mb-1 group-hover:text-green-600 transition-colors">
                                                    {{ $task->title }}
                                                </h5>
                                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                                    <span>Submitted {{ $submission->submitted_at->format('M d') }}</span>
                                                    @if ($submission->status == 'reviewed')
                                                        <span
                                                            class="px-1.5 py-0.5 rounded bg-green-100 text-green-700 text-[10px] font-bold uppercase">
                                                            Reviewed
                                                        </span>
                                                    @else
                                                        <span
                                                            class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 text-[10px] font-bold uppercase">
                                                            Pending Review
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div
                                                class="shrink-0 text-slate-300 group-hover:translate-x-1 transition-transform">
                                                <i class="bi bi-chevron-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-slate-400">
                                        <i class="bi bi-clipboard-x text-4xl mb-2 opacity-50 block"></i>
                                        <p class="text-sm">No submitted tasks yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Default List View for Admins --}}
                    <div class="space-y-4">
                        @forelse ($tasks as $task)
                            <div
                                class="group bg-slate-300 dark:bg-slate-800 rounded-2xl p-1 border border-slate-200 dark:border-slate-700 hover:border-brand-blue/30 dark:hover:border-slate-600 transition-all duration-300 hover:shadow-md">
                                <div class="flex flex-col md:flex-row gap-4 p-5">
                                    {{-- Icon / Type Column --}}
                                    <div class="shrink-0 flex md:flex-col items-center md:items-start gap-3 md:w-32">
                                        <div
                                            class="h-12 w-12 rounded-2xl flex items-center justify-center text-xl shadow-sm
                                        {{ $task->type === 'extra' ? 'bg-brand-teal/10 text-brand-teal' : 'bg-brand-blue/10 text-brand-blue' }}">
                                            <i
                                                class="bi bi-{{ $task->type === 'extra' ? 'star-fill' : 'journal-text' }}"></i>
                                        </div>
                                        <span
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider text-center w-auto md:w-full
                                        {{ $task->type === 'extra'
                                            ? 'bg-brand-teal/10 text-brand-teal dark:bg-brand-teal/20 dark:text-teal-300'
                                            : 'bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20 dark:text-blue-300' }}">
                                            {{ ucfirst($task->type) }}
                                        </span>
                                    </div>

                                    {{-- Main Content --}}
                                    <div class="flex-1 min-w-0 py-1">
                                        <div class="flex flex-col h-full justify-between gap-2">
                                            <div>
                                                <h4
                                                    class="font-bold text-lg text-slate-800 dark:text-white mb-1 group-hover:text-brand-blue transition-colors">
                                                    <a href="{{ route('tasks.show', $task) }}" class="hover:underline">
                                                        {{ $task->title }}
                                                    </a>
                                                </h4>

                                                {{-- Meta Row --}}
                                                <div
                                                    class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-500 dark:text-slate-400">
                                                    <div
                                                        class="flex items-center gap-1.5 {{ $task->deadline->isPast() ? 'text-red-500 font-bold' : '' }}">
                                                        <i
                                                            class="bi bi-clock{{ $task->deadline->isPast() ? '-history' : '' }}"></i>
                                                        {{ $task->deadline->format('M d, h:i A') }}
                                                        <span>(Late)</span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5">
                                                        <i class="bi bi-person-circle"></i>
                                                        {{ $task->creator->name ?? 'Unknown' }}
                                                    </div>
                                                    <div class="flex items-center gap-1.5">
                                                        @if ($task->submissions->isNotEmpty())
                                                            <span
                                                                class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                                Submitted
                                                            </span>
                                                        @else
                                                            <span
                                                                class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-400">
                                                                Pending
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Actions / Stats --}}
                                    <div
                                        class="flex md:flex-col items-center md:items-end justify-between md:justify-center gap-4 md:w-48 md:border-l md:border-slate-200 md:dark:border-slate-700/50 md:pl-6">
                                        @if (in_array(Auth::user()->role, ['top_management', 'board', 'committee_head']))
                                            <div class="text-right hidden md:block">
                                                <div
                                                    class="text-2xl font-bold text-slate-700 dark:text-slate-200 leading-none">
                                                    {{ $task->submissions_count }}
                                                </div>
                                                <div
                                                    class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mt-1">
                                                    Submissions
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Mobile Stat --}}
                                        @if (in_array(Auth::user()->role, ['top_management', 'board', 'committee_head']))
                                            <div
                                                class="md:hidden flex items-center gap-2 text-sm font-bold text-slate-600 dark:text-slate-400">
                                                <i class="bi bi-file-earmark-text"></i> {{ $task->submissions_count }}
                                                Submissions
                                            </div>
                                        @endif

                                        <div class="flex items-center gap-2">
                                            @can('update', $task)
                                                <a href="{{ route('tasks.edit', $task) }}"
                                                    class="h-9 w-9 rounded-xl flex items-center justify-center bg-white dark:bg-slate-700 text-slate-500 border border-slate-200 dark:border-slate-600 hover:border-brand-blue hover:text-brand-blue transition-colors"
                                                    title="Edit Task">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                            @endcan
                                            <a href="{{ route('tasks.show', $task) }}"
                                                class="px-4 py-2 bg-slate-800 dark:bg-slate-700 text-white text-sm font-bold rounded-xl hover:bg-brand-blue transition-colors shadow-sm flex items-center gap-2">
                                                View <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="py-16 text-center bg-slate-300 dark:bg-slate-800 rounded-3xl border-2 border-dashed border-slate-300 dark:border-slate-700">
                                <div
                                    class="w-20 h-20 bg-slate-200 dark:bg-slate-700/50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                                    <i class="bi bi-inbox text-4xl"></i>
                                </div>
                                <h4 class="text-xl font-bold text-slate-700 dark:text-slate-300">No tasks found</h4>
                                <p class="text-slate-500 dark:text-slate-500 mt-2">There are no tasks available for this
                                    committee yet.</p>

                                @can('create', App\Models\Task::class)
                                    <a href="{{ route('tasks.create') }}"
                                        class="inline-flex items-center gap-2 mt-6 px-6 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-brand-blue/90 transition-colors">
                                        <i class="bi bi-plus-lg"></i> Create First Task
                                    </a>
                                @endcan
                            </div>
                        @endforelse
                    </div>
                @endif

                {{-- Pagination --}}
                @if ($tasks->hasPages())
                    <div class="mt-8">
                        <div
                            class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700 flex justify-center">
                            {{ $tasks->links('components.pagination') }}
                        </div>
                    </div>
                @endif
            </div>
        @else
            {{-- Empty State (No Committee Selected) --}}
            <div class="min-h-[60vh] flex flex-col items-center justify-center text-center p-8">
                <div class="relative mb-8 group">
                    <div
                        class="absolute inset-0 bg-brand-teal/20 rounded-full blur-2xl group-hover:blur-3xl transition-all duration-500">
                    </div>
                    <div
                        class="relative w-24 h-24 bg-white dark:bg-slate-800 rounded-3xl shadow-xl flex items-center justify-center text-brand-teal text-5xl transform group-hover:scale-110 transition-transform duration-300">
                        <i class="bi bi-grid-fill"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-slate-800 dark:text-white mb-3">Select a Committee</h3>
                <p class="text-lg text-slate-500 dark:text-slate-400 max-w-lg mx-auto leading-relaxed mb-8">
                    Choose a committee from the top navigation bar to access its tasks, submissions, and management tools.
                </p>
                <div class="flex flex-wrap justify-center gap-2 max-w-2xl">
                    @foreach ($committees->take(5) as $committee)
                        <a href="{{ route('tasks.index', ['committee_id' => $committee->id]) }}"
                            class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-slate-600 dark:text-slate-300 font-bold hover:border-brand-blue hover:text-brand-blue transition-colors shadow-sm">
                            {{ $committee->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
