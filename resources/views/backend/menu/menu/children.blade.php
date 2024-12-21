@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['children'].$menu->languages->first()->pivot->name])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('menu.store')  : (($config['method'] == 'children') ? route('menu.save.children', [$menu->id]) : route('menu.update',$menu->id));
@endphp

<form action="{{ $url }}" method="post" class="box menuContainer">
    @csrf
    <div class="wrapper wrapper-content animated fadeInright">
        @include('backend.menu.menu.component.list')
        @include('backend.dashboard.component.button')
    </div>
</form>

@include('backend.menu.menu.component.popup')