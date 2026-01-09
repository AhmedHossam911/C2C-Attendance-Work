@extends('layouts.app')

@section('content')
    <div x-data="{
        showModal: false,
        recordId: null,
        status: '',
        notes: '',
        openModal(id, currentStatus, currentNotes) {
            this.recordId = id;
            this.status = currentStatus;
            this.notes = currentNotes === null ? '' : currentNotes;
            this.showModal = true;
        }
    }">
        <div class="mb-6">
            <a href="{{ route('sessions.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-brand-blue transition-colors">
                <i class="bi bi-arrow-left"></i> Back to Sessions
            </a>
        </div>

        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $session->title }}</h2>
                <span
                    class="px-3 py-1 rounded-full text-xs font-bold {{ $session->status === 'open' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                    {{ ucfirst($session->status) }}
                </span>
            </div>
            <div>
                @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr']))
                    <form action="{{ route('sessions.toggle', $session) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full md:w-auto px-4 py-2 rounded-xl font-bold text-sm shadow-sm transition-all active:scale-95 flex items-center justify-center gap-2 {{ $session->status === 'open' ? 'bg-amber-100 text-amber-700 hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400' }}">
                            <i
                                class="bi bi-{{ $session->status === 'open' ? 'stop-circle-fill' : 'play-circle-fill' }}"></i>
                            {{ $session->status === 'open' ? 'Close Session' : 'Open Session' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Search Bar -->
        <div class="mb-6">
            <form method="GET" action="{{ route('sessions.show', $session) }}" class="flex gap-2">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-slate-400"></i>
                    </div>
                    <input type="text" name="search"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-brand-blue focus:ring-brand-blue dark:text-white shadow-sm placeholder:text-slate-400"
                        placeholder="Search by member name or email..." value="{{ request('search') }}">
                </div>
                <x-primary-button type="submit" class="px-6">
                    Search
                </x-primary-button>
                @if (request('search'))
                    <x-secondary-button href="{{ route('sessions.show', $session) }}" class="flex items-center">
                        <i class="bi bi-x-lg"></i>
                    </x-secondary-button>
                @endif
            </form>
        </div>

        <!-- Records List -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Attendance Records</h3>
                <span
                    class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700/50 text-xs font-bold text-slate-600 dark:text-slate-400">
                    {{ $records->total() }} Records
                </span>
            </div>

            <x-card class="p-0 overflow-hidden" :embedded="true">
                <!-- Desktop Table -->
                <div class="hidden md:block">
                    <x-table :headers="['#', 'Member', 'Scanned At', 'Status', 'Notes', 'Scanned By', 'Updated By', 'Actions']">
                        @foreach ($records as $record)
                            <x-table.tr>
                                <x-table.td
                                    class="text-slate-400 text-xs">{{ $records->firstItem() + $loop->index }}</x-table.td>
                                <x-table.td>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-teal-500 flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($record->user->name, 0, 1) }}
                                        </div>
                                        <span
                                            class="font-bold text-slate-800 dark:text-slate-200">{{ $record->user->name }}</span>
                                    </div>
                                </x-table.td>
                                <x-table.td>
                                    <div class="flex items-center gap-1.5 text-slate-500 font-medium">
                                        <i class="bi bi-clock"></i>
                                        {{ $record->scanned_at->format('h:i:s A') }}
                                    </div>
                                </x-table.td>
                                <x-table.td>
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-bold {{ $record->status === 'present' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </x-table.td>
                                <x-table.td class="text-slate-500 italic text-sm">
                                    {{ $record->notes ? Str::limit($record->notes, 30) : '-' }}
                                </x-table.td>
                                <x-table.td class="text-slate-500 text-xs">{{ $record->scanner->name }}</x-table.td>
                                <x-table.td class="text-slate-500 text-xs">
                                    @if ($record->updater)
                                        <div class="font-medium">{{ $record->updater->name }}</div>
                                        <div class="text-[10px] opacity-75">{{ $record->updated_at->diffForHumans() }}
                                        </div>
                                    @else
                                        -
                                    @endif
                                </x-table.td>

                                <x-table.td class="text-right">
                                    @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr']))
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button"
                                                class="p-1.5 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded hover:bg-blue-100 transition-colors"
                                                title="Edit"
                                                @click="openModal({{ $record->id }}, '{{ $record->status }}', {{ json_encode($record->notes) }})">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            @if (Auth::user()->hasRole('top_management'))
                                                <form action="{{ route('attendance.destroy', $record->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-1.5 bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 hover:bg-red-100 transition-colors rounded"
                                                        title="Delete">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </x-table.td>
                            </x-table.tr>
                        @endforeach
                    </x-table>
                </div>

                <!-- Mobile List Cards -->
                <div class="md:hidden space-y-4 p-4">
                    @forelse ($records as $record)
                        <div
                            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <!-- Helper Strip -->
                            <div
                                class="h-1 w-full {{ $record->status === 'present' ? 'bg-green-500' : ($record->status === 'late' ? 'bg-amber-500' : 'bg-slate-400') }}">
                            </div>

                            <div class="p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-teal-500 flex items-center justify-center text-white font-bold shrink-0 shadow-sm">
                                            {{ substr($record->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-800 dark:text-white">{{ $record->user->name }}
                                            </h4>
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $record->status === 'present' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($record->status === 'late' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400') }}">
                                                {{ ucfirst($record->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="space-y-2 text-sm text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl">
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide opacity-70">
                                            <i class="bi bi-clock"></i> Scanned
                                        </span>
                                        <span
                                            class="font-mono text-slate-700 dark:text-slate-300">{{ $record->scanned_at->format('h:i:s A') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide opacity-70">
                                            <i class="bi bi-qr-code"></i> By
                                        </span>
                                        <span
                                            class="text-slate-700 dark:text-slate-300">{{ $record->scanner->name }}</span>
                                    </div>
                                    @if ($record->updater)
                                        <div
                                            class="pt-2 mt-2 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                                            <span
                                                class="flex items-center gap-2 font-semibold uppercase tracking-wide opacity-70">
                                                <i class="bi bi-pencil"></i> Updated
                                            </span>
                                            <div class="text-right">
                                                <div class="text-slate-700 dark:text-slate-300">
                                                    {{ $record->updater->name }}</div>
                                                <div class="text-[10px] opacity-60">
                                                    {{ $record->updated_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if ($record->notes)
                                    <div class="mt-3 text-sm text-slate-600 dark:text-slate-400 italic">
                                        <i class="bi bi-chat-quote mr-1 opacity-50"></i> "{{ $record->notes }}"
                                    </div>
                                @endif

                                @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr']))
                                    <div
                                        class="flex items-center gap-2 mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                                        <button type="button"
                                            class="flex-1 py-2 rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 font-bold text-sm hover:bg-blue-100 transition-colors flex items-center justify-center gap-2"
                                            @click="openModal({{ $record->id }}, '{{ $record->status }}', {{ json_encode($record->notes) }})">
                                            <i class="bi bi-pencil-fill"></i> Edit
                                        </button>

                                        @if (Auth::user()->hasRole('top_management'))
                                            <form action="{{ route('attendance.destroy', $record->id) }}" method="POST"
                                                class="w-auto"
                                                onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-2 rounded-lg bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 hover:bg-red-100 transition-colors"
                                                    title="Delete">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            <p>No records found.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if ($records->hasPages())
                    @php $records->appends(request()->query()); @endphp
                    <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-800">
                        {{ $records->links('components.pagination') }}
                    </div>
                @endif
            </x-card>
        </div>

        @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr']))
            <!-- Edit Custom Modal (Alpine) -->
            <div x-show="showModal" style="display: none;" x-cloak class="relative z-50" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <!-- Backdrop -->
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" @click="showModal = false">
                </div>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <!-- Modal Panel -->
                        <div x-show="showModal" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 text-left shadow-2xl ring-1 ring-slate-900/5 transition-all sm:my-8 sm:w-full sm:max-w-md">

                            <!-- Close Button (Absolute) -->
                            <div class="absolute right-4 top-4 z-10">
                                <button type="button" @click="showModal = false"
                                    class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-500 dark:hover:bg-slate-800 dark:hover:text-slate-300 transition-colors">
                                    <i class="bi bi-x-lg text-lg"></i>
                                </button>
                            </div>

                            <form method="POST" :action="'{{ url('/attendance') }}/' + recordId">
                                @csrf
                                @method('PUT')

                                <div class="px-8 pt-8 pb-6">
                                    <!-- Header with Icon -->
                                    <div class="flex flex-col items-center mb-6">
                                        <div
                                            class="h-14 w-14 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-4 text-2xl shadow-sm border border-blue-100 dark:border-blue-900/30">
                                            <i class="bi bi-pencil-fill"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-slate-800 dark:text-white" id="modal-title">
                                            Edit Attendance
                                        </h3>
                                        <p
                                            class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-[200px] text-center leading-relaxed">
                                            Update the status and notes for this member's record.
                                        </p>
                                    </div>

                                    <!-- Form Content -->
                                    <div class="space-y-5">
                                        @if (in_array(Auth::user()->role, ['top_management', 'board']))
                                            <div>
                                                <label for="status"
                                                    class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 pl-1">
                                                    Attendance Status
                                                </label>
                                                <div class="relative">
                                                    <select
                                                        class="appearance-none w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 py-3 pl-4 pr-10 focus:border-brand-blue focus:ring-brand-blue dark:text-white cursor-pointer transition-shadow"
                                                        name="status" x-model="status" required>
                                                        <option value="present">Present (On Time)</option>
                                                        <option value="late">Late</option>
                                                    </select>
                                                    <div
                                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                                        <i class="bi bi-chevron-down text-xs"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div>
                                            <label for="notes"
                                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 pl-1">
                                                Remarks & Notes
                                            </label>
                                            <textarea
                                                class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-4 focus:border-brand-blue focus:ring-brand-blue dark:text-white transition-shadow resize-none"
                                                name="notes" rows="4" x-model="notes" placeholder="Add any relevant details or reasons..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer Actions -->
                                <div
                                    class="bg-slate-50 dark:bg-slate-800/30 px-8 py-5 flex items-center gap-3 border-t border-slate-100 dark:border-slate-800/50">
                                    <button type="button"
                                        class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm"
                                        @click="showModal = false">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="flex-1 px-4 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-brand-blue/90 shadow-lg shadow-brand-blue/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                                        <i class="bi bi-check-lg"></i>
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
