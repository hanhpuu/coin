<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
	/*
	  |--------------------------------------------------------------------------
	  | Login Controller
	  |--------------------------------------------------------------------------
	  |
	  | This controller handles authenticating users for the application and
	  | redirecting them to your home screen. The controller uses a trait
	  | to conveniently provide its functionality to your applications.
	  |
	 */

use AuthenticatesUsers {
		redirectPath as laravelRedirectPath;
	}

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = '/coins';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest')->except('logout');
	}

	/**
	 * Get the post register / login redirect path.
	 *
	 * @return string
	 */
	public function redirectPath()
	{
		// Do your logic to flash data to session...
		Session::flash('success', 'You are logged in!');

		// Return the results of the method we are overriding that we aliased.
		return $this->laravelRedirectPath();
	}

}
