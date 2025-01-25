<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index()
    {
        $customers = DB::table('customers')->get();
        return response()->json($customers);
    }

    /**
     * Show the form for creating a new customer.
     * (Not applicable for APIs, but retained for structure.)
     */
    public function create()
    {
        return response()->json(['message' => 'Not applicable for API.'], 400);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'address' => 'required|string',
            'created_by' => 'required|integer|exists:users,id',
            'updated_by' => 'required|integer|exists:users,id',
        ]);

        $id = DB::table('customers')->insertGetId([
            'customer_name' => $request->customer_name,
            'contact' => $request->contact,
            'address' => $request->address,
            'created_by' => $request->created_by,
            'updated_by' => $request->updated_by,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Customer created successfully.', 'id' => $id]);
    }

    /**
     * Display the specified customer.
     */
    public function show($id)
    {
        $customer = DB::table('customers')->find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        return response()->json($customer);
    }

    /**
     * Show the form for editing the specified customer.
     * (Not applicable for APIs, but retained for structure.)
     */
    public function edit($id)
    {
        return response()->json(['message' => 'Not applicable for API.'], 400);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_name' => 'string|max:255',
            'contact' => 'string|max:255',
            'address' => 'string',
            'updated_by' => 'required|integer|exists:users,id',
        ]);

        $affectedRows = DB::table('customers')->where('id', $id)->update([
            'customer_name' => $request->customer_name,
            'contact' => $request->contact,
            'address' => $request->address,
            'updated_by' => $request->updated_by,
            'updated_at' => now(),
        ]);

        if ($affectedRows === 0) {
            return response()->json(['message' => 'Customer not found or nothing to update.'], 404);
        }

        return response()->json(['message' => 'Customer updated successfully.']);
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy($id)
    {
        $deleted = DB::table('customers')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        return response()->json(['message' => 'Customer deleted successfully.']);
    }
}
