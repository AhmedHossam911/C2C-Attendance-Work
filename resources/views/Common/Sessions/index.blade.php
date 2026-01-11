<x-app-layout>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Attendance Sessions</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Manage and track attendance sessions</p>
        </div>
        @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr', 'committee_head', 'vice_head']))
            <x-primary-button href="{{ route('sessions.create') }}"
                class="flex items-center gap-2 shadow-lg shadow-brand-blue/20">
                <i class="bi bi-plus-lg"></i>
                <span>Create Session</span>
            </x-primary-button>
        @endif
    </div>

    <!-- Filter Card (HR does NOT see this if they only have 1 committee, but for now we keep it conditional or general) -->
    <!-- User said: "without filter options for HR becanuse he manage one authed session" -->
    @if (!Auth::user()->hasRole('hr'))
        <x-card class="mb-8 border-none ring-1 ring-slate-200/50 dark:ring-slate-700/50">
            <x-slot name="header">
                <h3 class="font-bold text-base text-slate-800 dark:text-white flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-brand-blue/10 text-brand-blue">
                        <i class="bi bi-funnel-fill"></i>
                    </div>
                    Filter Options
                </h3>
            </x-slot>
            <form action="{{ route('sessions.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                <div>
                    <x-input-label for="committee_id" value="Committee" class="mb-1.5" />
                    <x-select-input name="committee_id" id="committee_id" class="w-full">
                        <option value="">All Committees</option>
                        @foreach ($committees as $committee)
                            <option value="{{ $committee->id }}"
                                {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                                {{ $committee->name }}
                            </option>
                        @endforeach
                    </x-select-input>
                </div>
                <div>
                    <x-input-label for="status" value="Status" class="mb-1.5" />
                    <x-select-input name="status" id="status" class="w-full">
                        <option value="">All Statuses</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </x-select-input>
                </div>
                <div>
                    <x-input-label for="date_from" value="From Date" class="mb-1.5" />
                    <x-text-input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                        class="w-full" />
                </div>
                <div>
                    <x-input-label for="date_to" value="To Date" class="mb-1.5" />
                    <x-text-input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                        class="w-full" />
                </div>
                <div class="flex items-end gap-3">
                    <x-primary-button type="submit"
                        class="flex-1 justify-center py-2.5 shadow-md shadow-brand-blue/10">
                        <i class="bi bi-search"></i>
                    </x-primary-button>
                    <x-secondary-button href="{{ route('sessions.index') }}" class="flex-1 justify-center py-2.5">
                        <i class="bi bi-x-lg"></i>
                    </x-secondary-button>
                </div>
            </form>
        </x-card>
    @endif

    <div class="space-y-6">
        <!-- Desktop Table -->
        <div class="hidden md:block">
            <x-card class="p-0 overflow-hidden" :embedded="true">
                <x-table>
                    <x-slot name="head">
                        <x-table.th class="w-3/12">Title</x-table.th>
                        <x-table.th class="w-2/12">Committee</x-table.th>
                        <x-table.th class="w-1/12">Status</x-table.th>
                        <x-table.th class="w-1/12">Threshold</x-table.th>
                        <x-table.th class="w-1/12">Attendance</x-table.th>
                        <x-table.th class="w-2/12">Created By</x-table.th>
                        <x-table.th class="w-1/12">Date</x-table.th>
                        <x-table.th class="w-1/12 text-right">Actions</x-table.th>
                    </x-slot>
                    @forelse ($sessions as $session)
                        <x-table.tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <x-table.td>
                                <span
                                    class="font-bold text-slate-800 dark:text-slate-200 block text-wrap leading-tight">{{ $session->title }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                    {{ $session->committee->name ?? 'General' }}
                                </span>
                            </x-table.td>
                            <x-table.td>
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $session->status === 'open' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </x-table.td>
                            <x-table.td
                                class="text-slate-500 font-medium whitespace-nowrap">{{ $session->late_threshold_minutes }}m</x-table.td>

                            {{-- Attendance Column --}}
                            <x-table.td>
                                <div class="flex items-center gap-2 font-bold text-slate-700 dark:text-slate-300">
                                    <div class="p-1 rounded bg-blue-50 dark:bg-blue-900/30 text-brand-blue">
                                        <i class="bi bi-people-fill text-xs"></i>
                                    </div>
                                    {{ $session->records_count }}
                                </div>
                            </x-table.td>

                            <x-table.td>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="h-7 w-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-[10px] font-bold ring-2 ring-white dark:ring-slate-800">
                                        {{ substr($session->creator->name, 0, 1) }}
                                    </div>
                                    <span
                                        class="text-slate-700 dark:text-slate-300 text-sm font-medium truncate max-w-[120px]">{{ $session->creator->name }}</span>
                                </div>
                            </x-table.td>
                            <x-table.td class="text-slate-500 text-xs font-medium whitespace-nowrap">
                                {{ $session->created_at->format('M d, h:i A') }}
                            </x-table.td>
                            <x-table.td align="right">
                                <div class="flex items-center justify-end gap-2">

                                    <a href="{{ route('sessions.show', $session) }}"
                                        class="p-2 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors"
                                        title="View Details">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>

                                    <a href="{{ route('sessions.export', $session) }}"
                                        class="p-2 bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors"
                                        title="Export Excel">
                                        <i class="bi bi-file-earmark-excel-fill"></i>
                                    </a>
                                    <form action="{{ route('sessions.toggle', $session) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="p-2 rounded-lg transition-colors {{ $session->status === 'open' ? 'bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:hover:bg-amber-900/40' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/40' }}"
                                            title="{{ $session->status === 'open' ? 'Close Session' : 'Open Session' }}">
                                            <i
                                                class="bi bi-{{ $session->status === 'open' ? 'stop-fill' : 'play-fill' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr>
                            <x-table.td colspan="8">
                                <x-empty-state icon="bi-calendar-x" title="No sessions found"
                                    message="Try creating a new session." />
                            </x-table.td>
                        </x-table.tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-5">
            @forelse ($sessions as $session)
                <x-card class="relative">
                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        <span
                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $session->status === 'open' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 ring-1 ring-green-500/10' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 ring-1 ring-slate-500/10' }}">
                            {{ ucfirst($session->status) }}
                        </span>
                    </div>

                    <div class="pr-20 mb-4">
                        <span
                            class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 mb-2">
                            {{ $session->committee->name ?? 'General' }}
                        </span>
                        <h3 class="font-bold text-slate-800 dark:text-white text-lg leading-snug">
                            {{ $session->title }}</h3>
                        <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1.5 font-medium">
                            <i class="bi bi-calendar-event"></i>
                            {{ $session->created_at->format('M d, h:i A') }}
                        </p>
                    </div>

                    <div
                        class="flex items-center justify-between gap-4 py-3 border-t border-slate-100 dark:border-slate-800 mb-4">
                        <div class="flex flex-col gap-0.5">
                            <span
                                class="text-[10px] uppercase text-slate-400 font-bold tracking-wider">Attendance</span>
                            <div class="flex items-center gap-1.5 font-bold text-slate-700 dark:text-slate-200">
                                <i class="bi bi-people-fill text-brand-blue"></i> {{ $session->records_count }}
                            </div>
                        </div>

                        <div class="flex flex-col gap-0.5 text-right">
                            <span class="text-[10px] uppercase text-slate-400 font-bold tracking-wider">Creator</span>
                            <div
                                class="flex items-center justify-end gap-1.5 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                <span class="truncate max-w-[100px]">{{ $session->creator->name }}</span>
                                <div
                                    class="h-5 w-5 rounded-full bg-brand-blue/10 flex items-center justify-center text-[8px] font-bold text-brand-blue">
                                    {{ substr($session->creator->name, 0, 1) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">

                        <a href="{{ route('sessions.show', $session) }}"
                            class="flex items-center justify-center gap-2 px-3 py-2.5 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-xl font-bold text-sm hover:bg-blue-100 transition-colors">
                            <i class="bi bi-eye"></i> View
                        </a>

                        <a href="{{ route('sessions.export', $session) }}"
                            class="flex items-center justify-center gap-2 px-3 py-2.5 bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400 rounded-xl font-bold text-sm hover:bg-green-100 transition-colors">
                            <i class="bi bi-download"></i> Excel
                        </a>
                        <form action="{{ route('sessions.toggle', $session) }}" method="POST" class="col-span-2">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl font-bold text-sm transition-colors {{ $session->status === 'open' ? 'bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400' }}">
                                <i class="bi bi-{{ $session->status === 'open' ? 'stop-fill' : 'play-fill' }}"></i>
                                {{ $session->status === 'open' ? 'Close Session' : 'Open Session' }}
                            </button>
                        </form>
                    </div>
                </x-card>
            @empty
                <x-empty-state icon="bi-calendar-x" title="No sessions found" message="No sessions available."
                    class="py-12" />
            @endforelse
        </div>

        @if ($sessions->hasPages())
            <div class="mt-4">
                {{ $sessions->links('components.pagination') }}
            </div>
        @endif
    </div>
</x-app-layout>
