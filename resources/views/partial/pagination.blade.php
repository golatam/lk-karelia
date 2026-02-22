@if ($paginator->hasPages())
    <ul class="pagination">
        @if (!$paginator->onFirstPage())
            <li>
                <a class="btn" href="{{ $paginator->previousPageUrl() }}">«</a>
            </li>
        @endif
        @foreach ($elements as $element)
            <li>
            @if (is_string($element))
                <span class="btn">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="btn">{{ $page }}</span>
                    @else
                        <a class="btn" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
            </li>
        @endforeach
        @if ($paginator->hasMorePages())
            <li>
                <a class="btn" href="{{ $paginator->nextPageUrl() }}">»</a>
            </li>
        @endif
    </ul>
@endif
