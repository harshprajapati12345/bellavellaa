<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Customer;
use App\Http\Resources\Api\CustomerResource;
use App\Http\Requests\Api\Admin\StoreCustomerRequest;
use App\Http\Requests\Api\Admin\UpdateCustomerRequest;
use Illuminate\Http\JsonResponse;

class CustomerController extends BaseController
{
    /**
     * Display a listing of the customers.
     */
    public function index(): JsonResponse
    {
        $query = Customer::query();

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $customers = $query->latest()->paginate(request('per_page', 15));

        return $this->success([
            'customers'   => CustomerResource::collection($customers),
            'pagination'  => [
                'total'        => $customers->total(),
                'count'        => $customers->count(),
                'per_page'     => $customers->perPage(),
                'current_page' => $customers->currentPage(),
                'total_pages'  => $customers->lastPage(),
            ]
        ], 'Customers retrieved successfully.');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());

        return $this->success(new CustomerResource($customer), 'Customer created successfully.', 201);
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): JsonResponse
    {
        return $this->success(new CustomerResource($customer), 'Customer retrieved successfully.');
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());

        return $this->success(new CustomerResource($customer), 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return $this->success(null, 'Customer deleted successfully.');
    }
}
