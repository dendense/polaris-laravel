<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $status_code = 200;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get JWT via given credtials.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        /**
         * Validation 
         */
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()){
            
            $response = [
                'status' => 'fail',
                'success' => false,
                'msg' => 'validation errors',
                'errors' => $validator->errors()
            ];

            return response()->json($response, 422);
        }

        /**
         * Comparing user data to database
         */
        if (! $token = auth()->attempt($validator->validated())) {
            
            $response = [
                'status' => 'fail',
                'success' => false,
                'msg' => 'Unauthorized'
            ];

            return response()->json($response, 401);

        }
            return $this->createNewToken($token);
        

    }

    /**
     * Register a User
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        /**
         * Validation
         */
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|email|string|max:100|unique:users',
            'password' => 'required|min:8|string|confirmed',
        ]);

        if ($validator->fails()) {
            
            $response = [
                'status' => 'fail',
                'success' => false,
                'msg' => 'Validation error',
                'errors' => $validator->errors(),
            ];

            return response()->json($response, 400);

        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        $user->login = [
            'href' => 'api/v1/user/login',
            'method' => 'POST'
        ];

        $response = [
            'status' => $this->status_code,
            'success' => true,
            'msg' => 'User successfuly registrated',
            'user' => $user
        ];

        return response()->json($response, 200);
    }

    /**
     * Log the user out (Invalidate the token)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'msg' => 'User successfuly signed out'
        ]);
    }

    /**
     * Refresh token
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {       
        return response()->json(auth()->user());
    }

    /**
     * Get token arra structure.
     * 
     * @param $token
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
