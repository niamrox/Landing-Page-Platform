<?php
namespace App\Controller;

use View, Auth, Response, Input;
//use RedBeanPHP\R;

/*
|--------------------------------------------------------------------------
| Account controller
|--------------------------------------------------------------------------
|
| Account related logic
|
*/

class AccountController extends \BaseController {

  /**
   * Construct
   */
  public function __construct()
  {
    if(Auth::check())
    {
      $this->parent_user_id = (Auth::user()->parent_id == NULL) ? Auth::user()->id : Auth::user()->parent_id;
    }
    else
    {
      $this->parent_user_id = NULL;
    }
  }

  /**
   * Show profile partial
   */
  public function getProfile()
  {
    return View::make('app.settings.profile');
  }

  /**
   * oAuth Facebook login
   */
  public function getLoginFacebook()
  {
    session_start();

    $code = \Input::get('code', '');
    $state = \Input::get('state', '');

    $provider = new \League\OAuth2\Client\Provider\Facebook([
      'clientId'      => \Config::get('social-login.facebook.app_id'),
      'clientSecret'    => \Config::get('social-login.facebook.app_secret'),
      'redirectUri'     => url('/api/v1/account/login-facebook'),
      'graphApiVersion'   => \Config::get('social-login.facebook.api_version', 'v2.8')
    ]);

    if ($code == '') 
    {
      // If we don't have an authorization code then get one
      $authUrl = $provider->getAuthorizationUrl();
      $_SESSION['oauth2state'] = $provider->getState();
      header('Location: '.$authUrl);
      exit;
    
    // Check given state against previously stored one to mitigate CSRF attack
    } 
    elseif ($state == '' || ($state !== $_SESSION['oauth2state'])) 
    {
    
      unset($_SESSION['oauth2state']);
      exit('Invalid state');
    
    } else {
    
      // Try to get an access token (using the authorization code grant)
      $token = $provider->getAccessToken('authorization_code', [
        'code' => $code
      ]);
    
      // Optional: Now you have a token you can look up a users profile data
      try {
        $reseller = \App\Controller\ResellerController::get();

        // We got an access token, let's now get the user's details
        $oauth_user = $provider->getResourceOwner($token);

        // Check if user already exists or not
        $user = \User::where('reseller_id', $reseller->id)->where('facebook', $oauth_user->getId())->first();

        if ($user == NULL)
        {
          // Create user and login
          $password = str_random(22);

          $user = new \User;

          $user->reseller_id = $reseller->id;
          $user->parent_id = NULL;
          $user->username = substr($oauth_user->getId(), 0, 16);
          $user->facebook = $oauth_user->getId();
          $user->plan_id = 1;
          $user->email = $oauth_user->getEmail();
          $user->password = $password;
          $user->password_confirmation = $password;
          $user->confirmed = 1;
          $user->logins = 1;
          $user->last_login = date('Y-m-d H:i:s');
          $user->first_name = $oauth_user->getFirstName();
          $user->last_name = $oauth_user->getLastName();
  
          if($user->save())
          {
            // Set role
            $user->attachRole(2);
          }
        }
        else
        {
          $user->increment('logins');
          $user->last_login = date('Y-m-d H:i:s');
          $user->save();
        }

        \Auth::loginUsingId($user->id);
        return \Redirect::to('/platform');
        die();
  
      } catch (Exception $e) {
    
        // Failed to get user details
        exit('Oh dear...');
      }
    }
  }

