<?php
namespace App\Controller;

use View, Auth;

/*
|--------------------------------------------------------------------------
| Message controller
|--------------------------------------------------------------------------
|
| Message related logic
|
*/

class MessageController extends \BaseController {

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
     * Show messages inbox
     */
    public function getInbox()
    {
        // Get account details
        $user = 'user';

        return View::make('app.messages.inbox', array(
            'user' => $user
        ));

    }

    /**
     * Show message
     */
    public function getMessage()
    {
        // Get account details
        $user = 'user';

        return View::make('app.messages.message', array(
            'user' => $user
        ));

    }



}
