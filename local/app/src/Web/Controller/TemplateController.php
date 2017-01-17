<?php
namespace Web\Controller;

/*
|--------------------------------------------------------------------------
| Template controller
|--------------------------------------------------------------------------
|
| Template related logic
|
*/

class TemplateController extends \BaseController {

    /**
	 * Construct
     */
    public function __construct()
    {
		if(Auth::check())
		{
			$this->parent_user_id = (Auth::user()->parent_id == NULL) ? Auth::user()->id : Auth::user()->parent_id;
		}
		else
		{
			$this->parent_user_id = NULL;
		}
    }

    /**
     * Load all template config
     */
    public static function loadAllTemplateConfig()
    {
		$templates_dir = public_path() . '/templates/';
		$templates = \File::directories($templates_dir);

		$template_config = array();

		foreach($templates as $template)
		{
			$template_config_file = $template . '/config/template.php';
			$template_lang_file = $template . '/lang/' . \App::getLocale() . '/global.php';
            if(! \File::exists($template_lang_file))
            {
			    $template_lang_file = $template . '/lang/en/global.php';
            }

			if(\File::exists($template_config_file) && \File::exists($template_lang_file))
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

    /**
     * Load template config
     */
    public static function loadTemplateConfig($template)
    {
		$template_config_file = public_path() . '/templates/' . $template . '/config/template.php';
		$template_file = public_path() . '/templates/' . $template . '/template.json';
		$template_lang_file = public_path() . '/templates/' . $template . '/lang/' . \App::getLocale() . '/global.php';

        if(! \File::exists($template_lang_file))
        {
            $template_lang_file = public_path() . '/templates/' . $template . '/lang/en/global.php';
        }

		if(\File::exists($template_config_file) && \File::exists($template_lang_file) && \File::exists($template_file))
		{
			$config = \File::getRequire($template_config_file);
			$lang = \File::getRequire($template_lang_file);
			$json = \File::get($template_file);
			$config['dir'] = $template;
			$config['json'] = $json;
			$config['name'] = $lang['name'];
		}

        return $config;
    }
}