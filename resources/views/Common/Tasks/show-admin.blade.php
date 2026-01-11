@extends('Common.Layouts.app')

@section('content')
    @php
        $isReadOnly = Auth::user()->role === 'top_management' || Auth::user()->role === 'board';
    @endphp

    <div x-data="{
        showReviewModal: false,
        submissionId: null,
        rating: '',
        feedback: '',
        status: 'reviewed',
        openReviewModal(id, currentRating, currentFeedback, currentStatus) {
            this.submissionId = id;
            this.rating = currentRating || '';
            this.feedback = currentFeedback || '';
            this.status = currentStatus || 'reviewed';
            this.showReviewModal = true;
        }
    }">
        {{-- Header Navigation --}}
        <div class="mb-6">
            <a href="{{ route('tasks.index', ['committee_id' => $task->committee_id]) }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 dark:hover:text-slate-300 transition-colors">
                <i class="bi bi-arrow-left"></i> Back to Tasks
            </a>
        </div>

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Left Column: Task Details & Submissions (2/3) --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Task Content Card --}}
                <div
                    class="bg-slate-300 dark:bg-slate-800 rounded-2xl p-6 md:p-8 shadow-sm border border-slate-200 dark:border-slate-700">
                    {{-- Header --}}
                    <div class="border-b border-slate-200 dark:border-slate-700 pb-6 mb-6">
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white leading-tight">
                                {{ $task->title }}
                            </h1>
                            @if ($task->deadline->isPast())
                                <span
                                    class="shrink-0 px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400 rounded-lg text-xs font-bold uppercase tracking-wide">
                                    Late
                                </span>
                            @else
                                <span
                                    class="shrink-0 px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400 rounded-lg text-xs font-bold uppercase tracking-wide">
                                    Open
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                            <span
                                class="font-bold text-slate-700 dark:text-slate-300">{{ $task->creator->name ?? 'Unknown' }}</span>
                            <span>opened this task {{ $task->created_at->diffForHumans() }}</span>
                            <span class="mx-1">•</span>
                            <span>{{ $task->submissions_count }} submissions</span>
                        </div>
                    </div>

                    {{-- Read-Only Indicator --}}
                    @if ($isReadOnly)
                        <div
                            class="mb-6 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl flex items-center gap-2 text-amber-800 dark:text-amber-400 text-sm font-medium">
                            <i class="bi bi-eye-fill"></i>
                            You are viewing this task with limited permissions.
                        </div>
                    @endif

                    {{-- Description --}}
                    <div
                        class="prose prose-slate dark:prose-invert max-w-none prose-p:leading-relaxed prose-headings:font-bold prose-img:rounded-xl">
                        @if ($task->description)
                            {!! $task->description !!}
                        @else
                            <p class="text-slate-500 italic">No description provided.</p>
                        @endif
                    </div>
                </div>

                {{-- Submissions Section --}}
                @can('viewAllSubmissions', $task)
                    <div class="pt-4">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                <i class="bi bi-chat-square-text-fill text-brand-blue"></i>
                                Submissions ({{ $task->submissions_count }})
                            </h3>

                            <form method="GET" action="{{ route('tasks.show', $task) }}"
                                class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                                {{-- Status Filter --}}
                                <select name="status" onchange="this.form.submit()"
                                    class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-brand-blue focus:border-transparent">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>
                                        Reviewed</option>
                                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late Only
                                    </option>
                                </select>

                                {{-- Search Input --}}
                                <div class="relative">
                                    <input type="text" name="search" placeholder="Search member..."
                                        value="{{ request('search') }}"
                                        class="w-full sm:w-48 pl-3 pr-8 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-brand-blue focus:border-transparent">
                                    @if (request('search'))
                                        <a href="{{ route('tasks.show', $task) }}"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </a>
                                    @else
                                        <button type="submit"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand-blue">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>

                        @if ($submissions->count() > 0)
                            <div class="space-y-4">
                                @foreach ($submissions as $sub)
                                    <div
                                        class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden hover:border-brand-blue/30 transition-all shadow-sm hover:shadow-md">
                                        {{-- Submission Header --}}
                                        <div
                                            class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 text-sm font-bold shadow-sm">
                                                    {{ substr($sub->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900 dark:text-white text-base">
                                                        {{ $sub->user->name }}</div>
                                                    <div
                                                        class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                                        <span><i class="bi bi-clock"></i>
                                                            {{ $sub->submitted_at->format('M d, H:i') }}</span>
                                                        <span>•</span>
                                                        <span>{{ $sub->submitted_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 self-start sm:self-center">
                                                @if ($sub->is_late)
                                                    <span
                                                        class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase bg-red-50 text-red-600 border border-red-100 dark:bg-red-900/10 dark:text-red-400 dark:border-red-900/20">
                                                        Late
                                                    </span>
                                                @endif
                                                @if ($sub->status == 'reviewed')
                                                    <span
                                                        class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase bg-green-50 text-green-600 border border-green-100 dark:bg-green-900/10 dark:text-green-400 dark:border-green-900/20">
                                                        Reviewed
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase bg-slate-50 text-slate-600 border border-slate-100 dark:bg-slate-700 dark:text-slate-300 dark:border-slate-600">
                                                        Pending
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Submission Content --}}
                                        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">
                                            {{-- Left: Submission Details --}}
                                            <div class="space-y-4">
                                                <div class="space-y-1">
                                                    <label
                                                        class="text-xs font-bold text-slate-400 uppercase tracking-wider">Submission</label>
                                                    <a href="{{ $sub->submission_link }}" target="_blank"
                                                        class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/20 rounded-xl group/link hover:bg-blue-100 dark:hover:bg-blue-900/20 transition-colors">
                                                        <div
                                                            class="h-8 w-8 rounded-lg bg-blue-100 dark:bg-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-300">
                                                            <i class="bi bi-link-45deg text-lg"></i>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p
                                                                class="text-sm font-bold text-blue-700 dark:text-blue-400 truncate w-full">
                                                                Open Link</p>
                                                            <p
                                                                class="text-xs text-blue-600/70 dark:text-blue-400/70 truncate w-full">
                                                                {{ $sub->submission_link }}</p>
                                                        </div>
                                                        <i class="bi bi-box-arrow-up-right text-blue-400"></i>
                                                    </a>
                                                </div>

                                                @if ($sub->note)
                                                    <div class="space-y-1">
                                                        <label
                                                            class="text-xs font-bold text-slate-400 uppercase tracking-wider">Note</label>
                                                        <div
                                                            class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                                            <p class="text-sm text-slate-600 dark:text-slate-300 italic">
                                                                "{{ $sub->note }}"</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Right: Review Section --}}
                                            <div class="relative">
                                                @if ($sub->status == 'reviewed')
                                                    <div
                                                        class="h-full bg-green-50/50 dark:bg-green-900/5 rounded-xl border border-green-100 dark:border-green-900/20 p-4">
                                                        <div class="flex items-center justify-between mb-3">
                                                            <h4
                                                                class="text-sm font-bold text-green-800 dark:text-green-400 flex items-center gap-2">
                                                                <i class="bi bi-check-circle-fill"></i> Feedback
                                                            </h4>
                                                            @if ($sub->rating)
                                                                <span
                                                                    class="px-2 py-1 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-green-100 dark:border-green-900/30 text-sm font-bold text-green-700 dark:text-green-400">
                                                                    {{ $sub->rating }}/10
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if ($sub->feedback)
                                                            <p
                                                                class="text-sm text-green-700 dark:text-green-300/80 leading-relaxed">
                                                                {{ $sub->feedback }}</p>
                                                        @else
                                                            <p class="text-sm text-green-600/60 italic">No text feedback
                                                                provided.</p>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div
                                                        class="h-full flex flex-col items-center justify-center p-6 text-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl">
                                                        <i class="bi bi-hourglass-split text-2xl text-slate-300 mb-2"></i>
                                                        <p class="text-sm text-slate-400 font-medium">No review yet</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Action Footer --}}
                                        @if (!$isReadOnly)
                                            <div
                                                class="bg-slate-50 dark:bg-slate-700/20 px-5 py-3 border-t border-slate-100 dark:border-slate-700/50 flex justify-end">
                                                <button
                                                    @click="openReviewModal({{ $sub->id }}, '{{ $sub->rating }}', '{{ addslashes($sub->feedback) }}', '{{ $sub->status }}')"
                                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-xl border border-slate-200 dark:border-slate-600 shadow-sm transition-all">
                                                    <i class="bi bi-pencil-square text-brand-blue"></i>
                                                    {{ $sub->status == 'reviewed' ? 'Edit Review' : 'Add Review' }}
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="p-12 text-center bg-slate-50 dark:bg-slate-800/50 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                                <div
                                    class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="bi bi-inbox text-2xl text-slate-400"></i>
                                </div>
                                <h3 class="text-slate-900 dark:text-white font-bold mb-1">No submissions found</h3>
                                <p class="text-slate-500 text-sm">Try adjusting your filters or search.</p>
                            </div>
                        @endif
                    </div>
                @endcan

                {{-- Pagination --}}
                @if (isset($submissions) &&
                        $submissions instanceof \Illuminate\Pagination\LengthAwarePaginator &&
                        $submissions->hasPages())
                    <div class="mt-8">
                        <div
                            class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700 flex justify-center">
                            {{ $submissions->links('components.pagination') }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Column: Metadata & Actions (1/3) --}}
            <div class="space-y-6">

                {{-- Meta Card --}}
                <div class="bg-slate-300 dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700">
                    <h4
                        class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 border-b border-slate-200 dark:border-slate-700 pb-2">
                        Details</h4>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 text-sm">Committee</span>
                            <span
                                class="font-bold text-slate-800 dark:text-white text-sm">{{ $task->committee->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 text-sm">Type</span>
                            <span
                                class="font-bold text-slate-800 dark:text-white text-sm uppercase">{{ $task->type }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 text-sm">Deadline</span>
                            <span
                                class="font-bold {{ $task->deadline->isPast() ? 'text-red-500' : 'text-slate-800 dark:text-white' }} text-sm">
                                {{ $task->deadline->format('M d') }}
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    @if (!$isReadOnly)
                        @can('update', $task)
                            <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700 space-y-2">
                                <a href="{{ route('tasks.edit', $task) }}"
                                    class="flex items-center justify-center w-full py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-white font-bold rounded-xl text-sm hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                                    Edit Task
                                </a>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                    onsubmit="return confirm('Delete this task?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="flex items-center justify-center w-full py-2 text-red-600 font-bold rounded-xl text-sm hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        Delete Task
                                    </button>
                                </form>
                            </div>
                        @endcan
                    @endif
                </div>


            </div>
        </div>

        <!-- Alpine Listener for Modal -->
        <div x-show="showReviewModal" x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            style="display: none;">
            <div @click.away="showReviewModal = false"
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full m-4 overflow-hidden">
                @include('Common.Tasks.partials.review-modal-content')
            </div>
        </div>

    </div>
@endsection
