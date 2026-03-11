<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\KitOrder;
use App\Models\KitProduct;
use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KitOrderController extends Controller
{
    // Canonical delivery statuses
    const DELIVERY_STATUSES = ['Pending', 'Dispatched', 'Delivered', 'Returned', 'Lost'];

    public function index()
    {
        $orders = KitOrder::with(['professional', 'kitProduct'])->orderBy('assigned_at', 'desc')->get();
        $professionals = Professional::all();
        $products = KitProduct::where('status', 'Active')->get();

        $totalKits  = $orders->count();
        $outStock   = KitProduct::where('total_stock', 0)->count();
        $lowStock   = KitProduct::whereColumn('total_stock', '<=', 'min_stock')->where('total_stock', '>', 0)->count();
        $totalValue = $orders->sum(fn($o) => $o->kitProduct->price * $o->quantity);

        return view('professionals.kit-orders.index', compact('orders', 'professionals', 'products', 'totalKits', 'outStock', 'lowStock', 'totalValue'));
    }

    /* ── Order History ─────────────────────────────────────────────── */

    public function history(Request $request)
    {
        $query = KitOrder::with(['professional', 'kitProduct'])->latest('assigned_at');

        // Filter by delivery status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by professional
        if ($request->filled('professional_id')) {
            $query->where('professional_id', $request->professional_id);
        }

        // Search by order ID or professional name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhereHas('professional', fn($pq) => $pq->where('name', 'like', "%$search%"))
                  ->orWhereHas('kitProduct', fn($pq) => $pq->where('name', 'like', "%$search%"));
            });
        }

        $orders        = $query->paginate(20)->withQueryString();
        $professionals = Professional::orderBy('name')->pluck('name', 'id');
        $statuses      = self::DELIVERY_STATUSES;

        // Status counts for summary tabs
        $statusCounts = KitOrder::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('professionals.kit-orders.history', compact('orders', 'professionals', 'statuses', 'statusCounts'));
    }

    /* ── Update Delivery Status (AJAX) ─────────────────────────────── */

    public function updateDeliveryStatus(Request $request, KitOrder $kitOrder)
    {
        $request->validate([
            'status' => ['required', Rule::in(self::DELIVERY_STATUSES)],
            'notes'  => 'nullable|string|max:500',
        ]);

        $kitOrder->update([
            'status' => $request->status,
            'notes'  => $request->notes ?? $kitOrder->notes,
        ]);

        return response()->json([
            'success' => true,
            'status'  => $kitOrder->status,
            'message' => "Order #K-{$kitOrder->id} marked as {$kitOrder->status}",
        ]);
    }

    /* ── Store (assign kit to professional) ────────────────────────── */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'professional_id' => 'required|exists:professionals,id',
            'kit_product_id'  => 'required|exists:kit_products,id',
            'quantity'        => 'required|integer|min:1',
        ]);

        $product = KitProduct::find($request->kit_product_id);
        if ($product->available_stock < $request->quantity) {
            return back()->with('error', 'Insufficient stock available.');
        }

        KitOrder::create([
            'professional_id' => $request->professional_id,
            'kit_product_id'  => $request->kit_product_id,
            'quantity'        => $request->quantity,
            'status'          => 'Pending',
            'assigned_at'     => now(),
        ]);

        return redirect()->route('kit-orders.index')->with('success', 'Kit assigned successfully.');
    }
}
