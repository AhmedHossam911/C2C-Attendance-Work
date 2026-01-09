@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('tasks.show', $task) }}"
                class="h-10 w-10 rounded-xl bg-slate-300 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Edit Task</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Update task details.</p>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="bg-slate-300 dark:bg-slate-800 rounded-3xl p-8 shadow-lg border border-slate-200 dark:border-slate-700">
            <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div class="space-y-2">
                    <x-input-label for="title" value="Task Title" class="!text-lg" />
                    <x-text-input id="title" class="block w-full text-lg py-3" type="text" name="title"
                        :value="old('title', $task->title)" required autofocus />
                    <x-input-error :messages="$errors->get('title')" />
                </div>

                {{-- Grid: Committee & Type --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <x-input-label for="committee_id" value="Assign To Committee" />
                        <x-select-input id="committee_id" name="committee_id" class="block w-full py-2.5" required>
                            @foreach ($committees as $committee)
                                <option value="{{ $committee->id }}"
                                    {{ old('committee_id', $task->committee_id) == $committee->id ? 'selected' : '' }}>
                                    {{ $committee->name }}
                                </option>
                            @endforeach
                        </x-select-input>
                        <x-input-error :messages="$errors->get('committee_id')" />
                    </div>

                    <div class="space-y-2">
                        <x-input-label for="type" value="Task Type" />
                        <div class="flex gap-4">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="type" value="basic" class="peer sr-only"
                                    {{ old('type', $task->type) == 'basic' ? 'checked' : '' }}>
                                <div
                                    class="text-center px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 peer-checked:border-brand-blue peer-checked:bg-brand-blue/5 peer-checked:text-brand-blue transition-all">
                                    <div class="font-bold">Basic</div>
                                    <div class="text-xs text-slate-500">Regular task</div>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="type" value="extra" class="peer sr-only"
                                    {{ old('type', $task->type) == 'extra' ? 'checked' : '' }}>
                                <div
                                    class="text-center px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 peer-checked:border-brand-teal peer-checked:bg-brand-teal/5 peer-checked:text-brand-teal transition-all">
                                    <div class="font-bold">Extra</div>
                                    <div class="text-xs text-slate-500">Bonus points</div>
                                </div>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('type')" />
                    </div>
                </div>

                {{-- Grid: Session & Deadline --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <x-input-label for="session_id" value="Link to Session (Optional)" />
                        <x-select-input id="session_id" name="session_id" class="block w-full py-2.5">
                            <option value="">No Session Link</option>
                            @foreach ($sessions as $session)
                                <option value="{{ $session->id }}"
                                    {{ old('session_id', $task->session_id) == $session->id ? 'selected' : '' }}>
                                    {{ $session->title }} ({{ $session->created_at->format('M d') }})
                                </option>
                            @endforeach
                        </x-select-input>
                    </div>

                    <div class="space-y-2">
                        <x-input-label for="deadline" value="Deadline" />
                        <x-text-input id="deadline" class="block w-full py-2.5" type="datetime-local" name="deadline"
                            :value="old('deadline', $task->deadline->format('Y-m-d\TH:i'))" required />
                        <x-input-error :messages="$errors->get('deadline')" />
                    </div>
                </div>

                {{-- Description --}}
                <div class="space-y-2">
                    <x-input-label for="description" value="Description / Instructions" />
                    <x-textarea id="description" name="description" rows="6" class="w-full"
                        placeholder="Detailed instructions for the task...">{{ old('description', $task->description) }}</x-textarea>
                    <x-input-error :messages="$errors->get('description')" />
                </div>

                {{-- Actions --}}
                <div class="flex justify-end pt-6 border-t border-slate-200 dark:border-slate-700">
                    <x-primary-button class="px-8 py-3 text-base">
                        Save Changes <i class="bi bi-check-lg ml-2"></i>
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection
