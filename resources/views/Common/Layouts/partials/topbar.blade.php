<header
    class="h-16 px-6 sticky top-0 z-30 flex items-center justify-between border-b border-slate-200 dark:border-slate-800/60 bg-slate-300 dark:bg-[#020617] transition-colors duration-300">
    <!-- Brand (Visible on Mobile & Desktop) -->
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
        <img src="{{ asset('logo-color.png') }}" alt="Logo"
            class="h-8 w-auto lg:hidden group-hover:scale-105 transition-transform bg-white rounded p-0.5">
        <div class="flex flex-col">
            <h1
                class="text-sm md:text-lg font-bold text-slate-800 dark:text-slate-100 leading-none group-hover:text-brand-teal transition-colors">
                C2C Management System
            </h1>
            <p
                class="hidden md:block text-xs text-slate-500 dark:text-slate-400 mt-1 group-hover:text-brand-blue dark:group-hover:text-blue-400 transition-colors">
                Academic Year
                {{ date('Y') }}-{{ date('Y') + 1 }}</p>
        </div>
    </a>

    <!-- Right Actions -->
    <div class="flex items-center gap-3 ml-auto">
        <!-- Date Badge -->
        <div
            class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
            <i class="bi bi-calendar4-week text-brand-teal"></i>
            <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ now()->format('D, M d') }}</span>
        </div>

        <!-- Notification Bell -->
        @auth
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="relative p-2 text-slate-500 hover:text-brand-teal hover:bg-brand-teal/10 rounded-full transition-all focus:outline-none w-10 h-10 flex items-center justify-center">
                    <i class="bi bi-bell text-lg"></i>
                    @if (Auth::user()->unreadNotifications->count() > 0)
                        <span
                            class="absolute top-1 right-1 h-4 w-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                            {{ min(Auth::user()->unreadNotifications->count(), 9) }}{{ Auth::user()->unreadNotifications->count() > 9 ? '+' : '' }}
                        </span>
                    @endif
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                    class="fixed left-4 right-4 top-20 md:absolute md:right-0 md:left-auto md:top-full md:mt-2 md:w-80 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden z-50">
                    <div
                        class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-slate-900">
                        <span class="font-bold text-slate-800 dark:text-slate-100 text-sm">Notifications</span>
                        <a href="{{ route('notifications.index') }}"
                            class="text-xs text-brand-blue hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">View
                            All</a>
                    </div>
                    <div class="max-h-[60vh] md:max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse(Auth::user()->unreadNotifications->take(5) as $notification)
                            <a href="{{ route('notifications.index') }}"
                                class="block p-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors relative group">
                                <div class="flex gap-3">
                                    <div class="shrink-0 mt-0.5">
                                        <div
                                            class="h-2.5 w-2.5 rounded-full bg-blue-600 dark:bg-blue-500 mt-1.5 ring-2 ring-white dark:ring-slate-900">
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-900 dark:text-gray-100 font-bold leading-snug">
                                            {{ $notification->data['message'] ?? 'New notification' }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 font-medium">
                                            {{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-8 text-center flex flex-col items-center text-slate-400 dark:text-slate-500">
                                <i class="bi bi-bell-slash text-2xl mb-2 opacity-50"></i>
                                <span class="text-sm font-medium">No new notifications</span>
                            </div>
                        @endforelse
                    </div>
                    @if (Auth::user()->unreadNotifications->count() > 0)
                        <div
                            class="p-2 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 text-center">
                            <form action="{{ route('notifications.readAll') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="text-xs text-slate-500 hover:text-brand-blue dark:text-slate-400 dark:hover:text-blue-400 transition-colors font-medium w-full py-1">
                                    Mark all as read
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @endauth

        <!-- Theme Toggle -->
        <button @click="darkMode = !darkMode"
            class="p-2 text-slate-500 hover:text-brand-gold hover:bg-brand-gold/10 rounded-full transition-all focus:outline-none w-10 h-10 flex items-center justify-center">
            <i class="bi text-lg transition-transform duration-500 rotate-0 dark:rotate-180"
                :class="darkMode ? 'bi-sun-fill' : 'bi-moon-stars-fill'"></i>
        </button>

        <!-- Mobile Menu Button -->
        <button @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden p-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
            <i class="bi text-2xl" :class="sidebarOpen ? 'bi-x-lg' : 'bi-list'"></i>
        </button>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2" @click.away="sidebarOpen = false"
        class="absolute top-16 left-0 w-full bg-slate-300 dark:bg-[#020617] border-b border-slate-200 dark:border-slate-800 shadow-xl lg:hidden max-h-[calc(100vh-4rem)] overflow-y-auto z-40">
        @include('Common.Layouts.partials.mobile-nav')
    </div>
</header>
