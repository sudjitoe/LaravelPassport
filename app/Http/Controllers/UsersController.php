<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UsersController extends Controller
{
    public function login()
    {
        // check auth
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            // create token
            $success['token'] = $user->createToken('appToken')->accessToken;
           // sAfter successfull authentication, notice how I return json parameters
           // return to json
            return response()->json([
              'success' => true,
              'token' => $success,
              'user' => $user
          ]);
        } else {
        // if authentication is unsuccessfull, notice how I return json parameters
        // return to json message error
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], 401);
        }
    }

    public function register(Request $request)
    {
        // validation data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);
        // if not valid
        if ($validator->fails()) {
          return response()->json([
            'success' => false,
            'message' => $validator->errors(),
          ], 401);
        }
        // if valid get all data input
        $input = $request->all();
        // bcrypt password
        $input['password'] = bcrypt($input['password']);
        // create user by input
        $user = User::create($input);
        // create token user
        $success['token'] = $user->createToken('appToken')->accessToken;
        // return to json
        return response()->json([
          'success' => true,
          'token' => $success,
          'user' => $user
      ]);
    }

    public function logout(Request $request)
    {
        // cek user auth
       if (Auth::user()) {
            // get token user
           $user = Auth::user()->token();
        // logout user
           $user->revoke();
        // return json json
           return response()->json([
               'success' =>true,
               'message' => 'Logout successfully'
           ]);
       } else {
        // return to json message eror
           return response()->json([
               'success' => false,
               'message' =>'Unable to logout'
           ]);
       }
       
    }
}
