<?php
/*
 |--------------------------------------------------------------------------
 | Installation check (database, permissions)
 |--------------------------------------------------------------------------
 */

\App\Controller\InstallationController::check();

/*
 |--------------------------------------------------------------------------
 | CORS
 |--------------------------------------------------------------------------
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

/*
 |--------------------------------------------------------------------------
 | Language
 |--------------------------------------------------------------------------
 */

$app_language = \App\Controller\AccountController::appLanguage();
App::setLocale($app_language);

/*
 |--------------------------------------------------------------------------
 | Globals
 |--------------------------------------------------------------------------
 */

$url_parts = parse_url(URL::current());

/*
 |--------------------------------------------------------------------------
 | Check for reseller or custom domain
 |--------------------------------------------------------------------------
 */

$reseller = \App\Controller\ResellerController::get();

if ($reseller !== false)
{
	$domain = str_replace('www.', '', $url_parts['host']);

	$custom_site = \Web\Model\Site::where('domain', $domain)
		->orWhere('domain', 'www.' . $domain)
		->first();
}
else
{
	$custom_site = array();
}

/*
 |--------------------------------------------------------------------------
 | Front end website
 |--------------------------------------------------------------------------
 */

Route::get('/', function() use($url_parts, $custom_site, $reseller)
{
	if ($reseller === false && count($custom_site) == 0)
	{
		return \Response::view('app.errors.reseller-404', [], 404);
	}
    elseif (count($custom_site) > 0)
    {
      // Naked or www domain?
      if (substr($custom_site->domain, 0, 4) == 'www.' && substr($url_parts['host'], 0, 4) != 'www.')
      {
        return \Redirect::to($url_parts['scheme'] . '://' . $custom_site->domain, 301);
      } 
      elseif (substr($custom_site->domain, 0, 4) != 'www.' && substr($url_parts['host'], 0, 4) == 'www.')
      {
        return \Redirect::to($url_parts['scheme'] . '://' . $custom_site->domain, 301);
      }

      // Site
      $language = \App\Controller\AccountController::siteLanguage($custom_site);
      App::setLocale($language);

      return App::make('\Web\Controller\SiteController')->showSite($custom_site->local_domain);
    }
	else
	{
		// Public facing website
		if (\Config::get('system.show_homepage')) 
		{
			return App::make('\App\Controller\WebsiteController')->showWebsite($url_parts);
		}
		else
		{
			return \Redirect::to('platform');
		}
	}
});

/*
 |--------------------------------------------------------------------------
 | API
 |--------------------------------------------------------------------------
 */

Route::group(array('prefix' => 'api/v1'), function()
{
    Route::controller('admin',                   'App\Controller\AdminController');
    Route::controller('campaign',                'Campaign\Controller\CampaignController');
    Route::controller('account',                 'App\Controller\AccountController');
    Route::controller('help',                    'App\Core\Help');
    Route::controller('thumb',                   'App\Core\Thumb');
    Route::controller('oauth',                   'App\Controller\oAuthController');
    Route::controller('website',                 'App\Controller\WebsiteController');
    Route::controller('site',                    'Web\Controller\SiteController');
    Route::controller('site-edit',               'Web\Controller\SiteEditController');
    Route::controller('site-analytics',          'Analytics\Controller\WebAnalyticsController');
    Route::controller('lead',                    'Lead\Controller\LeadController');
    Route::controller('hook',                    'App\Controller\HookController');
    //Route::controller('translation',             'App\Controller\TranslationController');
});

/*
 |--------------------------------------------------------------------------
 | App
 |--------------------------------------------------------------------------
 */

// Dashboard
Route::get( '/platform',                             'App\Controller\DashboardController@getMainDashboard');
Route::get( '/app/dashboard',                        'App\Controller\DashboardController@getDashboard');
Route::get( '/app/javascript',                       'App\Controller\DashboardController@getAppJs');

