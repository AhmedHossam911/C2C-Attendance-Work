@extends('Common.Layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        {{-- Back Link --}}
        <div class="mb-4">
            <a href="{{ route('committees.index') }}"
                class="inline-flex items-center text-sm text-slate-500 hover:text-brand-blue transition-colors">
                <i class="bi bi-arrow-left mr-2"></i> Back to Committees
            </a>
        </div>

        <x-card>
            <x-slot name="header">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-brand-blue/10 rounded-xl">
                        <i class="bi bi-people-fill text-brand-blue text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">Create New Committee</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Add a new committee to the organization</p>
                    </div>
                </div>
            </x-slot>

            <form method="POST" action="{{ route('committees.store') }}" class="space-y-5">
                @csrf

                {{-- Name --}}
                <div>
                    <x-input-label for="name" class="mb-2">
                        <i class="bi bi-tag text-slate-400 mr-1"></i> Committee Name
                    </x-input-label>
                    <x-text-input type="text" name="name" id="name" value="{{ old('name') }}" required
                        placeholder="e.g., Technical Committee" />
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i>
                            {{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <x-input-label for="description" class="mb-2">
                        <i class="bi bi-card-text text-slate-400 mr-1"></i> Description
                    </x-input-label>
                    <x-textarea name="description" id="description" rows="4"
                        placeholder="Describe the committee's purpose and responsibilities...">{{ old('description') }}</x-textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i>
                            {{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="pt-4 flex flex-col-reverse sm:flex-row gap-3 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ route('committees.index') }}"
                        class="flex-1 px-5 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-xl text-center transition-all">
                        Cancel
                    </a>
                    <x-primary-button type="submit" class="flex-1 justify-center py-3">
                        <i class="bi bi-check-lg mr-1"></i> Create Committee
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
