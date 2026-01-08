@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-6">Pending Approvals</h2>

    <x-card class="p-0" :embedded="true">
        @if ($users->isEmpty())
            <div class="p-8 text-center text-slate-500">
                <i class="bi bi-check-circle text-4xl mb-2 opacity-50 block"></i>
                No pending users.
            </div>
        @else
            <x-table :headers="['Name', 'Email', 'Registered At', 'Actions']">
                @foreach ($users as $user)
                    <x-table.tr>
                        <x-table.td>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->name }}</span>
                        </x-table.td>
                        <x-table.td>{{ $user->email }}</x-table.td>
                        <x-table.td>{{ $user->created_at->diffForHumans() }}</x-table.td>
                        <x-table.td>
                            <div class="flex items-center gap-2">
                                <form action="{{ route('users.approve', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-bold rounded-lg transition-colors">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('users.reject', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition-colors">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </x-table.td>
                    </x-table.tr>
                @endforeach
            </x-table>
        @endif
    </x-card>
@endsection
