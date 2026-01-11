<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6" x-data="{ selectedSession: {{ $activeSessions->first()?->id ?? 'null' }} }">
        <!-- Header -->
        <div class="text-center mb-2">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">QR Scanner</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Scan member QR codes to record attendance</p>
        </div>

        @if ($activeSessions->isEmpty())
            <x-empty-state icon="exclamation-triangle" title="No Active Sessions"
                description="Please open a session first before scanning.">
                <x-slot name="action">
                    <a href="{{ route('sessions.index') }}"
                        class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-slate-50 font-bold text-sm rounded-xl hover:bg-blue-700 transition-all">
                        <i class="bi bi-calendar-event mr-2"></i> View Sessions
                    </a>
                </x-slot>
            </x-empty-state>
        @else
            <!-- Session Selection Cards -->
            <div>
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-400 mb-3 flex items-center gap-2">
                    <i class="bi bi-calendar-check"></i> Select Session to Scan
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($activeSessions as $session)
                        <button type="button"
                            @click="selectedSession = {{ $session->id }}; document.getElementById('sessionSelect').value = {{ $session->id }}"
                            :class="selectedSession === {{ $session->id }} ?
                                'bg-blue-600 border-blue-600 text-slate-50 shadow-lg shadow-blue-600/25' :
                                'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 hover:border-blue-400 dark:hover:border-blue-500'"
                            class="p-4 rounded-xl border-2 text-left transition-all duration-200 group">
                            <div class="flex items-start gap-3">
                                <div :class="selectedSession === {{ $session->id }} ?
                                    'bg-slate-50/20' :
                                    'bg-blue-100 dark:bg-blue-900/30'"
                                    class="p-2 rounded-lg shrink-0 transition-colors">
                                    <i class="bi bi-broadcast"
                                        :class="selectedSession === {{ $session->id }} ? 'text-slate-50' :
                                            'text-blue-600 dark:text-blue-400'"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold truncate"
                                        :class="selectedSession === {{ $session->id }} ? 'text-slate-50' :
                                            'text-slate-800 dark:text-slate-100'">
                                        {{ $session->title }}
                                    </h4>
                                    <p class="text-xs mt-1 truncate"
                                        :class="selectedSession === {{ $session->id }} ? 'text-slate-200' :
                                            'text-slate-500 dark:text-slate-400'">
                                        <i class="bi bi-people-fill mr-1"></i>
                                        {{ $session->committee?->name ?? 'No Committee' }}
                                    </p>
                                </div>
                                <div x-show="selectedSession === {{ $session->id }}" class="shrink-0">
                                    <i class="bi bi-check-circle-fill text-slate-50"></i>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
                <!-- Hidden select for form submission -->
                <select id="sessionSelect" class="hidden">
                    @foreach ($activeSessions as $session)
                        <option value="{{ $session->id }}" {{ $loop->first ? 'selected' : '' }}>
                            {{ $session->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Scanner Card -->
            <x-card>
                <x-slot name="header">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-green-100 dark:bg-green-900/30 rounded-xl">
                            <i class="bi bi-qr-code-scan text-green-600 dark:text-green-400 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100">Camera Scanner</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Point camera at member's QR code</p>
                        </div>
                    </div>
                </x-slot>

                <!-- QR Scanner Container -->
                <div
                    class="rounded-2xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 relative bg-slate-900">
                    <div id="reader" style="width: 100%;"></div>
                </div>

                <!-- Scan Result -->
                <div id="result" class="mt-4 empty:hidden"></div>

                <!-- Scanner Tips -->
                <div
                    class="mt-4 p-3 bg-slate-100 dark:bg-slate-900/50 rounded-xl text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex items-start gap-2">
                        <i class="bi bi-lightbulb text-amber-500 mt-0.5"></i>
                        <span><strong class="text-slate-600 dark:text-slate-300">Tip:</strong> Hold the QR code steady
                            and
                            ensure good lighting for faster scanning.</span>
                    </div>
                </div>
            </x-card>

            <!-- Recent Scans List -->
            <x-card class="p-0" :embedded="true">
                <x-slot name="header">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-clock-history text-slate-400"></i>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100">Recent Scans</h3>
                    </div>
                    <span class="text-xs text-slate-500 dark:text-slate-400">This device only</span>
                </x-slot>

                <ul class="divide-y divide-slate-200 dark:divide-slate-700" id="recentScansList">
                    <li class="px-6 py-10 text-center" id="noScansPlaceholder">
                        <x-empty-state icon="qr-code" title="No scans yet"
                            description="Scanned members will appear here" :center="true" />
                    </li>
                </ul>
            </x-card>
        @endif
    </div>

    @if (!$activeSessions->isEmpty())
        <script>
            const html5QrCode = new Html5Qrcode("reader");
            let isScanning = true;
            let lastScannedCode = null;
            let lastScannedTime = 0;

            // Audio Feedback
            const successAudio = new Audio("{{ asset('sounds/success.mp3') }}");
            const errorAudio = new Audio("{{ asset('sounds/error.mp3') }}");

            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                if (!isScanning) return;

                // Debounce same code (prevent double scan of same person)
                const now = Date.now();
                if (decodedText === lastScannedCode && (now - lastScannedTime) < 3000) {
                    return; // Ignore same code for 3 seconds
                }

                // Pause scanning visually
                isScanning = false;
                html5QrCode.pause();

                lastScannedCode = decodedText;
                lastScannedTime = now;

                const sessionId = document.getElementById('sessionSelect').value;

                // Parse QR code: "ID,Name,Committee" or just "ID"
                let userId = decodedText;
                if (decodedText.includes(',')) {
                    userId = decodedText.split(',')[0].trim();
                }

                console.log('Scanning for Session:', sessionId, 'User ID:', userId);

                if (!sessionId) {
                    alert('Please select a session first.');
                    isScanning = true;
                    html5QrCode.resume();
                    return;
                }

                const scanRoute = "{{ route('scan.store', ':id') }}";
                const url = scanRoute.replace(':id', sessionId);

                fetch(url, {
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
                    .then(async response => {
                        const isJson = response.headers.get('content-type')?.includes('application/json');
                        const data = isJson ? await response.json() : null;

                        if (!response.ok) {
                            // If NOT JSON (e.g. 500 error page), we throw an error with status text
                            if (!isJson) {
                                throw new Error(`Server Error: ${response.status} ${response.statusText}`);
                            }
                            // If JSON, use the message from body
                            throw new Error(data.message || 'Unknown Error');
                        }

                        return {
                            status: response.status,
                            body: data
                        };
                    })
                    .then(result => {
                        const resultDiv = document.getElementById('result');
                        const recentList = document.getElementById('recentScansList');
                        const placeholder = document.getElementById('noScansPlaceholder');

                        if (result.status === 200) {
                            // Play Success Sound
                            successAudio.play().catch(e => console.log('Audio play failed:', e));

                            // Success Alert
                            const statusIcon = result.body.status === 'present' ? 'bi-check-circle-fill' :
                                'bi-clock-fill';
                            const statusBg = result.body.status === 'present' ?
                                'bg-green-100 dark:bg-green-900/30 border-green-200 dark:border-green-800/30' :
                                'bg-amber-100 dark:bg-amber-900/30 border-amber-200 dark:border-amber-800/30';
                            const statusText = result.body.status === 'present' ?
                                'text-green-800 dark:text-green-300' :
                                'text-amber-800 dark:text-amber-300';

                            resultDiv.innerHTML = `<div class="p-4 rounded-xl ${statusBg} ${statusText} border flex items-center gap-3 animate-fade-in-down">
                                <i class="bi ${statusIcon} text-xl shrink-0"></i>
                                <div>
                                    <strong class="font-bold">${result.body.user}</strong>
                                    <span class="opacity-80">marked as ${result.body.status}</span>
                                </div>
                            </div>`;

                            // Add to recent list
                            if (placeholder) placeholder.remove();
                            const li = document.createElement('li');
                            li.className =
                                'px-5 py-4 flex justify-between items-center hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors animate-fade-in';

                            const statusColor = result.body.status === 'present' ?
                                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';

                            li.innerHTML = `
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-blue-500 to-teal-500 flex items-center justify-center text-slate-50 text-xs font-bold">
                                        ${result.body.user.charAt(0).toUpperCase()}
                                    </div>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">${result.body.user}</span>
                                </div>
                                <span class="px-3 py-1.5 rounded-lg text-xs font-bold ${statusColor}">
                                    ${result.body.status.charAt(0).toUpperCase() + result.body.status.slice(1)}
                                </span>
                            `;
                            recentList.prepend(li);

                        }

                        // Resume scanning after 1 second
                        setTimeout(() => {
                            // document.getElementById('result').innerHTML = ''; // Optional: Clear result or leave it
                            html5QrCode.resume();
                            isScanning = true;
                        }, 1000);
                    })
                    .catch(error => {
                        console.error('Scan Error:', error);
                        // Play Error Sound
                        errorAudio.play().catch(e => console.log('Audio play failed:', e));

                        document.getElementById('result').innerHTML =
                            `<div class="p-4 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800/30 flex items-center gap-3">
                                <i class="bi bi-exclamation-triangle-fill text-xl shrink-0"></i>
                                <div>
                                    <strong class="font-bold">Error</strong>
                                    <p class="text-sm opacity-90">${error.message}</p>
                                </div>
                            </div>`;
                        setTimeout(() => {
                            html5QrCode.resume();
                            isScanning = true;
                        }, 1000); // Resume speed
                    });
            };

            const config = {
                fps: 10,
                qrbox: (viewfinderWidth, viewfinderHeight) => {
                    // Responsive QR Box: 70% of the smaller dimension
                    const minEdgePercentage = 0.7;
                    const minDimension = Math.min(viewfinderWidth, viewfinderHeight);
                    const boxSize = Math.floor(minDimension * minEdgePercentage);

                    return {
                        width: boxSize,
                        height: boxSize
                    };
                },
                aspectRatio: 1.0
            };

            // Start scanning
            html5QrCode.start({
                facingMode: "environment"
            }, config, qrCodeSuccessCallback);
        </script>
    @endif
</x-app-layout>
