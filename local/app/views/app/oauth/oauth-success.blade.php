@extends('../app.layouts.clean')

@section('page_head')
<style type="text/css">
body {
	padding:40px;
}
</style>
@stop

@section('content')
<h3 class="text-center">{{ trans('global.account_linked_successfully') }}</h3>
<br>
<a href="javascript:void(0);" onclick="window.opener.widgetOAuthCallback(); window.close()" class="btn btn-primary btn-lg btn-block">{{ trans('global.close_window') }}</a>
<p class="text-center" style="margin-top:5px">{{ trans('global.close_window_info') }}</p>
@stop