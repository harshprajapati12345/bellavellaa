<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\SectionResource;
use App\Http\Resources\Api\ServiceResource;
use App\Models\Category;
use App\Models\HomepageContent;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class HomepageController extends BaseController
{
    /**
     * GET /api/client/homepage
     * 
     * Returns an ordered list of sections for the client app home screen.
     */
    public function index(): JsonResponse
    {
        $sections = \App\Services\HomepageService::build();

        return $this->success([
            'sections' => \App\Http\Resources\Api\SectionResource::collection($sections),
            'active_bookings_count' => 0, // Keep for now, or fetch if needed
        ], 'Homepage data retrieved successfully.');
    }
}

