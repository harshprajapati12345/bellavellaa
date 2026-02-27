<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->city = $request->city;
        $customer->address = $request->address;
        $customer->zip = $request->zip;
        $customer->password = Hash::make('password'); // Default password
        $customer->status = $request->has('status') ? 'Active' : 'Inactive';

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $customer->avatar = '/storage/' . $path;
        }

        $customer->save();

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone ?? '—',
                'city' => $customer->city ?? '—',
                'status' => $customer->status,
                'avatar' => $customer->avatar ?? 'https://i.pravatar.cc/80?u=' . $customer->id,
                'joined' => $customer->created_at ? $customer->created_at->format('d M Y') : '—',
                'bookings_count' => 0 // Placeholder
            ]);
        }

        return redirect()->route('customers.index');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->city = $request->city;
        $customer->address = $request->address;
        $customer->zip = $request->zip;
        $customer->status = $request->has('status') ? 'Active' : 'Inactive';

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($customer->avatar && str_starts_with($customer->avatar, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $customer->avatar));
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $customer->avatar = '/storage/' . $path;
        }

        $customer->save();

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->avatar && str_starts_with($customer->avatar, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $customer->avatar));
        }
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->status = $customer->status === 'Active' ? 'Inactive' : 'Active';
        $customer->save();

        return response()->json(['success' => true, 'status' => $customer->status]);
    }
}
