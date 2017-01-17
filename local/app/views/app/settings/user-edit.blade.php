@extends('../app.layouts.partial')

@section('content')
		<ul class="breadcrumb breadcrumb-page">
			<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
			<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
			<li>{{ trans('global.settings') }}</li>
			<li><a href="#/users">{{ trans('global.team_management') }}</a></li>
			<li class="active">{{ trans('global.edit_team_member') }}</li>
		</ul>

		<div class="profile-full-name">
			<span class="text-semibold"><i class="fa fa-user"></i> {{ $user->username }}</span>
		</div>
	 	<div class="profile-row">
			<div class="left-col">
				<div class="profile-block">
					<div class="panel profile-photo">
						<img src="{{ App\Controller\AccountController::getAvatar(128, '4ab6d5', $sl) }}" data-modal="{{ url('/app/modal/avatar?sl=' . $sl) }}" class="hand avatar-128 modal-avatar">
					</div>
				</div>

				<div class="panel panel-transparent profile-skills">
					<ul class="list-group">
						<li class="list-group-item">
							<a href="javascript:void(0);" class="btn btn-success btn-labeled" data-modal="{{ url('/app/modal/avatar?sl=' . $sl) }}" style="width: 100%;"><span class="btn-label icon fa fa-cloud-upload"></span> {{ trans('global.change_avatar') }}</a>
						</li>
<?php if($user->id != Auth::user()->id) { ?>
						<li class="list-group-item">
							<a href="{{ url('/api/v1/account/login-as-user?sl=' . $sl) }}" class="btn btn-primary btn-labeled" style="width: 100%;"><span class="btn-label icon fa fa-sign-in"></span> {{ trans('global.login_as_user') }}</a>
						</li>
<?php } ?>
<?php if($user->parent_id != NULL) { ?>
						<li class="list-group-item">
							<a href="javascript:void(0);" class="btn btn-danger btn-labeled" onclick="_confirm('{{ url('/api/v1/account/delete-user') }}', '{{ $sl }}', 'GET', userDeleted);" style="width: 100%;"><span class="btn-label icon fa fa-times"></span> {{ trans('global.delete_user') }}</a>
						</li>
<?php } ?>
					</ul>
				</div>

				<div class="panel panel-transparent">
					<div class="panel-heading">
						<span class="panel-title">{{ trans('global.statistics') }}</span>
					</div>
                    <table class="padded">
                      <tbody>
                        <tr>
                          <td class="text-center"><i class="fa fa-unlock-alt"></i></td>
                          <td><strong>{{ trans('global.logins') }}</strong></td>
                          <td>{{ $user->logins }}</td>
                        </tr>
                        <tr>
                          <td class="text-center"><i class="fa fa-key"></i></td>
                          <td><strong>{{ trans('global.last_login') }}</strong></td>
                          <td data-moment="fromNowDateTime">{{ ($user->last_login != NULL) ? $user->last_login->timezone($user->timezone)->format("Y-m-d H:i:s") : trans('global.never'); }}</td>
                        </tr>
                        <tr>
                          <td class="text-center"><i class="fa fa-pencil-square-o"></i></td>
                          <td><strong>{{ trans('global.registered') }}</strong></td>
                          <td data-moment="fromNowDateTime">{{ $user->created_at->timezone($user->timezone)->format("Y-m-d H:i:s"); }}</td>
                        </tr>
                      </tbody>
                    </table>
				</div>

			</div>
			<div class="right-col">

				<hr class="profile-content-hr no-grid-gutter-h">
				
				<div class="profile-content" ng-init="selectedTab = 1;">

					<div class="scroller scroller-left"><i class="fa fa-chevron-left"></i></div>
					<div class="scroller scroller-right"><i class="fa fa-chevron-right"></i></div>

					<div class="scrolltabs-wrapper">
						<ul id="profile-tabs" class="nav nav-tabs scrolltabs">
							<li ng-class="{active: selectedTab == 1}">
								<a href="javascript:void(0);" ng-click="selectedTab = 1;">{{ trans('global.personal') }}</a>
							</li>
<?php if($user->parent_id != NULL) { ?>
							<li ng-class="{active: selectedTab == 2}">
								<a href="javascript:void(0);" ng-click="selectedTab = 2;">{{ trans('global.permissions') }}</a>
							</li>
<?php } ?>
							<li ng-class="{active: selectedTab == 3}">
								<a href="javascript:void(0);" ng-click="selectedTab = 3;">{{ trans('global.localization') }}</a>
							</li>
							<li ng-class="{active: selectedTab == 4}">
								<a href="javascript:void(0);" ng-click="selectedTab = 4;">{{ trans('global.change_password') }}</a>
							</li>
						</ul>
					</div>
