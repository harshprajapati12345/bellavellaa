<?php

namespace App\Http\Controllers;

use App\Models\KitOrder;
use App\Models\KitProduct;
use App\Models\Professional;
use Illuminate\Http\Request;

class KitOrderController extends Controller
{
    public function index()
    {
        $orders = KitOrder::with(['professional', 'kitProduct'])->orderBy('assigned_at', 'desc')->get();
        $professionals = Professional::all();
        $products = KitProduct::where('status', 'Active')->get();

        $totalKits = $orders->count();
        $outStock = KitProduct::where('total_stock', 0)->count();
        $lowStock = KitProduct::whereColumn('total_stock', '<=', 'min_stock')->where('total_stock', '>', 0)->count();
        $totalValue = $orders->sum(fn($o) => $o->kitProduct->price * $o->quantity);

        return view('professionals.kit-orders.index', compact('orders', 'professionals', 'products', 'totalKits', 'outStock', 'lowStock', 'totalValue'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'professional_id' => 'required|exists:professionals,id',
            'kit_product_id' => 'required|exists:kit_products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Check stock availability
        $product = KitProduct::find($request->kit_product_id);
        if ($product->available_stock < $request->quantity) {
            return back()->with('error', 'Insufficient stock available.');
        }

        KitOrder::create([
            'professional_id' => $request->professional_id,
            'kit_product_id' => $request->kit_product_id,
            'quantity' => $request->quantity,
            'status' => 'Assigned',
            'assigned_at' => now(),
        ]);

        return redirect()->route('kit-orders.index')->with('success', 'Kit assigned successfully.');
    }
}
