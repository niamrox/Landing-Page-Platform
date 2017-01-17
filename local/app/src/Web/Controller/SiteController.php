<?php
namespace Web\Controller;

use View;

/*
|--------------------------------------------------------------------------
| Site controller
|--------------------------------------------------------------------------
|
| Site related logic
|
*/

class SiteController extends \BaseController {

  /**
   * Construct
   */
  public function __construct()
  {
    if(\Auth::check())
    {
      $this->parent_user_id = (\Auth::user()->parent_id == NULL) ? \Auth::user()->id : \Auth::user()->parent_id;
    }
    else
    {
      $this->parent_user_id = NULL;
    }
  }

  /**
   * Get global global CSS
   */
  public function getGlobalCss($hash, $version = NULL)
  {
    if($hash != '')
    {
      $site_id = \App\Core\Secure::staticHashDecode($hash);
      $site = \Web\Model\Site::where('id', $site_id)->first();

      if($site)
      {
        if($version == 'edit')
        {
          $css = (isset($site->settings->css)) ? $site->settings->css : '';
        }
        else
        {
          $css = (isset($site->settings_published->css)) ? $site->settings_published->css : '';
        }

        $response = \Response::make($css, 200);
        $response->header('Content-Type', 'text/css');
        return $response;
      }
    }
  }

  /**
   * Get global global JavaScript
   */
  public function getGlobalJs($hash, $version = NULL)
  {
    if($hash != '')
    {
      $return = '';
      $site_id = \App\Core\Secure::staticHashDecode($hash);
      $site = \Web\Model\Site::where('id', $site_id)->first();

      // Global vars
      /*
      $event_submitted_title = (isset($site->settings->event_submitted_title)) ? $site->settings->event_submitted_title : trans('global.event_submitted_title');
      $event_submitted_msg = (isset($site->settings->event_submitted_msg)) ? $site->settings->event_submitted_msg : trans('global.event_submitted_msg');
      $event_submitted_btn = (isset($site->settings->event_submitted_btn)) ? $site->settings->event_submitted_btn : trans('global.event_submitted_btn');

      $return .= 'var event_submitted_title = "' . str_replace(chr(10), '\\n', str_replace(chr(13), '\\n', str_replace('"', '&quot;', $event_submitted_title))) . '";';
      $return .= 'var event_submitted_msg = "' . str_replace(chr(10), '\\n', str_replace(chr(13), '\\n', str_replace('"', '&quot;', $event_submitted_msg))) . '";';
      $return .= 'var event_submitted_btn = "' . str_replace(chr(10), '\\n', str_replace(chr(13), '\\n', str_replace('"', '&quot;', $event_submitted_btn))) . '";';
      */

      if($site)
      {
        if($version == 'edit')
        {
          $return .= (isset($site->settings->js)) ? $site->settings->js : '';
        }
        else
        {
          $return .= (isset($site->settings_published->js)) ? $site->settings_published->js : '';
        }
        $response = \Response::make($return, 200);
        $response->header('Content-Type', 'text/javascript');
        return $response;
      }
    }
  }

  /**
   * Show all sites
   */
  public function getSites()
  {
    $sites = \Web\Model\Site::where('user_id', '=', $this->parent_user_id)->orderBy('name', 'asc')->get();
    $campaigns = \Campaign\Model\Campaign::where('user_id', '=', $this->parent_user_id)->orderBy('name', 'asc')->get();

    return View::make('app.web.sites', array(
      'sites' => $sites,
      'campaigns' => $campaigns
    ));
  }

  /**
   * Preview template
   */
   /*
  public function previewTemplate($type_dir, $template_dir)
  {
    return View::make('user.site.main', array(
      'preview' => true,
      'edit' => false,
      'template_dir' => $template_dir,
      'type_dir' => $type_dir,
      'site' => NULL
    ));
  }
*/
  /**
   * View template
   */
  public function getView($template)
  {
    if($template != '')
    {
      $template = \Web\Controller\TemplateController::loadTemplateConfig($template);

      return View::make('user.site.main', array(
        'preview' => true,
        'edit' => false,
        'template' => $template
      ));
    }
  }

