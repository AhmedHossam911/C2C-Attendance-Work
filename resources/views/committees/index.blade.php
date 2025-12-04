@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Committees</h2>
        @if (Auth::user()->hasRole('top_management'))
            <a href="{{ route('committees.create') }}" class="btn btn-primary">Create Committee</a>
        @endif
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('committees.index') }}" method="GET" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Search Committees..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse ($committees as $committee)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $committee->name }}</h5>
                        <p class="card-text">{{ Str::limit($committee->description, 100) }}</p>
                        <p class="text-muted">{{ $committee->users->count() }} Members</p>
                        <a href="{{ route('committees.show', $committee) }}" class="btn btn-outline-primary">View
                            Details</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No committees found.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $committees->links() }}
    </div>
@endsection
