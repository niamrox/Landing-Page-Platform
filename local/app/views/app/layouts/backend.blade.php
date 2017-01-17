<!DOCTYPE html>
<!--[if IE 8]>         <html class="ie8" lang="{{ App::getLocale() }}" ng-app="cmsApp" dir="{{ trans('i18n.dir') }}"> <![endif]-->
<!--[if IE 9]>         <html class="ie9 gt-ie8" lang="{{ App::getLocale() }}" ng-app="cmsApp" dir="{{ trans('i18n.dir') }}"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="gt-ie8 gt-ie9 not-ie" lang="{{ App::getLocale() }}" ng-app="cmsApp" dir="{{ trans('i18n.dir') }}"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>{{ $cms_title }}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

	<link rel="shortcut icon" type="image/x-icon" href="{{ \App\Core\Settings::get('favicon', url('favicon.ico')) }}" />
	<link rel="stylesheet" href="{{ url('/assets/css/app.css?v=' . Config::get('system.version')) }}" />
	<link rel="stylesheet" href="{{ url('/assets/css/custom/app.general.css?v=' . Config::get('system.version')) }}" />

	<!--[if lt IE 9]>
		<script src="{{ url('/assets/js/ie.min.js') }}"></script>
	<![endif]-->

	<script src="{{ url('/app/javascript?lang=' . \App::getLocale()) }}"></script>
	<script>var init = [];var app_root = '{{ url() }}';</script>

	{{ \App\Controller\HookController::hook('head'); }}

</head>
<body class="theme-default main-menu-animated main-navbar-fixed main-menu-fixed<?php if(\Lang::has('i18n.dir') && trans('i18n.dir') == 'rtl') echo ' right-to-left'; ?> {{ \App\Controller\HookController::hook('body_class'); }}" ng-class="{
	'page-mail': $route.current.active == 'nomargin', 
	'page-profile': $route.current.active == 'profile' || $route.current.active == 'users' || $route.current.active == 'user-edit', 
	'page-profile-user': $route.current.active == 'user-new', 
	'page-edit-site': $route.current.active_sub == 'edit-site', 
	'page-pricing': $route.current.active == 'account', 
	'page-invoice': $route.current.active_sub == 'invoice'
}" ng-controller="MainNavCtrl">
<div class="modal fade" id="ajax-modal" data-backdrop="static" data-keyboard="true" tabindex="-1"></div>
<div class="modal fade" id="ajax-modal2" data-backdrop="static" data-keyboard="true" tabindex="-1"></div>
<div id="main-wrapper">
	<div id="main-navbar" class="navbar navbar-inverse" role="navigation">
		<button type="button" id="main-menu-toggle"><i class="navbar-icon fa fa-bars icon"></i><span class="hide-menu-text">{{ trans('global.hide_menu') }}</span></button>
		
		<div class="navbar-inner">
			<div class="navbar-header">
				<a href="#/" class="navbar-brand" title="{{ $cms_title }}">
					<div style="background-image:url('{{ $cms_logo }}') !important"></div>
					<span>{{ $cms_title }}</span>
				</a>
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-navbar-collapse"><i class="navbar-icon fa fa-bars"></i></button>
			</div>

			<div id="main-navbar-collapse" class="collapse navbar-collapse main-navbar-collapse">
				<div>

					<div class="right clearfix">
						<ul class="nav navbar-nav pull-right right-navbar-nav">
							<li id="msg-saved" class="bg-success">
								<a class="no-link text-danger"><i class="fa fa-circle-o-notch fa-spin"></i>&nbsp; Saved</a>
							</li>

<?php
\App\Controller\HookController::hook('top_nav');

$languages = \App\Controller\AccountController::getLanguages();

if(count($languages) > 1)
{
?>
							<li class="dropdown">
								<a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-flag"></i> {{ trans('i18n.language_title') }} <span class="caret"></span></a>
								<ul class="dropdown-menu">
<?php
foreach($languages as $language)
{
    $active = ($language['active']) ? ' class="active"' : '';
    echo '<li' . $active . '><a onclick="switchLanguage(\'' . $language['code'] . '\');" href="javascript:void(0);">' . $language['title'] . '</a></li>';
}
?>
								</ul>
							</li>
<?php } ?>
							<li class="dropdown">
								<a class="dropdown-toggle user-menu" data-toggle="dropdown">
									<img src="{{ App\Controller\AccountController::getAvatar(32) }}" class="avatar-32">
									<span>{{ $username }}</span>
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li><a href="#/profile"><i class="dropdown-icon fa fa-user"></i> {{ trans('global.my_profile') }}</a></li>
									<li class="divider"></li>
									<li><a href="{{ url('/logout') }}"><i class="dropdown-icon fa fa-power-off"></i> {{ trans('global.logout') }}</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="main-menu" role="navigation">
		<div id="main-menu-inner">
			<div class="menu-content top" id="menu-content-demo">
				<div>
					<div class="text-bg"><span class="text-semibold">{{ $username }}</span></div>
					<a href="#/profile"><img src="{{ App\Controller\AccountController::getAvatar(128) }}" height="54" width="54" class="avatar-32"></a>
					<div class="btn-group">
						<a href="#/profile" class="btn btn-xs btn-primary btn-outline dark" title="{{ trans('global.my_profile') }}"><i class="fa fa-user"></i></a>
						<a href="{{ url('/logout') }}" class="btn btn-xs btn-danger btn-outline dark" title="{{ trans('global.logout') }}"><i class="fa fa-power-off"></i></a>
					</div>
				</div>
			</div>
			<ul class="navigation">
				<li>
					<h4 style="margin-top:25px">{{ trans('global.one_pages') }}</h4>
				</li>

					<li ng-class="{'active': $route.current.active == 'web'}">
						<a href="#/web"><i class="menu-icon fa fa-laptop" title="{{ trans('global.one_pages') }}"></i><span class="mm-text">{{ trans('global.one_pages') }}</span><span class="label label-primary" id="count_sites">{{ $count_sites }}</span></a>
					</li>
