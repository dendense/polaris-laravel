<?php

namespace App\Http\Controllers;

use App\Follower;
use App\User;
use Illuminate\Http\Request;

class FollowerController extends Controller
{

    /**
     * Protecting MessageController
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $follower = Follower::where('following', auth()->id())->get();
        $following = Follower::where('follower', auth()->id())->get();
        $follower_count = Follower::where('following', auth()->id())->count();
        $following_count = Follower::where('follower', auth()->id())->count();

        if (is_null($follower)) {

            if (is_null($following)) {

                return response()->json([
                    'msg' => "you didn't have follower yet and didn't following anyone",
                    'follower count' => $follower_count,
                    'following count' => $following_count
                ]);
            }
                return response()->json([
                    'msg' => "you didn't have follower yet",
                    'follower count' => $follower_count,
                    'following count' => $following_count,
                ]);
        }

        return response()->json([
            'msg' => 'This is your follower and following stat',
            'follower' => $follower,
            'following' => $following,
            'follower count' => $follower_count,
            'following count' => $following_count
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $following = Follower::create([
            'follower' => auth()->id(),
            'following' => $request->following,
        ]);

        if (!$following) {

            return response()->json([
                'status' => 'failed',
                'success' => false,
                'msg' => 'Following success',
                'view_following' => [
                    'href' => 'user/follow',
                    'method' => 'GET'
                    ]
            ]);
        }
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

        if (is_null($user)) {
            
            return response()->json([
                'status' => 'failed',
                'success' => false,
                'msg' => 'Search is failed, there is nothing here'
            ]);
        }

        return response()->json([
            'status' => 200,
            'success' => true,
            'msg' => 'Search is completed',
            'user' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $follower = Follower::where('follower', $id)->first();
        
        if (is_null($follower)) {
            
            return response()->json([
                'status' => 'failed',
                'success' => false,
                'msg' => 'Deleting failed, there is not a such data here'
            ]);
        }

        if (! $follower->delete()) {
            $response = [
                'msg' => 'Delete failed'
            ];
            return response()->json($response, 500);
        }

        $response = [
            'msg' => 'Delete follower successfuly',
        ];

        return response()->json($response, 200);
    }
}
