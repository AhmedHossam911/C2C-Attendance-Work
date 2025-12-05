@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Committee Authorizations</h2>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Grant Authorization</div>
                <div class="card-body">
                    <form action="{{ route('authorizations.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">HR User</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Select HR User...</option>
                                @foreach ($hrUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Committee</label>
                            <select name="committee_id" class="form-select" required>
                                <option value="">Select Committee...</option>
                                @foreach ($committees as $committee)
                                    <option value="{{ $committee->id }}">{{ $committee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Grant Access</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Active Authorizations</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>HR User</th>
                                <th>Committee</th>
                                <th>Granted By</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($authorizations as $auth)
                                <tr>
                                    <td>{{ $auth->user->name }}</td>
                                    <td>{{ $auth->committee->name }}</td>
                                    <td>{{ $auth->granter->name }}</td>
                                    <td>{{ $auth->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <form action="{{ route('authorizations.destroy', $auth) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Revoke</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No active authorizations.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $authorizations->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
