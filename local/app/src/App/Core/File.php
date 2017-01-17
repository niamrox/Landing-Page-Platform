<?php
namespace App\Core;

/**
 * File class
 *
 *
 * @package		Core
 * @category	Base
 * @version		0.01
 * @since		2014-08-06
 * @author		Sem Kokhuis
 */

class File {

    /**
     * Get content from php file to variable,
     * alternative for file_get_contents:
	 * \App\Core\File::get_include_contents($filename)
     */
    public static function get_include_contents($filename) {
		if(is_file($filename))
		{
			ob_start();
			include $filename;
			return ob_get_clean();
		}
		return false;
	}
}