@extends('layouts.app')

@section('content')
    <div class="space-y-8 pb-20 md:pb-0" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
        <!-- 1. Welcome Banner (Gradient) -->
        <div x-show="show" x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 translateY-10" x-transition:enter-end="opacity-100 translateY-0"
            class="rounded-[2rem] bg-gradient-to-r from-brand-blue to-brand-teal p-8 md:p-10 shadow-2xl relative overflow-hidden group">
            <!-- Background Decorations (Animated) -->
            <div
                class="absolute top-0 right-0 -mt-10 -mr-10 h-64 w-64 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-1000 animate-pulse">
            </div>
            <div
                class="absolute bottom-0 left-0 -mb-10 -ml-10 h-40 w-40 bg-black/10 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-1000">
            </div>

            <div class="relative z-10 text-white">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2 flex items-center gap-3">
                            Welcome back, {{ strtok(Auth::user()->name, ' ') }}! <span
                                class="inline-block hover:animate-spin cursor-default">👋</span>
                        </h2>
                        <p class="text-blue-50 text-lg font-medium opacity-90 max-w-xl">
                            Here's your daily summary. You have <span
                                class="font-bold text-white text-xl">{{ isset($upcomingSessions) ? $upcomingSessions->count() : 0 }}</span>
                            active sessions today.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <span
                            class="px-4 py-2 rounded-xl bg-white/20 backdrop-blur-md border border-white/20 text-sm font-semibold text-white shadow-sm flex items-center gap-2 hover:bg-white/30 transition-colors">
                            <i class="bi bi-calendar4-week"></i> {{ now()->format('D, M d') }}
                        </span>

                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Admin Quick Stats (If Admin) -->
        @if (isset($adminStats))
            <div x-show="show" x-transition:enter="transition ease-out duration-700 delay-100"
                x-transition:enter-start="opacity-0 translateY-10" x-transition:enter-end="opacity-100 translateY-0"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <!-- Stat Card: Committees -->
                <div
                    class="bg-white dark:bg-[#1e293b] p-6 rounded-[1.5rem] shadow-sm border border-slate-100 dark:border-slate-800 flex items-center justify-between group hover:-translate-y-2 hover:shadow-lg transition-all duration-300">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">
                            Committees</p>
                        <h3 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $adminStats['committees'] }}</h3>
                    </div>
                    <div
                        class="h-12 w-12 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>

                <!-- Stat Card: Sessions -->
                <div
                    class="bg-white dark:bg-[#1e293b] p-6 rounded-[1.5rem] shadow-sm border border-slate-100 dark:border-slate-800 flex items-center justify-between group hover:-translate-y-2 hover:shadow-lg transition-all duration-300">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Active
                            Sessions</p>
                        <h3 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $adminStats['open_sessions'] }}
                        </h3>
                    </div>
                    <div
                        class="h-12 w-12 rounded-2xl bg-green-500/10 text-green-600 dark:text-green-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                </div>

                <!-- Stat Card: Attendees -->
                <div
                    class="bg-white dark:bg-[#1e293b] p-6 rounded-[1.5rem] shadow-sm border border-slate-100 dark:border-slate-800 flex items-center justify-between group hover:-translate-y-2 hover:shadow-lg transition-all duration-300">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">
                            Attendees Today</p>
                        <h3 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $adminStats['attendees_today'] }}
                        </h3>
                    </div>
                    <div
                        class="h-12 w-12 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>

                <!-- Stat Card: Users -->
                <div
                    class="bg-white dark:bg-[#1e293b] p-6 rounded-[1.5rem] shadow-sm border border-slate-100 dark:border-slate-800 flex items-center justify-between group hover:-translate-y-2 hover:shadow-lg transition-all duration-300">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Total
                            Users</p>
                        <h3 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $adminStats['total_users'] }}</h3>
                    </div>
                    <div
                        class="h-12 w-12 rounded-2xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        @elseif(isset($attendanceStats))
            <!-- User Stats -->
            <div x-show="show" x-transition:enter="transition ease-out duration-700 delay-100"
                x-transition:enter-start="opacity-0 translateY-10" x-transition:enter-end="opacity-100 translateY-0"
                class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                <!-- Total -->
                <div
                    class="bg-white dark:bg-[#1e293b] p-6 rounded-[1.5rem] shadow-sm border border-slate-100 dark:border-slate-800 flex items-center justify-between group hover:-translate-y-2 hover:shadow-lg transition-all duration-300">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Total
                            Attendance</p>
                        <h3 class="text-3xl font-bold text-brand-blue dark:text-blue-400">{{ $attendanceStats['total'] }}
                        </h3>
                    </div>
                    <div
                        class="h-12 w-12 rounded-2xl bg-blue-500/10 text-brand-blue dark:text-blue-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                </div>
                <!-- Present -->
                <div
                    class="bg-white dark:bg-[#1e293b] p-6 rounded-[1.5rem] shadow-sm border border-slate-100 dark:border-slate-800 flex items-center justify-between group hover:-translate-y-2 hover:shadow-lg transition-all duration-300">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">
                            Present</p>
                        <h3 class="text-3xl font-bold text-brand-teal dark:text-teal-400">{{ $attendanceStats['present'] }}
                        </h3>
                    </div>
                    <div
                        class="h-12 w-12 rounded-2xl bg-teal-500/10 text-brand-teal dark:text-teal-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
                <!-- Late -->
                <div
                    class="bg-white dark:bg-[#1e293b] p-6 rounded-[1.5rem] shadow-sm border border-slate-100 dark:border-slate-800 flex items-center justify-between group hover:-translate-y-2 hover:shadow-lg transition-all duration-300">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Late
                        </p>
                        <h3 class="text-3xl font-bold text-brand-gold dark:text-yellow-400">{{ $attendanceStats['late'] }}
                        </h3>
                    </div>
                    <div
                        class="h-12 w-12 rounded-2xl bg-yellow-500/10 text-brand-gold dark:text-yellow-400 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content Area -->
        <div x-show="show" x-transition:enter="transition ease-out duration-700 delay-200"
            x-transition:enter-start="opacity-0 translateY-10" x-transition:enter-end="opacity-100 translateY-0"
            class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
            <!-- Left: Actions/Lists -->
            <div class="lg:col-span-2 space-y-6">

                @if (Auth::user()->hasRole('top_management'))
                    <!-- Admin Actions Banner Style -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('users.pending') }}"
                            class="group relative overflow-hidden bg-white dark:bg-[#1e293b] rounded-[1.5rem] p-6 border border-slate-100 dark:border-slate-800 shadow-sm transition-all hover:shadow-lg hover:-translate-y-2">
                            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                <i class="bi bi-person-check-fill text-8xl transform rotate-12"></i>
                            </div>
                            <div class="relative z-10 flex items-center gap-4">
                                <div
                                    class="h-14 w-14 rounded-2xl bg-orange-500/10 text-orange-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                    <i class="bi bi-person-check-fill"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">Approvals</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Review pending users</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('qr.index') }}"
                            class="group relative overflow-hidden bg-white dark:bg-[#1e293b] rounded-[1.5rem] p-6 border border-slate-100 dark:border-slate-800 shadow-sm transition-all hover:shadow-lg hover:-translate-y-2">
                            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                <i class="bi bi-qr-code text-8xl transform -rotate-12"></i>
                            </div>
                            <div class="relative z-10 flex items-center gap-4">
                                <div
                                    class="h-14 w-14 rounded-2xl bg-brand-teal/10 text-brand-teal flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                    <i class="bi bi-qr-code"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">QR Tools</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Manage codes & scans</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                <!-- Active Sessions -->
                <div
                    class="bg-white dark:bg-[#1e293b] rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="bi bi-broadcast text-brand-teal animate-pulse"></i> Active Sessions
                        </h3>
                        @if ($upcomingSessions->count() > 0)
                            <span class="flex h-2.5 w-2.5 relative">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                            </span>
                        @endif
                    </div>
                    @if ($upcomingSessions->count() > 0)
                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($upcomingSessions as $session)
                                <a href="{{ route('sessions.show', $session->id) }}"
                                    class="p-4 md:p-6 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group cursor-pointer">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-12 w-12 rounded-2xl bg-green-500/10 text-green-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                                            <i class="bi bi-clock-fill"></i>
                                        </div>
                                        <div>
                                            <h4
                                                class="font-bold text-slate-800 dark:text-slate-100 group-hover:text-brand-teal transition-colors">
                                                {{ $session->title }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span
                                                    class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500">
                                                    {{ $session->committee->name ?? 'General' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <span
                                        class="px-5 py-2.5 bg-brand-teal hover:bg-brand-teal/90 text-white text-sm font-bold rounded-xl shadow-lg shadow-brand-teal/20 transition-all active:scale-95 hover:-translate-y-1 block md:inline-block text-center">
                                        Open
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <div
                                class="inline-flex h-16 w-16 rounded-full bg-slate-100 dark:bg-slate-800 items-center justify-center mb-4 text-slate-300 dark:text-slate-600">
                                <i class="bi bi-calendar-x text-2xl"></i>
                            </div>
                            <p class="text-slate-500 font-medium">No active sessions at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: QR & Extras -->
            <div class="space-y-6">
                <!-- Digital ID Card -->
                <div
                    class="bg-gradient-to-b from-[#1e293b] to-[#0f172a] rounded-[2rem] p-8 text-center text-white shadow-2xl border border-slate-800 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-500">
                    <!-- Decor -->
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-blue to-brand-teal"></div>
                    <div
                        class="absolute -top-24 -right-24 w-48 h-48 bg-brand-teal/10 rounded-full blur-3xl group-hover:bg-brand-teal/20 transition-all duration-700">
                    </div>

                    <div class="relative z-10">
                        <div
                            class="h-24 w-24 mx-auto bg-white p-1 rounded-full shadow-xl mb-4 group-hover:scale-105 transition-transform">
                            <div
                                class="w-full h-full rounded-full bg-slate-100 flex items-center justify-center text-3xl font-bold text-brand-blue">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-1">{{ Auth::user()->name }}</h3>
                        <p class="text-slate-400 text-sm font-medium uppercase tracking-widest mb-6">
                            {{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</p>

                        <div
                            class="bg-white p-4 rounded-2xl inline-block shadow-lg mx-auto transform transition-all hover:scale-105 cursor-pointer">
                            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(160)->generate(Auth::id()) !!}
                        </div>

                        <div class="mt-6 flex items-center justify-center gap-2 text-sm text-slate-400">
                            <i class="bi bi-person-badge"></i> Use for attendance
                        </div>
                    </div>
                </div>

                <!-- Recent Activity (Compact) -->
                <div
                    class="bg-white dark:bg-[#1e293b] rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-sm p-6 hover:shadow-md transition-shadow">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Recent History</h4>
                    <div class="space-y-4">
                        @if ($recentSessions->count() > 0)
                            @foreach ($recentSessions->take(3) as $session)
                                <a href="{{ route('sessions.show', $session->id) }}"
                                    class="flex items-center gap-3 group cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 -m-2 p-2 rounded-lg transition-colors">
                                    <div
                                        class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 group-hover:bg-green-100 dark:group-hover:bg-green-900/30 transition-colors">
                                        <i class="bi bi-check-lg text-green-500"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate group-hover:text-brand-teal transition-colors">
                                            {{ $session->title }}</p>
                                        <p class="text-xs text-slate-500">{{ $session->created_at->diffForHumans() }}</p>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <p class="text-sm text-slate-500 italic">No recent history.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
