<nav class="flex flex-col space-y-6 px-2">
    @auth
        <!-- SECTION: GENERAL -->
        <div>
            <div class="px-4 mb-2 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                GENERAL
            </div>
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                    <i class="bi bi-grid-fill text-lg"></i>
                    <span>Dashboard</span>
                </a>
            </div>
        </div>

        <!-- SECTION: MY WORK -->
        <div>
            <div
                class="px-4 mt-3 mb-2 text-xs font-bold text-brand-blue dark:text-blue-400 uppercase tracking-widest flex items-center gap-2">
                <span>MY WORK</span>
                <div class="h-px bg-blue-200 dark:bg-blue-900/50 flex-1"></div>
            </div>
            <div class="space-y-1">
                <a href="{{ route('tasks.index') }}"
                    class="sidebar-link {{ request()->routeIs('tasks.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                    <i class="bi bi-list-check text-lg"></i>
                    <span>Tasks management</span>
                </a>

                @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr', 'committee_head']))
                    <a href="{{ route('sessions.index') }}"
                        class="sidebar-link {{ request()->routeIs('sessions.index') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="bi bi-calendar-event text-lg"></i>
                        <span>Sessions management</span>
                    </a>
                @endif

                <a href="{{ route('sessions.history') }}"
                    class="sidebar-link {{ request()->routeIs('sessions.history') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                    <i class="bi bi-clock-history text-lg"></i>
                    <span>History & Feedback</span>
                </a>
            </div>
        </div>

        <!-- SECTION: TEAM MANAGEMENT -->
        @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr', 'committee_head']))
            <div>
                <div
                    class="px-4 mt-3 mb-2 text-xs font-bold text-brand-blue dark:text-blue-400 uppercase tracking-widest flex items-center gap-2">
                    <span>TEAM MANAGMENT</span>
                    <div class="h-px bg-blue-200 dark:bg-blue-900/50 flex-1"></div>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('committees.index') }}"
                        class="sidebar-link {{ request()->routeIs('committees.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="bi bi-people-fill text-lg"></i>
                        <span>Committees</span>
                    </a>

                    @if (Auth::user()->hasRole('top_management'))
                        <a href="{{ route('users.index') }}"
                            class="sidebar-link {{ request()->routeIs('users.index') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                            <i class="bi bi-person-badge-fill text-lg"></i>
                            <span>Users</span>
                        </a>

                        <a href="{{ route('users.pending') }}"
                            class="sidebar-link {{ request()->routeIs('users.pending') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                            <i class="bi bi-check-circle-fill text-lg"></i>
                            <span>Approvals</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- SECTION: INSIGHTS -->
        @if (in_array(Auth::user()->role, ['top_management', 'board', 'committee_head']))
            <div>
                <div
                    class="px-4 mt-3 mb-2 text-xs font-bold text-brand-blue dark:text-blue-400 uppercase tracking-widest flex items-center gap-2">
                    <span>INSIGHTS</span>
                    <div class="h-px bg-blue-200 dark:bg-blue-900/50 flex-1"></div>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('feedbacks.index') }}"
                        class="sidebar-link {{ request()->routeIs('feedbacks.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="bi bi-chat-right-quote-fill text-lg"></i>
                        <span>Feedback</span>
                    </a>

                    @if (\App\Models\ReportPermission::hasAnyAccess(Auth::user()->role))
                        <a href="{{ route('reports.index') }}"
                            class="sidebar-link {{ request()->routeIs('reports.index') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                            <i class="bi bi-file-earmark-bar-graph-fill text-lg"></i>
                            <span>Reports</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- SECTION: SYSTEM -->
        @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr']))
            <div>
                <div
                    class="px-4 mt-3 mb-2 text-xs font-bold text-brand-blue dark:text-blue-400 uppercase tracking-widest flex items-center gap-2">
                    <span>SYSTEM</span>
                    <div class="h-px bg-blue-200 dark:bg-blue-900/50 flex-1"></div>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('scan.index') }}"
                        class="sidebar-link {{ request()->routeIs('scan.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <i class="bi bi-qr-code-scan text-lg"></i>
                        <span>Scan Station</span>
                    </a>

                    @if (Auth::user()->hasRole('top_management'))
                        <a href="{{ route('authorizations.index') }}"
                            class="sidebar-link {{ request()->routeIs('authorizations.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                            <i class="bi bi-shield-lock-fill text-lg"></i>
                            <span>Authorizations</span>
                        </a>

                        <a href="{{ route('report-permissions.index') }}"
                            class="sidebar-link {{ request()->routeIs('report-permissions.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                            <i class="bi bi-eye-slash-fill text-lg"></i>
                            <span>Report Access</span>
                        </a>

                        <a href="{{ route('export_import.index') }}"
                            class="sidebar-link {{ request()->routeIs('export_import.*') ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                            <i class="bi bi-database-fill-gear text-lg"></i>
                            <span>Data Tools</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <div class="h-px bg-slate-200 dark:bg-slate-800 my-2"></div>

        <!-- LOGOUT -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full sidebar-link text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10 hover:text-red-700">
                <i class="bi bi-box-arrow-right text-lg"></i>
                <span>Logout</span>
            </button>
        </form>
    @endauth

    @guest
        <a href="{{ route('login') }}" class="sidebar-link sidebar-link-inactive">
            <i class="bi bi-box-arrow-in-right text-lg"></i>
            <span>Login</span>
        </a>
    @endguest
</nav>
