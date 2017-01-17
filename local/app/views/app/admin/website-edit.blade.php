@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('admin.system_administration') }}</li>
        <li>{{ trans('admin.website') }}</li>
        <li><a href="#/admin/website">{{ trans('admin.templates') }}</a></li>
        <li class="active">{{ trans('admin.edit_template') }}</li>
	</ul>

	<div class="page-header">
		<h1 style="height:32px"><i class="fa fa-paint-brush page-header-icon"></i> {{ $template['lang']['name'] }}</h1>
	</div>

	<div id="template-content">
<?php
Former::setOption('default_form_type', 'vertical');

echo Former::open()
	->class('ajax')
	->action(url('api/v1/admin/template-update'))
	->method('POST');

echo Former::hidden()
    ->name('sl')
    ->forceValue($sl);
?>
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#tab-general" data-toggle="tab" onclick="return false;">{{ trans('global.general') }}</a>
					</li>
					<li>
						<a href="#tab-content" data-toggle="tab" onclick="return false;">{{ trans('global.content') }}</a>
					</li>
				</ul>

				<div class="tab-content tab-content-bordered">
					<div class="tab-pane fade active in" id="tab-general">
<?php
echo \View::make('website::admin.index', array('template_dir' => $template['dir']));
?>
					</div>

					<div class="tab-pane fade" id="tab-content">
<?php
$translations = $template['lang_original'];

echo Former::hidden()
    ->name('name')
    ->forceValue($translations['name']);

unset($translations['name']);

foreach($translations as $key => $translation)
{
	$type = explode('_', $key);
	$type = (isset($type[0])) ? $type[0] : 'txt';

	$val = (isset($template['lang'][$key])) ? $template['lang'][$key] : '';

	$label = (\Lang::has('website::admin.label_' . $key)) ? trans('website::admin.label_' . $key) : $template['lang_original'][$key];
	$help = (\Lang::has('website::admin.help_' . $key)) ? trans('website::admin.help_' . $key) : '';

	switch($type)
	{
		case 'area':
			echo Former::textarea()
				->name($key)
				->forceValue(str_replace('<br>', chr(13), $val))
				->label($label)
				->help($help)
				->rows(3)
				->required();
			break;
		case 'rich':
			echo Former::textarea()
				->name($key)
				->forceValue($val)
				->label($label)
				->help($help)
				->rows(3)
				->required();
			break;
		default:
			echo Former::text()
				->name($key)
				->forceValue($val)
				->label($label)
				->help($help)
				->required();
	}
}

?>
					</div>
				</div>
<br>

<?php
echo Former::actions()
    ->lg_primary_submit(trans('global.save'))
    ->lg_default_link(trans('global.cancel'), '#/admin/website');

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

$('#template-content').on('click', '.file-browse,.img-browse', function(event)
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

$('#template-content').on('click', '.img-remove', function(event)
{
  if(event.handled !== true)
  {
	$('#' + $(this).attr('data-id') + '-image').html('');
	$('#' + $(this).attr('data-id')).val('');
    event.handled = true;
  }
  return false;
});

$('#template-content').on('click', '.file-remove', function(event)
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
    beforeSerialize: websiteBeforeSerialize,
    success: websiteFormResponse,
    error: websiteFormResponse
});

function websiteBeforeSerialize($jqForm, options)
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

function websiteFormResponse(responseText, statusText, xhr, $jqForm)
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

	showSaved();

    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').removeAttr('disabled');
    $(form).find('.sumbit,button[type="submit"],input[type="submit"]').removeClass('loading');
}
</script>
@stop