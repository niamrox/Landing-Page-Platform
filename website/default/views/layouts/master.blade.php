<!DOCTYPE HTML>
<!--
	Spectral by HTML5 UP
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html lang="{{ App::getLocale() }}"> 
	<head>
		<title>{{ $page_title }}</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="description" content="{{ $page_description }}" />
		<!--[if lte IE 8]><script src="{{ url('/website/default/assets/css/ie/html5shiv.js') }}"></script><![endif]-->
		<link rel="stylesheet" href="{{ url('/website/default/assets/css/style.css') }}" />
		<!--[if lte IE 9]><link rel="stylesheet" href="{{ url('/website/default/assets/css/ie/v9.css') }}" /><![endif]-->
		<!--[if lte IE 8]><link rel="stylesheet" href="{{ url('/website/default/assets/css/ie/v8.css') }}" /><![endif]-->
		<link rel="shortcut icon" href="{{ $favicon }}" type="image/x-icon"/>
<style type="text/css">
#banner:after {
	background: {{ $bg_color }};
}
#main > header {
	background-image: -moz-linear-gradient(top, rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("{{ $bg_image }}");
	background-image: -webkit-linear-gradient(top, rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("{{ $bg_image }}");
	background-image: -ms-linear-gradient(top, rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("{{ $bg_image }}");
	background-image: linear-gradient(top, rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("{{ $bg_image }}");
}
body.landing #page-wrapper {
	background-image: -moz-linear-gradient(top, rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("{{ $bg_image }}");
	background-image: -webkit-linear-gradient(top, rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("{{ $bg_image }}");
	background-image: -ms-linear-gradient(top, rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("{{ $bg_image }}");
	background-image: linear-gradient(top, rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("{{ $bg_image }}");
}

body.is-mobile.landing #banner,
body.is-mobile.landing .wrapper.style4 {
	background-image: -moz-linear-gradient(top, rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("{{ $bg_image }}");
	background-image: -webkit-linear-gradient(top, rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("{{ $bg_image }}");
	background-image: -ms-linear-gradient(top, rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("{{ $bg_image }}");
	background-image: linear-gradient(top, rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("{{ $bg_image }}");
}
</style>
	</head>
	<body class="landing">

		<!-- Page Wrapper -->
			<div id="page-wrapper">

@yield('content')

			</div>

		<!-- Scripts -->
			<script src="{{ url('/website/default/assets/js/jquery.min.js') }}"></script>
			<script src="{{ url('/website/default/assets/js/skel.min.js') }}"></script>
			<script src="{{ url('/website/default/assets/js/init.js') }}"></script>

	</body>
</html>