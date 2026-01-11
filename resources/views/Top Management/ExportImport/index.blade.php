<x-app-layout>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Data Management</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Import users and export QR codes</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Import Users Card -->
        <x-card>
            <x-slot name="header">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl">
                        <i class="bi bi-upload text-emerald-600 dark:text-emerald-400 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100">Import Users</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Upload Excel to add users</p>
                    </div>
                </div>
            </x-slot>

            <form action="{{ route('import.users') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <x-input-label class="mb-2">
                        <i class="bi bi-file-earmark-excel text-slate-400 mr-1"></i> Excel File
                    </x-input-label>
                    <div class="relative">
                        <input type="file" name="file" accept=".xlsx,.xls,.csv"
                            class="block w-full text-sm text-slate-600 dark:text-slate-400
                            file:mr-4 file:py-3 file:px-5
                            file:rounded-xl file:border-0
                            file:text-sm file:font-bold
                            file:bg-emerald-100 file:text-emerald-700
                            hover:file:bg-emerald-200
                            dark:file:bg-emerald-900/30 dark:file:text-emerald-400
                            dark:hover:file:bg-emerald-900/50
                            cursor-pointer transition-all"
                            required>
                    </div>
                    @error('file')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1"><i
                                class="bi bi-exclamation-circle"></i>
                            {{ $message }}</p>
                    @enderror
                </div>

                <div
                    class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="p-1.5 bg-blue-100 dark:bg-blue-800/30 rounded-lg shrink-0">
                            <i class="bi bi-info-circle text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="text-blue-800 dark:text-blue-300">
                            <strong class="block mb-2 font-bold">Instructions:</strong>
                            <ul class="list-disc list-inside space-y-1.5 text-blue-700 dark:text-blue-400">
                                <li>Use the template provided below</li>
                                <li><strong>committees:</strong> Names separated by commas (e.g., "IT, HR")</li>
                                <li><strong>role:</strong> top_management, board, hr, or member</li>
                                <li><strong>status:</strong> active or pending</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
                    <x-primary-button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/25 justify-center py-3">
                        <i class="bi bi-upload mr-2"></i> Import Users
                    </x-primary-button>
                    <a href="{{ route('import.template') }}"
                        class="inline-flex items-center justify-center px-4 py-3 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="bi bi-download mr-2"></i> Download Template
                    </a>
                </div>
            </form>
        </x-card>

        <!-- Export QRs & Data Card -->
        <x-card>
            <x-slot name="header">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                        <i class="bi bi-download text-blue-600 dark:text-blue-400 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100">Export QRs & Data</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Download QR codes as ZIP</p>
                    </div>
                </div>
            </x-slot>

            <form action="{{ route('export.qr') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <x-input-label class="mb-2">
                        <i class="bi bi-building text-slate-400 mr-1"></i> Select Committee
                    </x-input-label>
                    <x-select-input name="committee_id" required>
                        @foreach ($committees as $committee)
                            <option value="{{ $committee->id }}">{{ $committee->name }}</option>
                        @endforeach
                    </x-select-input>
                </div>

                <div
                    class="p-4 rounded-xl bg-slate-100 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="p-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg shrink-0">
                            <i class="bi bi-file-zip text-slate-600 dark:text-slate-400"></i>
                        </div>
                        <div class="text-slate-600 dark:text-slate-400">
                            <strong class="block mb-1 text-slate-800 dark:text-slate-200">ZIP Contents:</strong>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Excel file with member data</li>
                                <li>QR code images for each member</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <x-primary-button type="submit" class="w-full justify-center py-3">
                        <i class="bi bi-file-zip mr-2"></i> Download ZIP (Excel + Images)
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>

    <!-- Additional Info -->
    <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 rounded-xl">
        <div class="flex items-start gap-3">
            <div class="p-2 bg-amber-100 dark:bg-amber-800/30 rounded-lg shrink-0">
                <i class="bi bi-exclamation-triangle text-amber-600 dark:text-amber-400"></i>
            </div>
            <div class="text-amber-800 dark:text-amber-300 text-sm">
                <strong>Note:</strong> Importing users will create new accounts. Existing users with the same email will
                be
                skipped. Make sure to review the data before importing.
            </div>
        </div>
    </div>
</x-app-layout>
