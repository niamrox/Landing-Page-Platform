@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('global.settings') }}</li>
		<li><a href="#/account">{{ trans('global.account') }}</a></li>
		<li>{{ trans('admin.order_plan') }}</li>
		<li class="active">{{ trans('admin.confirm_order') }}</li>
	</ul>

	<div class="page-header">
		<div class="row">
			<h1 class="col-xs-12 col-sm-4 text-center text-left-sm"><i class="fa fa-shopping-cart page-header-icon" style="height:28px"></i> {{ trans('admin.confirm_order') }}</h1>
		</div>
	</div>

		<div class="panel invoice">
			<div class="invoice-header">
				<h3>
					<div>
						<small>{{ \App\Core\Settings::get('cms_title', trans('global.app_title')) }}</small><br>
						{{ trans('admin.invoice') }}
					</div>
				</h3>
				<address>
					{{ trans('admin.' . $payment_method) }}
				</address>


				<div class="invoice-date">
					<small><strong>{{ trans('admin.date') }}</strong></small><br>
					{{ $date }}
				</div>
			</div>
			<div class="invoice-info">
				<div class="invoice-recipient">
				<br>
					<strong>{{ $to }}</strong>
				</div>
				<div class="invoice-total">
					<span>{{ $cost_str }}</span>
					{{ trans('admin.total') }}:
				</div>
			</div>
			<hr>
			<div class="invoice-table">
				<table>
					<thead>
						<tr>
							<th>
								{{ trans('admin.order') }}
							</th>
							<th>
								{{ trans('admin.line_total') }}
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								{{ $plan->name }}
								<div class="invoice-description">{{ trans('admin.order_line', array('plan' => $plan->name, 'date' => $new_date)) }}</div>
							</td>
							<td>
								{{ $cost_str }}
							</td>
						</tr>
					</tbody>
				</table>
			</div> <!-- / .invoice-table -->
		</div> <!-- / .invoice -->
<?php

echo '<hr>';

Former::setOption('default_form_type', 'vertical');

if ($payment_method == 'bank')
{
	echo Former::open()
		->id('frmPay')
		->class('form-horizontal validate')
		->action(url('api/v1/account/order-plan-confirmed'))
		->method('POST');

	//echo Former::hidden('sl')->forceValue($sl);
}
elseif ($payment_method == 'paypal')
{
	$form_action = (\Config::get('payment-gateways.gateways.paypal.sandbox')) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr': 'https://www.paypal.com/cgi-bin/webscr';

// 	<input type="hidden" name="return" value="' . url('/api/v1/account/return-checkout') . '" />
// 	<input type="hidden" name="custom" value="' . $sl . '">
	$form_inputs = '
	<input type="hidden" name="cmd" value="_xclick">
	<input type="hidden" name="business" value="' . \Config::get('payment-gateways.gateways.paypal.email') . '">
	<input type="hidden" name="lc" value="US">
	<input type="hidden" name="item_name" value="' . trans('admin.order_line', array('plan' => $plan->name, 'date' => $new_date)) . '">
	<input type="hidden" name="amount" value="' . $cost . '">
	<input type="hidden" name="currency_code" value="' . $currency . '">
	<input type="hidden" name="button_subtype" value="services">
	<input type="hidden" name="no_note" value="0">
	<input type="hidden" name="tax_rate" value="0">
	<input type="hidden" name="shipping" value="0">
	<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">';

	echo Former::open()
		->id('frmPay')
		->class('form-horizontal')
		->target('_blank')
		->onsubmit('formSubmit()')
		->action($form_action)
		->method('POST');

	echo $form_inputs;
}
elseif ($payment_method == '2checkout')
{
	$form_action = (\Config::get('payment-gateways.gateways.2checkout.sandbox')) ? 'https://sandbox.2checkout.com/checkout/purchase': 'https://www.2checkout.com/checkout/purchase';

// 	<input type="hidden" name="x_receipt_link_url" value="' . url('/api/v1/account/return-checkout') . '" />
// 	<input type="hidden" name="custom" value="' . $sl . '">
	$form_inputs = '
	<input type="hidden" name="mode" value="2CO" />
	<input type="hidden" name="li_0_type" value="product" />
	<input type="hidden" name="li_0_quantity" value="1">
	<input type="hidden" name="li_0_tangible" value="Y" />
	<input type="hidden" name="sid" value="' . \Config::get('payment-gateways.gateways.2checkout.account_number') . '">
	<input type="hidden" name="li_0_product_id" value="">
	<input type="hidden" name="li_0_name" value="' . trans('admin.order_line', array('plan' => $plan->name, 'date' => $new_date)) . '">
	<input type="hidden" name="li_0_description" value="">
	<input type="hidden" name="li_0_price" value="' . $cost . '">
	<input type="hidden" name="currency_code" value="' . $currency . '">';

	echo Former::open()
		->id('frmPay')
		->class('form-horizontal')
		->target('_blank')
		->onsubmit('formSubmit()')
		->action($form_action)
		->method('POST');

	echo $form_inputs;
}

echo Former::hidden('sl')->forceValue($sl);

echo Former::actions()
	->lg_default_link(trans('global.back'), '#/order-subscription/' . $sl)
	->lg_primary_submit($payment_method_title);

echo Former::close();
?>
<script>
function formSubmit()
{
	blockUI();

	setTimeout(function() {
		$('#frmPay').addClass('validate');
		$('#frmPay').attr('onsubmit', '');
		$('#frmPay').attr('target', '_top');
		$('#frmPay').attr('action', '{{ url('api/v1/account/order-plan-confirmed') }}');
	}, 500);

	setTimeout(function() {
		formValidation();
		$('#frmPay').submit();
	}, 2500);
}

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