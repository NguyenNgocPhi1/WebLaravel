@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][($config['method'] == 'create' ? 'create' : 'edit')]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create' ? route('user.store') : route('user.update',$user->id));
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInright">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Nhập thông tin chung của người sử dụng</p>
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
                                    <label for="" class="control-lable text-left">Email
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text"
                                            name="email"
                                            value="{{ old('email', ($user->email) ?? '') }}"
                                            class="form-control"
                                            placeholder=""
                                            autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Họ Tên
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text"
                                            name="name"
                                            value="{{ old('name', ($user->name) ?? '') }}"
                                            class="form-control"
                                            placeholder=""
                                            autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Nhóm Thành Viên</label>
                                    <select name="user_catalogue_id" id="" class="form-control setupSelect2">
                                        <option value="0">[Chọn nhóm thành viên]</option>
                                        @foreach ($userCatalogues as $key=>$item)
                                            <option
                                                {{ $item->id == old('user_catalogue_id', (isset($user->user_catalogue_id)) ? $user->user_catalogue_id : '') ? 'selected' : ''}}
                                                value="{{ $item->id }}">{{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Ngày Sinh
                                        <span class="text-danger">(*)</span>
                                    </label>                                 
                                    <input type="date"
                                            name="birthday"
                                            value="{{ old('birthday', isset($user->birthday) ? date('Y-m-d', strtotime($user->birthday)) : '') }}"
                                            class="form-control"
                                            placeholder=""
                                            autocomplete="off">
                                </div>
                            </div>
                        </div>
                        @if ($config['method'] == 'create')
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Mật khẩu
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="password"
                                            name="password"
                                            value=""
                                            class="form-control"
                                            placeholder=""
                                            autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Nhập lại mật khẩu
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="password"
                                            name="re_password"
                                            value=""
                                            class="form-control"
                                            placeholder=""
                                            autocomplete="off">
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Ảnh đại diện</label>
                                    <input type="text"
                                            name="image"
                                            value="{{ old('image', ($user->image) ?? '') }}"
                                            class="form-control upload-image"
                                            placeholder=""
                                            autocomplete="off"
                                            data-type="Images">
                                </div>
                            </div>          
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin liên hệ</div>
                    <div class="panel-description">Nhập thông tin liên hệ của người sử dụng</div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Thành phố</label>
                                    <select name="province_id" class="form-control setupSelect2 province location" data-target="districts">
                                        <option value="0">[Chọn thành phố]</option>
                                        @if (isset($provinces))
                                            @foreach ($provinces as $province)
                                                <option 
                                                    @if (old('province_id') == $province->code) 
                                                        selected 
                                                    @endif
                                                    value="{{$province->code}}">{{$province->name}}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Quận/Huyện</label>
                                    <select name="district_id" class="form-control districts setupSelect2 location" data-target="wards">
                                        <option value="0">[Chọn Quận/Huyện]</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Phường/Xã </label>
                                    <select name="ward_id" class="form-control setupSelect2 wards">
                                        <option value="0">[Chọn Phường/Xã]</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Địa Chỉ</label>
                                    <input type="text"
                                            name="address"
                                            value="{{ old('address', ($user->address) ?? '') }}"
                                            class="form-control"
                                            placeholder=""
                                            autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Số Điện Thoại</label>
                                    <input type="text"
                                            name="phone"
                                            value="{{ old('phone', ($user->phone) ?? '') }}"
                                            class="form-control"
                                            placeholder=""
                                            autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-lable text-left">Ghi Chú</label>
                                    <input type="text"
                                            name="description"
                                            value="{{ old('description', ($user->description) ?? '') }}"
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
<script>
    var province_id = '{{ (isset($user->province_id)) ? $user->province_id : old('province_id') }}';
    var district_id = '{{ (isset($user->district_id)) ? $user->district_id : old('district_id') }}';
    var ward_id = '{{ (isset($user->ward_id)) ? $user->ward_id : old('ward_id') }}';
</script>