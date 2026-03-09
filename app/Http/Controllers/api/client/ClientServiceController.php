<?php

namespace App\Http\Controllers\api\client;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ClientServiceController extends Controller
{
    /**
     * CORE API: Complete details of a specified service.
     */
    public function show($id)
    {
        $service = Service::with([
            'includedItems' => function($q) {
                $q->orderBy('sort_order');
            },
            'addons' => function($q) {
                $q->where('status', 'Active')->orderBy('sort_order');
            },
            'variants' => function($q) {
                $q->where('status', 'Active')->orderBy('sort_order');
            }
        ])->where('status', 'Active')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Service fetched successfully',
            'data' => $service
        ], 200);
    }
}