<?php
echo Former::open()
	->class('form-horizontal validate')
	->action(url('api/v1/account/user-update'))
	->method('POST');

echo Former::hidden()
    ->name('sl')
    ->forceValue($sl);
?>
					<div class="tab-content tab-content-bordered panel-padding">
						<div class="tab-pane fade" ng-class="{'in active': selectedTab == 1}">

<?php
echo Former::text()
    ->name('first_name')
    ->forceValue($user->first_name)
    ->autocorrect('off')
	->label(trans('global.first_name'));

echo Former::text()
    ->name('last_name')
    ->forceValue($user->last_name)
    ->autocorrect('off')
	->label(trans('global.last_name'));

echo Former::email()
    ->name('email')
    ->forceValue($user->email)
	->label(trans('global.email'))
	->dataFvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
	->dataFvRegexp(true)
	->dataFvRegexpRegexp('^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$')
	->dataFvRegexpMessage(trans('global.please_enter_a_valid_email_address'))
    ->required();

if($user->parent_id != NULL)
{
    echo '<hr class="panel-wide">';

    echo Former::checkbox()
        ->name('confirmed')
        ->label(trans('global.active'))
        ->dataClass('switcher-success')
    	->help(trans('global.user_active_info'))
        ->check((boolean)$user->confirmed);
}

?>
						</div> <!-- / .tab-pane -->
<?php if($user->parent_id != NULL) { ?>
						<div class="tab-pane fade" ng-class="{'in active': selectedTab == 2}">
<?php
echo Former::select('role')
	->class('select2-required form-control')
    ->name('role')
    ->forceValue($user->getRoleId())
	->options(Role::roles())
	->label(trans('global.role') . ' ' . \App\Core\Help::popover('role'));

?>
						</div> <!-- / .tab-pane -->
<?php } ?>
						<div class="tab-pane fade" ng-class="{'in active': selectedTab == 3}">
<?php
echo Former::select('timezone')
	->class('select2-required form-control')
    ->name('timezone')
    ->forceValue($user->timezone)
	->options(trans('timezones.timezones'))
	->label(trans('global.timezone'));
?>
						</div> <!-- / .tab-pane -->

						<div class="tab-pane fade" ng-class="{'in active': selectedTab == 4}">
<?php
echo Former::password()
    ->name('new_password')
    ->forceValue('')
	->label(trans('global.new_password'))
    ->append(Former::button('<i class="fa fa-eye"></i>')->id('show_password')->tooltip(trans('global.show_password'))->tooltipPlacement('top')->dataToggle('button'))
    ->append(Former::button('<i class="fa fa-random"></i>')->tooltip(trans('global.generate_password'))->tooltipPlacement('top')->id('generate_password'))
	->help(trans('global.new_password_info_edit'));
?>
						</div> <!-- / .tab-pane -->

					</div> <!-- / .tab-content -->

					<div class="tab-content tab-content-bordered panel-padding">
						<div class="tab-pane active">
<?php
echo Former::password()
    ->name('current_password')
    ->forceValue('')
	->label(trans('global.current_password'))
	->help(trans('global.current_password_info'))
	->dataFvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
    ->required();

echo Former::actions()
    ->lg_primary_submit(trans('global.save'))
    ->lg_default_link(trans('global.cancel'), '#/users');

echo Former::close();
?>	
						</div>
					</div>

				</div>
			</div>
		</div>
<script>
function formSubmittedSuccess(result)
{
    if(result == 'success')
    {
        document.location = '#/users';
    }
}

checkRole();
$('#role').on('change', checkRole);

function checkRole()
{
	var role = parseInt($('#role').val());
	if(role == 4) // User
	{
		$('#role-permissions').show();
	}
	else
	{
		$('#role-permissions').hide();
	}
	
}

$('#show_password').on('click', function()
{
    if(! $(this).hasClass('active'))
    {
        togglePassword('new_password', true);
    }
    else
    {
        togglePassword('new_password', false);
    }
});

$('#generate_password').on('click', function()
{
    $('#new_password').val(randomString(10));
});
</script>
@stop