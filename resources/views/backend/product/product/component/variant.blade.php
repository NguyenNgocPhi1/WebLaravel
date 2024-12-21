<div class="ibox variant-box">
    <div class="ibox-title">
        <h5>Sản phẩm có nhiều phiên bản</h5>
        <div class="description">
            Cho phép bạn bán các phiên bản khác nhau của sản phẩm, ví dụ: quần, áo thì có các <strong
                class="text-danger">màu sắc</strong> và <strong class="text-danger">Size</strong> số khác nhau. Mỗi
            phiên bản sẽ là một dòng trong mục danh sách phiên bản phía dưới
        </div>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="variant-checkbox uk-flex uk-flex-middle">   
                    <input type="checkbox" value="1" name="accept" id="variantCheckbox" class="variantInputCheckbox" {{ (old('accept') == 1 || (isset($products) && count($products->product_variants) > 0)) ? 'checked' : '' }}>
                    <label for="variantCheckbox" class="turnOnVariant">Sản phẩm này nhiều biến thể. Ví dụ như khác nhau
                        về màu sắc, kích thước</label>
                </div>
            </div>
        </div>
        @php
            $variantCatalogue = old('attributeCatalogue', ((isset($products->attributeCatalogue)) ? json_decode($products->attributeCatalogue, true) : []));
            $variantCatalogue = array_filter($variantCatalogue);
        @endphp
        <div class="variant-wrapper {{ ($variantCatalogue && count($variantCatalogue)) ? '' : 'hidden' }}">
            <div class="row variant-container">
                <div class="col-lg-3">
                    <div class="attribute-title">Chọn thuộc tính</div>
                </div>
                <div class="col-lg-9">
                    <div class="attribute-title">Chọn giá trị của thuộc tính (nhập 2 từ để tìm kiếm)</div>
                </div>
            </div>
            <div class="variant-body">
                @if($variantCatalogue && count($variantCatalogue))
                @foreach($variantCatalogue as $keyAttr => $valAttr)
                <div class="row mb20 variant-item">
                    <div class="col-lg-3">
                        <div class="attribute-catalogue">
                            <select name="attributeCatalogue[]" id="" class="choose-attribute niceSelect">
                                <option value="">Chọn nhóm thuộc tính</option>
                                @foreach ($attributeCatalogue as $key => $val)
                                <option {{ $valAttr == $val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->attribute_catalogue_language->first()->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        {{-- <input type="text" name="" disabled class="fake-variant form-control"> --}}
                        <select name="attribute[{{ $valAttr }}][]" class="selectVariant variant-{{ $valAttr }} form-control" multiple data-catid="{{ $valAttr }}"></select>
                    </div>
                    <div class="col-lg-1">
                        <button type="button" class="remove-attribute btn btn-danger">
                            <svg data-icon="TrashSolidLarge" aria-hidden="true" focusable="false" width="15" height="16"
                                viewBox="0 0 15 16" class="bem-Svg" style="display: block">
                                <path fill="currentColor"
                                    d="M2 14a1 1 0 001 1h9a1 1 0 001-1V6H2v8zM13 2h-3a1 1 0 01-1-1H6a1 1 0 01-1 1H1v2h13V2h-1z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
            <div class="variant-foot mt10">
                <button type="button" class="add-variant">Thêm phiên bản mới</button>
            </div>
        </div>
    </div>
</div>
<div class="ibox product-variant">
    <div class="ibox-title">
        <h5>Danh sách phiên bản</h5>
    </div>
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped variantTable">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<script>
    var attributeCatalogue = @json($attributeCatalogue->map(function($item){
        $name = $item->attribute_catalogue_language->first()->name;
        return [
            'id' => $item->id,
            'name' => $name
        ];
    })->values());
    
    var attribute = '{{ base64_encode(json_encode(old('attribute') ?? ((isset($products->attribute)) ? $products->attribute : []))) }}'
    var variant = '{{ base64_encode(json_encode(old('variant') ?? ((isset($products->variant)) ? json_decode($products->variant, true) : []))) }}'
</script>