  /**
   * Show lead page with or without inline editor
   */
  public function showSite($local_domain)
  {
    // Get site
       $site = \Web\Model\Site::where('local_domain', '=', $local_domain)->first();

    if(empty($site))
    {
      return \Redirect::to('/');
    }

    // Edit or view
    $published = (\Request::get('published', false) === false) ? false : true;
    $edit = false;
    $user = false;

    if(\Auth::check() && ! $published)
    {
      $user = \Auth::user();
      if(\Auth::user()->id == $site->user_id || \Auth::user()->parent_id == $site->user_id)
      {
        $edit = true;
      }
    }
    else
    {
      // To do: redirect local domain to domain if it exists OR show 'Looks Like This Domain Isn’t Connected To A Website Yet'
     }

    if(! $edit)
    {
      $app_language = \App\Controller\AccountController::siteLanguage($site);
      \App::setLocale($app_language);
    }

    return View::make('user.site.main', array(
      'preview' => false,
      'published' => $published,
      'edit' => $edit,
      'template' => $site->template,
      'site' => $site,
      'user' => $user
    ));
  }

  /**
   * Show new or edit site template
   */
  public function getSite()
  {
    $sl = \Request::input('sl', '');

    if($sl != '')
    {
      $qs = \App\Core\Secure::string2array($sl);
     $site = \Web\Model\Site::where('id', '=', $qs['site_id'])->where('user_id', '=', $this->parent_user_id)->first();
     $campaigns = \Campaign\Model\Campaign::where('user_id', '=', $this->parent_user_id)->orderBy('name', 'asc')->get();

      if (\Auth::user()->parent_id != '')
      {
        $parent_user = \User::where('id', '=', \Auth::user()->parent_id)->first();
        $plan_settings = $parent_user->plan->settings;
      }
      else
      {
        $plan_settings = \Auth::user()->plan->settings;
      }

      $plan_settings = json_decode($plan_settings);
      $plan_widgets = (isset($plan_settings->widgets)) ? $plan_settings->widgets : array();
      if (! isset(\Auth::user()->plan->settings) || \Auth::user()->plan->settings == '') $plan_widgets = false;

      // Site limit
      $plan_max_sites = (isset($plan_settings->max_sites)) ? $plan_settings->max_sites : 0;
         $sites = \Web\Model\Site::where('user_id', '=', $this->parent_user_id)->count();

      $site_limit = ($plan_max_sites != 0 && $sites >= (int) $plan_max_sites) ? true : false;

      $download = (isset($plan_settings->download)) ? (boolean) $plan_settings->download : true;

      // Can user publish?
      $publish = (isset($plan_settings->publish)) ? (boolean) $plan_settings->publish : true;

      return View::make('app.web.site-edit', array(
        'sl' => $sl,
        'site' => $site,
        'campaigns' => $campaigns,
        'site_limit' => $site_limit,
        'download' => $download,
        'publish' => $publish
      ));
    }
    else
    {
      // Max sites
      if (\Auth::user()->parent_id != '')
      {
        $parent_user = \User::where('id', '=', \Auth::user()->parent_id)->first();
        $plan_settings = $parent_user->plan->settings;
      }
      else
      {
        $plan_settings = \Auth::user()->plan->settings;
      }

      $plan_settings = json_decode($plan_settings);
      $plan_max_sites = (isset($plan_settings->max_sites)) ? $plan_settings->max_sites : 0;

      // Current sites
         $sites = \Web\Model\Site::where('user_id', '=', $this->parent_user_id)->count();

      if($plan_max_sites != 0 && $sites >= (int) $plan_max_sites)
      {
        return View::make('app.auth.upgrade');
      }

      // Site types
      $site_types = \Web\Model\SiteType::orderBy('sort', 'asc')->where('active', '=', true)->remember(\Config::get('cache.ttl'), 'global_site_types')->get();
         $campaigns = \Campaign\Model\Campaign::where('user_id', '=', $this->parent_user_id)->orderBy('name', 'asc')->get();
      $site_templates = \Web\Controller\TemplateController::loadAllTemplateConfig();

      return View::make('app.web.site-new', array(
        'site_types' => $site_types,
        'campaigns' => $campaigns,
        'site_templates' => $site_templates
      ));
    }
  }

