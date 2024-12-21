@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
@include('backend.dashboard.component.formError')
<form action="{{route('post.catalogue.destroy', $postCatalogues->id)}}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => ($postCatalogues) ?? null])
</form>