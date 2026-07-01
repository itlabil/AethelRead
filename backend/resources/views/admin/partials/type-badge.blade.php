@php
    $colors = [
        'manga'   => 'bg-pink-100 text-pink-700',
        'manhwa'  => 'bg-blue-100 text-blue-700',
        'manhua'  => 'bg-orange-100 text-orange-700',
        'other'   => 'bg-gray-100 text-gray-600',
    ];
    $color = $colors[$type] ?? 'bg-gray-100 text-gray-600';
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
    {{ ucfirst($type) }}
</span>