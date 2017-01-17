<?php
namespace App\Controller;

/*
|--------------------------------------------------------------------------
| Reseller controller
|--------------------------------------------------------------------------
|
| Reseller related logic
|
*/

class ResellerController extends \BaseController {

    /**
     * Get reseller
     */
    public static function get()
    {
		$url_parts = parse_url(\URL::current());
		$domain = str_replace('www.', '', $url_parts['host']);

		$reseller = \App\Model\Reseller::where(function ($query) use($domain) {
			$query->where('domain', $domain)
				  ->orWhere('domain', 'www.' . $domain)
				  ->orWhere('domain', '');
			})->where(function ($query) use($domain) {
				$query->where('active', 1);
			})->first();

		return (count($reseller) == 0) ? false : $reseller;
    }
}