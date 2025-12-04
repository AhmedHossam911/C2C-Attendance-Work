@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Dashboard</h1>
            <p>Welcome, {{ Auth::user()->name }}!</p>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5>Your Role</h5>
                            <p class="badge bg-primary fs-5">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5>Your QR Code</h5>
                            <!-- Using a simple QR code API for demonstration. In production, use a library like simple-qrcode -->
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ Auth::id() }}"
                                alt="QR Code">
                            <p class="mt-2 text-muted">Show this to scan for attendance</p>
                        </div>
                    </div>
                </div>

                @if (Auth::user()->hasRole('top_management'))
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Quick Actions</h5>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('users.pending') }}" class="btn btn-warning">Pending Approvals</a>
                                    <a href="{{ route('users.create') }}" class="btn btn-success">Create User</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
