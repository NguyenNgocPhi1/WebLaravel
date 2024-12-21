@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][($config['method'] == 'create' ? 'create' : 'edit')]['title']])
@include('backend.dashboard.component.formError')

<form action="{{ route('menu.store') }}" method="post" class="box menuContainer">
    @csrf
    <div class="wrapper wrapper-content animated fadeInright">
        @include('backend.menu.menu.component.catalogue')
        <hr>
        @include('backend.menu.menu.component.list')
        <input type="hidden" name="redirect" value="{{ $id ?? 0 }}">
        @include('backend.dashboard.component.button')
    </div>
</form>

@include('backend.menu.menu.component.popup')