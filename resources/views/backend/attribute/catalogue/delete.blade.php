@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
@include('backend.dashboard.component.formError')
<form action="{{route('attribute.catalogue.destroy', $attributeCatalogues->id)}}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => ($attributeCatalogues) ?? null])
</form>