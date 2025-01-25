<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesTransactionController extends Controller
{
    /**
     * Display a listing of the sales transactions.
     */
    public function index()
    {
        $transactions = DB::table('sales_transactions')
            ->join('customers', 'sales_transactions.customer_id', '=', 'customers.id')
            ->select(
                'sales_transactions.*',
                'customers.customer_name'
            )
            ->get();

        return response()->json($transactions);
    }

    /**
     * Store a newly created sales transaction in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'transaction_date' => 'required|date',
            'total_payment' => 'required|numeric',
            'created_by' => 'required|exists:users,id',
            'updated_by' => 'required|exists:users,id',
        ]);

        $transactionId = DB::table('sales_transactions')->insertGetId([
            'customer_id' => $validated['customer_id'],
            'transaction_date' => $validated['transaction_date'],
            'total_payment' => $validated['total_payment'],
            'created_by' => $validated['created_by'],
            'updated_by' => $validated['updated_by'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Transaction created successfully', 'id' => $transactionId], 201);
    }

    /**
     * Display the specified sales transaction.
     */
    public function show($id)
    {
        $transaction = DB::table('sales_transactions')
            ->join('customers', 'sales_transactions.customer_id', '=', 'customers.id')
            ->select(
                'sales_transactions.*',
                'customers.customer_name'
            )
            ->where('sales_transactions.id', $id)
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }

    /**
     * Update the specified sales transaction in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'transaction_date' => 'required|date',
            'total_payment' => 'required|numeric',
            'updated_by' => 'required|exists:users,id',
        ]);

        $affected = DB::table('sales_transactions')
            ->where('id', $id)
            ->update([
                'customer_id' => $validated['customer_id'],
                'transaction_date' => $validated['transaction_date'],
                'total_payment' => $validated['total_payment'],
                'updated_by' => $validated['updated_by'],
                'updated_at' => now(),
            ]);

        if ($affected === 0) {
            return response()->json(['message' => 'Transaction not found or no changes made'], 404);
        }

        return response()->json(['message' => 'Transaction updated successfully']);
    }

    /**
     * Remove the specified sales transaction from storage.
     */
    public function destroy($id)
    {
        $deleted = DB::table('sales_transactions')->where('id', $id)->delete();

        if ($deleted === 0) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json(['message' => 'Transaction deleted successfully']);
    }
}
