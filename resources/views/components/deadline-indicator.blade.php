@props([
    'deadline', // Carbon date instance
    'format' => 'M d', // Date format
    'showTime' => false,
    'compact' => false,
])

@php
    $isOverdue = $deadline->isPast();
    $isToday = $deadline->isToday();
    $isTomorrow = $deadline->isTomorrow();
    $isThisWeek = $deadline->isCurrentWeek() && !$isToday && !$isTomorrow;

    // Format date
    $dateFormat = $showTime ? 'M d, h:i A' : $format;
    $formattedDate = $deadline->format($dateFormat);

    // Determine styling
    if ($isOverdue) {
        $textClass = 'text-red-600 dark:text-red-400';
        $bgClass = 'bg-red-50/50 dark:bg-red-900/10';
    } elseif ($isToday) {
        $textClass = 'text-amber-600 dark:text-amber-400';
        $bgClass = 'bg-amber-50/50 dark:bg-amber-900/10';
    } else {
        $textClass = 'text-slate-700 dark:text-slate-200';
        $bgClass = '';
    }
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex flex-col']) }}>
    <div class="flex items-center gap-1.5 font-bold {{ $textClass }} {{ $compact ? 'text-sm' : 'text-base' }}">
        <i class="bi bi-calendar-event opacity-70"></i>
        <span>{{ $formattedDate }}</span>
    </div>

    @if ($isOverdue)
        <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 mt-0.5">
            <i class="bi bi-exclamation-circle-fill"></i> Overdue
        </span>
    @elseif($isToday)
        <span class="text-amber-500 text-[10px] font-bold uppercase tracking-wider mt-0.5">Due Today</span>
    @elseif($isTomorrow)
        <span class="text-blue-500 text-[10px] font-bold uppercase tracking-wider mt-0.5">Due Tomorrow</span>
    @endif
</div>
