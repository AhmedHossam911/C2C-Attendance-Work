<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'C2C Attendance') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <!-- Tailwind CSS CDN (Restored for reliability) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            blue: '#1e3a8a',
                            teal: '#14b8a6',
                        },
                        // New C2C Palette
                        'c2c-blue': {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        },
                        'c2c-teal': {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                            950: '#042f2e',
                        }
                    },
                    backgroundImage: {
                        'gradient-brand': 'linear-gradient(to right, #0d9488, #1d4ed8)', // teal-600 to blue-700
                    },
                    boxShadow: {
                        'glass': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            body {
                @apply bg-slate-100 text-slate-800 dark:bg-[#0b1121] dark:text-slate-100 antialiased;
            }

            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            ::-webkit-scrollbar-track {
                background: transparent;
            }

            ::-webkit-scrollbar-thumb {
                @apply bg-slate-300 dark:bg-slate-700 rounded-full hover:bg-slate-400 dark:hover:bg-slate-600 border-2 border-transparent bg-clip-content;
            }
        }

        @layer utilities {
            .glass {
                @apply bg-white/70 dark:bg-[#0f172a]/70 backdrop-blur-xl border border-white/40 dark:border-slate-700/50 shadow-sm;
            }

            .glass-card {
                @apply bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 shadow-sm rounded-xl;
            }
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body
    class="font-sans antialiased h-full bg-slate-100 dark:bg-[#0b1121] text-slate-800 dark:text-slate-100 relative overflow-hidden transition-colors duration-300">

    <!-- Background Decor -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-c2c-blue-500/10 blur-3xl"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-c2c-teal-500/10 blur-3xl"></div>
    </div>

    <div class="min-h-screen flex flex-col items-center justify-center p-6">
        <div class="w-full max-w-md">
            <!-- Main Content -->
            <div
                class="glass-card shadow-2xl shadow-c2c-blue-500/10 dark:shadow-none overflow-hidden transition-all duration-300">

                <!-- Full Width Theme Toggle -->
                <button
                    onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light')"
                    class="w-full py-3 bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700 flex items-center justify-center gap-2 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-600 dark:text-slate-400 font-medium text-sm group">
                    <i class="bi bi-moon-stars-fill dark:hidden group-hover:text-c2c-blue-600 transition-colors"></i>
                    <i class="bi bi-sun-fill hidden dark:block group-hover:text-yellow-400 transition-colors"></i>
                    <span class="dark:hidden">Switch to Dark Mode</span>
                    <span class="hidden dark:inline">Switch to Light Mode</span>
                </button>

                <div class="p-8">
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('status') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <p class="mt-8 text-center text-xs text-slate-400 dark:text-slate-600">
                &copy; {{ date('Y') }} C2C Attendance System
            </p>
        </div>
    </div>

    <script>
        // Init Theme
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>

</html>
