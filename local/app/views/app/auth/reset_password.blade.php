@extends('../app.layouts.frontend')

@section('body_class') page-signin @stop

@section('content')
	<!-- Container -->
	<div class="signin-container" style="display:none">

		<!-- Left side -->

		<div class="signin-info">
			<a href="{{ url('/') }}" class="logo">{{ \App\Core\Settings::get('cms_title', trans('global.app_title')) }}</a>
			<div class="slogan">
				{{ \App\Core\Settings::get('cms_slogan', trans('global.app_title_slogan')) }}
			</div>
			<ul>
				<li><i class="fa fa-desktop signin-icon"></i> {{ trans('global.bullet_point1') }}</li>
				<li><i class="fa fa-tint signin-icon"></i> {{ trans('global.bullet_point2') }}</li>
				<li><i class="fa fa-code signin-icon"></i> {{ trans('global.bullet_point3') }}</li>
				<li><i class="fa fa-heart signin-icon"></i> {{ trans('global.bullet_point4') }}</li>
			</ul>
		</div>
		<!-- / Left side -->

		<!-- Right side -->
		<div class="signin-form">

			<!-- Form -->
			<form method="post" action="{{{ URL::to('/reset_password') }}}" accept-charset="UTF-8" id="reset-form_id">
				<input type="hidden" name="token" value="{{{ $token }}}">
				<input type="hidden" name="_token" value="{{{ Session::getToken() }}}">
				<div class="signin-text">
					<span>{{ trans('global.new_password') }}</span>
				</div>

				@if ( Session::get('error') )
					<div class="alert alert-error">{{{ Session::get('error') }}}</div>
				@endif

				@if ( Session::get('notice') )
					<div class="alert">{{{ Session::get('notice') }}}</div>
				@endif

				<div class="form-group w-icon">
					<input type="password" name="password" id="password" class="form-control input-lg" placeholder="{{{ Lang::get('confide.password') }}}" data-fv-notempty="true" data-fv-stringlength-min="4">
					<span class="fa fa-lock signin-form-icon"></span>
				</div>

				<div class="form-group w-icon">
					<input type="password" name="password_confirmation" id="password_confirmation" class="form-control input-lg" placeholder="{{{ Lang::get('confide.password_confirmation') }}}" data-fv-notempty="true" data-fv-stringlength-min="4">
					<span class="fa fa-lock signin-form-icon"></span>
				</div>

				<div class="form-actions">
					<input type="submit" value="{{{ Lang::get('confide.forgot.submit') }}}" class="signin-btn bg-primary">
					<a href="{{{ URL::to('/login') }}}" class="forgot-password">{{{ Lang::get('global.login') }}}</a>
				</div> <!-- / .form-actions -->
			</form>
			<!-- / Form -->
<?php if (\Config::get('system.allow_registration')) { ?>
			<div class="signin-with">
				<a href="{{ url('/signup') }}" class="signin-with-btn btn-success" >{{{ trans('global.sign_up_now') }}}</a>
			</div>
<?php } ?>

		</div>
		<!-- Right side -->
	</div>
	<!-- / Container -->

@stop

@section('page_bottom')

<script type="text/javascript">

	init.push(function () {
		$('.signin-container').flexVerticalCenter({ cssAttribute: 'margin-top'});

		setTimeout(function() { 
			$('.signin-container').fadeIn(400);
		}, 100);

		$('#reset-form_id').formValidation({
            framework: 'bootstrap',
			icon: {
				valid: 'fa fa-check',
				invalid: 'fa fa-times',
				validating: 'fa fa-refresh'
			},
			fields: {
				password: {
					validators: {
						stringLength: {
							min: 4
						}
					}
				},
				password_confirmation: {
					validators: {
						stringLength: {
							min: 4
						},
						identical: {
							field: 'password'
						}
					}
				}
			}
		});
	});

	window.CmsAdmin.start(init);
</script>

@stop
