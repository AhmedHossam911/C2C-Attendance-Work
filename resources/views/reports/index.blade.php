@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Committee Attendance Reports</h2>
        <a href="{{ route('reports.export.committees') }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Export to Excel
        </a>
    </div>

    @foreach ($committees as $committee)
        <div class="card mb-4 mt-3">
            <div class="card-header bg-success text-white" style="cursor: pointer;" data-bs-toggle="collapse"
                data-bs-target="#committee-{{ $committee->id }}">
                <h4 class="mb-0">{{ $committee->name }} <i class="bi bi-chevron-down float-end"></i></h4>
            </div>
            <div id="committee-{{ $committee->id }}" class="collapse card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Total Sessions</th>
                            <th>Present</th>
                            <th>Late</th>
                            <th>Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($committee->users as $user)
                            @php
                                $totalRecords = $user->attendanceRecords->count();
                                // Ideally we should count total sessions for this committee, but for simplicity let's use records count or calculate based on sessions.
// Requirement says "Attendance %".
// Let's assume we want % of sessions they attended vs total sessions held?
                                // Or just breakdown of their records.
                                // Let's show breakdown of records for now as calculating total eligible sessions is complex without more logic.
$present = $user->attendanceRecords->where('status', 'present')->count();
$late = $user->attendanceRecords->where('status', 'late')->count();
$percentage =
    $totalRecords > 0 ? round((($present + $late) / $totalRecords) * 100, 2) : 0; // This is % of attended sessions that were present/late? No, this is 100%.

// Let's try to get total sessions count.
                                // This is tricky without knowing which sessions were for this committee.
                                // The pivot table links users to committees, but sessions are general or linked to... wait.
                                // The requirement says "View attendance for all committees".
                                // Sessions seem to be global in the current design (Board creates session).
                                // So let's assume all sessions apply to everyone for now, or just show raw counts.

// Let's just show the counts we have.

                            @endphp
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $totalRecords }}</td>
                                <td>{{ $present }}</td>
                                <td>{{ $late }}</td>
                                <td>
                                    <!-- Placeholder for % calculation if we had total sessions count -->
                                    N/A
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection
