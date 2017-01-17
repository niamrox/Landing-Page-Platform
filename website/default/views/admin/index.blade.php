<?php
$bg_image = \Lang::has('custom::global.bg_image') ? trans('custom::global.bg_image') : \Config::get('website::default.bg_image');
$bg_color = \Lang::has('custom::global.bg_color') ? trans('custom::global.bg_color') : \Config::get('website::default.bg_color');

$field_label = trans('website::admin.background_image');
$field_name = 'bg_image';
$field_value = $bg_image;
$field_help = '';
?>
<div class="form-group">
	<input type="hidden" name="{{ $field_name }}" id="{{ $field_name }}" value="{{ $field_value }}">
	<label class="control-label col-lg-12">{{ $field_label }}</label>
	<div class="col-lg-12">

		<div class="btn-group" role="group" style="margin-bottom:15px">
			<button type="button" class="btn btn-primary img-browse" data-id="{{ $field_name }}"><i class="fa fa-picture-o"></i> {{ trans('global.select_image') }}</button>
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

<div class="form-group">
	<label for="bg_color">{{ trans('website::admin.background_color') }}</label>
	<input type="text" id="bg_color" name="bg_color" class="form-control" value="{{ $bg_color }}">
</div>
<script>
$('#bg_color').minicolors({
	control: 'hue',
	position: 'bottom left',
	theme: 'bootstrap'
});

</script>