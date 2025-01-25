<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = DB::table('products')->get(); // Get all products
        return response()->json($products); // Return the products
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'product_name' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'created_by' => 'required|exists:users,id', // Check if the user exists
        ]);

        // Insert the product into the database
        $productId = DB::table('products')->insertGetId([
            'product_name' => $validatedData['product_name'],
            'purchase_price' => $validatedData['purchase_price'],
            'selling_price' => $validatedData['selling_price'],
            'stock' => $validatedData['stock'],
            'created_by' => $validatedData['created_by'],
            'updated_by' => $validatedData['created_by'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Product created successfully', 'id' => $productId], 201);
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = DB::table('products')->find($id); // Find the product

        if (!$product) { // If the product is not found
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = DB::table('products')->find($id);

        if (!$product) { // If the product is not found
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'product_name' => 'sometimes|required|string|max:255',
            'purchase_price' => 'sometimes|required|numeric|min:0',
            'selling_price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'updated_by' => 'required|exists:users,id', // Check if the user exists
        ]);

        // Update the product
        // Merge the validated data with the updated_at field
        $updated = DB::table('products')->where('id', $id)->update(array_merge($validatedData, [
            'updated_at' => now(), // Update the updated_at field
        ]));

        if ($updated) { // If the product is updated
            return response()->json(['message' => 'Product updated successfully']);
        }

        return response()->json(['message' => 'No changes made'], 200);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = DB::table('products')->find($id);

        if (!$product) { // If the product is not found
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete the product with the specified id
        DB::table('products')->where('id', $id)->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
