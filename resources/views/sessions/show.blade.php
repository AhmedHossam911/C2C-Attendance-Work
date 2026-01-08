@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $session->title }}</h2>
            <span
                class="px-2 py-1 rounded-full text-xs font-bold {{ $session->status === 'open' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                {{ ucfirst($session->status) }}
            </span>
        </div>
        <div>
            <form action="{{ route('sessions.toggle', $session) }}" method="POST">
                @csrf
                <button type="submit"
                    class="px-4 py-2 rounded-xl font-bold text-sm shadow-sm transition-all active:scale-95 {{ $session->status === 'open' ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                    {{ $session->status === 'open' ? 'Close Session' : 'Open Session' }}
                </button>
            </form>
        </div>
    </div>

    <div class="mb-6">
        <form method="GET" action="{{ route('sessions.show', $session) }}" class="flex gap-2">
            <div class="relative flex-1">
                <input type="text" name="search"
                    class="w-full pl-4 pr-10 py-2.5 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-brand-blue focus:ring-brand-blue dark:text-white shadow-sm"
                    placeholder="Search by member name or email..." value="{{ request('search') }}">
            </div>
            <button
                class="px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white font-semibold rounded-xl shadow-lg shadow-brand-blue/20 transition-all"
                type="submit">
                Search
            </button>
            @if (request('search'))
                <a href="{{ route('sessions.show', $session) }}"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold rounded-xl transition-all flex items-center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <x-card class="p-0" :embedded="true">
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Attendance Records</h3>
                <span
                    class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700/50 text-xs font-bold text-slate-600 dark:text-slate-400">
                    {{ $session->records->count() }} Total
                </span>
            </div>
        </x-slot>

        <x-table :headers="[
            'No',
            'Member',
            'Scanned At',
            'Status',
            'Notes',
            'Scanned By',
            'Updated By',
            in_array(Auth::user()->role, ['top_management', 'board', 'hr']) ? 'Actions' : '',
        ]">
            @foreach ($records as $record)
                <x-table.tr>
                    <x-table.td>{{ $records->firstItem() + $loop->index }}</x-table.td>
                    <x-table.td>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $record->user->name }}</span>
                    </x-table.td>
                    <x-table.td class="text-slate-500">{{ $record->scanned_at->format('h:i:s A') }}</x-table.td>
                    <x-table.td>
                        <span
                            class="px-2 py-1 rounded-full text-xs font-bold {{ $record->status === 'present' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                            {{ ucfirst($record->status) }}
                        </span>
                    </x-table.td>
                    <x-table.td class="text-slate-500 italic">{{ Str::limit($record->notes, 20) }}</x-table.td>
                    <x-table.td class="text-slate-500 text-xs">{{ $record->scanner->name }}</x-table.td>
                    <x-table.td
                        class="text-slate-500 text-xs">{{ $record->updater ? $record->updater->name : '-' }}</x-table.td>

                    @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr']))
                        <x-table.td>
                            <div class="flex items-center gap-2">
                                <button type="button"
                                    class="px-2 py-1 bg-brand-blue/10 text-brand-blue hover:bg-brand-blue/20 rounded text-xs font-bold transition-colors"
                                    data-bs-toggle="modal" data-bs-target="#editAttendanceModal"
                                    data-record-id="{{ $record->id }}" data-record-status="{{ $record->status }}"
                                    data-record-notes="{{ $record->notes }}" onclick="openEditModal(this)">
                                    Edit
                                </button>
                                @if (Auth::user()->hasRole('top_management'))
                                    <form action="{{ route('attendance.destroy', $record->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 rounded text-xs font-bold transition-colors">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </x-table.td>
                    @endif
                </x-table.tr>
            @endforeach
        </x-table>

        @if ($records->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $records->links() }}
            </div>
        @endif
    </x-card>

    @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr']))
        <!-- Edit Attendance Modal (Bootstrap Wrapper with some Tailwind interiors if possible, avoiding full rewrite avoiding JS breakage) -->
        <div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-[1.5rem] border-0 shadow-2xl overflow-hidden">
                    <div class="modal-header bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700">
                        <h5 class="modal-title font-bold text-slate-800 dark:text-white">Edit Attendance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editAttendanceForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-6 bg-white dark:bg-[#1e293b]">
                            @if (in_array(Auth::user()->role, ['top_management', 'board']))
                                <div class="mb-4">
                                    <label for="status"
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
                                    <select
                                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                                        id="status" name="status" required>
                                        <option value="present">Present</option>
                                        <option value="late">Late</option>
                                    </select>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="notes"
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Notes</label>
                                <textarea
                                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                                    id="notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div
                            class="modal-footer border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30 p-4">
                            <button type="button"
                                class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-medium transition-colors"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit"
                                class="px-4 py-2 bg-brand-blue text-white rounded-xl hover:bg-brand-blue/90 font-bold transition-colors shadow-lg shadow-brand-blue/20">Save
                                changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr']))
        <script>
            function openEditModal(button) {
                const recordId = button.getAttribute('data-record-id');
                const status = button.getAttribute('data-record-status');
                const notes = button.getAttribute('data-record-notes');

                const form = document.getElementById('editAttendanceForm');
                form.action = `/attendance/${recordId}`;

                const statusSelect = document.getElementById('status');
                if (statusSelect) {
                    statusSelect.value = status;
                }

                const notesInput = document.getElementById('notes');
                notesInput.value = notes ? notes : '';
            }
        </script>
    @endif
