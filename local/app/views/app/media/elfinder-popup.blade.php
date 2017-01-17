<!DOCTYPE html>
<!--[if IE 8]>         <html class="ie8" lang="{{ App::getLocale() }}"> <![endif]-->
<!--[if IE 9]>         <html class="ie9 gt-ie8" lang="{{ App::getLocale() }}"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="gt-ie8 gt-ie9 not-ie" lang="{{ App::getLocale() }}"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>{{ trans('global.app_title') }}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

	<!--[if lt IE 9]>
		<script src="{{ url('/assets/js/ie.min.js') }}"></script>
	<![endif]-->

    <script src="{{ url('/assets/js/elfinder.js?v=' . Config::get('system.version')) }}"></script>
	<script src="{{ url('/app/javascript?lang=' . \App::getLocale()) }}"></script>

    <link rel="stylesheet" type="text/css" href="<?= asset('assets/js/vendor/jqueryui/jquery-ui.structure.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('assets/js/vendor/jqueryui/jquery-ui.theme.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset($dir.'/css/elfinder.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset($dir.'/css/theme.css') ?>">
	<link rel="stylesheet" href="{{ url('/assets/css/custom/elfinder.css') }}" />

    <style type="text/css">
    body, html {
        margin:0;
        padding:0;
    }
    </style>

    <script type="text/javascript">
        var $elf;
        $().ready(function () {
            $elf = $('#media-browser').elfinder({
                <?php if($locale){ echo "lang: '$locale',\n"; } ?>
                url: '<?= URL::action('Barryvdh\Elfinder\ElfinderController@showConnector') ?>',
                dialog: {width: 900, modal: true, title: 'Select a file'},
                resizable: false,
                commandsOptions: {
                    getfile: {
                        oncomplete: 'destroy'
                    }
                },
                getFileCallback: function (file) {
                    window.parent.<?php echo $callback ?>(file.url, '<?= $input_id ?>');
					if (typeof parent.$jq !== 'undefined')
					{
	                    parent.$jq.colorbox.close();
					}
					else
					{
	                    parent.$.colorbox.close();
					}
                },
<?php
if (\Config::get('s3.active', false))
{
?>
            commands : [
                /*'open', */'reload', 'home', 'up', 'back', 'forward', 
                'download', 'rm', 'rename', 'upload', 'copy', 
                'paste'/*, 'edit'*/, 'search', 'info', 'view',
                'resize', 'sort'
            ],
<?php } else { ?>
            commands : [
                /*'open', */'reload', 'home', 'up', 'back', 'forward', 
                'download', 'rm', 'rename', 'mkdir', 'upload', 'copy', 
                'paste'/*, 'edit'*/, 'search', 'info', 'view',
                'resize', 'sort'
            ],
<?php } ?>
				commandsOptions : {
					help : { view : ['shortcuts', null, null] }
				}
            }).elfinder('instance');
        });
    </script>

</head>
<body>

<div id="media-browser"></div>

<script src="<?= asset($dir.'/js/elfinder.min.js') ?>"></script>

<?php if($locale){ ?>
<script src="<?= asset($dir."/js/i18n/elfinder.$locale.js") ?>"></script>
<?php } ?>

<script type="text/javascript" charset="utf-8">
$(function() {

	mediaBrowserResize();

	$(window).resize($.debounce(100, mediaBrowserResize));

	function mediaBrowserResize()
	{
        $elf.resize('auto', (parseInt($(window).outerHeight())) - 2 + 'px');
        //$('#media-browser').css({ 'height' : (parseInt($(window).outerHeight())) - 2 + 'px'});
	}

});
</script>

</body>
</html>