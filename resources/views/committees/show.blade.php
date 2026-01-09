@extends('layouts.app')

@section('content')
    {{-- Back Link --}}
    <div class="mb-4">
        <a href="{{ route('committees.index') }}"
            class="inline-flex items-center text-sm text-slate-500 hover:text-brand-blue transition-colors">
            <i class="bi bi-arrow-left mr-2"></i> Back to Committees
        </a>
    </div>

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="hidden sm:flex p-3 bg-gradient-to-br from-brand-blue/10 to-brand-teal/10 rounded-xl">
                    <i class="bi bi-people-fill text-brand-blue text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $committee->name }}</h2>
                    @if ($committee->description)
                        <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm line-clamp-2">
                            {{ $committee->description }}</p>
                    @endif
                </div>
            </div>
            <span
                class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20">
                <i class="bi bi-person-fill mr-2"></i> {{ $committee->users->count() }} Members
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Members List -->
        <div class="lg:col-span-2">
            {{-- Desktop Table --}}
            <div class="hidden md:block">
                <x-card class="p-0" :embedded="true">
                    <x-slot name="header">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-person-lines-fill text-slate-400"></i>
                                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Members</h3>
                            </div>
                            <form action="{{ route('committees.show', $committee) }}" method="GET"
                                class="flex items-center gap-2">
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <i class="bi bi-search text-sm"></i>
                                    </div>
                                    <x-text-input type="text" name="search" class="pl-9 py-2 text-sm w-56"
                                        placeholder="Search members..." value="{{ request('search') }}" />
                                </div>
                                @if (request('search'))
                                    <a href="{{ route('committees.show', $committee) }}"
                                        class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                    </x-slot>

                    <x-table :headers="[
                        '#',
                        'Member',
                        'Email',
                        Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board') ? 'Actions' : '',
                    ]">
                        @forelse ($members as $member)
                            <x-table.tr>
                                <x-table.td class="text-slate-400 text-sm">{{ $loop->iteration }}</x-table.td>
                                <x-table.td>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-9 w-9 rounded-full bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($member->name, 0, 1) }}
                                        </div>
                                        <span
                                            class="font-semibold text-slate-800 dark:text-white">{{ $member->name }}</span>
                                    </div>
                                </x-table.td>
                                <x-table.td class="text-slate-500 dark:text-slate-400">{{ $member->email }}</x-table.td>
                                @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board'))
                                    <x-table.td>
                                        <form action="{{ route('committees.remove', [$committee, $member]) }}"
                                            method="POST"
                                            onsubmit="return confirm('Remove {{ $member->name }} from this committee?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3.5 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5">
                                                <i class="bi bi-x-circle"></i> Remove
                                            </button>
                                        </form>
                                    </x-table.td>
                                @endif
                            </x-table.tr>
                        @empty
                            <x-table.tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="p-4 bg-slate-100 dark:bg-slate-800 rounded-full mb-3">
                                            <i class="bi bi-person-x text-2xl text-slate-400"></i>
                                        </div>
                                        <p class="text-slate-500 dark:text-slate-400 font-medium">No members found</p>
                                        @if (request('search'))
                                            <p class="text-xs text-slate-400 mt-1">Try adjusting your search</p>
                                        @endif
                                    </div>
                                </td>
                            </x-table.tr>
                        @endforelse
                    </x-table>

                    {{-- Desktop Pagination --}}
                    @if ($members->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Showing <span
                                        class="font-semibold text-slate-800 dark:text-slate-200">{{ $members->firstItem() }}</span>
                                    to <span
                                        class="font-semibold text-slate-800 dark:text-slate-200">{{ $members->lastItem() }}</span>
                                    of <span
                                        class="font-semibold text-slate-800 dark:text-slate-200">{{ $members->total() }}</span>
                                </p>
                                <div class="flex items-center gap-1">
                                    @if ($members->onFirstPage())
                                        <span
                                            class="inline-flex items-center justify-center w-10 h-10 text-slate-400 bg-slate-100 dark:bg-slate-800 cursor-not-allowed rounded-xl">
                                            <i class="bi bi-chevron-left"></i>
                                        </span>
                                    @else
                                        <a href="{{ $members->previousPageUrl() }}"
                                            class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-brand-blue transition-all">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    @endif

                                    @foreach ($members->getUrlRange(1, $members->lastPage()) as $page => $url)
                                        @if ($page == $members->currentPage())
                                            <span
                                                class="inline-flex items-center justify-center w-10 h-10 text-white font-bold bg-brand-blue rounded-xl shadow-sm">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}"
                                                class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 font-medium bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-brand-blue transition-all">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach

                                    @if ($members->hasMorePages())
                                        <a href="{{ $members->nextPageUrl() }}"
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

            {{-- Mobile Cards --}}
            <div class="md:hidden space-y-4">
                {{-- Search --}}
                <form action="{{ route('committees.show', $committee) }}" method="GET" class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-search text-sm"></i>
                        </div>
                        <x-text-input type="text" name="search" class="pl-9 py-2.5 text-sm"
                            placeholder="Search members..." value="{{ request('search') }}" />
                    </div>
                    @if (request('search'))
                        <a href="{{ route('committees.show', $committee) }}"
                            class="p-2.5 bg-slate-100 dark:bg-slate-700 text-slate-400 hover:text-red-500 rounded-xl transition-colors">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </form>

                @forelse ($members as $member)
                    <x-card class="relative overflow-hidden">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-12 w-12 rounded-full bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white text-lg font-bold shadow-md">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-white">{{ $member->name }}</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 truncate max-w-[180px]">
                                        {{ $member->email }}</p>
                                </div>
                            </div>
                            @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board'))
                                <form action="{{ route('committees.remove', [$committee, $member]) }}" method="POST"
                                    onsubmit="return confirm('Remove {{ $member->name }} from this committee?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2.5 bg-red-50 dark:bg-red-500/10 text-red-500 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-xl transition-all">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </x-card>
                @empty
                    <x-card class="text-center py-10">
                        <div class="flex flex-col items-center">
                            <div class="p-4 bg-slate-100 dark:bg-slate-800 rounded-full mb-3">
                                <i class="bi bi-person-x text-2xl text-slate-400"></i>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 font-medium">No members found</p>
                            @if (request('search'))
                                <p class="text-xs text-slate-400 mt-1">Try adjusting your search</p>
                            @endif
                        </div>
                    </x-card>
                @endforelse

                {{-- Mobile Pagination --}}
                @if ($members->hasPages())
                    <div class="flex items-center justify-between mt-4">
                        <div class="flex-1 flex justify-start">
                            @if ($members->onFirstPage())
                                <span
                                    class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-100 dark:bg-slate-800 cursor-not-allowed rounded-xl">
                                    <i class="bi bi-chevron-left mr-1"></i> Previous
                                </span>
                            @else
                                <a href="{{ $members->previousPageUrl() }}"
                                    class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                                    <i class="bi bi-chevron-left mr-1"></i> Previous
                                </a>
                            @endif
                        </div>

                        <div class="px-4 text-sm text-slate-600 dark:text-slate-400 font-medium">
                            {{ $members->currentPage() }} / {{ $members->lastPage() }}
                        </div>

                        <div class="flex-1 flex justify-end">
                            @if ($members->hasMorePages())
                                <a href="{{ $members->nextPageUrl() }}"
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
                @endif
            </div>
        </div>

        <!-- Add Member -->
        @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board'))
            <div class="lg:col-span-1">
                <x-card>
                    <x-slot name="header">
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                <i class="bi bi-person-plus-fill text-green-600 dark:text-green-400"></i>
                            </div>
                            <h3 class="font-bold text-lg text-slate-800 dark:text-white">Add Member</h3>
                        </div>
                    </x-slot>

                    <form action="{{ route('committees.assign', $committee) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label class="mb-2">
                                <i class="bi bi-search text-slate-400 mr-1"></i> Search User
                            </x-input-label>
                            <x-text-input type="text" id="userSearch" placeholder="Type to filter..." />
                        </div>
                        <div>
                            <x-input-label class="mb-2">
                                <i class="bi bi-person text-slate-400 mr-1"></i> Select User
                            </x-input-label>
                            <select name="user_id" id="userSelect"
                                class="w-full px-4 py-2.5 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 dark:text-white text-sm transition-all"
                                required size="6">
                                <option value="">Choose a user...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <x-primary-button type="submit" class="w-full justify-center py-3">
                            <i class="bi bi-person-plus mr-1"></i> Add to Committee
                        </x-primary-button>
                    </form>
                </x-card>

                <script>
                    document.getElementById('userSearch').addEventListener('keyup', function() {
                        var searchText = this.value.toLowerCase();
                        var select = document.getElementById('userSelect');
                        var options = select.getElementsByTagName('option');

                        for (var i = 0; i < options.length; i++) {
                            var option = options[i];
                            var text = option.text.toLowerCase();
                            if (option.value === "") {
                                option.style.display = "";
                                continue;
                            }

                            if (text.indexOf(searchText) > -1) {
                                option.style.display = "";
                            } else {
                                option.style.display = "none";
                            }
                        }
                    });
                </script>
            </div>
        @endif
    </div>
@endsection
