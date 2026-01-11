<x-app-layout>
    @php
        // Members and HR are viewers here. HR might be read-only if not in committee.
        $isReadOnly = Auth::user()->role === 'hr' && !Auth::user()->committees->contains($task->committee_id);
    @endphp

    <div>
        {{-- Header Navigation --}}
        <div class="mb-6">
            <a href="{{ route('tasks.index', ['committee_id' => $task->committee_id]) }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 dark:hover:text-slate-300 transition-colors">
                <i class="bi bi-arrow-left"></i> Back to Tasks
            </a>
        </div>

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Left Column: Task Details & Submission Form (2/3) --}}
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

                {{-- Submission Action Box --}}
                @if (auth()->user()->can('submit', $task))
                    <div class="relative group">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-brand-blue to-cyan-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-500">
                        </div>
                        <div
                            class="relative bg-white dark:bg-slate-800 rounded-2xl p-6 md:p-8 border border-slate-100 dark:border-slate-700 shadow-sm">
                            @if (isset($mySubmission))
                                {{-- READ ONLY VIEW --}}
                                <div class="flex items-center gap-4 mb-6">
                                    <div
                                        class="h-12 w-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-sm">
                                        <i class="bi bi-check-circle-fill text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 dark:text-white text-xl">Submission Complete
                                        </h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                            You have successfully submitted this task.
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    {{-- Link Readonly --}}
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2 pl-1">
                                            Submission Link
                                        </label>
                                        <a href="{{ $mySubmission->submission_link }}" target="_blank"
                                            class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl group hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                            <div
                                                class="h-10 w-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                                <i class="bi bi-link-45deg text-xl"></i>
                                            </div>
                                            <span
                                                class="text-blue-600 dark:text-blue-400 font-bold truncate flex-1 min-w-0">
                                                {{ $mySubmission->submission_link }}
                                            </span>
                                            <i class="bi bi-box-arrow-up-right text-slate-400"></i>
                                        </a>
                                    </div>

                                    {{-- Note Readonly --}}
                                    @if ($mySubmission->note)
                                        <div>
                                            <label
                                                class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2 pl-1">
                                                Comments
                                            </label>
                                            <div
                                                class="p-4 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 italic text-sm">
                                                "{{ $mySubmission->note }}"
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Feedback Section for Member --}}
                                    @if ($mySubmission->status == 'reviewed')
                                        <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-700">
                                            <h5 class="text-sm font-bold text-slate-500 uppercase tracking-wide mb-3">
                                                Feedback from Reviewer</h5>
                                            <div
                                                class="bg-green-50/50 dark:bg-green-900/5 rounded-xl border border-green-100 dark:border-green-900/20 p-4">
                                                <div class="flex items-center justify-between mb-3">
                                                    <h4
                                                        class="text-sm font-bold text-green-800 dark:text-green-400 flex items-center gap-2">
                                                        <i class="bi bi-check-circle-fill"></i> Reviewed
                                                    </h4>
                                                    @if ($mySubmission->rating)
                                                        <span
                                                            class="px-2 py-1 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-green-100 dark:border-green-900/30 text-sm font-bold text-green-700 dark:text-green-400">
                                                            {{ $mySubmission->rating }}/10
                                                        </span>
                                                    @endif
                                                </div>
                                                @if ($mySubmission->feedback)
                                                    <p
                                                        class="text-sm text-green-700 dark:text-green-300/80 leading-relaxed">
                                                        {{ $mySubmission->feedback }}</p>
                                                @else
                                                    <p class="text-sm text-green-600/60 italic">No text feedback
                                                        provided.
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Footer Info --}}
                                    <div
                                        class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 border-t border-slate-100 dark:border-slate-700 pt-4 font-medium">
                                        <i class="bi bi-clock"></i>
                                        Submitted on {{ $mySubmission->submitted_at->format('M d, Y h:i A') }}
                                        @if ($mySubmission->is_late)
                                            <span class="text-red-500 font-bold ml-1">(Late)</span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                {{-- SUBMISSION FORM --}}
                                <div class="flex items-center gap-4 mb-8">
                                    <div
                                        class="h-12 w-12 rounded-2xl bg-brand-blue/10 flex items-center justify-center text-brand-blue shadow-sm">
                                        <i class="bi bi-send-fill text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 dark:text-white text-xl">Your Submission
                                        </h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Submit your work
                                            for this
                                            task directly below.</p>
                                    </div>
                                </div>

                                <form action="{{ route('tasks.submit', $task) }}" method="POST" class="space-y-6">
                                    @csrf
                                    <div>
                                        <label for="submission_link"
                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2 pl-1">
                                            Submission Link
                                        </label>
                                        <div class="relative group/input">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <i
                                                    class="bi bi-link-45deg text-slate-400 group-focus-within/input:text-brand-blue text-2xl transition-colors"></i>
                                            </div>
                                            <input type="url" name="submission_link" id="submission_link"
                                                placeholder="Paste your link here (e.g., Google Drive, GitHub)..."
                                                required
                                                class="w-full pl-12 pr-4 py-3.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-base font-medium focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-all placeholder:text-slate-400"
                                                value="">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="note"
                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2 pl-1">
                                            Comments <span
                                                class="text-slate-400 font-normal lowercase ml-1">(Optional)</span>
                                        </label>
                                        <textarea name="note" id="note" rows="3" placeholder="Add any notes for the reviewer..."
                                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-all placeholder:text-slate-400 resize-none"></textarea>
                                    </div>

                                    <div class="pt-2">
                                        <button type="submit"
                                            class="w-full md:w-auto md:min-w-[200px] py-3.5 px-8 bg-brand-blue hover:bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-brand-blue/20 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-3 group/btn">
                                            <span>Submit Work</span>
                                            <i
                                                class="bi bi-send-fill group-hover/btn:translate-x-1 group-hover/btn:-translate-y-1 transition-transform"></i>
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Column: Metadata (1/3) --}}
            <div class="space-y-6">

                {{-- Meta Card --}}
                <div
                    class="bg-slate-300 dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700">
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
                </div>

            </div>
        </div>

    </div>
</x-app-layout>ion
