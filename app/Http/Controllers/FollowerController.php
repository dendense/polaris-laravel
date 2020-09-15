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
               'msg' => 'Fail current following'
           ]); 
        }

        return response()->json([
            'status' => 200,
            'success' => true,
            'msg' => 'Following successfuly',
            'view_following' => [
                'href' => 'user/follow',
                'method' => 'GET'
                ]
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
        $following = Follower::where('following', $id)->where('follower', auth()->id())
            ->first();

        if (! $following->delete()) {
            $response = [
                'msg' => 'Delete failed'
            ];
            return response()->json($response, 500);
        }

        $response = [
            'msg' => 'Unfollowing successfuly',
        ];

        return response()->json($response, 200);
    }
}
