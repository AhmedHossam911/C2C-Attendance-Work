@extends('Common.Layouts.app')

@section('content')
    <div class="mb-6">
        <a href="{{ route('reports.session_quality') }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-brand-blue transition-colors">
            <i class="bi bi-arrow-left"></i> Back to Q-Index
        </a>
    </div>

    <div class="mb-8">
        <div class="flex items-center gap-2 mb-2">
            <span
                class="px-3 py-1 rounded-md bg-slate-100 dark:bg-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300">
                {{ $session->committee->name ?? 'General' }}
            </span>
            <span class="text-slate-400 text-sm">•</span>
            <span class="text-slate-500 dark:text-slate-400 text-sm">{{ $session->created_at->format('M d, Y') }}</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">
            {{ $session->title }}
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Feedback Report
        </p>
    </div>

    @if ($feedbacks->isEmpty())
        <x-card class="text-center py-12">
            <div
                class="inline-flex h-16 w-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 items-center justify-center mb-4">
                <i class="bi bi-chat-square-text text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">No Feedback Submitted</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">This session has not received any reviews yet.</p>
        </x-card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($feedbacks as $feedback)
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-full {{ $shouldAnonymize ? 'bg-slate-200 text-slate-500' : 'bg-gradient-to-br from-brand-blue to-brand-teal text-white' }} flex items-center justify-center font-bold">
                                @if ($shouldAnonymize)
                                    <i class="bi bi-person-fill"></i>
                                @else
                                    {{ substr($feedback->user_name, 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white text-sm">
                                    {{ $feedback->user_name }}
                                </h4>
                                <div class="flex text-amber-400 text-[10px] gap-0.5 mt-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i
                                            class="bi bi-star{{ $i <= round(($feedback->session_rating + $feedback->instructor_rating) / 2) ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <span class="text-xs text-slate-400">{{ $feedback->created_at->diffForHumans() }}</span>
                    </div>

                    <!-- Ratings Grid -->
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-2 rounded-lg">
                            <span class="block text-[10px] text-slate-500 uppercase font-bold">Objectives</span>
                            <span class="font-bold text-brand-blue">{{ $feedback->objectives_clarity }}/5</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-2 rounded-lg">
                            <span class="block text-[10px] text-slate-500 uppercase font-bold">Instructor</span>
                            <span class="font-bold text-brand-blue">{{ $feedback->instructor_understanding }}/5</span>
                        </div>
                    </div>

                    <!-- Comment -->
                    @if ($feedback->feedback)
                        <div
                            class="text-sm text-slate-600 dark:text-slate-300 italic bg-amber-50 dark:bg-amber-900/10 p-3 rounded-xl border border-amber-100 dark:border-amber-800/20">
                            "{{ $feedback->feedback }}"
                        </div>
                    @else
                        <div class="text-xs text-slate-400 italic">No written comment.</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection
