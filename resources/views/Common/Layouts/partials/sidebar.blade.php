<!-- Sidebar -->
<aside
    class="hidden lg:flex fixed inset-y-0 left-0 z-50 bg-slate-50 dark:bg-[#0f172a] text-slate-900 dark:text-slate-200 transform transition-all duration-300 ease-in-out flex-col border-r border-slate-200 dark:border-slate-800 shadow-2xl lg:shadow-none"
    :class="[
        sidebarCollapsed ? 'lg:w-20' : 'lg:w-72',
    ]">

    <!-- Toggle Handle -->
    <div @click="sidebarCollapsed = !sidebarCollapsed"
        class="hidden lg:flex absolute -right-3 top-20 w-3 h-12 bg-white dark:bg-[#0f172a] border border-l-0 border-slate-200 dark:border-slate-800 rounded-r-md cursor-pointer items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-800 transition-all shadow-sm group z-50"
        title="Toggle Sidebar">
        <div class="w-0.5 h-6 rounded-full bg-slate-300 dark:bg-slate-600 group-hover:bg-brand-teal transition-colors">
        </div>
    </div>

    <!-- Logo Section -->
    <div class="h-16 flex items-center border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-[#0f172a] relative overflow-hidden transition-all shrink-0"
        :class="sidebarCollapsed ? 'justify-center px-0' : 'justify-between px-6'">

        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group relative z-10">
            <div class="relative flex items-center justify-center transition-all duration-300">
                <img src="{{ asset('logo-color.png') }}" alt="C2C Logo"
                    class="w-auto relative z-10 transform group-hover:scale-105 transition-transform bg-white rounded-lg p-1 dark:bg-white"
                    :class="sidebarCollapsed ? 'h-8' : 'h-10'">
            </div>

            <div class="flex flex-col transition-opacity duration-200" x-show="!sidebarCollapsed">
                <span
                    class="text-base font-bold tracking-tight text-slate-900 dark:text-slate-100 leading-tight whitespace-nowrap">C2C
                    Management</span>
                <span
                    class="text-[10px] font-medium text-brand-teal uppercase tracking-[0.2em] whitespace-nowrap">System</span>
            </div>
        </a>
    </div>

    <!-- Nav Links -->
    <nav class="flex-1 py-6 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-700 scrollbar-track-transparent flex flex-col gap-6"
        :class="sidebarCollapsed ? 'px-2' : 'px-4'">

        @auth
            <!-- SECTION: GENERAL -->
            <div>
                <div class="mb-2 font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest flex items-center gap-2 transition-all duration-300"
                    :class="sidebarCollapsed ? 'justify-center text-[0px]' : 'px-4 text-[10px]'">
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">GENERAL</span>
                    <i x-show="sidebarCollapsed" class="bi bi-grid-fill text-xs text-slate-400"></i>
                </div>

                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="sidebar-link group"
                        :class="[
                            {{ request()->routeIs('dashboard') ? 'true' : 'false' }} ?
                            'sidebar-link-active !text-brand-teal bg-teal-50 dark:bg-teal-900/10' :
                            'sidebar-link-inactive',
                            sidebarCollapsed ? 'justify-center px-2' : ''
                        ]"
                        title="Dashboard">
                        <i
                            class="bi bi-grid-fill text-lg {{ request()->routeIs('dashboard') ? '!text-brand-teal' : 'text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"></i>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dashboard</span>
                    </a>
                </div>
            </div>

            <!-- SECTION: MY WORK (Tasks & Attendance) -->
            <div>
                <div class="mt-3 mb-2 font-bold text-brand-blue dark:text-blue-400 uppercase tracking-widest flex items-center gap-2 transition-all duration-300"
                    :class="sidebarCollapsed ? 'justify-center text-[0px]' : 'px-4 text-[10px]'">
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">MY WORK</span>
                    <div class="h-px bg-blue-200 dark:bg-blue-900/50 flex-1" x-show="!sidebarCollapsed"></div>
                    <i x-show="sidebarCollapsed" class="bi bi-briefcase-fill text-xs text-brand-blue"></i>
                </div>

                <div class="space-y-1">
                    <a href="{{ route('tasks.index') }}" class="sidebar-link group"
                        :class="[
                            {{ request()->routeIs('tasks.*') ? 'true' : 'false' }} ?
                            'sidebar-link-active !text-brand-teal bg-teal-50 dark:bg-teal-900/10' :
                            'sidebar-link-inactive',
                            sidebarCollapsed ? 'justify-center px-2' : ''
                        ]"
                        title="My Tasks">
                        <i
                            class="bi bi-list-check text-lg {{ request()->routeIs('tasks.*') ? '!text-brand-teal' : 'text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"></i>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">My Tasks</span>
                    </a>

                    @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr', 'committee_head']))
                        <a href="{{ route('sessions.index') }}" class="sidebar-link group"
                            :class="[
                                {{ request()->routeIs('sessions.index') ? 'true' : 'false' }} ?
                                'sidebar-link-active !text-brand-teal bg-teal-50 dark:bg-teal-900/10' :
                                'sidebar-link-inactive',
                                sidebarCollapsed ? 'justify-center px-2' : ''
                            ]"
                            title="My Sessions">
                            <i
                                class="bi bi-calendar-event text-lg {{ request()->routeIs('sessions.index') ? '!text-brand-teal' : 'text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">My Sessions</span>
                        </a>
                    @endif

                    <a href="{{ route('sessions.history') }}" class="sidebar-link group"
                        :class="[
                            {{ request()->routeIs('sessions.history') ? 'true' : 'false' }} ?
                            'sidebar-link-active !text-brand-teal bg-teal-50 dark:bg-teal-900/10' :
                            'sidebar-link-inactive',
                            sidebarCollapsed ? 'justify-center px-2' : ''
                        ]"
                        title="History & Feedback">
                        <i
                            class="bi bi-clock-history text-lg {{ request()->routeIs('sessions.history') ? '!text-brand-teal' : 'text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"></i>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">History & Feedback</span>
                    </a>
                </div>
            </div>

            <!-- SECTION: TEAM MANAGEMENT -->
            @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr', 'committee_head']))
                <div>
                    <div class="mt-3 mb-2 font-bold text-brand-blue dark:text-blue-400 uppercase tracking-widest flex items-center gap-2 transition-all duration-300"
                        :class="sidebarCollapsed ? 'justify-center text-[0px]' : 'px-4 text-[10px]'">
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">TEAM MANAGMENT</span>
                        <div class="h-px bg-blue-200 dark:bg-blue-900/50 flex-1" x-show="!sidebarCollapsed"></div>
                        <i x-show="sidebarCollapsed" class="bi bi-people-fill text-xs text-brand-blue"></i>
                    </div>

                    <div class="space-y-1">
                        <a href="{{ route('committees.index') }}" class="sidebar-link group"
                            :class="[
                                {{ request()->routeIs('committees.*') ? 'true' : 'false' }} ?
                                'sidebar-link-active !text-brand-teal bg-teal-50 dark:bg-teal-900/10' :
                                'sidebar-link-inactive',
                                sidebarCollapsed ? 'justify-center px-2' : ''
                            ]"
                            title="Committees">
                            <i
                                class="bi bi-people-fill text-lg {{ request()->routeIs('committees.*') ? '!text-brand-teal' : 'text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Committees</span>
                        </a>

                        @if (Auth::user()->hasRole('top_management'))
                            <a href="{{ route('users.index') }}" class="sidebar-link group"
                                :class="[
                                    {{ request()->routeIs('users.index') ? 'true' : 'false' }} ?
                                    'sidebar-link-active !text-brand-teal bg-teal-50 dark:bg-teal-900/10' :
                                    'sidebar-link-inactive',
                                    sidebarCollapsed ? 'justify-center px-2' : ''
                                ]"
                                title="Users">
                                <i
                                    class="bi bi-person-badge-fill text-lg {{ request()->routeIs('users.index') ? '!text-brand-teal' : 'text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"></i>
                                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Users</span>
                            </a>

                            <a href="{{ route('users.pending') }}" class="sidebar-link group"
                                :class="[
                                    {{ request()->routeIs('users.pending') ? 'true' : 'false' }} ?
                                    'sidebar-link-active !text-brand-teal bg-teal-50 dark:bg-teal-900/10' :
                                    'sidebar-link-inactive',
                                    sidebarCollapsed ? 'justify-center px-2' : ''
                                ]"
                                title="Approvals">
                                <i
                                    class="bi bi-check-circle-fill text-lg {{ request()->routeIs('users.pending') ? '!text-brand-teal' : 'text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"></i>
                                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Approvals</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- SECTION: INSIGHTS -->
            @if (in_array(Auth::user()->role, ['top_management', 'board', 'committee_head']))
                <div>
                    <div class="mt-3 mb-2 font-bold text-brand-blue dark:text-blue-400 uppercase tracking-widest flex items-center gap-2 transition-all duration-300"
                        :class="sidebarCollapsed ? 'justify-center text-[0px]' : 'px-4 text-[10px]'">
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">INSIGHTS</span>
                        <div class="h-px bg-blue-200 dark:bg-blue-900/50 flex-1" x-show="!sidebarCollapsed"></div>
                        <i x-show="sidebarCollapsed" class="bi bi-bar-chart-fill text-xs text-brand-blue"></i>
                    </div>

                    <div class="space-y-1">
                        <a href="{{ route('feedbacks.index') }}" class="sidebar-link group"
                            :class="[
                                {{ request()->routeIs('feedbacks.*') ? 'true' : 'false' }} ?
                                'sidebar-link-active !text-brand-teal bg-teal-50 dark:bg-teal-900/10' :
                                'sidebar-link-inactive',
                                sidebarCollapsed ? 'justify-center px-2' : ''
                            ]"
                            title="Feedbacks">
                            <i
                                class="bi bi-chat-right-quote-fill text-lg {{ request()->routeIs('feedbacks.*') ? '!text-brand-teal' : 'text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Feedback</span>
                        </a>

                        @if (\App\Models\ReportPermission::hasAnyAccess(Auth::user()->role))
                            <a href="{{ route('reports.index') }}" class="sidebar-link group"
                                :class="[
                                    {{ request()->routeIs('reports.index') ? 'true' : 'false' }} ?
                                    'sidebar-link-active !text-brand-teal bg-teal-50 dark:bg-teal-900/10' :
                                    'sidebar-link-inactive',
                                    sidebarCollapsed ? 'justify-center px-2' : ''
                                ]"
                                title="Reports">
                                <i
                                    class="bi bi-file-earmark-bar-graph-fill text-lg {{ request()->routeIs('reports.index') ? '!text-brand-teal' : 'text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"></i>
                                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Reports</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- SECTION: SYSTEM -->
            @if (in_array(Auth::user()->role, ['top_management', 'board', 'hr']))
                <div>
                    <div class="mt-3 mb-2 font-bold text-brand-blue dark:text-blue-400 uppercase tracking-widest flex items-center gap-2 transition-all duration-300"
                        :class="sidebarCollapsed ? 'justify-center text-[0px]' : 'px-4 text-[10px]'">
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">SYSTEM</span>
                        <div class="h-px bg-blue-200 dark:bg-blue-900/50 flex-1" x-show="!sidebarCollapsed"></div>
                        <i x-show="sidebarCollapsed" class="bi bi-gear-fill text-xs text-brand-blue"></i>
                    </div>

                    <div class="space-y-1">
                        <a href="{{ route('scan.index') }}" class="sidebar-link group"
                            :class="[
                                {{ request()->routeIs('scan.*') ? 'true' : 'false' }} ?
                                'sidebar-link-active !text-brand-teal bg-teal-50 dark:bg-teal-900/10' :
                                'sidebar-link-inactive',
                                sidebarCollapsed ? 'justify-center px-2' : ''
                            ]"
                            title="Scan Station">
                            <i
                                class="bi bi-qr-code-scan text-lg {{ request()->routeIs('scan.*') ? '!text-brand-teal' : 'text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"></i>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Scan Station</span>
                        </a>

                        @if (Auth::user()->hasRole('top_management'))
                            <a href="{{ route('authorizations.index') }}" class="sidebar-link group"
                                :class="[
                                    {{ request()->routeIs('authorizations.*') ? 'true' : 'false' }} ?
                                    'sidebar-link-active !text-brand-teal bg-teal-50 dark:bg-teal-900/10' :
                                    'sidebar-link-inactive',
                                    sidebarCollapsed ? 'justify-center px-2' : ''
                                ]"
                                title="Authorizations">
                                <i
                                    class="bi bi-shield-lock-fill text-lg {{ request()->routeIs('authorizations.*') ? '!text-brand-teal' : 'text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"></i>
                                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Authorizations</span>
                            </a>

                            <a href="{{ route('report-permissions.index') }}" class="sidebar-link group"
                                :class="[
                                    {{ request()->routeIs('report-permissions.*') ? 'true' : 'false' }} ?
                                    'sidebar-link-active !text-brand-teal bg-teal-50 dark:bg-teal-900/10' :
                                    'sidebar-link-inactive',
                                    sidebarCollapsed ? 'justify-center px-2' : ''
                                ]"
                                title="Report Access">
                                <i
                                    class="bi bi-eye-slash-fill text-lg {{ request()->routeIs('report-permissions.*') ? '!text-brand-teal' : 'text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"></i>
                                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Report Access</span>
                            </a>

                            <a href="{{ route('export_import.index') }}" class="sidebar-link group"
                                :class="[
                                    {{ request()->routeIs('export_import.*') ? 'true' : 'false' }} ?
                                    'sidebar-link-active !text-brand-teal bg-teal-50 dark:bg-teal-900/10' :
                                    'sidebar-link-inactive',
                                    sidebarCollapsed ? 'justify-center px-2' : ''
                                ]"
                                title="Data Tools">
                                <i
                                    class="bi bi-database-fill-gear text-lg {{ request()->routeIs('export_import.*') ? '!text-brand-teal' : 'text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"></i>
                                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Data Tools</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <!-- Non-Authenticated Links -->
            <a href="{{ route('login') }}" class="sidebar-link sidebar-link-inactive">
                <i class="bi bi-box-arrow-in-right text-lg"></i>
                <span x-show="!sidebarCollapsed">Login</span>
            </a>
        @endauth
    </nav>

    <!-- User Profile Footer -->
    @auth
        <div class="border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-[#0f172a] relative transition-all duration-300 shrink-0"
            :class="sidebarCollapsed ? 'p-2' : 'p-4'">

            <!-- Collapsed: Logout Only -->
            <div x-show="sidebarCollapsed">
                <form method="POST" action="{{ route('logout') }}" class="flex justify-center">
                    @csrf
                    <button type="submit"
                        class="h-9 w-9 flex items-center justify-center rounded-lg text-slate-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all"
                        title="Logout">
                        <i class="bi bi-box-arrow-right text-lg"></i>
                    </button>
                </form>
            </div>

            <!-- Expanded: Full Profile -->
            <div x-show="!sidebarCollapsed"
                class="flex items-center gap-3 group px-3 py-2.5 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-800/50 transition-all cursor-pointer border border-transparent hover:border-slate-300/50 dark:hover:border-slate-700/50">
                <div
                    class="h-9 w-9 min-w-[2.25rem] rounded-full bg-gradient-to-br from-brand-blue to-brand-teal flex items-center justify-center text-white font-bold text-sm shadow-md ring-2 ring-white dark:ring-slate-800 group-hover:ring-slate-200 dark:group-hover:ring-slate-700 transition-all">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>

                <div class="flex-1 min-w-0 transition-opacity duration-200">
                    <p
                        class="text-xs font-bold text-slate-900 dark:text-slate-200 truncate group-hover:text-brand-teal transition-colors">
                        {{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-500 uppercase tracking-wider truncate">
                        {{ Auth::user()->hasRole('committee_head') ? 'Committee BOARD' : ucfirst(str_replace('_', ' ', Auth::user()->role)) }}
                    </p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="p-1.5 text-slate-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-all"
                        title="Logout">
                        <i class="bi bi-box-arrow-right text-lg"></i>
                    </button>
                </form>
            </div>
        </div>
    @endauth
</aside>
