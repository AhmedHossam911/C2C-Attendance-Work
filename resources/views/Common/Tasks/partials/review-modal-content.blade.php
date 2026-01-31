<div class="p-6">
    <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
        Review Submission
    </h2>

    <form method="POST" x-bind:action="'/submissions/' + submissionId" class="space-y-4">
        @csrf
        @method('PATCH')
        {{-- Status --}}
        <div>
            <x-input-label for="status" value="Status" />
            <select name="status" x-model="status"
                class="w-full mt-1 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-300 focus:border-brand-blue focus:ring-brand-blue rounded-xl shadow-sm">
                <option value="reviewed">Approved</option>
                <option value="pending">Pending</option>
            </select>
        </div>

        {{-- Rating --}}
        <div>
            <x-input-label for="rating" value="Rating (0-10)" />
            <x-text-input type="number" name="rating" x-model="rating" min="0" max="10" step="0.1"
                class="mt-1 block w-full" />
        </div>

        {{-- Feedback --}}
        <div>
            <x-input-label for="feedback" value="Feedback" />
            <textarea name="feedback" x-model="feedback" rows="4"
                class="mt-1 block w-full border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-300 focus:border-brand-blue focus:ring-brand-blue rounded-xl shadow-sm"
                placeholder="Provide feedback for the member..."></textarea>
        </div>

        <div class="pt-4 flex justify-end gap-3">
            <button type="button" @click="showReviewModal = false"
                class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                Cancel
            </button>

            <button type="submit"
                class="px-4 py-2 bg-brand-blue text-white rounded-xl font-bold hover:bg-brand-blue/90 transition-colors shadow-md">
                Save Review
            </button>
        </div>
    </form>
</div>
