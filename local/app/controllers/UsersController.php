<?php
/** * UsersController Class * * Implements actions regarding user management */
class UsersController extends Controller

	{
	/** * Displays the form for account creation * * @return Illuminate\Http\Response */
	public

	function create()
		{
		return View::make(Config::get('confide::signup_form'));
		}

	/** * Stores new account * * @return Illuminate\Http\Response */
	public

	function store()
		{
		$repo = App::make('UserRepository');
		$user = $repo->signup(Input::all());
		if ($user->id)
		{
            // Set initial plan (free)
            $user->plan_id = 1;
            $user->save();

			Mail::send(Config::get('confide::email_account_confirmation') , compact('user') ,
			function ($message) use($user)
				{
				$message->to($user->email, $user->username)->subject(Lang::get('confide.email.account_confirmation.subject'));
				});
			return Redirect::action('UsersController@login')->with('notice', Lang::get('confide.alerts.account_created'));
		}
        else
		{
			$error = $user->errors()->all(':message');
			return Redirect::action('UsersController@create')->withInput(Input::except('password'))->with('error', $error);
			}
		}

	/** * Displays the login form * * @return Illuminate\Http\Response */
	public

	function login()
		{
		if (Confide::user())
			{
				return Redirect::to('/platform');
			}
		  else
			{
			return View::make(Config::get('confide::login_form'));
			}
		}

	/** * Attempt to do login * * @return Illuminate\Http\Response */
	public

	function doLogin()
		{
		$repo = App::make('UserRepository');
		$input = Input::all();
		if ($repo->login($input))
			{
				// Increment login count
				$reseller = \App\Controller\ResellerController::get();

				$user = User::where(function ($query) use($input) {
					$query->where('email', $input['email'])
						  ->orWhere('username', $input['email']);
				})->where(function ($query) use($reseller) {
						$query->where('reseller_id', $reseller->id);
				})->first();

				if (! empty($user))
				{
					$user->increment('logins');
					$user->last_login = date('Y-m-d H:i:s');

					// Set language
					$lang = \Input::get('lang', '');
					if ($lang != '')
					{
						$user->language = $lang;
					}

					$user->save();
				}

                $logout = Session::get('logout', '');

                if($logout == '')
                {
                    // Log
                    \App\Controller\LogController::Log($user, 'Login', 'logged in');
                }

				return Redirect::intended('/platform');
			}
		  else
			{
			if ($repo->isThrottled($input))
				{
				$err_msg = Lang::get('confide.alerts.too_many_attempts');
				}
			elseif ($repo->existsButNotConfirmed($input))
				{
				$err_msg = Lang::get('confide.alerts.not_confirmed');
				}
			  else
				{
				$err_msg = Lang::get('confide.alerts.wrong_credentials');
				}

			return Redirect::action('UsersController@login')->withInput(Input::except('password'))->with('error', $err_msg);
			}
		}

	/** * Attempt to confirm account with code * * @param string $code * * @return Illuminate\Http\Response */
	public

	function confirm($code)
		{
		if (Confide::confirm($code))
			{
			$notice_msg = Lang::get('confide.alerts.confirmation');
			return Redirect::action('UsersController@login')->with('notice', $notice_msg);
			}
		  else
			{
			$error_msg = Lang::get('confide.alerts.wrong_confirmation');
			return Redirect::action('UsersController@login')->with('error', $error_msg);
			}
		}

	/** * Displays the forgot password form * * @return Illuminate\Http\Response */
	public

	function forgotPassword()
		{
		return View::make(Config::get('confide::forgot_password_form'));
		}

	/** * Attempt to send change password link to the given email * * @return Illuminate\Http\Response */
	public

	function doForgotPassword()
		{
		if (Confide::forgotPassword(Input::get('email')))
			{
			$notice_msg = Lang::get('confide.alerts.password_forgot');
			return Redirect::action('UsersController@login')->with('notice', $notice_msg);
			}
		  else
			{
			$error_msg = Lang::get('confide.alerts.wrong_password_forgot');
			return Redirect::action('UsersController@forgotPassword')->withInput()->with('error', $error_msg);
			}
		}

	/** * Shows the change password form with the given token * * @param string $token * * @return Illuminate\Http\Response */
	public

	function resetPassword($token)
		{
		return View::make(Config::get('confide::reset_password_form'))->with('token', $token);
		}

	/** * Attempt change password of the user * * @return Illuminate\Http\Response */
	public

	function doResetPassword()
		{
		$repo = App::make('UserRepository');
		$input = array(
			'token' => Input::get('token') ,
			'password' => Input::get('password') ,
			'password_confirmation' => Input::get('password_confirmation') ,
		);

		// By passing an array with the token, password and confirmation

		if ($repo->resetPassword($input))
			{
			$notice_msg = Lang::get('confide.alerts.password_reset');
			return Redirect::action('UsersController@login')->with('notice', $notice_msg);
			}
		  else
			{
			$error_msg = Lang::get('confide.alerts.wrong_password_reset');
			return Redirect::action('UsersController@resetPassword', array(
				'token' => $input['token']
			))->withInput()->with('error', $error_msg);
			}
		}

	/** * Log the user out of the application. * * @return Illuminate\Http\Response */
	public

	function logout()
		{
            $sl = Session::pull('logout', '');
            if($sl != '')
            {
                $qs = \App\Core\Secure::string2array($sl);
                \Auth::loginUsingId($qs['user_id']);
                return \Redirect::to('/platform#/admin/users');
            }
            else
            {
                // Log
                \App\Controller\LogController::Log(Auth::user(), 'Login', 'logged out');

                Confide::logout();
                return Redirect::to('/login?lang=' . \App::getLocale());
            }
		}
	}

