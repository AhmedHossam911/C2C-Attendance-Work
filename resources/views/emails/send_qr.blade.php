@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Send QR Codes</h2>
    </div>

    <!-- Filter Form -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('qr.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Filter by Committee</label>
                <select name="committee_id"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white">
                    <option value="">All Committees</option>
                    @foreach ($committees as $committee)
                        <option value="{{ $committee->id }}"
                            {{ request('committee_id') == $committee->id ? 'selected' : '' }}>
                            {{ $committee->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Search (Name/Email)</label>
                <input type="text" name="search"
                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 focus:border-brand-blue focus:ring-brand-blue dark:text-white"
                    value="{{ request('search') }}" placeholder="Search members...">
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full px-5 py-2.5 bg-brand-blue hover:bg-brand-blue/90 text-white font-semibold rounded-xl shadow-lg shadow-brand-blue/20 transition-all active:scale-95">
                    Filter
                </button>
            </div>
        </form>
    </x-card>

    <div
        class="mb-6 p-4 rounded-xl bg-sky-50 text-sky-800 border border-sky-200 dark:bg-sky-900/20 dark:text-sky-300 dark:border-sky-800/30 text-sm flex items-start gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                clip-rule="evenodd" />
        </svg>
        <span><strong>Note:</strong> Clicking "Open Gmail" will open a draft with the member's details. You may need to
            manually attach the QR code image if the link is not sufficient.</span>
    </div>

    <!-- Desktop View: Table -->
    <div class="hidden md:block">
        <x-card class="p-0" :embedded="true">
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">Members List</h3>
                    <span
                        class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700/50 text-xs font-bold text-slate-600 dark:text-slate-400">
                        {{ $users->total() }} Members
                    </span>
                </div>
            </x-slot>

            <x-table :headers="['ID', 'QR', 'Name', 'Email', 'Committees', 'Action']">
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
                        <x-table.td>{{ $user->id }}</x-table.td>
                        <x-table.td>
                            <div class="bg-white p-1 rounded inline-block">
                                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->generate($user->id) !!}
                            </div>
                        </x-table.td>
                        <x-table.td class="font-bold text-slate-800 dark:text-white">{{ $user->name }}</x-table.td>
                        <x-table.td>{{ $user->email }}</x-table.td>
                        <x-table.td>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($user->committees as $comm)
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                        {{ $comm->name }}
                                    </span>
                                @endforeach
                            </div>
                        </x-table.td>
                        <x-table.td>
                            <a href="{{ $gmailUrl }}" target="_blank"
                                class="inline-flex items-center px-3 py-1.5 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 text-xs font-bold transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                                Open Gmail
                            </a>
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400 italic">No users
                            found.</td>
                    </x-table.tr>
                @endforelse
            </x-table>
        </x-card>
        <div class="mt-4">
            {{ $users->links() }}
        </div>
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
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h5 class="font-bold text-lg text-slate-800 dark:text-white">{{ $user->name }}</h5>
                        <p class="text-xs font-medium text-slate-400">#{{ $user->id }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 break-all">{{ $user->email }}</p>
                    </div>
                    <div class="bg-white p-1 rounded shrink-0">
                        {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($user->id) !!}
                    </div>
                </div>

                <div class="mb-4 flex flex-wrap gap-1">
                    @foreach ($user->committees as $comm)
                        <span
                            class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                            {{ $comm->name }}
                        </span>
                    @endforeach
                </div>

                <a href="{{ $gmailUrl }}" target="_blank"
                    class="flex items-center justify-center w-full px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-sm font-bold transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                    </svg>
                    Open Gmail
                </a>
            </x-card>
        @empty
            <div
                class="p-4 rounded-xl bg-orange-50 text-orange-800 border border-orange-200 dark:bg-orange-900/20 dark:text-orange-300 dark:border-orange-800/30">
                No users found.
            </div>
        @endforelse

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection
