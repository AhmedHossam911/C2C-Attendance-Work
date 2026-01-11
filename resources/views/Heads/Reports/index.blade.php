@extends('Common.Layouts.app')

@section('content')
    <div class="mb-8 px-2 md:px-0">
        <h2 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Reports Dashboard</h2>
        <p class="text-sm md:text-base text-slate-500 dark:text-slate-400 mt-2 max-w-2xl">
            Overview of system performance, member engagement, and strategic insights.
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        @if (\App\Models\ReportPermission::check('committees', Auth::user()->role))
            <div onclick="window.location='{{ route('reports.committees') }}'"
                class="group bg-slate-300 dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-lg transition-all duration-300 cursor-pointer flex flex-col items-center text-center relative overflow-hidden">
                <div
                    class="h-16 w-16 mb-4 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 mb-2">Committee Attendance</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Detailed logs and stats per committee.</p>
                <div
                    class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-500">
                </div>
            </div>
        @endif

        @if (\App\Models\ReportPermission::check('ghost_members', Auth::user()->role))
            <div onclick="window.location='{{ route('reports.ghost_members') }}'"
                class="group bg-slate-300 dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 hover:border-red-400 dark:hover:border-red-500 hover:shadow-lg transition-all duration-300 cursor-pointer flex flex-col items-center text-center relative overflow-hidden">
                <div
                    class="h-16 w-16 mb-4 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-500 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">
                    <i class="bi bi-person-x-fill"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 mb-2">Ghost Members</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Identify inactive members (0% stats).</p>
                <div
                    class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-red-500 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-500">
                </div>
            </div>
        @endif

        @if (\App\Models\ReportPermission::check('top_performers', Auth::user()->role))
            <div onclick="window.location='{{ route('reports.top_performers') }}'"
                class="group bg-slate-300 dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 hover:border-amber-400 dark:hover:border-amber-500 hover:shadow-lg transition-all duration-300 cursor-pointer flex flex-col items-center text-center relative overflow-hidden">
                <div
                    class="h-16 w-16 mb-4 rounded-2xl bg-amber-50 dark:bg-amber-900/20 text-amber-500 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 mb-2">Top Performers</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Leaderboard of engaged members.</p>
                <div
                    class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-500">
                </div>
            </div>
        @endif

        @if (\App\Models\ReportPermission::check('committee_performance', Auth::user()->role))
            <div onclick="window.location='{{ route('reports.committee_performance') }}'"
                class="group bg-slate-300 dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 hover:border-purple-400 dark:hover:border-purple-500 hover:shadow-lg transition-all duration-300 cursor-pointer flex flex-col items-center text-center relative overflow-hidden">
                <div
                    class="h-16 w-16 mb-4 rounded-2xl bg-purple-50 dark:bg-purple-900/20 text-purple-500 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">
                    <i class="bi bi-bar-chart-fill"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 mb-2">Committee Stats</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Compare performance across teams.</p>
                <div
                    class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-purple-500 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-500">
                </div>
            </div>
        @endif

        @if (\App\Models\ReportPermission::check('session_quality', Auth::user()->role))
            <div onclick="window.location='{{ route('reports.session_quality') }}'"
                class="group bg-slate-300 dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 hover:border-teal-400 dark:hover:border-teal-500 hover:shadow-lg transition-all duration-300 cursor-pointer flex flex-col items-center text-center relative overflow-hidden">
                <div
                    class="h-16 w-16 mb-4 rounded-2xl bg-teal-50 dark:bg-teal-900/20 text-teal-500 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">
                    <i class="bi bi-star-fill"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 mb-2">Session Quality</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Rank sessions by member feedback.</p>
                <div
                    class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-teal-500 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-500">
                </div>
            </div>
        @endif

        @if (\App\Models\ReportPermission::check('attendance_trends', Auth::user()->role))
            <div onclick="window.location='{{ route('reports.attendance_trends') }}'"
                class="group bg-slate-300 dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-lg transition-all duration-300 cursor-pointer flex flex-col items-center text-center relative overflow-hidden">
                <div
                    class="h-16 w-16 mb-4 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-500 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 mb-2">Attendance Trends</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Visualize engagement over time.</p>
                <div
                    class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-500">
                </div>
            </div>
        @endif

        @if (\App\Models\ReportPermission::check('member', Auth::user()->role))
            <div onclick="window.location='{{ route('reports.member') }}'"
                class="group bg-slate-300 dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 hover:border-pink-400 dark:hover:border-pink-500 hover:shadow-lg transition-all duration-300 cursor-pointer flex flex-col items-center text-center relative overflow-hidden">
                <div
                    class="h-16 w-16 mb-4 rounded-2xl bg-pink-50 dark:bg-pink-900/20 text-pink-500 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">
                    <i class="bi bi-search"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100 mb-2">Member Search</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Deep dive into individual member records.</p>
                <div
                    class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-pink-500 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-500">
                </div>
            </div>
        @endif
    </div>
@endsection
