<?php
namespace Lead\Controller;

use View, Auth, Input, Cache, Request;

/*
|--------------------------------------------------------------------------
| Lead controller
|--------------------------------------------------------------------------
|
| Lead related logic
|
*/

class LeadController extends \BaseController {

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
   * Show all leads partial
   */
  public function getLeads()
  {
    $leads = \Lead\Model\Lead::where('user_id', '=', $this->parent_user_id);

    // All sites, joined with campaigns
    $sites = \Web\Model\Site::where('sites.user_id', '=', $this->parent_user_id)
      ->leftJoin('campaigns as c', 'sites.campaign_id', '=', 'c.id')
      ->select(array('sites.*', 'c.name as campaign_name'))
      ->orderBy('campaign_name', 'asc')
      ->orderBy('sites.name', 'asc')
      ->get();

    return View::make('app.leads.leads', array(
      'leads' => $leads,
      'sites' => $sites
    ));
  }

  /**
   * Show lead partial
   */
  public function getLead()
  {
    $sl = \Request::input('sl', '');

    if($sl != '')
    {
      $qs = \App\Core\Secure::string2array($sl);
      $lead = \Lead\Model\Lead::where('id', '=', $qs['lead_id'])->where('user_id', '=',  $this->parent_user_id)->first();
      $sites = \Web\Model\Site::where('user_id', '=', $this->parent_user_id)->select(array('id', 'name'))->get();

      return View::make('app.leads.lead-edit', array(
        'lead' => $lead,
        'sl' => $sl,
        'sites' => $sites
      ));
    }
    else
    {
      $sites = \Web\Model\Site::where('user_id', '=', $this->parent_user_id)->select(array('id', 'name'))->get();

      return View::make('app.leads.lead-new', array(
        'sites' => $sites
      ));
    }
  }

