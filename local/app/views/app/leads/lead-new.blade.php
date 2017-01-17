@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li><a href="#/leads">{{ trans('global.form_entries') }}</a></li>
		<li class="active">{{ trans('global.add_form_entry') }}</li>
	</ul>

	<div class="page-header">
		<h1><i class="fa fa-pencil-square-o page-header-icon"></i> {{ trans('global.add_form_entry') }}</h1>
	</div>

<?php
echo Former::open()
	->class('form ajax ajax-validate')
	->action(url('api/v1/lead/save'))
	->method('POST');
?>
		  <div class="panel">
		   <div class="panel-body padding-sm">
<?php
echo Former::select('site_id')
	->class('select2-required form-control')
    ->name('site_id')
	->fromQuery($sites, 'name')
	->label(trans('global.website'));

echo Former::text()
    ->name('email')
    ->autocomplete('off')
	->dataFvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
	->dataFvRegexp(true)
	->dataFvRegexpRegexp('^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$')
	->dataFvRegexpMessage(trans('global.please_enter_a_valid_email_address'))
    ->forceValue('')
	->label(trans('global.email'));

echo Former::select('language')
	->class('select2-required form-control')
    ->name('language')
    ->id('language')
    ->forceValue(\Auth::user()->language)
	->options(trans('languages.languages'))
	->label(trans('global.language'));

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