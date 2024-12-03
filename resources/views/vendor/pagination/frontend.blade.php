<?php
$link_limit = 7; // maximum number of links (a little bit inaccurate, but will be ok for now)
?>
@if ($paginator->lastPage() > 1)
<nav aria-label="...">
    <ul class="pagination justify-content-end mb-0">
        <li id="news_previous" class="paginate_button page-item previous {{ ($paginator->currentPage() == 1) ? ' disabled' : '' }}">
            <a class="page-link" tabindex="0" href="{{ $paginator->url( (integer) request()->get('page') - 1) }}"><i class="fa fa-angle-double-left"></i></a>
        </li>
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
        <?php
        $half_total_links = floor($link_limit / 2);
        $from = $paginator->currentPage() - $half_total_links;
        $to = $paginator->currentPage() + $half_total_links;
        if ($paginator->currentPage() < $half_total_links) {
            $to += $half_total_links - $paginator->currentPage();
        }
        if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
            $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
        }
        ?>
        @if ($from < $i && $i < $to)
        <li class="paginate_button page-item {{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
            <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
        </li>
        @endif
        @endfor
        <li id="news_next" class="paginate_button page-item {{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}">
            @if($paginator->currentPage() == $paginator->lastPage())
            <a class="page-link" tabindex="0" href="{{ $paginator->url($paginator->currentPage()) }}" > Last </a>
            @else
            <a class="page-link" tabindex="0" href="{{ $paginator->url($paginator->currentPage()+1) }}" ><i class="fa fa-angle-double-right"></i></a>
            @endif
        </li>
    </ul>
</nav>

@endif

