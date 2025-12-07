@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2>Send QR Codes</h2>

            <!-- Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('qr.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Filter by Committee</label>
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
                        <div class="col-md-4">
                            <label class="form-label">Search (Name/Email)</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                                placeholder="Search members...">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bulk Actions (Optional, restored in a cleaner way if needed, but focusing on list for now) -->
            <div class="alert alert-info">
                <strong>Note:</strong> Clicking "Open Gmail" will open a draft with the member's details. You may need to
                manually attach the QR code image if the link is not sufficient.
            </div>

            <!-- Desktop View: Table -->
            <div class="card d-none d-md-block">
                <div class="card-header">Members List ({{ $users->total() }})</div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>QR</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Committees</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                @php
                                    $qrUrl = URL::signedRoute('qr.view', ['user' => $user->id]);
                                    $subject = 'Membership QR - ' . ($user->committees->first()->name ?? 'General');
                                    $body =
                                        'Hello ' .
                                        $user->name .
                                        ",\n\nHere is your membership QR code link:\n" .
                                        $qrUrl .
                                        "\n\nPlease click the link to view your QR code page.\n\nPlease keep it safe.\n\nBest regards,";
                                    $gmailUrl =
                                        'https://mail.google.com/mail/?view=cm&fs=1&to=' .
                                        $user->email .
                                        '&su=' .
                                        urlencode($subject) .
                                        '&body=' .
                                        urlencode($body);
                                @endphp
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->generate($user->id) !!}
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach ($user->committees as $comm)
                                            <span class="badge bg-secondary">{{ $comm->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ $gmailUrl }}" target="_blank"
                                            class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-envelope"></i> Open Gmail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $users->links() }}
                </div>
            </div>

            <!-- Mobile View: Cards -->
            <div class="d-md-none">
                <h5 class="mb-3">Members List ({{ $users->total() }})</h5>
                @forelse($users as $user)
                    @php
                        $qrUrl = URL::signedRoute('qr.view', ['user' => $user->id]);
                        $subject = 'Membership QR - ' . ($user->committees->first()->name ?? 'General');
                        $body =
                            'Hello ' .
                            $user->name .
                            ",\n\nHere is your membership QR code link:\n" .
                            $qrUrl .
                            "\n\nPlease click the link to view your QR code page.\n\nPlease keep it safe.\n\nBest regards,";
                        $gmailUrl =
                            'https://mail.google.com/mail/?view=cm&fs=1&to=' .
                            $user->email .
                            '&su=' .
                            urlencode($subject) .
                            '&body=' .
                            urlencode($body);
                    @endphp
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title fw-bold">{{ $user->name }}</h5>
                                    <p class="card-text text-muted mb-1">#{{ $user->id }}</p>
                                    <p class="card-text text-muted small">{{ $user->email }}</p>
                                </div>
                                <div>
                                    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($user->id) !!}
                                </div>
                            </div>

                            <div class="mb-3">
                                @foreach ($user->committees as $comm)
                                    <span class="badge bg-secondary">{{ $comm->name }}</span>
                                @endforeach
                            </div>

                            <div class="d-grid">
                                <a href="{{ $gmailUrl }}" target="_blank" class="btn btn-danger w-100">
                                    <i class="bi bi-envelope"></i> Open Gmail
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">No users found.</div>
                @endforelse

                {{ $users->links() }}
            </div>

        </div>
    </div>
@endsection
