<x-app-layout>
    <div class="max-w-2xl mx-auto">
        {{-- Back Link --}}
        <div class="mb-6">
            <x-back-button href="{{ route('users.index') }}" />
        </div>

        <x-card>
            <x-slot name="header">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-brand-blue/10 rounded-xl">
                        <i class="bi bi-person-plus-fill text-brand-blue text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">Create New User</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Add a new user to the system</p>
                    </div>
                </div>
            </x-slot>

            <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
                @csrf

                {{-- Name --}}
                <div>
                    <x-input-label for="name" icon="bi-person">Full Name</x-input-label>
                    <x-text-input type="text" name="name" id="name" class="w-full mt-1"
                        placeholder="Enter full name" value="{{ old('name') }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                </div>

                {{-- Email --}}
                <div>
                    <x-input-label for="email" icon="bi-envelope">Email Address</x-input-label>
                    <x-text-input type="email" name="email" id="email" class="w-full mt-1"
                        placeholder="user@example.com" value="{{ old('email') }}" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                {{-- Password --}}
                <div>
                    <x-input-label for="password" icon="bi-lock">Password</x-input-label>
                    <x-text-input type="password" name="password" id="password" class="w-full mt-1"
                        placeholder="Minimum 8 characters" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>

                {{-- Role & Status Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="role" icon="bi-person-badge">Role</x-input-label>
                        <x-select-input name="role" id="role" class="w-full mt-1" required>
                            <option value="member" {{ old('role') == 'member' ? 'selected' : '' }}>Member</option>
                            <option value="committee_head" {{ old('role') == 'committee_head' ? 'selected' : '' }}>
                                Committee
                                Head</option>
                            <option value="hr" {{ old('role') == 'hr' ? 'selected' : '' }}>HR</option>
                            <option value="board" {{ old('role') == 'board' ? 'selected' : '' }}>Board</option>
                            <option value="top_management" {{ old('role') == 'top_management' ? 'selected' : '' }}>Top
                                Management</option>
                        </x-select-input>
                        <x-input-error :messages="$errors->get('role')" class="mt-1.5" />
                    </div>
                    <div>
                        <x-input-label for="status" icon="bi-toggle-on">Status</x-input-label>
                        <x-select-input name="status" id="status" class="w-full mt-1" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="disabled" {{ old('status') == 'disabled' ? 'selected' : '' }}>Disabled
                            </option>
                        </x-select-input>
                        <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
                    </div>
                </div>

                {{-- Actions --}}
                <div class="pt-4 flex flex-col sm:flex-row gap-3">
                    <x-secondary-button href="{{ route('users.index') }}"
                        class="flex-1 justify-center py-2.5 text-center">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button class="flex-1 justify-center py-2.5">
                        <i class="bi bi-check-lg mr-2"></i> Create User
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
