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

    <div class="card">
        <div class="card-header">Attendance Records ({{ $session->records->count() }})</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Scanned At</th>
                        <th>Status</th>
                        <th>Scanned By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($session->records as $record)
                        <tr>
                            <td>{{ $record->user->name }}</td>
                            <td>{{ $record->scanned_at->format('H:i:s') }}</td>
                            <td>
                                <span class="badge bg-{{ $record->status === 'present' ? 'success' : 'warning' }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                            <td>{{ $record->scanner->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
