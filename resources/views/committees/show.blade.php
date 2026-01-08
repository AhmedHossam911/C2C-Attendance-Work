@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $committee->name }}</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-1">{{ $committee->description }}</p>
            </div>
            <div>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-brand-blue/10 text-brand-blue dark:bg-brand-blue/20 dark:text-brand-blue-light">
                    Total Members: {{ $committee->users->count() }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Members List -->
        <div class="lg:col-span-2">
            <x-card class="p-0" :embedded="true">
                <x-slot name="header">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">Members</h3>
                        <form action="{{ route('committees.show', $committee) }}" method="GET"
                            class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative w-full sm:w-64">
                                <input type="text" name="search"
                                    class="w-full pl-4 pr-10 py-2 rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-sm focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                                    placeholder="Search Members..." value="{{ request('search') }}">
                                <button type="submit"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand-blue transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </x-slot>

                <x-table :headers="[
                    'No',
                    'Name',
                    'Email',
                    Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board') ? 'Actions' : '',
                ]">
                    @forelse ($members as $member)
                        <x-table.tr>
                            <x-table.td>{{ $loop->iteration }}</x-table.td>
                            <x-table.td class="font-bold text-slate-800 dark:text-white">{{ $member->name }}</x-table.td>
                            <x-table.td>{{ $member->email }}</x-table.td>
                            @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board'))
                                <x-table.td>
                                    <form action="{{ route('committees.remove', [$committee, $member]) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to remove this member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold transition-colors">
                                            Remove
                                        </button>
                                    </form>
                                </x-table.td>
                            @endif
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400 italic">No
                                members found.</td>
                        </x-table.tr>
                    @endforelse
                </x-table>

                @if ($members->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $members->links() }}
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Add Member -->
        @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board'))
            <div class="lg:col-span-1">
                <x-card>
                    <x-slot name="header">
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">Add Member</h3>
                    </x-slot>

                    <form action="{{ route('committees.assign', $committee) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Search
                                User</label>
                            <input type="text" id="userSearch"
                                class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white text-sm"
                                placeholder="Type to filter...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Select
                                User</label>
                            <select name="user_id" id="userSelect"
                                class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white text-sm"
                                required size="5">
                                <option value="">Choose...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="w-full px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white font-semibold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-95">
                            Add to Committee
                        </button>
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
                            // Keep the placeholder option always visible or handle appropriately (usually skipped or matched if empty)
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
