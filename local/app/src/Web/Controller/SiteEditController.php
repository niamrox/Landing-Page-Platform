<?php
namespace Web\Controller;

use View;

/*
|--------------------------------------------------------------------------
| SiteEdit controller
|--------------------------------------------------------------------------
|
| Website editor related logic
|
*/

class SiteEditController extends \BaseController {

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
   * Save page
   */
  public function postPage()
  {
    $publish = (bool)\Request::get('publish', false);

    // Get security link with site_id, page_id
    $sl = \Request::get('sl', '');
    $qs = \App\Core\Secure::string2array($sl);

    $blocks = \Request::get('blocks', array());
    $text = \Request::get('text', array());
    $icons = \Request::get('icons', array());
    $images = \Request::get('images', array());
    $bg_images = \Request::get('bg_images', array());
    $color = \Request::get('color', array());
    $links = \Request::get('links', array());
    $video = \Request::get('video', array());
    $forms = \Request::get('forms', array());
    $iframes = \Request::get('iframes', array());

    $app_page_name = \Request::get('app_page_name', '');
    $app_meta_description = \Request::get('app_meta_description', '');
    $app_meta_robots = \Request::get('app_meta_robots', '');

    $site = \Web\Model\Site::where('id', '=', $qs['site_id'])
      ->where('user_id', '=', $this->parent_user_id)->first();

    $page = $site->sitePages()->where('id', '=', $qs['page_id'])->first();

    if(count($page) > 0)
    {
      $content = json_encode(array(
        'blocks' => $blocks,
        'text' => $text,
        'icons' => $icons,
        'images' => $images,
        'bg_images' => $bg_images,
        'color' => $color,
        'links' => $links,
        'video' => $video,
        'forms' => $forms,
        'iframes' => $iframes
      ));

      $page->content = $content;

      /*
      $page->meta_title = $app_page_name;
      $page->meta_desc = $app_meta_description;
      $page->meta_robots = $app_meta_robots;
      */

      if($publish)
      {
        $page->content_published = $content;
        $page->meta_title_published = $page->meta_title;
        $page->meta_desc_published = $page->meta_desc;
        $page->meta_robots_published = $page->meta_robots;

        $site->settings_published = array(
          'head_tag' => (isset($site->settings->head_tag)) ? $site->settings->head_tag : '',
          'end_of_body_tag' => (isset($site->settings->end_of_body_tag)) ? $site->settings->end_of_body_tag : '',
          'css' => (isset($site->settings->css)) ? $site->settings->css : '',
          'js' => (isset($site->settings->js)) ? $site->settings->js : ''
        );
        $site->save();
      }

      $page->save();

      // Delete thumb
      /*
      $thumb_filename = md5($site->domain()) . '.png';
      $thumb_path = public_path() . '/uploads/screens/' . $thumb_filename;
      \File::delete($thumb_path);
      */
    }

    return \Response::json(array('status' => 'success'));
  }

  /**
   * Unpublish page
   */
  public function postUnpublishPage()
  {
    // Get security link with site_id, page_id
    $sl = \Request::get('sl', '');
    $qs = \App\Core\Secure::string2array($sl);

    $site = \Web\Model\Site::where('id', '=', $qs['site_id'])
      ->where('user_id', '=', $this->parent_user_id)->first();

    $page = $site->sitePages()->where('id', '=', $qs['page_id'])->first();

    if(count($page) > 0)
    {
      $page->content_published = NULL;
      $page->name_published = NULL;
      $page->meta_desc_published = NULL;
      $page->meta_robots_published = NULL;
      $page->save();

      $site->settings_published = NULL;
      $site->save();

    }

    return \Response::json(array('status' => 'success'));
  }
  
  /**
   * Show Link Edit modal
   */
  public function getLinkEditModal()
  {
    return View::make('user.site.editor.link-edit');
  }
  
  /**
   * Show Iframe Edit modal
   */
  public function getIframeEditModal()
  {
    return View::make('user.site.editor.iframe-edit');
  }

  /**
   * Show Form Edit modal
   */
  public function getFormEditModal()
  {
    $sites = \Web\Model\Site::where('user_id', '=', $this->parent_user_id)->orderBy('name', 'asc')->get();
    $campaigns = \Campaign\Model\Campaign::where('user_id', '=', $this->parent_user_id)->orderBy('name', 'asc')->get();

    // Aweber connection
    $aweber_available = false;
    $aweber_lists = [];

    if (\Config::get('aweber.consumer_key', '') != '' && \Config::get('aweber.consumer_secret', '') != '') {

      $oAuth = \App\Model\oAuth::where('user_id', $this->parent_user_id)
        ->where('provider', 'aweber')->first();

      $aweber_available = (empty($oAuth)) ? false : true;

      // Get lists
      if ($aweber_available) {
        $aweber_lists = \App\Controller\oAuthController::getAweberLists();
      }
    }

    // GetResponse connection
    $getresponse_available = false;
    $getresponse_lists = [];

    if (\Config::get('getresponse.client_id', '') != '' && \Config::get('getresponse.client_secret', '') != '') {

      $oAuth = \App\Model\oAuth::where('user_id', $this->parent_user_id)
        ->where('provider', 'getresponse')->first();

      $getresponse_available = (empty($oAuth)) ? false : true;

      // Get lists
      if ($getresponse_available) {
        $getresponse_lists = \App\Controller\oAuthController::getGetResponseLists();
      }
    }

    // MailChimp connection
    $mailchimp_available = false;
    $mailchimp_lists = [];

    if (\Config::get('mailchimp.client_id', '') != '' && \Config::get('mailchimp.client_secret', '') != '') {

      $oAuth = \App\Model\oAuth::where('user_id', $this->parent_user_id)
        ->where('provider', 'mailchimp')->first();

      $mailchimp_available = (empty($oAuth)) ? false : true;

      // Get lists
      if ($mailchimp_available) {
        $mailchimp_lists = \App\Controller\oAuthController::getMailchimpLists();
      }
    }

    return View::make('user.site.editor.form-edit', array(
      'sites' => $sites,
      'campaigns' => $campaigns,
      'aweber_available' => $aweber_available,
      'aweber_lists' => $aweber_lists,
      'getresponse_available' => $getresponse_available,
      'getresponse_lists' => $getresponse_lists,
      'mailchimp_available' => $mailchimp_available,
      'mailchimp_lists' => $mailchimp_lists
    ));
  }

