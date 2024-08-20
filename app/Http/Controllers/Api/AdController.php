<?php
namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Models\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdResource;

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
        $word = $request->input('search');
         $this->paginatedResponse(
            Ad::when($word, fn($q) => $q->where('title', 'like', '%' . $word . '%'))->latest(),
            'Search completed',
            'No matching data'
        );
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
