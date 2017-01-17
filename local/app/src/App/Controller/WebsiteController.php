<?php
namespace App\Controller;

/*
|--------------------------------------------------------------------------
| Website controller
|--------------------------------------------------------------------------
|
| Public facing website logic
|
*/

class WebsiteController extends \BaseController {

    /**
     * Route widget admin post
     */
    public function showWebsite($url_parts)
    {
		$reseller = \App\Controller\ResellerController::get();

		// Rewrite path if installed in subdir
		$path = (isset($url_parts['path'])) ? $url_parts['path'] : '/';
		if (ends_with(url('/'), $path)) $url_parts['path'] = '/';

		// Get current template
		$template = \App\Core\Settings::get('website_template', 'default');

        $template_dir = public_path() . '/website/' . $template;

		if (! \File::isDirectory($template_dir))
		{
			$template = 'default';
        	$template_dir = public_path() . '/website/' . $template;
		}

		$language_dir = storage_path() . '/userdata/templates/' . $template . '/' . $reseller->id . '/lang';

		if (\File::isFile($language_dir . '/en/global.php'))
		{
        	\Lang::addNamespace('website', $language_dir);
		}
		else
		{
        	\Lang::addNamespace('website', $template_dir . '/lang');
		}

        \View::addLocation($template_dir . '/views');
        \View::addNamespace('website', $template_dir . '/views');
        \Config::addNamespace('website', $template_dir . '/config');

        require $template_dir . '/controllers/TemplateController.php';

        return \App::make('\Website\Controller\TemplateController')->callAction('getRoute', compact('url_parts'));
    }

    /**
     * Route website post
     */
    public static function postPost($template, $function)
    {
		$reseller = \App\Controller\ResellerController::get();
        $template_dir = public_path() . '/website/' . $template;

		if (! \File::isDirectory($template_dir))
		{
			$template = 'default';
        	$template_dir = public_path() . '/website/' . $template;
		}

		$language_dir = storage_path() . '/userdata/templates/' . $template . '/' . $reseller->id . '/lang';

		if (\File::isFile($language_dir . '/en/global.php'))
		{
        	\Lang::addNamespace('website', $language_dir);
		}
		else
		{
        	\Lang::addNamespace('website', $template_dir . '/lang');
		}

        \View::addLocation($template_dir . '/views');
        \View::addNamespace('website', $template_dir . '/views');
        \Config::addNamespace('website', $template_dir . '/config');

        require public_path() . '/website/' . $template . '/controllers/TemplateController.php';

        return \App::make('\Website\Controller\TemplateController')->callAction($function, []);
    }

    /**
     * Route website get
     */
    public static function getGet($template, $function)
    {
		$reseller = \App\Controller\ResellerController::get();
        $template_dir = public_path() . '/website/' . $template;

		if (! \File::isDirectory($template_dir))
		{
			$template = 'default';
        	$template_dir = public_path() . '/website/' . $template;
		}

		$language_dir = storage_path() . '/userdata/templates/' . $template . '/' . $reseller->id . '/lang';

		if (\File::isFile($language_dir . '/en/global.php'))
		{
        	\Lang::addNamespace('website', $language_dir);
		}
		else
		{
        	\Lang::addNamespace('website', $template_dir . '/lang');
		}

        \View::addLocation($template_dir . '/views');
        \View::addNamespace('website', $template_dir . '/views');
        \Config::addNamespace('website', $template_dir . '/config');

        require public_path() . '/website/' . $template . '/controllers/TemplateController.php';

        return \App::make('\Website\Controller\TemplateController')->callAction($function, []);
    }

    /**
     * Load template config
     */
    public static function loadTemplateConfig($template)
    {
		$reseller = \App\Controller\ResellerController::get();
		$templates_dir = public_path() . '/website/';
		$template_config = array();

		$template_config_file = $templates_dir . $template . '/config/template.php';
		$template_lang_file = $templates_dir . $template . '/lang/en/global.php';

		if(\File::exists($template_config_file))
		{
			$config = \File::getRequire($template_config_file);

			if($config['active'])
			{
				$language_dir = storage_path() . '/userdata/templates/' . $template . '/' . $reseller->id . '/lang';

				$lang_original = \File::getRequire($template_lang_file);

				if (\File::isFile($language_dir . '/en/global.php'))
				{
					$lang = \File::getRequire($language_dir . '/en/global.php');
				}
				else
				{
					$lang = $lang_original;\Lang::addNamespace('website', $templates_dir . $template . '/lang');
				}

				$config['dir'] = basename($template);
				$config['lang_original'] = $lang_original;
				$config['lang'] = $lang;
				$template_config[$lang['name']] = $config;
			}
		}

        return $template_config;
    }

    /**
     * Get all templates
     */
    public static function loadAllTemplateConfig()
    {
		$templates_dir = public_path() . '/website/';
		$templates = \File::directories($templates_dir);

		$template_config = array();

		foreach($templates as $template)
		{
			$template_config_file = $template . '/config/template.php';
			$template_lang_file = $template . '/lang/en/global.php';
			if(\File::exists($template_config_file))
			{
				$config = \File::getRequire($template_config_file);

				if($config['active'])
				{
					$lang = \File::getRequire($template_lang_file);
					$config['dir'] = basename($template);
					$template_config[$lang['name']] = $config;
				}
			}
		}

		ksort($template_config);

        return $template_config;
    }
}