@extends('layouts.guest')

@section('content')
    <!-- Header (Inside Card) -->
    <div class="flex flex-col items-center mb-8 text-center">
        <div class="relative mb-6 group">
            <div
                class="absolute inset-0 bg-brand-teal/30 blur-xl rounded-full group-hover:bg-brand-teal/50 transition-all duration-500">
            </div>
            <img src="{{ asset('logo-color.png') }}" class="h-20 w-auto relative z-10 drop-shadow-xl" alt="C2C Logo">
        </div>
        <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Welcome Back</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Sign in to access your dashboard</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Email Address
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="bi bi-envelope-fill"></i>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username"
                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue outline-none transition-all text-slate-900 dark:text-white placeholder-slate-400 font-medium"
                    placeholder="name@example.com">
            </div>
            @error('email')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="bi bi-lock-fill"></i>
                </div>
                <input id="password" :type="show ? 'text' : 'password'" name="password" required
                    autocomplete="current-password"
                    class="w-full pl-11 pr-12 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue outline-none transition-all text-slate-900 dark:text-white placeholder-slate-400 font-medium"
                    placeholder="••••••••">
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
                    class="rounded border-slate-300 text-brand-blue shadow-sm focus:ring-brand-blue/50 bg-slate-50 dark:bg-slate-800 dark:border-slate-600">
                <span
                    class="ml-2 text-sm text-slate-600 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-slate-200 transition-colors">Remember
                    me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    class="text-sm font-medium text-brand-blue hover:text-brand-teal dark:text-blue-400 dark:hover:text-teal-400 transition-colors">
                    Forgot Password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full py-3.5 px-4 bg-gradient-to-r from-brand-blue to-brand-blue/90 hover:to-brand-blue text-white font-bold rounded-xl shadow-lg shadow-brand-blue/20 hover:shadow-brand-blue/30 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2 group">
            <span class="group-hover:tracking-wider transition-all duration-300">Sign In</span>
            <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
        </button>
    </form>
@endsection
