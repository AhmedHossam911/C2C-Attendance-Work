@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('sessions.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-brand-blue transition-colors">
                <i class="bi bi-arrow-left"></i> Back to Sessions
            </a>
        </div>

        <x-card>
            <x-slot name="header">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="bi bi-plus-circle-fill text-brand-blue"></i>
                    Create Attendance Session
                </h3>
            </x-slot>

            <form method="POST" action="{{ route('sessions.store') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="committee_id" value="Committee" :required="true" />
                    <x-select-input name="committee_id" id="committee_id" class="w-full mt-1" required>
                        @foreach ($committees as $committee)
                            <option value="{{ $committee->id }}">{{ $committee->name }}</option>
                        @endforeach
                    </x-select-input>
                </div>

                <div>
                    <x-input-label for="title" value="Session Title" :required="true" />
                    <x-text-input type="text" name="title" id="title" class="w-full mt-1"
                        placeholder="e.g., Annual General Meeting" required />
                </div>

                <div>
                    <x-input-label for="late_threshold_minutes" value="Late Threshold (Minutes)" :required="true" />
                    <x-text-input type="number" name="late_threshold_minutes" id="late_threshold_minutes"
                        class="w-full mt-1" value="15" min="0" required />
                    <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1">
                        <i class="bi bi-info-circle"></i>
                        Members scanning after this time will be marked as late.
                    </p>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="hidden" name="counts_for_attendance" value="0">
                        <input type="checkbox"
                            class="rounded border-slate-300 text-brand-blue shadow-sm focus:border-brand-blue focus:ring focus:ring-brand-blue/20 h-5 w-5"
                            name="counts_for_attendance" value="1" id="countsCheck" checked>
                        <span class="ml-3 text-sm font-bold text-slate-700 dark:text-slate-300">Counts for Attendance</span>
                    </label>
                    <p class="text-xs text-slate-500 mt-1 ml-8">
                        If unchecked, this session won't affect members' attendance percentage.
                    </p>
                </div>

                <div class="pt-4 flex gap-4">
                    <x-primary-button class="justify-center w-full py-3 text-base">
                        <i class="bi bi-check-lg mr-2"></i> Create Session
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
