<?php

namespace App\Http\Controllers\adminroutes;

use App\Http\Controllers\Controller;
use App\Models\ScratchCard;
use App\Models\Customer;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScratchCardController extends Controller
{
    public function index(Request $request)
    {
        $query = ScratchCard::with('customer')->latest();

        // Filters
        if ($request->status) {
            $query->where('is_scratched', $request->status === 'scratched');
        }

        if ($request->source) {
            $query->where('source', $request->source);
        }

        if ($request->search) {
            $searchTerm = $request->search;
            $query->whereHas('customer', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('mobile', 'like', "%{$searchTerm}%");
            });
        }

        $cards = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => ScratchCard::count(),
            'pending' => ScratchCard::where('is_scratched', false)->count(),
            'scratched' => ScratchCard::where('is_scratched', true)->count(),
            'total_rewarded' => (int) ScratchCard::where('is_scratched', true)->sum('amount'),

        ];

        return view('admin.scratch_cards.index', compact('cards', 'stats'));
    }

    /**
     * AJAX Helper for Customer Search in creation modal
     */
    public function searchCustomers(Request $request)
    {
        $query = $request->get('q');
        $customers = Customer::where('name', 'like', "%{$query}%")
            ->orWhere('mobile', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'mobile']);

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|integer|min:1',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'expires_at' => 'nullable|date|after:now',
        ]);

        ScratchCard::create([
            'customer_id' => $request->customer_id,
            'amount' => $request->amount,
            'title' => $request->title ?? 'Surprise Reward',
            'description' => $request->description ?? 'You earned a scratch card!',
            'source' => 'admin',
            'is_scratched' => false,
            'expires_at' => $request->expires_at,
        ]);

        return back()->with('success', 'Scratch card created successfully.');
    }

    public function forceScratch($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $card = ScratchCard::lockForUpdate()->findOrFail($id);

                // Edge Case: Check Expiry
                if ($card->expires_at && now()->gt($card->expires_at)) {
                    throw new \Exception('This card has already expired and cannot be scratched.');
                }

                // Edge Case: Already Scratched
                if ($card->is_scratched) {
                    throw new \Exception('This card has already been scratched.');
                }

                $customer = $card->customer;
                if (!$customer) {
                    throw new \Exception('Corresponding customer not found.');
                }

                // Edge Case: Wallet Existence
                $wallet = $customer->coinWallet;
                if (!$wallet) {
                    // Auto-create wallet if missing (safety)
                    $wallet = Wallet::create([
                        'holder_type' => 'customer',
                        'holder_id' => $customer->id,
                        'type' => 'coin',
                        'balance' => 0
                    ]);
                }

                // 1. Mark card as scratched
                $card->update([
                    'is_scratched' => true,
                    'scratched_at' => now(),
                ]);

                // 2. Credit Wallet securely
                $wallet->credit(
                    amount: $card->amount,
                    source: 'admin_adjustment',
                    description: "Forced scratch by admin (ID: #{$card->id})",
                    referenceId: $card->id,
                    referenceType: ScratchCard::class
                );

                // 🕵️ Audit Log for Admin Abuse Prevention
                \Log::warning("Admin forced scratch action", [
                    'admin_id' => auth()->id() ?? 'system',
                    'card_id' => $card->id,
                    'customer_id' => $customer->id,
                    'amount' => $card->amount,
                    'timestamp' => now()
                ]);


                return back()->with('success', "Card scratched & ₹{$card->amount} reward credited successfully.");
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
