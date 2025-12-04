@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Attendance Sessions</h2>
        <a href="{{ route('sessions.create') }}" class="btn btn-primary">Create Session</a>
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
                    @foreach ($sessions as $session)
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
                                <a href="{{ route('sessions.show', $session) }}" class="btn btn-sm btn-info">View</a>
                                <form action="{{ route('sessions.toggle', $session) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm btn-{{ $session->status === 'open' ? 'warning' : 'success' }}">
                                        {{ $session->status === 'open' ? 'Close' : 'Open' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
