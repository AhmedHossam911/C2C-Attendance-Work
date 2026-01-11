@extends('Common.Layouts.app')

@section('content')
    <div x-data="{
        editId: null,
        editName: '',
        editDescription: '',
        openEditModal(committee) {
            this.editId = committee.id;
            this.editName = committee.name;
            this.editDescription = committee.description;
            $dispatch('open-modal', 'edit-committee');
        }
    }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Committees</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage organization committees and members</p>
            </div>
            @if (Auth::user()->hasRole('top_management'))
                <x-primary-button href="{{ route('committees.create') }}">
                    <i class="bi bi-plus-lg mr-1"></i> Create Committee
                </x-primary-button>
            @endif
        </div>

        <!-- Search Card -->
        <x-card class="mb-6">
            <form action="{{ route('committees.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="bi bi-search"></i>
                    </div>
                    <x-text-input type="text" name="search" class="pl-11" placeholder="Search committees by name..."
                        value="{{ request('search') }}" />
                </div>
                <div class="flex gap-2">
                    <x-primary-button type="submit" class="flex-1 sm:flex-none justify-center">
                        <i class="bi bi-funnel mr-1 sm:mr-0"></i> <span class="sm:hidden">Search</span>
                    </x-primary-button>
                    @if (request('search'))
                        <x-secondary-button href="{{ route('committees.index') }}" class="justify-center">
                            <i class="bi bi-x-circle"></i>
                        </x-secondary-button>
                    @endif
                </div>
            </form>
        </x-card>

        <!-- Committees Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($committees as $committee)
                <x-card
                    class="h-full flex flex-col justify-between hover:shadow-lg hover:shadow-brand-blue/5 transition-all duration-300 group">
                    <div>
                        {{-- Committee Icon & Name --}}
                        <div class="flex items-start gap-3 mb-3">
                            <div
                                class="p-2.5 bg-gradient-to-br from-brand-blue/10 to-brand-teal/10 rounded-xl group-hover:from-brand-blue/20 group-hover:to-brand-teal/20 transition-colors">
                                <i class="bi bi-people-fill text-brand-blue text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h5 class="font-bold text-lg text-slate-800 dark:text-white truncate">{{ $committee->name }}
                                </h5>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 mt-1">
                                    <i class="bi bi-person-fill text-[10px] mr-1"></i> {{ $committee->users->count() }}
                                    Members
                                </span>
                            </div>
                        </div>

                        {{-- Description --}}
                        @if ($committee->description)
                            <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 mb-4">
                                {{ $committee->description }}</p>
                        @else
                            <p class="text-sm text-slate-400 dark:text-slate-500 italic mb-4">No description</p>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                        <x-primary-button href="{{ route('committees.show', $committee) }}"
                            class="flex-1 justify-center py-2.5 text-sm">
                            <i class="bi bi-eye mr-1"></i> View Details
                        </x-primary-button>
                        @if (Auth::user()->hasRole('top_management'))
                            <x-secondary-button @click="openEditModal({{ $committee }})" class="py-2.5"
                                title="Edit Committee">
                                <i class="bi bi-pencil-square"></i>
                            </x-secondary-button>
                        @endif
                    </div>
                </x-card>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3">
                    <x-card class="text-center py-12">
                        <div class="flex flex-col items-center">
                            <div class="p-4 bg-slate-100 dark:bg-slate-800 rounded-full mb-4">
                                <i class="bi bi-people text-3xl text-slate-400"></i>
                            </div>
                            <p class="text-slate-600 dark:text-slate-400 font-medium">No committees found</p>
                            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">
                                @if (request('search'))
                                    Try adjusting your search query
                                @else
                                    Create your first committee to get started
                                @endif
                            </p>
                            @if (Auth::user()->hasRole('top_management') && !request('search'))
                                <x-primary-button href="{{ route('committees.create') }}" class="mt-4">
                                    <i class="bi bi-plus-lg mr-1"></i> Create Committee
                                </x-primary-button>
                            @endif
                        </div>
                    </x-card>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($committees->hasPages())
            <div class="mt-6">
                {{-- Desktop Pagination --}}
                <div class="hidden sm:flex items-center justify-between">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Showing <span
                            class="font-semibold text-slate-800 dark:text-slate-200">{{ $committees->firstItem() }}</span>
                        to <span
                            class="font-semibold text-slate-800 dark:text-slate-200">{{ $committees->lastItem() }}</span>
                        of <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $committees->total() }}</span>
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($committees->onFirstPage())
                            <span
                                class="inline-flex items-center justify-center w-10 h-10 text-slate-400 bg-slate-200 dark:bg-slate-800 cursor-not-allowed rounded-xl">
                                <i class="bi bi-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $committees->previousPageUrl() }}"
                                class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 bg-slate-300 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-700 hover:text-brand-blue transition-all">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        @endif

                        @foreach ($committees->getUrlRange(1, $committees->lastPage()) as $page => $url)
                            @if ($page == $committees->currentPage())
                                <span
                                    class="inline-flex items-center justify-center w-10 h-10 text-white font-bold bg-brand-blue rounded-xl shadow-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 font-medium bg-slate-300 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-700 hover:text-brand-blue transition-all">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if ($committees->hasMorePages())
                            <a href="{{ $committees->nextPageUrl() }}"
                                class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 bg-slate-300 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-700 hover:text-brand-blue transition-all">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        @else
                            <span
                                class="inline-flex items-center justify-center w-10 h-10 text-slate-400 bg-slate-200 dark:bg-slate-800 cursor-not-allowed rounded-xl">
                                <i class="bi bi-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Mobile Pagination --}}
                <div class="sm:hidden flex items-center justify-between">
                    <div class="flex-1 flex justify-start">
                        @if ($committees->onFirstPage())
                            <span
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-200 dark:bg-slate-800 cursor-not-allowed rounded-xl">
                                <i class="bi bi-chevron-left mr-1"></i> Previous
                            </span>
                        @else
                            <a href="{{ $committees->previousPageUrl() }}"
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-300 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-700 transition-all">
                                <i class="bi bi-chevron-left mr-1"></i> Previous
                            </a>
                        @endif
                    </div>

                    <div class="px-4 text-sm text-slate-600 dark:text-slate-400 font-medium">
                        {{ $committees->currentPage() }} / {{ $committees->lastPage() }}
                    </div>

                    <div class="flex-1 flex justify-end">
                        @if ($committees->hasMorePages())
                            <a href="{{ $committees->nextPageUrl() }}"
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-300 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-700 transition-all">
                                Next <i class="bi bi-chevron-right ml-1"></i>
                            </a>
                        @else
                            <span
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-200 dark:bg-slate-800 cursor-not-allowed rounded-xl">
                                Next <i class="bi bi-chevron-right ml-1"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Edit Modal -->
        <x-modal name="edit-committee" title="Edit Committee">
            <form method="POST" x-bind:action="'{{ route('committees.index') }}/' + editId" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="name" value="Committee Name" />
                    <x-text-input type="text" name="name" id="name" x-model="editName" required class="mt-1" />
                </div>

                <div>
                    <x-input-label for="description" value="Description" />
                    <x-textarea name="description" id="description" rows="3" x-model="editDescription"
                        class="mt-1" placeholder="Enter committee description..." />
                </div>

                <div
                    class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <x-secondary-button type="button" @click="$dispatch('close-modal', 'edit-committee')"
                        class="justify-center">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button type="submit" class="justify-center">
                        <i class="bi bi-check-lg mr-1"></i> Save Changes
                    </x-primary-button>
                </div>
            </form>
        </x-modal>
    </div>
@endsection
