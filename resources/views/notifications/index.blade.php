@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Notifications</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    @if (Auth::user()->unreadNotifications->count() > 0)
                        You have {{ Auth::user()->unreadNotifications->count() }} unread notifications
                    @else
                        All caught up!
                    @endif
                </p>
            </div>
            @if (Auth::user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.readAll') }}" method="POST">
                    @csrf
                    <x-secondary-button type="submit" class="py-2.5">
                        <i class="bi bi-check-all mr-1"></i> Mark All as Read
                    </x-secondary-button>
                </form>
            @endif
        </div>

        <x-card class="p-0" :embedded="true">
            <div class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse ($notifications as $notification)
                    <div
                        class="flex items-start gap-4 p-5 {{ $notification->read_at ? 'bg-slate-50 dark:bg-slate-800/50' : 'bg-blue-50/50 dark:bg-blue-900/20' }} hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        {{-- Icon --}}
                        <div class="flex-shrink-0">
                            @php
                                $type = $notification->data['type'] ?? '';
                                $iconConfig = match ($type) {
                                    'new_task' => [
                                        'icon' => 'bi-list-task',
                                        'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                                        'text' => 'text-blue-600 dark:text-blue-400',
                                    ],
                                    'task_deadline' => [
                                        'icon' => 'bi-alarm',
                                        'bg' => 'bg-amber-100 dark:bg-amber-900/30',
                                        'text' => 'text-amber-600 dark:text-amber-400',
                                    ],
                                    'late_submission' => [
                                        'icon' => 'bi-clock-history',
                                        'bg' => 'bg-red-100 dark:bg-red-900/30',
                                        'text' => 'text-red-600 dark:text-red-400',
                                    ],
                                    'task_reviewed' => [
                                        'icon' => 'bi-check-circle',
                                        'bg' => 'bg-green-100 dark:bg-green-900/30',
                                        'text' => 'text-green-600 dark:text-green-400',
                                    ],
                                    'session_feedback_enabled' => [
                                        'icon' => 'bi-chat-square-text',
                                        'bg' => 'bg-purple-100 dark:bg-purple-900/30',
                                        'text' => 'text-purple-600 dark:text-purple-400',
                                    ],
                                    default => [
                                        'icon' => 'bi-bell',
                                        'bg' => 'bg-slate-200 dark:bg-slate-700',
                                        'text' => 'text-slate-600 dark:text-slate-400',
                                    ],
                                };
                            @endphp
                            <div class="p-2.5 {{ $iconConfig['bg'] }} rounded-xl">
                                <i class="bi {{ $iconConfig['icon'] }} text-lg {{ $iconConfig['text'] }}"></i>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-sm font-medium text-slate-800 dark:text-slate-200 {{ !$notification->read_at ? 'font-semibold' : '' }}">
                                {{ $notification->data['message'] ?? 'You have a new notification.' }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 flex items-center gap-1">
                                <i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                @if (!$notification->read_at)
                                    <span class="inline-block w-2 h-2 bg-blue-500 rounded-full ml-2"></span>
                                @endif
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if (isset($notification->data['url']))
                                <a href="{{ $notification->data['url'] }}"
                                    class="px-3.5 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50 font-bold rounded-xl text-xs transition-all">
                                    View
                                </a>
                            @endif
                            @if (!$notification->read_at)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-xl transition-all"
                                        title="Mark as Read">
                                        <i class="bi bi-check2-circle text-lg"></i>
                                    </button>
                                </form>
                            @else
                                <span class="p-2 text-slate-300 dark:text-slate-600">
                                    <i class="bi bi-check2-circle text-lg"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <div class="p-4 bg-slate-200 dark:bg-slate-700 rounded-full inline-block mb-4">
                            <i class="bi bi-bell-slash text-3xl text-slate-400"></i>
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 font-medium">No notifications yet</p>
                        <p class="text-sm text-slate-500 dark:text-slate-500 mt-1">We'll notify you when something happens
                        </p>
                    </div>
                @endforelse
            </div>
        </x-card>

        {{-- Pagination --}}
        @if ($notifications->hasPages())
            <div class="mt-6">
                {{-- Desktop Pagination --}}
                <div class="hidden sm:flex items-center justify-between">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Showing <span
                            class="font-semibold text-slate-800 dark:text-slate-200">{{ $notifications->firstItem() }}</span>
                        to <span
                            class="font-semibold text-slate-800 dark:text-slate-200">{{ $notifications->lastItem() }}</span>
                        of <span
                            class="font-semibold text-slate-800 dark:text-slate-200">{{ $notifications->total() }}</span>
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($notifications->onFirstPage())
                            <span
                                class="inline-flex items-center justify-center w-10 h-10 text-slate-400 bg-slate-200 dark:bg-slate-700 cursor-not-allowed rounded-xl">
                                <i class="bi bi-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $notifications->previousPageUrl() }}"
                                class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-blue-600 transition-all">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        @endif

                        @foreach ($notifications->getUrlRange(1, $notifications->lastPage()) as $page => $url)
                            @if ($page == $notifications->currentPage())
                                <span
                                    class="inline-flex items-center justify-center w-10 h-10 text-slate-50 font-bold bg-blue-600 rounded-xl shadow-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 font-medium bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-blue-600 transition-all">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if ($notifications->hasMorePages())
                            <a href="{{ $notifications->nextPageUrl() }}"
                                class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-blue-600 transition-all">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        @else
                            <span
                                class="inline-flex items-center justify-center w-10 h-10 text-slate-400 bg-slate-200 dark:bg-slate-700 cursor-not-allowed rounded-xl">
                                <i class="bi bi-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Mobile Pagination --}}
                <div class="sm:hidden flex items-center justify-between">
                    <div class="flex-1 flex justify-start">
                        @if ($notifications->onFirstPage())
                            <span
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-200 dark:bg-slate-700 cursor-not-allowed rounded-xl">
                                <i class="bi bi-chevron-left mr-1"></i> Previous
                            </span>
                        @else
                            <a href="{{ $notifications->previousPageUrl() }}"
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                                <i class="bi bi-chevron-left mr-1"></i> Previous
                            </a>
                        @endif
                    </div>

                    <div class="px-4 text-sm text-slate-600 dark:text-slate-400 font-medium">
                        {{ $notifications->currentPage() }} / {{ $notifications->lastPage() }}
                    </div>

                    <div class="flex-1 flex justify-end">
                        @if ($notifications->hasMorePages())
                            <a href="{{ $notifications->nextPageUrl() }}"
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                                Next <i class="bi bi-chevron-right ml-1"></i>
                            </a>
                        @else
                            <span
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-200 dark:bg-slate-700 cursor-not-allowed rounded-xl">
                                Next <i class="bi bi-chevron-right ml-1"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
