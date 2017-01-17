@extends('../app.layouts.partial')

@section('content')
	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li>{{ trans('admin.system_administration') }}</li>
		<li><a href="#/admin/users">{{ trans('admin.user_administration') }}</a></li>
		<li class="active">{{ trans('global.edit_user') }}</li>
	</ul>

	<div class="page-header">
		<h1><i class="fa fa-database page-header-icon"></i> {{ trans('global.edit_user') }}</h1>
	</div>

	<div class="panel">
		<div class="panel-body padding-sm">
<?php
echo Former::open()
	->class('form-horizontal validate')
	->action(url('api/v1/admin/user-update'))
	->method('POST');

echo Former::hidden()
    ->name('sl')
    ->forceValue($sl);

echo '<div class="row">';
echo '<div class="col-md-6">';

echo Former::select('plan')
	->class('select2-required form-control')
    ->name('plan')
    ->forceValue($user->plan_id)
	->fromQuery(\App\Model\Plan::orderBy('sort')->get(), 'name', 'id')
	->label(trans('global.plan'));

$expires = ($user->expires != '') ? $user->expires->format('Y-m-d') : '';

echo Former::text()
    ->name('expires')
    ->forceValue($expires)
	->class('form-control date-picker-ymd')
	->label(trans('admin.expires'));

$disabled = ($user->reseller == 1) ? true : false;

echo Former::select('role')
	->class('select2-required form-control')
    ->name('role')
    ->forceValue($user->getRoles()[0]['id'])
	->options(Role::rolesAdmin())
	->label(trans('global.role') . ' ' . \App\Core\Help::popover('role_owner'))
	->disabled($disabled);

echo '<hr>';

echo Former::text()
    ->name('username')
    ->forceValue($user->username)
	->label(trans('global.username'))
	->dataBvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
    ->autocapitalize('off')
    ->autocorrect('off')
    ->autocomplete('off')
    ->required();

echo Former::email()
    ->name('email')
    ->forceValue($user->email)
	->label(trans('global.email'))
	->dataBvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
	->dataFvRegexp(true)
	->dataFvRegexpRegexp('^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$')
	->dataFvRegexpMessage(trans('global.please_enter_a_valid_email_address'))
    ->autocomplete('off')
    ->required();

echo Former::password()
    ->name('password')
    ->forceValue('')
	->label(trans('global.password'))
    ->append(Former::button('<i class="fa fa-eye"></i>')->id('show_password')->tooltip(trans('global.show_password'))->tooltipPlacement('top')->dataToggle('button'))
    ->append(Former::button('<i class="fa fa-random"></i>')->tooltip(trans('global.generate_password'))->tooltipPlacement('top')->id('generate_password'))
    ->help(trans('global.new_password_info_edit'));

echo Former::actions()
    ->lg_primary_submit(trans('global.save'))
    ->lg_default_link(trans('global.cancel'), '#/admin/users');

echo '</div>';
echo '<div class="col-md-6">';

echo Former::select('language')
	->class('select2-required form-control')
    ->name('language')
    ->forceValue($user->language)
	->options(\App\Controller\AccountController::languages())
	->label(trans('global.language'));

echo Former::select('timezone')
	->class('select2-required form-control')
    ->name('timezone')
    ->forceValue($user->timezone)
	->options(trans('timezones.timezones'))
	->label(trans('global.timezone'));

echo '<hr>';

echo Former::text()
    ->name('first_name')
    ->forceValue('')
    ->autocomplete('off')
	->label(trans('global.first_name'))
    ->forceValue($user->first_name);

echo Former::text()
    ->name('last_name')
    ->forceValue('')
    ->autocomplete('off')
	->label(trans('global.last_name'))
    ->forceValue($user->last_name);

echo '<hr>';


$disabled = ($user->reseller == 1) ? true : false;

echo Former::checkbox()
    ->name('confirmed')
    ->label(trans('global.active'))
	->dataClass('switcher-success')
    ->help(trans('global.user_active_info'))
	->disabled($disabled)
	->check((boolean)$user->confirmed);

echo Former::checkbox()
    ->disabled()
    ->name('send_mail')
    ->label(trans('global.send_mail'))
	->dataClass('switcher-success')
    ->help(trans('global.send_mail_info'))
	->check(false);

echo '</div>';
echo '</div>';

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
    document.location = '#/admin/users';
}

$('.input-group').on('keyup', '#password', checkSendMailBtn);

function checkSendMailBtn()
{
    if($('#password').val().length > 0)
    {
        $('#send_mail').switcher('enable');
    }
    else
    {
        $('#send_mail').switcher('off');
        $('#send_mail').switcher('disable');
    }
}

$('#show_password').on('click', function()
{
    if(! $(this).hasClass('active'))
    {
        togglePassword('password', true);
    }
    else
    {
        togglePassword('password', false);
    }
});

$('#generate_password').on('click', function()
{
    $('#password').val(randomString(10));
	checkSendMailBtn();
});
</script>
@stop