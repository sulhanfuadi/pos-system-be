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
        // Get all customers
        $customers = DB::table('customers')->get();
        return response()->json($customers);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'address' => 'required|string',
            'created_by' => 'required|integer|exists:users,id',
            'updated_by' => 'required|integer|exists:users,id',
        ]);

        // Insert the customer into the database
        $id = DB::table('customers')->insertGetId([
            'customer_name' => $request->customer_name,
            'contact' => $request->contact,
            'address' => $request->address,
            'created_by' => $request->created_by,
            'updated_by' => $request->updated_by,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Customer created successfully.', 'id' => $id], 201);
    }

    /**
     * Display the specified customer.
     */
    public function show($id)
    {
        // Find the customer
        $customer = DB::table('customers')->find($id);

        if (!$customer) { // If the customer is not found
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        return response()->json($customer);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'customer_name' => 'string|max:255',
            'contact' => 'string|max:255',
            'address' => 'string',
            'updated_by' => 'required|integer|exists:users,id', // Check if the user exists
        ]);

        // Update the customer
        $affectedRows = DB::table('customers')->where('id', $id)->update([
            'customer_name' => $request->customer_name,
            'contact' => $request->contact,
            'address' => $request->address,
            'updated_by' => $request->updated_by,
            'updated_at' => now(),
        ]);

        if ($affectedRows === 0) { // If no rows were affected
            return response()->json(['message' => 'Customer not found or nothing to update.'], 404);
        }

        return response()->json(['message' => 'Customer updated successfully.']);
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy($id)
    {
        // Delete the customer
        $deleted = DB::table('customers')->where('id', $id)->delete();

        if (!$deleted) { // If the customer is not found
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        return response()->json(['message' => 'Customer deleted successfully.']);
    }
}
