@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][($config['method'] == 'create' ? 'create' : 'edit')]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create' ? route('gallery.catalogue.store') : route('gallery.catalogue.update',$galleryCatalogues->id));
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInright">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('__sidebar.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.dashboard.component.content', ['model' => ($galleryCatalogues) ?? null])
                    </div>
                </div>
                @include('backend.dashboard.component.album', ['model' => ($galleryCatalogues) ?? null])
                @include('backend.dashboard.component.seo', ['model' => ($galleryCatalogues) ?? null])
            </div>
            <div class="col-lg-3">
                @include('backend.gallery.catalogue.component.aside')
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>