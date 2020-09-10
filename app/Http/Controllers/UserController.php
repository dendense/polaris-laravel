<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{

    use UploadTrait;

    /**
     * Protecting UserController
     *  
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('id', $id)->first();

        if (!is_null($user)) {
            return response()->json([
                'success' => true,
                'msg' => 'Success',
                'user' => $user
            ],200);
        }

        return response()->json([
            'success' => false,
            'msg' => 'User did not exist, try again',
        ],404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /**
         * Find current user in database
         */
        $user = User::where('id', $id)->findOrFail($id);

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'success' => true,
                'msg' => "Sorry, user is not exist"
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|email|string|max:100|unique:users,email,'.$user->id,
            'password' => 'required|min:8|string|confirmed',
            'profile_image' => 'image'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 'fail',
                'success' => false,
                'msg' => 'validation errors',
                'errors' => $validator->errors()
            ];

            return response()->json($response, 422);
        }

        /**
         * Variable for storing to database
         */
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $photo = $request->file('photo_profile');

        /**
         * Codes below is to uploading images
         */
        $p_name = $photo->getClientOriginalName();
        $photo->move(storage_path().'/images/', $p_name);  
        $data[] = $p_name;

        $user = User::where('id', $id)
            ->update([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
                'profile_image' => $data
            ]);
        
        $user = User::where('id', $id)->first();

        if ($user) {

            $user->view_user = [
                'href' => 'api/v1/user/'. $user->id,
                'method' => 'GET'
            ];
            return response()->json([
                'success' => true,
                'msg' => 'Your profile has been edited successfuly',
                'user' => $user
            ],200);
        }
        else {
            return response()->json([
                'success' => false,
                'msg' => 'error during updating'
            ], 500);
        }
    }
}