  /**
   * Post lead form from website
   */
  public function postForm()
  {
    $sl = \Request::input('sl', '');

    if($sl != '')
    {
      $sl = \App\Core\Secure::string2array($sl);

      $email = \Input::get('email', Input::get('mail', ''));
      $email = (isset($email[1])) ? $email[1] : $email;
      $name = '';

      if($email != '')
      {
        $user_id = $sl['user_id'];
        $site_id = $sl['site_id'];
        $language = $sl['language'];
        $settings = \Input::except(
          'sl',
          'mwp-fb-success-title',
          'mwp-fb-success-msg',
          'mwp-fb-success-btn',
          'mwp-fb-success-redirect',
          'mwp-fb-success-mail-to',
          'mwp-fb-success-mailchimp-list',
          'mwp-fb-success-aweber-list',
          'mwp-fb-success-getresponse-list',
          'mwp-fb-success-icontact-list'
        );

        // Parse settings
        $data = array();
        $custom_data = array();
        $html = '<table>';

        foreach($settings as $key => $val)
        {
          $label = (isset($val[0])) ? $val[0] : '';
          $value = (isset($val[1])) ? $val[1] : '';
          $type = (isset($val[2])) ? $val[2] : '';

          // Name field found
          if ($type == 'name') $name = $value;
          if ($type != 'name' && $type != 'email') $custom_data = array_merge($custom_data, array(trim($label) => trim($value)));

          if(isset($val['opts']))
          {
            $value = implode(', ', $val['opts']);
          }
          $data[] = array('name' => trim($label), 'val' => trim($value));
          $html .= '<tr><td><strong>' . trim($label) . ':</strong></td><td>' . trim($value) . '</td></tr>';
        }
        $html .= '</table>';

        /*
        ** REMOVED CHECK **
        
        // Check if email already exists in site
        $lead = \Lead\Model\Lead::where('site_id', '=', $site_id)->where('email', '=', $email)->first();

        if(count($lead) == 0)
        {
          $lead = new \Lead\Model\Lead;
        }
        */

        $lead = new \Lead\Model\Lead;

        $lead->user_id = $user_id;
        $lead->site_id = $site_id;
        $lead->language = $language;

        $lead->email = $email;
        $lead->settings = json_encode($data);
        $lead->ip = \App\Core\IP::address();

        // Parse useragent
        $ua = \App\Core\Piwik::getDevice();

        // User agent info
        $lead->os = $ua['os'];
        $lead->client = $ua['client'];
        $lead->device = $ua['device'];
        $lead->brand = $ua['brand'];
        $lead->model = $ua['model'];

        $lead->save();

        // ----------------------------------------------------------------------------------------------------------------------------------------------
        // Add Aweber subscriber
        // ----------------------------------------------------------------------------------------------------------------------------------------------

        $aweber_list = \Input::get('mwp-fb-success-aweber-list', '');

        if ($aweber_list != '') {
          if ($name == '') {
            $subscriber = [
              'email' => $email,
              'ip_address' => \App\Core\IP::address(),
              'custom_fields' => $custom_data
            ];
          } else {
            $subscriber = [
              'email' => $email,
              'ip_address' => \App\Core\IP::address(),
              'name' => $name,
              'custom_fields' => $custom_data
            ];
          }

          $add_aweber_subscriber = \App\Controller\oAuthController::postAweberSubscriber($user_id, $aweber_list, $subscriber);
        }

        // ----------------------------------------------------------------------------------------------------------------------------------------------
        // Add GetResponse subscriber
        // ----------------------------------------------------------------------------------------------------------------------------------------------

        $getresponse_list = \Input::get('mwp-fb-success-getresponse-list', '');

        if ($getresponse_list != '') {
          if ($name == '') {
            $contact = [
              'email' => $email,
              'ipAddress' => \App\Core\IP::address()
            ];
          } else {
            $contact = [
              'email' => $email,
              'ipAddress' => \App\Core\IP::address(),
              'name' => $name
            ];
          }

          $add_getresponse_subscriber = \App\Controller\oAuthController::postGetResponseContact($user_id, $getresponse_list, $contact);
        }

        // ----------------------------------------------------------------------------------------------------------------------------------------------
        // Add MailChimp member
        // ----------------------------------------------------------------------------------------------------------------------------------------------

        $mailchimp_list = \Input::get('mwp-fb-success-mailchimp-list', '');

        if ($mailchimp_list != '') {
          if ($name == '') {
            $member = [
              'email_address' => $email, 
              'status' => 'subscribed'
            ];
          } else {
            $member = [
              'email_address' => $email, 
              'status' => 'subscribed', 
              'merge_fields' => [
                'FNAME' => $name/*,
                'LNAME' => ''*/
              ]
            ];
          }

          $add_mailchimp_member = \App\Controller\oAuthController::postMailchimpMember($user_id, $mailchimp_list, $member);
        }

        // Mail copy to
        $mail_to = \Input::get('mwp-fb-success-mail-to', '');

        if ($mail_to != '')
        {
          $site = \Web\Model\Site::where('id', '=', $site_id)->first();
          $mail_to = str_replace(';', ',', $mail_to);
          $recipients = explode(',', $mail_to);

          if(count($recipients) > 0 && isset($recipients[0]) && $recipients[0] != '')
          {
            $subject = '[' . $_SERVER['HTTP_HOST'] . '] ' . $site->name;
  
            \Mail::send('emails.web.lead', ['body' => $html], function($message) use($site, $recipients, $subject)
            {
              $message->from(\Config::get('mail.from.address'), $site->name);
              $message->to($recipients)->subject($subject);
            });
          }
        }

        $redirect = '';
        $redirect_to_site = \Input::get('mwp-fb-success-redirect', '');

        if ($redirect_to_site != '' && is_numeric($redirect_to_site))
        {
          $site = \Web\Model\Site::where('id', '=', $redirect_to_site)->first();
          if (count($site) > 0)
          {
            $redirect = 'http://' . $site->domain();
            if (\Auth::check()) $redirect .= '?published';
          }
        }

        return \Response::json(array('result' => 'success', 'redirect' => $redirect));
      }
    }
  }

