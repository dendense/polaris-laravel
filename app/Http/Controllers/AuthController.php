<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    private $status_code = 200;

    public function register(Request $request)
    {
        /**
         * Validation
         */
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            
            return response()->json([
                'status' => 'fails',
                'success' => false,
                'msg' => 'Validation error',
                'errors' => $validator->errors(),
            ], 404);

        }

        /**
         * Checking if email is already exist
         */
        $email_status = User::where('email',$request->email)->first();

        if (is_null($email_status)) {

            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            if ($user->save()) {

                return response()->json([
                    'status' => $this->status_code,
                    'success' => true,
                    'msg' => 'Registration completed',
                    'data' => $user,
                    'href' => 'api/v1/user/login',
                    'method' => 'POST'
                ], 200);

            }
            else {
                
                return response()->json([
                    'status' => 'failed',
                    'success' => true,
                    'msg' => 'Registration failed',
                ], 404);

            }

        }
        else {

            return response()->json([
                'status' => 'failed',
                'success' => false,
                'msg' => 'Email is already registrated'
            ]);

        }
    }

    public function signin(Request $request)
    {
        /**
         * Validation
         */
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if($validator->fails()){
            
            return response()->json([
                'status' => 'fail',
                'success' => false,
                'msg' => 'Validation errors',
                'errors'=> $validator->errors(),
                'href'=> '/user/login' 
            ], 404);

        }

        /**
         * Comparing user data to database
         */
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            
            $user = $this->user($request->email);

            return response()->json([
                'status' => $this->status_code,
                'success' => true,
                'msg' => 'You have logged in successfuly',
                'href' => '/home',
                'method' => 'GET',
                'data' => $user,
            ], 200);

        }
        else {
            return response()->json([
                'status' => 'failed',
                'success' => false,
                'msg' => 'Oops! Login Failed',
            ], 404);
        }
        

    }

    public function user($email)
    {
        $user = array();

        if($email != '') {
            
            $user = User::where('email',$email)->first();

            return $user;
        }
        else
        {
            return false;
        }
    }
}
