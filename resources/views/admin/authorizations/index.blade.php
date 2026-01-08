@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Committee Authorizations</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Grant Authorization Form -->
        <div class="lg:col-span-1">
            <x-card>
                <x-slot name="header">
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">Grant Authorization</h3>
                </x-slot>

                <form action="{{ route('authorizations.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">HR User</label>
                        <select name="user_id"
                            class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                            required>
                            <option value="">Select HR User...</option>
                            @foreach ($hrUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Committee</label>
                        <select name="committee_id"
                            class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                            required>
                            <option value="">Select Committee...</option>
                            @foreach ($committees as $committee)
                                <option value="{{ $committee->id }}">{{ $committee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white font-semibold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-95">
                            Grant Access
                        </button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Active Authorizations Table -->
        <div class="lg:col-span-2">
            <x-card class="p-0" :embedded="true">
                <x-slot name="header">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">Active Authorizations</h3>
                        <span
                            class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700/50 text-xs font-bold text-slate-600 dark:text-slate-400">
                            {{ $authorizations->total() }} Users
                        </span>
                    </div>
                </x-slot>

                <x-table :headers="['HR User', 'Committee', 'Granted By', 'Date', 'Actions']">
                    @forelse ($authorizations as $auth)
                        <x-table.tr>
                            <x-table.td
                                class="font-bold text-slate-800 dark:text-white">{{ $auth->user->name }}</x-table.td>
                            <x-table.td>{{ $auth->committee->name }}</x-table.td>
                            <x-table.td class="text-xs text-slate-500">{{ $auth->granter->name }}</x-table.td>
                            <x-table.td class="text-xs text-slate-500">{{ $auth->created_at->format('Y-m-d') }}</x-table.td>
                            <x-table.td>
                                <form action="{{ route('authorizations.destroy', $auth) }}" method="POST"
                                    onsubmit="return confirm('Revoke this authorization?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold transition-colors">
                                        Revoke
                                    </button>
                                </form>
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400 italic">No
                                active authorizations.</td>
                        </x-table.tr>
                    @endforelse
                </x-table>

                @if ($authorizations->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $authorizations->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    </div>
@endsection
