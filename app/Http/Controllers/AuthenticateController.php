<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;
use App\User;

class AuthenticateController extends Controller
{
  public function __construct()
  {
    $this->middleware('jwt.auth', ['except' => ['authenticate', 'register']]);
  }
  public function index()
  {
    return User::all();
  }

  public function authenticate(Request $request)
  {
      $credentials = $request->only('email', 'password');

      try {
          // verify the credentials and create a token for the user
          if (! $token = JWTAuth::attempt($credentials)) {
              return response()->json(['error' => 'invalid_credentials'], 401);
          }
      } catch (JWTException $e) {
          // something went wrong
          return response()->json(['error' => 'could_not_create_token'], 500);
      }

      // if no errors are encountered we can return a JWT
      // return response()->json(compact('token'));
      return response()->json(['token' => $token], 200);
  }

  public function register(Request $request)
  {
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password),
    ]);

    try {
      if (!$user) {
        return response()->json(['reg_status', 'Registering Failed'], 401);
      }
    } catch (Exception $e) {
      return response()->json(['error' => 'could_not_create_user'], 500);
    }

    return response()->json(['reg_status' => 'Registered'], 200);
  }
}
