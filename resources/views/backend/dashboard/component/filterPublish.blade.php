@php
    $publish = request('publish') ?: old('publish');
@endphp
<select name="publish" class="form-control mr10 setupSelect2">
    @foreach (__('__sidebar.publish') as $key => $val)             
        <option {{ ($publish == $key) ? 'selected' : ''}} value="{{ $key }}">{{ $val }}</option>
    @endforeach
</select>