@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Create Committee</h2>
        </div>

        <x-card>
            <form method="POST" action="{{ route('committees.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Name</label>
                    <input type="text" name="name"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Description</label>
                    <textarea name="description"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                        rows="3"></textarea>
                </div>
                <div class="pt-2">
                    <button type="submit"
                        class="w-full px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white font-semibold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-95">
                        Create Committee
                    </button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
