<?php
namespace App\Core;

/**
 * Piwik class
 *
 * Depends on Guzzle (guzzlephp.org)
 *
 * @package		Core
 * @category	Base
 * @version		0.01
 * @since		2014-08-07
 * @author		Sem Kokhuis
 */

class Piwik {

    protected $ttl = 60;

    /**
	 * Construct
     */
    public function __construct()
    {
    }

    /**
     * Device detector 
     */
	public static function getDevice($userAgent = NULL)
	{
		// Parse useragent and cache results
		if($userAgent == NULL) $userAgent = $_SERVER['HTTP_USER_AGENT'];

		$ua = \Cache::rememberForever('ua-' . md5($userAgent) , function() use($userAgent)
		{
			// OPTIONAL: Set version truncation to none, so full versions will be returned
			// By default only minor versions will be returned (e.g. X.Y)
			// for other options see VERSION_TRUNCATION_* constants in DeviceParserAbstract class
			//\DeviceParserAbstract::setVersionTruncation(\DeviceParserAbstract::VERSION_TRUNCATION_NONE);

			$ua = new \DeviceDetector($userAgent);
			$ua->discardBotInformation();
			$ua->parse();

			if($ua->isBot()) {
				// handle bots,spiders,crawlers,...
				$ua_parse['client'] = $ua->getBot();
				$ua_parse['os'] = NULL;
				$ua_parse['device'] = NULL;
				$ua_parse['brand'] = NULL;
				$ua_parse['model'] = NULL;
			} else {
				$ua_parse['client'] = $ua->getClient(); // holds information about browser, feed reader, media player, ...
				$ua_parse['os'] = $ua->getOs();
				$ua_parse['device'] = ucwords($ua->getDeviceName());
				$ua_parse['brand'] = $ua->getBrand();
				$ua_parse['model'] = $ua->getModel();
			}

			return $ua_parse;
		});

		$response = array(
			'os' => $ua['os']['name'],
			'client' => $ua['client']['name'] . ' ' . $ua['client']['version'],
			'device' => ($ua['device'] == '') ? NULL : $ua['device'],
			'brand' =>  ($ua['brand'] == '') ? NULL : $ua['brand'],
			'model' => ($ua['model'] == '') ? NULL : $ua['model']
		);

		return $response;
	}

    /**
     * Get visits 
     */
	public static function getVisits($idSite, $start = NULL, $end = NULL)
	{
		if($start == NULL) $start = date('Y-m-d', strtotime(' - 30 day'));
		if($end == NULL) $end = date('Y-m-d');

		$multiple = false;

		if(! is_numeric($idSite) && $idSite != '')
		{
			$sites = \App\Core\Piwik::getSitesAccessFromUser($idSite);
			if(count($sites) > 0)
			{
				$multiple = true;
				$idSite = '';
				foreach($sites as $site)
				{
					$idSite .= $site['site'] . ',';
				}
				$idSite = rtrim($idSite, ',');
			}
			else
			{
				return 0;
			}
		}

        $response = \Piwik::custom('VisitsSummary.getVisits', array(
            'idSite' => urlencode($idSite),
            'period' => urlencode('range'),
            'date' => urlencode($start . ',' . $end)
        ), false, false, 'php');

		if($multiple && is_array($response))
		{
			$total = 0;
			foreach($response as $key => $val)
			{
				$total += $val;
			}
			$response = $total;
		}

		return $response;
	}

    /**
     * Get summary for range
     */
	public static function getSummaryRange($idSite, $start = NULL, $end = NULL)
	{
		if($start == NULL) $start = date('Y-m-d', strtotime(' - 30 day'));
		if($end == NULL) $end = date('Y-m-d');

        $response = \Piwik::custom('VisitsSummary.get', array(
            'idSite' => urlencode($idSite),
            'period' => urlencode('range'),
            'date' => urlencode($start . ',' . $end)
        ), false, false, 'php');

		return (isset($response['result']) && $response['result'] == 'error') ? array() : $response;
	}

