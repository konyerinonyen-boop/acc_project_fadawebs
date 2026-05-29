<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResourceMaterial;


class ResourceController extends Controller
{
    public function index(){
  

      $resources = ResourceMaterial::all();
      if($resources->isEmpty()){

        return response()->json([
            'status' => 'error',
            'message' => 'No Resources Found'
        ]);
      }

      return response()->json([
        'status' => 'success',
        'data' => $resources
      ]);
    }


   public function detailResource($id)
{


    $resource = ResourceMaterial::find($id);

    return $resource
        ? response()->json($resource)
        : response()->json([
            'message' => 'Resource not found'
        ], 404);
}
}
