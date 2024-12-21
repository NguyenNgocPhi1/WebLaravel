@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][($config['method'] == 'create' ? 'create' : 'edit')]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create' ? route('customer.catalogue.store') : route('customer.catalogue.update',$customerCatalogues->id));
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInright">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Nhập thông tin chung của nhóm khách hàng</p>
                        <p>- Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Tên nhóm
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text"
                                            name="name"
                                            value="{{ old('name', ($customerCatalogues->name) ?? '') }}"
                                            class="form-control"
                                            placeholder=""
                                            autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Ghi chú
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text"
                                            name="description"
                                            value="{{ old('description', ($customerCatalogues->description) ?? '') }}"
                                            class="form-control"
                                            placeholder=""
                                            autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>