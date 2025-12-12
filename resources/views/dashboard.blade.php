@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Dashboard</h1>
            <p>Welcome, {{ Auth::user()->name }}!</p>

            <div class="row mt-4">
                <!-- User Role -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5>Your Role</h5>
                            <p class="badge bg-primary fs-5">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</p>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5>Your QR Code</h5>
                            <div class="visible-print text-center">
                                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate(Auth::id()) !!}
                            </div>
                            <p class="mt-2 text-muted">Show this to scan for attendance</p>
                        </div>
                    </div>
                </div>

                <!-- Attendance Stats -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-center">My Attendance Stats</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Total Attended
                                    <span class="badge bg-primary rounded-pill">{{ $attendanceStats['total'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Present
                                    <span class="badge bg-success rounded-pill">{{ $attendanceStats['present'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Late
                                    <span
                                        class="badge bg-warning text-dark rounded-pill">{{ $attendanceStats['late'] }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions (Top Management) -->
                @if (Auth::user()->hasRole('top_management'))
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Quick Actions</h5>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('users.pending') }}" class="btn btn-warning">Pending Approvals</a>
                                    <a href="{{ route('users.create') }}" class="btn btn-success">Create User</a>
                                    <a href="{{ route('qr.index') }}" class="btn btn-info">Send QR Emails</a>
                                    <a href="{{ route('export_import.index') }}" class="btn btn-secondary">Export/Import</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Upcoming Sessions -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">Open Sessions (Active Now)</div>
                        <div class="card-body">
                            @if ($upcomingSessions->count() > 0)
                                <ul class="list-group list-group-flush">
                                    @foreach ($upcomingSessions as $session)
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $session->title }}</strong><br>
                                                    <small
                                                        class="text-muted">{{ $session->committee->name ?? 'General' }}</small>
                                                </div>
                                                <span class="badge bg-success">Open</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted text-center my-3">No active sessions at the moment.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recent History -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">Recent Session History</div>
                        <div class="card-body">
                            @if ($recentSessions->count() > 0)
                                <ul class="list-group list-group-flush">
                                    @foreach ($recentSessions as $session)
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $session->title }}</strong><br>
                                                    <small
                                                        class="text-muted">{{ $session->created_at->format('M d, Y h:i A') }}</small>
                                                </div>
                                                <small
                                                    class="text-muted">{{ $session->committee->name ?? 'General' }}</small>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted text-center my-3">No recent history.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
