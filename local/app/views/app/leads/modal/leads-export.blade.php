<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button class="close" type="button" data-dismiss="modal">×</button>
			<?php echo trans('global.export_form_entries') ?>
        </div>
		<div class="modal-body">
			<div class="container-fluid">
<?php

echo Former::open()
	->class('form-horizontal -validate -ajax')
	->action(url('app/leads/export'))
	->method('POST');
/*
echo Former::hidden()
	->name('sl')
	->forceValue($sl);
*/
echo Former::hidden()
	->name('leads')
	->id('leads');

echo Former::stacked_radios('export')
	->label(trans('global.export'))
    ->id('export')
    ->name('export')
	->radios(array(
		trans('global.all_form_entries') => array('name' => 'export', 'id' => 'export_all', 'value' => 'all'),
		trans('global.selected_form_entries') => array('name' => 'export', 'id' => 'export_selected', 'value' => 'selected')
	))
	->dataBvNotempty()
    ->required();

echo Former::select('to')
	->class('select2-required form-control')
    ->name('to')
	->options(array(
		'csv' => 'CSV',
		'xlsx' => 'Microsoft Excel 2007 (xlsx)',
		'xls' => 'Microsoft Excel 5 (xls)'/*,
		'mailchimp' => 'MailChimp'*/
	))
	->label(trans('global.to'));

echo Former::actions()
	->lg_primary_submit(trans('global.export'));	

echo Former::close();
?>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" type="button"><?php echo Lang::get('global.close') ?></button>
		</div>
	</div>
</div>
<script>
/*ajaxMalsupForm();*/

if(selected_leads.length == 0)
{
	$('#export_selected').prop('disabled', true);
}
else
{
    $('#leads').val(selected_leads);
}

function formSubmittedSuccess()
{
    //$modal.modal('hide');
}

$('form.validate').formValidation({
    framework: 'bootstrap',
    icon: {
        valid: null,
        invalid: null,
        validating: null
    }
});
</script>