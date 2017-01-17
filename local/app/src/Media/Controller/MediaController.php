<?php
namespace Media\Controller;

/*
|--------------------------------------------------------------------------
| Media controller
|--------------------------------------------------------------------------
|
| Media files related logic
|
*/

class MediaController extends \BaseController {

    /**
	 * Construct
     */
    public function __construct()
    {
		if(\Auth::check())
		{
			$this->parent_user_id = (\Auth::user()->parent_id == NULL) ? \Auth::user()->id : \Auth::user()->parent_id;
		}
		else
		{
			$this->parent_user_id = NULL;
		}
    }

    /**
     * Show media browser
     */
    public function getBrowser()
    {
        $dir = 'packages/elfinder';
        $locale = \Config::get('app.locale');
		$locale = str_replace('pt', 'pt_BR', $locale);

        if (!file_exists(public_path() . "/$dir/js/i18n/elfinder.$locale.js"))
        {
            $locale = false;
        }

        return \View::make('app.media.browser', compact('dir','locale'));
    }

    /**
     * Load elFinder
     */
    public function elFinder()
    {
		// Set Root dir
		if(\Auth::user()->parent_id == NULL)
		{
			$root_dir = \App\Core\Secure::staticHash(\Auth::user()->id);
		}
		else
		{
			$Punycode = new \Punycode();
			$user_dir = $Punycode->encode(\Auth::user()->username);
			$root_dir = \App\Core\Secure::staticHash(\Auth::user()->parent_id) . '/' . $user_dir;
		}

		$root_dir_full = public_path() . '/uploads/user/' . $root_dir;

		if(! \File::isDirectory($root_dir_full))
		{
			\File::makeDirectory($root_dir_full, 0775, true);
		}

		\Config::set('laravel-elfinder::dir', 'uploads/user/' . $root_dir);

        $dir = 'packages/elfinder';
        $locale = \Config::get('app.locale');
		$locale = str_replace('pt', 'pt_BR', $locale);

        if (!file_exists(public_path() . "/$dir/js/i18n/elfinder.$locale.js"))
        {
            $locale = false;
        }

        return \View::make('app.media.elfinder', compact('dir', 'locale'));
    }

    /**
     * Load elFinder CKEditor
     */
    public function ckEditor()
    {
		// Set Root dir
		if(\Auth::user()->parent_id == NULL)
		{
			$root_dir = \App\Core\Secure::staticHash(\Auth::user()->id);
		}
		else
		{
			$Punycode = new \Punycode();
			$user_dir = $Punycode->encode(\Auth::user()->username);
			$root_dir = \App\Core\Secure::staticHash(\Auth::user()->parent_id) . '/' . $user_dir;
		}

		$root_dir_full = public_path() . '/uploads/user/' . $root_dir;

		if(! \File::isDirectory($root_dir_full))
		{
			\File::makeDirectory($root_dir_full, 0775, true);
		}

		\Config::set('laravel-elfinder::dir', 'uploads/user/' . $root_dir);

        $dir = 'packages/elfinder';
        $locale = \Config::get('app.locale');
		$locale = str_replace('pt', 'pt_BR', $locale);

        if (!file_exists(public_path() . "/$dir/js/i18n/elfinder.$locale.js"))
        {
            $locale = false;
        }

        return \View::make('app.media.elfinder-ckeditor4', compact('dir', 'locale'));
    }

    /**
     * Load elFinder TinyMCE
     */
    public function showTinyMCE()
    {
        // Set Root dir
        if(\Auth::user()->parent_id == NULL)
        {
            $root_dir = \App\Core\Secure::staticHash(\Auth::user()->id);
        }
        else
        {
            $Punycode = new \Punycode();
            $user_dir = $Punycode->encode(\Auth::user()->username);
            $root_dir = \App\Core\Secure::staticHash(\Auth::user()->parent_id) . '/' . $user_dir;
        }

        $root_dir_full = public_path() . '/uploads/user/' . $root_dir;

        if(! \File::isDirectory($root_dir_full))
        {
            \File::makeDirectory($root_dir_full, 0775, true);
        }

        \Config::set('laravel-elfinder::dir', 'uploads/user/' . $root_dir);

        $dir = 'packages/elfinder';
        $locale = \Config::get('app.locale');

		if($locale == 'zh_cn') $locale = 'zh_CN';
		if($locale == 'cn') $locale = 'zh_TW';
		if($locale == 'kr') $locale = 'ko';

        if (!file_exists(public_path() . "/$dir/js/i18n/elfinder.$locale.js"))
        {
            $locale = false;
        }

        return \View::make('app.media.elfinder-tinymce', compact('dir', 'locale'));
    }

    /**
     * Load elFinder popup
     */
    public function popUp($input_id, $callback = 'processSelectedFile')
    {
		// Set Root dir
		if(\Auth::user()->parent_id == NULL)
		{
			$root_dir = \App\Core\Secure::staticHash(\Auth::user()->id);
		}
		else
		{
			$Punycode = new \Punycode();
			$user_dir = $Punycode->encode(\Auth::user()->username);
			$root_dir = \App\Core\Secure::staticHash(\Auth::user()->parent_id) . '/' . $user_dir;
		}

		$root_dir_full = public_path() . '/uploads/user/' . $root_dir;

		if(! \File::isDirectory($root_dir_full))
		{
			\File::makeDirectory($root_dir_full, 0775, true);
		}

		\Config::set('laravel-elfinder::dir', 'uploads/user/' . $root_dir);

        $dir = 'packages/elfinder';
        $locale = \Config::get('app.locale');
		$locale = str_replace('pt', 'pt_BR', $locale);

        if (!file_exists(public_path() . "/$dir/js/i18n/elfinder.$locale.js"))
        {
            $locale = false;
        }

        return \View::make('app.media.elfinder-popup', compact('dir', 'locale', 'input_id', 'callback'));
    }
}