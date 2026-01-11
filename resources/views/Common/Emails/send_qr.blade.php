<x-app-layout>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Send QR Codes</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Email QR codes to committee members</p>
        </div>
        <div
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm text-sm font-medium text-slate-700 dark:text-slate-300">
            <i class="bi bi-people-fill text-brand-blue mr-2"></i> {{ $users->total() }} Members
        </div>
    </div>

    <!-- Filter Form -->
    <x-card class="mb-8 border-none ring-1 ring-slate-200/50 dark:ring-slate-700/50">
        <x-slot name="header">
            <h3 class="font-bold text-base text-slate-800 dark:text-white flex items-center gap-2">
                <div class="p-1.5 rounded-lg bg-brand-blue/10 text-brand-blue">
                    <i class="bi bi-funnel-fill"></i>
                </div>
                Filter Options
            </h3>
        </x-slot>
        <form method="GET" action="{{ route('qr.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-5">
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
                <x-input-label for="search" value="Search (Name/Email)" class="mb-1.5" />
                <x-text-input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Search members..." class="w-full" />
            </div>
            <div class="flex items-end gap-2">
                <x-primary-button type="submit" class="flex-1 justify-center py-2.5">
                    <i class="bi bi-funnel mr-1"></i> Filter
                </x-primary-button>
                @if (request('search') || request('committee_id'))
                    <x-secondary-button href="{{ route('qr.index') }}" class="py-2.5" title="Clear Filters">
                        <i class="bi bi-x-lg"></i>
                    </x-secondary-button>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Info Notice -->
    <div
        class="mb-6 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 text-sm flex items-start gap-3">
        <div class="p-2 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-lg shrink-0">
            <i class="bi bi-info-circle-fill"></i>
        </div>
        <div class="text-slate-600 dark:text-slate-300 pt-1">
            <strong class="text-slate-800 dark:text-white font-semibold">Note:</strong> Clicking "Open Gmail" will open
            a
            draft with the member's details. You may need to manually attach the QR code image if the link is not
            sufficient.
        </div>
    </div>

    <!-- Desktop View: Table -->
    <div class="hidden md:block">
        <x-card class="p-0 border-none ring-1 ring-slate-200/50 dark:ring-slate-700/50 overflow-hidden"
            :embedded="true">
            <x-slot name="header">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-teal-50 dark:bg-teal-900/20 text-brand-teal">
                        <i class="bi bi-qr-code"></i>
                    </div>
                    <h3 class="font-bold text-base text-slate-800 dark:text-white">Members List</h3>
                </div>
            </x-slot>

            <x-table :headers="['#', 'QR', 'Member', 'Email', 'Committees', 'Action']">
                @forelse($users as $user)
                    @php
                        $qrUrl = URL::signedRoute('qr.view', ['user' => $user->id]);
                        $subject = 'Membership QR - ' . ($user->committees->first()->name ?? 'General');
                        $body =
                            'Hello ' .
                            $user->name .
                            ",\n\nHere is your membership QR code link:\n" .
                            $qrUrl .
                            "\n\nPlease click the link to view your QR code page.\n\nPlease keep it safe.\n\nBest regards,";
                        $gmailUrl =
                            'https://mail.google.com/mail/?view=cm&fs=1&to=' .
                            $user->email .
                            '&su=' .
                            urlencode($subject) .
                            '&body=' .
                            urlencode($body);
                    @endphp
                    <x-table.tr>
                        <x-table.td class="text-slate-400 text-sm w-12">{{ $loop->iteration }}</x-table.td>
                        <x-table.td class="w-20">
                            <div class="bg-white p-1 rounded-lg inline-block ring-1 ring-slate-100">
                                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(40)->generate($user->id) !!}
                            </div>
                        </x-table.td>
                        <x-table.td>
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-8 w-8 rounded-full bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="font-semibold text-slate-800 dark:text-white">{{ $user->name }}</span>
                            </div>
                        </x-table.td>
                        <x-table.td class="text-slate-600 dark:text-slate-400">{{ $user->email }}</x-table.td>
                        <x-table.td>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($user->committees as $comm)
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        {{ $comm->name }}
                                    </span>
                                @endforeach
                            </div>
                        </x-table.td>
                        <x-table.td class="w-32 text-right">
                            <a href="{{ $gmailUrl }}" target="_blank"
                                class="inline-flex items-center justify-center px-3 py-1.5 bg-red-50 dark:bg-red-900/10 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg text-xs font-bold transition-all group border border-red-100 dark:border-red-900/30">
                                <i class="bi bi-envelope-fill mr-1.5 group-hover:scale-110 transition-transform"></i>
                                Gmail
                            </a>
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr>
                        <x-table.td colspan="6" class="p-0">
                            <x-empty-state icon="search" title="No members found"
                                description="Try adjusting your filters" />
                        </x-table.td>
                    </x-table.tr>
                @endforelse
            </x-table>

            {{-- Desktop Pagination --}}
            @if ($users->hasPages())
                <div
                    class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/30">
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif
        </x-card>
    </div>

    <!-- Mobile View: Cards -->
    <div class="md:hidden space-y-4">
        @forelse($users as $user)
            @php
                $qrUrl = URL::signedRoute('qr.view', ['user' => $user->id]);
                $subject = 'Membership QR - ' . ($user->committees->first()->name ?? 'General');
                $body =
                    'Hello ' .
                    $user->name .
                    ",\n\nHere is your membership QR code link:\n" .
                    $qrUrl .
                    "\n\nPlease click the link to view your QR code page.\n\nPlease keep it safe.\n\nBest regards,";
                $gmailUrl =
                    'https://mail.google.com/mail/?view=cm&fs=1&to=' .
                    $user->email .
                    '&su=' .
                    urlencode($subject) .
                    '&body=' .
                    urlencode($body);
            @endphp
            <x-card class="border-none ring-1 ring-slate-200/50 dark:ring-slate-700/50">
                <div class="flex justify-between items-start gap-4 mb-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <div
                                class="h-10 w-10 rounded-full bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white text-sm font-bold shrink-0 shadow-sm">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <h5 class="font-bold text-base text-slate-800 dark:text-white truncate">
                                    {{ $user->name }}
                                </h5>
                                <p class="text-xs text-slate-500 dark:text-slate-400">#{{ $user->id }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 break-all pl-1">
                            {{ $user->email }}
                        </p>
                    </div>
                    <div class="bg-white p-1.5 rounded-xl shrink-0 ring-1 ring-slate-100 shadow-sm">
                        {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(64)->generate($user->id) !!}
                    </div>
                </div>

                <div class="mb-5 flex flex-wrap gap-1">
                    @foreach ($user->committees as $comm)
                        <span
                            class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                            {{ $comm->name }}
                        </span>
                    @endforeach
                </div>

                <a href="{{ $gmailUrl }}" target="_blank"
                    class="flex items-center justify-center w-full px-4 py-2.5 bg-red-50 dark:bg-red-900/10 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-xl text-sm font-bold transition-all border border-red-100 dark:border-red-900/30">
                    <i class="bi bi-envelope-fill mr-2"></i> Open Gmail
                </a>
            </x-card>
        @empty
            <x-empty-state icon="search" title="No members found" description="Try adjusting your filters" />
        @endforelse

        {{-- Mobile Pagination --}}
        @if ($users->hasPages())
            <div class="mt-4">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