  /**
   * Save (new) lead from CMS
   */
  public function postSave()
  {
    $sl = \Request::get('sl', NULL);
    $email = \Request::get('email', NULL);
    $language = \Request::get('language', NULL);
    $site_id = \Request::get('site_id', NULL);


    $input = array(
      'email' => $email
    );

    $rules = array(
      'email' => 'required|email'
    );

    $validator = \Validator::make($input, $rules);

    if($validator->fails())
    {
      return \Response::json(array(
        'result' => 'error', 
        'result_msg' => $validator->messages()->first()
      ));
    }

    // Check if user has permissions on site_id
    if($site_id != NULL)
    {
      $sites = \Web\Model\Site::where('user_id', '=', $this->parent_user_id)->where('id', '=', $site_id)->get();
      if(count($sites) == 0) die();
    }

    if($sl != NULL)
    {
      $qs = \App\Core\Secure::string2array($sl);
         $lead = \Lead\Model\Lead::where('id', $qs['lead_id'])->where('user_id', '=', $this->parent_user_id)->first();
    }
    else
    {
      $lead = new \Lead\Model\Lead;
    }

    $lead->user_id = $this->parent_user_id;
    $lead->email = $email;
    $lead->language = $language;
    $lead->site_id = $site_id;

    if($lead->save())
    {
      $response = array(
        'result' => 'success',
        'result_msg' => trans('global.lead_added')
      );
    }
    else
    {
      $response = array(
        'result' => 'error', 
        'result_msg' => $lead->errors()->first()
      );
    }

    return \Response::json($response);
  }

  /**
   * Show export leads modal
   */
  public function getLeadsExportModal()
  {
    $parent_user_id = (Auth::user()->parent_id == NULL) ? Auth::user()->id : Auth::user()->parent_id;
    $sl = \App\Core\Secure::array2string(array('user_id' => $parent_user_id));

    return View::make('app.leads.modal.leads-export', compact('sl'));
  }

  /**
   * Export leads
   */
  public function postLeadsExport()
  {
    $parent_user_id = (Auth::user()->parent_id == NULL) ? Auth::user()->id : Auth::user()->parent_id;
    $to = \Request::get('to', 'csv');
    $export = \Request::get('export', '');
    $leads = \Request::get('leads', '');
    if($leads != '') $leads = explode(',', $leads);

    if($export == 'selected')
    {
         $lead = \Lead\Model\Lead::where('user_id', '=', $parent_user_id)->whereIn('id', $leads)->select(array('created_at', 'site_id', 'language', 'ip', 'os', 'client', 'device', 'email', 'settings'))->get();
    }
    else
    {
      $lead = \Lead\Model\Lead::where('user_id', '=', $parent_user_id)->select(array('created_at', 'site_id', 'language', 'ip', 'os', 'client', 'device', 'email', 'settings'))->get();
    }

    $leads_export = array();
    $lead_data_columns = array();
    $i = 0;

    foreach($lead as $lead_export)
    {
      $settings = ($lead_export->settings != '') ? json_decode($lead_export->settings, true) : '';

      $lead_data = array();

      if($settings != '')
      {
        foreach($settings as $setting)
        {
          $lead_data[$setting['name']] = $setting['val'];
          if(! in_array($setting['name'], $lead_data_columns)) $lead_data_columns[] = $setting['name'];
        }
      }

      $site = (isset($lead_export->site->name)) ? $lead_export->site->name : '';

      $leads_export[$i] = array(
        'site' => $site,
        'language' => $lead_export->language,
        'email' => $lead_export->email,
        'ip' => $lead_export->ip,
        'os' => $lead_export->os,
        'client' => $lead_export->client,
        'device' => $lead_export->device,
        'created' => $lead_export->created_at->timezone(Auth::user()->timezone)->format('Y-m-d H:i:s'),
      );

      $leads_export[$i] = array_merge($leads_export[$i], $lead_data);

      $i++;
    }

    // Add missing columns which are collected in $lead_data_columns
    foreach($leads_export as $i => $lead_export)
    {
      foreach($lead_data_columns as $lead_data_column)
      {
        if(! isset($lead_export[$lead_data_column])) $lead_export[$lead_data_column] = ''; // $leads_export[$i][$lead_data_column] = '';
      }
      ksort($lead_export);

      $leads_export[$i] = ($lead_export);
    }

    return \Excel::create('Leads-export-'  .date('Y-m-d'), function($excel) use($leads_export){
      $excel->sheet('Leads', function($sheet) use($leads_export) {
        $sheet->fromArray($leads_export, null, 'A1', false, true);
      });
    })->download($to);
  
  }

