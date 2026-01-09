@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Committee Authorizations</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage HR user access to committees</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Grant Authorization Form -->
        <div class="lg:col-span-1">
            <x-card>
                <x-slot name="header">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-brand-blue/10 rounded-lg">
                            <i class="bi bi-key-fill text-brand-blue"></i>
                        </div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">Grant Authorization</h3>
                    </div>
                </x-slot>

                <form action="{{ route('authorizations.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="bi bi-person text-slate-400 mr-1"></i> User (HR, Head, Board)
                        </label>
                        <select name="user_id"
                            class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 dark:text-white text-sm transition-all"
                            required>
                            <option value="">Select User...</option>
                            @foreach ($hrUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            <i class="bi bi-people text-slate-400 mr-1"></i> Committee
                        </label>
                        <select name="committee_id"
                            class="w-full px-4 py-3 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 dark:text-white text-sm transition-all"
                            required>
                            <option value="">Select Committee...</option>
                            @foreach ($committees as $committee)
                                <option value="{{ $committee->id }}">{{ $committee->name }}</option>
                            @endforeach
                        </select>
                        @error('committee_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="pt-3">
                        <button type="submit"
                            class="w-full px-5 py-3 bg-brand-blue hover:bg-brand-blue/90 text-white font-bold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <i class="bi bi-shield-plus"></i>
                            Grant Access
                        </button>
                    </div>
                </form>
            </x-card>

            <!-- Quick Info Card (Mobile Friendly) -->
            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/50">
                <div class="flex items-start gap-3">
                    <i class="bi bi-info-circle text-blue-500 text-lg mt-0.5"></i>
                    <div>
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-300">What is an authorization?</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Authorizations allow HR users to manage
                            specific committees, including creating sessions and viewing attendance.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Authorizations -->
        <div class="lg:col-span-2">
            <!-- Desktop Table -->
            <div class="hidden md:block">
                <x-card class="p-0" :embedded="true">
                    <x-slot name="header">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                    <i class="bi bi-shield-check text-green-600 dark:text-green-400"></i>
                                </div>
                                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Active Authorizations</h3>
                            </div>
                            <span
                                class="px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-700/50 text-xs font-bold text-slate-600 dark:text-slate-400">
                                {{ $authorizations->total() }} Total
                            </span>
                        </div>
                    </x-slot>

                    <x-table :headers="['User', 'Role', 'Committee', 'Granted By', 'Date', 'Actions']">
                        @forelse ($authorizations as $auth)
                            <x-table.tr>
                                <x-table.td>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-8 w-8 rounded-full bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($auth->user->name, 0, 1) }}
                                        </div>
                                        <span
                                            class="font-semibold text-slate-800 dark:text-white">{{ $auth->user->name }}</span>
                                    </div>
                                </x-table.td>
                                <x-table.td>
                                    <span
                                        class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-xs font-bold uppercase text-slate-500">
                                        {{ str_replace('_', ' ', $auth->user->role) }}
                                    </span>
                                </x-table.td>
                                <x-table.td>
                                    <span
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 text-xs font-medium text-slate-700 dark:text-slate-300">
                                        {{ $auth->committee->name }}
                                    </span>
                                </x-table.td>
                                <x-table.td
                                    class="text-sm text-slate-500 dark:text-slate-400">{{ $auth->granter->name }}</x-table.td>
                                <x-table.td
                                    class="text-sm text-slate-500 dark:text-slate-400">{{ $auth->created_at->format('M d, Y') }}</x-table.td>
                                <x-table.td>
                                    <form action="{{ route('authorizations.destroy', $auth) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to revoke this authorization?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3.5 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5">
                                            <i class="bi bi-x-circle"></i>
                                            Revoke
                                        </button>
                                    </form>
                                </x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="p-4 bg-slate-100 dark:bg-slate-800 rounded-full mb-3">
                                            <i class="bi bi-shield-slash text-2xl text-slate-400"></i>
                                        </div>
                                        <p class="text-slate-500 dark:text-slate-400 font-medium">No active authorizations
                                        </p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Grant access to HR users
                                            using the form</p>
                                    </div>
                                </td>
                            </x-table.tr>
                        @endforelse
                    </x-table>

                    @if ($authorizations->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Showing <span
                                        class="font-semibold text-slate-800 dark:text-slate-200">{{ $authorizations->firstItem() }}</span>
                                    to <span
                                        class="font-semibold text-slate-800 dark:text-slate-200">{{ $authorizations->lastItem() }}</span>
                                    of <span
                                        class="font-semibold text-slate-800 dark:text-slate-200">{{ $authorizations->total() }}</span>
                                </p>
                                <div class="flex items-center gap-1">
                                    @if ($authorizations->onFirstPage())
                                        <span
                                            class="inline-flex items-center justify-center w-10 h-10 text-slate-400 bg-slate-100 dark:bg-slate-800 cursor-not-allowed rounded-xl">
                                            <i class="bi bi-chevron-left"></i>
                                        </span>
                                    @else
                                        <a href="{{ $authorizations->previousPageUrl() }}"
                                            class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-brand-blue transition-all">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    @endif

                                    @foreach ($authorizations->getUrlRange(1, $authorizations->lastPage()) as $page => $url)
                                        @if ($page == $authorizations->currentPage())
                                            <span
                                                class="inline-flex items-center justify-center w-10 h-10 text-white font-bold bg-brand-blue rounded-xl shadow-sm">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}"
                                                class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 font-medium bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-brand-blue transition-all">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach

                                    @if ($authorizations->hasMorePages())
                                        <a href="{{ $authorizations->nextPageUrl() }}"
                                            class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-brand-blue transition-all">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    @else
                                        <span
                                            class="inline-flex items-center justify-center w-10 h-10 text-slate-400 bg-slate-100 dark:bg-slate-800 cursor-not-allowed rounded-xl">
                                            <i class="bi bi-chevron-right"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </x-card>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">Active Authorizations</h3>
                    <span
                        class="px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-700/50 text-xs font-bold text-slate-600 dark:text-slate-400">
                        {{ $authorizations->total() }} Total
                    </span>
                </div>

                @forelse ($authorizations as $auth)
                    <x-card class="relative">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-12 w-12 rounded-full bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white text-lg font-bold shadow-md">
                                    {{ substr($auth->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-base">{{ $auth->user->name }}
                                    </h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                                        <i class="bi bi-people text-xs mr-1"></i> {{ $auth->committee->name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3">
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                <span class="block"><i class="bi bi-person-check mr-1"></i> Granted by
                                    {{ $auth->granter->name }}</span>
                                <span class="block mt-1"><i class="bi bi-calendar3 mr-1"></i>
                                    {{ $auth->created_at->format('M d, Y') }}</span>
                            </div>
                            <form action="{{ route('authorizations.destroy', $auth) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to revoke this authorization?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-4 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                                    <i class="bi bi-x-circle"></i>
                                    Revoke Access
                                </button>
                            </form>
                        </div>
                    </x-card>
                @empty
                    <x-card class="text-center py-10">
                        <div class="flex flex-col items-center">
                            <div class="p-4 bg-slate-100 dark:bg-slate-800 rounded-full mb-3">
                                <i class="bi bi-shield-slash text-3xl text-slate-400"></i>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 font-medium">No active authorizations</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Grant access using the form above
                            </p>
                        </div>
                    </x-card>
                @endforelse

                @if ($authorizations->hasPages())
                    <div class="mt-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-start">
                                @if ($authorizations->onFirstPage())
                                    <span
                                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-100 dark:bg-slate-800 cursor-not-allowed rounded-xl">
                                        <i class="bi bi-chevron-left mr-1"></i> Previous
                                    </span>
                                @else
                                    <a href="{{ $authorizations->previousPageUrl() }}"
                                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                                        <i class="bi bi-chevron-left mr-1"></i> Previous
                                    </a>
                                @endif
                            </div>

                            <div class="px-4 text-sm text-slate-600 dark:text-slate-400 font-medium">
                                {{ $authorizations->currentPage() }} / {{ $authorizations->lastPage() }}
                            </div>

                            <div class="flex-1 flex justify-end">
                                @if ($authorizations->hasMorePages())
                                    <a href="{{ $authorizations->nextPageUrl() }}"
                                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                                        Next <i class="bi bi-chevron-right ml-1"></i>
                                    </a>
                                @else
                                    <span
                                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-100 dark:bg-slate-800 cursor-not-allowed rounded-xl">
                                        Next <i class="bi bi-chevron-right ml-1"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
