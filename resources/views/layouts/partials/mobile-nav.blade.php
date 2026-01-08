<nav class="flex flex-col space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}"
        class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-brand-teal/10 text-brand-teal' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
        <i class="bi bi-grid-fill text-lg"></i>
        <span>Dashboard</span>
    </a>

    @auth
        <!-- ACADEMIC -->
        @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board') || Auth::user()->hasRole('hr'))
            <div class="pt-2 pb-1 px-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                Academic
            </div>
            <a href="{{ route('committees.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('committees.*') ? 'bg-brand-teal/10 text-brand-teal' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <i class="bi bi-people-fill text-lg"></i>
                <span>Committees</span>
            </a>
            <a href="{{ route('sessions.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('sessions.*') ? 'bg-brand-teal/10 text-brand-teal' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <i class="bi bi-calendar-event-fill text-lg"></i>
                <span>Sessions</span>
            </a>
        @endif

        <!-- OPERATIONS -->
        @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board') || Auth::user()->hasRole('hr'))
            <div class="pt-2 pb-1 px-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                Operations
            </div>
            <a href="{{ route('scan.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('scan.*') ? 'bg-brand-teal/10 text-brand-teal' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <i class="bi bi-qr-code-scan text-lg"></i>
                <span>Scan QR</span>
            </a>

            @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board') || Auth::user()->hasRole('hr'))
                <a href="{{ route('reports.index') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('reports.index') ? 'bg-brand-teal/10 text-brand-teal' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-file-text-fill text-lg"></i>
                    <span>Reports</span>
                </a>
            @endif

            <a href="{{ route('reports.member') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('reports.member') ? 'bg-brand-teal/10 text-brand-teal' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <i class="bi bi-person-lines-fill text-lg"></i>
                <span>Member Search</span>
            </a>
        @endif

        <!-- SYSTEM ADMIN -->
        @if (Auth::user()->hasRole('top_management'))
            <div class="pt-2 pb-1 px-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                System Admin
            </div>
            <a href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('users.index') ? 'bg-brand-teal/10 text-brand-teal' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <i class="bi bi-shield-shaded text-lg"></i>
                <span>All Users</span>
            </a>
            <a href="{{ route('users.pending') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('users.pending') ? 'bg-brand-teal/10 text-brand-teal' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <i class="bi bi-person-check-fill text-lg"></i>
                <span>Approvals</span>
            </a>
            <a href="{{ route('authorizations.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('authorizations.*') ? 'bg-brand-teal/10 text-brand-teal' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <i class="bi bi-key-fill text-lg"></i>
                <span>HR Access</span>
            </a>
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
