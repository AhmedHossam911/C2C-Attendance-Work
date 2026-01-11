@extends('Common.Layouts.guest')

@section('content')
    <!-- Header (Inside Card) -->
    <div class="flex flex-col items-center mb-8 text-center">
        <div class="relative mb-6 group">
            <div
                class="absolute inset-0 bg-brand-teal/30 blur-xl rounded-full group-hover:bg-brand-teal/50 transition-all duration-500">
            </div>
            <img src="{{ asset('logo-color.png') }}" class="h-20 w-auto relative z-10 drop-shadow-xl bg-white rounded-xl p-2"
                alt="C2C Logo">
        </div>
        <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Create Account</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Join the C2C Attendance System</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Full Name" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="bi bi-person-fill"></i>
                </div>
                <x-text-input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="pl-11 py-3" placeholder="John Doe" />
            </div>
            @error('name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email Address" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="bi bi-envelope-fill"></i>
                </div>
                <x-text-input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="pl-11 py-3" placeholder="name@example.com" />
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
                    autocomplete="new-password" class="pl-11 pr-12 py-3" placeholder="••••••••" />
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

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Confirm Password" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required
                    class="pl-11 pr-4 py-3" placeholder="••••••••" />
            </div>
        </div>

        <!-- Submit Button -->
        <!-- Submit Button -->
        <x-primary-button class="w-full justify-center py-3.5 group">
            <span class="group-hover:tracking-wider transition-all duration-300">Register</span>
            <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform ml-2"></i>
        </x-primary-button>

        <p class="text-center text-sm text-slate-500 dark:text-slate-400">
            Already have an account?
            <a href="{{ route('login') }}"
                class="font-bold text-brand-blue hover:text-brand-teal dark:text-blue-400 transition-colors">Sign in</a>
        </p>
    </form>
@endsection