  /**
   * Show view modal
   */
  public function getLeadsViewModal()
  {
    $sl = \Input::get('sl', '');
/*
    $qs = array();

    if($sl != '')
    {
      $qs = \App\Core\Secure::string2array($sl);
    }
*/
    return View::make('app.leads.modal.leads-view', compact('sl'));
  }

  /**
   * Delete lead(s)
   */
  public function postDelete()
  {
    $sl = \Request::input('sl', '');

    if(\Auth::check() && $sl != '')
    {
      $qs = \App\Core\Secure::string2array($sl);
      $response = array('result' => 'success');

      try
      {
        $lead = \Lead\Model\Lead::where('id', '=',  $qs['lead_id'])->where('user_id', '=',  $this->parent_user_id)->delete();
      }
      catch (\Exception $e)
      {
        $response = array('result' => 'error', 'msg' => 'Unknown error');
      }
    }
    elseif(\Auth::check())
    {
      foreach(\Request::get('ids', array()) as $id)
      {
        $affected = \Lead\Model\Lead::where('id', '=', $id)->where('user_id', '=',  $this->parent_user_id)->delete();
      }
    }

    return \Response::json(array('result' => 'success'));
  }

  /**
   * Get single lead record
   */
  public function getRecord()
  {
    if(\Auth::check())
    {
      $sl = Input::get('sl', '');
      $id = Input::get('id', '');
      //$response = \Lead\Model\Lead::where('id', '=', $id)->where('user_id', '=',  $this->parent_user_id)->first();

      if($sl != '')
      {
        $qs = \App\Core\Secure::string2array($sl);
        $id = $qs['lead_id'];
      }

      $response = \Lead\Model\Lead::where('leads.id', '=', $id)
        ->where('leads.user_id', '=', $this->parent_user_id)
        ->leftJoin('sites as s', 'leads.site_id', '=', 's.id')
        ->select(array('leads.id', 'leads.email', 'leads.language', 'leads.settings', 'leads.created_at', 'leads.os', 'leads.client', 'leads.device', 'leads.brand', 'leads.model', 's.name as site_name'))
        ->first();

      $response['settings'] = json_decode($response['settings']);
    }

    return \Response::json(array('result' => 'success', 'response' => $response));
  }