    /**
     * Get range with visits
     */
	public static function getSummaryDays($idSite, $start = NULL, $end = NULL)
	{
		if($start == NULL) $start = date('Y-m-d', strtotime(' - 30 day'));
		if($end == NULL) $end = date('Y-m-d');

        $response = \Piwik::custom('VisitsSummary.get', array(
            'idSite' => urlencode($idSite),
            'period' => urlencode('day'),
            'date' => urlencode($start . ',' . $end)
        ), false, false, 'php');

		return (isset($response['result']) && $response['result'] == 'error') ? array() : $response;
	}

    /**
     * Get referrers for range
     */
	public static function getReferrersRange($idSite, $start = NULL, $end = NULL)
	{
		if($start == NULL) $start = date('Y-m-d', strtotime(' - 30 day'));
		if($end == NULL) $end = date('Y-m-d');

        $response = \Piwik::custom('Referrers.getWebsites', array(
            'idSite' => urlencode($idSite),
            'period' => urlencode('range'),
            'date' => urlencode($start . ',' . $end),
            'expanded' => 1
        ), false, false, 'php');

		return (isset($response['result']) && $response['result'] == 'error') ? array() : $response;
	}

    /**
     * Get browsers range
     */
	public static function getBrowsersRange($idSite, $start = NULL, $end = NULL)
	{
		if($start == NULL) $start = date('Y-m-d', strtotime(' - 30 day'));
		if($end == NULL) $end = date('Y-m-d');

        $response = \Piwik::custom('DevicesDetection.getBrowsers', array(
            'idSite' => urlencode($idSite),
            'period' => urlencode('range'),
            'date' => urlencode($start . ',' . $end)
        ), false, false, 'php');

		return (isset($response['result']) && $response['result'] == 'error') ? array() : $response;
	}

    /**
     * Get operating system range
     */
	public static function getOsRange($idSite, $start = NULL, $end = NULL)
	{
		if($start == NULL) $start = date('Y-m-d', strtotime(' - 30 day'));
		if($end == NULL) $end = date('Y-m-d');

        $response = \Piwik::custom('DevicesDetection.getOsFamilies', array(
            'idSite' => urlencode($idSite),
            'period' => urlencode('range'),
            'date' => urlencode($start . ',' . $end)
        ), false, false, 'php');

		return (isset($response['result']) && $response['result'] == 'error') ? array() : $response;
	}

    /**
     * Get countries range
     */
	public static function getCountriesRange($idSite, $start = NULL, $end = NULL)
	{
		if($start == NULL) $start = date('Y-m-d', strtotime(' - 30 day'));
		if($end == NULL) $end = date('Y-m-d');

        $response = \Piwik::custom('UserCountry.getCountry', array(
            'idSite' => urlencode($idSite),
            'period' => urlencode('range'),
            'date' => urlencode($start . ',' . $end)
        ), false, false, 'php');

		return (isset($response['result']) && $response['result'] == 'error') ? array() : $response;
	}

    /**
     * Get JavaScript tag
     */
    public static function getJavascriptTag($idSite)
    {
        $response = \Piwik::custom('SitesManager.getJavascriptTag', array(
            'idSite' => urlencode($idSite)
        ), false, false, 'php');

		return $response;
    }

    /**
     * Check if user exists, if not add user and create site
     */
    public static function checkUserAddSite($urls, $timezone)
    {
		if(\Auth::check())
		{
			$parent_user_id = (\Auth::user()->parent_id == NULL) ? \Auth::user()->id : \Auth::user()->parent_id;
		}
		else
		{
			die('Not authorized');
		}

		$site_id = 0;
        $userLogin = 'user' . $parent_user_id;
        $password = str_random(20);
        $email = $userLogin . '@' . $_SERVER['HTTP_HOST'];
        $alias = 'user' ;
        $group = 'categoty' ;
        $siteName = $urls;
        $timezone = (timezone_name_from_abbr($timezone) != '') ? timezone_name_from_abbr($timezone) : 'UTC';

        // Check if user exists
        $user_exists = \Piwik::custom('UsersManager.userExists', array(
            'userLogin' => urlencode($userLogin)
        ), false, false, 'php');

        // Create if not exists
        if(! $user_exists)
        {
            $user_id = \Piwik::custom('UsersManager.addUser', array(
                'userLogin' => urlencode($userLogin),
                'password' => urlencode($password),
                'email' => urlencode($email),
                'alias' => urlencode($alias)
            ), false, false, 'php');
        }

        // Add site
        $site_id = \Piwik::custom('SitesManager.addSite', array(
            'siteName' => urlencode($siteName),
            'urls' => urlencode($urls),
            'timezone' => urlencode($timezone)
        ), false, false, 'php');

        // Give user permission on site
        $response = \Piwik::custom('UsersManager.setUserAccess', array(
            'userLogin' => urlencode($userLogin),
            'access' => urlencode('admin'), // view, admin
            'idSites' => urlencode($site_id)
        ), false, false, 'php');

        // Return site id
		return $site_id;
    }

