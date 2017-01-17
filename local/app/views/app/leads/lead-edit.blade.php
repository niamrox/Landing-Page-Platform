@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li><a href="#/leads">{{ trans('global.form_entries') }}</a></li>
		<li class="active">{{ trans('global.edit_form_entry') }}</li>
	</ul>

	<div class="page-header">
		<h1><i class="fa fa-pencil-square-o page-header-icon"></i> {{ trans('global.edit_form_entry') }}</h1>
	</div>

<?php
echo Former::open()
	->class('form ajax ajax-validate')
	->action(url('api/v1/lead/save'))
	->method('POST');

echo Former::hidden()
		->name('sl')
		->forceValue($sl);
?>
		  <div class="panel"> 
		   <div class="panel-body padding-sm">
<?php
echo Former::select('site_id')
	->class('select2-required form-control')
    ->name('site_id')
	->fromQuery($sites, 'name')
    ->forceValue($lead->site_id)
	->label(trans('global.website'));

echo Former::text()
    ->name('email')
    ->autocomplete('off')
	->dataFvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
	->dataFvRegexp(true)
	->dataFvRegexpRegexp('^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$')
	->dataFvRegexpMessage(trans('global.please_enter_a_valid_email_address'))
    ->forceValue($lead->email)
	->label(trans('global.email'));

$data = json_decode($lead->settings);

if($data != NULL)
{
    echo '<hr class="no-grid-gutter-h grid-gutter-margin-b no-margin-t">';

    foreach($data as $row)
    {
        echo '<div class="form-group"><label class="control-label col-lg-2 col-sm-4">' . $row->name . '</label><div class="col-lg-10 col-sm-8">' . $row->val . '</div></div>';
    }
}

echo '<hr class="no-grid-gutter-h grid-gutter-margin-b no-margin-t">';

echo Former::select('language')
	->class('select2-required form-control')
    ->name('language')
    ->id('language')
    ->forceValue($lead->language)
	->options(trans('languages.languages'))
	->label(trans('global.language'));

echo '<hr class="no-grid-gutter-h grid-gutter-margin-b no-margin-t">';

$os = ($lead->os == NULL) ? '-' : $lead->os;
$client = ($lead->client == NULL) ? '-' : $lead->client;
$device = ($lead->device == NULL) ? '-' : $lead->device;

echo '<div class="form-group"><label class="control-label col-lg-2 col-sm-4">' . trans('global.os') . '</label><div class="col-lg-10 col-sm-8">' . $os . '</div></div>';
echo '<div class="form-group"><label class="control-label col-lg-2 col-sm-4">' . trans('global.browser') . '</label><div class="col-lg-10 col-sm-8">' . $client . '</div></div>';
echo '<div class="form-group"><label class="control-label col-lg-2 col-sm-4">' . trans('global.device') . '</label><div class="col-lg-10 col-sm-8">' . $device . '</div></div>';

echo '<hr class="no-grid-gutter-h grid-gutter-margin-b no-margin-t">';

echo Former::actions(
    Former::submit(trans('global.save'))->class('btn-lg btn-primary btn')->id('btn-submit'),
    Former::link(trans('global.cancel'))->class('btn-lg btn-default btn')->href('#/leads')
);
?>
			 </div>
		   </div>
		</div>
<?php
echo Former::close();
?>

<script>
function formSubmittedSuccess(r)
{
    if(r.result == 'error')
    {
        return;
    }
	document.location = '#/leads';
}
</script>
@stop