  /**
   * oAuth Twitter login
   */
  public function getLoginTwitter()
  {
    session_start();

    $oauth_token = \Input::get('oauth_token');
    $oauth_verifier = \Input::get('oauth_verifier');

    $server = new \League\OAuth1\Client\Server\Twitter(array(
      'identifier' => \Config::get('social-login.twitter.api_key'),
      'secret' => \Config::get('social-login.twitter.api_secret'),
      'callback_uri' => url('/api/v1/account/login-twitter'),
    ));

    if (! empty($oauth_token) && ! empty($oauth_verifier)) {
      // Retrieve the temporary credentials we saved before
      $temporaryCredentials = unserialize($_SESSION['temporary_credentials']);

      // We will now retrieve token credentials from the server
      $tokenCredentials = $server->getTokenCredentials($temporaryCredentials, $oauth_token, $oauth_verifier);

      // User is an instance of League\OAuth1\Client\Server\User
      $oauth_user = $server->getUserDetails($tokenCredentials);

      $reseller = \App\Controller\ResellerController::get();

      // Check if user already exists or not
      $user = \User::where('reseller_id', $reseller->id)->where('twitter', $oauth_user->uid)->first();

      if ($user == NULL)
      {
        $reseller = \App\Controller\ResellerController::get();

        // Create user and login
        $password = str_random(22);

           $user = new \User;

        $user->reseller_id = $reseller->id;
        $user->parent_id = NULL;
        $user->username = $oauth_user->nickname;
        $user->twitter = $oauth_user->uid;
        $user->plan_id = 1;
        $user->email = $oauth_user->uid . '@twitter.com';
        $user->password = $password;
        $user->password_confirmation = $password;
        $user->confirmed = 1;
        $user->logins = 1;
        $user->last_login = date('Y-m-d H:i:s');
        $user->first_name = $oauth_user->firstName;
        $user->last_name = $oauth_user->lastName;

        if($user->save())
        {
          // Set role
          $user->attachRole(2);
        }
      }
      else
      {
        $user->increment('logins');
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();
      }

      \Auth::loginUsingId($user->id);
      return \Redirect::to('/platform');
      die();
    }

    $temporaryCredentials = $server->getTemporaryCredentials();
    $_SESSION['temporary_credentials'] = serialize($temporaryCredentials);

    session_write_close();

    $server->authorize($temporaryCredentials);
    die();
  }

  /**
   * Show upgrade partial
   */
  public function getUpgrade()
  {
    return View::make('app.auth.upgrade');
  }

  /**
   * Show account partial
   */
  public function getAccount()
  {
    $reseller = \App\Controller\ResellerController::get();

    // Plans + current plan
    $plans = \App\Model\Plan::where('reseller_id', $reseller->id)->orderBy('sort')->get();
    $expiration_date = \Auth::user()->expires;
    $expiration_date = ($expiration_date == NULL) ? trans('admin.never') : $expiration_date->timezone(Auth::user()->timezone)->format("Y-m-d");

    $user_now = \Carbon::now(new \DateTimeZone(Auth::user()->timezone))->format("Y-m-d");

    $expired = ($expiration_date < $user_now) ? true : false;
    $expiration_message = ($expired) ? 'this_plan_has_expired' : 'this_plan_expires';
    $expiration_message = trans('admin.' . $expiration_message, ['expiration_date' => '<strong>' . $expiration_date . '</strong>']);

    // Orders
    $parent_user_id = (Auth::user()->parent_id == NULL) ? Auth::user()->id : Auth::user()->parent_id;
    $orders = \App\Model\Order::where('reseller_id', $reseller->id)->where('user_id', $parent_user_id)->orderBy('invoice_datetime', 'ASC')->get();
    //$sqlite = storage_path() . '/userdata/subscription_orders' . $reseller->id . '.sqlite';
    //R::setup('sqlite:' . $sqlite);
    //$orders = R::find('orders', 'user_id = ' . $parent_user_id . ' ORDER BY invoice_datetime ASC');
    //R::close();

    return \View::make('app.auth.account', array(
      'plans' => $plans,
      'expired' => $expired,
      'expiration_date' => $expiration_date,
      'expiration_message' => $expiration_message,
      'orders' => $orders
    ));
  }

