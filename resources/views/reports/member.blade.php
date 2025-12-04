@extends('layouts.app')

@section('content')
    <h2>Member Search</h2>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('reports.member') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by ID, Name, or Email"
                        value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>

    @if (isset($members))
        @foreach ($members as $member)
            <div class="card mb-3">
                <div class="card-header">{{ $member->name }} ({{ $member->email }}) - Role: {{ $member->role }}</div>
                <div class="card-body">
                    <h5>Attendance History</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Session</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($member->attendanceRecords as $record)
                                <tr>
                                    <td>{{ $record->session->title }}</td>
                                    <td>{{ $record->scanned_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $record->status === 'present' ? 'success' : 'warning' }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif
@endsection
