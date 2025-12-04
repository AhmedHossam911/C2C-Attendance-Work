@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Attendance Sessions</h2>
        <a href="{{ route('sessions.create') }}" class="btn btn-primary">Create Session</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('sessions.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="committee_id" class="form-select">
                        <option value="">All Committees</option>
                        @foreach ($committees as $committee)
                            <option value="{{ $committee->id }}"
                                {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                                {{ $committee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Committee</th>
                        <th>Status</th>
                        <th>Late Threshold</th>
                        <th>Attendance</th>
                        <th>Created By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sessions as $session)
                        <tr>
                            <td>{{ $session->title }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $session->committee->name ?? 'General' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $session->status === 'open' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </td>
                            <td>{{ $session->late_threshold_minutes }} mins</td>
                            <td>{{ $session->records_count }}</td>
                            <td>{{ $session->creator->name }}</td>
                            <td>{{ $session->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('sessions.show', $session) }}" class="btn btn-sm btn-info m-2">View</a>
                                    <a href="{{ route('sessions.export', $session) }}"  class="btn  btn-sm btn-success m-2">Export</a>
                                    <form action="{{ route('sessions.toggle', $session) }}" method="POST"
                                        class="d-inline m-2">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm btn-{{ $session->status === 'open' ? 'warning' : 'success' }}">
                                            {{ $session->status === 'open' ? 'Close' : 'Open' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No sessions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $sessions->links() }}
            </div>
        </div>
    </div>
@endsection