// Web
Route::get( '/app/web',                              'Web\Controller\SiteController@getSites');
Route::get( '/app/site',                             'Web\Controller\SiteController@getSite');
Route::get( '/app/modal/web/qr',                     'Web\Controller\SiteController@getQrModal');
Route::get( '/app/modal/web/site-settings',          'Web\Controller\SiteController@getSiteSettingsModal');

// Web Analytics
Route::get( '/app/web/analytics',                    'Analytics\Controller\WebAnalyticsController@getWeb');

// Web preview, edit and view
Route::get( '/web/view/{theme_dir}',                 'Web\Controller\SiteController@getView');
Route::get( '/web/view/{type_dir}/{theme_dir}',      'Web\Controller\SiteController@previewTemplate');
Route::get( '/web/{local_domain}',                   'Web\Controller\SiteController@showSite');

// Inline site editor
Route::get( '/edit/site',                            'Web\Controller\SiteController@getSiteEditor');
Route::get( '/app/modal/web/form-editor',            'Web\Controller\SiteEditController@getFormEditModal');
Route::get( '/app/modal/web/iframe-editor',          'Web\Controller\SiteEditController@getIframeEditModal');
Route::get( '/app/modal/web/link-editor',            'Web\Controller\SiteEditController@getLinkEditModal');

// Leads
Route::get( '/app/leads',                            'Lead\Controller\LeadController@getLeads');
Route::get( '/app/lead',                             'Lead\Controller\LeadController@getLead');
Route::get( '/app/leads/leads-view',                 'Lead\Controller\LeadController@getLeadsViewModal');
Route::get( '/app/leads/leads-export',               'Lead\Controller\LeadController@getLeadsExportModal');
Route::post('/app/leads/export',                     'Lead\Controller\LeadController@postLeadsExport');

// Media
Route::get( '/app/media',                            'Media\Controller\MediaController@getBrowser');
Route::get( '/app/browser',                          'Media\Controller\MediaController@elFinder');
Route::get( '/app/editor',                           'Media\Controller\EditorController@getEditor');
Route::get( '/app/editor/templates',                 'Media\Controller\EditorController@getTemplates');
Route::get( '/app/editor/template/{tpl}',            'Media\Controller\EditorController@getTemplate');

// Profile, team and subscription
Route::get( '/app/profile',                          'App\Controller\AccountController@getProfile');
Route::post('/app/profile',                          'App\Controller\AccountController@postProfile');
Route::get( '/app/modal/avatar',                     'App\Controller\AccountController@getAvatarModal');
Route::get( '/app/users',                            'App\Controller\AccountController@getUsers');
Route::get( '/app/user',                             'App\Controller\AccountController@getUser');
Route::get( '/app/upgrade',                          'App\Controller\AccountController@getUpgrade');
Route::get( '/app/account',                          'App\Controller\AccountController@getAccount');
Route::get( '/app/order-subscription',               'App\Controller\AccountController@getOrderSubscription');
Route::get( '/app/order-subscription-confirm',       'App\Controller\AccountController@getOrderSubscriptionConfirm');
Route::get( '/app/order-subscription-confirmed',     'App\Controller\AccountController@getOrderSubscriptionConfirmed');
Route::get( '/app/modal/account/invoice',            'App\Controller\AccountController@getInvoiceModal');

// Third-party apps
Route::get( '/app/oauth',                            'App\Controller\oAuthController@getApps');

// Campaigns
Route::get( '/app/campaigns',                        'Campaign\Controller\CampaignController@getCampaigns');
Route::get( '/app/campaign',                         'Campaign\Controller\CampaignController@getCampaign');

// Log
Route::get( '/app/log',                              'App\Controller\LogController@getLog');

// Help
Route::get( '/app/help/{item}',                      'App\Core\Help@getHelp');

// Admin
Route::get( '/app/admin/users',                      'App\Controller\AdminController@getUsers');
Route::get( '/app/admin/user',                       'App\Controller\AdminController@getUser');
Route::get( '/app/admin/plans',                      'App\Controller\AdminController@getPlans');
Route::get( '/app/admin/plan',                       'App\Controller\AdminController@getPlan');
Route::get( '/app/admin/website',                    'App\Controller\AdminController@getWebsite');
Route::get( '/app/admin/modal/website-settings',     'App\Controller\AdminController@getWebsiteSettingsModal');
Route::get( '/app/admin/purchases',                  'App\Controller\AdminController@getPurchases');
Route::get( '/app/admin/cms',                        'App\Controller\AdminController@getCms');

