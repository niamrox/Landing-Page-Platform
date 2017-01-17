@extends('../app.layouts.frontend')

@section('body_class') page-404  @stop

@section('content')

		<div class="header">
			<a href="{{ url('/') }}" class="logo">
				<strong>{{ Lang::get('global.app_title') }}</strong>
			</a>
		</div>

		<div class="error-code">{{ $title }}</div>

		<div class="error-text">
			<span class="oops">{{ $subtitle }}</span><br>
			<span class="hr"></span>
			<br>
			{{ $msg }}
<?php
if(isset($error))
{
?>
			<br>
			<br>
			<div class="alert alert-danger">{{ $error }}</div>
<?php
}
?>
		</div> <!-- / .error-text -->

@stop