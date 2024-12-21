@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][($config['method'] == 'create' ? 'create' : 'edit')]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create' ? route('slide.store') : route('slide.update',$slide->id));
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInright">
        <div class="row">
            <div class="col-lg-9">
                @include('backend.slide.slide.component.list')
            </div>
            <div class="col-lg-3">
                @include('backend.slide.slide.component.aside')
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>