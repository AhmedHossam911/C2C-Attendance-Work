@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit User: {{ $user->name }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password (Leave blank to keep current)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="member" {{ $user->role == 'member' ? 'selected' : '' }}>Member</option>
                                <option value="hr" {{ $user->role == 'hr' ? 'selected' : '' }}>HR</option>
                                <option value="board" {{ $user->role == 'board' ? 'selected' : '' }}>Board</option>
                                <option value="top_management" {{ $user->role == 'top_management' ? 'selected' : '' }}>Top
                                    Management</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ $user->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="disabled" {{ $user->status == 'disabled' ? 'selected' : '' }}>Disabled
                                </option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