  /**
   * Save new Site
   */
  public function postNew()
  {
    $name = \Request::get('name');
    $campaign = \Request::get('campaign', NULL);
    $timezone = \Request::get('timezone', 'UTC');
    $language = \Request::get('language', 'en');
    $site_type_id = \Request::get('site_type_id');
    $site_template = \Request::get('site_template');

    $site = new \Web\Model\Site;
    $site->user_id = $this->parent_user_id;
    $site->site_type_id = $site_type_id;
    $site->template = $site_template;
    $site->name = $name;
    $site->timezone = $timezone;
    $site->language = $language;

    // Generate Site key
    $keyauth = new \App\Core\KeyAuth;
    $keyauth->key_unique = TRUE;
    $keyauth->key_store = TRUE;
    $keyauth->key_chunk = 4;
    $keyauth->key_part = 4;
    $keyauth->key_pre = "";

    $key = $keyauth->generate_key();

    $site->local_domain = $key;

    if($campaign != NULL)
    {
      $campaign = json_decode($campaign);
      if($campaign->id > 0)
      {
        // Campaign already exists, just set campaign_id
        $site->campaign_id = $campaign->id;
      }
      else
      {
        // Campaign doesn't exist yet, add it first
        $new_campaign = new \Campaign\Model\Campaign;
        $new_campaign->user_id = $this->parent_user_id;
        $new_campaign->name = $campaign->text;
        if($new_campaign->save())
        {
          $site->campaign_id = $new_campaign->id;
        }
      }
    }

    // Create Piwik analytics
    if (\Config::get('piwik.url', '') != '')
    {
      $domain = url('/web/' . $key);
      $domain = str_replace('http://', '', str_replace('https://', '', $domain));

      $site->piwik_site_id = \App\Core\Piwik::checkUserAddSite($domain, $timezone);
    }

    if($site->save())
    {
      // Get default content
      $template = \Web\Controller\TemplateController::loadTemplateConfig($site_template);

      // Create homepage
      $homepage = new \Web\Model\SitePage;
      $homepage->site_id = $site->id;
      $homepage->name = $name;
         $homepage->meta_title = $name;
      $homepage->content = utf8_encode($template['json']);
      $homepage->slug = '/';
      $homepage->save();

      $sl = \App\Core\Secure::array2string(array('site_id' => $site->id));

      $response = array(
        'result' => 'success',
        'sl' => $sl
      );
    }
    else
    {
      $response = array(
        'result' => 'error', 
        'result_msg' => $beacon->errors()->first()
      );
    }

    return \Response::json($response);
  }

  /**
   * Delete site
   */
  public function getDelete()
  {
    $sl = \Request::input('data', '');
    $qs = \App\Core\Secure::string2array($sl);
       $site = \Web\Model\Site::where('id', '=', $qs['site_id'])->where('user_id', '=', $this->parent_user_id)->first();

    if(! is_null($site))
    {
      // Delete Piwik site
      if (\Config::get('piwik.url', '') != '' && ! \Config::get('app.demo', false))
      {
        \App\Core\Piwik::deleteSite($site->piwik_site_id);
      }

      $site->forceDelete();
    }

    return \Response::json(array('result' => 'success'));
  }

  /**
   * Show QR modal
   */
  public function getQrModal()
  {
    return View::make('app.web.modal.qr');
  }

