@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('admin.system_administration') }}</li>
		<li><a href="#/admin/plans">{{ trans('admin.user_plans') }}</a></li>
		<li class="active">{{ trans('admin.new_plan') }}</li>
	</ul>

	<div class="page-header">
		<h1><i class="fa fa-trophy page-header-icon"></i> {{ trans('admin.new_plan') }}</h1>
	</div>

	<div class="panel">
		<div class="panel-body padding-sm">
<?php
echo Former::open()
	->class('form-horizontal validate')
	->action(url('api/v1/admin/plan-new'))
	->method('POST');

echo '<div class="row">';
echo '<div class="col-md-6">';

echo Former::text()
    ->name('name')
    ->forceValue('')
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

$max_sites = array_combine(range(1,50), range(1,50));
array_unshift($max_sites, trans('admin.unlimited'));

echo Former::select('role')
	->class('select2-required form-control')
    ->name('max_sites')
    ->forceValue(0)
	->options($max_sites)
	->label(trans('admin.max_sites'));

$support_settings = '-';

echo Former::text()
    ->name('support')
    ->forceValue($support_settings)
	->label(trans('admin.support'))
	->help(trans('admin.support_info'));

echo '</div>';
echo '<div class="col-md-6">';

echo '<legend>&nbsp;</legend>';

$checked = ' checked';

echo '<div class="form-group"><input type="hidden" name="domain" value="0">
	<label class="control-label control-label col-lg-4 col-sm-4">' . trans('global.custom_domain') . '</label>
	<div class="col-lg-8 col-sm-8"><div class="checkbox">
	<input data-class="switcher-success" type="checkbox" name="domain" value="1"' . $checked . '></div></div></div>';
/*
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

$monthly_settings = 0;

echo Former::number()
    ->name('monthly')
    ->forceValue($monthly_settings)
    ->step('any')
	->label(trans('admin.monthly'))
	->append(trans('admin.per_mo'));

echo '</div>';
echo '<div class="col-md-6">';

$currency_settings = 'USD';
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

$annual_settings = 0;

echo Former::number()
    ->name('annual')
    ->forceValue($annual_settings)
    ->step('any')
	->label(trans('admin.annual'))
	->append(trans('admin.per_mo'))
	->help(trans('admin.annual_info'));

echo '</div>';
echo '<div class="col-md-6">';

$featured_settings = false;

$checked = ($featured_settings) ? ' checked' : '';

echo '<div class="form-group"><input type="hidden" name="featured" value="0">
	<label class="control-label control-label col-lg-2 col-sm-4">' . trans('admin.featured') . '</label>
	<div class="col-lg-10 col-sm-8"><div class="checkbox">
	<input data-class="switcher-success" type="checkbox" name="featured" value="1"' . $checked . '></div></div></div>';

$checked = ' checked';

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