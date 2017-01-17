@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('global.settings') }}</li>
		<li><a href="#/account">{{ trans('global.account') }}</a></li>
		<li>{{ trans('admin.order_plan') }}</li>
		<li>{{ trans('admin.confirm_order') }}</li>
		<li class="active">{{ trans('admin.thank_you') }}</li>
	</ul>

	<div class="page-header">
		<div class="row">
			<h1 class="col-xs-12 col-sm-4 text-center text-left-sm"><i class="fa fa-thumbs-o-up page-header-icon" style="height:28px"></i> {{ trans('admin.thank_you') }}</h1>
		</div>
	</div>

<?php
Former::setOption('default_form_type', 'vertical');

echo Former::open()
	->class('form-horizontal validate')
	->action(url('api/v1/account/order-plan-pay'))
	->method('POST');

echo Former::hidden()
    ->name('sl')
    ->forceValue($sl);


?>
<p>{{ trans('admin.thank_you_message') }}</p>
<?php

echo '<hr>';

echo Former::actions()
    ->lg_success_link(trans('admin.return_to_your_account'), '#/account');

echo Former::close();
?>

@stop