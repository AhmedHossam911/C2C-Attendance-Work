@extends('layouts.app')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('reports.index') }}" class="hover:text-brand-blue transition-colors font-medium">Reports</a>
                <i class="bi bi-chevron-right text-xs opacity-50"></i>
                <span class="text-slate-800 dark:text-slate-200">Session Quality</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Session Quality
                Index</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-xl">
                Sessions ranked by member feedback ratings.
            </p>
        </div>

        <x-card class="p-1 md:p-2 bg-white dark:bg-slate-800 border-0 shadow-sm">
            <form action="{{ route('reports.session_quality') }}" method="GET" class="flex items-center gap-2">
                <div class="relative">
                    <select name="committee_id" onchange="this.form.submit()"
                        class="px-4 py-2 text-sm border-slate-200 dark:border-slate-700 rounded-lg focus:ring-brand-blue focus:border-brand-blue dark:bg-slate-900 dark:text-slate-300 shadow-sm cursor-pointer hover:border-slate-300 transition-colors">
                        <option value="">All Committees</option>
                        @foreach ($committees as $committee)
                            <option value="{{ $committee->id }}"
                                {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                                {{ $committee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </x-card>
    </div>

    @if ($sessions->isEmpty())
        <div
            class="flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50/50 dark:bg-slate-800/50">
            <div
                class="inline-flex h-20 w-20 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 items-center justify-center mb-6">
                <i class="bi bi-star text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100">No Feedback Data</h3>
            <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-2">
                Wait for members to submit feedback on sessions to see ranking data.
            </p>
        </div>
    @else
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead
                        class="bg-slate-50 dark:bg-slate-900/50 text-xs uppercase font-bold text-slate-500 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4 w-12 text-center">Rank</th>
                            <th class="px-6 py-4">Session</th>
                            <th class="px-6 py-4 hidden md:table-cell">Committee</th>
                            <th class="px-6 py-4 hidden sm:table-cell">Date</th>
                            <th class="px-6 py-4 text-center">Feedback</th>
                            <th class="px-6 py-4 text-right">Rating</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @php
                            $startIndex = ($sessions->currentPage() - 1) * $sessions->perPage();
                        @endphp
                        @foreach ($sessions as $index => $session)
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-6 py-4 text-center font-bold">
                                    <div
                                        class="flex items-center justify-center h-8 w-8 rounded-full {{ $startIndex + $index < 3 ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400' }}">
                                        {{ $startIndex + $index + 1 }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">{{ $session['title'] }}
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <span
                                        class="px-2.5 py-1 rounded-md bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-xs font-bold text-slate-600 dark:text-slate-300 shadow-sm">{{ $session['committee'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 hidden sm:table-cell">{{ $session['date'] }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="text-xs font-bold bg-blue-50 dark:bg-blue-900/30 text-brand-blue px-2.5 py-1 rounded-full">{{ $session['feedback_count'] }}
                                        Reviews</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex flex-col items-end">
                                        <div class="flex text-amber-400 text-xs gap-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="bi bi-star{{ $i <= round($session['avg_rating']) ? '-fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <span
                                            class="font-bold text-slate-800 dark:text-slate-200 mt-1">{{ number_format($session['avg_rating'], 1) }}/5.0</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if ($sessions->hasPages())
                    <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                        {{ $sessions->links('components.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
