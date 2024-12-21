@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][($config['method'] == 'create' ? 'create' : 'edit')]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create' ? route('widget.store') : route('widget.update',$widget->id));
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInright">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin Widget</h5>
                    </div>
                    <div class="ibox-content widgetContent">
                        @include('backend.dashboard.component.content', ['offTitle' => true, 'offContent' => true, 'model' => ($widget) ?? null])
                    </div>
                </div>
                @include('backend.dashboard.component.album', ['model' => ($widget) ?? null])
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cấu hình nội dung Widget</h5>
                    </div>
                    <div class="ibox-content model-list">
                        <div class="labelText">
                            Chọn Module
                        </div>
                        @foreach(__('module.model') as $key => $val)
                        <div class="model-item uk-flex uk-flex-middle">
                            <input 
                                type="radio" 
                                id="{{ $key }}" 
                                class="input-radio" 
                                value="{{ $key }}" 
                                name="model"
                                {{ (old('model', ($widget->model) ?? null) == $key) ? 'checked' : '' }}
                            >
                            <label for="{{ $key }}">{{ $val }}</label>
                        </div>
                        @endforeach
                        <div class="search-model-box">
                            <i class="fa fa-search"></i>
                            <input 
                                type="text" 
                                class="form-control search-model"
                            >
                            <div class="ajax-search-result"></div>
                        </div>
                        @php
                            $modelItem = old('modelItem', ($widgetItem) ?? null);
                        @endphp
                        <div class="search-model-result">
                            @if(!is_null($modelItem) && count($modelItem))
                            @foreach($modelItem['id'] as $key => $val)
                            <div class="search-result-item" id="model-{{ $val }}" data-modelId="{{ $val }}">
                                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                    <div class="uk-flex uk-flex-middle">
                                        <span class="image img-cover">
                                            <img src="{{ $modelItem['image'][$key] }}" alt="">
                                        </span>
                                        <span class="name">{{ $modelItem['name'][$key] }}</span>
                                        <div class="hidden">
                                            <input type="text" name="modelItem[id][]" value="{{ $val }}">
                                            <input type="text" name="modelItem[name][]" value="{{ $modelItem['name'][$key] }}">
                                            <input type="text" name="modelItem[image][]" value="{{ $modelItem['image'][$key] }}">
                                        </div>
                                    </div>
                                    <div class="deleted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24">
                                            <path d="M 4.7070312 3.2929688 L 3.2929688 4.7070312 L 10.585938 12 L 3.2929688 19.292969 L 4.7070312 20.707031 L 12 13.414062 L 19.292969 20.707031 L 20.707031 19.292969 L 13.414062 12 L 20.707031 4.7070312 L 19.292969 3.2929688 L 12 10.585938 L 4.7070312 3.2929688 z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                @include('backend.widget.component.aside')
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>

