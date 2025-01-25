<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get all purchase details
        $purchaseDetails = DB::table('purchase_details') // Select table purchase_details
            ->join('products', 'purchase_details.product_id', '=', 'products.id') // Join table product
            ->join('purchase_transactions', 'purchase_details.transaction_id', '=', 'purchase_transactions.id') // Join table purchase_transactions
            ->select( // Select columns
                'purchase_details.*',
                'products.product_name',
                'purchase_transactions.purchase_date'
            )
            ->get(); // Get all data

        return response()->json($purchaseDetails);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate request data
        $validated = $request->validate([
            'transaction_id' => 'required|exists:purchase_transactions,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'created_by' => 'required|exists:users,id',
        ]);

        // calculate sub_total, and set updated_by
        $validated['sub_total'] = $validated['quantity'] * $validated['unit_price'];
        $validated['updated_by'] = $validated['created_by']; // updated_by is the same as created_by

        // insert data
        $purchaseDetailId = DB::table('purchase_details')->insertGetId($validated);

        return response()->json([
            'message' => 'Purchase detail created successfully.',
            'id' => $purchaseDetailId,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // get purchase detail by id
        $purchaseDetail = DB::table('purchase_details')
            ->where('purchase_details.id', $id)
            ->join('products', 'purchase_details.product_id', '=', 'products.id')
            ->join('purchase_transactions', 'purchase_details.transaction_id', '=', 'purchase_transactions.id')
            ->select(
                'purchase_details.*',
                'products.product_name',
                'purchase_transactions.purchase_date'
            )
            ->first();

        if (!$purchaseDetail) { // If the purchase detail is not found
            return response()->json(['message' => 'Purchase detail not found.'], 404);
        }

        return response()->json($purchaseDetail);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'transaction_id' => 'sometimes|exists:purchase_transactions,id',
            'product_id' => 'sometimes|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
            'unit_price' => 'sometimes|numeric|min:0',
            'updated_by' => 'required|exists:users,id',
        ]);

        // If quantity or unit_price is updated, update sub_total
        if (isset($validated['quantity']) || isset($validated['unit_price'])) {
            $currentDetail = DB::table('purchase_details')->where('id', $id)->first(); // Get current purchase detail
            if (!$currentDetail) { // If the purchase detail is not found
                return response()->json(['message' => 'Purchase detail not found.'], 404);
            }

            $validated['quantity'] = $validated['quantity'] ?? $currentDetail->quantity; // If quantity is not updated, use the current quantity
            $validated['unit_price'] = $validated['unit_price'] ?? $currentDetail->unit_price;
            $validated['sub_total'] = $validated['quantity'] * $validated['unit_price']; // Calculate sub_total
        }

        // update data, 
        $updated = DB::table('purchase_details')->where('id', $id)->update($validated);

        if (!$updated) { // If no changes made
            return response()->json(['message' => 'No changes made.'], 400);
        }

        return response()->json(['message' => 'Purchase detail updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // delete purchase detail by id
        $deleted = DB::table('purchase_details')->where('id', $id)->delete();

        if (!$deleted) { // If the purchase detail is not found or could not be deleted
            return response()->json(['message' => 'Purchase detail not found or could not be deleted.'], 404);
        }

        return response()->json(['message' => 'Purchase detail deleted successfully.']);
    }
}
