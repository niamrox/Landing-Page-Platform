@extends('../app.layouts.frontend')

@section('body_class') page-signin @stop

@section('content')
	<div class="signin-container" style="display:none">

		<div class="signin-info">
			<a href="{{ url('/') }}" class="logo" style="display:block">{{ \App\Core\Settings::get('cms_title', trans('global.app_title')) }}</a>
			<div class="slogan" style="display:block">
				{{ \App\Core\Settings::get('cms_slogan', trans('global.app_title_slogan')) }}
			</div>
			<ul>
				<li><i class="fa fa-desktop signin-icon"></i> {{ trans('global.bullet_point1') }}</li>
				<li><i class="fa fa-tint signin-icon"></i> {{ trans('global.bullet_point2') }}</li>
				<li><i class="fa fa-code signin-icon"></i> {{ trans('global.bullet_point3') }}</li>
				<li><i class="fa fa-heart signin-icon"></i> {{ trans('global.bullet_point4') }}</li>
			</ul>
		</div>

		<div class="signin-form">
			<form method="post" action="{{{ URL::to('/login') }}}" accept-charset="UTF-8" id="signin-form_id">
				<input type="hidden" name="_token" value="{{{ Session::getToken() }}}">
				<div class="signin-text">
					<span>{{{ Lang::get('global.sign_in_account') }}}</span>
				</div>

				@if ( Session::get('error') )
					<div class="alert alert-error">{{{ Session::get('error') }}}</div>
				@endif

				@if ( Session::get('notice') )
					<div class="alert">{{{ Session::get('notice') }}}</div>
				@endif

<?php
$email = (Input::old('email') != '') ? Input::old('email') : '';
$password = ''; 
if(\Config::get('app.demo'))
{
	if($email == '') $email = 'info@example.com';
	$password = 'welcome'; 
	echo '<div class="alert alert-info">This demo is reset every hour. If you can\'t login, please come back later. Thank you!</div>';
	echo '<div class="alert alert-warning"><span id="countdown">Loading...</span></div>';
?>
<script>
var server_end = <?php echo strtotime(date('Y-m-d H:0:0') . ' + 1 hours'); ?> * 1000;
var server_now = <?php echo time(); ?> * 1000;
var client_now = new Date().getTime();
var end = server_end - server_now + client_now;

var _second = 1000;
var _minute = _second * 60;
var _hour = _minute * 60;
var _day = _hour * 24;

var timer;

function showRemaining()
{
    var now = new Date();
    var distance = end - now;
    if (distance < 0 ) {
      var countdownElement = document.getElementById('countdown');
      countdownElement.innerHTML = 'Start reset...';
      setTimeout(function() {
  		  document.location.reload();
      }, 3000);
    }
    else
    {
      var days = Math.floor(distance / _day);
      var hours = Math.floor( (distance % _day ) / _hour );
      var minutes = Math.floor( (distance % _hour) / _minute );
      var seconds = Math.floor( (distance % _minute) / _second );

      var countdownElement = document.getElementById('countdown');
      countdownElement.innerHTML = 'Next reset in ' + minutes + ' minutes and ' + seconds + ' seconds.';
    }
}

timer = setInterval(showRemaining, 1000);
</script>
<?php
}
?>
<?php
$languages = \App\Controller\AccountController::getLanguages();

if(count($languages) > 1)
{
?>
				<div class="form-group">
					<select class="form-control input-lg" name="lang" id="lang" style="line-height: 34px;font-size:15px">
<?php
foreach($languages as $language)
{
    $active = ($language['active']) ? ' selected' : '';
    echo '<option' . $active . ' value="' . $language['code'] . '">' . $language['title'] . '</option>';
}
?>
					</select>
				</div>
<?php } ?>
				<div class="form-group w-icon">
					<input type="text" name="email" id="email" autocomplete="off" autocapitalize="off" autocorrect="off" class="form-control input-lg" placeholder="{{{ Lang::get('confide.username_e_mail') }}}" value="{{{ $email }}}" data-fv-notempty="true" data-fv-notempty-message="{{ trans('global.please_enter_a_value') }}">
					<span class="fa fa-user signin-form-icon"></span>
				</div>

				<div class="form-group w-icon">
					<input type="password" name="password" id="password" class="form-control input-lg" placeholder="{{{ Lang::get('confide.password') }}}" value="{{{ $password }}}" data-fv-notempty="true" data-fv-notempty-message="{{ trans('global.please_enter_a_value') }}">
					<span class="fa fa-lock signin-form-icon"></span>
				</div>

				<div class="checkbox" style="margin:15px 0 0 0">
					<label for="remember" class="checkbox">
						<input type="checkbox" name="remember" id="remember" value="1"{{ (Input::old('remember') == '1') ? ' checked' : ''; }}> {{{ Lang::get('confide.login.remember') }}}
					</label>
				</div>

				<div class="form-actions">
					<input type="submit" value="{{{ Lang::get('confide.login.submit') }}}" class="signin-btn bg-primary">
					<a href="{{{ URL::to('/forgot_password') }}}" class="forgot-password">{{{ Lang::get('global.forgot_password') }}}</a>
				</div>
			</form>
<?php if (\Config::get('system.allow_registration')) { ?>
			<div class="signin-with">

<?php if (\Config::get('social-login.facebook.app_id') != '') { ?>
				<a href="{{ url('/api/v1/account/login-facebook') }}" class="signin-with-btn btn-default" style="background-color:#3B5998; background-image:none"><i class="fa fa-facebook"></i> {{{ trans('auth.sign_in_with', ['provider' => 'Facebook']) }}}</a>
<?php } ?>

<?php if (\Config::get('social-login.twitter.api_key') != '') { ?>
				<a href="{{ url('/api/v1/account/login-twitter') }}" class="signin-with-btn btn-default" style="background-color:#55acee; background-image:none"><i class="fa fa-twitter"></i> {{{ trans('auth.sign_in_with', ['provider' => 'Twitter']) }}}</a>
<?php } ?>

				<a href="{{ url('/signup') }}" class="signin-with-btn btn-success"><i class="fa fa-envelope-o"></i> {{{ trans('global.sign_up_now') }}}</a>
			</div>
<?php } ?>
		</div>

	</div>

@stop

@section('page_bottom')

<script type="text/javascript">

	init.push(function () {
		$('.signin-container').flexVerticalCenter({ cssAttribute: 'margin-top'});

		setTimeout(function() { 
			$('.signin-container').fadeIn(400);
		}, 100);

		$('#signin-form_id').formValidation({
            framework: 'bootstrap',
			icon: {
				valid: 'fa fa-check',
				invalid: 'fa fa-times',
				validating: 'fa fa-refresh'
			}
		});

		$('#lang').on('change', function() {
			document.location = '?lang=' + $(this).val();
		});
	});

	window.CmsAdmin.start(init);
</script>

@stop