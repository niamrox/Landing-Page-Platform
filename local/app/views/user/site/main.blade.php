@extends('user.site.layouts.master')

@section('content')
<?php
/*
 |--------------------------------------------------------------------------
 | Defaults
 |--------------------------------------------------------------------------
 */

if(isset($site) && ! $preview)
{
  $page = $site->sitePages->first();
  $sl = \App\Core\Secure::array2string(array('user_id' => $site->user_id, 'language' => $site->language, 'site_id' => $site->id, 'page_id' => $page->id));

  if($published)
  {
    $name = $page->name_published;
    $content = $page->content_published;
    $page_title = $page->meta_title_published;
    $meta_desc = $page->meta_desc_published;
    $meta_robots = $page->meta_robots_published;
  }
  else
  {
    $name = $page->name;
    $content = $page->content;
    $page_title = $page->meta_title;
    $meta_desc = $page->meta_desc;
    $meta_robots = $page->meta_robots;
  }

  $language = $site->language;
  $template_dir = $site->template;
}
else
{
  $page_title = '';
  $sl = '';
  $content = '';
  $language = 'en';

  if($preview)
  {
    $page_title = trans('global.preview');
    $template_dir = $template['dir'];
    $content = utf8_encode($template['json']);
    $language = \App::getLocale();
  }
}

/*
 |--------------------------------------------------------------------------
 | Replace updated nodes
 |--------------------------------------------------------------------------
 */

