<div class="uk-search uk-flex uk-flex-middle mr10"> 
    <div class="input-group">
        <input 
            type="text" 
            name="keyword" 
            value="{{ request('keyword') ?: old('keyword') }}" 
            placeholder="{{ __('__sidebar.searchInput') }}"
            class="form-control"
        >
        <span class="input-group-btn">
            <button 
                type="submit" 
                name="search" 
                value="search" 
                class="btn btn-primary mba btn-sm">{{ __('__sidebar.search') }}
            </button>
        </span>
    </div>
</div>