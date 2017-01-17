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
                <li>1. Updates found</li>
                <li><strong>2. Download and install updates</strong></li>
			</ul>
		</div>

		<div class="signin-form">
			<form method="post" accept-charset="UTF-8" id="update-form_id">
				<div class="signin-text">
					<span>System Updated</span>
				</div>

					<div class="alert alert-success">The system has succesfully been updated.</div>

				<div class="form-actions">

					<input type="button" value="Close this window" onclick="window.close()" class="signin-btn bg-primary">			

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