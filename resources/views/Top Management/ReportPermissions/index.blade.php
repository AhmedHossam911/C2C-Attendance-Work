@extends('Common.Layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Report Permissions</h1>
                <div class="text-sm text-slate-500 mt-1">
                    Manage which roles can view specific reports. Top Management always has full access.
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <form action="{{ route('report-permissions.update') }}" method="POST" x-data="{ permissions: {{ json_encode($matrix) }} }">
                @csrf

                <!-- Desktop View: Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-300">Report Name</th>
                                @foreach ($roles as $role)
                                    <th
                                        class="p-4 font-semibold text-slate-600 dark:text-slate-300 text-center capitalize text-sm">
                                        {{ str_replace('_', ' ', $role) }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($reports as $key => $label)
                                <tr>
                                    <td class="p-4 font-medium text-slate-800 dark:text-slate-200">
                                        {{ $label }}
                                        <div class="text-xs text-slate-400 font-normal mt-0.5 font-mono">{{ $key }}
                                        </div>
                                    </td>
                                    @foreach ($roles as $role)
                                        <td class="p-4 text-center">
                                            <div class="relative">
                                                <select name="permissions[{{ $key }}][{{ $role }}]"
                                                    x-model="permissions['{{ $key }}']['{{ $role }}']"
                                                    class="block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white py-1.5 pl-3 pr-8 text-xs focus:border-brand-blue focus:outline-none focus:ring-brand-blue sm:text-sm">
                                                    <option value="none">None</option>
                                                    <option value="own">Authorized Only</option>
                                                    <option value="global">Global</option>
                                                </select>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile View: Cards -->
                <div class="md:hidden space-y-4 p-4 bg-slate-50 dark:bg-slate-900/50">
                    @foreach ($reports as $key => $label)
                        <div
                            class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                            <div class="mb-4">
                                <h3 class="font-bold text-slate-800 dark:text-white">{{ $label }}</h3>
                                <p class="text-xs text-slate-400 font-mono">{{ $key }}</p>
                            </div>

                            <div class="space-y-3">
                                @foreach ($roles as $role)
                                    <div class="flex items-center justify-between gap-4">
                                        <label class="text-sm font-medium text-slate-600 dark:text-slate-400 capitalize">
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
                    class="p-4 md:p-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex justify-end sticky bottom-0 z-10 md:static backdrop-blur-sm md:backdrop-blur-none bg-opacity-90 md:bg-opacity-100">
                    <button type="button" onclick="window.history.back()"
                        class="mr-3 px-4 py-2 md:px-6 md:py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors font-medium text-sm md:text-base">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 md:px-6 md:py-2.5 rounded-lg bg-gradient-to-r from-brand-blue to-brand-teal text-white font-bold hover:shadow-lg transition-all text-sm md:text-base">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
