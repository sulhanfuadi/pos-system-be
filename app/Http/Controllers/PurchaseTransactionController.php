<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseTransactionController extends Controller
{
    /**
     * Display a listing of purchase transactions.
     */
    public function index()
    {
        $purchaseTransactions = DB::table('purchase_transactions')
            ->select('id', 'purchase_date', 'total_cost', 'created_at', 'updated_at')
            ->get();

        return response()->json($purchaseTransactions, 200);
    }

    /**
     * Store a newly created purchase transaction in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'purchase_date' => 'required|date',
            'total_cost' => 'required|numeric',
            'created_by' => 'required|exists:users,id',
            'updated_by' => 'required|exists:users,id',
        ]);

        $id = DB::table('purchase_transactions')->insertGetId([
            'purchase_date' => $validatedData['purchase_date'],
            'total_cost' => $validatedData['total_cost'],
            'created_by' => $validatedData['created_by'],
            'updated_by' => $validatedData['updated_by'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Purchase transaction created successfully.',
            'transaction_id' => $id,
        ], 201);
    }

    /**
     * Display the specified purchase transaction.
     */
    public function show($id)
    {
        $purchaseTransaction = DB::table('purchase_transactions')
            ->where('id', $id)
            ->first();

        if (!$purchaseTransaction) {
            return response()->json(['message' => 'Purchase transaction not found.'], 404);
        }

        return response()->json($purchaseTransaction, 200);
    }

    /**
     * Update the specified purchase transaction in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'purchase_date' => 'sometimes|date',
            'total_cost' => 'sometimes|numeric',
            'updated_by' => 'required|exists:users,id',
        ]);

        $purchaseTransaction = DB::table('purchase_transactions')->where('id', $id)->first();

        if (!$purchaseTransaction) {
            return response()->json(['message' => 'Purchase transaction not found.'], 404);
        }

        DB::table('purchase_transactions')
            ->where('id', $id)
            ->update([
                'purchase_date' => $validatedData['purchase_date'] ?? $purchaseTransaction->purchase_date,
                'total_cost' => $validatedData['total_cost'] ?? $purchaseTransaction->total_cost,
                'updated_by' => $validatedData['updated_by'],
                'updated_at' => Carbon::now(),
            ]);

        return response()->json(['message' => 'Purchase transaction updated successfully.'], 200);
    }

    /**
     * Remove the specified purchase transaction from storage.
     */
    public function destroy($id)
    {
        $purchaseTransaction = DB::table('purchase_transactions')->where('id', $id)->first();

        if (!$purchaseTransaction) {
            return response()->json(['message' => 'Purchase transaction not found.'], 404);
        }

        DB::table('purchase_transactions')->where('id', $id)->delete();

        return response()->json(['message' => 'Purchase transaction deleted successfully.'], 200);
    }
}
