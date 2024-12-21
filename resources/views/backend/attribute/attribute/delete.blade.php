@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
@include('backend.dashboard.component.formError')
<form action="{{route('attribute.destroy', $attributes->id)}}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => ($attributes) ?? null])
</form>