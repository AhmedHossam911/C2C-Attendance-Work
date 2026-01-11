@extends('Common.Layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="mb-2">
            <a href="{{ route('sessions.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-brand-blue transition-colors">
                <i class="bi bi-arrow-left"></i> Back to Sessions
            </a>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Feedback Results</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    {{ $session->title }} • {{ $session->created_at->format('M d, Y') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="px-3 py-1 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-full text-xs font-bold">
                    {{ $stats['total'] }} Responses
                </span>
            </div>
        </div>

        @if ($stats['total'] > 0)
            <!-- Key Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Satisfaction --}}
                <x-card class="bg-gradient-to-br from-blue-500 to-blue-600 text-white border-0">
                    <div class="flex flex-col h-full justify-between">
                        <div class="text-blue-100 text-sm font-medium mb-2">Overall Satisfaction</div>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-bold">{{ number_format($stats['avg_satisfaction'], 1) }}</span>
                            <span class="text-blue-200 text-sm mb-1">/ 10</span>
                        </div>
                    </div>
                </x-card>

                {{-- Objectives --}}
                <x-card>
                    <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">Objectives Clarity</div>
                    <div class="flex items-center gap-3">
                        <div class="text-2xl font-bold text-slate-800 dark:text-white">
                            {{ number_format($stats['avg_objectives'], 1) }}</div>
                        <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500" style="width: {{ ($stats['avg_objectives'] / 5) * 100 }}%">
                            </div>
                        </div>
                    </div>
                    <div class="text-xs text-slate-400 mt-1">Average rating (1-5)</div>
                </x-card>

                {{-- Instructor --}}
                <x-card>
                    <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">Instructor Understanding
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-2xl font-bold text-slate-800 dark:text-white">
                            {{ number_format($stats['avg_instructor'], 1) }}</div>
                        <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-violet-500" style="width: {{ ($stats['avg_instructor'] / 5) * 100 }}%">
                            </div>
                        </div>
                    </div>
                    <div class="text-xs text-slate-400 mt-1">Average rating (1-5)</div>
                </x-card>

                {{-- System --}}
                <x-card>
                    <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2">System Rating</div>
                    <div class="flex items-center gap-3">
                        <div class="text-2xl font-bold text-slate-800 dark:text-white">
                            {{ number_format($stats['avg_system'], 1) }}</div>
                        <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500" style="width: {{ ($stats['avg_system'] / 10) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="text-xs text-slate-400 mt-1">Average rating (1-10)</div>
                </x-card>
            </div>

            <!-- Room Suitability -->
            <div class="mb-6">
                <x-card>
                    <h3 class="font-bold text-slate-800 dark:text-white mb-4">Room Suitability</h3>
                    <div class="space-y-3">
                        @foreach ($stats['room_suitability'] as $label => $count)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-slate-600 dark:text-slate-400">{{ $label }}</span>
                                    <span class="font-bold text-slate-800 dark:text-white">{{ $count }}</span>
                                </div>
                                <div class="h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-brand-blue"
                                        style="width: {{ ($count / $stats['total']) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>

            <!-- Detailed Feedback -->
            <div class="space-y-4">
                <h3 class="font-bold text-slate-800 dark:text-white">Detailed Feedback</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($feedbacks as $feedback)
                        <x-card
                            class="h-full flex flex-col relative overflow-hidden transition-all border border-slate-200 dark:border-slate-700 hover:border-brand-blue/30 dark:hover:border-brand-blue/30 group">
                            @php
                                $isTopManagement = auth()->user()->role === 'top_management';
                            @endphp

                            {{-- Header --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 rounded-full flex items-center justify-center text-sm font-bold shadow-sm {{ $isTopManagement ? 'bg-gradient-to-br from-brand-blue to-purple-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                        {{ $isTopManagement ? substr($feedback->user->name, 0, 1) : '?' }}
                                    </div>
                                    <div>
                                        @if ($isTopManagement)
                                            <a href="{{ route('reports.member', ['search' => $feedback->user->email]) }}"
                                                class="font-bold text-slate-800 dark:text-white text-sm hover:text-brand-blue hover:underline transition-colors block">
                                                {{ $feedback->user->name }}
                                            </a>
                                        @else
                                            <div class="font-bold text-slate-800 dark:text-white text-sm">
                                                Anonymous Member
                                            </div>
                                        @endif
                                        <div class="text-[10px] text-slate-400">
                                            {{ $feedback->created_at->format('M d, H:i') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end">
                                    <div
                                        class="text-xs font-bold px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-300 mb-1">
                                        Score: {{ $feedback->overall_satisfaction }}/10
                                    </div>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="space-y-3 flex-1 text-sm">
                                @if ($feedback->feedback)
                                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Session Feedback
                                        </p>
                                        <p class="text-slate-600 dark:text-slate-300 italic">
                                            "{{ $feedback->feedback }}"
                                        </p>
                                    </div>
                                @endif

                                @if ($feedback->attendance_system_suggestions)
                                    <div>
                                        <p class="text-[10px] font-bold text-brand-blue uppercase mb-0.5">System
                                            Suggestion
                                        </p>
                                        <p class="text-slate-600 dark:text-slate-400">
                                            {{ $feedback->attendance_system_suggestions }}</p>
                                    </div>
                                @endif

                                @if ($feedback->future_suggestions)
                                    <div>
                                        <p class="text-[10px] font-bold text-brand-teal uppercase mb-0.5">Future
                                            Suggestion
                                        </p>
                                        <p class="text-slate-600 dark:text-slate-400">
                                            {{ $feedback->future_suggestions }}</p>
                                    </div>
                                @endif

                                @if (!$feedback->feedback && !$feedback->attendance_system_suggestions && !$feedback->future_suggestions)
                                    <p class="text-slate-400 italic text-center py-4">No detailed text feedback
                                        provided.
                                    </p>
                                @endif
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($feedbacks->hasPages())
                    <div class="mt-6">
                        {{ $feedbacks->links('components.pagination') }}
                    </div>
                @endif
            </div>
        @else
            <x-card class="py-12 flex flex-col items-center justify-center text-center">
                <div class="p-4 bg-slate-100 dark:bg-slate-800 rounded-full mb-4">
                    <i class="bi bi-chat-square-text text-3xl text-slate-400"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">No Feedback Yet</h3>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mt-1">
                    Once users start submitting feedback for this session, the results will appear here.
                </p>
            </x-card>
        @endif
    </div>
@endsection
