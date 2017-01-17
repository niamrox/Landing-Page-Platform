@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('admin.system_administration') }}</li>
        <li class="active">{{ trans('admin.cms') }}</li>
	</ul>

	<div class="page-header">
		<h1 style="height:32px"><i class="fa fa-dashboard page-header-icon"></i> {{ trans('admin.cms') }}</h1>
	</div>

	<div id="cms-settings">

<?php
Former::setOption('default_form_type', 'vertical');

echo Former::open()
	->class('validate ajax')
	->action(url('api/v1/admin/cms-update'))
	->method('POST');

?>
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#tab-general" data-toggle="tab" onclick="return false;">{{ trans('global.general') }}</a>
					</li>
					<li>
						<a href="#tab-design" data-toggle="tab" onclick="return false;">{{ trans('global.design') }}</a>
					</li>
<?php
if(Auth::user()->can('system_management') && Auth::user()->id == 1 && ! \Config::get('app.demo'))
{
?>
					<li>
						<a href="#tab-system" data-toggle="tab" onclick="return false;">{{ trans('admin.system_reset') }}</a>
					</li>
<?php
}
?>
				</ul>

				<div class="tab-content tab-content-bordered">
					<div class="tab-pane fade active in" id="tab-general">
<?php

echo Former::text()
    ->name('cms_title')
    ->forceValue($cms_title)
	->label(trans('global.title'))
	->dataFvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
    ->required();

echo Former::text()
    ->name('cms_slogan')
	->label('')
    ->forceValue($cms_slogan)
	->dataFvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
    ->required();

?>

					</div>

					<div class="tab-pane fade" id="tab-design">
<?php
$field_label = trans('admin.favicon');
$field_name = 'favicon';
$field_value = $favicon;
?>
<div class="form-group">
	<input type="hidden" name="{{ $field_name }}" id="{{ $field_name }}" value="{{ $field_value }}">
	<label class="control-label col-lg-12">{{ $field_label }} (<a href="http://www.favicon.cc/" target="_blank">www.favicon.cc</a>)</label>
	<div class="col-lg-12">

		<div class="btn-group" role="group" style="margin-bottom:15px">
			<button type="button" class="btn btn-primary img-browse" data-id="{{ $field_name }}"><i class="fa fa-picture-o"></i> {{ trans('global.select_image') }}</button>
			<button type="button" class="btn btn-warning img-reset" data-id="{{ $field_name }}" data-default="{{ '/favicon.ico' }}" title="{{ trans('global.reset') }}"><i class="fa fa-refresh"></i></button>
			<button type="button" class="btn btn-danger img-remove" data-id="{{ $field_name }}" title="{{ trans('global.remove_image') }}"><i class="fa fa-remove"></i></button>
		</div>

		<div id="{{ $field_name }}-image" data-thumb="0">
<?php
if($field_value != '')
{
    echo '<img src="' . url($field_value) . '" class="thumbnail widget-thumb">';
}
?>
		</div>

	</div>
</div>
<?php
$field_label = trans('admin.logo');
$field_name = 'cms_logo';
$field_value = $cms_logo;
?>
<div class="form-group">
	<input type="hidden" name="{{ $field_name }}" id="{{ $field_name }}" value="{{ $field_value }}">
	<label class="control-label col-lg-12">{{ $field_label }} (36 x 36 px)</label>
	<div class="col-lg-12">

		<div class="btn-group" role="group" style="margin-bottom:15px">
			<button type="button" class="btn btn-primary img-browse" data-id="{{ $field_name }}"><i class="fa fa-picture-o"></i> {{ trans('global.select_image') }}</button>
			<button type="button" class="btn btn-warning img-reset" data-id="{{ $field_name }}" data-default="{{ '/assets/images/interface/logo/icon.png' }}" title="{{ trans('global.reset') }}"><i class="fa fa-refresh"></i></button>
			<button type="button" class="btn btn-danger img-remove" data-id="{{ $field_name }}" title="{{ trans('global.remove_image') }}"><i class="fa fa-remove"></i></button>
		</div>

		<div id="{{ $field_name }}-image" data-w="36" data-h="36">
<?php
if($field_value != '')
{
    echo '<img src="' . url($field_value) . '" class="thumbnail widget-thumb" style="height:36px">';
}
?>
		</div>

	</div>