  /**
   * Show order subscription partial
   */
  public function getOrderSubscription()
  {
    $sl = \Request::input('sl', '');
    if($sl != '')
    {
      $qs = \App\Core\Secure::string2array($sl);

      $current_plan = \App\Model\Plan::find(\Auth::user()->plan_id);

      $current_plan_settings = $current_plan->settings;
      if ($current_plan_settings != '') $current_plan_settings = json_decode($current_plan_settings);

      $current_plan_monthly = (isset($current_plan_settings->monthly)) ? $current_plan_settings->monthly : 0;
      $current_plan_annual = (isset($current_plan_settings->annual)) ? $current_plan_settings->annual : 0;

      $plan = \App\Model\Plan::find($qs['plan_id']);

      $settings = $plan->settings;
      if ($settings != '') $settings = json_decode($settings);

      $monthly = (isset($settings->monthly)) ? $settings->monthly : 0;
      $annual = (isset($settings->annual)) ? $settings->annual : 0;
      $currency = (isset($settings->currency)) ? $settings->currency : 'USD';
      $currencies = trans('currencies');
      $currency_symbol = $currencies[$currency][1];

      $order_message = (\Auth::user()->plan_id != $qs['plan_id']) ? trans('admin.upgrade_plan_for', ['plan' => $plan->name]) : trans('admin.order_plan_for', ['plan' => $plan->name]);

      $expiration_date = (\Auth::user()->expires != NULL) ? \Auth::user()->expires : \Carbon::now();
      $new_date_calc = (\Auth::user()->expires != NULL) ? \Auth::user()->expires : \Carbon::now();
      $new_date_month = $new_date_calc->addMonth()->format("Y-m-d");
      $new_date_year = $new_date_calc->addYear()->format("Y-m-d");

      $cost_month = str_replace('.00', '', number_format($monthly, 2));
      $cost_year = str_replace('.00', '', number_format($annual * 12, 2));

      if (\Auth::user()->expires != NULL && $current_plan->id != $plan->id)
      {
        $diff_days_end_of_plan = $expiration_date->diffInDays(\Carbon::now());

        $discount_month = ($diff_days_end_of_plan > 30) ? $cost_month + $current_plan_monthly : ($current_plan_monthly / 30) * $diff_days_end_of_plan;
        $discount_year = ($diff_days_end_of_plan > 365) ? $cost_year + $current_plan_annual : ($current_plan_annual / 30) * $diff_days_end_of_plan;
        $discount = ($diff_days_end_of_plan > 30) ? $cost_month + $current_plan_monthly : ($current_plan_monthly / 30) * $diff_days_end_of_plan;

        $cost_month = str_replace('.00', '', number_format($monthly + $discount_month, 2));
        $cost_year = str_replace('.00', '', number_format(($annual * 12) + $discount_year, 2));
      }

      $cost_month_str = $currency_symbol . $cost_month;
      $cost_year_str = $currency_symbol . $cost_year;

      return View::make('app.auth.subscription-order', array(
        'sl' => $sl,
        'order_message' => $order_message,
        'plan' => $plan,
        'new_date_month' => $new_date_month,
        'new_date_year' => $new_date_year,
        'cost_month' => $cost_month,
        'cost_year' => $cost_year,
        'cost_month_str' => $cost_month_str,
        'cost_year_str' => $cost_year_str,
        'currency' => $currency
      ));
    }
  }

  /**
   * Post confirm order partial
   */
  public function postOrderPlanConfirm()
  {
    $sl = \Request::get('sl');
    $qs = \App\Core\Secure::string2array($sl);

    $period = \Request::get('period');
    $payment_method = \Request::get('payment_method');
    $cost_month_str = \Request::get('cost_month_str');
    $cost_month = \Request::get('cost_month');
    $new_date_month = \Request::get('new_date_month');
    $cost_year_str = \Request::get('cost_year_str');
    $cost_year = \Request::get('cost_year');
    $new_date_year = \Request::get('new_date_year');
    $currency = \Request::get('currency');

    if ($period == 'month')
    {
      $cost = $cost_month;
      $cost_str = $cost_month_str;
      $new_date = $new_date_month;
    }
    else
    {
      $cost = $cost_year;
      $cost_str = $cost_year_str;
      $new_date = $new_date_year;
    }

    $invoice = array(
      'period' => $period,
      'payment_method' => $payment_method,
      'cost' => $cost,
      'cost_str' => $cost_str,
      'new_date' => $new_date,
      'currency' => $currency,
      'plan_id' => $qs['plan_id']
    );

    $sl = \App\Core\Secure::array2string($invoice);

    return \Response::json(array('result' => 'success', 'redir' => '#/order-subscription-confirm/' . $sl), 200);
  }

  /**
   * Show confirm order partial
   */
  public function getOrderSubscriptionConfirm()
  {
    $sl = \Request::input('sl', '');
    if($sl != '')
    {
      $qs = \App\Core\Secure::string2array($sl);

      $date = date('Y-m-d');

      $to = (\Auth::user()->first_name != '' || \Auth::user()->last_name != '') ? \Auth::user()->first_name . ' ' . \Auth::user()->last_name : \Auth::user()->username;
      $to = $to . ' [' . \Auth::user()->email . ']';

      $plan = \App\Model\Plan::find($qs['plan_id']);

      $payment_method_title = trans('admin.' . $qs['payment_method'] . '_title');
      $currency = $qs['currency'];

      return View::make('app.auth.subscription-order-confirm', array(
        'sl' => $sl,
        'period' => $qs['period'],
        'payment_method' => $qs['payment_method'],
        'cost' => $qs['cost'],
        'cost_str' => $qs['cost_str'],
        'date' => $date,
        'new_date' => $qs['new_date'],
        'to' => $to,
        'plan' => $plan,
        'payment_method_title' => $payment_method_title,
        'currency' => $currency
      ));
    }
  }

