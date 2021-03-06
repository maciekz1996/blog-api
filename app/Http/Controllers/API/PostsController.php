<?php

namespace App\Http\Controllers\API;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->only(['index', 'destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Post::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // VALIDATE DATA
        $validator = Validator::make($request->all(), [
            'post_title' => 'required|max:255',
            'post_content' => 'required',
            'post_photo' => 'nullable|image'
        ]);

        if ($validator->fails())
        {
            return response()->json(['error' => 'Bad request'], 400);
        }

        $post = Post::create($request->all());
        $post->user_id = $request->user()->id;
        $photo_url = $request->file('post_photo')->store('public');
        $post->photo_url = $photo_url;
        $post->save();

        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        if (Auth::guard('api')->user()->id == $post->user_id)
        {
            return $post;
        }        
        else 
        {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        // VALIDATE DATA
        $validator = Validator::make($request->all(), [
            'post_title' => 'required|max:255',
            'post_content' => 'required'            
        ]);

        if ($validator->fails())
        {
            return response()->json(['error' => 'Bad request'], 400);
        }

        if ($request->user()->id == $post->user_id)
        {
            $post->update($request->all());
            return response()->json($post, 200);
        }        
        else 
        {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if ($post->photo_url != null)
        {
            Storage::delete($post->photo_url);
        }        
        $post->delete();
        return response()->json(null, 204);
    }
}
