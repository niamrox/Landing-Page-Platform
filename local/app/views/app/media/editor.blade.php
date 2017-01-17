<!DOCTYPE html>
<!--[if IE 8]>         <html class="ie8" lang="{{ App::getLocale() }}"> <![endif]-->
<!--[if IE 9]>         <html class="ie9 gt-ie8" lang="{{ App::getLocale() }}"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="gt-ie8 gt-ie9 not-ie" lang="{{ App::getLocale() }}"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>{{ trans('global.app_title') }}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

	<script src="{{ url('/assets/js/vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>

<script type="text/javascript">
var editor = tinymce.init({
	selector: "#content",
	forced_root_block : "",
    plugins: [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime nonbreaking save table contextmenu",
        "template paste textcolor colorpicker textpattern imagetools"
    ],
/*    plugins: [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table contextmenu directionality",
        "emoticons template paste textcolor colorpicker textpattern imagetools"
    ],*/
    toolbar1: "save insertfile undo redo | styleselect | forecolor backcolor | bold italic",
    toolbar2: "alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
    image_advtab: true,
	templates: "{{ url('/app/editor/templates') }}",/*
    templates: [
        {title: 'Test template 1', content: 'Test 1'},
        {title: 'Test template 2', content: 'Test 2'}
    ],*/
/*	content_css: "{{ url('/assets/css/mobile.css') }}",*/
	content_css: "../assets/css/tinymce-ionic.css",
	resize: false,
    relative_urls : false,
    convert_urls : false,
    paste_use_dialog : true,
	save_enablewhendirty: true,
    save_onsavecallback: function() {
		var ed = tinyMCE.get('content');
		var content = ed.getContent();

		parent.saveTemplateEditor({{ \Request::get('i', '') }}, content);
	},
	setup: function(ed) {
        ed.on('init', function(e) {
			var w = window,
				d = document,
				e = d.documentElement,
				g = d.getElementsByTagName('body')[0],
				x = w.innerWidth || e.clientWidth || g.clientWidth,
				y = w.innerHeight|| e.clientHeight|| g.clientHeight;

			tinyMCE.DOM.setStyle(tinyMCE.DOM.get("content" + '_ifr'), 'height', parseInt(y) - 143 + 'px');
			tinyMCE.DOM.setStyle(tinyMCE.DOM.get("content" + '_ifr'), 'width', 360 + 'px');

			/* Scrollbar */
			document.getElementById('content_ifr').contentWindow.document.getElementById('tinymce').style.height = parseInt(y) - 143 + 'px';

			/* Set content */
			var content = parent.getTemplateContent({{ \Request::get('i', '') }});
			tinymce.activeEditor.selection.setContent(content);
        });
    }
});
</script>

<style type="text/css">
html, body {
	margin:0;
	padding:0;
}

.mce-edit-area {
	background-color: #ccc !important;
}
</style>

</head>
<body>

    <textarea id="content" style="width:100%"></textarea>

</body>
</html>