  /**
   * Post confirm order partial
   */
  public function postOrderPlanConfirmed()
  {
    $sl = \Request::input('sl', '');
    if($sl != '')
    {
      $reseller = \App\Controller\ResellerController::get();
      $qs = \App\Core\Secure::string2array($sl);

      $date = date('Y-m-d');

      $to_name = (\Auth::user()->first_name != '' || \Auth::user()->last_name != '') ? \Auth::user()->first_name . ' ' . \Auth::user()->last_name : \Auth::user()->username;
      $to = $to_name . ' [' . \Auth::user()->email . ']';

      $plan = \App\Model\Plan::find($qs['plan_id']);
      $plan_settings = json_decode($plan->settings);
      $plan_monthly = (isset($plan_settings->monthly)) ? $plan_settings->monthly : 0;
      $plan_annual = (isset($plan_settings->annual)) ? $plan_settings->annual : 0;

      // Update user
      $user_id = (\Auth::user()->parent_id == NULL) ? \Auth::user()->id : \Auth::user()->parent_id;

         $user = \User::find($user_id);

      if (\Config::get('payment-gateways.auto_update_subscription', false))
      {
        $user->plan_id = $qs['plan_id'];
        $user->expires = $qs['new_date'];
      }

      $settings = json_decode($user->settings);
      $invoices = (isset($settings->invoices)) ? $settings->invoices : 0;
      $user->settings = \App\Core\Settings::json(array('invoices' => $invoices + 1), $user->settings);

      $user->save();

      // Get invoice number
      $invoice_number = \App\Core\Settings::get('invoice_number', 0, 0);
      $invoice_number++;
      \App\Core\Settings::set('invoice_number', $invoice_number);

      // Store order
      //$sqlite = storage_path() . '/userdata/subscription_orders' . $reseller->id . '.sqlite';
      //R::setup('sqlite:' . $sqlite);
      //$order = R::dispense('orders');

      $order = new \App\Model\Order;

      $order->reseller_id = $reseller->id;
      $order->user_id = $user_id;
      $order->user_name = $to_name;
      $order->user_mail = \Auth::user()->email;
      $order->invoice = $invoice_number;
      $order->invoice_date = date('Y-m-d');
      $order->invoice_datetime = date('Y-m-d H:i:s');
      $order->plan_id = $qs['plan_id'];
      $order->plan_monthly = round($plan_monthly * 100);
      $order->plan_annual = round($plan_annual * 100);
      $order->plan_name = $plan->name;
      $order->expires = $qs['new_date'];
      $order->period = $qs['period'];
      $order->payment_method = $qs['payment_method'];
      $order->cost = round($qs['cost'] * 100);
      $order->cost_str = $qs['cost_str'];
      $order->status = 'unconfirmed';

      $order->save();

      //$id = R::store($order);
      //R::close();

      // To do: Create PDF

      // Get html order
      $order = \View::make('app.auth.mail.order', array(
        'invoice_date' => $order->invoice_date,
        'payment_method' => $qs['payment_method'],
        'cost' => $qs['cost'],
        'cost_str' => $qs['cost_str'],
        'plan_id' => $qs['plan_id'],
        'plan_name' => $plan->name,
        'expires' => $qs['new_date'],
        'period' => $qs['period']
      ))->render();

      // Send confirmation mail
      $recipient = \Auth::user()->email;
      $subject = '[' . trans('global.app_title') . '] ' . trans('admin.confirmation_subject');
      $html = trans('admin.confirmation_body', ['name' => $to_name]) . '<br><br>' . $order . '<br><br>---<br>' . trans('global.app_title') . '<br><a href="' . \Request::server('HTTP_HOST') . '">' . \Request::server('HTTP_HOST') . '</a>';

      $bcc = \Config::get('payment-gateways.admin_mail');
      if ($bcc == '') $bcc = \Config::get('mail.from.address');

      $bcc = explode(',', $bcc);

      \Mail::send('app.layouts.mail', ['body' => $html], function($message) use($recipient, $subject)
      {
        $message->from(\Config::get('mail.from.address'), \Config::get('mail.from.name'));
        $message->to($recipient);
        $message->subject($subject);
      });

      $html = trans('admin.recepient') . ': ' . $recipient . '<br><br>' . $html;
      $subject = '[' . trans('global.app_title') . '] ' . trans('admin.account_purchased');

      \Mail::send('app.layouts.mail', ['body' => $html], function($message) use($bcc, $subject)
      {
        $message->from(\Config::get('mail.from.address'), \Config::get('mail.from.name'));
        $message->to($bcc);
        $message->subject($subject);
      });

      return \Response::json(array('result' => 'success', 'redir' => '#/order-subscription-confirmed/' . $sl), 200);
    }
  }

