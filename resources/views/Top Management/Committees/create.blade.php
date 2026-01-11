<x-app-layout>
    <div class="max-w-2xl mx-auto">
        {{-- Back Link --}}
        <div class="mb-6">
            <x-back-button href="{{ route('committees.index') }}" />
        </div>

        <x-card>
            <x-slot name="header">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-brand-blue/10 rounded-xl">
                        <i class="bi bi-people-fill text-brand-blue text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">Create New Committee</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Add a new committee to the organization</p>
                    </div>
                </div>
            </x-slot>

            <form method="POST" action="{{ route('committees.store') }}" class="space-y-5">
                @csrf

                {{-- Name --}}
                <div>
                    <x-input-label for="name" class="mb-2">
                        <i class="bi bi-tag text-slate-400 mr-1"></i> Committee Name
                    </x-input-label>
                    <x-text-input type="text" name="name" id="name" value="{{ old('name') }}" required
                        placeholder="e.g., Technical Committee" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                </div>

                {{-- Description --}}
                <div>
                    <x-input-label for="description" class="mb-2">
                        <i class="bi bi-card-text text-slate-400 mr-1"></i> Description
                    </x-input-label>
                    <x-textarea name="description" id="description" rows="4"
                        placeholder="Describe the committee's purpose and responsibilities...">{{ old('description') }}</x-textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
                </div>

                {{-- Actions --}}
                <div
                    class="pt-4 flex flex-col-reverse sm:flex-row gap-3 border-t border-slate-100 dark:border-slate-700">
                    <x-secondary-button href="{{ route('committees.index') }}" class="flex-1 justify-center py-3">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button type="submit" class="flex-1 justify-center py-3">
                        <i class="bi bi-check-lg mr-1"></i> Create Committee
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
