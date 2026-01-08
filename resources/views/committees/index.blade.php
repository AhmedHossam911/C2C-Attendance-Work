@extends('layouts.app')

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
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Committees</h2>
            @if (Auth::user()->hasRole('top_management'))
                <a href="{{ route('committees.create') }}"
                    class="px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white font-semibold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-95">
                    Create Committee
                </a>
            @endif
        </div>

        <!-- Search Card -->
        <x-card class="mb-6">
            <form action="{{ route('committees.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
                <div class="flex-1 relative">
                    <input type="text" name="search"
                        class="w-full pl-4 pr-10 py-2.5 rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                        placeholder="Search Committees..." value="{{ request('search') }}">
                </div>
                <div>
                    <button type="submit"
                        class="w-full md:w-auto px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white font-semibold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-95">
                        Search
                    </button>
                </div>
            </form>
        </x-card>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($committees as $committee)
                <x-card class="h-full flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <h5 class="font-bold text-xl text-slate-800 dark:text-white mb-2">{{ $committee->name }}</h5>
                        <p class="text-slate-500 dark:text-slate-400 mb-4 line-clamp-3">{{ $committee->description }}</p>
                    </div>

                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                {{ $committee->users->count() }} Members
                            </span>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('committees.show', $committee) }}"
                                class="flex-1 text-center px-4 py-2 border-2 border-brand-blue text-brand-blue hover:bg-brand-blue hover:text-white font-semibold rounded-xl transition-all">
                                View
                            </a>
                            @if (Auth::user()->hasRole('top_management'))
                                <button @click="openEditModal({{ $committee }})"
                                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold rounded-xl transition-all">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </x-card>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3">
                    <div
                        class="p-4 rounded-xl bg-sky-50 text-sky-800 border border-sky-200 dark:bg-sky-900/20 dark:text-sky-300 dark:border-sky-800/30">
                        No committees found.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $committees->links() }}
        </div>

        <!-- Edit Modal -->
        <x-modal name="edit-committee" title="Edit Committee">
            <form method="POST" x-bind:action="'{{ route('committees.index') }}/' + editId" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name"
                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Name</label>
                    <input type="text" name="name" id="name" x-model="editName" required
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white">
                </div>

                <div>
                    <label for="description"
                        class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3" x-model="editDescription"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="$dispatch('close-modal', 'edit-committee')"
                        class="px-5 py-2.5 text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white font-semibold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-95">
                        Save Changes
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
@endsection
