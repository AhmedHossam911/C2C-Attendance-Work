@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Send QR Codes to Members</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('qr.send') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Select Committee</label>
                            <select name="committee_id" class="form-select" required>
                                @foreach ($committees as $committee)
                                    <option value="{{ $committee->id }}">{{ $committee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Session</label>
                            <select name="session_id" class="form-select" required>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-info">
                            Emails will be sent to all members of the selected committee.
                            The sender will be recorded as <strong>{{ Auth::user()->email }}</strong>.
                        </div>
                        <button type="submit" class="btn btn-primary">Send QRs</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
