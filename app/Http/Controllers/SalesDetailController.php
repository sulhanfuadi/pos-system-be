<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesDetailController extends Controller
{
    // Display a listing of the resource
    public function index()
    {
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
            'sub_total' => 'required|numeric|min:0',
        ]);

        $salesDetail = DB::table('sales_details')->insert([
            'transaction_id' => $request->transaction_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'sub_total' => $request->sub_total,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($salesDetail) {
            return response()->json(['message' => 'Sales detail created successfully'], 201);
        }

        return response()->json(['message' => 'Failed to create sales detail'], 500);
    }

    // Display the specified resource
    public function show($id)
    {
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

        if ($salesDetail) {
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
            'sub_total' => 'sometimes|numeric|min:0',
        ]);

        $updateData = $request->only(['transaction_id', 'product_id', 'quantity', 'unit_price', 'sub_total']);
        $updateData['updated_by'] = auth()->id();
        $updateData['updated_at'] = now();

        $updated = DB::table('sales_details')->where('id', $id)->update($updateData);

        if ($updated) {
            return response()->json(['message' => 'Sales detail updated successfully']);
        }

        return response()->json(['message' => 'Failed to update sales detail or no changes made'], 500);
    }

    // Remove the specified resource from storage
    public function destroy($id)
    {
        $deleted = DB::table('sales_details')->where('id', $id)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Sales detail deleted successfully']);
        }

        return response()->json(['message' => 'Failed to delete sales detail'], 500);
    }
}