    /**
     * Add a new Piwik user
     */
    public static function addUser($userLogin, $password, $email, $alias)
    {
		$stats_user_id = 0;

        // Check if user exists
        $user_exists = \Piwik::custom('UsersManager.userExists', array(
            'userLogin' => urlencode($userLogin)
        ), false, false, 'php');

        // Create if not exists
        if(! $user_exists)
        {
            $stats_user_id = \Piwik::custom('UsersManager.addUser', array(
                'userLogin' => urlencode($userLogin),
                'password' => urlencode($password),
                'email' => urlencode($email),
                'alias' => urlencode($alias)
            ), false, false, 'php');
        }

		return $stats_user_id;
    }

    /**
     * Delete existing Piwik user by userLogin (username)
     */
    public static function deleteUser($userLogin)
    {
        $response = \Piwik::custom('UsersManager.deleteUser', array(
            'userLogin' => urlencode($userLogin)
        ), false, false, 'php');

		return $response;
    }

    /**
     * Add a new site
     */
    public static function addSite($siteName, $urls, $timezone, $group)
    {
        $stats_id = \Piwik::custom('SitesManager.addSite', array(
            'siteName' => urlencode($siteName),
            'urls' => urlencode($urls),
            'timezone' => urlencode($timezone),
            'group' => urlencode($group)
        ), false, false, 'php');

		return $stats_id;
    }

    /**
     * Delete existing Piwik site by ID
     */
    public static function deleteSite($idSite)
    {
        $response = \Piwik::custom('SitesManager.deleteSite', array(
            'idSite' => urlencode($idSite)
        ), false, false, 'php');

		return $response;
    }

    /**
     * Update existing Piwik site by idSite
     */
    public static function updateSite($idSite, $siteName, $urls, $timezone, $group = NULL)
    {
		if($group == NULL)
		{
			$response = \Piwik::custom('SitesManager.updateSite', array(
				'idSite' => urlencode($idSite),
				'siteName' => urlencode($siteName),
				'urls' => urlencode($urls),
				'timezone' => urlencode($timezone)
			), false, false, 'php');
		}
		else
		{
			$response = \Piwik::custom('SitesManager.updateSite', array(
				'idSite' => urlencode($idSite),
				'siteName' => urlencode($siteName),
				'urls' => urlencode($urls),
				'timezone' => urlencode($timezone),
				'group' => urlencode($group)
			), false, false, 'php');
		}

		return $response;
    }

    /**
     * Get sites where user has access to
	 * 
     */
    public static function getSitesAccessFromUser($userLogin)
    {
		$response = \Piwik::custom('UsersManager.getSitesAccessFromUser', array(
            'userLogin' => urlencode($userLogin)
        ), false, false, 'php');

		/*
		if($response != NULL)
		{
			foreach($response as $site)
			{
				echo $site['site'] . '<br>';
				echo $site['access'] . '<br>';
			}
		}

		Output is NULL or:
		
		array (size=2)
		  0 => 
			array (size=2)
			  'site' => string '2' (length=1)
			  'access' => string 'admin' (length=5)
		  1 => 
			array (size=2)
			  'site' => string '3' (length=1)
			  'access' => string 'admin' (length=5)
		*/
		return $response;
    }
}