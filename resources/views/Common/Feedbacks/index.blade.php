@extends('Common.Layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Session Feedbacks</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Review feedback and ratings for attendance sessions
            </p>
        </div>
    </div>

    <!-- Filter Card -->
    <x-card class="mb-8 border-none ring-1 ring-slate-200/50 dark:ring-slate-700/50">
        <x-slot name="header">
            <h3 class="font-bold text-base text-slate-800 dark:text-white flex items-center gap-2">
                <div class="p-1.5 rounded-lg bg-brand-blue/10 text-brand-blue">
                    <i class="bi bi-funnel-fill"></i>
                </div>
                Filter Options
            </h3>
        </x-slot>
        <form action="{{ route('feedbacks.index') }}" method="GET"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div>
                <x-input-label for="committee_id" value="Committee" class="mb-1.5" />
                <x-select-input name="committee_id" id="committee_id" class="w-full">
                    <option value="">All Committees</option>
                    @foreach ($committees as $committee)
                        <option value="{{ $committee->id }}"
                            {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                            {{ $committee->name }}
                        </option>
                    @endforeach
                </x-select-input>
            </div>
            <div class="flex items-end gap-3">
                <x-primary-button type="submit" class="flex-1 justify-center py-2.5 shadow-md shadow-brand-blue/10">
                    <i class="bi bi-search"></i>
                </x-primary-button>
                <x-secondary-button href="{{ route('feedbacks.index') }}" class="flex-1 justify-center py-2.5">
                    <i class="bi bi-x-lg"></i>
                </x-secondary-button>
            </div>
        </form>
    </x-card>

    @if ($sessions->isEmpty())
        <x-card class="text-center py-12 text-slate-500">
            <i class="bi bi-chat-square-text text-4xl mb-3 opacity-50 inline-block"></i>
            <p>No feedback records found.</p>
        </x-card>
    @else
        <div class="space-y-6">
            <!-- Desktop Table -->
            <div class="hidden md:block">
                <x-card class="p-0 overflow-hidden" :embedded="true">
                    <x-table>
                        <x-slot name="head">
                            <x-table.th class="w-1/12">#</x-table.th>
                            <x-table.th class="w-4/12">Session Title</x-table.th>
                            <x-table.th class="w-2/12">Committee</x-table.th>
                            <x-table.th class="w-2/12">Date</x-table.th>
                            <x-table.th class="w-1/12 text-center">Reviews</x-table.th>
                            <x-table.th class="w-1/12 text-center">Avg Rating</x-table.th>
                            <x-table.th class="w-1/12 text-right">Actions</x-table.th>
                        </x-slot>
                        @foreach ($sessions as $index => $session)
                            <x-table.tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <x-table.td class="text-slate-500">
                                    {{ $sessions->firstItem() + $index }}
                                </x-table.td>
                                <x-table.td>
                                    <span
                                        class="font-bold text-slate-800 dark:text-slate-200 block text-wrap leading-tight">
                                        {{ $session['title'] }}
                                    </span>
                                </x-table.td>
                                <x-table.td>
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        {{ $session['committee'] }}
                                    </span>
                                </x-table.td>
                                <x-table.td class="text-slate-500 text-xs font-medium">
                                    {{ $session['date'] }}
                                </x-table.td>
                                <x-table.td align="center">
                                    <div
                                        class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-900/20 text-brand-blue dark:text-blue-400 text-xs font-bold">
                                        {{ $session['feedback_count'] }}
                                    </div>
                                </x-table.td>
                                <x-table.td align="center">
                                    <div
                                        class="flex items-center justify-center gap-1.5 font-bold text-slate-700 dark:text-slate-300">
                                        <span>{{ number_format($session['avg_rating'], 1) }}</span>
                                        <i class="bi bi-star-fill text-amber-400 text-xs"></i>
                                    </div>
                                </x-table.td>
                                <x-table.td align="right">
                                    <a href="{{ route('sessions.feedback-results', $session['id']) }}"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-500 hover:bg-brand-blue hover:text-white transition-all"
                                        title="View Details">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </x-table.td>
                            </x-table.tr>
                        @endforeach
                    </x-table>
                </x-card>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-4">
                @foreach ($sessions as $session)
                    <x-card>
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <div>
                                <span
                                    class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 mb-2">
                                    {{ $session['committee'] }}
                                </span>
                                <h3 class="font-bold text-slate-800 dark:text-white leading-snug">
                                    {{ $session['title'] }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                    <i class="bi bi-calendar-event"></i> {{ $session['date'] }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center justify-end gap-1 mb-1">
                                    <span
                                        class="text-lg font-black text-slate-800 dark:text-white">{{ number_format($session['avg_rating'], 1) }}</span>
                                    <i class="bi bi-star-fill text-amber-400 text-xs"></i>
                                </div>
                                <span
                                    class="text-[10px] uppercase font-bold text-slate-400">{{ $session['feedback_count'] }}
                                    Reviews</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                            <a href="{{ route('sessions.feedback-results', $session['id']) }}"
                                class="flex items-center justify-center w-full py-2 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 font-bold text-sm transition-colors">
                                View Details <i class="bi bi-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </x-card>
                @endforeach
            </div>

            @if ($sessions->hasPages())
                <div class="pt-4">
                    {{ $sessions->links('components.pagination') }}
                </div>
            @endif
        </div>
    @endif
@endsection
