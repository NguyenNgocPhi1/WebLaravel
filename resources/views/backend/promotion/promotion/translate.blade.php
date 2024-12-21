@php
    $title = str_replace('{language}', $language->name, $config['seo']['translate']['title']).' '.$widget->name;
@endphp
@include('backend.dashboard.component.breadcrumb', ['title' => $title])
@include('backend.dashboard.component.formError')

<form action="{{ route('widget.saveTranslate') }}" method="post">
    @csrf
    <div class="wrapper wrapper-content animated fadeInright">
        <input type="hidden" name="translateId" value="{{ $language->id }}">
        <input type="hidden" name="widgetId" value="{{ $widget->id }}">
        <div class="row">
            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('__sidebar.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.dashboard.component.content', ['model' => ($widget) ?? null, 'disabled' => 1, 'offTitle' => true, 'offContent' => true])
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('__sidebar.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                        @include('backend.dashboard.component.translate', ['model' => ($widgetTranslate) ?? null, 'offTitle' => true, 'offContent' => true])
                    </div>
                </div>
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>
