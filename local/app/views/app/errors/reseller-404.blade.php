@extends('../app.layouts.frontend')

@section('body_class') page-404  @stop

@section('content')

		<div class="header">
			<a href="{{ url('/') }}" class="logo">
				<strong>Reseller not found</strong>
			</a>
		</div>

		<div class="error-code">404</div>

		<div class="error-text">
			<span class="oops">OOPS!</span><br>
			<span class="hr"></span>
			<br>
			This reseller domain is not active
		</div>

@stop