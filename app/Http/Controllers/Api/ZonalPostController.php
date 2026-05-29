<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Zone;

class ZonalPostController extends Controller
{  

public function zoneBlogs(string $zoneId){
   
    $blogs = Post::with(['user', 'category', 'zone'])->where('zone_id', $zoneId)->get();
    if($blogs->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No blogs found for this zone'
        ], 404);
    }
    return response()->json([
        'status' => 'success',
        'data' => $blogs
    ], 200);
}

public function detailPost(string $zoneId, string $postId){


    $post = Post::with([ 'category', 'zone', 'user'])
    ->where('zone_id', $zoneId)
    ->where('id', $postId)
    ->first();

    if (!$post) {
        return response()->json([
            'status' => 'error',
            'message' => 'Post not found in the specified zone'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $post
    ], 200);
}




public function getBlog($id){

$post = Post::with(['category', 'user', 'zone'])
->where("user_id", 1)
->find($id);
if(!$post){
     return response()->json([
            'status' => 'error',
            'message' =>  'No  blog found'
          ], 404);
}

 return response()->json([
            'status' => 'success',
            'data' => $post
          ], 200);
}

public function allPost(){
    $posts = Post::with(['user', 'category', 'zone'])->where('user_id', 1)->get();

    if($posts->isEmpty()){
      return response()->json([
            'status' => 'error',
            'message' =>  'No  blogs found'
          ], 404);
    }

     return response()->json([
            'status' => 'success',
            'data' => $posts
          ], 200);


}



}
