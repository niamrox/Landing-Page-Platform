<div class="modal-dialog" style="width:800px">
<?php
echo Former::open()
	->class('form-horizontal validate')
	->action(url('api/v1/admin/website-update'))
	->method('POST');

?>
	<div class="modal-content">
		<div class="modal-header">
			<button class="close" type="button" data-dismiss="modal">×</button>
			<?php echo trans('admin.website_settings') ?>
        </div>
		<div class="modal-body">

			<div class="container-fluid">

				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#tab-general" data-toggle="tab" onclick="return false;">{{ trans('global.general') }}</a>
					</li>
				</ul>

				<div class="tab-content tab-content-bordered">
					<div class="tab-pane fade active in" id="tab-general">
<?php
echo Former::text()
    ->name('page_title')
    ->forceValue($page_title)
	->label(trans('admin.page_title'))
    ->required();

echo Former::text()
    ->name('page_description')
    ->forceValue($page_description)
	->label(trans('admin.description'))
    ->required();

$field_label = trans('admin.favicon');
$field_name = 'favicon';
$field_value = $favicon;
$field_help = '';
?>
<div class="form-group">
	<label for="page_description" class="control-label col-lg-2 col-sm-4">{{ $field_label }}</label>
	<div class="col-lg-10 col-sm-8">


		<div class="btn-group" role="group" style="margin-bottom:15px">
			<button type="button" class="btn btn-primary img-browse" data-id="{{ $field_name }}"><i class="fa fa-picture-o"></i> {{ trans('global.select_image') }}</button>
			<button type="button" class="btn btn-danger img-remove" data-id="{{ $field_name }}" title="{{ trans('global.remove_image') }}"><i class="fa fa-remove"></i></button>
		</div>

		<div id="{{ $field_name }}-image">
<?php
if($field_value != '')
{
    echo '<img src="' . url($field_value) . '" class="thumbnail widget-thumb">';
}
?>
		</div>

	</div>
</div>


					</div>
				</div>

			</div>

		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" type="submit"><?php echo Lang::get('global.save') ?></button>
			<button class="btn" data-dismiss="modal" type="button"><?php echo Lang::get('global.close') ?></button>
		</div>
<?php
echo Former::close();
?>
	</div>
</div>
<script>
function formSubmittedSuccess()
{
    parent.showSaved();
    $modal.modal('hide');
}


var elfinderUrl = 'elfinder/standalonepopup/';

$('.modal-dialog').on('click', '.file-browse,.img-browse', function(event)
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

$('.modal-dialog').on('click', '.img-remove', function(event)
{
  if(event.handled !== true)
  {
	$('#' + $(this).attr('data-id') + '-image').html('<img src="/favicon.ico" class="thumbnail" style="max-width:100%; margin:0">');
	$('#' + $(this).attr('data-id')).val('/favicon.ico');
    event.handled = true;
  }
  return false;
});

$('.modal-dialog').on('click', '.file-remove', function(event)
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
		var w = (typeof $('#' + requestingField + '-image').attr('data-w') !== 'undefined') ? $('#' + requestingField + '-image').attr('data-w') : 120;
		var h = (typeof $('#' + requestingField + '-image').attr('data-h') !== 'undefined') ? $('#' + requestingField + '-image').attr('data-h') : 120;
		var img = decodeURI(filePath);
		var thumb = '{{ url('/api/v1/thumb/nail?') }}w=' + w + '&h=' + h + '&img=' + filePath;

		$('#' + requestingField + '-image').addClass('bg-loading');

		$('<img/>').attr('src', decodeURI(thumb)).load(function() {
			$(this).remove();
			$('#' + requestingField + '-image').html('<img src="' + thumb + '" class="thumbnail" style="max-width:100%; margin:0">');
			$('#' + requestingField + '-image').removeClass('bg-loading');
		});

        $('#' + requestingField).val(img);
    }
}

</script>