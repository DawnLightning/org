<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Http\Middleware\Cauth;
use Jtools\Capi;
use Illuminate\Support\Facades\Input;

class AuthController extends Controller {
	/*
	 * |--------------------------------------------------------------------------
	 * | Registration & Login Controller
	 * |--------------------------------------------------------------------------
	 * |
	 * | This controller handles the registration of new users, as well as the
	 * | authentication of existing users. By default, this controller uses
	 * | a simple trait to add these behaviors. Why don't you explore it?
	 * |
	 */
	
	use AuthenticatesAndRegistersUsers, ThrottlesLogins;
	protected $redirectPath = '/';
	protected $username = 'phone';
	
	/**
	 * Create a new authentication controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware ( 'guest', [ 
				'except' => 'getLogout' 
		] );
	}
	
	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param array $data        	
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data) {
		return Validator::make ( $data, [ 
				'name' => 'required|max:255',
				'phone' => 'required|digits:11|unique:users',
				'password' => 'required|confirmed|min:6' 
		] );
	}
	
	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param array $data        	
	 * @return User
	 */
	protected function create(array $data) {
		return User::create ( [ 
				'name' => $data ['name'],
				'phone' => $data ['phone'],
				'password' => bcrypt ( $data ['password'] ) 
		] );
	}
	
	/**
	 * Show the application login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getLogin()
	{
		if (Input::get ( "ajax" )) {
			Capi::showmessage_by_data ( "do_success", 0, array (
					"_token" => csrf_token () 
			) );
		} elseif (view()->exists('auth.authenticate')) {
			return view('auth.authenticate');
		}
	
		return view('auth.login');
	}
	

	/**
	 * Handle a login request to the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function postLogin(Request $request)
	{
		$this->validate($request, [
				$this->loginUsername() => 'required', 'password' => 'required',
		]);
	
		// If the class is using the ThrottlesLogins trait, we can automatically throttle
		// the login attempts for this application. We'll key this by the username and
		// the IP address of the client making these requests into this application.
		$throttles = $this->isUsingThrottlesLoginsTrait();
	
		if ($throttles && $this->hasTooManyLoginAttempts($request)) {
			return $this->sendLockoutResponse($request);
		}
	
		$credentials = $this->getCredentials($request);
	
		if (Auth::attempt($credentials, $request->has('remember'))) {
			return $this->handleUserWasAuthenticated($request, $throttles);
		}
	
		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		if ($throttles) {
			$this->incrementLoginAttempts($request);
		}
	
		return redirect($this->loginPath())
		->withInput($request->only($this->loginUsername(), 'remember'))
		->withErrors([
				$this->loginUsername() => $this->getFailedLoginMessage(),
		]);
	}
}
