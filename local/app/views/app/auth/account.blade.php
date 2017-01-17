@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('global.settings') }}</li>
		<li class="active">{{ trans('global.account') }}</li>
	</ul>

	<div class="page-header">
		<div class="row">
			<h1 class="col-xs-12 text-center text-left-sm"><i class="fa fa-credit-card page-header-icon" style="height:28px"></i> {{ trans('admin.your_current_plan', ['plan' => '<strong>' . Auth::user()->plan->name . '</strong>']) }} {{ $expiration_message }}</h1>

		</div>
	</div>

	<ul class="nav nav-tabs">
		<li ng-class="{active: selectedTab == 'subscription'}">
			<a href="javascript:void(0);" ng-click="selectedTab = 'subscription';"><span class="fa fa-trophy"></span> &nbsp; {{ trans('admin.subscription') }}</a>
		</li>
		<li ng-class="{active: selectedTab == 'invoices'}">
			<a href="javascript:void(0);" ng-click="selectedTab = 'invoices';"><span class="fa fa-file-text-o"></span> &nbsp; {{ trans('admin.invoices') }}</a>
		</li>
	</ul>

	<div class="panel-body no-padding" ng-init="selectedTab = 'subscription';">
		<div class="tab-content">
			<div class="tab-pane fade" ng-class="{'in active': selectedTab == 'subscription'}">

<style type="text/css">
.plan-align-left {
	text-align:left;
	padding-left:20px !important;
}
</style>

				<div class="plans-panel">
					<div class="plans-container">
<?php
$i=1;
foreach($plans as $plan)
{
	$sl = \App\Core\Secure::array2string(array('plan_id' => $plan->id));

	$settings = $plan->settings;
	if ($settings != '') $settings = json_decode($settings);

	$support = (isset($settings->support)) ? $settings->support : '-';
	$domain = (isset($settings->domain)) ? (boolean) $settings->domain : true;
	$domain_icon = ($domain) ? '<i class="fa fa-check icon-active"></i>' : '<i class="fa fa-times icon-nonactive"></i>';
	$download = (isset($settings->download)) ? (boolean) $settings->download : true;
	$download_icon = ($download) ? '<i class="fa fa-check icon-active"></i>' : '<i class="fa fa-times icon-nonactive"></i>';
	$monthly = (isset($settings->monthly)) ? $settings->monthly : 0;
	$annual = (isset($settings->annual)) ? 12 * $settings->annual : 0;
	$currency = (isset($settings->currency)) ? $settings->currency : 'USD';
	$currencies = trans('currencies');
	$currency_symbol = $currencies[$currency][1];

	$sites = (isset($settings->max_sites)) ? $settings->max_sites : 0;
	if ($sites == 0) $sites = trans('admin.unlimited');
?>
				<div class="plan-col col-sm-3">
					<div class="plan-header bg-light-green <?php echo ($i%2) ? 'darken': 'darker'; ?>">{{ $plan->name }}</div>
					<div class="plan-pricing bg-light-green<?php echo ($i%2) ? '': ' darken'; ?>"><span class="plan-currency">{{ $currency_symbol }}</span><span class="plan-value">{{ str_replace('.00', '', number_format($monthly, 2)) }}</span><span class="plan-period">{{ trans('admin.per_mo') }}</span></div>
					<div class="plan-header bg-light-green <?php echo ($i%2) ? 'darken': 'darker'; ?>">{{ $currency_symbol }} {{ str_replace('.00', '', number_format($annual, 2)) }} {{ trans('admin.annual') }}</div>
					<ul class="plan-features">
						<li class="plan-align-left">{{ trans('admin.sites') }}: {{ $sites }}</li>
						<li class="plan-align-left">{{ $domain_icon . ' ' . trans('global.custom_domain') }}</li>
<?php /*						<li class="plan-align-left">{{ $download_icon . ' ' . trans('global.download_site') }}</li>*/ ?>
						<li class="plan-align-left">{{ trans('admin.support') }}: {{ $support }}</li>

<?php if ((int) $monthly > 0 && ((! $expired && Auth::user()->plan->sort <= $plan->sort) || $expired)) { ?>
						<a href="#/order-subscription/{{ $sl }}" class="bg-light-green <?php echo ($i%2) ? 'darken': 'darker'; ?>">{{ (Auth::user()->plan->sort == $plan->sort) ? trans('admin.extend_subscription') : trans('admin.upgrade'); }} <i class="fa fa-arrow-right"></i></a>
<?php } ?>
					</ul>
				</div>
<?php
	$i++;
}
?>
					</div>
				</div>
			</div>

			<div class="tab-pane fade" ng-class="{'in active': selectedTab == 'invoices'}">
<?php
if (count($orders) == 0)
{
	echo '<p class="lead" style="margin:20px">' . trans('admin.no_invoices') . '</p>';
}
else
{
?>
					<table class="table table-striped">
						<thead>
							<tr>
								<th>{{ trans('admin.order') }}</th>
								<th>{{ trans('admin.payment_method') }}</th>
								<th>{{ trans('admin.date') }}</th>
								<th class="text-right">{{ trans('admin.total') }}</th>
							</tr>
						</thead>
						<tbody>
<?php
foreach ($orders as $order)
{
	$sl_invoice = \App\Core\Secure::array2string(array('invoice_id' => $order->id));
?>
							<tr>
								<td><a href="javascript:void(0);" data-modal="{{ url('/app/modal/account/invoice?sl=' . $sl_invoice) }}" style="text-decoration:underline">{{ trans('admin.order_line', array('plan' => $order->plan_name, 'date' => $order->expires)) }} - {{ trans('admin.invoice') }} #{{ $order->invoice }}</a></td>
								<td>{{ trans('admin.' . $order->payment_method) }}</td>
								<td>{{ $order->invoice_date }}</td>
								<td class="text-right">{{ $order->cost_str }}</td>
							</tr>
<?php
	$i++;
}
?>
						</tbody>
					</table>
<?php
}
?>
				</div>
			</div>
		</div>
@stop