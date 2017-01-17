<?php
namespace App\Core;

/**
 * Slug class
 *
 *
 * @package		Core
 * @category	Base
 * @version		0.01
 * @since		2014-11-29
 * @author		Sem Kokhuis
 */

class Slug {

    /**
     * Create app page slug and check if it doesn't exist yet
     */
    public static function app($text, $app_id, $page_id = NULL)
	{
		$punycode = new \Punycode();
		$slugify = new \Slugify();

		$punycode_slug = $punycode->encode($text);
		$slug = $slugify->slugify(urlencode($punycode_slug));

		$slug = \App\Core\Slug::check_app($slug, $app_id, $page_id);

		return $slug;
	}

    /**
     * Check if slug exists, if so increment number
     */
    public static function check_app($slug, $app_id, $page_id, $i = 1)
	{
		if($i == 1)
		{
			if(is_numeric($page_id))
			{
				$exists = \Mobile\Model\AppPage::where('app_id', '=', $app_id)
					->where('slug', '=', $slug)
					->where('id', '<>', $page_id)
					->first();
			}
			else
			{
				$exists = \Mobile\Model\AppPage::where('app_id', '=', $app_id)
					->where('slug', '=', $slug)
					->first();
			}
		}
		else
		{
			if(is_numeric($page_id))
			{
				$exists = \Mobile\Model\AppPage::where('app_id', '=', $app_id)
					->where('slug', '=', $slug . '-' . $i)
					->where('id', '<>', $page_id)
					->first();
			}
			else
			{
				$exists = \Mobile\Model\AppPage::where('app_id', '=', $app_id)
					->where('slug', '=', $slug . '-' . $i)
					->first();
			}
		}

		if(count($exists) > 0)
		{
			$i++;
			return \App\Core\Slug::check_app($slug, $app_id, $page_id, $i);
		}
		else
		{
			return ($i > 1) ? $slug . '-' . $i : $slug;
		}
	}
}