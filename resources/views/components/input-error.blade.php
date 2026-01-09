@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 dark:text-red-400 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-center gap-1">
                <i class="bi bi-exclamation-circle"></i>
                {{ $message }}
            </li>
        @endforeach
    </ul>
@endif
