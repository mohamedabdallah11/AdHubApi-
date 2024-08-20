<?php
namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdRequest;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use Illuminate\Http\Request;

class AdController extends Controller
{
    private function paginatedResponse($query, $successMessage, $emptyMessage)
    {
        $ads = $query->paginate(10);
        if ($ads->count() > 0) {
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
            return ApiResponse::sendResponse(200, $successMessage, $data);
        }
        return ApiResponse::sendResponse(200, $emptyMessage, []);
    }

    public function index()
    {
        return $this->paginatedResponse(
            Ad::latest(),
            'Ads Retrieved Successfully',
            'No Ads available'
        );
    }

    public function latest()
    {
        $ads = Ad::latest()->take(2)->get();
        if (count($ads) > 0) {
            return ApiResponse::sendResponse(200, 'Latest Ads Retrieved Successfully', AdResource::collection($ads));
        }
        return ApiResponse::sendResponse(200, 'There are no latest ads', []);
    }

    public function domain($domain_id)
    {
        return $this->paginatedResponse(
            Ad::where('domain_id', $domain_id)->latest(),
            'Ads in the domain retrieved successfully',
            'No Ads in the domain'
        );
    }
    public function search(Request $request)
    {
        $word = $request->has('search') ? $request->input('search') : null;
        $ads = Ad::when($word != null, function ($q) use ($word) {
            $q->where('title', 'like', '%' . $word . '%');
        })->latest()->paginate(10); 
    
        if ($ads->count() > 0) {
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
            return ApiResponse::sendResponse(200, 'Search completed', $data);
        }
        return ApiResponse::sendResponse(200, 'No matching data', []);
    }
    

        
    public function create(AdRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $record = Ad::create($data);
        if ($record) return ApiResponse::sendResponse(201, 'Your Ad created successfully', new AdResource($record));
    }

    public function update(AdRequest $request, $adId)
    {
        $ad = Ad::findOrFail($adId);
        if ($ad->user_id != $request->user()->id) {
            return ApiResponse::sendResponse(403, 'You aren\'t allowed to take this action', []);
        }

        $data = $request->validated();
        $updating = $ad->update($data);
        if ($updating) return ApiResponse::sendResponse(201, 'Your Ad updated successfully', new AdResource($ad));
    }

    public function delete(Request $request, $adId)
    {
        $ad = Ad::findOrFail($adId);
        if ($ad->user_id != $request->user()->id) {
            return ApiResponse::sendResponse(403, 'You aren\'t allowed to take this action', []);
        }
        $success = $ad->delete();
        if ($success) return ApiResponse::sendResponse(200, 'Your Ad deleted successfully', []);
    }
    
    public function myads(Request $request)
    {
        return $this->paginatedResponse(
            Ad::where('user_id', $request->user()->id)->latest(),
            'My ads retrieved successfully',
            'You don\'t have any ads'
        );
    }

}