  /**
   * Get lead data
   */
  public function getData()
  {
    $filter = Input::get('filter', '');
    $site_id = 0;
    if($filter != '')
    {
      $qs = \App\Core\Secure::string2array($filter);
      $site_id = $qs['site_id'];
    }

    $order_by = Input::get('order.0.column', 0);
    $order = Input::get('order.0.dir', 'asc');
    $search = Input::get('search.regex', '');
    $q = Input::get('search.value', '');
    $start = Input::get('start', 0);
    $draw = Input::get('draw', 1);
    $length = Input::get('length', 10);
    if($length == -1) $length = 100000;
    $data = array();

    $aColumn = array('s.name', 'leads.email', 'leads.language', 'leads.client', 'leads.os', 'leads.created_at');

    if($q != '')
    {
      $count = \Lead\Model\Lead::orderBy($aColumn[$order_by], $order)
        ->leftJoin('sites as s', 'leads.site_id', '=', 's.id')
        ->select(array('leads.id', 'leads.email', 'leads.language', 'leads.client', 'leads.os', 'leads.created_at', 's.name as site_name'))
        ->where(function ($query) use($q) {
          $query->orWhere('email', 'like', '%' . $q . '%');
          $query->orWhere('s.name', 'like', '%' . $q . '%');
        })
        ->where(function ($query) use($site_id) {
          $query->where('leads.user_id', '=', $this->parent_user_id);
          if($site_id > 0)
          {
            $query->where('leads.site_id', '=', $site_id);
          }
        })
        ->count();

      $oData = \Lead\Model\Lead::orderBy($aColumn[$order_by], $order)
        ->leftJoin('sites as s', 'leads.site_id', '=', 's.id')
        ->select(array('leads.id', 'leads.email', 'leads.language', 'leads.client', 'leads.os', 'leads.created_at', 's.name as site_name'))
        ->where(function ($query) use($q) {
          $query->orWhere('email', 'like', '%' . $q . '%');
          $query->orWhere('s.name', 'like', '%' . $q . '%');
        })
        ->where(function ($query) use($site_id) {
          $query->where('leads.user_id', '=', $this->parent_user_id);
          if($site_id > 0)
          {
            $query->where('leads.site_id', '=', $site_id);
          }
        })
        ->take($length)->skip($start)->get();
    }
    else
    {
      $count = \Lead\Model\Lead::where('user_id', '=', $this->parent_user_id)->count();

      if($order_by == 0)
      {
        $oData = \Lead\Model\Lead::where('leads.user_id', '=', $this->parent_user_id)
          ->leftJoin('sites as s', 'leads.site_id', '=', 's.id')
          ->select(array('leads.id', 'leads.email', 'leads.language', 'leads.client', 'leads.os', 'leads.created_at', 's.name as site_name'))
          ->orderBy($aColumn[$order_by], $order)
          ->orderBy('s.name', $order)
          ->take($length)
          ->skip($start)
          ->where(function ($query) use($site_id) {
            if($site_id > 0)
            {
              $query->where('leads.site_id', '=', $site_id);
            }
          })
          ->get();
      }
      else
      {
        $oData = \Lead\Model\Lead::where('leads.user_id', '=', $this->parent_user_id)
          ->leftJoin('sites as s', 'leads.site_id', '=', 's.id')
          ->select(array('leads.id', 'leads.email', 'leads.language', 'leads.client', 'leads.os', 'leads.created_at', 's.name as site_name'))
          ->orderBy($aColumn[$order_by], $order)
          ->take($length)
          ->skip($start)
          ->where(function ($query) use($site_id) {
            if($site_id > 0)
            {
              $query->where('leads.site_id', '=', $site_id);
            }
          })
          ->get();
      }
    }

    if($length == -1) $length = $count;

    $recordsTotal = $count;
    $recordsFiltered = $count;

    foreach($oData as $row)
    {
      if($row->site_name != NULL)
      {
         //$source = '<div class="label label-success">' . trans('global.web') . '</div> ' . $row->site_name;
        $source = $row->site_name;
      }
      else
      {
        $source = '<div class="label label-warning">' . trans('global.none') . '</div>';
        
      }

      $data[] = array(
        'DT_RowId' => 'row_' . $row->id,
        'source' => $source,
        'email' => $row->email,
        'language' => $row->language,
        'client' => $row->client,
        'os' => $row->os,
        'qs' => \App\Core\Secure::array2string(array('lead_id' => $row->id)),
        'created_at' => $row->created_at->timezone(Auth::user()->timezone)->format('Y-m-d H:i:s'),
        'sl' => \App\Core\Secure::array2string(array('lead_id' => $row->id))
        /*'created_at' => $row->created_at->timezone(Auth::user()->timezone)->format(trans('i18n.dateformat_full'))*/
      );
    }

    $response = array(
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsFiltered,
      'data' => $data
    );

    echo json_encode($response);
  }
}