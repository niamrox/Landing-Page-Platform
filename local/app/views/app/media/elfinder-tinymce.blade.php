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
	<script src="{{ url('/assets/js/lang/' . App::getLocale() . '.js') }}"></script>

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
  
        var FileBrowserDialogue = {
            init: function() {
                /* Here goes your code for setting your custom things onLoad.*/
            },
            mySubmit: function (URL) {
				console.log(parent.tinyMCE.activeEditor.windowManager.getParams());
                parent.tinyMCE.activeEditor.windowManager.getParams().setUrl(URL);

                parent.tinyMCE.activeEditor.windowManager.close();
            }
        }

        $().ready(function() {
            $elf = $('#media-browser').elfinder({
                 resizable: false,
                <?php if($locale){ ?>
                    lang: '<?= $locale ?>',
                <?php } ?>
                <?php if(isset($csrf)){ ?>
                customData: { _token:  '<?php echo csrf_token(); ?>' },
                <?php } ?>
                url: '<?= URL::action('Barryvdh\Elfinder\ElfinderController@showConnector') ?>',
                getFileCallback: function(file) {
                    FileBrowserDialogue.mySubmit(file.url);
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
	}

});
</script>

</body>
</html>
