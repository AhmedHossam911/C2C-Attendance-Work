@extends('layouts.app')

@section('content')
    <h2>Member Search</h2>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('reports.member') }}" method="GET" class="d-flex gap-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by ID, Name, or Email"
                        value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
                <a href="{{ route('reports.export.members', ['search' => request('search')]) }}"
                    class="btn btn-success text-nowrap">
                    <i class="bi bi-file-earmark-excel"></i> Export Results
                </a>
            </form>
        </div>
    </div>

    @if (isset($members))
        @foreach ($members as $member)
            <div class="card mb-3">
                <div class="card-header" style="cursor: pointer;" data-bs-toggle="collapse"
                    data-bs-target="#member-{{ $member->id }}">
                    {{ $member->name }} ({{ $member->email }}) - Role: {{ $member->role }}
                    <i class="bi bi-chevron-down float-end"></i>
                </div>
                <div id="member-{{ $member->id }}" class="collapse card-body">
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
                                    <td>{{ $record->scanned_at->format('Y-m-d h:i A') }}</td>
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

        <div class="mt-3">
            {{ $members->links() }}
        </div>
    @endif
@endsection
