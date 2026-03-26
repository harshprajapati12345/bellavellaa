<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->unsignedBigInteger('max_discount_paise')->nullable()->after('discount_value');
            $table->unsignedBigInteger('min_order_paise')->default(0)->after('max_discount_paise');
            $table->unsignedInteger('usage_limit')->nullable()->after('min_order_paise');
            $table->unsignedInteger('per_user_limit')->default(1)->after('usage_limit');
            $table->unsignedInteger('times_used')->default(0)->after('per_user_limit');
            $table->string('target_type')->nullable()->after('times_used');
            $table->unsignedBigInteger('target_id')->nullable()->after('target_type');
            $table->unsignedBigInteger('legacy_promotion_id')->nullable()->unique()->after('target_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('offer_id')->nullable()->after('promotion_id')->constrained('offers')->nullOnDelete();
        });

        Schema::create('offer_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('discount_paise');
            $table->timestamps();

            $table->index(['offer_id', 'customer_id']);
        });

        if (!Schema::hasTable('promotions')) {
            return;
        }

        $promotions = DB::table('promotions')->orderBy('id')->get();

        foreach ($promotions as $promotion) {
            if (!in_array($promotion->type, ['percentage', 'flat'], true)) {
                continue;
            }

            $discountType = $promotion->type === 'flat' ? 'fixed' : 'percentage';
            $discountValue = $promotion->type === 'flat'
                ? round(((int) $promotion->value) / 100, 2)
                : (float) $promotion->value;

            $offerId = DB::table('offers')
                ->where('legacy_promotion_id', $promotion->id)
                ->orWhere('code', $promotion->code)
                ->value('id');

            $payload = [
                'name' => $promotion->name,
                'code' => $promotion->code,
                'description' => $promotion->description,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'max_discount_paise' => $promotion->max_discount_paise,
                'min_order_paise' => $promotion->min_order_paise ?? 0,
                'usage_limit' => $promotion->usage_limit,
                'per_user_limit' => $promotion->per_user_limit ?? 1,
                'times_used' => $promotion->times_used ?? 0,
                'target_type' => $promotion->target_type,
                'target_id' => $promotion->target_id,
                'valid_from' => $promotion->starts_at,
                'valid_until' => $promotion->ends_at,
                'status' => $promotion->is_active ? 'Active' : 'Inactive',
                'legacy_promotion_id' => $promotion->id,
                'updated_at' => now(),
            ];

            if ($offerId) {
                DB::table('offers')->where('id', $offerId)->update($payload);
                continue;
            }

            $payload['created_at'] = $promotion->created_at ?? now();
            DB::table('offers')->insert($payload);
        }

        $offerIdByLegacyPromotion = DB::table('offers')
            ->whereNotNull('legacy_promotion_id')
            ->pluck('id', 'legacy_promotion_id');

        foreach ($offerIdByLegacyPromotion as $promotionId => $offerId) {
            DB::table('orders')
                ->where('promotion_id', $promotionId)
                ->whereNull('offer_id')
                ->update(['offer_id' => $offerId]);
        }

        if (!Schema::hasTable('promotion_usages')) {
            return;
        }

        $promotionUsages = DB::table('promotion_usages')->orderBy('id')->get();

        foreach ($promotionUsages as $usage) {
            $offerId = $offerIdByLegacyPromotion[$usage->promotion_id] ?? null;
            if (!$offerId) {
                continue;
            }

            $exists = DB::table('offer_usages')
                ->where('offer_id', $offerId)
                ->where('customer_id', $usage->customer_id)
                ->where('order_id', $usage->order_id)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('offer_usages')->insert([
                'offer_id' => $offerId,
                'customer_id' => $usage->customer_id,
                'order_id' => $usage->order_id,
                'discount_paise' => $usage->discount_paise,
                'created_at' => $usage->created_at ?? now(),
                'updated_at' => $usage->updated_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_usages');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('offer_id');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropUnique(['legacy_promotion_id']);
            $table->dropColumn([
                'max_discount_paise',
                'min_order_paise',
                'usage_limit',
                'per_user_limit',
                'times_used',
                'target_type',
                'target_id',
                'legacy_promotion_id',
            ]);
        });
    }
};
