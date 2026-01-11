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
                        brand: {
                            blue: '#1e3a8a',
                            /* Brand Blue */
                            teal: '#14b8a6',
                            /* Brand Teal */
                            gold: '#FFC107',
                            /* Gold/Yellow from original */
                            dark: '#0F172A',
                        },
                        // Expanded palette based on brand
                        'c2c-blue': {
                            50: '#eef4ff',
                            100: '#e0eafe',
                            200: '#c5d7fc',
                            300: '#a2bbf8',
                            400: '#7a9af3',
                            500: '#5777eb',
                            49: '#5777eb', // Typo fix in previous palette if any
                            600: '#3856dd',
                            700: '#2b43c6',
                            800: '#2636a0',
                            900: '#1E3B8A',
                            /* Adjusted to match brand */
                            950: '#111827',
                        },
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .glass {
                @apply bg-slate-200/80 dark:bg-gray-900/80 backdrop-blur-lg border-b border-slate-200/50 dark:border-gray-800/50;
            }

            .sidebar-link {
                @apply flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 border border-transparent;
            }

            .sidebar-link-active {
                @apply bg-brand-blue/10 text-brand-blue border-brand-blue/20 shadow-sm;
            }

            .sidebar-link-inactive {
                @apply text-slate-500 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-200 dark:hover:bg-slate-800/50;
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.5s ease-out forwards;
            }

            .animation-delay-100 {
                animation-delay: 100ms;
            }

            .animation-delay-200 {
                animation-delay: 200ms;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        }

        [x-cloak] {
            display: none !important;
        }

        /* Scrollbar customization for webkit */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #374151;
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- HTML5-QRCode -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>

<body
    class="font-sans antialiased h-full bg-[#e2e8f0] dark:bg-[#020617] text-slate-900 dark:text-slate-100 transition-colors duration-300">

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
