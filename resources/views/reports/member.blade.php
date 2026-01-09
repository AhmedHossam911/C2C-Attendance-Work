@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Member Search</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">View detailed member profiles and history</p>
            </div>

            <!-- Search Form -->
            <div class="flex-1 max-w-lg">
                <form action="{{ route('reports.member') }}" method="GET" class="relative group">
                    <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-blue transition-colors">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" name="search"
                        class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-all shadow-sm"
                        placeholder="Search by Name, ID, or Email..." value="{{ request('search') }}">
                    @if (request('search'))
                        <a href="{{ route('reports.member') }}"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i class="bi bi-x-circle-fill"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        @if (!request('search'))
            <x-card
                class="text-center py-20 border-dashed border-2 border-slate-200 dark:border-slate-700 bg-transparent shadow-none">
                <div
                    class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-search text-2xl text-slate-400"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">Ready to Search</h3>
                <p class="text-slate-500 max-w-sm mx-auto mt-2 text-sm">Enter a member's Name, ID, or Email above to view
                    their full profile.</p>
            </x-card>
        @elseif ($members->isEmpty())
            <x-card class="text-center py-20">
                <div
                    class="w-16 h-16 bg-red-50 dark:bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-person-x text-2xl text-red-500"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">No Member Found</h3>
                <p class="text-slate-500 mt-2 text-sm">We couldn't find any member matching "<span
                        class="font-bold">{{ request('search') }}</span>".</p>
            </x-card>
        @else
            <!-- Results -->
            <div class="space-y-8">
                @foreach ($members as $member)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        <!-- Col 1: Profile Summary -->
                        <div class="space-y-6">
                            <x-card class="relative overflow-hidden">
                                <div
                                    class="absolute top-0 left-0 w-full h-24 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900">
                                </div>

                                <div class="relative pt-12 px-4 text-center">
                                    <!-- Avatar -->
                                    <div class="relative inline-block">
                                        <div class="w-24 h-24 rounded-2xl bg-white dark:bg-slate-800 p-1 shadow-lg mx-auto">
                                            <div
                                                class="w-full h-full rounded-xl bg-gradient-to-br from-brand-blue to-purple-600 flex items-center justify-center text-3xl font-bold text-white uppercase">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="absolute -bottom-2 -right-2">
                                            @php
                                                $statusClass = match ($member->status) {
                                                    'active' => 'bg-green-500 border-green-100 dark:border-slate-800',
                                                    'pending' => 'bg-amber-500 border-amber-100 dark:border-slate-800',
                                                    'disabled' => 'bg-red-500 border-red-100 dark:border-slate-800',
                                                    default => 'bg-slate-400',
                                                };
                                            @endphp
                                            <span
                                                class="w-5 h-5 rounded-full border-4 border-white dark:border-slate-800 {{ $statusClass }} block"
                                                title="{{ ucfirst($member->status) }}"></span>
                                        </div>
                                    </div>

                                    <!-- Name & Role -->
                                    <div class="mt-4">
                                        <h2 class="text-xl font-bold text-slate-800 dark:text-white">{{ $member->name }}
                                        </h2>
                                        <div class="flex items-center justify-center gap-2 mt-2">
                                            <span
                                                class="px-2.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold uppercase tracking-wider">
                                                {{ str_replace('_', ' ', $member->role) }}
                                            </span>
                                            <span class="text-xs text-slate-400 font-mono">#{{ $member->id }}</span>
                                        </div>
                                    </div>

                                    <!-- Contact -->
                                    <div class="mt-6 py-6 border-t border-slate-100 dark:border-slate-800 space-y-3">
                                        <div
                                            class="flex items-center justify-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                                            <i class="bi bi-envelope text-slate-400"></i>
                                            {{ $member->email }}
                                        </div>
                                        <div
                                            class="flex items-center justify-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                                            <i class="bi bi-calendar3 text-slate-400"></i>
                                            Joined {{ $member->created_at->format('M Y') }}
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div
                                        class="pt-6 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-3">
                                        <a href="{{ route('reports.export.members', ['search' => $member->id]) }}"
                                            class="btn-secondary text-xs py-2 justify-center">
                                            <i class="bi bi-download mr-1"></i> Export
                                        </a>
                                        <button
                                            onclick="document.getElementById('qr-modal-{{ $member->id }}').showModal()"
                                            class="btn-primary text-xs py-2 justify-center">
                                            <i class="bi bi-qr-code mr-1"></i> QR Code
                                        </button>
                                    </div>
                                </div>
                            </x-card>

                            <!-- QR Modal -->
                            <dialog id="qr-modal-{{ $member->id }}"
                                class="modal bg-transparent p-0 backdrop:bg-slate-900/50">
                                <form method="dialog"
                                    class="modal-box bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 p-0 overflow-hidden max-w-sm w-full mx-auto relative">
                                    <button
                                        class="absolute top-3 right-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <div class="p-8 flex flex-col items-center justify-center text-center">
                                        <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-6">Member QR Code
                                        </h3>
                                        <div class="bg-white p-4 rounded-xl shadow-inner border border-slate-100">
                                            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->margin(1)->color(30, 41, 59)->generate($member->id) !!}
                                        </div>
                                        <p class="text-slate-500 text-sm mt-4 font-mono select-all">{{ $member->id }}</p>
                                        <p class="text-xs text-slate-400 mt-1">Scan to mark attendance</p>
                                    </div>
                                </form>
                            </dialog>

                            <!-- Committees -->
                            <x-card>
                                <h3 class="font-bold text-slate-800 dark:text-white text-sm mb-4">Committees</h3>
                                @if ($member->committees->isNotEmpty())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($member->committees as $c)
                                            <span
                                                class="px-3 py-1 bg-slate-50 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300 rounded-lg text-xs font-medium border border-slate-100 dark:border-slate-700">
                                                {{ $c->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-slate-400 text-xs italic">No committees assigned.</p>
                                @endif
                            </x-card>
                        </div>

                        <!-- Col 2 & 3: Stats & History -->
                        <div class="lg:col-span-2 space-y-6">

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <x-card class="bg-gradient-to-br from-brand-blue to-blue-600 text-white border-0">
                                    <div class="text-blue-100 text-xs font-bold uppercase tracking-wider mb-1">Total
                                        Attendance</div>
                                    <div class="text-3xl font-bold">{{ $member->attendanceRecords->count() }}</div>
                                    <div class="text-xs text-blue-200 mt-1">Sessions logged</div>
                                </x-card>

                                <x-card class="bg-slate-50 dark:bg-slate-800/50">
                                    <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">Present
                                        Count</div>
                                    <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $member->attendanceRecords->where('status', 'present')->count() }}
                                    </div>
                                    <div class="text-xs text-slate-400 mt-1">On-time arrivals</div>
                                </x-card>

                                <x-card class="bg-slate-50 dark:bg-slate-800/50">
                                    <div class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">Late Count
                                    </div>
                                    <div class="text-3xl font-bold text-amber-500">
                                        {{ $member->attendanceRecords->where('status', 'late')->count() }}
                                    </div>
                                    <div class="text-xs text-slate-400 mt-1">Sessions late</div>
                                </x-card>
                            </div>

                            <!-- Attendance Timeline -->
                            <x-card>
                                <h3 class="font-bold text-slate-800 dark:text-white mb-6 flex items-center justify-between">
                                    <span>Attendance History</span>
                                </h3>

                                @if ($member->attendanceRecords->isEmpty())
                                    <div class="text-center py-12">
                                        <i class="bi bi-calendar-x text-slate-300 text-3xl mb-2 block"></i>
                                        <p class="text-slate-500 dark:text-slate-400">No attendance records found.</p>
                                    </div>
                                @else
                                    <div class="relative pl-4 border-l-2 border-slate-100 dark:border-slate-700 space-y-8">
                                        @foreach ($member->attendanceRecords->take(10) as $record)
                                            <div class="relative">
                                                <!-- Dot -->
                                                <div
                                                    class="absolute -left-[21px] top-1.5 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-slate-800 {{ $record->status === 'present' ? 'bg-emerald-500' : ($record->status === 'late' ? 'bg-amber-500' : 'bg-slate-300') }}">
                                                </div>

                                                <div
                                                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                    <div>
                                                        <h4 class="font-bold text-slate-800 dark:text-white text-sm">
                                                            {{ $record->session->title ?? 'Session' }}
                                                        </h4>
                                                        <div
                                                            class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-0.5">
                                                            <span><i class="bi bi-calendar2"></i>
                                                                {{ $record->created_at->format('M d, Y') }}</span>
                                                            <span><i class="bi bi-clock"></i>
                                                                {{ $record->created_at->format('h:i A') }}</span>
                                                        </div>
                                                    </div>

                                                    <span
                                                        class="self-start sm:self-auto px-2.5 py-1 rounded-md text-xs font-bold uppercase {{ $record->status === 'present' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400' : ($record->status === 'late' ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400' : 'bg-slate-100 text-slate-500') }}">
                                                        {{ $record->status }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if ($member->attendanceRecords->count() > 10)
                                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
                                            <span class="text-xs text-slate-500">Showing recent 10 records only. Export for
                                                full history.</span>
                                        </div>
                                    @endif
                                @endif
                            </x-card>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($members->hasPages())
                <div class="mt-8">
                    {{ $members->links('components.pagination') }}
                </div>
            @endif
        @endif
    </div>

    <style>
        .btn-primary {
            @apply flex items-center px-4 py-2 bg-brand-blue text-white font-bold rounded-lg hover:bg-blue-600 hover:shadow-lg hover:-translate-y-0.5 transition-all;
        }

        .btn-secondary {
            @apply flex items-center px-4 py-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all;
        }
    </style>
@endsection
