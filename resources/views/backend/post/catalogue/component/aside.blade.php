<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('__sidebar.parent') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="text-danger notice">{{ __('__sidebar.parentNotice') }}</span>
                    <select name="parent_id" class="form-control setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                        <option {{ $key == old('parent_id', (isset($postCatalogues->parent_id)) ? $postCatalogues->parent_id : '') ? 'selected' : ''}} value="{{$key}}">{{$val}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
@include('backend.dashboard.component.publish',['model' => ($postCatalogues) ?? null])