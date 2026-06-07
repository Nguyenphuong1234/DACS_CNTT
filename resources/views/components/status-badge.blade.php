@props(['status'])

@php
    $labels = \App\Models\Order::statuses();
    $classes = [
        'pending' => 'bg-amber-100 text-amber-800 ring-amber-200',
        'confirmed' => 'bg-sky-100 text-sky-800 ring-sky-200',
        'packing' => 'bg-indigo-100 text-indigo-800 ring-indigo-200',
        'shipping' => 'bg-blue-100 text-blue-800 ring-blue-200',
        'completed' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
        'cancelled' => 'bg-rose-100 text-rose-800 ring-rose-200',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.($classes[$status] ?? 'bg-zinc-100 text-zinc-800 ring-zinc-200')]) }}>
    {{ $labels[$status] ?? $status }}
</span>