  /**
   * Update site title
   */
  public function postSiteTitle()
  {
    // Get security link with site_id, page_id
    $sl = \Request::get('pk', '');
    $qs = \App\Core\Secure::string2array($sl);

    $name = \Request::get('value', '');

    $site = \Web\Model\Site::where('id', '=', $qs['site_id'])
      ->where('user_id', '=', $this->parent_user_id)->first();

    if(! empty($site))
    {
      $site->name = $name;
      $site->save();
    }

    return \Response::json(array('status' => 'success'));
  }

  /**
   * Update site campaign
   */
  public function postSiteCampaign()
  {
    // Get security link with site_id, page_id
    $sl = \Request::get('pk', '');
    $qs = \App\Core\Secure::string2array($sl);

    $campaign_id = \Request::get('value', '');

    $site = \Web\Model\Site::where('id', '=', $qs['site_id'])
      ->where('user_id', '=', $this->parent_user_id)->first();

    if(! empty($site))
    {
      $site->campaign_id = $campaign_id;
      $site->save();
    }

    return \Response::json(array('status' => 'success'));
  }

  /**
   * Get block html
   */
  public function getBlockHtml()
  {
    $block_dir = \Request::get('block_dir');
    $block_name = \Request::get('block_name');

    $block_path = public_path() . '/blocks/' . $block_dir . '/' . $block_name . '.php';

    $html = '';

    if(\File::exists($block_path))
    {
      //$html = \File::get($block_path);
      $html = \App\Core\File::get_include_contents($block_path);
    }

    return \Response::json(array('html' => $html));
  }

  /**
   * Get icon picker
   */
  public function getIconPicker()
  {
    $current_icon = \Request::get('current_icon', '');
    $icons = \Config::get('iconsmind');
    $html = '';

    $html .= '<div class="app-ie" id="app-icon-picker" style="height:100%;">';

    $html .= '<nav class="navbar navbar-default" style="position:relative;top:0;width:100%; z-index:9999; margin-right:20px">
  <div class="container-fluid">
  <div class="navbar-header">
    <a class="navbar-brand" href="javascript:void(0);">' . trans('global.icons') . '</a>
  </div>
  <div class="collapse navbar-collapse">
    <form class="navbar-form navbar-left" role="search">
    <div class="form-group">
      <input type="text" class="form-control" placeholder="' . trans('global.search') . '" id="app-icon-search">
    </div>
    </form>
  </div>
  </div>
</nav>';

    $html .= '<div style="margin:-10px 0 0 6px;">';

    foreach($icons as $class => $name)
    {
      $sel = ($current_icon == $class) ? 'btn-primary' : 'btn-default';
      $html .= '<button type="button" class="btn app-pick-icon ' . $sel . '" data-icon="' . $class . '" title="' . $name . '"><div>' . $name . '</div><i class="' . $class . '"></i></button>';
    }

    $html .= '</div>';
    $html .= '</div>';

    $html .= '<script></script>';

    return $html;
  }

  /**
   * Save new template (owner only)
   */
  public function postTemplate()
  {
    if(\Auth::user()->can('system_management'))
    {
      $page_nodes = \Request::get('page_nodes', array());

      $admin_template_name = \Request::get('admin_template_name', '');
      $site_type_id = \Request::get('site_type_id', '');

      if($admin_template_name != '' && $site_type_id != '')
      {
        $content = json_encode(array(
          'blocks' => isset($page_nodes['blocks']) ? $page_nodes['blocks'] : NULL,
          'text' => isset($page_nodes['text']) ? $page_nodes['text'] : NULL,
          'icons' => isset($page_nodes['icons']) ? $page_nodes['icons'] : NULL,
          'images' => isset($page_nodes['images']) ? $page_nodes['images'] : NULL,
          'bg_images' => isset($page_nodes['bg_images']) ? $page_nodes['bg_images'] : NULL,
          'color' => isset($page_nodes['color']) ? $page_nodes['color'] : NULL,
          'links' => isset($page_nodes['links']) ? $page_nodes['links'] : NULL,
          'video' => isset($page_nodes['video']) ? $page_nodes['video'] : NULL,
          'forms' => isset($page_nodes['forms']) ? $page_nodes['forms'] : NULL,
          'iframes' => isset($page_nodes['iframes']) ? $page_nodes['iframes'] : NULL
        ));

        $site_template = new \Web\Model\SiteTemplate;
        $site_template->content = $content;
        $site_template->name = $admin_template_name;
        $site_template->site_type_id = $site_type_id;
        $site_template->save();

        // Create screenshot
        $thumb = \App\Core\Thumb::template($site_template->id, 0);
      }

      return \Response::json(array('status' => 'success'));
    }
  }

  /**
   * Editor JavaScript
   */
  public function getEditorJs()
  {
    $translation = \Lang::get('javascript-editor');

    $js = '_lang=[];';
    foreach($translation as $key => $val)
    {
      $js .= '_lang["' . $key . '"]="' . $val . '";';
    }

    $response = \Response::make($js);
    $response->header('Content-Type', 'application/javascript');

    return $response;
  }
}
