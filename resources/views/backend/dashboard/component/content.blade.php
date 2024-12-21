@if(!isset($offTitle))
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-lable text-left">{{ __('__sidebar.title') }}
                <span class="text-danger">(*)</span>
            </label>
            <input type="text"
                    name="name"
                    value="{{ old('name', ($model->name) ?? '') }}"
                    class="form-control change-title"
                    placeholder=""
                    data-flag = "{{ (isset($model->name)) ? 1 : 0 }}"
                    autocomplete="off"
                    {{ (isset($disabled)) ? 'disabled' : '' }}>
        </div>
    </div>
</div>
@endif
<div class="row mb30">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-lable text-left">
                {{ __('__sidebar.description') }}
            </label>
            <textarea type="text"
                    name="description"
                    class="form-control ck-editor"
                    placeholder=""
                    autocomplete="off"
                    id="ckDescription"
                    data-height="100"
                    {{ (isset($disabled)) ? 'disabled' : '' }}
            >{{ old('description', ($model->description) ?? '') }}</textarea>
        </div>
    </div>
</div>
@if(!isset($offContent))
<div class="row">
    <div class="col-lg-12">
        <div class="form-row">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <label for="" class="control-lable text-left">{{ __('__sidebar.content') }}</label>
                <a href="" class="multipleUploadImageCkeditor" data-target="ckContent">{{ __('__sidebar.upload') }}</a>
            </div>
            <textarea type="text"
                    name="content"
                    class="form-control ck-editor"
                    placeholder=""
                    autocomplete="off"
                    id="ckContent"
                    data-height="500"
                    {{ (isset($disabled)) ? 'disabled' : '' }}
            >{{ old('content', ($model->content) ?? '') }}</textarea>
        </div>
    </div>
</div>
@endif