<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesDetailController extends Controller
{
    // Display a listing of the resource
    public function index()
    {
        // Get all sales details
        $salesDetails = DB::table('sales_details')
            ->join('products', 'sales_details.product_id', '=', 'products.id')
            ->join('sales_transactions', 'sales_details.transaction_id', '=', 'sales_transactions.id')
            ->select(
                'sales_details.*',
                'products.product_name',
                'sales_transactions.transaction_date'
            )
            ->get();

        return response()->json($salesDetails);
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:sales_transactions,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'created_by' => 'required|exists:users,id', // Check if the user exists
        ]);

        // Calculate the sub total
        $sub_total = $request->quantity * $request->unit_price;

        // Insert the sales detail into the database
        $salesDetail = DB::table('sales_details')->insert([
            'transaction_id' => $request->transaction_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'sub_total' => $sub_total,
            // 'created_by' => auth()->id(), // Get the authenticated user id
            // 'updated_by' => auth()->id(),
            'created_by' => $request->created_by,
            'updated_by' => $request->created_by,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($salesDetail) { // If the sales detail is successfully created
            return response()->json(['message' => 'Sales detail created successfully'], 201);
        }

        return response()->json(['message' => 'Failed to create sales detail'], 500);
    }

    // Display the specified resource
    public function show($id)
    {
        // Find the sales detail, join with products and sales_transactions
        $salesDetail = DB::table('sales_details')
            ->where('sales_details.id', $id)
            ->join('products', 'sales_details.product_id', '=', 'products.id')
            ->join('sales_transactions', 'sales_details.transaction_id', '=', 'sales_transactions.id')
            ->select(
                'sales_details.*',
                'products.product_name',
                'sales_transactions.transaction_date'
            )
            ->first();

        if ($salesDetail) { // If the sales detail is found
            return response()->json($salesDetail);
        }

        return response()->json(['message' => 'Sales detail not found'], 404);
    }

    // Update the specified resource in storage
    public function update(Request $request, $id)
    {
        $request->validate([
            'transaction_id' => 'sometimes|exists:sales_transactions,id',
            'product_id' => 'sometimes|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
            'unit_price' => 'sometimes|numeric|min:0',
            'updated_by' => 'required|exists:users,id', // added updated_by validation
        ]);

        // Get the sales detail, check if it exists
        $updateData = $request->only(['transaction_id', 'product_id', 'quantity', 'unit_price']); // Get the request data

        // Calculate sub_total if quantity and unit_price are provided
        if ($request->has('quantity') && $request->has('unit_price')) {
            $updateData['sub_total'] = $request->quantity * $request->unit_price;
        }

        // $updateData['updated_by'] = auth()->id(); // Get the authenticated user id
        $updatedData['updated_by'] = $request->updated_by; // Get the updated_by from the request
        $updateData['updated_at'] = now();

        // Update the sales detail
        $updated = DB::table('sales_details')->where('id', $id)->update($updateData);

        if ($updated) { // If the sales detail is successfully updated
            return response()->json(['message' => 'Sales detail updated successfully']);
        }

        return response()->json(['message' => 'Failed to update sales detail or no changes made'], 500);
    }

    // Remove the specified resource from storage
    public function destroy($id)
    {
        // Delete the sales detail by id
        $deleted = DB::table('sales_details')->where('id', $id)->delete();

        if ($deleted) { // If the sales detail is successfully deleted
            return response()->json(['message' => 'Sales detail deleted successfully']);
        }

        return response()->json(['message' => 'Failed to delete sales detail'], 500);
    }
}
