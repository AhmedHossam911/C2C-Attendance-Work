@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-card>
            <x-slot name="header">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Create Attendance Session</h3>
            </x-slot>

            <form method="POST" action="{{ route('sessions.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Committee</label>
                    <select name="committee_id"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                        required>
                        @foreach ($committees as $committee)
                            <option value="{{ $committee->id }}">{{ $committee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Title</label>
                    <input type="text" name="title"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                        placeholder="e.g., General Assembly Meeting" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Late Threshold
                        (Minutes)</label>
                    <input type="number" name="late_threshold_minutes"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                        value="15" min="0" required>
                    <p class="text-xs text-slate-500 mt-1">Members scanning after this time will be marked as late.</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="counts_for_attendance" value="0">
                    <input type="checkbox"
                        class="rounded border-slate-300 text-brand-blue shadow-sm focus:border-brand-blue focus:ring focus:ring-brand-blue/20"
                        name="counts_for_attendance" value="1" id="countsCheck" checked>
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300" for="countsCheck">Counts for
                        Attendance</label>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white font-bold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-95">
                        Create Session
                    </button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
