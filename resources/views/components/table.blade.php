@props(['headers' => []])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse']) }}>
        @if (count($headers) > 0)
            <thead>
                <tr>
                    @foreach ($headers as $header)
                        <th
                            class="px-6 py-4 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider bg-slate-100 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 first:pl-6 last:pr-6">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @else
            <thead class="bg-slate-100 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
                <tr>
                    {{ $head ?? '' }}
                </tr>
            </thead>
        @endif

        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            {{ $slot }}
        </tbody>
    </table>
</div>
