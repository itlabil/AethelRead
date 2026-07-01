@if ($paginator->hasPages())
<nav class="flex items-center justify-between">

    {{-- Info --}}
    <p class="text-sm text-gray-500">
        Showing
        <span class="font-medium">{{ $paginator->firstItem() }}</span>
        to
        <span class="font-medium">{{ $paginator->lastItem() }}</span>
        of
        <span class="font-medium">{{ $paginator->total() }}</span>
        results
    </p>

    {{-- Pages --}}
    <div class="flex items-center gap-1">

        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1.5 text-sm text-gray-300 rounded-lg cursor-not-allowed">
                &lsaquo;
            </span>
        @else
            
            <a href="{{ $paginator->previousPageUrl() }}"
                class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition"
            >
                &lsaquo;
            </a>
        @endif

        {{-- Page Numbers --}}
        @php
            $currentPage = $paginator->currentPage();
            $lastPage    = $paginator->lastPage();
            $start       = max(1, $currentPage - 2);
            $end         = min($lastPage, $currentPage + 2);
        @endphp

        {{-- First page --}}
        @if ($start > 1)
            <a href="{{ $paginator->url(1) }}" class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition">1</a>
            @if ($start > 2)
                <span class="px-2 text-gray-400">...</span>
            @endif
        @endif

        {{-- Page range --}}
        @for ($page = $start; $page <= $end; $page++)
            @if ($page == $currentPage)
                <span class="px-3 py-1.5 text-sm bg-primary-600 text-white rounded-lg font-medium">
                    {{ $page }}
                </span>
            @else
                
                <a href="{{ $paginator->url($page) }}"
                    class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition"
                >
                    {{ $page }}
                </a>
            @endif
        @endfor

        {{-- Last page --}}
        @if ($end < $lastPage)
            @if ($end < $lastPage - 1)
                <span class="px-2 text-gray-400">...</span>
            @endif
            <a href="{{ $paginator->url($lastPage) }}" class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition">{{ $lastPage }}</a>
        @endif

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            
            <a href="{{ $paginator->nextPageUrl() }}"
                class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition"
            >
                &rsaquo;
            </a>
        @else
            <span class="px-3 py-1.5 text-sm text-gray-300 rounded-lg cursor-not-allowed">
                &rsaquo;
            </span>
        @endif

    </div>
</nav>
@endif