<?php
namespace App\Controller;

use View, Auth, Input, Cache;

/*
|--------------------------------------------------------------------------
| oAuth controller
|--------------------------------------------------------------------------
|
| oAuth related logic
|
*/

class oAuthController extends \BaseController {

  const LIST_CACHE = 5;

  /**
   * Construct
   */

  public function __construct() {
		if(Auth::check()) {
			$this->parent_user_id = (Auth::user()->parent_id == NULL) ? Auth::user()->id : Auth::user()->parent_id;
		} else {
			$this->parent_user_id = NULL;
		}

    if (\Config::get('mailchimp.client_id', '') != '' && \Config::get('mailchimp.client_secret', '') != '') {
      $this->mailchimp_provider = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId' => \Config::get('mailchimp.client_id'),
        'clientSecret' => \Config::get('mailchimp.client_secet'),
        'redirectUri' => url('/api/v1/oauth/mailchimp'),
        'urlAuthorize' => \Config::get('mailchimp.urlAuthorize'),
        'urlAccessToken' => \Config::get('mailchimp.urlAccessToken'),
        'urlResourceOwnerDetails' => \Config::get('mailchimp.urlResourceOwnerDetails')
      ]);
    }

    if (\Config::get('getresponse.client_id', '') != '' && \Config::get('getresponse.client_secret', '') != '') {
      require 'oAuth/GetResponse.php';
      $this->getresponse_provider = new \League\OAuth2\Client\Provider\GetResponse([
        'clientId' => \Config::get('getresponse.client_id'),
        'clientSecret' => \Config::get('getresponse.client_secret'),
        'redirectUri' => url('/api/v1/oauth/getresponse'),
        'urlAuthorize' => \Config::get('getresponse.urlAuthorize'),
        'urlAccessToken' => \Config::get('getresponse.urlAccessToken'),
        'urlResourceOwnerDetails' => \Config::get('getresponse.urlResourceOwnerDetails')
      ]);
    }
  }

  /**
   * Show app
   */

  public function getApps() {

    $apps = [];

    // Aweber connection
    if (\Config::get('aweber.consumer_key', '') != '' && \Config::get('aweber.consumer_secret', '') != '') {

      $oAuth = \App\Model\oAuth::where('user_id', $this->parent_user_id)
        ->where('provider', 'aweber')->first();

      $connected = (empty($oAuth)) ? false : true;

      $connect_url = ($connected) ? url('api/v1/oauth/disconnect/aweber') : url('api/v1/oauth/aweber');

      if ($connected) {
        $accountname = $oAuth->uuid;
      } else {
        $accountname = trans('global.not_connected');
      }

      $apps[] = [
        'name' => 'Aweber',
        'dataName' => 'aweber',
        'img' => url('assets/images/interface/third-parties/aweber.svg'),
        'connected' => $connected,
        'connect_url' => $connect_url,
        'accountname' => $accountname,
        'info' => trans('global.aweber_info')
      ];
    }

    // GetResponse connection
    if (\Config::get('getresponse.client_id', '') != '' && \Config::get('getresponse.client_secret', '') != '') {

      $oAuth = \App\Model\oAuth::where('user_id', $this->parent_user_id)
        ->where('provider', 'getresponse')->first();

      $connected = (empty($oAuth)) ? false : true;

      $connect_url = ($connected) ? url('api/v1/oauth/disconnect/getresponse') : url('api/v1/oauth/getresponse');

      if ($connected) {
        $settings = json_decode($oAuth->settings);
        $accountname = (isset($settings->accountname)) ? $settings->accountname : '';
      } else {
        $accountname = trans('global.not_connected');
      }

      $apps[] = [
        'name' => 'GetResponse',
        'dataName' => 'getresponse',
        'img' => url('assets/images/interface/third-parties/getresponse.svg'),
        'connected' => $connected,
        'connect_url' => $connect_url,
        'accountname' => $accountname,
        'info' => trans('global.getresponse_info')
      ];
    }

    // MailChimp connection
    if (\Config::get('mailchimp.client_id', '') != '' && \Config::get('mailchimp.client_secret', '') != '') {

      $oAuth = \App\Model\oAuth::where('user_id', $this->parent_user_id)
        ->where('provider', 'mailchimp')->first();

      $connected = (empty($oAuth)) ? false : true;

      $connect_url = ($connected) ? url('api/v1/oauth/disconnect/mailchimp') : url('api/v1/oauth/mailchimp');

      if ($connected) {
        $settings = json_decode($oAuth->settings);
        $accountname = (isset($settings->accountname)) ? $settings->accountname : '';
      } else {
        $accountname = trans('global.not_connected');
      }

      $apps[] = [
        'name' => 'MailChimp',
        'dataName' => 'mailchimp',
        'img' => url('assets/images/interface/third-parties/mailchimp.svg'),
        'connected' => $connected,
        'connect_url' => $connect_url,
        'accountname' => $accountname,
        'info' => trans('global.mailchimp_info')
      ];
    }

    return View::make('app.oauth.apps', array(
      'apps' => $apps
    ));
  }

  /**
   * Disconnect any oAuth account
   */

  public function getDisconnect($provider) {

    $oAuth = \App\Model\oAuth::where('user_id', $this->parent_user_id)
      ->where('provider', $provider)
      ->first();

    if (! empty($oAuth)) {
      $oAuth->forceDelete();
    }

    return \Response::json(array('result' => 'success'));
  }

  /**
   * -------------------------------------------------------------------------------------------------------------------------------
   * MailChimp authorize
   * -------------------------------------------------------------------------------------------------------------------------------
   */

  public function getMailchimp() {

		session_start();

    // If we don't have an authorization code then get one
    if (!isset($_GET['code'])) {
    
      // Fetch the authorization URL from the provider; this returns the
      // urlAuthorize option and generates and applies any necessary parameters
      // (e.g. state).
      $authorizationUrl = $this->mailchimp_provider->getAuthorizationUrl();
    
      // Get the state generated for you and store it to the session.
      $_SESSION['oauth2state'] = $this->mailchimp_provider->getState();
    
      // Redirect the user to the authorization URL.
      header('Location: ' . $authorizationUrl);
      exit;
    
    // Check given state against previously stored one to mitigate CSRF attack
    } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    
      unset($_SESSION['oauth2state']);
      exit('Invalid state');
    
    } else {
    
      try {

        // Try to get an access token using the authorization code grant.
        $accessToken = $this->mailchimp_provider->getAccessToken('authorization_code', [
          'code' => $_GET['code']
        ]);
  
        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $this->mailchimp_provider->getResourceOwner($accessToken)->toArray();

        $oAuth = \App\Model\oAuth::where('user_id', $this->parent_user_id)
          ->where('provider', 'mailchimp')
          ->where('uuid', $resourceOwner['user_id'])
          ->first();

        // Get MailChimp API Endpoint
        $request = $this->mailchimp_provider->getAuthenticatedRequest(
          'GET',
          \Config::get('mailchimp.urlResourceOwnerDetails'),
          $accessToken->getToken()
        );

        $response = $this->mailchimp_provider->getResponse($request);

        $api_endpoint = $response['api_endpoint'];

        if (empty($oAuth)) {
          $oAuth = new \App\Model\oAuth;
          $oAuth->user_id = $this->parent_user_id;
          $oAuth->uuid = $resourceOwner['user_id'];
          $oAuth->provider = 'mailchimp';
          $oAuth->oauth2_access_token = $accessToken->getToken();
          $oAuth->settings = \App\Core\Settings::json(array(
            'accountname' => $resourceOwner['accountname'],
            'api_endpoint' => $response['api_endpoint']
          ));

          $oAuth->save();
        } else {
          $oAuth->oauth2_access_token = $accessToken->getToken();
          $oAuth->settings = \App\Core\Settings::json(array(
            'accountname' => $resourceOwner['accountname'],
            'api_endpoint' => $response['api_endpoint']
          ), $oAuth->settings);

          $oAuth->save();
        }

        return View::make('app.oauth.oauth-success');

      } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());
      }
    }
  }

  /**
   * Get MailChimp lists
   */

  public static function getMailchimpLists() {

    $parent_user_id = (Auth::user()->parent_id == NULL) ? Auth::user()->id : Auth::user()->parent_id;

    $cache_key = 'mailchimp' . $parent_user_id;

    return \Cache::remember($cache_key, 5, function() use($parent_user_id) {

      $mailchimp_provider = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId' => \Config::get('mailchimp.client_id'),
        'clientSecret' => \Config::get('mailchimp.client_secet'),
        'redirectUri' => url('/api/v1/oauth/mailchimp'),
        'urlAuthorize' => \Config::get('mailchimp.urlAuthorize'),
        'urlAccessToken' => \Config::get('mailchimp.urlAccessToken'),
        'urlResourceOwnerDetails' => \Config::get('mailchimp.urlResourceOwnerDetails')
      ]);

      $oAuth = \App\Model\oAuth::where('user_id', $parent_user_id)
        ->where('provider', 'mailchimp')
        ->first();

      if (! empty($oAuth)) {
  
        $settings = json_decode($oAuth->settings);
        $api_endpoint = (isset($settings->api_endpoint)) ? $settings->api_endpoint . '/3.0' : '';
  
        // Get MailChimp Lists
        $request = $mailchimp_provider->getAuthenticatedRequest(
          'GET',
          $api_endpoint . '/lists',
          $oAuth->oauth2_access_token
        );
  
        $lists = $mailchimp_provider->getResponse($request);
  
        return $lists['lists'];
      } else {
        return [];
      }

    });
  }

  /**
   * Insert member into MailChimp list
   */

  public static function postMailchimpMember($parent_user_id, $list_id, $arrPost) {

    $mailchimp_provider = new \League\OAuth2\Client\Provider\GenericProvider([
      'clientId' => \Config::get('mailchimp.client_id'),
      'clientSecret' => \Config::get('mailchimp.client_secet'),
      'redirectUri' => url('/api/v1/oauth/mailchimp'),
      'urlAuthorize' => \Config::get('mailchimp.urlAuthorize'),
      'urlAccessToken' => \Config::get('mailchimp.urlAccessToken'),
      'urlResourceOwnerDetails' => \Config::get('mailchimp.urlResourceOwnerDetails')
    ]);

    $oAuth = \App\Model\oAuth::where('user_id', $parent_user_id)
      ->where('provider', 'mailchimp')
      ->first();

    if (! empty($oAuth)) {

      $settings = json_decode($oAuth->settings);
      $api_endpoint = (isset($settings->api_endpoint)) ? $settings->api_endpoint . '/3.0' : '';

      // POST MailChimp member
      $options['body'] = json_encode($arrPost);
      $options['headers']['content-type'] = 'application/json';

      $request = $mailchimp_provider->getAuthenticatedRequest(
        'POST',
        $api_endpoint . '/lists/' . $list_id . '/members',
        $oAuth->oauth2_access_token,
        $options
      );

      $response = $mailchimp_provider->getResponse($request);

      return $response;
    }
  }

  /**
   * -------------------------------------------------------------------------------------------------------------------------------
   * Aweber authorize
   * -------------------------------------------------------------------------------------------------------------------------------
   */

  public function getAweber() {

		session_start();

    $aweber = new \AWeberAPI(\Config::get('aweber.consumer_key'), \Config::get('aweber.consumer_secret'));

    # Get an access token
    if (empty($_COOKIE['accessToken'])) {
      if (empty($_GET['oauth_token'])) {
        $callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        list($requestToken, $requestTokenSecret) = $aweber->getRequestToken($callbackUrl);
        setcookie('requestTokenSecret', $requestTokenSecret);
        setcookie('callbackUrl', $callbackUrl);
        header("Location: {$aweber->getAuthorizeUrl()}");
        exit();
      }
  
      $aweber->user->tokenSecret = $_COOKIE['requestTokenSecret'];
      $aweber->user->requestToken = $_GET['oauth_token'];
      $aweber->user->verifier = $_GET['oauth_verifier'];
      list($accessToken, $accessTokenSecret) = $aweber->getAccessToken();
      setcookie('accessToken', $accessToken);
      setcookie('accessTokenSecret', $accessTokenSecret);
      header('Location: '.$_COOKIE['callbackUrl']);
      exit();
    }

    # Get AWeber Account
    $account = $aweber->getAccount($_COOKIE['accessToken'], $_COOKIE['accessTokenSecret']);

    $oAuth = \App\Model\oAuth::where('user_id', $this->parent_user_id)
      ->where('provider', 'aweber')
      ->where('uuid', $account->id)
      ->first();

    if (empty($oAuth)) {
      $oAuth = new \App\Model\oAuth;
      $oAuth->user_id = $this->parent_user_id;
      $oAuth->uuid = $account->id;
      $oAuth->provider = 'aweber';
      $oAuth->oauth1_token_identifier = $_COOKIE['accessToken'];
      $oAuth->oauth1_token_secret = $_COOKIE['accessTokenSecret'];

      $oAuth->save();
    } else {
      $oAuth->oauth1_token_identifier = $_COOKIE['accessToken'];
      $oAuth->oauth1_token_secret = $_COOKIE['accessTokenSecret'];

      $oAuth->save();
    }

    return View::make('app.oauth.oauth-success');
  }

  /**
   * Get Aweber lists
   */

  public static function getAweberLists() {

    $parent_user_id = (Auth::user()->parent_id == NULL) ? Auth::user()->id : Auth::user()->parent_id;

    $cache_key = 'aweber' . $parent_user_id;

    return \Cache::remember($cache_key, 10, function() use($parent_user_id) {
    
      $oAuth = \App\Model\oAuth::where('user_id', $parent_user_id)
        ->where('provider', 'aweber')
        ->first();
    
      if (! empty($oAuth)) {
        $aweber = new \AWeberAPI(\Config::get('aweber.consumer_key'), \Config::get('aweber.consumer_secret'));
        $account = $aweber->getAccount($oAuth->oauth1_token_identifier, $oAuth->oauth1_token_secret);
    
        $lists = [];
    
        foreach ($account->lists as $list) {
          $lists[] = [
            'id' => $list->id,
            'name' => $list->name
          ];
        }
    
        return $lists;
      } else {
        return [];
      }

    });

  }

  /**
   * Insert subscriber into Aweber list
   */

  public static function postAweberSubscriber($parent_user_id, $list_id, $arrPost) {

    $oAuth = \App\Model\oAuth::where('user_id', $parent_user_id)
      ->where('provider', 'aweber')
      ->first();

    if (! empty($oAuth)) {
      $aweber = new \AWeberAPI(\Config::get('aweber.consumer_key'), \Config::get('aweber.consumer_secret'));
      $account = $aweber->getAccount($oAuth->oauth1_token_identifier, $oAuth->oauth1_token_secret);
      $listURL = '/accounts/' . $oAuth->uuid . '/lists/' . $list_id;

      $list = $account->loadFromUrl($listURL);

      $subscribers = $list->subscribers;
      $new_subscriber = $subscribers->create($arrPost);

      return $new_subscriber;
    }
  }

  /**
   * -------------------------------------------------------------------------------------------------------------------------------
   * GetResponse authorize
   * -------------------------------------------------------------------------------------------------------------------------------
   */

  public function getGetresponse() {

		session_start();

    // If we don't have an authorization code then get one
    if (!isset($_GET['code'])) {
    
      // Fetch the authorization URL from the provider; this returns the
      // urlAuthorize option and generates and applies any necessary parameters
      // (e.g. state).
      $authorizationUrl = $this->getresponse_provider->getAuthorizationUrl();
    
      // Get the state generated for you and store it to the session.
      $_SESSION['oauth2state'] = $this->getresponse_provider->getState();

      // Redirect the user to the authorization URL.
      header('Location: ' . $authorizationUrl);
      exit;
    
    // Check given state against previously stored one to mitigate CSRF attack
    } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    
      unset($_SESSION['oauth2state']);
      exit('Invalid state');
    
    } else {
    
      try {

        $accessToken = $this->getresponse_provider->getAccessToken('authorization_code', [
          'code' => $_GET['code']
        ]);

/*        echo $accessToken->getToken() . "<br>";
        echo $accessToken->getRefreshToken() . "<br>";
        echo $accessToken->getExpires() . "<br>";
        echo ($accessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";
*/

        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $this->getresponse_provider->getResourceOwner($accessToken)->toArray();

        $oAuth = \App\Model\oAuth::where('user_id', $this->parent_user_id)
          ->where('provider', 'getresponse')
          ->where('uuid', $resourceOwner['accountId'])
          ->first();

        if (empty($oAuth)) {
          $oAuth = new \App\Model\oAuth;
          $oAuth->user_id = $this->parent_user_id;
          $oAuth->uuid = $resourceOwner['accountId'];
          $oAuth->provider = 'getresponse';
          $oAuth->oauth2_access_token = $accessToken->getToken();
          $oAuth->settings = \App\Core\Settings::json(array(
            'accountname' => $resourceOwner['email']
          ));

          $oAuth->save();
        } else {
          $oAuth->oauth2_access_token = $accessToken->getToken();
          $oAuth->settings = \App\Core\Settings::json(array(
            'accountname' => $resourceOwner['email']
          ), $oAuth->settings);

          $oAuth->save();
        }

        return View::make('app.oauth.oauth-success');

      } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());
      }
    }
  }

  /**
   * GetResponse lists
   */

  public static function getGetresponseLists() {

    $parent_user_id = (Auth::user()->parent_id == NULL) ? Auth::user()->id : Auth::user()->parent_id;

    $cache_key = 'getresponse' . $parent_user_id;

    return \Cache::remember($cache_key, 5, function() use($parent_user_id) {

      $oAuth = \App\Model\oAuth::where('user_id', $parent_user_id)
        ->where('provider', 'getresponse')
        ->first();
    
      if (! empty($oAuth)) {

        require 'oAuth/GetResponse.php';

        $getresponse_provider = new \League\OAuth2\Client\Provider\GetResponse([
          'clientId' => \Config::get('getresponse.client_id'),
          'clientSecret' => \Config::get('getresponse.client_secret'),
          'redirectUri' => url('/api/v1/oauth/getresponse'),
          'urlAuthorize' => \Config::get('getresponse.urlAuthorize'),
          'urlAccessToken' => \Config::get('getresponse.urlAccessToken'),
          'urlResourceOwnerDetails' => \Config::get('getresponse.urlResourceOwnerDetails')
        ]);

        $request = $getresponse_provider->getAuthenticatedRequest(
          'GET',
          'https://api.getresponse.com/v3/campaigns',
          $oAuth->oauth2_access_token
        );

        $response = $getresponse_provider->getResponse($request);

        $lists = [];
    
        foreach ($response as $list) {
          $lists[] = [
            'id' => $list['campaignId'],
            'name' => $list['name']
          ];
        }
    
        return $lists;
      } else {
        return [];
      }

    });
  }

  /**
   * Insert contact into GetResponse list
   */

  public static function postGetResponseContact($parent_user_id, $list_id, $arrPost) {

    require 'oAuth/GetResponse.php';

    $getresponse_provider = new \League\OAuth2\Client\Provider\GetResponse([
      'clientId' => \Config::get('getresponse.client_id'),
      'clientSecret' => \Config::get('getresponse.client_secret'),
      'redirectUri' => url('/api/v1/oauth/getresponse'),
      'urlAuthorize' => \Config::get('getresponse.urlAuthorize'),
      'urlAccessToken' => \Config::get('getresponse.urlAccessToken'),
      'urlResourceOwnerDetails' => \Config::get('getresponse.urlResourceOwnerDetails')
    ]);

    $oAuth = \App\Model\oAuth::where('user_id', $parent_user_id)
      ->where('provider', 'getresponse')
      ->first();

    if (! empty($oAuth)) {
      $arrPost['campaign'] = ['campaignId' => $list_id];

      $options['body'] = json_encode($arrPost);
      $options['headers']['content-type'] = 'application/json';

      $request = $getresponse_provider->getAuthenticatedRequest(
        'POST',
        'https://api.getresponse.com/v3/contacts',
        $oAuth->oauth2_access_token,
        $options
      );

      $response = $getresponse_provider->getResponse($request);

      return $response;
    }
  }
}