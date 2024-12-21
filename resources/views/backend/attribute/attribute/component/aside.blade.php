<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('__sidebar.parent') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="text-danger notice">{{ __('__sidebar.parentNotice') }}</span>
                    <select name="attribute_catalogue_id" class="form-control setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                        <option {{ $key == old('attribute_catalogue_id', (isset($attributes->attribute_catalogue_id)) ? $attributes->attribute_catalogue_id : '') ? 'selected' : ''}} value="{{$key}}">{{$val}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @php
            $catalogue = [];
            if(isset($attributes)){
                foreach ($attributes->attribute_catalogues as $key => $val) {
                    $catalogue[] = $val->id;
                }
            }
        @endphp
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-lable">{{ __('__sidebar.tableChooseCatalogue') }}</label>
                    <select multiple name="catalogue[]" class="form-control setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                        <option @if(is_array(old('catalogue', (isset($catalogue) && count($catalogue)) ? $catalogue : [])) && isset($attributes->attribute_catalogue_id) && $key !== $attributes->attribute_catalogue_id && in_array($key, old('catalogue', (isset($catalogue)) ? $catalogue : []))) selected @endif value="{{$key}}">{{$val}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
@include('backend.dashboard.component.publish',['model' => ($attributes) ?? null])