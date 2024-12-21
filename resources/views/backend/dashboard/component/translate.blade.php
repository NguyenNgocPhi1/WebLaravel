@if(!isset($offTitle))
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-lable text-left">{{ __('__sidebar.title') }}
                <span class="text-danger">(*)</span>
            </label>
            <input type="text"
                    name="translate_name"
                    value="{{ old('name', ($model->name) ?? '') }}"
                    class="form-control"
                    placeholder=""
                    autocomplete="off">
        </div>
    </div>
</div>
@endif
@if(!isset($offDescription))
<div class="row mb30">
    <div class="col-lg-12">
        <div class="form-row">
            <label for="" class="control-lable text-left">
                {{ __('__sidebar.description') }}
            </label>
            <textarea type="text"
                    name="translate_description"
                    class="form-control ck-editor"
                    placeholder=""
                    autocomplete="off"
                    id="ckDescription_1"
                    data-height="100"
            >{{ old('description', ($model->description) ?? '') }}</textarea>
        </div>
    </div>
</div>
@endif
@if(!isset($offContent))
<div class="row">
    <div class="col-lg-12">
        <div class="form-row">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <label for="" class="control-lable text-left">{{ __('__sidebar.content') }}</label>
                <a href="" class="multipleUploadImageCkeditor" data-target="ckContent_1">{{ __('__sidebar.upload') }}</a>
            </div>
            <textarea type="text"
                    name="translate_content"
                    class="form-control ck-editor"
                    placeholder=""
                    autocomplete="off"
                    id="ckContent_1"
                    data-height="500"
            >{{ old('content', ($model->content) ?? '') }}</textarea>
        </div>
    </div>
</div>
@endif