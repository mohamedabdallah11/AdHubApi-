<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Helpers\ApiResponse;
use App\Http\Resources\CityResource;

class CityController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $cities = City::all();
        if ($cities)
        {
            return ApiResponse::sendResponse(200, 'cities retrieved successfully ',CityResource::collection($cities));
            
        } 
        
            return ApiResponse::sendResponse(200, 'cities Not Found', []);
    }
}