  /**
   * Return from 3rd party checkout
   */
  public function getReturnCheckout()
  {
    $sl = \Request::input('custom', '');
    if($sl != '')
    {
      $qs = \App\Core\Secure::string2array($sl);
      dd($qs);
    }
  }

  /**
   * Show confirmed order partial
   */
  public function getOrderSubscriptionConfirmed()
  {
    $sl = \Request::input('sl', '');
    if($sl != '')
    {
      $qs = \App\Core\Secure::string2array($sl);

      $date = date('Y-m-d');

      $to = (\Auth::user()->first_name != '' || \Auth::user()->last_name != '') ? \Auth::user()->first_name . ' ' . \Auth::user()->last_name : \Auth::user()->username;
      $to = $to . ' [' . \Auth::user()->email . ']';

      $plan = \App\Model\Plan::find($qs['plan_id']);

      return View::make('app.auth.subscription-order-confirmed', array(
        'sl' => $sl,
        'period' => $qs['period'],
        'payment_method' => $qs['payment_method'],
        'cost' => $qs['cost'],
        'cost_str' => $qs['cost_str'],
        'date' => $date,
        'new_date' => $qs['new_date'],
        'to' => $to,
        'plan' => $plan
      ));
    }
  }

  /**
   * Show invoice modal
   */
  public function getInvoiceModal()
  {
    $sl = \Request::input('sl', NULL);
  
    if($sl != NULL)
    {
      $reseller = \App\Controller\ResellerController::get();
      $data = \App\Core\Secure::string2array($sl);
      $invoice_id = $data['invoice_id'];
      $user_id = (\Auth::user()->parent_id == NULL) ? \Auth::user()->id : \Auth::user()->parent_id;

      $order = \App\Model\Order::where('id', $invoice_id)->where('user_id', $user_id)->orderBy('invoice_datetime', 'ASC')->first();

      //$sqlite = storage_path() . '/userdata/subscription_orders' . $reseller->id . '.sqlite';
      //R::setup('sqlite:' . $sqlite);

      //$order = R::findOne('orders', 'id = ' . $invoice_id . ' AND user_id = ' . $user_id);

      //R::close();

      return View::make('app.auth.modal.invoice', array(
        'sl' => $sl,
        'invoice_date' => $order->invoice_date,
        'payment_method' => $order->payment_method,
        'cost' => $order->cost,
        'cost_str' => $order->cost_str,
        'plan_id' => $order->plan_id,
        'plan_name' => $order->plan_name,
        'expires' => $order->new_date,
        'period' => $order->period,
        'expires' => $order->expires,
        'user_name' => $order->user_name,
        'status' => $order->status
      ));
    }
  }

  /**
   * Show users partial
   */
  public function getUsers()
  {
    $users = \User::account()->get();

    return View::make('app.settings.users', array(
      'users' => $users
    ));
  }

  /**
   * Show user partial
   */
  public function getUser()
  {
    $sl = \Request::input('sl', '');

    if($sl != '')
    {
      $sl = \App\Core\Secure::string2array($sl);
         $user = \User::find($sl['user_id']);
         $user->settings = json_decode($user->settings);

      return View::make('app.settings.user-edit', array(
        'user' => $user,
        'sl' => \Request::input('sl')
      ));
    }
    else
    {
      return View::make('app.settings.user-new');
    }
  }

  /**
   * Return array of languages in form of [LANGUAGE_CODE] => [LANGUAGE_NAME]
   */
  public static function languages() 
  {
    $languages = array();
    $lang_path = base_path() . '/app/lang/';
    $lang_dirs = \File::directories($lang_path);

    foreach($lang_dirs as $lang)
    {
      $language = include $lang . '/i18n.php';
      $languages[$language['language_code']] = $language['language_title'];
    }

    return $languages;
  }

  /**
   * Get all languages
   */
  public static function getLanguages()
  {
    $current_language = \App::getLocale();
    $languages = \File::directories(base_path() . '/app/lang/');

    $return = array();
    foreach($languages as $language)
    {
      if(\File::isFile($language . '/i18n.php'))
      {
        $i18n = include($language . '/i18n.php');
        $active = ($i18n['language_code'] == $current_language) ? true : false;
        $return[] = array('code' => $i18n['language_code'], 'title' => $i18n['language_title'], 'active' => $active);
      }
    }

    $title = array();

    foreach ($return as $key => $row)
    {
      $title[$key] = $row['title'];
    }

    array_multisort($title, SORT_ASC, $return);

    return $return;
  }

