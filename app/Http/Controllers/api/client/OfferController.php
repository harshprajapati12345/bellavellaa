<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Resources\Api\ClientOfferResource;
use App\Models\Offer;
use App\Models\OfferUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class OfferController extends BaseController
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    protected function listOffers(bool $legacyPromotionContract = false): JsonResponse
    {
        $offers = Offer::active()->orderByDesc('id')->get();

        return $this->success(
            ClientOfferResource::collection($offers)->resolve(),
            $legacyPromotionContract ? 'Promotions retrieved successfully.' : 'Offers retrieved successfully.'
        );
    }

    protected function validateOfferCode(Request $request, bool $legacyPromotionContract = false): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'order_amount_paise' => 'required|integer',
        ]);

        $offer = Offer::active()
            ->where('code', strtoupper($validated['code']))
            ->first();

        if (!$offer) {
            return $this->error('Invalid or expired coupon code.', 404);
        }

        if ($validated['order_amount_paise'] < $offer->min_order_paise) {
            return $this->error("Minimum order amount of â‚¹" . ($offer->min_order_paise / 100) . " required for this coupon.", 422);
        }

        $usageCount = Schema::hasTable('offer_usages')
            ? OfferUsage::query()
                ->where('offer_id', $offer->id)
                ->where('customer_id', $this->guard()->id())
                ->count()
            : 0;

        if ($offer->per_user_limit !== null && $usageCount >= $offer->per_user_limit) {
            return $this->error('You have already used this coupon code.', 422);
        }

        $discount = $offer->calculateDiscount((int) $validated['order_amount_paise']);

        $payload = [
            'offer_id' => $offer->id,
            'code' => $offer->code,
            'discount_paise' => $discount,
            'message' => 'Coupon applied successfully.',
        ];

        if ($legacyPromotionContract) {
            // Backward-compatibility shim for Flutter clients still expecting promotion_id.
            $payload['promotion_id'] = $offer->id;
        }

        return $this->success($payload);
    }

    public function index(): JsonResponse
    {
        return $this->listOffers();
    }

    public function validateCode(Request $request): JsonResponse
    {
        return $this->validateOfferCode($request);
    }
}
