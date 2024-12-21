@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
@include('backend.dashboard.component.formError')
<form action="{{route('{view}.destroy', ${module}s->id)}}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => (${module}s) ?? null])
</form>