<?php
namespace App\Controller;

/*
|--------------------------------------------------------------------------
| Hook controller
|--------------------------------------------------------------------------
|
| Hook related logic to plugin extra functionality throughout the system
|
| \App\Controller\HookController::hook('dir_name');
*/

class HookController extends \BaseController {

    /**
     * Include Hook
     */
    public static function hook($location, $var = NULL)
    {
		// First, load config
		$hooks_config_dir = public_path() . '/hooks/config';

		if (\File::isDirectory($hooks_config_dir))
		{
			foreach (glob($hooks_config_dir . "/*.php") as $filename)
			{
				$key = str_replace('.php', '', basename($filename));
				$hook_config[$key] = include $filename;
			}
		}

		// Then, load hook file(s)
		$hooks_dir = public_path() . '/hooks/' . $location;

		if (\File::isDirectory($hooks_dir))
		{
			\View::addNamespace('hooks', $hooks_dir);

			foreach (glob($hooks_dir . "/*.php") as $filename)
			{
				if (strpos($filename, '.blade.php') !== false)
				{
					$view = str_replace('.blade.php', '', basename($filename));
					return \View::make('hooks::' . $view, compact('hook_config'));
				}
				else
				{
					$hook_return = include $filename;
				}
			}
		}
    }

    /**
     * Post
     */
    public static function postAjax($location)
    {
		// Then, load hook file(s)
		$hooks_file = public_path() . '/hooks/ajax/' . $location . '.php';
		$hooks_file_blade = public_path() . '/hooks/ajax/' . $location . '.blade.php';

		if (\File::isFile($hooks_file))
		{
			include $hooks_file;
		}
		elseif (\File::isFile($hooks_file_blade))
		{
			\View::addNamespace('hooks', public_path() . '/hooks/ajax/');
			return \View::make('hooks::' . $location);
		}
    }
}