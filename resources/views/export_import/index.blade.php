@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Data Management</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Import Users Card -->
        <x-card>
            <x-slot name="header">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Import Users</h3>
            </x-slot>

            <form action="{{ route('import.users') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Excel File</label>
                    <input type="file" name="file"
                        class="block w-full text-sm text-slate-500
                        file:mr-4 file:py-2.5 file:px-4
                        file:rounded-xl file:border-0
                        file:text-sm file:font-semibold
                        file:bg-brand-blue/10 file:text-brand-blue
                        hover:file:bg-brand-blue/20
                        dark:file:bg-brand-blue/20 dark:file:text-brand-blue-light"
                        required>
                </div>

                <div
                    class="p-4 rounded-xl bg-sky-50 text-sky-800 border border-sky-200 dark:bg-sky-900/20 dark:text-sky-300 dark:border-sky-800/30 text-sm">
                    <strong class="block mb-2 font-bold">Instructions:</strong>
                    <ul class="list-disc list-inside space-y-1 ml-1">
                        <li>Use the template provided.</li>
                        <li><strong class="font-medium">committees:</strong> Enter committee names separated by commas
                            (e.g., "IT, HR"). Users will be assigned to these committees.</li>
                        <li><strong class="font-medium">role:</strong> top_management, board, hr, or member.</li>
                        <li><strong class="font-medium">status:</strong> active or pending.</li>
                    </ul>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button type="submit"
                        class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/20 transition-all active:scale-95">
                        Import Users
                    </button>
                    <a href="{{ route('import.template') }}"
                        class="text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
                        Download Template
                    </a>
                </div>
            </form>
        </x-card>

        <!-- Export QRs & Data Card -->
        <x-card>
            <x-slot name="header">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Export QRs & Data</h3>
            </x-slot>

            <form action="{{ route('export.qr') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Select
                        Committee</label>
                    <select name="committee_id"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                        required>
                        @foreach ($committees as $committee)
                            <option value="{{ $committee->id }}">{{ $committee->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white font-semibold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-95">
                        Download ZIP (Excel + Images)
                    </button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
