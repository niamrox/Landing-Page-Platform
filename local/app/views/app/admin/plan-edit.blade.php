@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('admin.system_administration') }}</li>
		<li><a href="#/admin/plans">{{ trans('admin.user_plans') }}</a></li>
		<li class="active">{{ trans('admin.edit_plan') }}</li>
	</ul>

	<div class="page-header">
		<h1><i class="fa fa-trophy page-header-icon"></i> {{ trans('admin.edit_plan') }}</h1>
	</div>

	<div class="panel">
		<div class="panel-body padding-sm">
<?php
//Former::setOption('default_form_type', 'vertical');

echo Former::open()
	->class('form-horizontal validate')
	->action(url('api/v1/admin/plan-update'))
	->method('POST');

echo Former::hidden()
    ->name('sl')
    ->forceValue($sl);

echo '<div class="row">';
echo '<div class="col-md-6">';

echo '<legend>' . trans('global.general') . '</legend>';

echo Former::text()
    ->name('name')
    ->forceValue($plan->name)
	->label(trans('global.name'))
	->dataBvNotempty()
    ->autocapitalize('off')
    ->autocorrect('off')
    ->autocomplete('off')
    ->required();

echo '</div>';
echo '<div class="col-md-6">';

echo '</div>';
echo '</div>';

echo '<div class="row">';
echo '<div class="col-md-6">';

echo '<legend>' . trans('admin.limitations') . '</legend>';

$max_sites_settings = (isset($settings->max_sites)) ? $settings->max_sites : 0;

$max_sites = array_combine(range(1,50), range(1,50));
array_unshift($max_sites, trans('admin.unlimited'));

echo Former::select('role')
	->class('select2-required form-control')
    ->name('max_sites')
    ->forceValue($max_sites_settings)
	->options($max_sites)
	->label(trans('admin.max_sites'));

$support_settings = (isset($settings->support)) ? $settings->support : '-';

echo Former::text()
    ->name('support')
    ->forceValue($support_settings)
	->label(trans('admin.support'))
	->help(trans('admin.support_info'));

echo '</div>';
echo '<div class="col-md-6">';

echo '<legend>&nbsp;</legend>';

$domain_settings = (isset($settings->domain)) ? $settings->domain : true;
$checked = ($domain_settings) ? ' checked' : '';

echo '<div class="form-group"><input type="hidden" name="domain" value="0">
	<label class="control-label control-label col-lg-4 col-sm-4">' . trans('global.custom_domain') . '</label>
	<div class="col-lg-8 col-sm-8"><div class="checkbox">
	<input data-class="switcher-success" type="checkbox" name="domain" value="1"' . $checked . '></div></div></div>';
/*
$download_settings = (isset($settings->download)) ? $settings->download : true;
$checked = ($download_settings) ? ' checked' : '';

echo '<div class="form-group"><input type="hidden" name="download" value="0">
	<label class="control-label control-label col-lg-4 col-sm-4">' . trans('global.download_site') . '</label>
	<div class="col-lg-8 col-sm-8"><div class="checkbox">
	<input data-class="switcher-success" type="checkbox" name="download" value="1"' . $checked . '></div></div></div>';
*/
echo '</div>';
echo '</div>';


echo '<legend>' . trans('admin.pricing') . '</legend>';

echo '<div class="row">';
echo '<div class="col-md-6">';

$monthly_settings = (isset($settings->monthly)) ? $settings->monthly : 0;

echo Former::number()
    ->name('monthly')
    ->forceValue($monthly_settings)
    ->step('any')
	->label(trans('admin.monthly'))
	->append(trans('admin.per_mo'));

echo '</div>';
echo '<div class="col-md-6">';

$currency_settings = (isset($settings->currency)) ? $settings->currency : 'USD';
$currencies = trans('currencies');

foreach($currencies as $abbr => $currency)
{
	$currency_array[$abbr] = $currency[0] . ' (' . $currency[1] . ')';
}

echo Former::select('currency')
	->class('select2-required form-control')
    ->name('currency')
    ->forceValue($currency_settings)
	->options($currency_array)
	->label(trans('admin.currency'));

echo '</div>';
echo '</div>';

echo '<div class="row">';
echo '<div class="col-md-6">';

$annual_settings = (isset($settings->annual)) ? $settings->annual : 0;

echo Former::number()
    ->name('annual')
    ->forceValue($annual_settings)
    ->step('any')
	->label(trans('admin.annual'))
	->append(trans('admin.per_mo'))
	->help(trans('admin.annual_info'));

echo '</div>';
echo '<div class="col-md-6">';

$featured_settings = (isset($settings->featured)) ? $settings->featured : false;
$checked = ($featured_settings) ? ' checked' : '';

echo '<div class="form-group"><input type="hidden" name="featured" value="0">
	<label class="control-label control-label col-lg-2 col-sm-4">' . trans('admin.featured') . '</label>
	<div class="col-lg-10 col-sm-8"><div class="checkbox">
	<input data-class="switcher-success" type="checkbox" name="featured" value="1"' . $checked . '></div></div></div>';

$publish_settings = (isset($settings->publish)) ? $settings->publish : true;
$checked = ($publish_settings) ? ' checked' : '';

echo '<div class="form-group"><input type="hidden" name="publish" value="0">
	<label class="control-label control-label col-lg-2 col-sm-4">' . trans('global.publish') . ' ' . trans('global.one_pages') . '</label>
	<div class="col-lg-10 col-sm-8"><div class="checkbox">
	<input data-class="switcher-success" type="checkbox" name="publish" value="1"' . $checked . '></div></div></div>';

echo '</div>';
echo '</div>';

echo '<hr>';

echo Former::actions()
    ->lg_primary_submit(trans('global.save'))
    ->lg_default_link(trans('global.cancel'), '#/admin/plans');

echo Former::close();
?>
		</div>
	</div>

<script>
function formSubmittedSuccess(result)
{
    if(result == 'error')
    {
        return;
    }
    document.location = '#/admin/plans';
}
</script>
@stop