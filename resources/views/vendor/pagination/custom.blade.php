@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Paginacja">
        {{-- Przycisk: poprzednia strona --}}
        @if ($paginator->onFirstPage())
            <span class="pagination__item pagination__item--disabled">&laquo;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination__item" rel="prev">&laquo;</a>
        @endif

        {{-- Numery stron --}}
        @foreach ($elements as $element)
            {{-- separator "..." gdy stron jest dużo --}}
            @if (is_string($element))
                <span class="pagination__item pagination__item--disabled">{{ $element }}</span>
            @endif

            {{-- faktyczne linki do stron --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination__item pagination__item--active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pagination__item">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Przycisk: następna strona --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination__item" rel="next">&raquo;</a>
        @else
            <span class="pagination__item pagination__item--disabled">&raquo;</span>
        @endif
    </nav>
@endif
