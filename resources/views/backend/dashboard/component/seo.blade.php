<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('__sidebar.seo') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="seo-container">
            <div class="meta-title">
                {{ (old('meta_title', ($model->meta_title) ?? '')) ? (old('meta_title', ($model->meta_title) ?? '')) : __('__sidebar.seoTitle') }}
            </div>
            <div class="canonical">
                {{ (old('canonical',($model->canonical) ?? '')) ? config('app.url').old('canonical', ($model->canonical) ?? '').config('apps.general.suffix') : __('__sidebar.seoCanonical') }}
            </div>
            <div class="meta-description">
                {{ (old('meta_description', ($model->meta_description) ?? '')) ? (old('meta_description', ($model->meta_description) ?? '')) : __('__sidebar.seoDescription') }}
            </div>
        </div>
        <div class="seo-wrapper">
            <div class="row mb15">
                <div class="col-lg-12">
                    <label for="" class="control-lable text-left">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <span>{{ __('__sidebar.seoMetaTitle') }}</span>
                            <span class="count_meta-title">0 {{ __('__sidebar.character') }}</span>
                        </div>
                    </label>
                    <input type="text"
                            name="meta_title"
                            value="{{ old('meta_title', ($model->meta_title) ?? '') }}"
                            class="form-control"
                            placeholder=""
                            autocomplete="off"
                            {{ (isset($disabled)) ? 'disabled' : '' }}>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <label for="" class="control-lable text-left">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <span>{{ __('__sidebar.seoMetaKeyword') }}</span>
                        </div>
                    </label>
                    <input type="text"
                            name="meta_keyword"
                            value="{{ old('meta_keyword', ($model->meta_keyword) ?? '') }}"
                            class="form-control"
                            placeholder=""
                            autocomplete="off"
                            {{ (isset($disabled)) ? 'disabled' : '' }}>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <label for="" class="control-lable text-left">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <span>{{ __('__sidebar.seoMetaDescription') }}</span>
                            <span class="count_meta-description">0 {{ __('__sidebar.character') }}</span>
                        </div>
                    </label>
                    <textarea type="text"
                            name="meta_description"
                            class="form-control"
                            placeholder=""
                            autocomplete="off"
                            {{ (isset($disabled)) ? 'disabled' : '' }}
                    >{{ old('meta_description', ($model->meta_description) ?? '') }}</textarea>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <label for="" class="control-lable text-left">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <span>{{ __('__sidebar.canonical') }} <span class="text-danger">(*)</span></span>
                        </div>
                    </label>
                    <div class="input-wrapper">
                        <input type="text"
                            name="canonical"
                            value="{{ old('canonical', ($model->canonical) ?? '') }}"
                            class="form-control seo-canonical"
                            placeholder=""
                            autocomplete="off"
                            {{ (isset($disabled)) ? 'disabled' : '' }}>
                        <span class="baseUrl">{{ config('app.url') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>