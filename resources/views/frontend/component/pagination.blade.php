@if ($model->hasPages())
<ul class="pagination">
    @foreach($model->getUrlRange(1, $model->lastPage()) as $page => $url)
        @php
            $paginationUrl = str_replace('?trang=', '/trang-', $url).config('apps.general.suffix');
            $paginationUrl = ($page == 1) ? str_replace('/trang-'.$page, '', $paginationUrl) : $paginationUrl;
        @endphp
        <li class="page-item {{ ($page == $model->currentPage()) ? 'active' : '' }}">
            <a href="{{ $paginationUrl }}" class="page-link">{{ $page }}</a>
        </li>
    @endforeach
</ul>
@endif