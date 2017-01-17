@extends('../app.layouts.frontend')

@section('body_class') page-signup @stop

@section('content')
	<div class="signup-container" style="display:none">
		<div class="signup-header">
			<a href="{{ url('/') }}" class="logo">{{ \App\Core\Settings::get('cms_title', trans('global.app_title')) }}</a>
			<div class="slogan">
				{{ \App\Core\Settings::get('cms_slogan', trans('global.app_title_slogan')) }}
			</div>
		</div>

		<div class="signup-form">
			<form action="{{ url('/signup') }}" id="signin-form_id" method="post">
				<input type="hidden" name="_token" value="{{{ Session::getToken() }}}">
				<div class="signup-text">
					<span>{{ Lang::get('global.create_an_account') }}</span>
				</div>

        @if (Session::get('error'))
            <div class="alert alert-error alert-danger">
                @if (is_array(Session::get('error')))
                    {{ head(Session::get('error')) }}
                @endif
            </div>
        @endif

        @if (Session::get('notice'))
            <div class="alert">{{ Session::get('notice') }}</div>
        @endif

				<div class="form-group w-icon">
					<input type="text" name="username" id="username" autocomplete="off" autocapitalize="off" autocorrect="off" class="form-control input-lg" placeholder="{{ Lang::get('confide.username') }}" value="{{{ Input::old('username') }}}" data-fv-notempty="true" data-fv-notempty-message="{{ trans('global.please_enter_a_value') }}">
					<span class="fa fa-user signup-form-icon"></span>
				</div>

				<div class="form-group w-icon">
					<input type="text" name="email" id="email" autocomplete="off" autocapitalize="off" autocorrect="off" class="form-control input-lg" placeholder="{{{ Lang::get('confide.e_mail') }}}" value="{{{ Input::old('email') }}}" data-fv-notempty="true" data-fv-notempty-message="{{ trans('global.please_enter_a_value') }}" data-fv-regexp="" data-fv-regexp-regexp="^[^@\s]+@([^@\s]+\.)+[^@\s]+$" data-fv-regexp-message="{{ trans('global.please_enter_a_valid_email_address') }}">
					<span class="fa fa-envelope signup-form-icon"></span>
				</div>

				<div class="form-group w-icon">
					<input type="password" name="password" id="password" class="form-control input-lg" placeholder="{{{ Lang::get('confide.password') }}}" data-fv-notempty="true" data-fv-notempty-message="{{ trans('global.please_enter_a_value') }}">
					<span class="fa fa-lock signup-form-icon"></span>
				</div>

				<div class="form-group w-icon">
					<input type="password" name="password_confirmation" id="password_confirmation" class="form-control input-lg" placeholder="{{{ Lang::get('confide.password_confirmation') }}}" data-fv-notempty="true" data-fv-notempty-message="{{ trans('global.please_enter_a_value') }}">
					<span class="fa fa-lock signup-form-icon"></span>
				</div>
<?php if(Config::get('system.toc_url') != '') { ?>
				<div class="form-group" style="margin-top: 20px;margin-bottom: 20px;">
						<span class="lbl">{{ sprintf(trans('global.agree_toc'), '<a href="' . Config::get('system.toc_url') . '" target="_blank">' . trans('global.toc') . '</a>') }}</span>
				</div>
<?php } ?>
				<div class="form-actions">
			        <input type="submit" class="signup-btn bg-primary" value="{{{ Lang::get('confide.signup.submit') }}}">
				</div>
			</form>

			<div class="signup-with">
				<a href="{{ url('/login') }}" class="signup-with-btn btn-success">{{ trans('global.already_have_an_account') }} <span>{{ trans('global.sign_in') }}</span></a>
			</div>

		</div>
	</div>

@stop

@section('page_bottom')

<script type="text/javascript">

	init.push(function () {
		$('.signup-container').flexVerticalCenter({ cssAttribute: 'margin-top'});

		setTimeout(function() { 
			$('.signup-container').fadeIn(400);
		}, 50);

		$('#signin-form_id').formValidation({
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