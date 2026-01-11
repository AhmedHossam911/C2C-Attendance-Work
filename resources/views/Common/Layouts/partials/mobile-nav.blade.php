<nav class="flex flex-col space-y-1">
    @auth
        <!-- SECTION: MY WORKSPACE (Blue Theme) -->
        <div class="pt-2 pb-1 px-4 text-xs font-bold text-brand-blue dark:text-blue-400 uppercase tracking-widest">
            MY WORKSPACE
        </div>

        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-teal-50 text-brand-teal dark:bg-teal-900/10 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
            <i class="bi bi-grid-fill text-lg"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('tasks.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('tasks.*') ? 'bg-teal-50 text-brand-teal dark:bg-teal-900/10 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
            <i class="bi bi-list-check text-lg"></i>
            <span>My Tasks</span>
        </a>

        <a href="{{ route('sessions.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('sessions.*') && request('status') != 'closed' ? 'bg-teal-50 text-brand-teal dark:bg-teal-900/10 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
            <i class="bi bi-calendar-event text-lg"></i>
            <span>My Sessions</span>
        </a>

        @if (!in_array(Auth::user()->role, ['top_management', 'board', 'hr']))
            <a href="{{ route('sessions.index', ['status' => 'closed']) }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('sessions.*') && request('status') == 'closed' ? 'bg-teal-50 text-brand-teal dark:bg-teal-900/10 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <i class="bi bi-clock-history text-lg"></i>
                <span>History & Feedback</span>
            </a>
        @endif

        <!-- SECTION: ADMINISTRATION (Teal Theme) -->
        @if (Auth::user()->hasRole('top_management') ||
                Auth::user()->hasRole('board') ||
                Auth::user()->hasRole('hr') ||
                Auth::user()->hasRole('committee_head'))
            <div class="pt-4 pb-1 px-4 text-xs font-bold text-brand-blue dark:text-blue-400 uppercase tracking-widest">
                ADMINISTRATION
            </div>

            <a href="{{ route('committees.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('committees.*') ? 'bg-teal-50 text-brand-teal dark:bg-teal-900/10 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <i class="bi bi-people-fill text-lg"></i>
                <span>Committees</span>
            </a>

            @if (!Auth::user()->hasRole('hr'))
                <a href="{{ route('reports.index') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('reports.index') ? 'bg-teal-50 text-brand-teal dark:bg-teal-900/10 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-bar-chart-fill text-lg"></i>
                    <span>Reports</span>
                </a>
            @endif
        @endif

        <!-- SECTION: PLATFORM CONTROLS (Blue Theme) -->
        @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board') || Auth::user()->hasRole('hr'))
            <div class="pt-4 pb-1 px-4 text-xs font-bold text-brand-blue dark:text-blue-400 uppercase tracking-widest">
                PLATFORM CONTROLS
            </div>

            @if (Auth::user()->hasRole('top_management'))
                <a href="{{ route('users.index') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('users.index') ? 'bg-teal-50 text-brand-teal dark:bg-teal-900/10 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-person-badge-fill text-lg"></i>
                    <span>Users</span>
                </a>

                <a href="{{ route('users.pending') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('users.pending') ? 'bg-teal-50 text-brand-teal dark:bg-teal-900/10 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-check-circle-fill text-lg"></i>
                    <span>Approvals</span>
                </a>

                <a href="{{ route('authorizations.index') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('authorizations.*') ? 'bg-teal-50 text-brand-teal dark:bg-teal-900/10 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-key-fill text-lg"></i>
                    <span>Authorizations</span>
                </a>
            @endif

            <a href="{{ route('scan.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('scan.*') ? 'bg-teal-50 text-brand-teal dark:bg-teal-900/10 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <i class="bi bi-qr-code-scan text-lg"></i>
                <span>Scan Station</span>
            </a>

            @if (Auth::user()->hasRole('top_management'))
                <a href="{{ route('export_import.index') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('export_import.*') ? 'bg-teal-50 text-brand-teal dark:bg-teal-900/10 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-database-fill-gear text-lg"></i>
                    <span>Data Tools</span>
                </a>

                <a href="{{ route('reports.member') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('reports.member') ? 'bg-teal-50 text-brand-teal dark:bg-teal-900/10 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-search text-lg"></i>
                    <span>Member Search</span>
                </a>
            @endif
        @endif

        <div class="h-px bg-slate-200 dark:bg-slate-800 my-2"></div>

        <!-- LOGOUT -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10 transition-colors">
                <i class="bi bi-box-arrow-right text-lg"></i>
                <span>Logout</span>
            </button>
        </form>
    @endauth

    @guest
        <a href="{{ route('login') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            <i class="bi bi-box-arrow-in-right text-lg"></i>
            <span>Login</span>
        </a>
    @endguest
</nav>
