@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Send QR Codes</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Email QR codes to committee members</p>
        </div>
        <span
            class="inline-flex items-center px-4 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-xl text-sm font-bold">
            <i class="bi bi-people-fill mr-2"></i> {{ $users->total() }} Members
        </span>
    </div>

    <!-- Filter Form -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('qr.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-input-label class="mb-2">
                    <i class="bi bi-building text-slate-400 mr-1"></i> Filter by Committee
                </x-input-label>
                <x-select-input name="committee_id">
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
                <x-input-label class="mb-2">
                    <i class="bi bi-search text-slate-400 mr-1"></i> Search (Name/Email)
                </x-input-label>
                <x-text-input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search members..." />
            </div>
            <div class="flex items-end gap-2">
                <x-primary-button type="submit" class="flex-1 justify-center py-2.5">
                    <i class="bi bi-funnel mr-1"></i> Filter
                </x-primary-button>
                @if (request('search') || request('committee_id'))
                    <x-secondary-button href="{{ route('qr.index') }}" class="py-2.5">
                        <i class="bi bi-x-circle"></i>
                    </x-secondary-button>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Info Notice -->
    <div
        class="mb-6 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 text-sm flex items-start gap-3">
        <div class="p-2 bg-blue-100 dark:bg-blue-800/30 rounded-lg shrink-0">
            <i class="bi bi-info-circle text-blue-600 dark:text-blue-400"></i>
        </div>
        <div class="text-blue-800 dark:text-blue-300">
            <strong>Note:</strong> Clicking "Open Gmail" will open a draft with the member's details. You may need to
            manually attach the QR code image if the link is not sufficient.
        </div>
    </div>

    <!-- Desktop View: Table -->
    <div class="hidden md:block">
        <x-card class="p-0" :embedded="true">
            <x-slot name="header">
                <div class="flex items-center gap-2">
                    <i class="bi bi-qr-code text-slate-400"></i>
                    <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100">Members List</h3>
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
                        <x-table.td class="text-slate-400 text-sm">{{ $loop->iteration }}</x-table.td>
                        <x-table.td>
                            <div class="bg-[#ffffff] p-1.5 rounded-lg inline-block shadow-sm">
                                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->generate($user->id) !!}
                            </div>
                        </x-table.td>
                        <x-table.td>
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-9 w-9 rounded-full bg-gradient-to-br from-blue-500 to-teal-500 flex items-center justify-center text-slate-50 text-xs font-bold">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $user->name }}</span>
                            </div>
                        </x-table.td>
                        <x-table.td class="text-slate-600 dark:text-slate-400">{{ $user->email }}</x-table.td>
                        <x-table.td>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($user->committees as $comm)
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-lg bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                        {{ $comm->name }}
                                    </span>
                                @endforeach
                            </div>
                        </x-table.td>
                        <x-table.td>
                            <a href="{{ $gmailUrl }}" target="_blank"
                                class="inline-flex items-center px-3.5 py-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 text-xs font-bold transition-all">
                                <i class="bi bi-envelope-fill mr-1.5"></i> Open Gmail
                            </a>
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-slate-200 dark:bg-slate-700 rounded-full mb-3">
                                    <i class="bi bi-person-x text-2xl text-slate-400"></i>
                                </div>
                                <p class="text-slate-600 dark:text-slate-400 font-medium">No members found</p>
                                <p class="text-xs text-slate-500 mt-1">Try adjusting your filters</p>
                            </div>
                        </td>
                    </x-table.tr>
                @endforelse
            </x-table>

            {{-- Desktop Pagination --}}
            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Showing <span
                                class="font-semibold text-slate-800 dark:text-slate-200">{{ $users->firstItem() }}</span>
                            to <span
                                class="font-semibold text-slate-800 dark:text-slate-200">{{ $users->lastItem() }}</span>
                            of <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $users->total() }}</span>
                        </p>
                        <div class="flex items-center gap-1">
                            @if ($users->onFirstPage())
                                <span
                                    class="inline-flex items-center justify-center w-10 h-10 text-slate-400 bg-slate-200 dark:bg-slate-700 cursor-not-allowed rounded-xl">
                                    <i class="bi bi-chevron-left"></i>
                                </span>
                            @else
                                <a href="{{ $users->previousPageUrl() }}"
                                    class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-blue-600 transition-all">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            @endif

                            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                @if ($page == $users->currentPage())
                                    <span
                                        class="inline-flex items-center justify-center w-10 h-10 text-slate-50 font-bold bg-blue-600 rounded-xl shadow-sm">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}"
                                        class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 font-medium bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-blue-600 transition-all">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            @if ($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}"
                                    class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-blue-600 transition-all">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            @else
                                <span
                                    class="inline-flex items-center justify-center w-10 h-10 text-slate-400 bg-slate-200 dark:bg-slate-700 cursor-not-allowed rounded-xl">
                                    <i class="bi bi-chevron-right"></i>
                                </span>
                            @endif
                        </div>
                    </div>
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
            <x-card>
                <div class="flex justify-between items-start gap-4 mb-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <div
                                class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-teal-500 flex items-center justify-center text-slate-50 text-sm font-bold shrink-0">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <h5 class="font-bold text-lg text-slate-800 dark:text-slate-100 truncate">
                                    {{ $user->name }}</h5>
                                <p class="text-xs text-slate-400">#{{ $user->id }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-400 break-all mt-2">
                            <i class="bi bi-envelope mr-1 text-slate-400"></i> {{ $user->email }}
                        </p>
                    </div>
                    <div class="bg-[#ffffff] p-2 rounded-lg shrink-0 shadow-sm">
                        {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(70)->generate($user->id) !!}
                    </div>
                </div>

                <div class="mb-4 flex flex-wrap gap-1">
                    @foreach ($user->committees as $comm)
                        <span
                            class="px-2.5 py-1 text-xs font-medium rounded-lg bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                            {{ $comm->name }}
                        </span>
                    @endforeach
                </div>

                <a href="{{ $gmailUrl }}" target="_blank"
                    class="flex items-center justify-center w-full px-4 py-3 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 rounded-xl text-sm font-bold transition-all">
                    <i class="bi bi-envelope-fill mr-2"></i> Open Gmail
                </a>
            </x-card>
        @empty
            <x-card class="text-center py-10">
                <div class="flex flex-col items-center">
                    <div class="p-4 bg-slate-200 dark:bg-slate-700 rounded-full mb-3">
                        <i class="bi bi-person-x text-2xl text-slate-400"></i>
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 font-medium">No members found</p>
                    <p class="text-xs text-slate-500 mt-1">Try adjusting your filters</p>
                </div>
            </x-card>
        @endforelse

        {{-- Mobile Pagination --}}
        @if ($users->hasPages())
            <div class="flex items-center justify-between mt-4">
                <div class="flex-1 flex justify-start">
                    @if ($users->onFirstPage())
                        <span
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-200 dark:bg-slate-700 cursor-not-allowed rounded-xl">
                            <i class="bi bi-chevron-left mr-1"></i> Previous
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}"
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                            <i class="bi bi-chevron-left mr-1"></i> Previous
                        </a>
                    @endif
                </div>

                <div class="px-4 text-sm text-slate-600 dark:text-slate-400 font-medium">
                    {{ $users->currentPage() }} / {{ $users->lastPage() }}
                </div>

                <div class="flex-1 flex justify-end">
                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}"
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                            Next <i class="bi bi-chevron-right ml-1"></i>
                        </a>
                    @else
                        <span
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-200 dark:bg-slate-700 cursor-not-allowed rounded-xl">
                            Next <i class="bi bi-chevron-right ml-1"></i>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
