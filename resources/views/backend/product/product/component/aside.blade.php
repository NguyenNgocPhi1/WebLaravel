<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('__sidebar.parent') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="text-danger notice">{{ __('__sidebar.parentNotice') }}</span>
                    <select name="product_catalogue_id" class="form-control setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                        <option {{ $key == old('product_catalogue_id', (isset($products->product_catalogue_id)) ? $products->product_catalogue_id : '') ? 'selected' : ''}} value="{{$key}}">{{$val}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @php
            $catalogue = [];
            if(isset($products)){
                foreach ($products->product_catalogues as $key => $val) {
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
                        <option @if(is_array(old('catalogue', (isset($catalogue) && count($catalogue)) ? $catalogue : [])) && isset($products->product_catalogue_id) && $key !== $products->product_catalogue_id && in_array($key, old('catalogue', (isset($catalogue)) ? $catalogue : []))) selected @endif value="{{$key}}">{{$val}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('__sidebar.tableHeading') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">{{__('messages.product.product_code')}}</label>
                    <input type="text" name="code" value="{{old('code', ($products->code) ?? null)}}" class="form-control">
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">{{__('messages.product.made_in')}}</label>
                    <input type="text" name="made_in" value="{{old('made_in', ($products->made_in) ?? null)}}" class="form-control">
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">{{__('messages.product.price')}}</label>
                    <input type="text" name="price" value="{{old('price', (isset($products)) ? (number_format($products->price,0 ,',' ,'.')) : '' )}}" class="form-control int text-right">
                </div>
            </div>
        </div>
    </div>
</div>
@include('backend.dashboard.component.publish',['model' => ($products) ?? null])