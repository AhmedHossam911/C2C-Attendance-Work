@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2>{{ $committee->name }}</h2>
        <p class="lead">{{ $committee->description }}</p>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Members</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board'))
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($committee->users as $member)
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->email }}</td>
                                    @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board'))
                                        <td>
                                            <form action="{{ route('committees.remove', [$committee, $member]) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board'))
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Add Member</div>
                    <div class="card-body">
                        <form action="{{ route('committees.assign', $committee) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Select User</label>
                                <select name="user_id" class="form-select" required>
                                    <option value="">Choose...</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add to Committee</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