  /**
   * Change the language of the interface.
   *
   * @param   string $lang GET Language code
   *
   * @return  JSON: result => success
   */
  public function getLanguage()
  {
    $lang = \Input::get('lang', \Config::get('app.locale'));

    if($lang != '' && \File::isFile(base_path() . '/app/lang/' . $lang . '/i18n.php')) {
      \Session::put('system.language', $lang);
    } elseif(\Session::get('system.language') != '') {
      $lang = \Session::get('system.language');
    } else {
      $lang = \Config::get('app.locale');
      \Session::put('system.language', $lang);
    }

    \Auth::user()->language = $lang;
    \Auth::user()->save();

    return \Response::json(array('result' => 'success'), 200);
  }

  /**
   * Get App (CMS) language
   */
  public static function appLanguage()
  {
    $lang = \Config::get('app.locale');
    $lang_qs = \Input::get('lang', '');

    if ($lang_qs != '')
    {
      \Session::put('system.language', $lang_qs);
    }

    if(\Auth::check())
    {
      $lang = \Auth::user()->language;
    }
    elseif (\Session::get('system.language') != '')
    {
      $lang = \Session::get('system.language');
    }

    return $lang;
  }

  /**
   * Get Site (user App) language
   */
  public static function siteLanguage($app)
  {
    if (isset($app->language))
    {
      // Check if site language exists
      if(\File::isFile(base_path() . '/app/lang/' . $app->language . '/i18n.php'))
      {
        return $app->language;
      }

      // Check if user's language exists
      $user = \User::find($app->user_id);
      if(\File::isFile(base_path() . '/app/lang/' . $user->language . '/i18n.php'))
      {
        return $user->language;
      }
    }
    else
    {
      // Check if language exists
      if(\File::isFile(base_path() . '/app/lang/' . substr($app, 0, 2) . '/i18n.php'))
      {
        return substr($app, 0, 2);
      }
    }
    // Return default language
    return \Config::get('app.locale');
  }

  /**
   * Show avatar upload partial
   */
  public function getAvatarModal()
  {
    $sl = \Request::input('sl', NULL);
    $user_id = NULL;
  
    if($sl != NULL)
    {
      $data = \App\Core\Secure::string2array($sl);
      $user_id = $data['user_id'];
         $user = \User::find($user_id);

      $has_avatar = ($user->avatar_file_name != '') ? true : false;
      $own_avatar = ($user_id == Auth::user()->id) ? true : false;
    }
    else
    {
      $user_id = Auth::user()->id;
      $own_avatar = true;
      $has_avatar = (Auth::user()->avatar_file_name == '') ? false : true;
    }

    return View::make('app.settings.modal.avatar', array(
      'sl' => $sl,
      'user_id' => $user_id,
      'own_avatar' => $own_avatar,
      'has_avatar' => $has_avatar
    ));
  }

  /**
   * Get user's avatar and default to Identicon
   */
  public static function getAvatar($size, $color = '4ab6d5', $sl = NULL)
  {
    if($sl == NULL)
    {
      $avatar_file_name = Auth::user()->avatar_file_name;
      $avatar_small = Auth::user()->avatar->url('small');
      $avatar_medium = Auth::user()->avatar->url('medium');
      $email = Auth::user()->email;
    }
    else
    {
      $sl = \App\Core\Secure::string2array($sl);
      $user = \User::find($sl['user_id']);
      $avatar_file_name = $user->avatar_file_name;
      $avatar_small = $user->avatar->url('small');
      $avatar_medium =$user->avatar->url('medium');
      $email = $user->email;
    }
    
    if($avatar_file_name != '')
    {
      switch($size)
      {
        case '32':
          return $avatar_small;
          break;
        default:
          return $avatar_medium;
      }
    }
    else
    {
      // Identicon
      $identicon = new \Identicon\Identicon();
      return $identicon->getImageDataUri($email, $size, $color);
    }
  }

  /**
   * Update avatar
   */
  public function postAvatar()
  {
    $input = array(
      'avatar' => Input::file('avatar'),
      'extension'  => \Str::lower(Input::file('avatar')->getClientOriginalExtension())
    );

    $rules = array(
      'avatar' => 'mimes:jpeg,gif,png',
      'extension'  => 'required|in:jpg,jpeg,png,gif'
    );
    $validator = \Validator::make($input, $rules);

    if($validator->fails())
    {
         echo "<script>alert('" . $validator->messages()->first() . "')</script>";
      die();
    }
    else
    {
      $sl = \Request::input('sl', NULL);
  
      if($sl != NULL)
      {
        $data = \App\Core\Secure::string2array($sl);
        $user_id = $data['user_id'];
      }
      else
      {
        $user_id = \Auth::user()->id;
      }

      $user = \User::find($user_id);
      $user->avatar = $input['avatar'];
      $user->save();

      // Log
      \App\Controller\LogController::Log($user, 'Account', 'changed avatar');
    }

    echo "<script>parent.formSubmittedSuccess('" . $user->avatar->url('small') . "', '" . $user->avatar->url('medium') . "')</script>";
  }

