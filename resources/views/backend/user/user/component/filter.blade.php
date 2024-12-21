<form action="{{ route('user.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uf-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    @php
                        $userCatalogueId = request('user_catalogue_id') ?: old('user_catalogue_id');
                    @endphp
                    <select name="user_catalogue_id" class="form-control setupSelect2 ml10">
                        <option value="0" selected="selected">Chọn Nhóm Thành Viên</option>
                        @foreach($userCatalogues as $key => $val)
                            <option {{ ($userCatalogueId == $val->id) ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
                        @endforeach
                    </select>
                    @include('backend.dashboard.component.keyword')
                    <a href="{{ route('user.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>{{ __('messages.user.create.title') }}</a>
                </div>
            </div>
        </div>
    </div>
</form>