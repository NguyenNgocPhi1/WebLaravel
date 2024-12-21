@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][($config['method'] == 'create' ? 'create' : 'edit')]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create' ? route('source.store') : route('source.update',$source->id));
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInright">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin nguồn khách</h5>
                    </div>
                    <div class="ibox-content sourceContent">
                        @include('backend.dashboard.component.content', ['offTitle' => true, 'offContent' => true, 'model' => ($source) ?? null])
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                @include('backend.source.component.aside')
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>

