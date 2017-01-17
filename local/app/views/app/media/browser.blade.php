@extends('../app.layouts.partial')

@section('content')

    <link rel="stylesheet" type="text/css" href="<?= asset('assets/js/vendor/jqueryui/jquery-ui.structure.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset('assets/js/vendor/jqueryui/jquery-ui.theme.min.css') ?>">

    <link rel="stylesheet" type="text/css" href="<?= asset($dir.'/css/elfinder.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset($dir.'/css/theme.css') ?>">

	<link rel="stylesheet" href="{{ url('/assets/css/custom/elfinder.css') }}" />

	<ul class="breadcrumb breadcrumb-page">
		<div class="breadcrumb-label text-light-gray">{{ trans('global.you_are_here') }} </div>
		<li><a href="{{ trans('global.home_crumb_url') }}">{{ trans('global.home_crumb_text') }}</a></li>
		<li class="active">{{ trans('global.media_browser') }}</li>
	</ul>

	<div class="page-header">
		<h1 style="height:32px"><i class="fa fa-cloud page-header-icon"></i> {{ trans('global.media_browser') }}</h1>
	</div>

	<div id="media-browser"></div>

	<script src="<?= asset($dir.'/js/elfinder.min.js') ?>"></script>
<?php if($locale){ ?>
	<script src="<?= asset($dir."/js/i18n/elfinder.$locale.js") ?>"></script>
<?php } ?>
<script type="text/javascript" charset="utf-8">
$(function() {
    var $elf;

	// Documentation for client options:
	// https://github.com/Studio-42/elFinder/wiki/Client-configuration-options

	// Keep bootstrap from messing up our buttons.
	if($.fn.button.noConflict) {
		$.fn.btn = $.fn.button.noConflict();
	}

    setTimeout(function() {

        $elf = $('#media-browser').elfinder({
            // set your elFinder options here
            <?php if($locale){ ?>
                lang: '<?= $locale ?>',
            <?php } ?>
            url : "{{ url('/elfinder/connector') }}",
            resizable: false,
            useBrowserHistory: false,
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
                help : { view : ['shortcuts', 'help', 'about'] }
            }
        });

        var $window = $(window);
        $window.resize(function(){
            var win_height = parseInt($window.height()) - 189;
            if( $elf.height() != win_height ){
                $elf.height(win_height).resize();
            }
        });

    }, 200);
});
</script>
@stop