</div>
<?php
$field_label = trans('global.login');
$field_name = 'cms_bg_login';
$field_value = $cms_bg_login;
?>
<div class="form-group" style="margin-bottom:0">
	<input type="hidden" name="{{ $field_name }}" id="{{ $field_name }}" value="{{ $field_value }}">
	<label class="control-label col-lg-12">{{ $field_label }} (1920 x 1280 px)</label>
	<div class="col-lg-12">

		<div class="btn-group" role="group" style="margin-bottom:15px">
			<button type="button" class="btn btn-primary img-browse" data-id="{{ $field_name }}"><i class="fa fa-picture-o"></i> {{ trans('global.select_image') }}</button>
			<button type="button" class="btn btn-warning img-reset" data-id="{{ $field_name }}" data-default="{{ '/assets/images/bg/login.jpg' }}" title="{{ trans('global.reset') }}"><i class="fa fa-refresh"></i></button>
			<button type="button" class="btn btn-danger img-remove" data-id="{{ $field_name }}" title="{{ trans('global.remove_image') }}"><i class="fa fa-remove"></i></button>
		</div>

		<div id="{{ $field_name }}-image" data-w="192" data-h="108">
<?php
if($field_value != '')
{
    echo '<img src="' . url($field_value) . '" class="thumbnail widget-thumb" style="width:192px">';
}
?>
		</div>

	</div>
</div>

					</div>
<?php
if(Auth::user()->can('system_management') && Auth::user()->id == 1 && ! \Config::get('app.demo'))
{
?>
					<div class="tab-pane fade" id="tab-system">

						<button type="button" id="btn-reset-system" class="btn btn-danger btn-lg"><i class="fa fa-exclamation-triangle"></i> {{ trans('admin.system_reset') }}</button>

<script type="text/javascript">
$('#btn-reset-system').on('click', function() {

    if(confirm('This feature is only available for the root user. This will delete all cache, database data and user uploaded files and return a clean installation. Are you sure you want to reset the complete system?'))
    {
        if(confirm('[WARNING] Are you really sure you want to DELETE ALL DATA? NEVER DO THIS ON A PRODUCTION SYSTEM!'))
        {
            if(confirm('Please confirm one last time you are sure you want to reset the complete system and all its data.'))
            {
                blockUI();

                var request = $.ajax({
                  url: "{{ url('/api/v1/account/reset-system') }}",
                  type: 'POST',
                  data: {},
                  dataType: 'json'
                });

                request.done(function(json) {
                    document.location = '/';
                });

                request.fail(function(jqXHR, textStatus) {
                    alert('Request failed, please try again (' + textStatus + ')');
                    unblockUI();
                });
            }
        }
    }

});
</script>

					</div>
<?php
}
?>
				</div>
			<br>
<?php
echo Former::actions()
    ->lg_primary_submit(trans('global.save'));

echo Former::close();
?>
			</div>

<script>
var elfinderUrl = 'elfinder/standalonepopup/';

function formSubmittedSuccess(result)
{
    if(result == 'error')
    {
        return;
    }
	showSaved();
}

$('[data-class]').switcher(
{
	theme: 'square',
	on_state_content: '<span class="fa fa-check"></span>',
	off_state_content: '<span class="fa fa-times"></span>'
});

select2();

bsTooltipsPopovers();

$('select.image-picker').imagepicker();

$('.date-picker').datepicker({
	format: 'yyyy-mm-dd'
});

$('.time-picker').timepicker({
	minuteStep: 5,
	showSeconds: false,
	showMeridian: false,
	showInputs: false,
	orientation: $('body').hasClass('right-to-left') ? { x: 'right', y: 'auto'} : { x: 'auto', y: 'auto'}
});

$('#cms-settings').on('click', '.file-browse,.img-browse', function(event)
{
  if(event.handled !== true)
  {
	// trigger the reveal modal with elfinder inside
	$.colorbox(
	{
		href: elfinderUrl + $(this).attr('data-id') + '/processWidgetFile',
		fastIframe: true,
		iframe: true,
		width: '70%',
		height: '80%'
	});
    event.handled = true;
  }
  return false;

});

