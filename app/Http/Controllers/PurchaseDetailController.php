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
        // Mengambil semua data purchase_details dengan informasi tambahan dari tabel terkait
        $purchaseDetails = DB::table('purchase_details')
            ->join('products', 'purchase_details.product_id', '=', 'products.id')
            ->join('purchase_transactions', 'purchase_details.transaction_id', '=', 'purchase_transactions.id')
            ->select(
                'purchase_details.*',
                'products.product_name',
                'purchase_transactions.purchase_date'
            )
            ->get();

        return response()->json($purchaseDetails);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'transaction_id' => 'required|exists:purchase_transactions,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'created_by' => 'required|exists:users,id',
        ]);

        // Hitung sub_total
        $validated['sub_total'] = $validated['quantity'] * $validated['unit_price'];
        $validated['updated_by'] = $validated['created_by']; // Default updated_by sama dengan created_by

        // Simpan ke database
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
        // Ambil data berdasarkan ID
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

        if (!$purchaseDetail) {
            return response()->json(['message' => 'Purchase detail not found.'], 404);
        }

        return response()->json($purchaseDetail);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi data
        $validated = $request->validate([
            'transaction_id' => 'sometimes|exists:purchase_transactions,id',
            'product_id' => 'sometimes|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
            'unit_price' => 'sometimes|numeric|min:0',
            'updated_by' => 'required|exists:users,id',
        ]);

        // Jika quantity atau unit_price diupdate, hitung ulang sub_total
        if (isset($validated['quantity']) || isset($validated['unit_price'])) {
            $currentDetail = DB::table('purchase_details')->where('id', $id)->first();
            if (!$currentDetail) {
                return response()->json(['message' => 'Purchase detail not found.'], 404);
            }

            $validated['quantity'] = $validated['quantity'] ?? $currentDetail->quantity;
            $validated['unit_price'] = $validated['unit_price'] ?? $currentDetail->unit_price;
            $validated['sub_total'] = $validated['quantity'] * $validated['unit_price'];
        }

        // Update data
        $updated = DB::table('purchase_details')->where('id', $id)->update($validated);

        if (!$updated) {
            return response()->json(['message' => 'No changes made.'], 400);
        }

        return response()->json(['message' => 'Purchase detail updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Hapus data
        $deleted = DB::table('purchase_details')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Purchase detail not found or could not be deleted.'], 404);
        }

        return response()->json(['message' => 'Purchase detail deleted successfully.']);
    }
}
