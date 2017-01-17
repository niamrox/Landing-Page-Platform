<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button class="close" type="button" data-dismiss="modal">×</button>
			<?php echo trans('global.change_avatar') ?>
        </div>
		<div class="modal-body">
			<div class="container-fluid">
				<div class="row">
					<div class="col-xs-3 col-md-3">
						<img src="{{ App\Controller\AccountController::getAvatar(128, '4ab6d5', $sl) }}" class="img-responsive thumbnail avatar-128 modal-avatar">
					</div>
					<div class="col-xs-9 col-md-9">
<?php
echo Former::open_for_files()
	->class('form-horizontal validate')
    ->target('iSubmit')
	->action(url('api/v1/account/avatar'))
	->method('POST');

echo Former::hidden()
    ->name('sl')
    ->forceValue($sl);

echo Former::file()
	->class('styled')
    ->name('avatar')
	->label(trans('global.new_avatar'))
	->help(trans('global.avatar_help'))
	->dataFvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
    ->required();

if($has_avatar)
{
	echo Former::actions()
		->lg_primary_submit(trans('global.upload'))
		->lg_danger_link(trans('global.delete'), "javascript:_confirm('" . url('/api/v1/account/delete-avatar') . "', '" . \App\Core\Secure::array2string(array('user_id' => $user_id)) . "', 'GET', avatarDeleted);");
}
else
{
	echo Former::actions()
		->lg_primary_submit(trans('global.upload'));
}

echo Former::close();
?>
                        <iframe name="iSubmit" id="iSubmit" frameborder="0" src="about:blank" style="display:none;width:0;height:0"></iframe>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" type="button"><?php echo Lang::get('global.close') ?></button>
		</div>
	</div>
</div>
<script>
override_form = true;

function avatarDeleted()
{
	$.get("{{ url('/api/v1/account/avatar/128/1cefce/' . \App\Core\Secure::array2string(array('user_id' => $user_id))) }}", function(data) {
		formSubmittedSuccess(data, data);
	});
}

function formSubmittedSuccess(img_small, img_medium)
{
    $('img.avatar-128.modal-avatar').attr('src', img_medium);

<?php if($own_avatar) { ?>

    $('img.avatar-32').attr('src', img_small);
    $('img.avatar-128').attr('src', img_medium);

<?php } ?>

	$modal.modal('hide');
    /*$('.pfi-clear').trigger('click');*/
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