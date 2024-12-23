
<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th style="width: 50px">
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Tên module</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($generates) && is_object($generates))
        @foreach($generates as $item)
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
            <td class="text-center">
                <a href="{{ route('generate.edit', $item->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                <a href="{{ route('generate.delete', $item->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        @endforeach
    @endif 
    </tbody>
</table>
{{
    $generates->links('pagination::bootstrap-4')
}}