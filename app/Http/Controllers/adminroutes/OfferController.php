<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

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
        $request->validate([
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:50|unique:offers,code',
            'discount_type'  => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'valid_from'     => 'nullable|date',
            'valid_until'    => 'nullable|date|after_or_equal:valid_from',
            'status'         => 'nullable|string',
            'image'          => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('offers', 'public');
            }

            Offer::create([
                'name'           => $request->name,
                'description'    => $request->description,
                'discount_value' => $request->discount_value,
                'discount_type'  => $request->discount_type,
                'code'           => strtoupper($request->code),
                'valid_from'     => $request->valid_from,
                'valid_until'    => $request->valid_until,
                'status'         => $request->has('status') ? 'Active' : 'Inactive',
                'image'          => $imagePath,
            ]);

            DB::commit();
            return redirect()->route('offers.index')->with('success', 'Offer created successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating offer: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create offer. please try again.');
        }
    }

    public function show(Offer $offer)
    {
        return response()->json([
            'id'          => $offer->id,
            'title'       => $offer->name,
            'image'       => $offer->image ? asset('storage/' . $offer->image) : asset('assets/images/placeholder.png'),
            'name'        => $offer->name,
            'description' => strip_tags($offer->description ?? 'No description available.'),
            'discount'    => ($offer->discount_type === 'percentage' ? $offer->discount_value . '%' : '₹' . $offer->discount_value) . ' OFF',
            'code'        => $offer->code ?? 'NO CODE',
            'usage'       => $offer->usage_count ?? 0,
            'valid_from'  => $offer->valid_from ? \Carbon\Carbon::parse($offer->valid_from)->format('d M Y') : '—',
            'valid_until' => $offer->valid_until ? \Carbon\Carbon::parse($offer->valid_until)->format('d M Y') : '—',
            'status'      => $offer->status,
        ]);
    }

    public function edit(Offer $offer)
    {
        return view('offers.edit', compact('offer'));
    }

    public function update(Request $request, Offer $offer)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:50|unique:offers,code,' . $offer->id,
            'discount_type'  => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'valid_from'     => 'nullable|date',
            'valid_until'    => 'nullable|date|after_or_equal:valid_from',
            'status'         => 'nullable|string',
            'image'          => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $imagePath = $offer->image;
            if ($request->hasFile('image')) {
                // Consider deleting old image here if needed
                $imagePath = $request->file('image')->store('offers', 'public');
            }

            $offer->update([
                'name'           => $request->name,
                'description'    => $request->description,
                'discount_value' => $request->discount_value,
                'discount_type'  => $request->discount_type,
                'code'           => strtoupper($request->code),
                'valid_from'     => $request->valid_from,
                'valid_until'    => $request->valid_until,
                'status'         => $request->has('status') ? 'Active' : 'Inactive',
                'image'          => $imagePath,
            ]);

            DB::commit();
            return redirect()->route('offers.index')->with('success', 'Offer updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating offer: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update offer. ' . $e->getMessage());
        }
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();
        return redirect()->route('offers.index')->with('success', 'Offer deleted.');
    }
}
