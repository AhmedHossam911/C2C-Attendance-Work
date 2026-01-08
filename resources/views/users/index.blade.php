@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Users Management</h2>
        <a href="{{ route('users.create') }}"
            class="px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white text-sm font-semibold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-95">
            Create User
        </a>
    </div>

    <!-- Search & Filter Card -->
    <x-card class="mb-6">
        <form action="{{ route('users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-1">
                <input type="text" name="search"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                    placeholder="Search by Name or Email" value="{{ request('search') }}">
            </div>
            <div>
                <select name="role"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white">
                    <option value="">All Roles</option>
                    <option value="board" {{ request('role') == 'board' ? 'selected' : '' }}>Board</option>
                    <option value="hr" {{ request('role') == 'hr' ? 'selected' : '' }}>HR</option>
                    <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Member</option>
                </select>
            </div>
            <div>
                <select name="status"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-semibold rounded-xl transition-colors">
                    Filter
                </button>
                <a href="{{ route('users.index') }}"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold rounded-xl transition-colors flex items-center justify-center">
                    Reset
                </a>
            </div>
        </form>
    </x-card>

    <!-- Users Table Card -->
    <x-card class="p-0" :embedded="true">
        <x-table :headers="['ID', 'Name', 'Email', 'Role', 'Status', 'Actions']">
            @forelse ($users as $user)
                <x-table.tr>
                    <x-table.td>{{ $user->id }}</x-table.td>
                    <x-table.td>
                        <div class="font-bold text-slate-800 dark:text-slate-200">{{ $user->name }}</div>
                    </x-table.td>
                    <x-table.td>{{ $user->email }}</x-table.td>
                    <x-table.td>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </x-table.td>
                    <x-table.td>
                        @php
                            $statusClasses = match ($user->status) {
                                'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'disabled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                default => 'bg-slate-100 text-slate-800',
                            };
                        @endphp
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </x-table.td>
                    <x-table.td>
                        <a href="{{ route('users.edit', $user) }}"
                            class="text-brand-blue hover:text-brand-blue/80 font-medium text-sm">
                            Edit
                        </a>
                    </x-table.td>
                </x-table.tr>
            @empty
                <x-table.tr>
                    <x-table.td colspan="6" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center text-slate-500">
                            <i class="bi bi-people text-4xl mb-2 opacity-50"></i>
                            <p>No users found matching your criteria.</p>
                        </div>
                    </x-table.td>
                </x-table.tr>
            @endforelse
        </x-table>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $users->links() }}
            </div>
        @endif
    </x-card>
