@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][($config['method'] == 'create' ? 'create' : 'edit')]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create' ? route('product.catalogue.store') : route('product.catalogue.update',$productCatalogues->id));
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
                        @include('backend.dashboard.component.content', ['model' => ($productCatalogues) ?? null])
                    </div>
                </div>
                @include('backend.dashboard.component.album', ['model' => ($productCatalogues) ?? null])
                @include('backend.dashboard.component.seo', ['model' => ($productCatalogues) ?? null])
            </div>
            <div class="col-lg-3">
                @include('backend.product.catalogue.component.aside')
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>