if(isset($content))
{
  // Suppress libxml errors
  libxml_use_internal_errors(true);

  //Decode JSON string
  $content = json_decode($content, true);

  // Blocks
  $blocks = '';
  if(isset($content['blocks']))
  {
    foreach($content['blocks'] as $key => $val)
    {
      $id = $val['id'];
      $block_dir = $val['dir'];
      $block_name = $val['block'];

      $block_path = public_path() . '/blocks/' . $block_dir . '/' . $block_name . '.php';

      if(\File::exists($block_path))
      {
        $block = \App\Core\File::get_include_contents($block_path);

        if($edit)
        {
          $shortid = str_replace('block-', '', $id);
          $block = preg_replace('/<section(.*)>/', '<section$1 id="' . $id . '" data-dir="' . $block_dir . '" data-block="' . $block_name . '" data-name="' . $block_name . '-' . $shortid . '">', $block);
        }
        else
        {
          $block = preg_replace('/<section(.*)>/', '<section$1 id="' . $id . '" data-block="' . $block_name . '">', $block);
        }

        $blocks .= $block;
      }
    }
  }

  $css_extra = (\File::exists(public_path() . '/templates/' . $template_dir . '/assets/css/custom.css')) ? '<link rel="stylesheet" href="' . url('/templates/' . $template_dir . '/assets/css/custom.css') . '" />' : '';
  $css_extra .= ($preview) ? '<link rel="stylesheet" href="' . url('/assets/css/custom/template.preview.css') . '" />' : '';

  $html = '<!doctype html>
  <html lang="' . $language . '">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="' . url('/blocks/assets/css/bootstrap.min.css') . '" />
    <link rel="stylesheet" href="' . url('/blocks/assets/fonts/iconsmind/line/style.min.css') . '" />
    <link rel="stylesheet" href="' . url('/blocks/assets/css/base.min.css') . '" />
    ' . $css_extra . '
    <!--[if lt IE 9]>
      <script src="' . url('/blocks/assets/js/ie.min.js') . '"></script>
    <![endif]-->
  </head>
  <body><div class="modal fade" id="ajax-modal" data-backdrop="static" data-keyboard="true" tabindex="-1"></div>';

  if($edit) $html .= '<h2 id="no-blocks"><i class="fa fa-long-arrow-left"></i> ' . trans('global.no_blocks_msg') . '</h2>';
  if($edit) $html .= '<div id="blocks">';

  $html .= $blocks;

  if($edit) $html .= '</div>';

  $html .= '</body>
   </html>';

  //$dom = phpQuery::newDocumentHTML($html, $charset = 'utf-8');
  $dom = phpQuery::newDocumentHTML($html);
  phpQuery::selectDocument($dom);

  // Text
  if(isset($content['text']))
  {
    foreach($content['text'] as $key => $val)
    {
      if(isset($val['html']))
      {
        pq($val['path'])->html($val['html']);
      }
    }
  }

  // Images
  if(isset($content['images']))
  {
    foreach($content['images'] as $key => $val)
    {
      pq($val['path'])->attr('src', $val['src']);
    }
  }

  // Background images
  if(isset($content['bg_images']))
  {
    foreach($content['bg_images'] as $key => $val)
    {
      pq($val['path'])->attr('style', 'background-image:url("' . $val['url'] . '")');
    }
  }

  // Background color
  if(isset($content['color']))
  {
    foreach($content['color'] as $key => $val)
    {
      pq($val['path'])->attr('style', 'background-color:' . $val['rgba'] . '');
    }
  }

  // Links
  if(isset($content['links']))
  {
    foreach($content['links'] as $key => $val)
    {
      if(isset($val['settings']) && $val['settings'] != '')
      {
        $settings = json_decode($val['settings']);

        $href_only = (isset($settings->href_only)) ? $settings->href_only : false;

        if(isset($settings->type) && $settings->type == 'link')
        {
          if(! $href_only)
          {
            $href = (isset($settings->href ) && $settings->href  != '') ? $settings->href : '#';
            pq($val['path'])->attr('href', html_entity_decode($href));
          }
 
          if(isset($settings->html) && $settings->html != '')
          {
            pq($val['path'])->html($settings->html);
          }

          if(isset($val['settings']) && $val['settings'] != '' && $edit) pq($val['path'])->after('<textarea style="display:none" class="link-settings">' . $val['settings'] . '</textarea>');
        }

        if(isset($settings->type) && $settings->type == 'paypal')
        {
          // Default settings
          $classes = (isset($settings->classes)) ? $settings->classes : 'btn btn-default';
          $html = (isset($settings->html)) ? $settings->html : 'Button';
          $paypal_sandbox = (isset($settings->paypal_sandbox)) ? $settings->paypal_sandbox : 1;
          $paypal_email = (isset($settings->paypal_email)) ? $settings->paypal_email : 0;
          $paypal_item_name = (isset($settings->paypal_item_name)) ? $settings->paypal_item_name : '';
          $paypal_item_price = (isset($settings->paypal_item_price)) ? $settings->paypal_item_price : 0;
          $paypal_currency = (isset($settings->paypal_currency)) ? $settings->paypal_currency : 'USD';
          $paypal_tax_rate = (isset($settings->paypal_tax_rate)) ? $settings->paypal_tax_rate : 0;
          $paypal_shipping_cost = (isset($settings->paypal_shipping_cost)) ? $settings->paypal_shipping_cost : 0;

          $form_action = ($paypal_sandbox == '1') ? 'https://www.sandbox.paypal.com/cgi-bin/webscr': 'https://www.paypal.com/cgi-bin/webscr';

          $html = '<form action="' . $form_action . '" method="post" target="_top" class="link-editable">
          <input type="hidden" name="cmd" value="_xclick">
          <input type="hidden" name="business" value="' . $paypal_email . '">
          <input type="hidden" name="lc" value="US">
          <input type="hidden" name="item_name" value="' . $paypal_item_name . '">
          <input type="hidden" name="amount" value="' . $paypal_item_price . '">
          <input type="hidden" name="currency_code" value="' . $paypal_currency . '">
          <input type="hidden" name="button_subtype" value="services">
          <input type="hidden" name="no_note" value="0">
          <input type="hidden" name="tax_rate" value="' . $paypal_tax_rate . '">
          <input type="hidden" name="shipping" value="' . $paypal_shipping_cost . '">
          <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
          <button type="submit" class="' . $classes . '">' . $html . '</button>
          <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
          </form>';

          if(isset($val['settings']) && $val['settings'] != '' && $edit) pq($val['path'])->after('<textarea style="display:none" class="link-settings">' . $val['settings'] . '</textarea>');
          pq($val['path'])->replaceWith($html);
        }

        if(isset($settings->type) && $settings->type == '2checkout')
        {
          // Default settings
          $classes = (isset($settings->classes)) ? $settings->classes : 'btn btn-default';
          $html = (isset($settings->html)) ? $settings->html : 'Button';
          $checkout_sandbox = (isset($settings->checkout_sandbox)) ? $settings->checkout_sandbox : 1;

          $checkout_account = (isset($settings->checkout_account)) ? $settings->checkout_account : '';
          $checkout_currency = (isset($settings->checkout_currency)) ? $settings->checkout_currency : '';
          $checkout_item_id = (isset($settings->checkout_item_id)) ? $settings->checkout_item_id : '';
          $checkout_item_name = (isset($settings->checkout_item_name)) ? $settings->checkout_item_name : '';
          $checkout_item_description = (isset($settings->checkout_item_description)) ? $settings->checkout_item_description : '';
          $checkout_item_price = (isset($settings->checkout_item_price)) ? $settings->checkout_item_price : '';

          $form_action = ($checkout_sandbox == '1') ? 'https://sandbox.2checkout.com/checkout/purchase': 'https://www.2checkout.com/checkout/purchase';

          $html = '<form action="' . $form_action . '" method="post" target="_top" class="link-editable">
          <input type="hidden" name="mode" value="2CO" />
          <input type="hidden" name="li_0_type" value="product" />
          <input type="hidden" name="li_0_quantity" value="1">
          <input type="hidden" name="li_0_tangible" value="Y" />
          <input type="hidden" name="sid" value="' . $checkout_account . '">
          <input type="hidden" name="li_0_product_id" value="' . $checkout_item_id . '">
          <input type="hidden" name="li_0_name" value="' . $checkout_item_name . '">
          <input type="hidden" name="li_0_description" value="' . $checkout_item_description . '">
          <input type="hidden" name="li_0_price" value="' . $checkout_item_price . '">
          <input type="hidden" name="currency_code" value="' . $checkout_currency . '">
          <button type="submit" class="' . $classes . '">' . $html . '</button>
          </form>';

          if(isset($val['settings']) && $val['settings'] != '' && $edit) pq($val['path'])->after('<textarea style="display:none" class="link-settings">' . $val['settings'] . '</textarea>');
          pq($val['path'])->replaceWith($html);
        }

      }
    }
  }

  // Icons
  if(isset($content['icons']))
  {
    foreach($content['icons'] as $key => $val)
    {
      // Remove existing class
      $original_icon = pq($val['path'])->attr('data-icon');

      pq($val['path'])->attr('style', 'color:' . $val['color'] . '');
      pq($val['path'])->attr('data-icon', $val['icon']);
      pq($val['path'])->removeClass($original_icon);
      pq($val['path'])->addClass($val['icon']);
    }
  }

  // Forms
  if(isset($content['forms']))
  {
    foreach($content['forms'] as $key => $val)
    {
      $html = '';
      $html .= '<form method="post" class="form-editable">';
      $html .= $val['html'];
      $html .= '</form>';

      pq($val['path'])->html($html);
    }
  }

  // Iframes
  if(isset($content['iframes']))
  {

    foreach($content['iframes'] as $key => $val)
    {
      $src = html_entity_decode($val['src']);
      //$src = str_replace('&', '%26', $src);

      pq($val['path'])->attr('src', 'about:blank');
      pq($val['path'])->attr('data-src', $src);
      //pq($val['path'])->attr('src', html_entity_decode($val['src']));
      if(isset($val['settings']) && $val['settings'] != '' && $edit) pq($val['path'])->after('<textarea style="display:none" class="iframe-settings">' . $val['settings'] . '</textarea>');
    }
  }

}
else
{
  // No content
  $html = '<!doctype html>
  <html lang="' . $language . '">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="' . url('/blocks/assets/css/bootstrap.min.css') . '" />
    <link rel="stylesheet" href="' . url('/blocks/assets/fonts/iconsmind/line/style.min.css') . '" />
    <link rel="stylesheet" href="' . url('/blocks/assets/css/base.min.min.css') . '" />
    <!--[if lt IE 9]>
      <script src="' . url('/blocks/assets/js/ie.min.js') . '"></script>
    <![endif]-->
  </head>
  <body>
  <div id="blocks">';

  if($published)
  {
    echo '<div class="container text-center">';
    echo '<br><br><br><br>';
    echo '<h3>' . trans('global.no_site_published_yet') . '</h3>';
    echo '</div>';
  }

  $html .= '</div>';
  $html .= '</body></html>';

  //$dom = phpQuery::newDocumentHTML($html, $charset = 'utf-8');
  $dom = phpQuery::newDocumentHTML($html);
  phpQuery::selectDocument($dom);
}

