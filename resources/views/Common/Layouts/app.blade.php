<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" x-data="{
    sidebarOpen: false,
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
    darkMode: localStorage.getItem('theme') === 'dark'
}"
    :class="{ 'dark': darkMode }" x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'));
    $watch('sidebarCollapsed', val => localStorage.setItem('sidebarCollapsed', val))">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>C2C Management System</title>
    <link rel="icon" href="{{ asset('logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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

            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                @apply font-bold tracking-tight text-slate-900 dark:text-white;
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

            .hover-lift {
                @apply transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg;
            }

            .sidebar-link {
                @apply flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 border border-transparent;
            }

            .sidebar-link-active {
                @apply bg-c2c-blue-50 text-c2c-blue-700 dark:bg-c2c-blue-900/20 dark:text-c2c-blue-400 border-c2c-blue-200 dark:border-c2c-blue-800/50 shadow-sm;
            }

            .sidebar-link-inactive {
                @apply text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-800/50;
            }
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- HTML5-QRCode -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>

<body
    class="font-sans antialiased h-full bg-slate-100 dark:bg-[#0b1121] text-slate-800 dark:text-slate-100 transition-colors duration-300">

    <div class="min-h-screen flex flex-col">

        @include('Common.Layouts.partials.sidebar')

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 min-h-screen"
            :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-72'">

            @include('Common.Layouts.partials.topbar')

            <!-- Main Content Area -->
            <main class="flex-1 p-4 md:p-8 w-full max-w-[1600px] mx-auto pb-8 lg:pb-0">
                <div class="space-y-6">


                    @yield('content')
                </div>
            </main>

            <!-- Spacer for Fixed Footer (Desktop Only) -->
            <div class="hidden lg:block h-32 w-full"></div>

            @include('Common.Layouts.partials.footer')
        </div>
    </div>

    <x-toast />
    @yield('scripts')
</body>

</html>