  /**
   * Delete avatar
   */
  public function getDeleteAvatar()
  {
    $sl = Input::get('data');
    $sl = \App\Core\Secure::string2array($sl);

    $user = \User::find($sl['user_id']);
    $user->avatar = STAPLER_NULL;
    $user->save();

    // Log
    \App\Controller\LogController::Log($user, 'Account', 'deleted avatar');

    return Response::json(array('result' => 'success'));
  }

  /**
   * Login as user
   */
  public function getLoginAsUser()
  {
    // Check if logged in user has permissions
    if(\Auth::user()->can('user_management'))
    {
      $sl = Input::get('sl');
      $sl = \App\Core\Secure::string2array($sl);

      \Auth::loginUsingId($sl['user_id']);
      return \Redirect::to('/platform');
    }
    else
    {
      die('No permission');
    }
  }

  /**
   * Delete user
   */
  public function getDeleteUser()
  {
    $sl = Input::get('data');
    $sl = \App\Core\Secure::string2array($sl);

    $user = \User::find($sl['user_id']);

    // Check if user has right permissions
    if($user->parent_id != NULL)
    {
      $user->avatar = STAPLER_NULL;
      $user->forceDelete();
    }

    // Log
    \App\Controller\LogController::Log($user, 'Account', 'deleted account');

    return Response::json(array('result' => 'success'));
  }

  /**
   * Update "My account"
   */
  public function postSave()
  {
    $social_login = (Auth::user()->twitter == '' && Auth::user()->facebook == '') ? false : true;

    $input = array(
      'first_name' => Input::get('first_name'),
      'last_name' => Input::get('last_name'),
      'timezone' => Input::get('timezone'),
      'email' => Input::get('email'),
      'new_password' => Input::get('new_password'),
      'current_password' => Input::get('current_password')
    );

    $current_password = ($social_login) ? '' : 'required';

    $rules = array(
      'email' => 'required|email|unique:users,email,' . Auth::user()->id,
      'new_password' => 'min:5|max:20',
      'timezone' => 'required',
      'current_password' => $current_password
    );

    $validator = \Validator::make($input, $rules);

    if($validator->fails())
    {
      $response = array(
        'result' => 'error', 
        'result_msg' => $validator->messages()->first()
      );
    }
    else
    {
      // Check password
      if (! $social_login)
      {
        if(! \Hash::check($input['current_password'], Auth::user()->password))
        {
          return Response::json(array(
            'result' => 'error', 
            'result_msg' => trans('global.password_not_correct')
          ));
        }
      }

      $user = \User::find(Auth::user()->id);

      $user->first_name = $input['first_name'];
      $user->last_name = $input['last_name'];
      $user->email = $input['email'];
      $user->timezone = $input['timezone'];
      if($input['new_password'] != '' && \Config::get('app.demo', false) === false)
      {
        $user->password_confirmation = $input['new_password'];
        $user->password = $input['new_password'];
      }

      if($user->save())
      {
        $response = array(
          'result' => 'success', 
          'result_msg' => trans('global.changes_saved')
        );

        // Log
        \App\Controller\LogController::Log($user, 'Account', 'updated account');
      }
      else
      {
        $response = array(
          'result' => 'error', 
          'result_msg' => $user->errors()->first()
        );
      }
    }
    return Response::json($response);
  }

