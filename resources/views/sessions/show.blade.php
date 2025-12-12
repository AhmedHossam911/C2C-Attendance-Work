@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>{{ $session->title }}</h2>
            <span class="badge bg-{{ $session->status === 'open' ? 'success' : 'secondary' }}">
                {{ ucfirst($session->status) }}
            </span>
        </div>
        <div>
            <form action="{{ route('sessions.toggle', $session) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-{{ $session->status === 'open' ? 'warning' : 'success' }}">
                    {{ $session->status === 'open' ? 'Close Session' : 'Open Session' }}
                </button>
            </form>
        </div>
    </div>

    <form method="GET" action="{{ route('sessions.show', $session) }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by member name or email..."
                value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Search</button>
            @if (request('search'))
                <a href="{{ route('sessions.show', $session) }}" class="btn btn-secondary">Clear</a>
            @endif
        </div>
    </form>

    <div class="card">
        <div class="card-header">Attendance Records ({{ $session->records->count() }})</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Member</th>
                        <th>Scanned At</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Scanned By</th>
                        <th>Updated By</th>
                        @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr']))
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td>{{ $records->firstItem() + $loop->index }}</td>
                            <td>{{ $record->user->name }}</td>
                            <td>{{ $record->scanned_at->format('h:i:s A') }}</td>
                            <td>
                                <span class="badge bg-{{ $record->status === 'present' ? 'success' : 'warning' }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                            <td>{{ $record->notes }}</td>
                            <td>{{ $record->scanner->name }}</td>
                            <td>{{ $record->updater ? $record->updater->name : '-' }}</td>
                            @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr']))
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editAttendanceModal" data-record-id="{{ $record->id }}"
                                        data-record-status="{{ $record->status }}"
                                        data-record-notes="{{ $record->notes }}" onclick="openEditModal(this)">
                                        Edit
                                    </button>
                                    @if (Auth::user()->hasRole('top_management'))
                                        <form action="{{ route('attendance.destroy', $record->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                {{ $records->links() }}
            </div>
        </div>
    </div>

    @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr']))
        <!-- Edit Attendance Modal -->
        <div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Attendance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editAttendanceForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            @if (in_array(Auth::user()->role, ['top_management', 'board']))
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="present">Present</option>
                                        <option value="late">Late</option>
                                    </select>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
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
