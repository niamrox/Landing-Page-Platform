@extends('../app.layouts.frontend')

@section('body_class') page-signin @stop

@section('content')
	<div class="signin-container" style="display:none;">

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

			<form method="post" action="{{{ URL::to('/forgot_password') }}}" accept-charset="UTF-8" id="password-reset-form_id">
				<input type="hidden" name="_token" value="{{{ Session::getToken() }}}">
				<div class="signin-text">
					<span>{{{ trans('global.forgot_password') }}}</span>
				</div>

				@if ( Session::get('error') )
					<div class="alert alert-error alert-danger">{{{ Session::get('error') }}}</div>
				@endif

				@if ( Session::get('notice') )
					<div class="alert">{{{ Session::get('notice') }}}</div>
				@endif

				<div class="form-group w-icon">
					<input type="text" name="email" autocomplete="off" autocapitalize="off" autocorrect="off" id="email" class="form-control input-lg" placeholder="{{{ Lang::get('confide.e_mail') }}}" value="{{{ Input::old('email') }}}" data-fv-notempty data-fv-emailaddress>
					<span class="fa fa-envelope signin-form-icon"></span>
				</div>

				<div class="form-actions">
					<input type="submit" value="{{{ Lang::get('confide.forgot.submit') }}}" class="signin-btn bg-primary">
					<a href="{{{ URL::to('/login') }}}" class="forgot-password">{{{ trans('global.sign_in_account') }}}</a>
				</div>

			</form>
<?php if (\Config::get('system.allow_registration')) { ?>
			<div class="signin-with">
				<a href="{{{ URL::to('/signup') }}}" class="signin-with-btn btn-success" >{{{ trans('global.sign_up_now') }}}</a>
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

		$('#password-reset-form_id').formValidation({
            framework: 'bootstrap',
			icon: {
				valid: 'fa fa-check',
				invalid: 'fa fa-times',
				validating: 'fa fa-refresh'
			}
		});
	});

	window.CmsAdmin.start(init);
</script>

@stop