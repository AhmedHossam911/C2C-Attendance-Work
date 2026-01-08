<header
    class="h-16 px-6 sticky top-0 z-30 flex items-center justify-between border-b border-slate-200 dark:border-slate-800/60 bg-white dark:bg-[#020617] transition-colors duration-300">
    <!-- Brand (Visible on Mobile & Desktop) -->
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
        <img src="{{ asset('logo-color.png') }}" alt="Logo"
            class="h-8 w-auto lg:hidden group-hover:scale-105 transition-transform">
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
        class="absolute top-16 left-0 w-full bg-white dark:bg-[#020617] border-b border-slate-200 dark:border-slate-800 shadow-xl lg:hidden max-h-[calc(100vh-4rem)] overflow-y-auto z-40">
        @include('layouts.partials.mobile-nav')
    </div>
</header>
