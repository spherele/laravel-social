<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Http\Resources\Post\PostResourse;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cacheKey = 'posts_index';
        $cacheDuration = 600;

        $posts = Cache::remember($cacheKey, $cacheDuration, function () {
            return Post::orderBy('created_at', 'desc')->get();
        });

        $etag = md5($posts->toJson());

        if ($request->header('If-None-Match') === $etag) {
            return response()->json([], 304);
        }

        return response()->json(PostResourse::collection($posts))->withHeaders([
            'ETag' => $etag,
            'Cache-Control' => 'public, max-age=600',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $post = Post::create($data);

        Cache::forget('posts_index');

        return PostResourse::make($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $cacheKey = 'post_' . $post->id;
        $cacheDuration = 600;

        $cachedPost = Cache::get($cacheKey);

        if (!$cachedPost) {
            Cache::put($cacheKey, $post, $cacheDuration);
            $cachedPost = $post;
        }

        return PostResourse::make($cachedPost);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Post $post)
    {
        $data = $request->validated();
        $post->update($data);
        $post->refresh();

        Cache::forget('posts_index');
        Cache::forget('post_' . $post->id);

        return PostResourse::make($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ]);
        }

        $post->delete();

        Cache::forget('posts_index');
        Cache::forget('post_' . $post->id);

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }

}