  /**
   * Save new user
   */
  public function postUserNew()
  {
    $input = array(
      'role_id' => Input::get('role', 3),
      'username' => Input::get('username'),
      'email' => Input::get('email'),
      'password' => Input::get('password'),
      'confirmed' => Input::get('confirmed', 0),
      'first_name' => Input::get('first_name'),
      'last_name' => Input::get('last_name'),
      'timezone' => Input::get('timezone'),
      'language' => Input::get('language'),
      'send_mail' => Input::get('send_mail', 0)
    );

    $rules = array(
      'username' => 'alpha_dash|required|between:4,20|unique:users,username',
      'email' => 'required|email|unique:users,email',
      'password' => 'required|between:5,20',
      'timezone' => 'required',
      'language' => 'required'
    );

    $validator = \Validator::make($input, $rules);

    if($validator->fails())
    {
      $response = array(
        'result' => 'error', 
        'result_msg' => $validator->messages()->first()
      );
    }
    else
    {
      $reseller = \App\Controller\ResellerController::get();

      $user = new \User;

      $user->reseller_id = $reseller->id;
      $user->parent_id = $this->parent_user_id;
      $user->username = $input['username'];
      $user->email = $input['email'];
      $user->password = $input['password'];
      $user->password_confirmation = $input['password'];
      $user->confirmed = $input['confirmed'];
      $user->timezone = $input['timezone'];
      $user->language = $input['language'];
      $user->first_name = $input['first_name'];
      $user->last_name = $input['last_name'];

      if($user->save())
      {
        // Set role
        $user->attachRole($input['role_id']);

        // Send mail with login information
        $name = ($user->first_name != '' || $user->last_name != '') ? $user->first_name . ' ' . $user->last_name : $user->username;

        if($input['send_mail'] == 1)
        {
          // Change language to user's language for mail
          $language = \App::getLocale();
          \App::setLocale($input['language']);

          $data = array(
            'username' => $input['username'],
            'name' => $name,
            'email' => $input['email'],
            'password' => $input['password']
          );

          \Mail::send('emails.auth.accountcreated', $data, function($message) use($data)
          {
            $message->to($data['email'], $data['name'])->subject(trans('confide.email.account_created.subject'));
          });

          // ... And change language back
          \App::setLocale($language);
        }

        $username = $name . ' [' . $user->email . ']';

        // Log
        \App\Controller\LogController::Log(Auth::user(), 'Account', 'created new account - ' . $username . '');

        $response = array(
          'result' => 'success', 
          'result_msg' => trans('global.new_user_created')
        );
      }
      else
      {
        $response = array(
          'result' => 'error', 
          'result_msg' => $user->errors()->first()
        );
      }
    }
    return Response::json($response);
  }

  /**
   * Update existing user
   */
  public function postUserUpdate()
  {
    $sl = Input::get('sl');
    $sl = \App\Core\Secure::string2array($sl);

    if(! is_numeric($sl['user_id'])) return 'Encryption Error.';

    $input = array(
      'role_id' => Input::get('role', 3),
      'email' => Input::get('email'),
      'first_name' => Input::get('first_name'),
      'last_name' => Input::get('last_name'),
      'timezone' => Input::get('timezone'),
      'confirmed' => Input::get('confirmed', 1),
      'new_password' => Input::get('new_password'),
      'current_password' => Input::get('current_password'),
      'app_permissions' => Input::get('app_permissions', array())
    );

    $rules = array(
      'email' => 'required|email|unique:users,email,' . $sl['user_id'],
      'new_password' => 'between:5,20',
      'timezone' => 'required',
      'current_password' => 'required'
    );

    $validator = \Validator::make($input, $rules);

    if($validator->fails())
    {
      $response = array(
        'result' => 'error', 
        'result_msg' => $validator->messages()->first()
      );
    }
    else
    {
      // Check password
      if(! \Hash::check($input['current_password'], Auth::user()->password))
      {
        return Response::json(array(
          'result' => 'error', 
          'result_msg' => trans('global.password_not_correct')
        ));
      }

      $user = \User::find($sl['user_id']);

      $user->first_name = $input['first_name'];
      $user->last_name = $input['last_name'];
      $user->email = $input['email'];
      $user->timezone = $input['timezone'];
      $user->confirmed = $input['confirmed'];
      $user->settings = \App\Core\Settings::json(array('app_permissions' => $input['app_permissions']), $user->settings);
      if($input['new_password'] != '')
      {
        $user->password_confirmation = $input['new_password'];
        $user->password = $input['new_password'];
      }

      if($user->parent_id != NULL)
      {
        // Update role, first detach existing
        $user->roles()->detach();
        $user->attachRole($input['role_id']);
      }

      if($user->save())
      {
        $response = array(
          'result' => 'success', 
          'result_msg' => trans('global.changes_saved')
        );

        $username = ($user->first_name != '' || $user->last_name != '') ? $user->first_name . ' ' . $user->last_name : $user->username;
        $username = $username . ' [' . $user->email . ']';

        // Log
        \App\Controller\LogController::Log(Auth::user(), 'Account', 'updated account ' . $username);
      }
      else
      {
        $response = array(
          'result' => 'error', 
          'result_msg' => $user->errors()->first()
        );
      }
    }
    return Response::json($response);
  }

  /**
   * Reset system
   */
  public function postResetSystem()
  {
    if(Auth::user()->can('system_management') && Auth::user()->id == 1)
    {
      \App\Controller\InstallationController::clean();
    }
    return Response::json(array('result' => 'success'));
  }
}