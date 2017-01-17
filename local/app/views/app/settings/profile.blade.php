@extends('../app.layouts.partial')

@section('content')
		<ul class="breadcrumb breadcrumb-page">
			<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
			<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
			<li>{{ trans('global.settings') }}</li>
			<li class="active">{{ trans('global.my_profile') }}</li>
		</ul>

		<div class="profile-full-name">
			<span class="text-semibold"><i class="fa fa-user"></i> {{ Auth::user()->username }}</span>
		</div>
	 	<div class="profile-row">
			<div class="left-col">
				<div class="profile-block">
					<div class="panel profile-photo">
						<img src="{{ App\Controller\AccountController::getAvatar(128) }}" data-modal="{{ url('/app/modal/avatar') }}" class="hand avatar-128">
					</div>
				</div>

				<div class="panel panel-transparent profile-skills">
					<ul class="list-group">
						<li class="list-group-item">
							<a href="javascript:void(0);" class="btn btn-success btn-labeled" data-modal="{{ url('/app/modal/avatar') }}" style="width: 100%;"><span class="btn-label icon fa fa-cloud-upload"></span> {{ trans('global.change_avatar') }}</a>
						</li>
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
                          <td>{{ Auth::user()->logins }}</td>
                        </tr>
                        <tr>
                          <td class="text-center"><i class="fa fa-key"></i></td>
                          <td><strong>{{ trans('global.last_login') }}</strong></td>
                          <td data-moment="fromNowDateTime">{{ (Auth::user()->last_login != NULL) ? Auth::user()->last_login->timezone(Auth::user()->timezone)->format("Y-m-d H:i:s") : trans('global.never'); }}</td>
                        </tr>
                        <tr>
                          <td class="text-center"><i class="fa fa-pencil-square-o"></i></td>
                          <td><strong>{{ trans('global.registered') }}</strong></td>
                          <td data-moment="fromNowDateTime">{{ Auth::user()->created_at->timezone(Auth::user()->timezone)->format("Y-m-d H:i:s"); }}</td>
                        </tr>
<?php if(Auth::user()->can('system_management')) { ?>
                        <tr>
                          <td class="text-center"><i class="fa fa-code-fork"></i></td>
                          <td><strong>System version</strong></td>
                          <td>{{ Config::get('version.version') }}</td>
                        </tr>
<?php } ?>
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
							<li ng-class="{active: selectedTab == 2}">
								<a href="javascript:void(0);" ng-click="selectedTab = 2;">{{ trans('global.localization') }}</a>
							</li>
							<li ng-class="{active: selectedTab == 3}">
								<a href="javascript:void(0);" ng-click="selectedTab = 3;">{{ trans('global.change_password') }}</a>
							</li>
						</ul>
					</div>
<?php
echo Former::open()
	->class('form-horizontal validate')
	->action(url('api/v1/account/save'))
	->method('POST');
?>
					<div class="tab-content tab-content-bordered panel-padding">
						<div class="tab-pane fade" ng-class="{'in active': selectedTab == 1}">

<?php
echo Former::text()
    ->name('first_name')
    ->forceValue(Auth::user()->first_name)
    ->autocorrect('off')
	->label(trans('global.first_name'));

echo Former::text()
    ->name('last_name')
    ->forceValue(Auth::user()->last_name)
    ->autocorrect('off')
	->label(trans('global.last_name'));

echo Former::email()
    ->name('email')
    ->forceValue(Auth::user()->email)
	->label(trans('global.email'))
	->dataFvNotempty()
	->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
	->dataFvRegexp(true)
	->dataFvRegexpRegexp('^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$')
	->dataFvRegexpMessage(trans('global.please_enter_a_valid_email_address'))
    ->required();
?>
						</div> <!-- / .tab-pane -->

						<div class="tab-pane fade" ng-class="{'in active': selectedTab == 2}">
<?php
Former::setOption('default_form_type', 'vertical');
echo Former::select('timezone')
	->class('select2-required form-control')
    ->name('timezone')
    ->forceValue(Auth::user()->timezone)
	->options(trans('timezones.timezones'))
	->label(trans('global.timezone'));
?>
						</div> <!-- / .tab-pane -->

						<div class="tab-pane fade" ng-class="{'in active': selectedTab == 3}">
<?php
echo Former::password()
    ->name('new_password')
    ->forceValue('')
	->label(trans('global.new_password'))
    ->append(Former::button('<i class="fa fa-eye"></i>')->id('show_password')->tooltip(trans('global.show_password'))->tooltipPlacement('top')->dataToggle('button'))
    ->append(Former::button('<i class="fa fa-random"></i>')->tooltip(trans('global.generate_password'))->tooltipPlacement('top')->id('generate_password'))
	->help(trans('global.new_password_info'));
?>
						</div> <!-- / .tab-pane -->

					</div> <!-- / .tab-content -->

					<div class="tab-content tab-content-bordered panel-padding">
						<div class="tab-pane active">
<?php
if (Auth::user()->twitter == '' && Auth::user()->facebook == '')
{
	echo Former::password()
		->name('current_password')
		->forceValue('')
		->label(trans('global.current_password'))
		->help(trans('global.current_password_info'))
		->dataFvNotempty()
		->dataFvNotemptyMessage(trans('global.please_enter_a_value'))
		->required();
}

echo Former::actions()
    ->lg_primary_submit(trans('global.save'));

echo Former::close();
?>	
						</div>
					</div>

				</div>
			</div>
		</div>
<script>
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

function formSubmittedSuccess()
{
}
</script>
@stop