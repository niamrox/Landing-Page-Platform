<?php
namespace App\Core;

/**
 * Screenshot class
 *
 *
 * @package		Core
 * @category	Base
 * @version		0.01
 * @since		2014-10-12
 * @author		Sem Kokhuis
 */

class Screenshot extends \BaseController {

    /**
     * Responsive screenshot thumb
	 * Thumb width and height are fixed because of positioning
     */
    public static function responsive($url, $thumbnail, $empty_cache = 0, $timeout = 2000)
    {
        if($empty_cache == '0' && \File::exists($thumbnail))
        {
            return false;
        }

		$img = \Image::canvas(548, 274);

		$thumb_laptop_url = file_get_contents(\Config::get('screenshotserver.url') . '/grab?url=' . $url . '&empty_cache=' . $empty_cache . '&browser_width=1280&browser_height=800&thumb_width=371&thumb_height=null&timeout=' . $timeout);
		$thumb_laptop = \Image::make('http:' . $thumb_laptop_url);

		$img->insert(public_path() . '/assets/images/mockups/responsive/laptop.png');
		$img->insert($thumb_laptop, NULL, 120, 25);

		$thumb_phone_url = file_get_contents(\Config::get('screenshotserver.url') . '/grab?url=' . $url . '&empty_cache=' . $empty_cache . '&browser_width=400&browser_height=630&thumb_width=81&thumb_height=null&timeout=' . $timeout);
		$thumb_phone = \Image::make('http:' . $thumb_phone_url);

		$img->insert(public_path() . '/assets/images/mockups/responsive/phone.png');
		$img->insert($thumb_phone, NULL, 431, 128);

		$thumb_tablet_url = file_get_contents(\Config::get('screenshotserver.url') . '/grab?url=' . $url . '&empty_cache=' . $empty_cache . '&browser_width=900&browser_height=1200&thumb_width=150&thumb_height=null&timeout=' . $timeout);
		$thumb_tablet = \Image::make('http:' . $thumb_tablet_url);

		$img->insert(public_path() . '/assets/images/mockups/responsive/tablet.png');
		$img->insert($thumb_tablet, NULL, 38, 57);

		$img->save($thumbnail, 60);

		return $img->response();
    }

    /**
     * Webblock screenshot url
     */
    public static function getBlock($block_dirname, $block_filename)
    {
        $html = \App\Core\File::get_include_contents(public_path() . '/blocks/' . $block_dirname . '/' . $block_filename);
        return \View::make('screenshot.webblock', array(
            'html' => $html
        ));
    }

    /**
     * Webblock screenshot
     */
    public static function webblock($block, $thumbnail, $empty_thumb_cache = 0, $empty_cache = 1, $thumb_width = 250, $thumb_height = 'null', $browser_width = 1280, $browser_height = 'null', $timeout = 2000)
    {
        if($empty_thumb_cache == '0' && \File::exists($thumbnail))
        {
            return false;
        }
        $screenshot_url = \Config::get('screenshotserver.url') . '/grab?url=' . url('/api/v1/screenshot/block/' . $block) . '&empty_cache=' . $empty_cache . '&browser_width=' . $browser_width . '&browser_height=' . $browser_height . '&thumb_width=' . $thumb_width . '&thumb_height=' . $thumb_height . '&timeout=' . $timeout;
		$img_url = file_get_contents($screenshot_url);
        \File::copy('http:' . $img_url, $thumbnail);
    }

    /**
     * Async GET or POST
     */
	public static function curl_request_async($url, $params, $type = 'POST')
	{
		foreach ($params as $key => &$val) {
			if (is_array($val)) $val = implode(',', $val);
			$post_params[] = $key.'='.urlencode($val);
		}
		$post_string = implode('&', $post_params);

		$parts=parse_url($url);

		$fp = fsockopen($parts['host'],
		  isset($parts['port'])?$parts['port']:80,
		  $errno, $errstr, 30);

		// Data goes in the path for a GET request
		if('GET' == $type) $parts['path'] .= '?'.$post_string;

		$out = "$type ".$parts['path']." HTTP/1.1\r\n";
		$out.= "Host: ".$parts['host']."\r\n";
		$out.= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out.= "Content-Length: ".strlen($post_string)."\r\n";
		$out.= "Connection: Close\r\n\r\n";
		// Data goes in the request body for a POST request
		if ('POST' == $type && isset($post_string)) $out.= $post_string;

		fwrite($fp, $out);
		fclose($fp);
	}
}