/*
 |--------------------------------------------------------------------------
 | Page title & SEO tags
 |--------------------------------------------------------------------------
 */

if(isset($page))
{
  pq('title')->text($page_title);

  pq('head')->prepend('<meta name="description" content="' . $meta_desc . '">');

  $meta_robots = ($meta_robots != '') ? $meta_robots : 'index, follow';
  pq('head')->prepend('<meta name="robots" content="' . $meta_robots . '">');
}

if(isset($site))
{
  pq('html')->attr('lang', str_replace('_', '-', $site->language));
}

/*
 |--------------------------------------------------------------------------
 | Forms
 |--------------------------------------------------------------------------
 */
$forms = pq('form');

foreach($forms as $form)
{
  // First, check if form has class link-editable,
  // in that case it's a payment form and doesn't 
  // need to be rewritten for cms submissions.
  if(! pq($form)->hasClass('link-editable'))
  {
    pq($form)->addClass('app-form');

    if(! $edit)
    {
      // Add sl (security link) variable to identify from
      // which site the post came.
      pq($form)->prepend('<input type="hidden" name="sl" id="sl" value="' . $sl . '">');
    }

    if($preview || $edit)
    {
      // If preview or in edit mode, we don't need a real submission.
      pq($form)->attr('method', 'get');
      pq($form)->attr('action', '#');
    }
    else
    {
      // This form is published, give it the attributes we
      // need to use it.
      pq($form)->addClass('ajax');
      pq($form)->attr('method', 'post');
      pq($form)->attr('action', url('/api/v1/lead/form'));
    }
  }
}