// Demo
Route::get( '/reset/{key}',                          'App\Controller\InstallationController@reset');

// Update
Route::get( '/update',                               'App\Controller\InstallationController@update');
Route::get( '/update/now',                           'App\Controller\InstallationController@doUpdate');

/*
 |--------------------------------------------------------------------------
 | Confide routes / authorization
 |--------------------------------------------------------------------------
 */

if (\Config::get('system.allow_registration')) 
{
	Route::get( 'signup',                                'UsersController@create');
	Route::get( 'confirm/{code}',                        'UsersController@confirm');

	Route::group(array('before' => 'csrf'), function()
	{
		Route::post('signup',                                'UsersController@store');
	});
}

Route::get( 'login',                                 'UsersController@login');
Route::get( 'forgot_password',                       'UsersController@forgotPassword');
Route::get( 'reset_password/{token}',                'UsersController@resetPassword');
Route::get( 'logout',                                'UsersController@logout');

Route::group(array('before' => 'csrf'), function()
{
	Route::post('login',                                 'UsersController@doLogin');
	Route::post('forgot_password',                       'UsersController@doForgotPassword');
	Route::post('reset_password',                        'UsersController@doResetPassword');
});

/*
 |--------------------------------------------------------------------------
 | ElFinder File browser
 |--------------------------------------------------------------------------
 */

