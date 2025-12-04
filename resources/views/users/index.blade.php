@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Users Management</h2>
        <a href="{{ route('users.create') }}" class="btn btn-primary">Create User</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by Name or Email"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="board" {{ request('role') == 'board' ? 'selected' : '' }}>Board</option>
                        <option value="hr" {{ request('role') == 'hr' ? 'selected' : '' }}>HR</option>
                        <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Member</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $user->role)) }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-info">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
