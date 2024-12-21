<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('__sidebar.parent') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="text-danger notice">{{ __('__sidebar.parentNotice') }}</span>
                    <select name="post_catalogue_id" class="form-control setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                        <option {{ $key == old('post_catalogue_id', (isset($posts->post_catalogue_id)) ? $posts->post_catalogue_id : '') ? 'selected' : ''}} value="{{$key}}">{{$val}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @php
            $catalogue = [];
            if(isset($posts)){
                foreach ($posts->post_catalogues as $key => $val) {
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
                        <option @if(is_array(old('catalogue', (isset($catalogue) && count($catalogue)) ? $catalogue : [])) && isset($posts->post_catalogue_id) && $key !== $posts->post_catalogue_id && in_array($key, old('catalogue', (isset($catalogue)) ? $catalogue : []))) selected @endif value="{{$key}}">{{$val}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
@include('backend.dashboard.component.publish',['model' => ($posts) ?? null])