if(isset($url_parts['path']) && strpos($url_parts['path'], '/elfinder') !== false)
{
    Route::group(array('before' => 'auth'), function()
    {
        if(Auth::check())
        {
            // Set Root dir
            if(Auth::user()->parent_id == NULL)
            {
                $root_dir = \App\Core\Secure::staticHash(Auth::user()->id);
            }
            else
            {
                // Check if user has admin access to media
                if(\Auth::user()->can('user_management'))
                {
                    $root_dir = \App\Core\Secure::staticHash(Auth::user()->parent_id);
                }
                else
                {
                    $Punycode = new Punycode();
                    $user_dir = $Punycode->encode(Auth::user()->username);
                    $root_dir = \App\Core\Secure::staticHash(Auth::user()->parent_id) . '/' . $user_dir;
                }
            }

            $root_dir_full = public_path() . '/uploads/user/' . $root_dir;

            $root = substr(url('/'), strpos(url('/'), \Request::server('HTTP_HOST')));
            $abs_path_prefix = str_replace(\Request::server('HTTP_HOST'), '', $root);

            if(! File::isDirectory($root_dir_full))
            {
                File::makeDirectory($root_dir_full, 0775, true);
            }

			if (\Config::get('s3.active', false))
			{
				$client = Aws\S3\S3Client::factory([
					'key'    => \Config::get('s3.key'),
					'secret' => \Config::get('s3.secret'),
					'region' => \Config::get('s3.region'),
					'version' => 'latest',
					'ACL' => 'public-read',
					'http'    => [
						'verify' => base_path() . '/cacert.pem'
					]
				]);

				$adapter = new League\Flysystem\AwsS3v2\AwsS3Adapter($client, \Config::get('s3.media_root_bucket'), null, array('ACL' => 'public-read'));

				// Create root dir if not exists
				$filesystem = new \League\Flysystem\Filesystem($adapter);
				$filesystem->createDir($root_dir);
			}
			elseif (\Config::get('ftp.active', false))
			{
				$adapter = new \League\Flysystem\Adapter\Ftp(
					[
						'host' => \Config::get('ftp.host'),
						'username' => \Config::get('ftp.username'),
						'password' => \Config::get('ftp.password'),
						'root' => \Config::get('ftp.root'),
						'port' => \Config::get('ftp.port'),
						'mode' => \Config::get('ftp.mode')
					]
				);
			}

			if (\Config::get('s3.active', false))
			{
				$roots = array(
					array(
						'driver'        => 'Flysystem',
						'path'          => $root_dir,
						'filesystem'    => new \League\Flysystem\Filesystem($adapter),
						'URL'           => \Config::get('s3.url') . '/' . \Config::get('s3.media_root_bucket') . '/' . $root_dir,
						'alias'         => trans('global.my_files'),
						'accessControl' => 'Barryvdh\Elfinder\Elfinder::checkAccess',
						'alias'         => trans('global.my_files'),
						'tmpPath'       => $root_dir_full,
						'tmbPath'       => $root_dir_full . '/.tmb',
						'tmbURL'        => url('/uploads/user/' . $root_dir . '/.tmb'),
						'tmbSize'       => '100',
						'tmbCrop'       => false,
						'icon'          => url('packages/elfinder/img/volume_icon_local.png')
					)
				);
			}
			elseif (\Config::get('ftp.active', false))
			{
				
			}
			else
			{
				$roots = array(
					array(
						'driver'        => 'LocalFileSystem',
						'path'          => public_path() . '/uploads/user/' . $root_dir,
						'URL'           => $abs_path_prefix . '/uploads/user/' . $root_dir,
						'accessControl' => 'access',
						'tmpPath'       => public_path() . '/uploads/user/' . $root_dir,
                   		'uploadMaxSize' => '4M',
						'tmbSize'       => '100',
						'tmbCrop'       => false,
						'icon'          => url('packages/elfinder/img/volume_icon_local.png'),
						'alias'         => trans('global.my_files'),
						'uploadDeny'    => array('text/x-php'),
						'attributes' => array(
							array(
							  'pattern' => '/.tmb/',
							   'read' => false,
							   'write' => false,
							   'hidden' => true,
							   'locked' => false
							),
							array(
							  'pattern' => '/.quarantine/',
							   'read' => false,
							   'write' => false,
							   'hidden' => true,
							   'locked' => false
							),
							array( // hide readmes
								'pattern' => '/\.(txt|html|php|py|pl|sh|xml)$/i',
								'read'   => false,
								'write'  => false,
								'locked' => true,
								'hidden' => true
							)
						)
					),
					array(
						'driver'        => 'LocalFileSystem',
						'path'          => public_path() . '/stock',
						'URL'           => '/stock',
						'defaults'       => array('read' => false, 'write' => false),
						'alias'         => trans('global.stock'),
						'tmbSize'       => '100',
						'tmbCrop'       => false,
						'icon'          => '/packages/elfinder/img/volume_icon_image.png',
						'attributes' => array(
							array(
								'pattern' => '!^.!',
								'hidden'  => false,
								'read'    => true,
								'write'   => false,
								'locked'  => true
							),
							array(
							  'pattern' => '/.tmb/',
							   'read' => false,
							   'write' => false,
							   'hidden' => true,
							   'locked' => false
							),
							array(
							  'pattern' => '/.quarantine/',
							   'read' => false,
							   'write' => false,
							   'hidden' => true,
							   'locked' => false
							)
						)
					)
				);
			}

          \Config::set('laravel-elfinder::roots', $roots);

          \Route::get('elfinder/ckeditor4', '\Media\Controller\MediaController@ckEditor');
          \Route::get('elfinder/tinymce', 'Media\Controller\MediaController@showTinyMCE');
          \Route::get('elfinder/standalonepopup/{input_id}/{callback?}', '\Media\Controller\MediaController@popUp');
          \Route::any('elfinder/connector', 'Barryvdh\Elfinder\ElfinderController@showConnector');
        }
    });
}

/*
 |--------------------------------------------------------------------------
 | 404
 |--------------------------------------------------------------------------
 */

App::missing(function($exception) use($url_parts)
{

	/*
	 |--------------------------------------------------------------------------
	 | Public facing website, 404's are managed at the template controller
	 |--------------------------------------------------------------------------
	 */

	return App::make('\App\Controller\WebsiteController')->showWebsite($url_parts);
});