  /**
   * Show site settings modal
   */
  public function getSiteSettingsModal()
  {
    $sl = \Request::input('sl', '');
    $qs = \App\Core\Secure::string2array($sl);
       $site = \Web\Model\Site::where('id', '=', $qs['site_id'])->where('user_id', '=', $this->parent_user_id)->first();
       $page = \Web\Model\SitePage::where('site_id', '=', $qs['site_id'])->first();


    if (\Auth::user()->parent_id != '')
    {
      $parent_user = \User::where('id', '=', \Auth::user()->parent_id)->first();
      $plan_settings = $parent_user->plan->settings;
    }
    else
    {
      $plan_settings = \Auth::user()->plan->settings;
    }

    $plan_settings = json_decode($plan_settings);

    // Can user update domain?
    $domain = (isset($plan_settings->domain)) ? (boolean) $plan_settings->domain : true;

    return View::make('app.web.modal.site-settings', array(
      'site' => $site,
      'page' => $page,
      'sl' => $sl,
      'domain' => $domain
    ));
  }

  /**
   * Save site settings modal
   */
  public function postSiteSettings()
  {
    $sl = \Request::input('sl', '');
    $qs = \App\Core\Secure::string2array($sl);
       $site = \Web\Model\Site::where('id', '=', $qs['site_id'])->where('user_id', '=', $this->parent_user_id)->first();
       $page = \Web\Model\SitePage::where('site_id', '=', $qs['site_id'])->first();

    if(count($site) > 0)
    {
      $local_domain = \Request::get('local_domain', '');

      // Validate local domain
      $input = array(
        'local_domain' => $local_domain
      );

      $rules = array(
        'local_domain'  => 'required|min:3|max:42|regex:/^[a-zA-Z0-9_-]+$/|unique:sites,local_domain,' . $qs['site_id']
      );
  
      $validator = \Validator::make($input, $rules);

      if($validator->fails())
      {
        return \Response::json(array(
          'result' => 'error', 
          'msg' => $validator->messages()->first()
        ));
        die();
      }

      $app_page_name = \Request::get('app_page_name', $page->meta_title);
      $app_meta_description = \Request::get('app_meta_description', $page->meta_desc);
      $app_meta_robots = \Request::get('app_meta_robots', $page->meta_robots);
      $name = \Request::get('name');
      $domain = \Request::get('domain');
      $timezone = \Request::get('timezone', 'UTC');
      $language = \Request::get('language', 'en');
      $head_tag = \Request::get('head_tag', '');
      $end_of_body_tag = \Request::get('end_of_body_tag', '');
      $css = \Request::get('css', '');
      $js = \Request::get('js', '');

      // Update Piwik timezone and/or domain?
      if($timezone != $site->timezone || $domain != $site->domain && \Config::get('piwik.url', '') != '')
      {
        if($domain != '')
        {
          $urls = $site->local_domain . ',' . $domain;
        }
        else
        {
          $urls = $site->local_domain;
        }
         \App\Core\Piwik::updateSite($site->piwik_site_id, $site->local_domain, $urls, $timezone);
      }

      // Prevent same domain update
      $url_parts = parse_url(\URL::current());
      if($domain != $url_parts['host']) $site->domain = $domain;

      $site->name = $name;
      $site->local_domain = $local_domain;
      $site->timezone = $timezone;
      $site->language = $language;

      // Settings
      $site->settings = array(
        'head_tag' => $head_tag,
        'end_of_body_tag' => $end_of_body_tag,
        'css' => $css,
        'js' => $js
      );

      $site->save();

      $page->meta_title = $app_page_name;
      $page->meta_desc = $app_meta_description;
      $page->meta_robots = $app_meta_robots;
      
      $page->save();
    }

    return \Response::json(array('result' => 'success', 'url' => url('web/' . $local_domain)));
  }
}
