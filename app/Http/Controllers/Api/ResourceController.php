<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResourceMaterial;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = ResourceMaterial::all();

        if ($resources->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No Resources Found',
            ], 404);
        }

        $resources->transform(function ($resource) {
            $resource->download_url = url("/api/dashboard/resources/{$resource->id}/download");

            return $resource;
        });

        return response()->json([
            'status' => 'success',
            'data' => $resources,
        ]);
    }


    public function detailResource($id)
{
    $resource = ResourceMaterial::find($id);

    if (! $resource) {
        return response()->json([
            'status' => 'error',
            'message' => 'Resource not found',
        ], 404);
    }

    $resource->download_url = asset('storage/' . $resource->file);

    return response()->json([
        'status' => 'success',
        'data' => $resource,
    ]);
}


}
