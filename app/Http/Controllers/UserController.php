<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;
use JWTAuthException;
use Hash;
use Validator;

class UserController extends Controller
{

	private $user;

	public function __construct(User $user)
	{
		$this->user = $user;
	}

	public function register(Request $request)
	{
		$validator = Validator::make($request->all(), [
					'name' => 'required|max:255',
					'email' => 'required|unique:users|email',
					'password' => 'required'
		]);

		if ($validator->fails()) {
			return response()->json([
						'code' => 406,
						'message' => 'Please enter validate data!'
			]);
		}

		$user = $this->user->create([
			'name' => $request->get('name'),
			'email' => $request->get('email'),
			'password' => Hash::make($request->get('password'))
		]);

		return response()->json([
					'status' => 200,
					'message' => 'User created successfully',
					'data' => $user
		]);
	}

	public function login(Request $request)
	{
		$credentials = $request->only('email', 'password');
		$token = null;
		try {
			if (!$token = JWTAuth::attempt($credentials)) {
				return response()->json(['invalid_email_or_password'], 422);
			}
		} catch (JWTAuthException $e) {
			return response()->json(['failed_to_create_token'], 500);
		}
		return response()->json(compact('token'));
	}

	public function getUserInfo(Request $request)
	{
		$user = JWTAuth::toUser($request->token);
		return response()->json(['result' => $user]);
	}

	public function logout(Request $request)
	{
		$this->validate($request, ['token' => 'required']);
        
        try {
            JWTAuth::invalidate($request->input('token'));
            return response()->json(['success' => true, 'message'=> "You have successfully logged out."]);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }		
	}
	
	public function test(Request $request)
	{
		try {
			$user = JWTAuth::toUser($request->token);
			return response()->json(['success' => true, 'message'=> 'Permission granted']);
		} catch (JWTException $e) {
			return response()->json(['success' => false, 'error' => 'You need to logout, please try again.'], 500);
		}
	
	
	}

}
