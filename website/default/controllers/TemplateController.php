<?php
namespace Website\Controller;

/*
|--------------------------------------------------------------------------
| Template controller
|--------------------------------------------------------------------------
|
| Routes and other template logic
|
*/

class TemplateController extends \BaseController {

    /**
     * Routes
     */
    public function getRoute($url_parts)
    {
		$path = (! isset($url_parts['path']) || $url_parts['path'] == '/') ? '/' : $url_parts['path'];

		$favicon = \App\Core\Settings::get('favicon', '/favicon.ico');
		$page_title = \App\Core\Settings::get('page_title', trans('global.app_title'));
		$page_description = \App\Core\Settings::get('page_description', trans('global.app_title_slogan'));

		$bg_image = \Lang::has('website::global.bg_image') ? trans('website::global.bg_image') : \Config::get('website::default.bg_image');
		$bg_color = \Lang::has('website::global.bg_color') ? trans('website::global.bg_color') : \Config::get('website::default.bg_color');

		switch($path)
		{
			case '/': 
		        return \View::make('home', compact('favicon', 'page_title', 'page_description', 'bg_image', 'bg_color'));
				break;
			default:
				return \Response::view('app.errors.404', [], 404);
		}
	}
}