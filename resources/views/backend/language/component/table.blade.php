
<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th style="width:100px">Ảnh</th>
        <th>Tên ngôn ngữ</th>
        <th>Canonical</th>
        <th>Mô tả</th>
        <th class="text-center">Tình trạng</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($language) && is_object($language))
        @foreach($language as $item)
        <tr>
            <td>
                <input type="checkbox" value="{{ $item->id }}"  class="input-checkbox checkBoxItem">
            </td>
            <td>
                <span class="image img-cover"><img src="{{$item->image}}" alt=""></span>
            </td>
            <td>
                {{$item->name}}
            </td>
            <td>
                {{$item->canonical}}
            </td>
            <td>
                {{$item->description}}
            </td>
            <td class="text-center js-switch-{{ $item->id }}">
                <input type="checkbox" value="{{ $item->publish }}" class="js-switch status" data-field="publish" data-model="{{$config['model']}}" {{ ($item->publish == 2) ? 'checked' : '' }} data-modelId = "{{ $item->id }}" />
            </td>
            <td class="text-center">
                <a href="{{ route('language.edit', $item->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                <a href="{{ route('language.delete', $item->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        @endforeach
    @endif 
    </tbody>
</table>
{{
    $language->links('pagination::bootstrap-4')
}}