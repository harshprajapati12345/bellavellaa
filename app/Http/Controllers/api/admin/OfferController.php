<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Offer;
use App\Http\Resources\Api\OfferResource;
use App\Http\Requests\Api\Admin\StoreOfferRequest;
use App\Http\Requests\Api\Admin\UpdateOfferRequest;
use Illuminate\Http\JsonResponse;

class OfferController extends BaseController
{
    /**
     * Display a listing of the offers.
     */
    public function index(): JsonResponse
    {
        $query = Offer::query();

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $offers = $query->latest()->paginate(request('per_page', 15));

        return $this->success([
            'offers'      => OfferResource::collection($offers),
            'pagination'  => [
                'total'        => $offers->total(),
                'count'        => $offers->count(),
                'per_page'     => $offers->perPage(),
                'current_page' => $offers->currentPage(),
                'total_pages'  => $offers->lastPage(),
            ]
        ], 'Offers retrieved successfully.');
    }

    /**
     * Store a newly created offer in storage.
     */
    public function store(StoreOfferRequest $request): JsonResponse
    {
        $offer = Offer::create($request->validated());

        return $this->success(new OfferResource($offer), 'Offer created successfully.', 201);
    }

    /**
     * Display the specified offer.
     */
    public function show(Offer $offer): JsonResponse
    {
        return $this->success(new OfferResource($offer), 'Offer retrieved successfully.');
    }

    /**
     * Update the specified offer in storage.
     */
    public function update(UpdateOfferRequest $request, Offer $offer): JsonResponse
    {
        $offer->update($request->validated());

        return $this->success(new OfferResource($offer), 'Offer updated successfully.');
    }

    /**
     * Remove the specified offer from storage.
     */
    public function destroy(Offer $offer): JsonResponse
    {
        $offer->delete();

        return $this->success(null, 'Offer deleted successfully.');
    }
}
