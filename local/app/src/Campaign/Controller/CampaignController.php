<?php
namespace Campaign\Controller;

use View, Auth, Input, Cache;

/*
|--------------------------------------------------------------------------
| Campaign controller
|--------------------------------------------------------------------------
|
| Campaign related logic
|
*/

class CampaignController extends \BaseController {

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
     * Show all campaigns partial
     */
    public function getCampaigns()
    {
		if(\Auth::user()->parent_id != NULL && \Auth::user()->getRoleId() == 4)
		{
			return View::make('app.auth.no-access');
		}

		$campaigns = \Campaign\Model\Campaign::where('user_id', '=', $this->parent_user_id)->orderBy('name', 'asc');

        return View::make('app.campaigns.campaigns', array(
			'campaigns' => $campaigns
		));
    }

    /**
     * Show campaign partial
     */
    public function getCampaign()
    {
		if(\Auth::user()->parent_id != NULL && \Auth::user()->getRoleId() == 4)
		{
			return View::make('app.auth.no-access');
		}

		$sl = \Request::input('sl', '');

		if($sl != '')
		{
			$qs = \App\Core\Secure::string2array($sl);
       		$campaign = \Campaign\Model\Campaign::where('id', '=', $qs['campaign_id'])->where('user_id', '=',  $this->parent_user_id)->first();

			return View::make('app.campaigns.campaign-edit', array(
				'campaign' => $campaign,
				'sl' => $sl
			));
		}
		else
		{
			return View::make('app.campaigns.campaign-new');
		}
    }

    /**
     * Get log data
     */
    public function getData()
    {
		$order_by = Input::get('order.0.column', 0);
		$order = Input::get('order.0.dir', 'asc');
		$search = Input::get('search.regex', '');
		$q = Input::get('search.value', '');
		$start = Input::get('start', 0);
		$draw = Input::get('draw', 1);
		$length = Input::get('length', 10);
        if($length == -1) $length = 100000;
		$data = array();

		$aColumn = array('name', 'created_at', 'id');

		if($q != '')
		{
			$count = \Campaign\Model\Campaign::orderBy($aColumn[$order_by], $order)
				->where('user_id', '=', $this->parent_user_id)
				->where(function ($query) use($q) {
					$query->orWhere('name', 'like', '%' . $q . '%');
					$query->orWhere('active', '=', '' . $q . '');
				})
				->count();

			$oData = \Campaign\Model\Campaign::orderBy($aColumn[$order_by], $order)
				->where('user_id', '=', $this->parent_user_id)
				->where(function ($query) use($q) {
					$query->orWhere('name', 'like', '%' . $q . '%');
					$query->orWhere('active', '=', '' . $q . '');
				})
				->take($length)->skip($start)->get();
		}
		else
		{
			$count = \Campaign\Model\Campaign::where('user_id', '=', $this->parent_user_id)->orderBy($aColumn[$order_by], $order)->count();
			$oData = \Campaign\Model\Campaign::where('user_id', '=', $this->parent_user_id)->orderBy($aColumn[$order_by], $order)->take($length)->skip($start)->get();
		}


		if($length == -1) $length = $count;

		$recordsTotal = $count;
		$recordsFiltered = $count;

		foreach($oData as $row)
		{
			$data[] = array(
				'DT_RowId' => 'row_' . $row->id,
				'name' => $row->name,
				'created_at' => $row->created_at->timezone(Auth::user()->timezone)->format('Y-m-d H:i:s'),
				'sl' => \App\Core\Secure::array2string(array('campaign_id' => $row->id))
			);
		}

		$response = array(
			'draw' => $draw,
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		);

		return \Response::json($response);
    }

    /**
     * Save (new) campaign
     */
    public function postSave()
    {
        $sl = \Request::get('sl', NULL);
        $name = \Request::get('name');

		if($sl != NULL)
		{
			$qs = \App\Core\Secure::string2array($sl);
       		$campaign = \Campaign\Model\Campaign::where('id', $qs['campaign_id'])->where('user_id', '=', $this->parent_user_id)->first();
		}
		else
		{
	        $campaign = new \Campaign\Model\Campaign;
		}

        $campaign->user_id = $this->parent_user_id;
        $campaign->name = $name;

        if($campaign->save())
        {
            $response = array(
                'result' => 'success',
                'result_msg' => trans('global.campaign_added')
            );
        }
        else
        {
            $response = array(
                'result' => 'error', 
                'result_msg' => $campaign->getErrors()->first()
            );
        }

		return \Response::json($response);
    }

    /**
     * Delete campaign
     */
    public function postDelete()
    {
		$sl = \Request::input('sl', '');

        if(\Auth::check() && $sl != '')
        {
            $qs = \App\Core\Secure::string2array($sl);
            $response = array('result' => 'success');

            // Check if apps make use of this campaign
            $app = \Web\Model\Site::where('campaign_id', '=', $qs['campaign_id'])->first();
            if (empty($app))
            {
                $campaign = \Campaign\Model\Campaign::where('id', '=',  $qs['campaign_id'])->where('user_id', '=',  $this->parent_user_id)->delete();
            }
            else
            {
                $response = array('result' => 'error', 'msg' => trans('global.delete_campaign_restricted'));
            }
        }
		return \Response::json($response);
    }
}