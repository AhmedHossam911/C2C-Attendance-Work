@extends('Common.Layouts.guest')

@section('content')
    <!-- Header (Inside Card) -->
    <div class="flex flex-col items-center mb-8 text-center">
        <div class="relative mb-6">
            <img src="{{ asset('logo-color.png') }}" class="h-20 w-auto relative z-10 drop-shadow-sm bg-white rounded-xl p-2"
                alt="C2C Logo">
        </div>
        <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Welcome Back</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Sign in to access your dashboard</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email Address" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="bi bi-envelope-fill"></i>
                </div>
                <x-text-input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username" class="pl-11 py-3" placeholder="name@example.com" />
            </div>
            @error('email')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <x-input-label for="password" value="Password" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="bi bi-lock-fill"></i>
                </div>
                <x-text-input id="password" x-bind:type="show ? 'text' : 'password'" name="password" required
                    autocomplete="current-password" class="pl-11 pr-12 py-3" placeholder="••••••••" />
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors focus:outline-none">
                    <i class="bi" :class="show ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-slate-300 text-c2c-blue-600 shadow-sm focus:ring-c2c-blue-500/50 bg-slate-50 dark:bg-slate-800 dark:border-slate-600">
                <span
                    class="ml-2 text-sm text-slate-600 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-slate-200 transition-colors">Remember
                    me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    class="text-sm font-medium text-c2c-blue-600 hover:text-c2c-teal-600 dark:text-c2c-blue-400 dark:hover:text-c2c-teal-400 transition-colors">
                    Forgot Password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <x-primary-button class="w-full justify-center py-3.5 group relative" ::disabled="loading">
            <div x-show="!loading" class="flex items-center">
                <span class="group-hover:tracking-wider transition-all duration-300">Sign In</span>
                <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform ml-2"></i>
            </div>
            <div x-show="loading" class="flex items-center absolute inset-0 justify-center" style="display: none;">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span>Signing In...</span>
            </div>
        </x-primary-button>
    </form>
@endsection
