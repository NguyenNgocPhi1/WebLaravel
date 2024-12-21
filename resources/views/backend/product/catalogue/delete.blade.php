@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
@include('backend.dashboard.component.formError')
<form action="{{route('product.catalogue.destroy', $productCatalogues->id)}}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => ($productCatalogues) ?? null])
</form>