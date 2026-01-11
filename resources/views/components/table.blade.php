@props(['headers' => []])

<div class="overflow-x-auto custom-scrollbar">
    <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse']) }}>
        @if (count($headers) > 0)
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-700">
                    @foreach ($headers as $header)
                        <th
                            class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50/80 dark:bg-slate-800/80 first:pl-6 last:pr-6 whitespace-nowrap">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @else
            <thead class="border-b border-slate-200 dark:border-slate-700">
                <tr class="bg-slate-50/80 dark:bg-slate-800/80">
                    {{ $head ?? '' }}
                </tr>
            </thead>
        @endif

        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-slate-50 dark:bg-slate-900">
            {{ $slot }}
        </tbody>
    </table>
</div>
