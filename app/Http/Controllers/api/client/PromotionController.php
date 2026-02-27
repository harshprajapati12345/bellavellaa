<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionController extends BaseController
{
    protected function guard()
    {
        return Auth::guard('api');
    }

    public function index(): JsonResponse
    {
        $promotions = Promotion::active()->get();

        return $this->success($promotions, 'Promotions retrieved successfully.');
    }

    public function validateCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'order_amount_paise' => 'required|integer',
        ]);

        $promotion = Promotion::where('code', $validated['code'])
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();

        if (!$promotion) {
            return $this->error('Invalid or expired coupon code.', 404);
        }

        if ($validated['order_amount_paise'] < $promotion->min_order_paise) {
            return $this->error("Minimum order amount of ₹" . ($promotion->min_order_paise / 100) . " required for this coupon.", 422);
        }

        // Check user limit
        $usageCount = \DB::table('promotion_usages')
            ->where('promotion_id', $promotion->id)
            ->where('customer_id', $this->guard()->id())
            ->count();

        if ($usageCount >= $promotion->per_user_limit) {
            return $this->error('You have already used this coupon code.', 422);
        }

        // Calculate discount
        $discount = 0;
        if ($promotion->type === 'percentage') {
            $discount = ($validated['order_amount_paise'] * $promotion->value) / 100;
            if ($promotion->max_discount_paise && $discount > $promotion->max_discount_paise) {
                $discount = $promotion->max_discount_paise;
            }
        } elseif ($promotion->type === 'flat') {
            $discount = $promotion->value;
        }

        return $this->success([
            'promotion_id' => $promotion->id,
            'code' => $promotion->code,
            'discount_paise' => (int) $discount,
            'message' => 'Coupon applied successfully.',
        ]);
    }
}
