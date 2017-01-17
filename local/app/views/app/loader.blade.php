@extends('../app.layouts.partial')
@section('content')

    <div class="small-throbber main-throbber" id="throbber" style="display:none"> </div>

<script>
if(window.jQuery)
{
	$('#throbber').css('margin', (parseInt($(window).outerHeight()) / 2) - 64 + 'px auto 0 auto');
	$('#throbber').fadeIn();
}
else
{
	var w = window,
		d = document,
		e = d.documentElement,
		g = d.getElementsByTagName('body')[0],
		x = w.innerWidth || e.clientWidth || g.clientWidth,
		y = w.innerHeight|| e.clientHeight|| g.clientHeight;

	document.getElementById('throbber').style.margin = ((parseInt(y) / 2) - 64) + 'px auto 0 auto';
	document.getElementById('throbber').style.display = 'block';
}
</script>
@stop