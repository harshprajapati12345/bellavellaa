<?php

namespace App\Http\Controllers\Api\Client;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionController extends OfferController
{
    public function index(): JsonResponse
    {
        return $this->listOffers(true);
    }

    public function validateCode(Request $request): JsonResponse
    {
        return $this->validateOfferCode($request, true);
    }
}
