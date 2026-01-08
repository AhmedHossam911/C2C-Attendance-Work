@props(['headers' => []])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse']) }}>
        @if (count($headers) > 0)
            <thead>
                <tr>
                    @foreach ($headers as $header)
                        <th
                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 first:pl-6 last:pr-6">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @else
            <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                <tr>
                    {{ $head ?? '' }}
                </tr>
            </thead>
        @endif

        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            {{ $slot }}
        </tbody>
    </table>
</div>
