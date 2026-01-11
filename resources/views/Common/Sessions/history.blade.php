@extends('Common.Layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">My Attendance History</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">View your session history and feedback.</p>
        </div>
    </div>

    <!-- No Filters as requested -->

    <div class="space-y-6">
        <!-- Desktop Table -->
        <div class="hidden md:block">
            <x-card class="p-0 overflow-hidden" :embedded="true">
                <x-table>
                    <x-slot name="head">
                        <x-table.th class="w-3/12">Session Title</x-table.th>
                        <x-table.th class="w-2/12">Committee</x-table.th>
                        <x-table.th class="w-2/12">Date</x-table.th>
                        <x-table.th class="w-2/12">My Status</x-table.th>
                        <x-table.th class="w-3/12 text-right">Actions</x-table.th>
                    </x-slot>
                    @forelse ($sessions as $session)
                        @php
                            // The controller eager loads 'records' filtered by the current user
                            $myRecord = $session->records->first();
                        @endphp
                        <x-table.tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <x-table.td>
                                <span
                                    class="font-bold text-slate-800 dark:text-slate-200 block text-wrap leading-tight">{{ $session->title }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                    {{ $session->committee->name ?? 'General' }}
                                </span>
                            </x-table.td>
                            <x-table.td class="text-slate-500 text-xs font-medium whitespace-nowrap">
                                {{ $session->created_at->format('M d, Y') }}
                            </x-table.td>
                            <x-table.td>
                                @if ($myRecord)
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold
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
                            </x-table.td>
                            <x-table.td align="right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('sessions.member_details', $session) }}"
                                        class="p-2 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors"
                                        title="View Details & Feedback">
                                        <i class="bi bi-eye-fill"></i> View & Feedback
                                    </a>
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="5" class="text-center py-16">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-full mb-4">
                                        <i class="bi bi-clock-history text-4xl opacity-50"></i>
                                    </div>
                                    <p class="font-medium text-lg text-slate-600 dark:text-slate-300">No history found</p>
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-5">
            @forelse ($sessions as $session)
                @php
                    $myRecord = $session->records->first();
                @endphp
                <x-card class="relative">
                    <div class="pr-20 mb-4">
                        <span
                            class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 mb-2">
                            {{ $session->committee->name ?? 'General' }}
                        </span>
                        <h3 class="font-bold text-slate-800 dark:text-white text-lg leading-snug">{{ $session->title }}</h3>
                        <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1.5 font-medium">
                            <i class="bi bi-calendar-event"></i>
                            {{ $session->created_at->format('M d, Y') }}
                        </p>
                    </div>

                    <div
                        class="flex items-center justify-between gap-4 py-3 border-t border-slate-100 dark:border-slate-800 mb-4">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[10px] uppercase text-slate-400 font-bold tracking-wider">My Status</span>
                            @if ($myRecord)
                                <div
                                    class="font-bold {{ $myRecord->status === 'present' ? 'text-green-600' : ($myRecord->status === 'late' ? 'text-amber-600' : 'text-red-500') }}">
                                    {{ ucfirst($myRecord->status) }}
                                </div>
                            @else
                                <div class="text-slate-400 font-medium text-xs">Not Recorded</div>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-2.5">
                        <a href="{{ route('sessions.member_details', $session) }}"
                            class="flex items-center justify-center gap-2 px-3 py-2.5 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-xl font-bold text-sm hover:bg-blue-100 transition-colors">
                            <i class="bi bi-eye"></i> View & Feedback
                        </a>
                    </div>
                </x-card>
            @empty
                <x-card class="text-center py-12 text-slate-500">
                    <i class="bi bi-clock-history text-4xl mb-3 opacity-50 inline-block"></i>
                    <p>No history found.</p>
                </x-card>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($sessions->hasPages())
            <div class="mt-4">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
@endsection
