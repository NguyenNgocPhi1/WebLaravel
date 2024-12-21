<form action="{{ route('customer.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uf-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    @php
                        $customerCatalogueId = request('customer_catalogue_id') ?: old('customer_catalogue_id');
                    @endphp
                    <select name="customer_catalogue_id" class="form-control setupSelect2 ml10">
                        <option value="0" selected="selected">Chọn Nhóm Khách Hàng</option>
                        @foreach($customerCatalogues as $key => $val)
                            <option {{ ($customerCatalogueId == $val->id) ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
                        @endforeach
                    </select>
                    @include('backend.dashboard.component.keyword')
                    <a href="{{ route('customer.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>{{
                        __('messages.customer.create.title') }}</a>
                </div>
            </div>
        </div>
    </div>
</form>