<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::all();
        $total  = $offers->count();
        $active = $offers->where('status', 'Active')->count();

        return view('offers.index', compact('offers', 'total', 'active'));
    }

    public function create()
    {
        return view('offers.create');
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $stored = $request->file('image')->store('offers', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        Offer::create([
            'title'       => $request->title,
            'description' => $request->description,
            'discount'    => $request->discount ?? 0,
            'code'        => $request->code,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'status'      => $request->has('status') ? 'Active' : 'Inactive',
            'image'       => $imagePath,
        ]);

        return redirect()->route('offers.index')->with('success', 'Offer created successfully!');
    }

    public function show(Offer $offer)
    {
        return response()->json([
            'id' => $offer->id,
            'title' => $offer->title,
            'description' => strip_tags($offer->description ?? 'No description available.'),
            'discount' => $offer->discount . '% OFF',
            'code' => $offer->code ?? 'NO CODE',
            'start_date' => $offer->start_date ? \Carbon\Carbon::parse($offer->start_date)->format('d M Y') : '—',
            'end_date' => $offer->end_date ? \Carbon\Carbon::parse($offer->end_date)->format('d M Y') : '—',
            'status' => $offer->status,
            'image' => $offer->image ?: 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=400&q=80',
        ]);
    }

    public function edit(Offer $offer)
    {
        return view('offers.edit', compact('offer'));
    }

    public function update(Request $request, Offer $offer)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $imagePath = $offer->image;
        if ($request->hasFile('image')) {
            $stored = $request->file('image')->store('offers', 'public');
            $imagePath = asset('storage/' . $stored);
        }

        $offer->update([
            'title'       => $request->title,
            'description' => $request->description,
            'discount'    => $request->discount ?? $offer->discount,
            'code'        => $request->code,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'status'      => $request->has('status') ? 'Active' : 'Inactive',
            'image'       => $imagePath,
        ]);

        return redirect()->route('offers.index')->with('success', 'Offer updated!');
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();
        return redirect()->route('offers.index')->with('success', 'Offer deleted.');
    }
}
