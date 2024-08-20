<?php

namespace App\Http\Controllers\Api;
use App\models\District;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\DistrictResource;
use App\Helpers\ApiResponse;

class DistrictController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request,$city_id)
    {
        $districts = District::where('city_id',$city_id)->get();
        if ($districts->count() > 0)
        {
            return ApiResponse::sendResponse(200, 'districts retrieved successfully ',DistrictResource::collection($districts));
            
        } 
        
            return ApiResponse::sendResponse(200, 'districts Not Found For This City', []);
    }
}
