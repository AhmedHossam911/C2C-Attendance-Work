@extends('Common.Layouts.app')

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
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-2">
                                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                    <i class="bi bi-shield-check text-green-600 dark:text-green-400"></i>
                                </div>
                                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Active Authorizations</h3>
                            </div>

                            <!-- specific search for desktop -->
                            <form action="{{ route('authorizations.index') }}" method="GET"
                                class="relative group w-full sm:w-64">
                                <div
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i class="bi bi-search"></i>
                                </div>
                                <input type="text" name="search"
                                    class="w-full pl-9 pr-8 py-1.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all placeholder:text-slate-400"
                                    placeholder="Search user or committee..." value="{{ request('search') }}">
                                @if (request('search'))
                                    <a href="{{ route('authorizations.index') }}"
                                        class="absolute inset-y-0 right-0 pr-2 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                        <i class="bi bi-x-circle-fill text-xs"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                    </x-slot>

                    <x-table>
                        <x-slot name="head">
                            <x-table.th class="w-3/12">User</x-table.th>
                            <x-table.th class="w-1/12">Role</x-table.th>
                            <x-table.th class="w-3/12">Committee</x-table.th>
                            <x-table.th class="w-2/12">Granted By</x-table.th>
                            <x-table.th class="w-2/12">Date</x-table.th>
                            <x-table.th class="w-1/12">Actions</x-table.th>
                        </x-slot>
                        @forelse ($authorizations as $auth)
                            <x-table.tr>
                                <x-table.td :tooltip="true">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-8 w-8 rounded-full bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ substr($auth->user->name, 0, 1) }}
                                        </div>
                                        <span
                                            class="font-semibold text-slate-800 dark:text-white truncate">{{ $auth->user->name }}</span>
                                    </div>
                                </x-table.td>
                                <x-table.td>
                                    <span
                                        class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-xs font-bold uppercase text-slate-500">
                                        {{ str_replace('_', ' ', $auth->user->role) }}
                                    </span>
                                </x-table.td>
                                <x-table.td :tooltip="true">
                                    <span
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 text-xs font-medium text-slate-700 dark:text-slate-300">
                                        {{ $auth->committee->name }}
                                    </span>
                                </x-table.td>
                                <x-table.td :tooltip="true"
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
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden group">

                        <!-- Decorative Background Gradient -->
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-brand-blue/5 to-transparent rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110">
                        </div>

                        <div class="relative z-10">
                            <!-- Header: User & Role -->
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <div
                                            class="h-14 w-14 rounded-2xl bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-brand-blue/20">
                                            {{ substr($auth->user->name, 0, 1) }}
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 bg-white dark:bg-slate-800 p-1 rounded-lg">
                                            <div
                                                class="bg-green-500 h-3 w-3 rounded-full border-2 border-white dark:border-slate-800">
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 dark:text-white text-lg leading-tight">
                                            {{ $auth->user->name }}</h4>
                                        <span
                                            class="inline-flex mt-1.5 px-2.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/50 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                            {{ str_replace('_', ' ', $auth->user->role) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Body: Committee Info -->
                            <div
                                class="bg-slate-50 dark:bg-slate-700/30 rounded-xl p-4 mb-4 border border-slate-100 dark:border-slate-700/50">
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Authorized For
                                </p>
                                <div class="flex items-center gap-2 text-brand-blue font-bold">
                                    <i class="bi bi-people-fill"></i>
                                    <span>{{ $auth->committee->name }}</span>
                                </div>
                            </div>

                            <!-- Footer: Meta & Action -->
                            <div class="flex items-end justify-between gap-2">
                                <div class="text-xs text-slate-500 dark:text-slate-400 space-y-1">
                                    <div class="flex items-center gap-1.5">
                                        <i class="bi bi-person-check text-slate-400"></i>
                                        <span>By: {{ $auth->granter->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="bi bi-clock-history text-slate-400"></i>
                                        <span>{{ $auth->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>

                                <form action="{{ route('authorizations.destroy', $auth) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to revoke this authorization?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm hover:shadow-md">
                                        <i class="bi bi-trash"></i>
                                        Revoke
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <x-card class="text-center py-10 border-dashed border-2">
                        <div class="flex flex-col items-center">
                            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-full mb-3">
                                <i class="bi bi-shield-plus text-3xl text-slate-300"></i>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 font-medium">No active authorizations</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Use the grant form to add access</p>
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
