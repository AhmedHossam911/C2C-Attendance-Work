@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Attendance Session</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('sessions.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Committee</label>
                            <select name="committee_id" class="form-select" required>
                                @foreach ($committees as $committee)
                                    <option value="{{ $committee->id }}">{{ $committee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control"
                                placeholder="e.g., General Assembly Meeting" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Late Threshold (Minutes)</label>
                            <input type="number" name="late_threshold_minutes" class="form-control" value="15"
                                min="0" required>
                            <div class="form-text">Members scanning after this time will be marked as late.</div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="hidden" name="counts_for_attendance" value="0">
                            <input type="checkbox" class="form-check-input" name="counts_for_attendance" value="1"
                                id="countsCheck" checked>
                            <label class="form-check-label" for="countsCheck">Counts for Attendance</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Session</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
