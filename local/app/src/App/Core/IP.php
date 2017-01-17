<?php
namespace App\Core;

/**
 * IP class
 *
 *
 * @package		Core
 * @category	Base
 * @version		0.01
 * @since		2015-04-27
 * @author		Sem Kokhuis
 */

class IP {

    /**
     * Get real IP address
     * \App\Core\IP::address()
     */
    public static function address() {
		$headers = array ('HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_VIA', 'HTTP_X_COMING_FROM', 'HTTP_COMING_FROM', 'HTTP_CLIENT_IP' );
 
		foreach ( $headers as $header ) {
			if (isset ( $_SERVER [$header]  )) {
			
				if (($pos = strpos ( $_SERVER [$header], ',' )) != false) {
					$ip = substr ( $_SERVER [$header], 0, $pos );
				} else {
					$ip = $_SERVER [$header];
				}
				$ipnum = ip2long ( $ip );
				if ($ipnum !== - 1 && $ipnum !== false && (long2ip ( $ipnum ) === $ip)) {
					if (($ipnum - 184549375) && // Not in 10.0.0.0/8
					($ipnum  - 1407188993) && // Not in 172.16.0.0/12
					($ipnum  - 1062666241)) // Not in 192.168.0.0/16
					if (($pos = strpos ( $_SERVER [$header], ',' )) != false) {
						$ip = substr ( $_SERVER [$header], 0, $pos );
					} else {
						$ip = $_SERVER [$header];
					}
					return $ip;
				}
			}
			
		}
		return $_SERVER ['REMOTE_ADDR'];
	}
}