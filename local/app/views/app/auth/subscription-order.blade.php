@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('global.settings') }}</li>
		<li><a href="#/account">{{ trans('global.account') }}</a></li>
		<li class="active">{{ trans('admin.order_plan') }}</li>
	</ul>

	<div class="page-header">
		<div class="row">
			<h1 class="col-xs-12 col-sm-4 text-center text-left-sm"><i class="fa fa-shopping-cart page-header-icon" style="height:28px"></i> {{ trans('admin.order_plan') }}</h1>
		</div>
	</div>

<?php
Former::setOption('default_form_type', 'vertical');

echo Former::open()
	->class('form-horizontal validate')
	->action(url('api/v1/account/order-plan-confirm'))
	->method('POST');

echo Former::hidden()
    ->name('sl')
    ->forceValue($sl);

echo Former::hidden()
    ->name('cost_month_str')
    ->forceValue($cost_month_str);

echo Former::hidden()
    ->name('cost_month')
    ->forceValue($cost_month);

echo Former::hidden()
    ->name('new_date_month')
    ->forceValue($new_date_month);

echo Former::hidden()
    ->name('cost_year_str')
    ->forceValue($cost_year_str);

echo Former::hidden()
    ->name('cost_year')
    ->forceValue($cost_year);

echo Former::hidden()
    ->name('new_date_year')
    ->forceValue($new_date_year);

echo Former::hidden()
    ->name('currency')
    ->forceValue($currency);

?>
<div class="panel panel-info">
	<div class="panel-heading">
		<span class="panel-title">{{ $order_message }}</span>
	</div>
	<div class="panel-body form-no-margin">
<?php
echo \Former::radios('radio')
	->label('')
	->dataFvNotempty()
	->dataFvNotemptyMessage(trans('global.please_select_an_option'))
	->radios(array(
		'<strong>' . trans('admin.one_month') . ', ' . $cost_month_str . '</strong><br>' . trans('admin.subscription_ends_on', ['date' => $new_date_month]) => array('name' => 'period', 'value' => 'month'),
		'<strong>' . trans('admin.one_year') . ', ' . $cost_year_str . '</strong><br>' . trans('admin.subscription_ends_on', ['date' => $new_date_year]) => array('name' => 'period', 'value' => 'year')
	));
?>
	</div>
</div>

<div class="panel panel-info">
	<div class="panel-heading">
		<span class="panel-title">{{ trans('admin.payment_method') }}</span>
	</div>
	<div class="panel-body form-no-margin">
<?php
$payment_gateways = \Config::get('payment-gateways.gateways');

$options = array();
foreach ($payment_gateways as $payment_gateway => $gateway)
{
	if ($gateway['active'])
	{
		$options['<img src="' . url('/assets/images/payment-gateways/' . trans('admin.' . $payment_gateway . '_image'))  . '" tooltip-placement="right" tooltip="' . trans('admin.' . $payment_gateway . '_title') . '"><br>' . trans('admin.' . $payment_gateway . '_info')] = array('name' => 'payment_method', 'value' => $payment_gateway);
	}
}

if (! empty($options))
{
	echo '<div id="payment-options">';

	echo \Former::radios('radio')
		->label('')
		->dataFvNotempty()
		->dataFvNotemptyMessage(trans('global.please_select_an_option'))
		->radios($options);

	echo '</div>';
}
?>
	</div>
</div>
<?php

echo '<hr>';

echo Former::actions()
    ->lg_default_link(trans('global.back'), '#/account')
    ->lg_primary_submit(trans('admin.next'));

echo Former::close();
?>
<script>
function formSubmittedSuccess(result, response)
{
    if(result == 'error')
    {
        return;
    }
	document.location = response.redir;
}
</script>
@stop