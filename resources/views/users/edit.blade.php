@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        {{-- Back Link --}}
        <div class="mb-4">
            <a href="{{ route('users.index') }}"
                class="inline-flex items-center text-sm text-slate-500 hover:text-brand-blue transition-colors">
                <i class="bi bi-arrow-left mr-2"></i> Back to Users
            </a>
        </div>

        <x-card>
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-12 w-12 rounded-full bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white text-lg font-bold shadow-md">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-slate-800 dark:text-white">Edit User</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                        </div>
                    </div>
                    @php
                        $statusClasses = match ($user->status) {
                            'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                            'disabled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $statusClasses }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </x-slot>

            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div>
                    <x-input-label for="name" icon="bi-person">Full Name</x-input-label>
                    <x-text-input type="text" name="name" id="name" class="w-full mt-1"
                        value="{{ old('name', $user->name) }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                </div>

                {{-- Email --}}
                <div>
                    <x-input-label for="email" icon="bi-envelope">Email Address</x-input-label>
                    <x-text-input type="email" name="email" id="email" class="w-full mt-1"
                        value="{{ old('email', $user->email) }}" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                {{-- Password --}}
                <div>
                    <x-input-label for="password" icon="bi-lock">
                        New Password <span class="font-normal text-slate-400 ml-1">(leave blank to keep current)</span>
                    </x-input-label>
                    <x-text-input type="password" name="password" id="password" class="w-full mt-1"
                        placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>

                {{-- Role & Status Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="role" icon="bi-person-badge">Role</x-input-label>
                        <x-select-input name="role" id="role" class="w-full mt-1" required>
                            <option value="member" {{ old('role', $user->role) == 'member' ? 'selected' : '' }}>Member
                            </option>
                            <option value="committee_head"
                                {{ old('role', $user->role) == 'committee_head' ? 'selected' : '' }}>Committee Head</option>
                            <option value="hr" {{ old('role', $user->role) == 'hr' ? 'selected' : '' }}>HR</option>
                            <option value="board" {{ old('role', $user->role) == 'board' ? 'selected' : '' }}>Board
                            </option>
                            <option value="top_management"
                                {{ old('role', $user->role) == 'top_management' ? 'selected' : '' }}>Top Management
                            </option>
                        </x-select-input>
                        <x-input-error :messages="$errors->get('role')" class="mt-1.5" />
                    </div>
                    <div>
                        <x-input-label for="status" icon="bi-toggle-on">Status</x-input-label>
                        <x-select-input name="status" id="status" class="w-full mt-1" required>
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="pending" {{ old('status', $user->status) == 'pending' ? 'selected' : '' }}>
                                Pending</option>
                            <option value="disabled" {{ old('status', $user->status) == 'disabled' ? 'selected' : '' }}>
                                Disabled</option>
                        </x-select-input>
                        <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
                    </div>
                </div>

                {{-- Actions --}}
                <div class="pt-4 flex flex-col sm:flex-row gap-3">
                    <x-secondary-button href="{{ route('users.index') }}" class="flex-1 justify-center py-2.5 text-center">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button class="flex-1 justify-center py-2.5">
                        <i class="bi bi-check-lg mr-2"></i> Save Changes
                    </x-primary-button>
                </div>
            </form>
        </x-card>

        {{-- User Info Card --}}
        <div class="mt-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                <i class="bi bi-info-circle"></i>
                <div>
                    <span>User ID: <strong class="text-slate-700 dark:text-slate-300">{{ $user->id }}</strong></span>
                    <span class="mx-2">•</span>
                    <span>Created: <strong
                            class="text-slate-700 dark:text-slate-300">{{ $user->created_at->format('M d, Y') }}</strong></span>
                </div>
            </div>
        </div>
    </div>
@endsection
