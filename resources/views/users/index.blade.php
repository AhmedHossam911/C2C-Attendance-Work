@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Users Management</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage system users and their roles</p>
        </div>
        <x-primary-button href="{{ route('users.create') }}" class="flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Create User
        </x-primary-button>
    </div>

    <!-- Search & Filter Card -->
    <x-card class="mb-6">
        <form action="{{ route('users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <x-input-label for="search" value="Search" class="mb-1.5" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-slate-400"></i>
                    </div>
                    <x-text-input type="text" name="search" id="search" class="pl-10 w-full"
                        placeholder="Name or Email..." value="{{ request('search') }}" />
                </div>
            </div>
            <div>
                <x-input-label for="role" value="Role" class="mb-1.5" />
                <x-select-input name="role" id="role" class="w-full">
                    <option value="">All Roles</option>
                    <option value="board" {{ request('role') == 'board' ? 'selected' : '' }}>Board</option>
                    <option value="hr" {{ request('role') == 'hr' ? 'selected' : '' }}>HR</option>
                    <option value="committee_head" {{ request('role') == 'committee_head' ? 'selected' : '' }}>Committee
                        Head</option>
                    <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Member</option>
                </x-select-input>
            </div>
            <div>
                <x-input-label for="status" value="Status" class="mb-1.5" />
                <x-select-input name="status" id="status" class="w-full">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                </x-select-input>
            </div>
            <div class="flex items-end gap-2">
                <x-primary-button type="submit" class="flex-1 justify-center py-2.5">
                    <i class="bi bi-funnel mr-1"></i> Filter
                </x-primary-button>
                <x-secondary-button href="{{ route('users.index') }}" class="justify-center py-2.5" title="Clear Filters">
                    <i class="bi bi-x-lg"></i>
                </x-secondary-button>
            </div>
        </form>
    </x-card>

    <!-- Desktop Table -->
    <div class="hidden md:block">
        <x-card class="p-0 overflow-hidden" :embedded="true">
            <x-table :headers="['User', 'Email', 'Role', 'Status', 'Actions']">
                @forelse ($users as $user)
                    <x-table.tr>
                        <x-table.td>
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 rounded-full bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 dark:text-slate-200">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-400 font-mono">ID: {{ $user->id }}</div>
                                </div>
                            </div>
                        </x-table.td>
                        <x-table.td class="text-slate-600 dark:text-slate-400 font-medium">{{ $user->email }}</x-table.td>
                        <x-table.td>
                            @php
                                $roleClasses = match ($user->role) {
                                    'top_management'
                                        => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 border border-purple-200 dark:border-purple-800',
                                    'board'
                                        => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800',
                                    'hr'
                                        => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-800',
                                    'committee_head'
                                        => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800',
                                    default
                                        => 'bg-slate-400 text-slate-800 dark:bg-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700',
                                };
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $roleClasses }}">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </x-table.td>
                        <x-table.td>
                            @php
                                $statusClasses = match ($user->status) {
                                    'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    'disabled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    default => 'bg-slate-400 text-slate-700',
                                };
                                $statusIcon = match ($user->status) {
                                    'active' => 'bi-check-circle-fill',
                                    'pending' => 'bi-clock-fill',
                                    'disabled' => 'bi-x-circle-fill',
                                    default => 'bi-circle',
                                };
                            @endphp
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $statusClasses }}">
                                <i class="bi {{ $statusIcon }}"></i>
                                {{ ucfirst($user->status) }}
                            </span>
                        </x-table.td>
                        <x-table.td>
                            <a href="{{ route('users.edit', $user) }}"
                                class="p-2 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors inline-flex items-center justify-center"
                                title="Edit User">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr>
                        <x-table.td colspan="5" class="text-center py-12">
                            <div class="flex flex-col items-center justify-center">
                                <i class="bi bi-people text-4xl text-slate-300 dark:text-slate-600 mb-3"></i>
                                <p class="text-slate-500 dark:text-slate-400 font-medium">No users found</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Try adjusting your filters</p>
                            </div>
                        </x-table.td>
                    </x-table.tr>
                @endforelse
            </x-table>

            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $users->links('components.pagination') }}
                </div>
            @endif
        </x-card>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
        @forelse ($users as $user)
            <x-card class="relative overflow-hidden p-0">
                {{-- Status indicator strip --}}
                @php
                    $stripColor = match ($user->status) {
                        'active' => 'bg-green-500',
                        'pending' => 'bg-amber-500',
                        'disabled' => 'bg-red-500',
                        default => 'bg-slate-400',
                    };
                @endphp
                <div class="absolute top-0 left-0 w-1 h-full {{ $stripColor }}"></div>

                <div class="p-4 pl-5">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-12 w-12 rounded-full bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white text-lg font-bold shadow-md shrink-0">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white text-base">{{ $user->name }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium truncate max-w-[180px]">
                                    {{ $user->email }}
                                </p>
                            </div>
                        </div>

                        @php
                            $statusClasses = match ($user->status) {
                                'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                'disabled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                default => 'bg-slate-400 text-slate-700',
                            };
                        @endphp
                        <span
                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClasses }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                        @php
                            $roleClasses = match ($user->role) {
                                'top_management'
                                    => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                'board' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'hr' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400',
                                'committee_head'
                                    => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400',
                                default => 'bg-slate-400 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $roleClasses }}">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>

                        <a href="{{ route('users.edit', $user) }}"
                            class="px-3 py-1.5 bg-brand-blue/10 text-brand-blue hover:bg-brand-blue/20 dark:bg-brand-blue/20 dark:hover:bg-brand-blue/30 rounded-lg text-xs font-bold transition-colors flex items-center gap-1.5">
                            <i class="bi bi-pencil-fill"></i> Edit
                        </a>
                    </div>
                </div>
            </x-card>
        @empty
            <x-card class="text-center py-12 text-slate-500">
                <i class="bi bi-people text-4xl mb-3 opacity-50 inline-block"></i>
                <p>No users found.</p>
                <p class="text-xs opacity-70 mt-1">Try adjusting your filters.</p>
            </x-card>
        @endforelse

        @if ($users->hasPages())
            <div class="mt-4">
                {{ $users->links('components.pagination') }}
            </div>
        @endif
    </div>
@endsection
