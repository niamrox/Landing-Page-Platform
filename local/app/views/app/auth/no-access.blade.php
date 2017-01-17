@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li class="active">{{ trans('global.no_access') }}</li>
	</ul>

	<div class="page-header">
		<div class="row">
			<h1 class="col-xs-12 col-sm-4 text-center text-left-sm"><i class="fa fa-ban page-header-icon" style="height:28px"></i> {{ trans('global.no_access') }}</h1>

		</div>
	</div>

	<p class="lead">{{ trans('global.no_access_text') }}</p>

@stop