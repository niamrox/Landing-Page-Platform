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
				@if ( $update_found )
					<li><strong>1. Updates found</strong></li>
					<li>2. Download and install updates</li>
				@endif
			</ul>
		</div>

		<div class="signin-form">
			<form method="post" accept-charset="UTF-8" id="update-form_id">
				<div class="signin-text">
					<span>Check for Updates</span>
				</div>

				@if ( $update_found )
					<div class="alert alert-info">There are updates available.</div>
                    <p>During the update process the system will be unavailable. This should only take a little while.</p>
                    <br>
				@endif

				@if ( ! $update_found )
					<div class="alert alert-success">You have the latest version.</div>
				@endif

				@if ( Session::get('error') )
					<div class="alert alert-error">{{{ Session::get('error') }}}</div>
				@endif

				@if ( Session::get('notice') )
					<div class="alert">{{{ Session::get('notice') }}}</div>
				@endif

				<div class="form-actions">

				@if ( $update_found )
					<a href="{{ url('update/now') }}" class="signin-btn bg-primary">Update now</a>
				@endif

				@if ( ! $update_found )
					<input type="button" value="Close this window" onclick="window.close()" class="signin-btn bg-primary">
				@endif					

				</div>
			</form>
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
	});

	window.CmsAdmin.start(init);
</script>

@stop