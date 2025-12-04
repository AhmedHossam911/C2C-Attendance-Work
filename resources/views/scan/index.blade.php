@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">QR Scanner</div>
                <div class="card-body">
                    @if ($activeSessions->isEmpty())
                        <div class="alert alert-warning">No active sessions found. Please open a session first.</div>
                    @else
                        <div class="mb-3">
                            <label class="form-label">Select Session</label>
                            <select id="sessionSelect" class="form-select">
                                @foreach ($activeSessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="reader" width="600px"></div>

                        <div id="result" class="mt-3"></div>
                    @endif
                </div>
            </div>

            <!-- Recent Scans List -->
            <div class="card">
                <div class="card-header">Recent Scans (This Session)</div>
                <ul class="list-group list-group-flush" id="recentScansList">
                    <!-- Scans will be appended here -->
                    <li class="list-group-item text-muted text-center" id="noScansPlaceholder">No scans yet.</li>
                </ul>
            </div>
        </div>
    </div>

    @if (!$activeSessions->isEmpty())
        <script>
            const html5QrCode = new Html5Qrcode("reader");
            let isScanning = true;

            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                if (!isScanning) return;

                // Prevent rapid double scans
                isScanning = false;
                html5QrCode.pause();

                const sessionId = document.getElementById('sessionSelect').value;

                // Parse QR code: "ID,Name,Committee" or just "ID"
                let userId = decodedText;
                if (decodedText.includes(',')) {
                    userId = decodedText.split(',')[0].trim();
                }

                fetch(`/session/${sessionId}/scan`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            user_id: userId
                        })
                    })
                    .then(response => response.json().then(data => ({
                        status: response.status,
                        body: data
                    })))
                    .then(result => {
                        const resultDiv = document.getElementById('result');
                        const recentList = document.getElementById('recentScansList');
                        const placeholder = document.getElementById('noScansPlaceholder');

                        if (result.status === 200) {
                            resultDiv.innerHTML = `<div class="alert alert-success">
                    <strong>Success!</strong> ${result.body.user} marked as ${result.body.status}.
                </div>`;

                            // Add to recent list
                            if (placeholder) placeholder.remove();
                            const li = document.createElement('li');
                            li.className = 'list-group-item d-flex justify-content-between align-items-center';
                            li.innerHTML = `
                                <span>${result.body.user}</span>
                                <span class="badge bg-${result.body.status === 'present' ? 'success' : 'warning'}">${result.body.status}</span>
                            `;
                            recentList.prepend(li);

                        } else {
                            resultDiv.innerHTML = `<div class="alert alert-danger">
                    <strong>Error:</strong> ${result.body.message}
                </div>`;
                        }

                        // Resume scanning after 2 seconds
                        setTimeout(() => {
                            resultDiv.innerHTML = '';
                            html5QrCode.resume();
                            isScanning = true;
                        }, 2000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('result').innerHTML =
                            `<div class="alert alert-danger">System Error</div>`;
                        setTimeout(() => {
                            html5QrCode.resume();
                            isScanning = true;
                        }, 2000);
                    });
            };

            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            };

            // Start scanning
            html5QrCode.start({
                facingMode: "environment"
            }, config, qrCodeSuccessCallback);
        </script>
    @endif
@endsection
