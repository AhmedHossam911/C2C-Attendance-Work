@extends('Common.Layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                    <a href="{{ route('reports.index') }}"
                        class="hover:text-brand-blue transition-colors font-medium">Reports</a>
                    <i class="bi bi-chevron-right text-[10px] opacity-50"></i>
                    <span class="text-slate-800 dark:text-slate-200">Session Quality</span>
                </div>
                <h2 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight">Session Quality Index</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm max-w-2xl">
                    Performance rankings based on member feedback ratings.
                </p>
            </div>

            <x-card class="p-1.5 bg-white dark:bg-slate-800 border-0 shadow-sm rounded-xl">
                <form action="{{ route('reports.session_quality') }}" method="GET" class="flex items-center gap-2">
                    <div class="relative min-w-[200px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-funnel-fill text-xs"></i>
                        </div>
                        <select name="committee_id" onchange="this.form.submit()"
                            class="w-full pl-9 pr-8 py-2 text-sm font-bold border-0 bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 rounded-lg focus:ring-2 focus:ring-brand-blue/20 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors appearance-none">
                            <option value="">All Committees</option>
                            @foreach ($committees as $committee)
                                <option value="{{ $committee->id }}"
                                    {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                                    {{ $committee->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>

        @if ($sessions->isEmpty())
            <div
                class="py-20 text-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-3xl bg-slate-50/50 dark:bg-slate-800/50">
                <div
                    class="inline-flex h-24 w-24 rounded-full bg-white dark:bg-slate-800 text-slate-300 dark:text-slate-600 shadow-sm items-center justify-center mb-6">
                    <i class="bi bi-inbox-fill text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No Data Available</h3>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                    No session feedback has been recorded specifically for this selection yet.
                </p>
                <div class="mt-8">
                    <a href="{{ route('reports.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                        <i class="bi bi-arrow-left"></i> Go Back
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4">
                @php $startIndex = ($sessions->currentPage() - 1) * $sessions->perPage(); @endphp

                @foreach ($sessions as $index => $session)
                    @php $rank = $startIndex + $index + 1; @endphp
                    <div
                        class="group relative bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-md hover:border-brand-blue/30 dark:hover:border-brand-blue/30 transition-all duration-300">
                        <div class="flex flex-col md:flex-row md:items-center gap-6">

                            <!-- Rank & Info -->
                            <div class="flex items-center gap-5 flex-1">
                                <!-- Rank Badge -->
                                <div class="flex-shrink-0">
                                    <div
                                        class="h-12 w-12 flex items-center justify-center rounded-2xl font-black text-lg shadow-sm {{ $rank <= 3 ? 'bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-amber-500/20' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">
                                        @if ($rank <= 3)
                                            <i class="bi bi-trophy-fill text-xs mr-1 opacity-75"></i>
                                        @endif
                                        {{ $rank }}
                                    </div>
                                </div>

                                <!-- Session Details -->
                                <div>
                                    <h3
                                        class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-brand-blue transition-colors">
                                        {{ $session['title'] }}
                                    </h3>
                                    <div
                                        class="flex items-center gap-3 mt-1 text-xs font-medium text-slate-500 dark:text-slate-400">
                                        <span
                                            class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-700/50 px-2 py-1 rounded-md">
                                            <i class="bi bi-people-fill opacity-70"></i> {{ $session['committee'] }}
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <i class="bi bi-calendar-event opacity-70"></i> {{ $session['date'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Metrics -->
                            <div
                                class="flex items-center justify-between md:justify-end gap-8 pt-4 md:pt-0 border-t md:border-t-0 border-slate-50 dark:border-slate-700/50">
                                <!-- Feedback Count -->
                                <div class="text-center min-w-[80px]">
                                    <span
                                        class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-0.5">Reviews</span>
                                    <div
                                        class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-brand-blue dark:text-blue-400 text-sm font-bold">
                                        <i class="bi bi-chat-text-fill text-xs mr-1.5 opacity-70"></i>
                                        {{ $session['feedback_count'] }}
                                    </div>
                                </div>

                                <!-- Rating -->
                                <div class="text-right min-w-[100px]">
                                    <span
                                        class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-0.5">Avg
                                        Rating</span>
                                    <div class="flex items-center justify-end gap-2">
                                        <span
                                            class="text-xl font-black text-slate-800 dark:text-white">{{ number_format($session['avg_rating'], 1) }}</span>
                                        <div class="flex text-amber-400 text-xs">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="bi bi-star{{ $i <= round($session['avg_rating']) ? '-fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>

                                <!-- Action -->
                                <div class="pl-4 md:border-l border-slate-100 dark:border-slate-700">
                                    @if (!Auth::user()->hasRole('hr'))
                                        <a href="{{ route('reports.feedback_details', $session['id']) }}"
                                            class="inline-flex items-center justify-center h-10 w-10 md:w-auto md:h-auto md:px-5 md:py-2.5 rounded-xl bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold hover:bg-brand-blue hover:text-white dark:hover:bg-brand-blue dark:hover:text-white transition-all shadow-sm active:scale-95">
                                            <span class="hidden md:inline mr-2">Details</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    @else
                                        <span
                                            class="inline-flex items-center justify-center h-10 w-10 md:w-auto md:px-4 md:py-2 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-300 cursor-not-allowed border border-slate-100 dark:border-slate-700">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            @if ($sessions->hasPages())
                <div class="pt-6">
                    {{ $sessions->links('components.pagination') }}
                </div>
            @endif
        @endif
    </div>
@endsection
