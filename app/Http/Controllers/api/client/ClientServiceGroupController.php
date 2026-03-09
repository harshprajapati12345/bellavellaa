<?php

namespace App\Http\Controllers\api\client;

use App\Models\ServiceGroup;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ClientServiceGroupController extends Controller
{
    /**
     * Fetch a specific service group by ID.
     */
    public function show($id)
    {
        $group = ServiceGroup::where('status', 'Active')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $group
        ]);
    }
}
