<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Report Permissions</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage which roles can view specific reports.
                    Top
                    Management always has full access.</p>
            </div>
        </div>

        <form action="{{ route('report-permissions.update') }}" method="POST" x-data="{ permissions: {{ json_encode($matrix) }} }">
            @csrf

            <x-card class="p-0 overflow-hidden" :embedded="true">
                <!-- Desktop View: Table -->
                <div class="hidden md:block">
                    <x-table>
                        <x-slot name="head">
                            <x-table.th>Report Name</x-table.th>
                            @foreach ($roles as $role)
                                <x-table.th
                                    class="text-center capitalize">{{ str_replace('_', ' ', $role) }}</x-table.th>
                            @endforeach
                        </x-slot>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($reports as $key => $label)
                                <x-table.tr>
                                    <x-table.td class="font-medium text-slate-800 dark:text-slate-200">
                                        {{ $label }}
                                        <div class="text-xs text-slate-400 font-normal mt-0.5 font-mono">
                                            {{ $key }}
                                        </div>
                                    </x-table.td>
                                    @foreach ($roles as $role)
                                        <x-table.td class="text-center">
                                            <div class="relative max-w-xs mx-auto">
                                                <select name="permissions[{{ $key }}][{{ $role }}]"
                                                    x-model="permissions['{{ $key }}']['{{ $role }}']"
                                                    class="block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white py-1.5 pl-3 pr-8 text-xs focus:border-brand-blue focus:outline-none focus:ring-brand-blue sm:text-sm">
                                                    <option value="none">None</option>
                                                    <option value="own">Authorized Only</option>
                                                    <option value="global">Global</option>
                                                </select>
                                            </div>
                                        </x-table.td>
                                    @endforeach
                                </x-table.tr>
                            @endforeach
                        </tbody>
                    </x-table>
                </div>

                <!-- Mobile View: Cards -->
                <div class="md:hidden">
                    @foreach ($reports as $key => $label)
                        <div class="p-4 border-b last:border-0 border-slate-100 dark:border-slate-700">
                            <div class="mb-4">
                                <h3 class="font-bold text-slate-800 dark:text-white">{{ $label }}</h3>
                                <p class="text-xs text-slate-400 font-mono">{{ $key }}</p>
                            </div>

                            <div class="space-y-3">
                                @foreach ($roles as $role)
                                    <div class="flex items-center justify-between gap-4">
                                        <label
                                            class="text-sm font-medium text-slate-600 dark:text-slate-400 capitalize">
                                            {{ str_replace('_', ' ', $role) }}
                                        </label>
                                        <select name="permissions[{{ $key }}][{{ $role }}]"
                                            x-model="permissions['{{ $key }}']['{{ $role }}']"
                                            class="block w-40 rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white py-1.5 pl-2 pr-8 text-xs focus:border-brand-blue focus:outline-none focus:ring-brand-blue">
                                            <option value="none">None</option>
                                            <option value="own">Authorized Only</option>
                                            <option value="global">Global</option>
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Footer Actions -->
                <div
                    class="p-4 md:p-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-3 rounded-b-xl sticky bottom-0 z-10 md:static backdrop-blur-sm md:backdrop-blur-none bg-opacity-90 md:bg-opacity-100">
                    <x-secondary-button type="button" onclick="window.history.back()">
                        Cancel
                    </x-secondary-button>
                    <x-primary-button type="submit">
                        Save Changes
                    </x-primary-button>
                </div>
            </x-card>
        </form>
    </div>
</x-app-layout>
