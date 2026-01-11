@extends('Common.Layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Report Permissions</h1>
            <div class="text-sm text-slate-500">
                Manage which roles can view specific reports. Top Management always has full access.
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <form action="{{ route('report-permissions.update') }}" method="POST">
                @csrf

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-300">Report Name</th>
                                @foreach ($roles as $role)
                                    <th class="p-4 font-semibold text-slate-600 dark:text-slate-300 text-center capitalize">
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
                                                    class="block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white py-1.5 pl-3 pr-8 text-xs focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                                                    <option value="none"
                                                        {{ $matrix[$key][$role] === 'none' ? 'selected' : '' }}>
                                                        None</option>
                                                    <option value="own"
                                                        {{ $matrix[$key][$role] === 'own' ? 'selected' : '' }}>
                                                        Authorized Only</option>
                                                    <option value="global"
                                                        {{ $matrix[$key][$role] === 'global' ? 'selected' : '' }}>Global
                                                    </option>
                                                </select>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div
                    class="p-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex justify-end">
                    <button type="button" onclick="window.history.back()"
                        class="mr-4 px-6 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold hover:shadow-lg hover:to-indigo-500 transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
