<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'C2C Attendance') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
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
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .glass {
                @apply bg-slate-50/70 dark:bg-slate-900/70 backdrop-blur-md border border-slate-50/20 dark:border-slate-800/50;
            }

            .card-hover {
                @apply transition-all duration-300 hover:shadow-lg hover:-translate-y-1;
            }
        }
    </style>
</head>

<body
    class="h-full bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-slate-100 antialiased selection:bg-c2c-teal-500 selection:text-slate-50">

    <div class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden">

        <!-- Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
            <div class="absolute -top-[30%] -left-[10%] w-[70%] h-[70%] rounded-full bg-c2c-blue-500/10 blur-3xl"></div>
            <div class="absolute top-[40%] -right-[10%] w-[60%] h-[60%] rounded-full bg-c2c-teal-500/10 blur-3xl"></div>
        </div>

        <div class="w-full max-w-7xl mx-auto px-6 lg:px-8">
            <nav class="absolute top-0 left-0 w-full p-6 flex justify-between items-center z-20">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo-color.png') }}" alt="Logo"
                        class="h-10 w-auto bg-slate-50 rounded-full p-1 shadow-sm dark:bg-white">
                    <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50">C2C
                        Attendance</span>
                </div>

                @if (Route::has('login'))
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-sm font-medium text-slate-600 hover:text-c2c-blue-600 dark:text-slate-300 dark:hover:text-slate-50 transition-colors">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-sm font-medium text-slate-600 hover:text-c2c-blue-600 dark:text-slate-300 dark:hover:text-slate-50 transition-colors">Log
                                in</a>
                        @endauth
                    </div>
                @endif
            </nav>

            <main class="grid lg:grid-cols-2 gap-12 items-center py-16 lg:py-24">
                <!-- Left Column: Hero Text -->
                <div class="text-center lg:text-left space-y-8">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-c2c-blue-50 dark:bg-c2c-blue-900/30 border border-c2c-blue-100 dark:border-c2c-blue-800 text-c2c-blue-700 dark:text-c2c-blue-300 text-xs font-semibold uppercase tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-c2c-blue-500 animate-pulse"></span>
                        System Online
                    </div>

                    <h1
                        class="text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50 leading-tight">
                        Streamline Your <br>
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-c2c-blue-600 to-c2c-teal-500">Attendance
                            Flow</span>
                    </h1>

                    <p class="text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        The official attendance tracking system for C2C. Manage committees, sessions, and members
                        efficiently with our secure and automated platform.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-c2c-blue-600 hover:bg-c2c-blue-700 text-slate-50 font-semibold shadow-lg shadow-c2c-blue-600/20 transition-all hover:-translate-y-1 text-center">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-c2c-blue-600 hover:bg-c2c-blue-700 text-slate-50 font-semibold shadow-lg shadow-c2c-blue-600/20 transition-all hover:-translate-y-1 text-center">
                                Log in to Portal
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Right Column: Abstract Visualization or 3D Element -->
                <div class="hidden lg:block relative">
                    <div
                        class="relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 aspect-[4/3] group">
                        <!-- Mockup of the dashboard -->
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-c2c-blue-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 flex items-center justify-center">
                            <div
                                class="text-center space-y-4 opacity-50 transform group-hover:scale-105 transition-transform duration-700">
                                <div
                                    class="w-16 h-16 bg-c2c-blue-500/20 rounded-2xl mx-auto flex items-center justify-center backdrop-blur-sm">
                                    <svg class="w-8 h-8 text-c2c-blue-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="h-2 w-32 bg-slate-200 dark:bg-slate-700 rounded mx-auto"></div>
                                <div class="h-2 w-24 bg-slate-200 dark:bg-slate-700 rounded mx-auto"></div>
                            </div>
                        </div>

                        <!-- Overlay Glass Card -->
                        <div
                            class="absolute bottom-6 left-6 right-6 bg-slate-50/10 dark:bg-slate-800/40 backdrop-blur-md rounded-xl p-4 border border-slate-50/20 dark:border-slate-50/10 text-slate-50 shadow-xl translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500 delay-100">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 rounded-full bg-green-500/20 flex items-center justify-center text-green-400">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 dark:text-slate-50">Seamless Check-ins</p>
                                    <p class="text-xs text-slate-600 dark:text-slate-300">QR Code scanning & Real-time
                                        tracking</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer
                class="mt-16 py-8 text-center text-sm text-slate-500 dark:text-slate-400 border-t border-slate-200 dark:border-slate-800">
                <p>&copy; {{ date('Y') }} C2C Attendance System.</p>
            </footer>
        </div>
    </div>
</body>

</html>
