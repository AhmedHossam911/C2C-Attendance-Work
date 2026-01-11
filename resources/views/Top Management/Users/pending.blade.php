@extends('Common.Layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Pending Approvals</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Review and approve new user registrations</p>
        </div>
        @if (!$users->isEmpty())
            <span
                class="px-4 py-2 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-xl text-sm font-bold flex items-center gap-2">
                <i class="bi bi-hourglass-split"></i> {{ $users->count() }} Pending
                Request{{ $users->count() !== 1 ? 's' : '' }}
            </span>
        @endif
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block">
        <x-card class="p-0 overflow-hidden" :embedded="true">
            @if ($users->isEmpty())
                <div class="p-12 text-center bg-white dark:bg-slate-900">
                    <div class="flex flex-col items-center">
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-full mb-4">
                            <i class="bi bi-check-circle-fill text-3xl text-green-500 dark:text-green-400"></i>
                        </div>
                        <p class="text-slate-800 dark:text-white font-bold text-lg">All Caught Up!</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">No pending users waiting for approval.
                        </p>
                    </div>
                </div>
            @else
                <x-table :headers="['User', 'Email', 'Registered', 'Actions']">
                    @foreach ($users as $user)
                        <x-table.tr>
                            <x-table.td>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->name }}</span>
                                </div>
                            </x-table.td>
                            <x-table.td
                                class="text-slate-600 dark:text-slate-400 font-medium">{{ $user->email }}</x-table.td>
                            <x-table.td class="text-sm text-slate-500 dark:text-slate-400">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-xs font-semibold">
                                    <i class="bi bi-clock"></i>
                                    {{ $user->created_at->diffForHumans() }}
                                </span>
                            </x-table.td>
                            <x-table.td>
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('users.approve', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="px-3.5 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-bold rounded-lg transition-all shadow-sm shadow-green-500/20 flex items-center gap-1.5">
                                            <i class="bi bi-check-lg text-sm"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('users.reject', $user) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to reject this user?');">
                                        @csrf
                                        <button type="submit"
                                            class="px-3.5 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 text-xs font-bold rounded-lg transition-all flex items-center gap-1.5">
                                            <i class="bi bi-x-lg text-sm"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @endforeach
                </x-table>
            @endif
        </x-card>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-4">
        @forelse ($users as $user)
            <x-card class="relative overflow-hidden p-0">
                {{-- Pending indicator --}}
                <div class="absolute top-0 left-0 w-1 h-full bg-amber-500"></div>

                <div class="p-4 pl-5">
                    <div class="flex items-start gap-4 mb-4">
                        <div
                            class="h-12 w-12 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-lg font-bold shadow-md flex-shrink-0">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-slate-800 dark:text-white text-lg">{{ $user->name }}</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400 truncate mb-1">{{ $user->email }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                <i class="bi bi-clock-history"></i> Registered {{ $user->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <form action="{{ route('users.approve', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-green-500/20 flex items-center justify-center gap-2">
                                <i class="bi bi-check-lg text-lg"></i> Approve
                            </button>
                        </form>
                        <form action="{{ route('users.reject', $user) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to reject this user?');">
                            @csrf
                            <button type="submit"
                                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-400 text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-x-lg text-lg"></i> Reject
                            </button>
                        </form>
                    </div>
                </div>
            </x-card>
        @empty
            <x-card class="text-center py-12">
                <div class="flex flex-col items-center">
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-full mb-4">
                        <i class="bi bi-check-circle-fill text-3xl text-green-500 dark:text-green-400"></i>
                    </div>
                    <p class="text-slate-800 dark:text-white font-bold text-lg">All Caught Up!</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">No pending users waiting for approval.</p>
                </div>
            </x-card>
        @endforelse
    </div>
@endsection
