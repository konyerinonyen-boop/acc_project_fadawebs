<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MemberReportingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberController extends Controller
{

public function __construct(protected  MemberReportingService $reportingService)
{
    $this->reportingService = $reportingService;
}

  public function index()
{

    $members = User::whereNotNull('membership_id')
        ->where('status', 'approved')
        ->get();

    if ($members->isEmpty()) {
        return response()->json([
            'message' => 'No active members found'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $members
    ], 200);
}

public function detailMember($id){
    $member = User::whereNotNull('membership_id')
        ->where('status', 'approved')
        ->find($id);

   if (!$member) {
        return response()->json([
            'message' => 'No active members found'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $member
    ], 200);
}

public function search(Request $request)
{

    $request->validate(['query' => 'required|string|min:1']);

    $searchTerm = $request->input('query');

    $members = User::whereNotNull('membership_id')
        ->where('status', 'approved')
        ->where(function ($q) use ($searchTerm) {
            $q->where('first_name', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('last_name', 'LIKE', '%' . $searchTerm . '%');
        })
        ->get();

    return $members->isEmpty()
        ? response()->json(['message' => 'No members match your search'], 404)
        : response()->json(['status' => 'success', 'data' => $members], 200);
}

public function memberStatus(User $user): JsonResponse
    {
        $data = $this->reportingService->getMemberStatus($user);

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Get aggregate data for a specific zone.
     * URL: /api/zones/{zoneId}/summary
     */
    public function zoneSummary(int $zoneId): JsonResponse
    {
        $data = $this->reportingService->getZoneSummary($zoneId);

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

      public function qr($id)
{
    $member = User::whereNotNull('membership_id')
        ->where('status', 'approved')
        ->find($id);

    if (!$member) {
        return response()->json([
            'status'  => false,
            'message' => 'No active member found'
        ], 404);
    }

    $url = rtrim(config('app.frontend_url'), '/') . "/members/" . $member->id;

    $svg = QrCode::format('svg')
        ->size(300)
        ->generate($url);

    return response()->json([
        'status' => true,
        'data' => [
            'qr_code' => 'data:image/svg+xml;base64,' . base64_encode($svg),
            'url'     => $url,
        ],
    ]);
}
}



