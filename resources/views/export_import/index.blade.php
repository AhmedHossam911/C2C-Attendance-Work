@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Import Users</div>
                <div class="card-body">
                    <form action="{{ route('import.users') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Excel File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="alert alert-info">
                            <strong>Instructions:</strong><br>
                            <ul>
                                <li>Use the template provided.</li>
                                <li><strong>committees:</strong> Enter committee names separated by commas (e.g., "IT, HR").
                                    Users will be assigned to these committees.</li>
                                <li><strong>role:</strong> top_management, board, hr, or member.</li>
                                <li><strong>status:</strong> active or pending.</li>
                            </ul>
                        </div>
                        <button type="submit" class="btn btn-success">Import Users</button>
                        <a href="{{ route('import.template') }}" class="btn btn-outline-secondary btn-sm float-end">Download
                            Template</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Export QRs & Data</div>
                <div class="card-body">
                    <form action="{{ route('export.qr') }}" method="POST">
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
                        <button type="submit" class="btn btn-primary">Download ZIP (Excel + Images)</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
