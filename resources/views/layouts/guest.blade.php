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
        href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'sans-serif'],
                    },
                    colors: {
                        'c2c-blue': {
                            50: '#eef4ff',
                            100: '#e0eafe',
                            200: '#c5d7fc',
                            300: '#a2bbf8',
                            400: '#7a9af3',
                            500: '#5777eb',
                            600: '#3856dd',
                            700: '#2b43c6',
                            800: '#2636a0',
                            900: '#1e3b8a',
                            950: '#192361',
                        },
                        'c2c-teal': {
                            50: '#effcf9',
                            100: '#cbf9f1',
                            200: '#9bf3e4',
                            300: '#64e8d3',
                            400: '#34d5bd',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                            950: '#042f2e',
                        },
                        brand: {
                            blue: '#1E3B8A',
                            teal: '#14B8A6',
                            gold: '#FFC107',
                        }
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .glass {
                @apply bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-lg border border-slate-50/20 dark:border-slate-800/50;
            }
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body
    class="font-sans antialiased h-full bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 selection:bg-brand-teal selection:text-white relative overflow-hidden">

    <!-- Background Decor -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-[20%] -left-[10%] w-[60%] h-[60%] rounded-full bg-brand-blue/10 blur-3xl"></div>
        <div class="absolute top-[30%] -right-[10%] w-[50%] h-[50%] rounded-full bg-brand-teal/10 blur-3xl"></div>
    </div>

    <div class="min-h-screen flex flex-col items-center justify-center p-6">
        <div class="w-full max-w-md">
            <!-- Theme Toggle (Above Card) -->
            <div class="flex justify-end mb-4">
                <button
                    onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light')"
                    class="p-2 text-slate-500 hover:text-brand-gold bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm rounded-full transition-all border border-transparent hover:border-slate-200 dark:hover:border-slate-700 shadow-sm">
                    <i class="bi bi-moon-stars-fill dark:hidden"></i>
                    <i class="bi bi-sun-fill hidden dark:block"></i>
                </button>
            </div>


            <!-- Main Content -->
            <div
                class="glass rounded-3xl shadow-2xl shadow-brand-blue/5 dark:shadow-black/20 overflow-hidden p-8 border-slate-200/50 dark:border-slate-700/50">
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
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
