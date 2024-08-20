<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Models\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdRequest;
use App\Http\Resources\AdResource;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::latest()->paginate(10);
        if (count($ads) > 0) {
            if ($ads->total() > $ads->perPage()) {
                $data = [
                    'records' => AdResource::collection($ads),
                    'pagination links' => [
                        'current page' => $ads->currentPage(),
                        'per page' => $ads->perPage(),
                        'total' => $ads->total(),
                        'links' => [
                            'first' => $ads->url(1),
                            'last' => $ads->url($ads->lastPage()),
                        ],
                    ],
                ];
            } else {
                $data = AdResource::collection($ads);
            }
            return ApiResponse::sendResponse(200, 'Ads Retrieved Successfully', $data);
        }
        return ApiResponse::sendResponse(200, 'No Ads available', []);
    }

    public function latest()
    {
        $ads = Ad::latest()->take(2)->get();
        if (count($ads) > 0) {
            return ApiResponse::sendResponse(200, 'Latest Ads Retrieved Successfully', AdResource::collection($ads));
        }
        return ApiResponse::sendResponse(200, 'There are no latest ads', []);
    }

}