/*
 |--------------------------------------------------------------------------
 | Add JS + CSS
 |--------------------------------------------------------------------------
 */

$qs_edit = ($edit) ? '/edit': '';

if(! $preview) pq('head')->append('<link href="' . url('/api/v1/site/global-css/' . \App\Core\Secure::staticHash($site->id)) . $qs_edit .'" rel="stylesheet" type="text/css">');
pq('body')->append('<script src="' . url('/api/v1/site-edit/editor-js') .'" type="text/javascript"></script>');

$app_edit = ($edit) ? 'true': 'false';
pq('body')->append('<script>var app_root = \'' . url('/') . '\';var app_lang = \'' . \App::getLocale() . '\';var sl = \'' . $sl . '\';var app_edit = ' . $app_edit . ';</script>');
pq('head')->append('<link href="' . url('/assets/css/site.global.css') . '" rel="stylesheet" type="text/css">');
pq('head')->append('<link href="' . url('/assets/css/custom/site.global.css') . '" rel="stylesheet" type="text/css">');
pq('body')->append('<script src="' . url('/assets/js/site.global.js') . '" type="text/javascript"></script>');
if(! $preview) pq('body')->append('<script src="' . url('/api/v1/site/global-js/' . \App\Core\Secure::staticHash($site->id)) . $qs_edit . '" type="text/javascript"></script>');
pq('body')->append('<script src="' . url('/assets/js/custom/site.global.js') . '" type="text/javascript"></script>');


if($edit)
{
  pq('head')->append('<link href="' . url('/assets/css/editor.css') . '" rel="stylesheet" type="text/css">');
  pq('head')->append('<link href="' . url('/assets/css/custom/editor.general.css') . '" rel="stylesheet" type="text/css">');

  pq('body')->append('<script src="' . url('/assets/js/editor.js') . '" type="text/javascript"></script>');
  pq('body')->append('<script src="' . url('/assets/js/fonts.js') . '" type="text/javascript"></script>');
  pq('body')->append('<script src="' . url('/assets/js/custom/editor.general.js') . '" type="text/javascript"></script>');
  pq('body')->append('<script src="' . url('/assets/js/custom/editor.formbuilder.js') . '" type="text/javascript"></script>');
  pq('body')->append('<script src="' . url('/assets/js/vendor/tinymce/js/tinymce/tinymce.min.js') . '" type="text/javascript"></script>');

  $toolbar = View::make('user.site.editor.toolbar', array(
    'site' => $site,
    'page' => $page
  ))->render();

  pq('body')->append($toolbar);
}

/*
 |--------------------------------------------------------------------------
 | Tracking codes
 |--------------------------------------------------------------------------
 */

if($edit)
{
  $head_tag = (isset($site->settings->head_tag)) ? $site->settings->head_tag : '';
  $end_of_body_tag = (isset($site->settings->end_of_body_tag)) ? $site->settings->end_of_body_tag : '';
}
else
{
  $head_tag = (isset($site->settings_published->head_tag)) ? $site->settings_published->head_tag : '';
  $end_of_body_tag = (isset($site->settings_published->end_of_body)) ? $site->settings_published->end_of_body_tag : '';
}

if($head_tag != '')
{
  pq('head')->append('<script>' . $head_tag . '</script>');

}

if($end_of_body_tag != '')
{
  pq('body')->append('<script>' . $end_of_body_tag . '</script>');
}

/*
 |--------------------------------------------------------------------------
 | Stats
 |--------------------------------------------------------------------------
 */

if(! $edit && ! $preview && isset($site))
{
  if($site->piwik_site_id != '')
  {
    $statsJs = \App\Core\Piwik::getJavascriptTag($site->piwik_site_id);
    $statsJs = html_entity_decode($statsJs);
    pq('body')->append($statsJs);
  }
}

/*
 |--------------------------------------------------------------------------
 | Output html
 |--------------------------------------------------------------------------
 */

//$html = \Response::make($dom);
//$html->header('Content-Type', 'text/html');

echo $dom;
?>
@stop