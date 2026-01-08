@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Scanner Card -->
        <x-card>
            <x-slot name="header">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">QR Scanner</h3>
            </x-slot>

            @if ($activeSessions->isEmpty())
                <div
                    class="p-4 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-800/30 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>No active sessions found. Please open a session first.</span>
                </div>
            @else
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Select Session</label>
                    <select id="sessionSelect"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white text-sm">
                        @foreach ($activeSessions as $session)
                            <option value="{{ $session->id }}">{{ $session->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="rounded-2xl overflow-hidden border-2 border-slate-100 dark:border-slate-700 relative bg-black">
                    <div id="reader" style="width: 100%;"></div>
                </div>

                <div id="result" class="mt-4 empty:hidden"></div>
            @endif
        </x-card>

        <!-- Recent Scans List -->
        <x-card class="p-0" :embedded="true">
            <x-slot name="header">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Recent Scans (This Session)</h3>
            </x-slot>

            <ul class="divide-y divide-slate-100 dark:divide-slate-800" id="recentScansList">
                <li class="px-6 py-8 text-center text-slate-500 dark:text-slate-400 italic" id="noScansPlaceholder">
                    No scans yet using this device.
                </li>
            </ul>
        </x-card>
    </div>

    @if (!$activeSessions->isEmpty())
        <script>
            const html5QrCode = new Html5Qrcode("reader");
            let isScanning = true;

            // Audio Feedback
            const successAudio = new Audio("{{ asset('sounds/success.mp3') }}");
            const errorAudio = new Audio("{{ asset('sounds/error.mp3') }}");

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
                            // Play Success Sound
                            successAudio.play().catch(e => console.log('Audio play failed:', e));

                            // Success Alert (Tailwind)
                            resultDiv.innerHTML = `<div class="p-4 rounded-xl bg-green-50 text-green-800 border border-green-200 dark:bg-green-900/20 dark:text-green-300 dark:border-green-800/30 flex items-center gap-3 animate-fade-in-down">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <strong class="font-bold">Success!</strong> 
                                    <span>${result.body.user} marked as ${result.body.status}.</span>
                                </div>
                            </div>`;

                            // Add to recent list
                            if (placeholder) placeholder.remove();
                            const li = document.createElement('li');
                            li.className =
                                'px-6 py-4 flex justify-between items-center hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors animate-fade-in';

                            const statusColor = result.body.status === 'present' ?
                                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';

                            li.innerHTML = `
                                <span class="font-medium text-slate-700 dark:text-slate-200">${result.body.user}</span>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold ${statusColor}">
                                    ${result.body.status.charAt(0).toUpperCase() + result.body.status.slice(1)}
                                </span>
                            `;
                            recentList.prepend(li);

                        } else {
                            // Play Error Sound
                            errorAudio.play().catch(e => console.log('Audio play failed:', e));

                            // Error Alert (Tailwind)
                            resultDiv.innerHTML = `<div class="p-4 rounded-xl bg-red-50 text-red-800 border border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-800/30 flex items-center gap-3 animate-fade-in-down">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <strong class="font-bold">Error:</strong> 
                                    <span>${result.body.message}</span>
                                </div>
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
                        // Play Error Sound
                        errorAudio.play().catch(e => console.log('Audio play failed:', e));

                        document.getElementById('result').innerHTML =
                            `<div class="p-4 rounded-xl bg-red-50 text-red-800 border border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-800/30">System Error</div>`;
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