<?php
if(\Config::get('piwik.url', '') != '')
{
?>
					<li ng-class="{'active': $route.current.active == 'web-analytics'}">
						<a href="#/web/analytics"><i class="menu-icon fa fa-line-chart"></i><span class="mm-text">{{ trans('global.analytics') }}</span></a>
					</li>
<?php
}
?>

					<li ng-class="{'active': $route.current.active == 'leads'}">
						<a href="#/leads"><i class="menu-icon fa fa-pencil-square-o" title="{{ trans('global.form_entries') }}"></i><span class="mm-text">{{ trans('global.form_entries') }}</span></a>
					</li>
	

				<li>
					<h4>{{ trans('global.general') }}</h4>
				</li>

                <li ng-class="{'active': $route.current.active == 'media'}">
                    <a href="#/media"><i class="menu-icon fa fa-cloud"></i><span class="mm-text">{{ trans('global.media') }}</span></a>
                </li>

				<li class="mm-dropdown" ng-class="{'open': $route.current.active == 'oauth' || $route.current.active == 'profile' || $route.current.active == 'campaigns' || $route.current.active == 'account' || $route.current.active == 'subscription' || $route.current.active == 'log' || $route.current.active == 'users' || $route.current.active == 'user-new' || $route.current.active == 'user-edit'}">
					<a href="javascript:void(0);"><i class="menu-icon fa fa-sliders"></i><span class="mm-text">{{ trans('global.settings') }}</span></a>
					<ul>

						<li ng-class="{'active': $route.current.active == 'profile'}">
							<a href="#/profile"><i class="menu-icon fa fa-user"></i><span class="mm-text">{{ trans('global.profile') }}</span></a>
						</li>
<?php
if(\Auth::user()->can('user_management'))
{

?>
						<li ng-class="{'active': $route.current.active == 'users' || $route.current.active == 'user-new' || $route.current.active == 'user-edit'}">
							<a href="#/users"><i class="menu-icon fa fa-users"></i><span class="mm-text">{{ trans('global.team') }}</span></a>
						</li>
<?php

	if(\Auth::user()->parent_id == NULL)
{
?>
						<li ng-class="{'active': $route.current.active == 'oauth'}">
							<a href="#/oauth"><i class="menu-icon fa fa-plug"></i><span class="mm-text">{{ trans('global.apps') }}</span></a>
						</li>
						<li ng-class="{'active': $route.current.active == 'subscription' || $route.current.active == 'account'}">
							<a href="#/account"><i class="menu-icon fa fa-credit-card"></i><span class="mm-text">{{ trans('global.account') }}</span></a>
						</li>
<?php
	}
}
?>
<?php
if(\Auth::user()->getRoleId() != 4)
{
?>
						<li ng-class="{'active': $route.current.active == 'campaigns'}">
							<a href="#/campaigns"><i class="menu-icon fa fa-share-alt"></i><span class="mm-text">{{ trans('global.campaigns') }}</span></a>
						</li>
<?php
}
?>
					</ul>
				</li>
<?php
if(\Auth::user()->can('system_management'))
{
?>
                <li class="mm-dropdown" ng-class="{'open': $route.current.active == 'admin-users' || $route.current.active == 'admin-plans' || $route.current.active == 'admin-purchases' || $route.current.active == 'admin-website' || $route.current.active == 'admin-cms'}">
                    <a href="javascript:void(0);"><i class="menu-icon fa fa-wrench"></i><span class="mm-text">{{ trans('admin.system_administration') }}</span></a>
                    <ul>
                        <li ng-class="{'active': $route.current.active == 'admin-users'}">
                            <a href="#/admin/users"><i class="menu-icon fa fa-database"></i><span class="mm-text">{{ trans('admin.user_administration') }}</span></a>
                        </li>
                        <li ng-class="{'active': $route.current.active == 'admin-purchases'}">
                            <a href="#/admin/purchases"><i class="menu-icon fa fa-money"></i><span class="mm-text">{{ trans('admin.purchases') }}</span></a>
                        </li>
                        <li ng-class="{'active': $route.current.active == 'admin-plans'}">
                            <a href="#/admin/plans"><i class="menu-icon fa fa-trophy"></i><span class="mm-text">{{ trans('admin.user_plans') }}</span></a>
                        </li>
                        <li ng-class="{'active': $route.current.active == 'admin-website'}">
                            <a href="#/admin/website"><i class="menu-icon fa fa-globe"></i><span class="mm-text">{{ trans('admin.website') }}</span></a>
                        </li>
                        <li ng-class="{'active': $route.current.active == 'admin-cms'}">
                            <a href="#/admin/cms"><i class="menu-icon fa fa-dashboard"></i><span class="mm-text">{{ trans('admin.cms') }}</span></a>
                        </li>
                    </ul>
                </li>
<?php
}
?>
			</ul>

		</div>
	</div>

	<div id="content-wrapper" ng-view>
		@yield('content')
	</div>
	<div id="main-menu-bg"></div>
</div>

<script src="{{ url('/assets/js/app.js?v=' . Config::get('system.version')) }}"></script>
<script src="{{ url('/assets/js/custom/app.angular.js?v=' . Config::get('system.version')) }}"></script>
<script src="{{ url('/assets/js/custom/app.general.js?v=' . Config::get('system.version')) }}"></script>

<script type="text/javascript">
window.CmsAdmin.start(init);
</script>

@yield('page_bottom')
</body>
</html>