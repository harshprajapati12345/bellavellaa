<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::all();
        $total = $offers->count();
        $active = $offers->where('status', 'Active')->count();

        return view('offers.index', compact('offers', 'total', 'active'));
    }

    public function create()
    {
        return view('offers.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('offers', 'public');
        }

        Offer::create([
            'name' => $request->name,
            'description' => $request->description,
            'discount_value' => $request->discount_value ?? 0,
            'discount_type' => $request->discount_type ?? 'percentage',
            'code' => $request->code,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'status' => $request->has('status') ? 'Active' : 'Inactive',
            'image' => $imagePath,
        ]);

        return redirect()->route('offers.index')->with('success', 'Offer created successfully!');
    }

    public function show(Offer $offer)
    {
        return response()->json([
            'id' => $offer->id,
            'name' => $offer->name,
            'description' => strip_tags($offer->description ?? 'No description available.'),
            'discount' => ($offer->discount_type === 'percentage' ? $offer->discount_value . '%' : '₹' . $offer->discount_value) . ' OFF',
            'code' => $offer->code ?? 'NO CODE',
            'usage' => $offer->usage_count ?? 0,
            'valid_from' => $offer->valid_from ? \Carbon\Carbon::parse($offer->valid_from)->format('d M Y') : '—',
            'valid_until' => $offer->valid_until ? \Carbon\Carbon::parse($offer->valid_until)->format('d M Y') : '—',
            'status' => $offer->status,
        ]);
    }

    public function edit(Offer $offer)
    {
        return view('offers.edit', compact('offer'));
    }

    public function update(Request $request, Offer $offer)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $imagePath = $offer->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('offers', 'public');
        }

        $offer->update([
            'name' => $request->name,
            'description' => $request->description,
            'discount_value' => $request->discount_value ?? $offer->discount_value,
            'discount_type' => $request->discount_type ?? $offer->discount_type,
            'code' => $request->code,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'status' => $request->has('status') ? 'Active' : 'Inactive',
            'image' => $imagePath,
        ]);

        return redirect()->route('offers.index')->with('success', 'Offer updated!');
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();
        return redirect()->route('offers.index')->with('success', 'Offer deleted.');
    }
}
