<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Zone;

class ZonalPostController extends Controller
{  

public function zoneBlogs(string $zoneId){
    $blogs = Post::with(['user', 'category'])->where('zone_id', $zoneId)->get();
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
    $post = Post::with([ 'category'])
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

// public function detailPost(string $postId, string $zoneId){
//     $post = Post::with(['user', 'zone', 'category'])
//     ->where('id', $postId)
//     ->where('zone_id', $zoneId)
//     ->first();
//     if (!$post) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Post not found in the specified zone'
//         ], 404);
//     } 
//     return response()->json([
//         'status' => 'success',
//         'data' => $post
//     ], 200);

// }


//     public function zonalPosts(string $zone_name){
    
//         $zone = Zone::where('name', $zone_name)->first();

// if (!$zone) {
//     return response()->json([
//         'status' => 'error',
//         'message' => 'Zone not found'
//     ], 404);
// }
//         $posts = Post::with(['user', 'zone', 'category'])->where('zone_id', $zone->id)->get();

//         if($posts->isEmpty()) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'No posts found for this zone'
//             ], 404);
//         }

//         return response()->json([
//             'status' => 'success',
//             'count' => $posts->count(),
//             'data' => $posts
//         ], 200);
//     }

//     public function allPosts(){
//         $posts = Post::with(['user', 'zone', 'category'])->get();

//             if($posts->isEmpty()) {
//                 return response()->json([
//                     'status' => 'error',
//                     'message' => 'No posts found'
//                 ], 404);
//             }

//         return response()->json([
//             'status' => 'success',
//             'count' => $posts->count(),
//             'data' => $posts
//         ], 200);
//     }


}
