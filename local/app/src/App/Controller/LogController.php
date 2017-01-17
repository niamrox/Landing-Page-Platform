<?php
namespace App\Controller;

use View, Auth, Input, Cache;

/*
|--------------------------------------------------------------------------
| Log controller
|--------------------------------------------------------------------------
|
| Log related logic
|
*/

class LogController extends \BaseController {

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
     * Show log partial
     */
    public function getLog()
    {
        return View::make('app.settings.log');
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

		$aColumn = array('desc', 'os', 'client', 'device', 'ip', 'created_at');

		$cache_key = $this->cache_pefix . md5(Auth::user()->id . implode(',', $aColumn) . '-' . $order_by . '-' . $order . '-' . $search . '-' . $q . '-' . $start . '-' . $draw . '-' . $length);

		if($q != '')
		{
			$count = \App\Model\Log::orderBy($aColumn[$order_by], $order)
				->where(function ($query) {
					$query->where('user_id', '=', Auth::user()->id);
					$query->orWhere('parent_id', '=', Auth::user()->id);
				})
				->where(function ($query) use($q) {
					$query->orWhere('subject', 'like', '%' . $q . '%');
					$query->orWhere('desc', 'like', '%' . $q . '%');
					$query->orWhere('ip', 'like', '%' . $q . '%');
					$query->orWhere('client', 'like', '%' . $q . '%');
					$query->orWhere('device', 'like', '%' . $q . '%');
					$query->orWhere('os', 'like', '%' . $q . '%');
				})
				->count();

			$oData = \App\Model\Log::orderBy($aColumn[$order_by], $order)
				->where(function ($query) {
					$query->where('user_id', '=', Auth::user()->id);
					$query->orWhere('parent_id', '=', Auth::user()->id);
				})
				->where(function ($query) use($q) {
					$query->orWhere('subject', 'like', '%' . $q . '%');
					$query->orWhere('desc', 'like', '%' . $q . '%');
					$query->orWhere('ip', 'like', '%' . $q . '%');
					$query->orWhere('client', 'like', '%' . $q . '%');
					$query->orWhere('device', 'like', '%' . $q . '%');
					$query->orWhere('os', 'like', '%' . $q . '%');
				})
				->take($length)->skip($start)->get();
		}
		else
		{
			$count = \App\Model\Log::where('user_id', '=', Auth::user()->id)->orWhere('parent_id', '=', Auth::user()->id)->orderBy($aColumn[$order_by], $order)->count();
			$oData = \App\Model\Log::where('user_id', '=', Auth::user()->id)->orWhere('parent_id', '=', Auth::user()->id)->orderBy($aColumn[$order_by], $order)->take($length)->skip($start)->get();
		}

		if($length == -1) $length = $count;

		$recordsTotal = $count;
		$recordsFiltered = $count;

		foreach($oData as $row)
		{
			$data[] = array(
				'DT_RowId' => 'row_' . $row->id,
				'desc' => $row->desc,
				'ip' => $row->ip,
				'client' => $row->client,
				'os' => $row->os,
				'device' => $row->device,
				/*'brand' => $row->brand,
				'model' => $row->model,*/
				'created_at' => $row->created_at->timezone(Auth::user()->timezone)->format(trans('i18n.dateformat_full'))
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
     * Log action
     */
    public static function Log($user, $subject, $desc, $type = 'user')
    {
		// Disable logging
		return;
		// Parse useragent
		$ua = \App\Core\Piwik::getDevice();

		if(isset($user->username))
		{
			$username = ($user->first_name != '' || $user->last_name != '') ? $user->first_name . ' ' . $user->last_name : $user->username;
			$username = $username . ' [' . $user->email . ']';
		}
		elseif(isset($user) && is_numeric($user))
		{
			$username = $user;
		}
		else
		{
			return;
		}

		$desc = $username . ' ' . $desc;

		$log = new \App\Model\Log;
		$log->user_id = (Auth::check() ? Auth::user()->id : 0);
		$log->parent_id = (Auth::check() && Auth::user()->parent_id != NULL) ? Auth::user()->parent_id : NULL;
		$log->ip = \App\Core\IP::address();
		$log->subject = $subject;
		$log->desc = $desc;
		$log->type = $type;

		// User agent info
		$log->os = $ua['os'];
		$log->client = $ua['client'];
		$log->device = $ua['device'];
		$log->brand = $ua['brand'];
		$log->model = $ua['model'];

		$success = $log->save(); 
    }
}