$('#cms-settings').on('click', '.img-reset', function(event)
{
  if(event.handled !== true)
  {
	var img_default = $(this).attr('data-default');
	var requestingField = $(this).attr('data-id');
	var thumb = (typeof $('#' + requestingField + '-image').attr('data-thumb') !== 'undefined') ? $('#' + requestingField + '-image').attr('data-thumb') : 1;

	if (thumb == 1)
	{
		var w = (typeof $('#' + requestingField + '-image').attr('data-w') !== 'undefined') ? $('#' + requestingField + '-image').attr('data-w') : 120;
		var h = (typeof $('#' + requestingField + '-image').attr('data-h') !== 'undefined') ? $('#' + requestingField + '-image').attr('data-h') : 120;
		thumb = '{{ url('/api/v1/thumb/nail?') }}w=' + w + '&h=' + h + '&img=' + img_default;
	}
	else
	{
		thumb = img_default;
	}

	$('#' + $(this).attr('data-id') + '-image').html('<img src="' + img_default + '" class="thumbnail" style="max-width:100%; margin:0; height:' + h + 'px">');
	$('#' + $(this).attr('data-id')).val(img_default);

    event.handled = true;
  }
  return false;
});

$('#cms-settings').on('click', '.img-remove', function(event)
{
  if(event.handled !== true)
  {
	$('#' + $(this).attr('data-id') + '-image').html('');
	$('#' + $(this).attr('data-id')).val('');
    event.handled = true;
  }
  return false;
});

$('#cms-settings').on('click', '.file-remove', function(event)
{
  if(event.handled !== true)
  {
	$('#' + $(this).attr('data-id')).val('');

    event.handled = true;
  }
  return false;
});

// Callback after elfinder selection
window.processWidgetFile = function(filePath, requestingField)
{
    if($('#' + requestingField).attr('type') == 'text')
    {
	    $('#' + requestingField).val(decodeURI(filePath));
    }

    if($('#' + requestingField + '-image').length)
    {
		var img = decodeURI(filePath);
		var thumb = (typeof $('#' + requestingField + '-image').attr('data-thumb') !== 'undefined') ? $('#' + requestingField + '-image').attr('data-thumb') : 1;

		if (thumb == 1)
		{
			var w = (typeof $('#' + requestingField + '-image').attr('data-w') !== 'undefined') ? $('#' + requestingField + '-image').attr('data-w') : 120;
			var h = (typeof $('#' + requestingField + '-image').attr('data-h') !== 'undefined') ? $('#' + requestingField + '-image').attr('data-h') : 120;
			thumb = '{{ url('/api/v1/thumb/nail?') }}w=' + w + '&h=' + h + '&img=' + filePath;
		}
		else
		{
			thumb = filePath;
		}

		$('#' + requestingField + '-image').addClass('bg-loading');

		$('<img/>').attr('src', decodeURI(thumb)).load(function() {
			$(this).remove();
			$('#' + requestingField + '-image').html('<img src="' + thumb + '" class="thumbnail" style="max-width:100%; margin:0">');
			$('#' + requestingField + '-image').removeClass('bg-loading');
		});

        $('#' + requestingField).val(img);
    }
}

$('form.ajax').ajaxForm({
    dataType: 'json',
    beforeSerialize: cmsBeforeSerialize,
    success: cmsFormResponse,
    error: cmsFormResponse
});

function cmsBeforeSerialize($jqForm, options)
{
    var form = $jqForm[0];

	// Summernote
	$('.summernote').each(function() {
		var content = $(this).code();
		$(this).val(content);

	});

    // Set non-checked checkboxes to value="0"
    var cb = form.getElementsByTagName('input');

    for(var i=0;i<cb.length;i++){ 
        if(cb[i].type=='checkbox' && !cb[i].checked)
        {
           cb[i].value = 0;
           cb[i].checked = true;
        }
    }

    // Loading state
    blockUI();
    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').addClass('loading');
    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').attr('disabled', 'disabled');
}

function cmsFormResponse(responseText, statusText, xhr, $jqForm)
{
    var form = $jqForm[0];

    // Remove possible old markup
    $(form).find('.ajax-help-inline').remove();
    $(form).find('.form-group').removeClass('has-error has-warning has-info has-success');

    // Process JSON response
    unblockUI();

    // Reset non-checked checkboxes
    var cb = form.getElementsByTagName('input');

    for(var i=0;i<cb.length;i++){ 
        if(cb[i].type=='checkbox' && cb[i].value == 0)
        {
           cb[i].value = 1;
           cb[i].checked = false;
        }
    }

	// Set title & logo
	var cms_title = $('#cms_title').val();
	var cms_logo = $('#cms_logo').val();
	$('.navbar-brand span').html(cms_title);
	$('.navbar-brand').attr('title', cms_title);
	document.title = cms_title;
	$('.navbar-brand div').attr('style', 'background-image: url(\'' + cms_logo + '\') !important');

	showSaved();

    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').removeAttr('disabled');
    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').removeClass('loading');
}
</script>
@stop