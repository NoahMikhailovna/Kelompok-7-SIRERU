@if ($paginator->hasPages())
<div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap; font-size:13px; padding:4px 0;">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="padding:5px 12px; border-radius:6px; background:#f3f4f6; color:#ccc; cursor:not-allowed;">← Prev</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="padding:5px 12px; border-radius:6px; background:#f3f4f6; color:#374151; text-decoration:none;">← Prev</a>
    @endif

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="padding:5px 8px; color:#aaa;">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="padding:5px 12px; border-radius:6px; background:#b91c1c; color:#fff; font-weight:700;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="padding:5px 12px; border-radius:6px; background:#f3f4f6; color:#374151; text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="padding:5px 12px; border-radius:6px; background:#f3f4f6; color:#374151; text-decoration:none;">Next →</a>
    @else
        <span style="padding:5px 12px; border-radius:6px; background:#f3f4f6; color:#ccc; cursor:not-allowed;">Next →</span>
    @endif

    <span style="color:#aaa; margin-left:8px;">
        {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
    </span>
</div>
@endif
