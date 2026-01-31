<x-app-layout>
    <div class="space-y-8 pb-20 md:pb-0" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
        <!-- 1. Welcome Banner (Gradient) -->
        <div x-show="show" x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 translateY-10" x-transition:enter-end="opacity-100 translateY-0"
            class="rounded-xl bg-gradient-brand p-8 md:p-10 shadow-lg relative overflow-hidden group">
            <!-- Background Decorations -->
            <div class="absolute inset-0 bg-white/5 opacity-50 pattern-grid-lg"></div>

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
                            class="px-4 py-2 rounded-xl bg-slate-300/20 backdrop-blur-md border border-white/20 text-sm font-semibold text-white shadow-sm flex items-center gap-2 hover:bg-slate-300/30 transition-colors">
                            <i class="bi bi-calendar4-week"></i> {{ now()->format('D, M d') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Role-Based Stats Grid -->
        @if (isset($adminStats))
            <div x-show="show" x-transition:enter="transition ease-out duration-700 delay-100"
                x-transition:enter-start="opacity-0 translateY-10" x-transition:enter-end="opacity-100 translateY-0"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">

                <x-stats-card title="Committees" value="{{ $adminStats['committees'] }}" icon="bi-people-fill"
                    color="purple" />

                <x-stats-card title="Active Sessions" value="{{ $adminStats['open_sessions'] }}" icon="bi-clock-fill"
                    color="green" />

                <x-stats-card title="Attendees Today" value="{{ $adminStats['attendees_today'] }}"
                    icon="bi-person-check-fill" color="blue" />

                <x-stats-card title="Total Users" value="{{ $adminStats['total_users'] }}" icon="bi-person-lines-fill"
                    color="orange" />
            </div>
        @elseif(isset($headStats))
            <div x-show="show" x-transition:enter="transition ease-out duration-700 delay-100"
                x-transition:enter-start="opacity-0 translateY-10" x-transition:enter-end="opacity-100 translateY-0"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">

                <x-stats-card title="My Committees" value="{{ $headStats['my_committees'] }}" icon="bi-people-fill"
                    color="purple" />

                <x-stats-card title="Total Members" value="{{ $headStats['total_members'] }}"
                    icon="bi-person-badge-fill" color="orange" />

                <x-stats-card title="Open Sessions" value="{{ $headStats['open_sessions'] }}" icon="bi-clock-fill"
                    color="green" />

                <x-stats-card title="Pending Reviews" value="{{ $headStats['pending_reviews'] ?? 0 }}"
                    icon="bi-list-check" color="brand-teal" />
            </div>
        @elseif(isset($memberStats))
            <div x-show="show" x-transition:enter="transition ease-out duration-700 delay-100"
                x-transition:enter-start="opacity-0 translateY-10" x-transition:enter-end="opacity-100 translateY-0"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">

                <x-stats-card title="Pending Tasks" value="{{ $memberStats['pending_tasks'] }}" icon="bi-list-task"
                    color="brand-blue" />

                <x-stats-card title="My Attendance" value="{{ $memberStats['total'] }}" icon="bi-calendar-check-fill"
                    color="slate" />

                <x-stats-card title="Present" value="{{ $memberStats['present'] }}" icon="bi-check-circle-fill"
                    color="brand-teal" />

                <x-stats-card title="Late" value="{{ $memberStats['late'] }}" icon="bi-exclamation-circle-fill"
                    color="brand-gold" />
            </div>
        @endif

        <!-- Main Content Area -->
        <div x-show="show" x-transition:enter="transition ease-out duration-700 delay-200"
            x-transition:enter-start="opacity-0 translateY-10" x-transition:enter-end="opacity-100 translateY-0"
            class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
            <!-- Left: Actions/Lists -->
            <div class="lg:col-span-2 space-y-6">

                @if (Auth::user()->hasRole('top_management'))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-action-card href="{{ route('users.pending') }}" icon="bi-person-check-fill"
                            title="Approvals" description="Review pending users" color="orange" :rotate="false" />
                        <x-action-card href="{{ route('qr.index') }}" icon="bi-qr-code" title="QR Tools"
                            description="Manage codes & scans" color="teal" :rotate="true" />
                    </div>
                @elseif (Auth::user()->hasRole('committee_head'))
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-action-card href="{{ route('tasks.index') }}" icon="bi-list-check" title="Tasks"
                            description="Manage committee tasks" color="blue" :rotate="false" />
                        <x-action-card href="{{ route('reports.index') }}" icon="bi-bar-chart-fill" title="Reports"
                            description="View performance" color="indigo" :rotate="true"
                            class="bg-slate-300 dark:bg-[#1e293b]" />
                        <x-action-card href="{{ route('reports.session_quality') }}" icon="bi-star-fill"
                            title="Reviews" description="Session feedback" color="amber" :rotate="false"
                            class="bg-slate-300 dark:bg-[#1e293b]" />
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-action-card href="{{ route('tasks.index') }}" icon="bi-list-task" title="My Tasks"
                            description="View and submit work" color="brand-blue" :rotate="false"
                            class="bg-slate-300 dark:bg-[#1e293b]" />
                        <x-action-card href="{{ route('sessions.index') }}" icon="bi-calendar-event"
                            title="Sessions" description="View attendance history" color="purple" :rotate="true"
                            class="bg-slate-300 dark:bg-[#1e293b]" />
                    </div>
                @endif

                <!-- Active Sessions -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
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
                                    class="p-4 md:p-6 flex items-center justify-between hover:bg-slate-300 dark:hover:bg-slate-800/50 transition-colors group cursor-pointer">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-12 w-12 rounded-2xl bg-green-500/10 text-green-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                                            <i class="bi bi-clock-fill"></i>
                                        </div>
                                        <div>
                                            <h4
                                                class="font-bold text-slate-900 dark:text-slate-100 group-hover:text-brand-teal transition-colors">
                                                {{ $session->title }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span
                                                    class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-300 dark:bg-slate-800 text-slate-500">
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
                                class="inline-flex h-16 w-16 rounded-full bg-slate-300 dark:bg-slate-800 items-center justify-center mb-4 text-teal-200 dark:text-slate-600">
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
                    class="bg-gradient-to-b from-slate-100 via-white to-slate-50 dark:from-slate-800 dark:to-slate-900 rounded-xl p-8 text-center shadow-xl border border-slate-200 dark:border-slate-700 relative overflow-hidden group hover:scale-[1.01] transition-transform duration-500">
                    <!-- Decor -->
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-blue to-brand-teal"></div>
                    <div
                        class="absolute -top-24 -right-24 w-48 h-48 bg-brand-blue/5 dark:bg-brand-teal/10 rounded-full blur-3xl group-hover:bg-brand-blue/10 dark:group-hover:bg-brand-teal/20 transition-all duration-700">
                    </div>
                    <div
                        class="absolute -bottom-16 -left-16 w-32 h-32 bg-brand-teal/5 dark:bg-transparent rounded-full blur-2xl">
                    </div>

                    <div class="relative z-10">
                        <div
                            class="h-24 w-24 mx-auto bg-gradient-to-br from-brand-blue to-brand-teal p-1 rounded-full shadow-xl shadow-brand-blue/20 dark:shadow-brand-blue/10 mb-4 group-hover:scale-105 transition-transform">
                            <div
                                class="w-full h-full rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-3xl font-bold text-brand-blue dark:text-brand-teal">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-1 text-slate-800 dark:text-white">{{ Auth::user()->name }}
                        </h3>
                        <p
                            class="text-slate-500 dark:text-slate-400 text-sm font-medium uppercase tracking-widest mb-6">
                            {{ Auth::user()->hasRole('committee_head') ? 'Committee BOARD' : ucfirst(str_replace('_', ' ', Auth::user()->role)) }}
                        </p>

                        <div
                            class="bg-white p-4 rounded-2xl inline-block shadow-lg shadow-slate-200/50 dark:shadow-none mx-auto transform transition-all hover:scale-105 cursor-pointer border border-slate-100 dark:border-transparent">
                            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(160)->generate(Auth::id()) !!}
                        </div>

                        <div
                            class="mt-6 flex items-center justify-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                            <i class="bi bi-person-badge"></i> Use for attendance
                        </div>
                    </div>
                </div>

                <!-- Recent Activity (Compact) -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm p-6 hover:shadow-md transition-shadow">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Recent History</h4>
                    <div class="space-y-4">
                        @if ($recentSessions->count() > 0)
                            @foreach ($recentSessions->take(3) as $session)
                                <a href="{{ route('sessions.show', $session->id) }}"
                                    class="flex items-center gap-3 group cursor-pointer hover:bg-slate-300 dark:hover:bg-slate-800/50 -m-2 p-2 rounded-lg transition-colors">
                                    <div
                                        class="h-10 w-10 rounded-full bg-slate-300 dark:bg-slate-800 flex items-center justify-center shrink-0 group-hover:bg-green-100 dark:group-hover:bg-green-900/30 transition-colors">
                                        <i class="bi bi-check-lg text-green-500"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-sm font-bold text-slate-900 dark:text-slate-200 truncate group-hover:text-brand-teal transition-colors">
                                            {{ $session->title }}</p>
                                        <p class="text-xs text-slate-500">{{ $session->created_at->diffForHumans() }}
                                        </p>
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
</x-app-layout>
