@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][($config['method'] == 'create' ? 'create' : 'edit')]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create' ? route('generate.store') : route('generate.update',$generates->id));
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInright">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Nhập thông tin chung của dữ liệu</p>
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
                                    <label for="" class="control-lable text-left">Tên model
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text"
                                            name="name"
                                            value="{{ old('name', ($generates->name) ?? '') }}"
                                            class="form-control"
                                            placeholder=""
                                            autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Tên chức năng
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text"
                                            name="module"
                                            value="{{ old('module', ($generates->module) ?? '') }}"
                                            class="form-control"
                                            placeholder=""
                                            autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Loại module
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <select name="module_type" id="" class="form-control setupSelect2">
                                        <option value="0">Chọn Loại Module</option>
                                        <option value="catalogue">Module danh mục</option>
                                        <option value="detail">Module chi tiết</option>
                                        <option value="difference">Module khác</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Đường dẫn
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text"
                                            name="path"
                                            value="{{ old('path', ($generates->path) ?? '') }}"
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
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin schema</div>
                    <div class="panel-description">
                        <p>- Nhập thông tin schema</p>
                        <p>- Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Schema
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <textarea 
                                            name="schema"
                                            value="{{ old('schema', ($generates->schema) ?? '') }}"
                                            class="form-control schema"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Schema 2</label>
                                    <textarea 
                                            name="schema"
                                            value="{{ old('schema', ($generates->schema) ?? '') }}"
                                            class="form-control schema"
                                    ></textarea>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>