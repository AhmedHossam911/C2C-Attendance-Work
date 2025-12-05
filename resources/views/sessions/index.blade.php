@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Attendance Sessions</h2>
        <a href="{{ route('sessions.create') }}" class="btn btn-primary">Create Session</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('sessions.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
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
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" placeholder="From Date"
                        value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" placeholder="To Date"
                        value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                    <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Desktop Table -->
            <div class="table-responsive d-none d-md-block">
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
                                        <a href="{{ route('sessions.show', $session) }}"
                                            class="btn btn-sm btn-info m-2">View</a>
                                        <a href="{{ route('sessions.export', $session) }}"
                                            class="btn  btn-sm btn-success m-2">Export</a>
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
            </div>

            <!-- Mobile Cards -->
            <div class="d-md-none">
                @forelse ($sessions as $session)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $session->title }}</h5>
                                <span class="badge bg-{{ $session->status === 'open' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </div>
                            <p class="card-text small text-muted mb-2">
                                {{ $session->created_at->format('Y-m-d H:i') }} | Created by {{ $session->creator->name }}
                            </p>
                            <div class="mb-2">
                                <span class="badge bg-secondary">{{ $session->committee->name ?? 'General' }}</span>
                                <span class="badge bg-info text-dark">Threshold:
                                    {{ $session->late_threshold_minutes }}m</span>
                                <span class="badge bg-primary">Count: {{ $session->records_count }}</span>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('sessions.show', $session) }}" class="btn btn-info btn-sm">View
                                    Details</a>
                                <a href="{{ route('sessions.export', $session) }}"
                                    class="btn btn-success btn-sm">Export</a>
                                <form action="{{ route('sessions.toggle', $session) }}" method="POST" class="d-grid">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-{{ $session->status === 'open' ? 'warning' : 'success' }} btn-sm">
                                        {{ $session->status === 'open' ? 'Close Session' : 'Open Session' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-3">No sessions found.</div>
                @endforelse
            </div>

            <div class="mt-3">
                {{ $sessions->links() }}
            </div>
        </div>
    </div>
@endsection
