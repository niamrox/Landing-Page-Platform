<?php
namespace App\Core;

use Crypt;

/**
 * Secure class
 *
 *
 * @package		Core
 * @category	Base
 * @version		0.01
 * @since		2014-09-09
 * @author		Sem Kokhuis
 */

class Secure {

    /**
     * Array to encrypted string - $sl = \App\Core\Secure::array2string(array('id' => 1))
     */

    public static function array2string($array)
    {
        $string = http_build_query($array);
        $string =  Crypt::encrypt($string);
		return rawurlencode($string);
    }

    /**
     * Encrypted string to array - $sl = \App\Core\Secure::string2array($sl)
     */

    public static function string2array($string)
    {
        try {
            $string = rawurldecode($string);
			$string = Crypt::decrypt($string);
		}
		catch(\Illuminate\Encryption\DecryptException  $e)
		{
			echo 'Decrypt Error';
			die();
		}

        parse_str($string, $array);
		return $array;
    }

    /**
     * Short hash ONLY for numbers, for example user_id to create upload directory. $hash = \App\Core\Secure::staticHash(1)
     */

    public static function staticHash($number)
    {
		$hashids = new \Hashids\Hashids(\Config::get('app.key'));
		$string = $hashids->encode($number);
		return $string;
    }

    /**
     * Decode hash. $number = \App\Core\Secure::staticHashDecode($hash)
     */

    public static function staticHashDecode($hash)
    {
		$hashids = new \Hashids\Hashids(\Config::get('app.key'));
		$number = $hashids->decode($hash);
		return $number;
    }
}