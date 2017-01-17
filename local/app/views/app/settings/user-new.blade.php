@extends('../app.layouts.partial')

@section('content')
		<ul class="breadcrumb breadcrumb-page">
			<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
			<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
			<li>{{ trans('global.settings') }}</li>
			<li><a href="#/users">{{ trans('global.team_management') }}</a></li>
			<li class="active">{{ trans('global.add_team_member') }}</li>
		</ul>

		<div class="page-header">
			<h1 style="height:32px"><i class="fa fa-user-plus page-header-icon"></i> {{ trans('global.new_team_member') }}</h1>
		</div>

		<div class="panel">
			<div class="panel-body padding-sm">
<?php
echo Former::open()
	->class('form-horizontal validate')
	->action(url('api/v1/account/user-new'))
	->method('POST');

echo '<div class="row">';
echo '<div class="col-md-6">';

echo '<legend>' . trans('global.login') . '</legend>';

echo Former::text()
    ->name('username')
    ->forceValue('')
	->label(trans('global.username'))
	->dataFvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
    ->autocapitalize('off')
    ->autocorrect('off')
    ->autocomplete('off')
    ->required();

echo Former::text()
    ->name('email')
    ->forceValue('')
	->label(trans('global.email'))
	->dataFvNotempty()
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
    ->required()
    ->append(Former::button('<i class="fa fa-eye"></i>')->id('show_password')->tooltip(trans('global.show_password'))->tooltipPlacement('top')->dataToggle('button'))
    ->append(Former::button('<i class="fa fa-random"></i>')->tooltip(trans('global.generate_password'))->tooltipPlacement('top')->id('generate_password'))
	->dataFvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'));


echo '<br><legend>' . trans('global.personal') . '</legend>';

echo Former::text()
    ->name('first_name')
    ->forceValue('')
    ->autocomplete('off')
	->label(trans('global.first_name'));

echo Former::text()
    ->name('last_name')
    ->forceValue('')
    ->autocomplete('off')
	->label(trans('global.last_name'));

echo '<br>';

echo Former::actions()
    ->lg_primary_submit(trans('global.save'))
    ->lg_default_link(trans('global.cancel'), '#/users');

echo '</div>';
echo '<div class="col-md-6">';


echo '<legend>' . trans('global.general') . '</legend>';

echo Former::select('role')
	->class('select2-required form-control')
    ->name('role')
    ->forceValue(3)
	->options(Role::roles())
	->label(trans('global.role') . ' ' . \App\Core\Help::popover('role'));

$languages = \App\Controller\AccountController::languages();

echo Former::select('language')
	->class('select2-required form-control')
    ->name('language')
    ->forceValue(Auth::user()->language)
	->options($languages)
	->label(trans('global.language'));

echo Former::select('timezone')
	->class('select2-required form-control')
    ->name('timezone')
    ->forceValue(Auth::user()->timezone)
	->options(trans('timezones.timezones'))
	->label(trans('global.timezone'));

echo '<br><legend>' . trans('global.account') . '</legend>';

echo Former::checkbox()
    ->name('confirmed')
    ->label(trans('global.active'))
	->dataClass('switcher-success')
    ->help(trans('global.user_active_info'))
	->check();

echo Former::checkbox()
    ->name('send_mail')
    ->label(trans('global.send_mail'))
	->dataClass('switcher-success')
    ->help(trans('global.send_mail_info'))
	->check();

echo '</div>';
echo '</div>';

echo Former::close();
?>
			</div>
		</div>

<script>
override_form = true;

function formSubmittedSuccess(result)
{
    if(result == 'success')
    {
        document.location = '#/users';
    }
}

$('form.validate').formValidation({
    framework: 'bootstrap',
    icon: {
        valid: null,
        invalid: null,
        validating: null
    }
});

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
});
</script>
@stop