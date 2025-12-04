@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2>{{ $committee->name }}</h2>
        <p class="lead">{{ $committee->description }}</p>
        <span class="badge bg-secondary">Total Members: {{ $committee->users->count() }}</span>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Members</span>
                    <form action="{{ route('committees.show', $committee) }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2"
                            placeholder="Search Members..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-primary">Search</button>
                    </form>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board'))
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($members as $member)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
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
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No members found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $members->links() }}
                    </div>
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
                                <label class="form-label">Search User</label>
                                <input type="text" id="userSearch" class="form-control mb-2"
                                    placeholder="Type to filter...">
                                <label class="form-label">Select User</label>
                                <select name="user_id" id="userSelect" class="form-select" required size="5">
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

            <script>
                document.getElementById('userSearch').addEventListener('keyup', function() {
                    var searchText = this.value.toLowerCase();
                    var select = document.getElementById('userSelect');
                    var options = select.getElementsByTagName('option');

                    for (var i = 0; i < options.length; i++) {
                        var option = options[i];
                        var text = option.text.toLowerCase();
                        if (text.indexOf(searchText) > -1 || option.value === "") {
                            option.style.display = "";
                        } else {
                            option.style.display = "none";
                        }
                    }
                });
            </script>
        @endif
    </div>
@endsection
