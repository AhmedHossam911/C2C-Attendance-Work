@extends('Common.Layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Committee Authorizations</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage HR user access to committees</p>
        </div>
    </div>

    <div class="flex flex-col gap-8">
        <!-- Grant Authorization Form -->
        <div>
            <x-card>
                <x-slot name="header">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-brand-blue/10 rounded-lg">
                            <i class="bi bi-key-fill text-brand-blue"></i>
                        </div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">Grant Authorization</h3>
                    </div>
                </x-slot>

                <form action="{{ route('authorizations.store') }}" method="POST"
                    class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">
                    @csrf
                    <div>
                        <x-input-label for="user_id" value="User (HR, Head, Board)" class="mb-1.5" />
                        <x-select-input name="user_id" id="user_id" class="w-full">
                            <option value="">Select User...</option>
                            @foreach ($hrUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </x-select-input>
                        <x-input-error :messages="$errors->get('user_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="committee_id" value="Committee" class="mb-1.5" />
                        <x-select-input name="committee_id" id="committee_id" class="w-full">
                            <option value="">Select Committee...</option>
                            @foreach ($committees as $committee)
                                <option value="{{ $committee->id }}">{{ $committee->name }}</option>
                            @endforeach
                        </x-select-input>
                        <x-input-error :messages="$errors->get('committee_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-primary-button class="w-full justify-center py-3 md:py-2.5 shadow-lg shadow-brand-blue/20">
                            <i class="bi bi-shield-plus mr-2"></i>
                            Grant Access
                        </x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Active Authorizations -->
        <div>
            <!-- Desktop Table -->
            <div class="hidden md:block">
                <x-card class="p-0 overflow-hidden" :embedded="true">
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
                                    class="w-full pl-9 pr-8 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition-all placeholder:text-slate-400"
                                    placeholder="Search user or committee..." value="{{ request('search') }}">
                                @if (request('search'))
                                    <a href="{{ route('authorizations.index') }}"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                        <i class="bi bi-x-circle-fill text-xs"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                    </x-slot>

                    <x-table>
                        <x-slot name="head">
                            <x-table.th class="w-3/12">User</x-table.th>
                            <x-table.th class="w-2/12">Role</x-table.th>
                            <x-table.th class="w-3/12">Committee</x-table.th>
                            <x-table.th class="w-2/12">Granted By</x-table.th>
                            <x-table.th class="w-2/12">Date</x-table.th>
                            <x-table.th class="w-1/12 text-right">Actions</x-table.th>
                        </x-slot>
                        @forelse ($authorizations as $auth)
                            <x-table.tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <x-table.td>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-9 w-9 rounded-full bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white text-xs font-bold ring-2 ring-white dark:ring-slate-800 flex-shrink-0">
                                            {{ substr($auth->user->name, 0, 1) }}
                                        </div>
                                        <span
                                            class="font-bold text-slate-800 dark:text-white truncate max-w-[150px]">{{ $auth->user->name }}</span>
                                    </div>
                                </x-table.td>
                                <x-table.td>
                                    <span
                                        class="inline-flex px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-bold uppercase tracking-wide text-slate-500 border border-slate-200 dark:border-slate-700">
                                        {{ str_replace('_', ' ', $auth->user->role) }}
                                    </span>
                                </x-table.td>
                                <x-table.td>
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-xs font-semibold text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800/30">
                                        {{ $auth->committee->name }}
                                    </span>
                                </x-table.td>
                                <x-table.td class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                                    {{ $auth->granter->name }}
                                </x-table.td>
                                <x-table.td
                                    class="text-sm text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
                                    {{ $auth->created_at->format('M d, Y') }}
                                </x-table.td>
                                <x-table.td align="right">
                                    <form action="{{ route('authorizations.destroy', $auth) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to revoke this authorization?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-white border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors text-xs font-bold shadow-sm"
                                            title="Revoke Authorization">
                                            <i class="bi bi-trash3-fill"></i>
                                            <span>Revoke</span>
                                        </button>
                                    </form>
                                </x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.tr>
                                <x-table.td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-full mb-3">
                                            <i class="bi bi-shield-slash text-3xl text-slate-300"></i>
                                        </div>
                                        <p class="text-slate-500 dark:text-slate-400 font-medium text-lg">No active
                                            authorizations</p>
                                        <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Grant access to HR users
                                            using the form above.</p>
                                    </div>
                                </x-table.td>
                            </x-table.tr>
                        @endforelse
                    </x-table>

                    @if ($authorizations->hasPages())
                        <div
                            class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                            <div class="flex items-center justify-between">
                                <div class="hidden sm:block text-sm text-slate-500 dark:text-slate-400">
                                    Showing <span
                                        class="font-bold text-slate-700 dark:text-slate-200">{{ $authorizations->firstItem() }}</span>
                                    to <span
                                        class="font-bold text-slate-700 dark:text-slate-200">{{ $authorizations->lastItem() }}</span>
                                    of <span
                                        class="font-bold text-slate-700 dark:text-slate-200">{{ $authorizations->total() }}</span>
                                </div>
                                <div class="flex gap-2 mx-auto sm:mx-0">
                                    <a href="{{ $authorizations->previousPageUrl() }}"
                                        class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 transition-colors {{ $authorizations->onFirstPage() ? 'pointer-events-none opacity-50' : '' }}">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>

                                    @foreach ($authorizations->getUrlRange(max(1, $authorizations->currentPage() - 1), min($authorizations->lastPage(), $authorizations->currentPage() + 1)) as $page => $url)
                                        <a href="{{ $url }}"
                                            class="px-3.5 py-2 rounded-lg text-sm font-bold border {{ $page == $authorizations->currentPage() ? 'bg-brand-blue border-brand-blue text-white shadow-md shadow-brand-blue/20' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                            {{ $page }}
                                        </a>
                                    @endforeach

                                    <a href="{{ $authorizations->nextPageUrl() }}"
                                        class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 transition-colors {{ !$authorizations->hasMorePages() ? 'pointer-events-none opacity-50' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
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
                        class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-700/50 text-xs font-bold text-slate-600 dark:text-slate-400">
                        {{ $authorizations->total() }} Total
                    </span>
                </div>

                @forelse ($authorizations as $auth)
                    <x-card class="relative">
                        <!-- Header: User & Role -->
                        <div class="flex items-start justify-between gap-4 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <div
                                        class="h-12 w-12 rounded-xl bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white text-lg font-bold shadow-lg shadow-brand-blue/20">
                                        {{ substr($auth->user->name, 0, 1) }}
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 bg-white dark:bg-slate-800 p-0.5 rounded-lg">
                                        <div
                                            class="bg-green-500 h-2.5 w-2.5 rounded-full border-2 border-white dark:border-slate-800">
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-base leading-tight">
                                        {{ $auth->user->name }}
                                    </h4>
                                    <span
                                        class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400">
                                        {{ str_replace('_', ' ', $auth->user->role) }}
                                    </span>
                                </div>
                            </div>

                            <form action="{{ route('authorizations.destroy', $auth) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to revoke this authorization?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex items-center gap-2 px-3 py-1.5 bg-white border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors text-xs font-bold shadow-sm"
                                    title="Revoke">
                                    <i class="bi bi-trash"></i>
                                    <span>Revoke</span>
                                </button>
                            </form>
                        </div>

                        <!-- Body: Committee Info -->
                        <div
                            class="bg-slate-50 dark:bg-slate-700/30 rounded-xl p-4 mb-4 border border-slate-100 dark:border-slate-700/50">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Authorized For
                            </p>
                            <div class="flex items-center gap-2 text-brand-blue font-bold">
                                <i class="bi bi-people-fill"></i>
                                <span>{{ $auth->committee->name }}</span>
                            </div>
                        </div>

                        <!-- Footer: Meta -->
                        <div
                            class="grid grid-cols-2 gap-4 text-xs text-slate-500 dark:text-slate-400 border-t border-slate-100 dark:border-slate-800 pt-3">
                            <div>
                                <span
                                    class="block text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-0.5">Granted
                                    By</span>
                                <span
                                    class="font-medium text-slate-700 dark:text-slate-300">{{ $auth->granter->name }}</span>
                            </div>
                            <div class="text-right">
                                <span
                                    class="block text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-0.5">Date</span>
                                <span
                                    class="font-medium text-slate-700 dark:text-slate-300">{{ $auth->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </x-card>
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
                    <div class="mt-4 flex justify-center">
                        <div class="flex gap-2">
                            <a href="{{ $authorizations->previousPageUrl() }}"
                                class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 transition-colors {{ $authorizations->onFirstPage() ? 'pointer-events-none opacity-50' : '' }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>

                            @foreach ($authorizations->getUrlRange(max(1, $authorizations->currentPage() - 1), min($authorizations->lastPage(), $authorizations->currentPage() + 1)) as $page => $url)
                                <a href="{{ $url }}"
                                    class="px-3.5 py-2 rounded-lg text-sm font-bold border {{ $page == $authorizations->currentPage() ? 'bg-brand-blue border-brand-blue text-white shadow-md shadow-brand-blue/20' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                    {{ $page }}
                                </a>
                            @endforeach

                            <a href="{{ $authorizations->nextPageUrl() }}"
                                class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 transition-colors {{ !$authorizations->hasMorePages() ? 'pointer-events-none opacity-50' : '' }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
