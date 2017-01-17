@extends('../app.layouts.frontend')

@section('body_class') page-404  @stop

@section('content')

		<div class="header">
			<a href="{{ url('/') }}" class="logo">
				<strong>{{ Lang::get('global.app_title') }}</strong>
			</a>
		</div>

		<div class="error-code">404</div>

		<div class="error-text">
			<span class="oops">OOPS!</span><br>
			<span class="hr"></span>
			<br>
			SOMETHING WENT WRONG, OR THAT PAGE DOESN'T EXIST... YET
		</div> <!-- / .error-text -->

@stop