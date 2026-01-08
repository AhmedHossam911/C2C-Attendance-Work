@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-card>
            <x-slot name="header">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Edit User: {{ $user->name }}</h3>
            </x-slot>

            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Name</label>
                    <input type="text" name="name"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                        value="{{ $user->name }}" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                    <input type="email" name="email"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                        value="{{ $user->email }}" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password (Leave blank
                        to keep current)</label>
                    <input type="password" name="password"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Role</label>
                    <select name="role"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                        required>
                        <option value="member" {{ $user->role == 'member' ? 'selected' : '' }}>Member</option>
                        <option value="hr" {{ $user->role == 'hr' ? 'selected' : '' }}>HR</option>
                        <option value="board" {{ $user->role == 'board' ? 'selected' : '' }}>Board</option>
                        <option value="top_management" {{ $user->role == 'top_management' ? 'selected' : '' }}>Top
                            Management</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
                    <select name="status"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                        required>
                        <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ $user->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="disabled" {{ $user->status == 'disabled' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white font-bold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-95">Update
                        User</button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
