<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Support\Str;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    use UploadTrait;

    protected $user;

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
        $posts = Post::all();

        foreach($posts as $post)
        {
            $posts->view_post = [
                'href' => 'api/v1/post' . $post->id,
                'method' => 'GET'
            ];
        }

        $response = [
            'msg' => 'List all post',
            'post' => $posts
        ];

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'photo' => 'required',
            'photo.*' => 'image',
            'location' => 'required|string',
            'place_name' => 'required|string',
            'description' => 'string',
            'transportation' => 'required',
            'demography' => 'required',
            'user_id' => 'required'
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

        $title = $request->input('title');
        $location = $request->input('location');
        $place_name = $request->input('place_name');
        $description = $request->input('description');
        $transportation = $request->input('transportation');
        $demography = $request->input('demography');
        $user_id = $request->input('user_id');

        if($request->hasfile('photo'))
         {

            foreach($request->file('photo') as $file)
            {
                $name=$file->getClientOriginalName();
                $file->move(storage_path().'/images/', $name);  
                $data[] = $name;  
            }
         }

        $post = new Post;

        $post->title = $title;
        $post->photo = json_encode($data);
        $post->location = $location;
        $post->place_name = $place_name;
        $post->description = $description;
        $post->transportation = $transportation;
        $post->demography = $demography;
        $post->user_id = $user_id;

        if ($post->save()) {

            $post->view_post = [
                'href' => '/api/v1/post',
                'method' => 'GET'
            ];

            $response = [
                'status' => 200,
                'success' => true,
                'msg' => 'Your post successfuly added!',
                'post' => $post
            ];

            return response()->json($response, 201);

        }
        else {
            return response()->json([
                'status' => 'fail',
                'success' => false,
                'msg' => 'Error during creating post'
            ], 500);
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
        $post = Post::where('id',$id)->first();
        $post->view_post = [
            'href' => 'api/v1/post',
            'method' => 'GET'
        ];

       $response = [
            'msg' => 'Post information',
            'post' => $post
       ];

       return response()->json($response, 201);
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
        $post = Post::where('id', $id)->findOrFail($id);
        
        if (!$post) {
            return response()->json([
                'status' => 'failed',
                'success' => false,
                'msg' => "Sorry, we can't find that post in here"
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'photo' => 'required',
            'photo.*' => 'image',
            'location' => 'required|string',
            'place_name' => 'required|string',
            'description' => 'string',
            'transportation' => 'required',
            'demography' => 'required',
            'user_id' => 'required'
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

        $title = $request->input('title');
        $location = $request->input('location');
        $place_name = $request->input('place_name');
        $description = $request->input('description');
        $transportation = $request->input('transportation');
        $demography = $request->input('demography');
        $user_id = $request->input('user_id');

        if($request->hasfile('photo'))
         {

            foreach($request->file('photo') as $file)
            {
                $name=$file->getClientOriginalName();
                $file->move(storage_path().'/images/', $name);  
                $data[] = $name;  
            }
         }

        $post = Post::where('id', $id)->update([
            'title' => $title,
            'photo' => json_encode($data),
            'location' => $location,
            'place_name' => $place_name,
            'description' => $description,
            'transportation' => $transportation,
            'demography' => $demography,
            'user_id' => $user_id
        ]);

        $post = Post::where('id', $id)->first();

        if ($post) {
            $post->view_post = [
                'href' => 'api/v1/post/' . $post->id,
                'method' => 'GET'
            ];
            return response()->json([
                'success' => true,
                'msg' => 'Your post has been edited successfuly',
                'post' => $post
            ], 200);
        }
        else {
            return response()->json([
                'success' => false,
                'msg' => 'Error during update'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if (! $post->delete()) {
            $response = [
                'msg' => 'Delete failed'
            ];
            return response()->json($response, 500);
        }

        $response = [
            'msg' => 'Post deleted successfuly',
            'create' => [
                'href' => 'api/v1/post',
                'method' => 'POST',
                'params' => 'title, photo, location, place_name, description, transportation, demography'
            ]
        ];

        return response()->json($response, 200);
    }

    /**
     * Search post
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $post = Post::where('title', 'like', $request->title.'%')
            ->orWhere('place_name', 'like', $request->title.'%')->get();

        if ($post->isEmpty()) {
            return response()->json([
                'status' => 404,
                'success' => false,
                'msg' => "Search failed, such post didn't exist"
            ]);
        }
        else {
            return response()->json([
                'status' => 200,
                'success' => true,
                'msg' => 'Search success',
                'post' => $post,
            ]);
        }
        
    }
}
