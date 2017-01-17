<?php
namespace Analytics\Controller;

use View, Auth, Input, Cache;

/*
|--------------------------------------------------------------------------
| Web Analytics controller
|--------------------------------------------------------------------------
|
| Stats related logic
|
*/

class WebAnalyticsController extends \BaseController {

    /**
	 * Construct
     */
    public function __construct()
    {
		$this->query_cache = 60*24*7*4*12; // minutes
		$this->cache_pefix = 'rs-log-';

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
     * Show web(site) stats
     */
    public function getWeb()
    {
        // Security link        
		$sl = Input::get('sl', '');

        // Range
		$date_start = Input::get('start', date('Y-m-d', strtotime(' - 30 day')));
		$date_end = Input::get('end', date('Y-m-d'));

        // All sites, joined with campaigns
		$sites = \Web\Model\Site::where('sites.user_id', '=', $this->parent_user_id)
			->leftJoin('campaigns as c', 'sites.campaign_id', '=', 'c.id')
			->select(array('sites.*', 'c.name as campaign_name'))
			->orderBy('campaign_name', 'asc')
			->orderBy('sites.name', 'asc')
			->get();

   		//$sites = \Web\Model\Site::where('user_id', '=', $this->parent_user_id)->orderBy('name', 'asc')->get();

        $site = false;
        $site_created = NULL;

        if($sl != '')
        {
            $qs = \App\Core\Secure::string2array($sl);
            $site = \Web\Model\Site::where('id', '=', $qs['site_id'])->where('user_id', '=', $this->parent_user_id)->first();
			$site_created = $site->created_at->timezone(Auth::user()->timezone)->format("Y-m-d");
			//if($site_created >= $date_start) $date_start = $site_created;
        }

        // Stats found?
        $stats_found = false;
		$summary_range = NULL;
		$summary_days = NULL;
		$browsers_range = NULL;
		$os_range = NULL;
		$countries_range = NULL;
		$referrers_range = NULL;
		$lead_count = NULL;
		$leads_days = NULL;

        if($site !== false)
        {

            $from =  $date_start . ' 00:00:00';
            $to = $date_end . ' 23:59:59';

            $summary_range = \App\Core\Piwik::getSummaryRange($site->piwik_site_id, $date_start, $date_end);
            $summary_days = \App\Core\Piwik::getSummaryDays($site->piwik_site_id, $date_start, $date_end);
            $browsers_range = \App\Core\Piwik::getBrowsersRange($site->piwik_site_id, $date_start, $date_end);
            $os_range = \App\Core\Piwik::getOsRange($site->piwik_site_id, $date_start, $date_end);
            $countries_range = \App\Core\Piwik::getCountriesRange($site->piwik_site_id, $date_start, $date_end);
            $referrers_range = \App\Core\Piwik::getReferrersRange($site->piwik_site_id, $date_start, $date_end);
            $lead_count = \Lead\Model\Lead::where('site_id', '=', $site->id)
                ->where('created_at', '>=', $from)
                ->where('created_at', '<=', $to)
                ->count();

            $leads_days = \Lead\Model\Lead::where('site_id', '=', $site->id)
                ->select(\DB::raw('DATE(created_at) as date'), \DB::raw('count(*) as leads'))
                ->where('created_at', '>=', $from)
                ->where('created_at', '<=', $to)
                ->groupBy(\DB::raw('DATE(created_at)'))
                ->get()->toArray();

			$stats_found = true;
        }

        return View::make('app.analytics.web-analytics', array(
			'sl' => $sl,
			'date_start' => $date_start,
			'date_end' => $date_end,
			'sites' => $sites,
			'stats_found' => $stats_found,
			'site' => $site,
			'site_created' => $site_created,
			'summary_range' => $summary_range,
			'summary_days' => $summary_days,
			'browsers_range' => $browsers_range,
			'os_range' => $os_range,
			'countries_range' => $countries_range,
			'referrers_range' => $referrers_range,
			'lead_count' => $lead_count,
			'leads_days' => $leads_days
		));
    }

    /**
     * Get totals for this month
     */
    public function getThisMonth()
    {
		// Range
		$date_start = Input::get('start', date('Y-m-01'));
		$date_end = Input::get('end', date('Y-m-d'));

		$visits = \App\Core\Piwik::getVisits('user' . $this->parent_user_id, $date_start, $date_end);

		$limit = 1000;
		$perc = ($visits / $limit) * 100;

        $formatted_limit = number_format($limit, 0, trans('i18n.dec_point'), trans('i18n.thousands_sep'));
        $formatted_visits = number_format($visits, 0, trans('i18n.dec_point'), trans('i18n.thousands_sep'));
        $formatted_perc = (float)number_format($perc, 1, trans('i18n.dec_point'), trans('i18n.thousands_sep'));

		return \Response::json(array('visits' => $visits, 'formatted_visits' => $formatted_visits, 'formatted_limit' => $formatted_limit, 'limit' => $limit, 'perc' => $perc, 'formatted_perc' => $formatted_perc));
    }

    /**
     * Get visits and conversion rate
     */
    public function getVisitsConversion()
    {
        // Security link        
		$sl = Input::get('sl', '');

        if($sl != '')
        {
			// Range
			$date_start = Input::get('start', date('Y-m-d', strtotime(' - 30 day')));
			$date_end = Input::get('end', date('Y-m-d'));

            $qs = \App\Core\Secure::string2array($sl);

            if(isset($qs['site_id']))
            {
                //$site = \Web\Model\Site::where('id', '=', $qs['site_id'])->where('user_id', '=', $this->parent_user_id)->first();
                $site = \Web\Model\Site::where('id', '=', $qs['site_id'])->first();
    			$date_start = $site->created_at->timezone(Auth::user()->timezone)->format("Y-m-d");
    			$visits = \App\Core\Piwik::getVisits($site->piwik_site_id, $date_start, $date_end);
                $leads = \Lead\Model\Lead::where('site_id', '=', $site->id)->count();
            }
            elseif(isset($qs['app_id']))
            {
                $app = \Mobile\Model\App::where('id', '=', $qs['app_id'])->first();
    			$date_start = $app->created_at->timezone(Auth::user()->timezone)->format("Y-m-d");
    			$visits = \App\Core\Piwik::getVisits($app->piwik_site_id, $date_start, $date_end);
                $leads = \Lead\Model\Lead::where('app_id', '=', $app->id)->count();
            }

			$conversion = ($leads == 0 || $visits == 0) ? 0 : round($leads / $visits * 100);

			$visits = number_format($visits, 0, trans('i18n.dec_point'), trans('i18n.thousands_sep'));

			return \Response::json(array('visits' => $visits, 'leads' => $leads, 'conversion' => $conversion));
		}
    }
}